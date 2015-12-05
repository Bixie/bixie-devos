<?php

namespace Bixie\Framework\Helper;


use YOOtheme\Framework\Application;
use YOOtheme\Framework\ApplicationAware;

class BaseHelper extends ApplicationAware {


	/**
	 * BaseHelper constructor.
	 * @param Application $app
	 */
	public function __construct (Application $app) {
		$this->app = $app;
	}

}