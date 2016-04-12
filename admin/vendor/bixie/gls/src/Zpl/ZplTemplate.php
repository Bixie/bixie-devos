<?php

namespace Bixie\Gls\Zpl;

use Zebra\Client;
use Zebra\Zpl\Builder;
use Zebra\Zpl\Image;

class ZplTemplate {
	/**
	 * @var string
	 */
	protected $raw_string = '';
	/**
	 * @var string
	 */
	protected $output = '';

	protected $zpl;
	/**
	 * ZplTemplate constructor.
	 * @param string $raw_string
	 */
	public function __construct ($raw_string) {
		$this->raw_string = $raw_string;
		$this->zpl = new Builder();

	}

	/**
	 * @return string
	 */
	public function render () {
		$this->output = $this->raw_string;

		$addition = $this->zpl->toZpl();

		return sprintf("%s\r\n[SENDER LOGO]\r\n%s", $this->raw_string, $addition);
	}

	public function addSenderLogo ($path) {
		if ($path && file_exists($path)) {
			$image = new Image(file_get_contents($path));

			$this->zpl->fo(50, 50);
			$this->zpl->gf($image);
			$this->zpl->fs();
		}
		return $this;
	}

	public function toPrinter ($ip = '10.0.0.50') {
		$client = new Client($ip);
		$client->send($this->zpl);

	}

}