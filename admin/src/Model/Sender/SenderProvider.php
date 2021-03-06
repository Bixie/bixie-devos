<?php

namespace Bixie\Devos\Model\Sender;

use Bixie\Framework\Utils\Query;
use Bixie\Framework\Application;
use Bixie\Framework\ApplicationAware;

class SenderProvider extends ApplicationAware {

	protected $class = 'Bixie\Devos\Model\Sender\Sender';
	protected $table = '@dv_sender';

	/**
	 * Constructor.
	 * @param Application $app
	 */
	public function __construct (Application $app) {
		$this->app = $app;
	}

	/**
	 * Gets the content object, if type object exists.
	 * @param  int $id
	 * @return Sender
	 */
	public function get ($id) {
		if ($object = $this->find($id)) {
			return $object;
		}
		return false;
	}

	/**
	 * Gets the shipment object by id.
	 * @param  int $id
	 * @return bool|Sender
	 */
	public function find ($id) {
		return $this['db']->fetchObject("SELECT * FROM {$this->table} WHERE id = :id", compact('id'), $this->class);
	}


	/**
	 * Gets all product objects.
	 * @param string $statement
	 * @param array  $params
	 * @param int    $start
	 * @param int    $limit
	 * @return array
	 */
	public function query ($statement, $params = [], $start = 0, $limit = 0) {
		$objects = [];

		if ($start || $limit) {
			$statement .= sprintf(' LIMIT %d, %d', $start, $limit);
		}

		/** @var Sender $object */
		foreach ($this['db']->fetchAllObjects($statement, $params, $this->class) as $object) {
			$objects[$object->getId()] = $object;
		}

		return $objects;
	}

	/**
	 * Saves the object.
	 * @param  array $data
	 * @return array
	 */
	public function save ($data) {
		$store = $data;
		$store['data'] = isset($store['data']) ? json_encode($store['data'], true): '[]';
		foreach (['created', 'modified'] as $dateField) {
			if (isset($store[$dateField]) && $store[$dateField] instanceof \DateTime) {
				$store[$dateField] = $store[$dateField]->format('Y-m-d H:i:s');
			}
		}
		if (!isset($store['id']) || !$store['id']) {
			$store['created'] = $this['date']->format('', 'Y-m-d H:i:s');
			$store['created_by'] = $this['user']->getId();
			$this['db']->insert($this->table, $store);
			$data['id'] = $this['db']->lastInsertId();
		} else {
			$store['modified'] = $this['date']->format('', 'Y-m-d H:i:s');
			$store['modified_by'] = $this['user']->getId();
			$this['db']->update($this->table, $store, ['id' => $store['id']]);
		}
		return $data;
	}

	/**
	 * @param int $id
	 * @param int $user_id
	 * @return bool
	 */
	public function setDefault ($id, $user_id) {

		$query = "UPDATE {$this->table} SET def = 0 WHERE user_id = :user_id";
		if ($result = $this['db']->executeQuery($query, ['user_id' => $user_id])) {

			return $this['db']->update($this->table, ['def' => 1], ['id' => $id]);
		}
		return false;
	}

	/**
	 * Deletes the object.
	 * @param  int $id
	 * @return mixed
	 */
	public function delete ($id) {
		return $this['db']->delete($this->table, compact('id'));
	}

}
