<?php

namespace Bixie\Framework\Traits;


use Bixie\Framework\Utils\Arr;

trait DataTrait {

	protected $data;

	/**
	 * @return array
	 */
	public function getData () {
		if (is_string($this->data)) {
			$this->data = json_decode($this->data, true) ?: [];
		}
		return $this->data;
	}

	/**
	 * @param array $data
	 */
	public function addData ($data) {
		$this->data = array_merge($this->data, $data);
	}

	/**
	 * @param array $data
	 */
	public function setData ($data) {
		$this->data = $data;
	}

	/**
	 * Checks if a key exists.
	 * @param  string $key
	 * @return bool
	 */
	public function offsetExists ($key) {
		return Arr::has($this->getData(), $key);
	}

	/**
	 * Gets a value by key.
	 * @param  string $key
	 * @return mixed
	 */
	public function offsetGet ($key) {
		return Arr::get($this->getData(), $key);
	}

	/**
	 * Sets a value.
	 * @param string $key
	 * @param string $value
	 */
	public function offsetSet ($key, $value) {
		Arr::set($this->getData(), $key, $value);
	}

	/**
	 * Unset a value.
	 * @param string $key
	 */
	public function offsetUnset ($key) {
		Arr::remove($this->getData(), $key);
	}


}