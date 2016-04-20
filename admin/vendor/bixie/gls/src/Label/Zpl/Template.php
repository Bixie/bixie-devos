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
			$this->raw_string = preg_replace('/DG001\.(.*)\^XA/', $this->zpl->imageString('DG001', $this->image), $this->raw_string);
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