<?php

namespace Bixie\Framework\User;

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
     * Constructor.
     *
     * @param string   $asset
     * @param string[] $permissions
     */
    public function __construct($asset, $permissions = array())
    {
        $this->asset       = $asset;
        $this->permissions = $permissions;
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
}
