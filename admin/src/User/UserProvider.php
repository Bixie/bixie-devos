<?php

namespace Bixie\Devos\User;

use Bixie\Framework\User\UserProvider as UserProviderBase;
use YOOtheme\Framework\User\UserProviderInterface;

class UserProvider extends UserProviderBase implements UserProviderInterface
{

	/**
	 * {@inheritdoc}
	 */
	public function get($id = null)
	{
		if ($this->app['apitoken']->getToken()) {
			return $this->getByApiUsername($this->app['apitoken']->getName());
		} else {
			return $this->loadUserBy('id', $id);
		}
	}

    /**
     */
    public function getByApiUsername($api_username)
    {
        return $this->loadUserByProfile('api_username', $api_username);
    }


}
