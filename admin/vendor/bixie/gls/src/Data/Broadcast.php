<?php


namespace Bixie\Gls\Data;


use Bixie\Gls\GlsException;

class Broadcast implements \ArrayAccess {
	/**
	 * @var Tag[]
	 */
	protected $tags;

	/**
	 * @var Tag[]
	 */
	protected $glsTags = [];

	/**
	 * @var array
	 */
	protected $glsErrors;

	/**
	 * @var array
	 */
	protected $tagsByCode;

	/**
	 * @param array $data
	 * @return Broadcast
	 */
	public static function create ($data = []) {
		$broadcast = (new Broadcast())
			->setTag('software_name', 'Bixie Gls API')
			->setTag('contact_id', '5280000000');
		foreach ($data as $key => $value) {
			if ($broadcast->isGlsTag($key)) {
				$broadcast[$key] = $value;
			}
		}
		return $broadcast;
	}

	public function __construct () {
		$this->glsTags = include(dirname(dirname(__DIR__)) . '/gls_tags.php');

	}

	function __toString () {
		$out = '\\\\\\\\\\GLS\\\\\\\\\\';
		$out .= implode('|', array_map(function ($tag) {
			/** @var Tag $tag */
			return $tag->toStream();
		}, array_filter($this->tags, function ($tag) {
			/** @var Tag $tag */
			return $tag->hasValue();
		})));
		return $out . '|/////GLS/////';
	}

	/**
	 * @param string $name
	 * @param mixed  $value
	 * @return Broadcast
	 */
	public function setTag ($name, $value) {
		if (!isset($this->glsTags[$name])) {
			throw new \InvalidArgumentException(sprintf('Tag %s does not exist in GLS API', $name));
		}
		if (!isset($this->tags[$name])) {
			$this->tags[$name] = $this->glsTags[$name];
		}
		$this->tags[$name]->setValue($value);
		return $this;
	}

	/**
	 * @param $name
	 * @return bool
	 */
	public function isGlsTag ($name) {
		return isset($this->glsTags[$name]);
	}

	/**
	 * @param $code
	 * @return bool
	 */
	public function isGlsCode($code) {
		$this->getTagsByCode();
		return isset($this->tagsByCode[ltrim($code, 'T')]);
	}

	/**
	 * @param $code
	 * @return Tag
	 */
	public function tagByCode ($code) {
		$this->getTagsByCode();
		$code = ltrim($code, 'T');
		if (!isset($this->tagsByCode[$code])) {
			throw new \InvalidArgumentException(sprintf('Tag %s does not exist in GLS API', $code));
		}
		return $this->glsTags[$this->tagsByCode[$code]];
	}

	/**
	 * @return array
	 */
	public function getTagsByCode () {
		if (!isset($this->tagsByCode)) {
			foreach ($this->glsTags as $glsTag) {
				$this->tagsByCode[$glsTag->code] = $glsTag->name;
			}
		}
		return $this->tagsByCode;
	}

	/**
	 *
	 */
	public function validate () {
		foreach ($this->tags as $tag) {
			$tag->validate();
		}
	}

	/**
	 * @param $parcel_number
	 * @return int
	 */
	public function getDomesticParcelNumber ($parcel_number) {
		$numberBase = $this['gls_customer_number'] . sprintf("%05d", ($parcel_number));
		return (int) $this->addControlNumber($numberBase);
	}

	/**
	 * @param $numberBase
	 * @return int|string
	 */
	private function addControlNumber ($numberBase) {
		$_digitArray = str_split($numberBase);
		$_digitArray = array_reverse($_digitArray);
		$sum = 0;
		foreach($_digitArray as $key => $value){
			if($key%2 == 0){
				$sum += 3*$value;
			}else{
				$sum += $value;
			}
		}
		$diff = (int)(ceil($sum/10)*10)-($sum+1);
		if($diff == 10){
			return $numberBase.'0';
		}
		return $numberBase.$diff;

	}

	/**
	 * @param $returnStream
	 * @return array
	 * @throws GlsException
	 */
	public function parseIncomingStream($returnStream) {
		//strip head and tail
		if( stripos($returnStream ,'\\\\\\\\\\GLS\\\\\\\\\\' ) === false || stripos($returnStream ,'/////GLS/////' ) === false ){
			throw new GlsException('Invalid incoming GLS stream');
		}
		$returnStream = str_ireplace ( array('\\\\\\\\\\GLS\\\\\\\\\\','/////GLS/////') ,'', $returnStream);
		$tagData = array();
		foreach (explode('|',$returnStream) as $item) {

			if (stripos($item,'T') === 0) {

				$tmp = explode(':',$item,2); $tmp[0] = ltrim($tmp[0], 'T');
				if ($tmp[1] == '') continue;

				if($this->isGlsCode($tmp[0])){
					$tag = $this->tagByCode($tmp[0])->setValue($tmp[1]);
					$tagData[$tag->name] = $tag->getValue();
				} else {
					$tagData[$tmp[0]] = $tmp[1] ;
				}

			} elseif (stripos($item,'RESULT') === 0 && stripos($item,'E000') === false ) {

				throw new GlsException(sprintf($this->parseErrorCode(substr($item, 7))));

			}
			$tmp = null;
		}
		return $tagData;
	}

	public function getTemplate ($returnStream) {
		if(false === ($pos = stripos($returnStream ,'\\\\\\\\\\GLS\\\\\\\\\\')) or stripos($returnStream ,'/////GLS/////' ) === false ){
			throw new GlsException('Invalid incoming GLS stream');
		}
		return substr($returnStream, 0, ($pos - 1));
	}
	/**
	 * @param $errorString
	 * @return string
	 */
	private function parseErrorCode ($errorString) {
		if (!isset($this->glsErrors)) {
			$this->glsErrors = include(dirname(dirname(__DIR__)) . '/gls_errors.php');
		}
		$errors = [];
		$parts = explode(':', $errorString);
		if (isset($this->glsErrors[$parts[0]])) {
			$errors[] = $this->glsErrors[$parts[0]];
			if (count($parts) > 1) {
				if ($this->isGlsCode($parts[1])) {
					$errors[] = sprintf('Property: %s', $this->tagByCode($parts[1])->description);
				} else {
					$errors[] = sprintf('Value: %s', $parts[1]);
				}
				if (count($parts) == 3) {
					$errors[] = sprintf('Value: %s', $parts[2]);
				}
			}
			return implode('. ', $errors);
		}
		return $errorString;
	}

	/**
	 * Checks if a key exists.
	 * @param  string $key
	 * @return bool
	 */
	public function offsetExists ($key) {
		return isset($this->tags[$key]);
	}

	/**
	 * Gets a value by key.
	 * @param  string $key
	 * @return mixed
	 */
	public function offsetGet ($key) {
		return isset($this->tags[$key]) ? $this->tags[$key]->getValue() : null;
	}

	/**
	 * Sets a value.
	 * @param string $key
	 * @param string $value
	 */
	public function offsetSet ($key, $value) {
		$this->setTag($key, $value);
	}

	/**
	 * Unset a value.
	 * @param string $key
	 */
	public function offsetUnset ($key) {
		unset($this->tags[$key]);
	}


}