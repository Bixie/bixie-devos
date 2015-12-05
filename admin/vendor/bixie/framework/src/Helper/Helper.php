<?php

namespace Bixie\Framework\Helper;


class Helper implements \ArrayAccess
{

	protected $helpers = array();

	/**
	 * @param $name
	 * @param $instance
	 * @return $this
	 */
	public function addHelper ($name, $instance) {
		$this->helpers[$name] = $instance;
		return $this;
	}

	/**
	 * Whether an helpers exists.
	 *
	 * @param  string $offset
	 * @return mixed
	 */
	public function offsetExists($offset)
	{
		return isset($this->helpers[$offset]);
	}

	/**
	 * Gets a helper.
	 *
	 * @param  string $offset
	 * @return mixed
	 */
	public function offsetGet($offset)
	{
		return $this->helpers[$offset];
	}

	/**
	 * Sets a helper.
	 *
	 * @param  string $offset
	 * @param  mixed  $value
	 */
	public function offsetSet($offset, $value)
	{
		$this->helpers[$offset] = $value;
	}

	/**
	 * Unsets a helper.
	 *
	 * @param  string $offset
	 */
	public function offsetUnset($offset)
	{
		unset($this->helpers[$offset]);
	}

}
