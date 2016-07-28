<?php

namespace Bixie\Devos\Apihost;

use Bixie\Devos\Apihost\Apitoken\Exception\ApitokenException;
use Bixie\Devos\Apihost\Apitoken\Provider\RequestApitokenProvider;
use Bixie\Devos\Apihost\Csrf\CsrfProvider;
use Bixie\Devos\Apihost\Request\RequestParameters;
use Bixie\Framework\Application;
use Bixie\Framework\Event\Event;
use Bixie\Framework\Plugin\Plugin;
use Bixie\Framework\Routing\Request;

class ApihostPlugin extends Plugin
{
	const HEADER_KEY_TOKEN = 'X-DEVOS-API-TOKEN';
	const REQUEST_KEY_TOKEN = '_api_token';
	const HEADER_KEY_SALT = 'X-DEVOS-API-SALT';
	const REQUEST_KEY_SALT = '_api_salt';
	const HEADER_KEY_USERNAME = 'X-DEVOS-API-USERNAME';
	const REQUEST_KEY_USERNAME = '_api_username';

	/**
     * {@inheritdoc}
     */
    public function main(Application $app)
    {

        $app->on('boot', array($this, 'boot'));
        $app->on('request', array($this, 'onRequest'));
    }

    /**
     * Callback for 'boot' event.
     */
    public function boot($event, $app)
    {
		$app['apitoken'] = new RequestApitokenProvider();
		//override csrf
		$app['csrf'] = function ($app) {
			return new CsrfProvider($app);
		};

	}

    /**
     * Callback to initialize app.
     */
    public function init()
    {

    }

	/**
	 * Checks for the API token and throws 401 exception if invalid.
	 * @param Event $event
	 * @throws ApitokenException
	 */
	public function onRequest(Event $event)
	{
		/** @var Request $request */
		$request = $event['request'];
		$this['apitoken']->setToken($request->get(self::REQUEST_KEY_TOKEN, $request->headers->get(self::HEADER_KEY_TOKEN)));
		$this['apitoken']->setSalt($request->get(self::REQUEST_KEY_SALT, $request->headers->get(self::HEADER_KEY_SALT)));
		$access = $request->attributes->get('access', '');
		if (in_array($access, ['client_devos', 'manage_devos'])) {
			$params = new RequestParameters($request->request->all());

			//if api request, find user
			if ($this['apitoken']->getToken()) {

				//todo refuse routes !$/api

				$api_username = $request->get(self::REQUEST_KEY_USERNAME, $request->headers->get(self::HEADER_KEY_USERNAME));

				if (!$user = $this->app['users']->getByApiUsername($api_username)) {
					throw new ApitokenException(401, 'Invalid API user name.');
				}
				$this['apitoken']->setName($api_username);
				$this['apitoken']->setPrivateKey($user['api_private_key']);

				if ($access == 'manage_devos' && !$user->hasPermission('manage_devos')) {
					throw new ApitokenException(403, 'Insufficient User Rights.');
				}
				
				if (!$this['apitoken']->validate($params)) {
				$s = $params->toTokenString();
					throw new ApitokenException(401, 'Invalid API token.');
				}
			}

		}
	}

}
