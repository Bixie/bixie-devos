<?php

namespace Bixie\Devos\Model\Shipment;

use Bixie\Framework\Utils\Query;
use Bixie\Framework\Application;
use Bixie\Framework\ApplicationAware;

class ShipmentSendCloudProvider extends ApplicationAware {

	protected $class = 'Bixie\Devos\Model\Shipment\ShipmentSendCloud';
	protected $table = '@dv_shipment_sendcloud';
	
	protected static $cacheTracking = [];

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
	 * @return ShipmentSendCloud
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
	 * @return bool|ShipmentSendCloud
	 */
	public function find ($id) {
		return $this['db']->fetchObject("SELECT * FROM {$this->table} WHERE id = :id", compact('id'), $this->class);
	}

	/**
	 * Gets the shipment object by domestic_parcel_number_nl.
	 * @param string $tracking_number
	 * @return bool|ShipmentSendCloud
	 */
	public function findTrackingNumber ($tracking_number) {
		if (!isset(static::$cacheTracking[$tracking_number])) {
			static::$cacheTracking[$tracking_number] = $this['db']->fetchObject(
				"SELECT * FROM {$this->table} WHERE tracking_number = :tracking_number",
				compact('tracking_number'),
				$this->class
			);
		}
		return static::$cacheTracking[$tracking_number];
	}

	/**
	 * Gets all product objects.
	 * @param Query $query
	 * @param int    $start
	 * @param int    $limit
	 * @return array
	 */
	public function query (Query $query, $start = 0, $limit = 0) {
		$objects = [];

		if ($start || $limit) {
			$query->setLimit($start, $limit);
		}

		/** @var ShipmentGls $object */
		foreach ($this['db']->fetchAllObjects((string) $query, $query->getParams(), $this->class) as $object) {
			$objects[$object->getId()] = $object;
		}

		return $objects;
	}

	/**
	 * Gets all product objects.
	 * @param Query $query
	 * @return int
	 */
	public function count (Query $query) {
		$q = clone $query;
		$q->clear('order')->clear('select')->select('COUNT(*) AS cnt');
		$res = $this['db']->fetchAssoc((string) $q, $q->getParams());

		return (int) $res['cnt'];
	}

	/**
	 * Saves the object.
	 * @param  array $data
	 * @return array
	 */
	public function save ($data) {
		$store = $data;
		if (isset($store['data'])) {
			$store['data'] = !empty($store['data']) ? json_encode($store['data'], true) : '[]';
		}
		if (isset($store['parcel'])) {
			$store['parcel'] = !empty($store['parcel']) ? json_encode($store['parcel'], true) : '[]';
		}
		if (isset($store['events'])) {
			$store['events'] = !empty($store['events']) ? json_encode($store['events'], true) : '[]';
		}
		if (isset($store['shipping_method'])) { //used in js to avoid scope conflict
			$store['shipment'] = $store['shipping_method'];
		}
        foreach (['zpl_template', 'pdf_path', 'company_name', 'email', 'telephone', 'tracking_number'] as $key) {
            if (!isset($store[$key])) {
                $store[$key] = '';
            }
        }
		unset($store['shipping_method']);
		unset($store['pdf_url']);
		unset($store['png_url']);
		unset($store['contact']);
		unset($store['statusname']);
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
	 * Deletes the object.
	 * @param  int $id
	 * @return mixed
	 */
	public function delete ($id) {
		return $this['db']->delete($this->table, compact('id'));
	}

}
