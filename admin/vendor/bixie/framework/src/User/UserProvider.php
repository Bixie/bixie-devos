<?php

namespace Bixie\Framework\User;

use YOOtheme\Framework\Application;
use Bixie\Framework\Utils\Query;
use YOOtheme\Framework\User\UserProviderInterface;

class UserProvider implements UserProviderInterface
{
    /**
     * @var string
     */
    protected $asset;

    /**
     * @var string[]
     */
    protected $permissions;

    /**
     * @var Application
     */
    protected $app;

    /**
     * Constructor.
     *
     * @param Application $app
     * @param string   $asset
     * @param string[] $permissions
     */
    public function __construct(Application $app, $asset, $permissions = array())
    {
        $this->asset       = $asset;
        $this->permissions = $permissions;
		$this->app = $app;
	}

    /**
     * {@inheritdoc}
     */
    public function get($id = null)
    {
        return $this->loadUserBy('id', $id);
    }

    /**
     * {@inheritdoc}
     */
    public function getByUsername($username)
    {
        return $this->loadUserBy('username', $username);
    }

    /**
     * Loads a user.
     *
     * @param  string $field
     * @param  string $value
     * @return \YOOtheme\Framework\User\UserInterface
     */
    protected function loadUserBy($field, $value)
    {
        if (in_array($field, array('id', 'username')) && $user = \JFactory::getUser($value)) {

			return $this->loadUser($user);

        }
		return false;
    }

    /**
     * Loads a user.
     *
     * @param  string $field
     * @param  string $value
     * @return \YOOtheme\Framework\User\UserInterface
     */
    protected function loadUserByProfile($field, $value)
    {

		$query = Query::query('@users AS u', 'id')->where('u.block = :block', ['block' => 0]);

		$query->join('@user_profiles AS upf ON upf.user_id = u.id AND upf.profile_key = :field', ['field' => 'profile.' . $field])
			->where('upf.profile_value = :value', ['value' => json_encode($value)]);

		$result = $this->app['db']->fetchAssoc((string) $query, $query->getParams());

		if (!empty($result['id']) && $user = \JFactory::getUser($result['id'])) {

			return $this->loadUser($user);

        }
		return false;
    }

	/**
	 * Loads a user.
	 *
	 * @param \JUser $user
	 * @return \YOOtheme\Framework\User\UserInterface
	 */
	protected function loadUser(\JUser $user)
	{

		$permissions = array();

		foreach($this->permissions as $jpermission => $permission) {
			if ($user->authorise($jpermission, $this->asset)) {
				$permissions[] = $permission;
			}
		}

		return new User([
			'id' => $user->id,
			'username' => $user->username,
			'name' => $user->name,
			'email' => $user->email,
			'data' => $user->id ? \JUserHelper::getProfile($user->id)->profile : [],
			'permissions' => $permissions
		]);

	}

}
