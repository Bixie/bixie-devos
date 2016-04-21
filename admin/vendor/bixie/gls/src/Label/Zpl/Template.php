<?php

namespace Bixie\Gls\Label\Zpl;

use Zebra\Client;
use Zebra\Zpl\Image;

class Template {
	/**
	 * @var string
	 */
	protected $raw_string = '';
	/**
	 * @var string
	 */
	protected $output = '';
	/**
	 * @var Image
	 */
	protected $image = '';
	/**
	 * @var Builder
	 */
	protected $zpl;
	/**
	 * Template constructor.
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
		if ($this->output) {
			return $this->output;
		}

		if ($this->image) {
			if (preg_match('/DG001\.GRF,02688(.*)\^XA/', $this->raw_string)) { //testomgeving zpl

				$this->raw_string = preg_replace('/DG001\.(.*)\^XA/', $this->zpl->imageString('DG001', $this->image), $this->raw_string);

			} else { //live zpl

				$this->raw_string = preg_replace([
					'/DG000\.(.*)\^XA/',
					'/\^FO36,1015\^XG000\.GRF,1,1\^FS/',
					'/\^FO780,453\^FR\^GB0,588,4\^FS/',
					'/\^FO382,588\^FR\^GB241,0,8\^FS/',
					'/\^FO37,453\^FR\^GB0,588,4\^FS/'

				], [
					$this->zpl->imageString('DG001', $this->image),
					'^FO560,1075^XG001.GRF,1,1^FS',
					'^FO780,453^FR^GB0,728,4^FS',
					'^FO37,1180^FR^GB747,0,4^FS',
					'^FO37,453^FR^GB0,728,4^FS'
				], $this->raw_string);

			}
		}
//		$this->raw_string = preg_replace('/(\t|\r|\n)/', '', $this->raw_string);

		return $this->output = $this->raw_string;
	}

	/**
	 * @param $path
	 * @return $this
	 */
	public function addSenderLogo ($path) {
		if ($path && file_exists($path)) {
			$this->image = new Image(file_get_contents($path));
		}
		return $this;
	}
	
}