<?php

namespace Bixie\Framework\Helper;


use Bixie\Framework\Application;
use Bixie\Framework\ApplicationAware;

class BaseHelper extends ApplicationAware {


	/**
	 * BaseHelper constructor.
	 * @param Application $app
	 */
	public function __construct (Application $app) {
		$this->app = $app;
	}

}