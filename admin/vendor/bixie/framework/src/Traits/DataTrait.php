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
		$this->getData();
		return Arr::has($this->data, $key);
	}

	/**
	 * Gets a value by key.
	 * @param  string $key
	 * @return mixed
	 */
	public function offsetGet ($key) {
		$this->getData();
		return Arr::get($this->data, $key);
	}

	/**
	 * Sets a value.
	 * @param string $key
	 * @param string $value
	 */
	public function offsetSet ($key, $value) {
		$this->getData();
		Arr::set($this->data, $key, $value);
	}

	/**
	 * Unset a value.
	 * @param string $key
	 */
	public function offsetUnset ($key) {
		$this->getData();
		Arr::remove($this->data, $key);
	}


}