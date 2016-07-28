<?php

namespace Bixie\Devos\Apihost\Csrf;

use Bixie\Devos\Apihost\ApihostPlugin;
use Bixie\Framework\Csrf\DefaultCsrfProvider;

class CsrfProvider extends DefaultCsrfProvider
{
	/**
	 * @var string
	 */
	private $app;

	/**
	 * CsrfProvider constructor.
	 * @param string $app
	 * @param string $name
	 */
	public function __construct ($app, $name = '_csrf') {
		parent::__construct($name);
		$this->app = $app;
	}


	/**
     * {@inheritdoc}
     */
    public function generate()
    {
        return \JFactory::getSession()->getToken();
    }

	public function validate ($token) {
		//if api leave validation to api key
		if (!$this->app['request']->headers->get(ApihostPlugin::HEADER_KEY_TOKEN)) {
			return parent::validate($token);
		}
		return true;
	}


}
