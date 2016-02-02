<?php

namespace Bixie\Framework\Traits;


trait HydrateTrait {

	/**
	 * Cache class reflections.
	 *
	 * @var array
	 */
	protected static $reflClasses = [];

	/**
	 * Cache class reflection properties.
	 *
	 * @var array
	 */
	protected static $reflFields = [];

	protected function merge ($data, $class = 'stdClass') {

		$reflFields = self::getReflectionFields($class);

		foreach ($data as $key => $value) {
			if ('stdClass' === $class) {
				$this->$key = $value;
			} elseif (isset($reflFields[$key])) {
				$reflFields[$key]->setValue($this, $value);
			}
		}

		return $this;

	}
	/**
	 * Creates object from data.
	 * @param  array  $data
	 * @param  string $class
	 * @param  array  $args
	 * @return mixed
	 */
	protected static function hydrate ($data, $class = 'stdClass', $args = []) {

		$reflClass = self::getReflectionClass($class);
		$reflFields = self::getReflectionFields($class);

		$instance = $args ? $reflClass->newInstanceArgs($args) : $reflClass->newInstance();

		foreach ($data as $key => $value) {
			if ('stdClass' === $class) {
				$instance->$key = $value;
			} elseif (isset($reflFields[$key])) {
				$reflFields[$key]->setValue($instance, $value);
			}
		}

		return $instance;
	}

	/**
	 * Gets ReflectionClass for given class name.
	 * @param  string $class
	 * @return \ReflectionClass
	 */
	protected static function getReflectionClass ($class) {
		if (!isset(self::$reflClasses[$class])) {
			self::$reflClasses[$class] = new \ReflectionClass($class);
		}

		return self::$reflClasses[$class];
	}

	/**
	 * Gets ReflectionProperty array for given class name.
	 * @param  string $class
	 * @return \ReflectionProperty[]
	 */
	protected static function getReflectionFields ($class) {
		if (!isset(self::$reflFields[$class])) {

			self::$reflFields[$class] = array();

			foreach (self::getReflectionClass($class)->getProperties() as $property) {
				$property->setAccessible(true);
				self::$reflFields[$class][$property->getName()] = $property;
			}
		}

		return self::$reflFields[$class];
	}

}