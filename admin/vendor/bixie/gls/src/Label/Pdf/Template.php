<?php

namespace Bixie\Gls\Label\Pdf;

use Bixie\Gls\Label\Labelary;

class Template {

	/**
	 * @var string
	 */
	protected $zpl_string = '';
	/**
	 * @var string
	 */
	protected $output = '';
	/**
	 * @var Labelary
	 */
	protected $api;
	/**
	 * Template constructor.
	 * @param string $zpl_string
	 */
	public function __construct ($zpl_string) {
		$this->zpl_string = $zpl_string;
		$this->api = new Labelary();
	}

	/**
	 * @return string
	 */
	public function render () {
		if ($this->output) {
			return $this->output;
		}

		return $this->output = $this->api->getPdf($this->zpl_string);
	}
}