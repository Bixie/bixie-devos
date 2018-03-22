<?php


namespace Bixie\Gls\Data;


class Tag {
	/**
	 * @var string
	 */
	public $code;
	/**
	 * @var string
	 */
	public $name;
	/**
	 * @var string
	 */
	public $description;
	/**
	 * @var string
	 */
	public $example;
	/**
	 * @var string
	 */
	public $type;
	/**
	 * @var bool
	 */
	public $required;
	/**
	 * @var mixed
	 */
	public $maxLength;
	/**
	 * @var mixed
	 */
	protected $value;

	/**
	 * Tag constructor.
	 * @param string $code
	 * @param string $name
	 * @param string $description
	 * @param string $example
	 * @param string $type
	 * @param bool   $required
	 * @param mixed  $maxLength
	 */
	public function __construct ($code, $name, $description, $example, $type = 'C', $required = false, $maxLength = 50) {
		$this->code = $code;
		$this->name = $name;
		$this->description = $description;
		$this->example = $example;
		$this->type = $type;
		$this->required = $required;
		$this->maxLength = $maxLength;
	}

	/**
	 * @return mixed
	 */
	public function getValue () {
		return $this->value;
	}

	/**
	 * @return mixed
	 */
	public function hasValue () {
		return !empty($this->value);
	}

	/**
	 * @param mixed $value
	 * @return Tag
	 */
	public function setValue ($value) {
		//never allow | pipes in stream
		if ($this->type == 'C') {
			$value = str_replace('|', '-', $value);
		}
		//special cases
		switch ($this->name) {
			case 'date_of_shipping':
				$date = ($value instanceof \DateTime) ? $value : new \DateTime($value);
			    $value = $date->format('d.m.Y');
				break;
			case 'product_short_description':
				$value = strtoupper($value);
				break;
			case 'express_service_flag_sat':
				$value = $value ? 'SCB' : '';
				break;
//			case 'express_service_flag':
//				$value = $value ? 'T' : '';
//				break;
			case 'send_email':
				$value = $value ? '1' : '0';
				break;
			case 'secondary_code':
				$value = str_replace("¬", "|",iconv("ISO-8859-1" ,"UTF-8//IGNORE", $value));
				break;
			default:

				break;
		}
		$this->value = $value;
		return $this;
	}

	/**
	 * @return bool
	 */
	public function validate () {
		if (($this->required && (!isset($this->value) || ($this->type == 'C' && $this->value == '')))
//			or ($this->type == 'C' && !is_string($this->value))
			or ($this->type == 'N' && !is_numeric($this->value))
			or (is_integer($this->maxLength) && strlen($this->value) > $this->maxLength)) {
			throw new \InvalidArgumentException(sprintf('Invalid value for tag %s: %s', $this->name, $this->value));
		}
		return true;
	}

	public function toStream () {
		$stream = 'T' . $this->code . ':' . $this->getValue();
		//special cases
		switch ($this->name) {
			case 'express_flag':
			case 'cash_flag':
				if ($this->value) {
					$stream = 'T' . $this->code . ':' . $this->example;
				}
			break;
			case 'parcel_weight':
				$stream = 'T' . $this->code . ':' . number_format($this->value, 1, ',', '.');
			break;
			default:
				if (!$this->value) {
					$stream = false;
				}
				break;
		}
		return $stream;
	}

}