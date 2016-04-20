<?php

namespace Bixie\Devos\Model\Shipment;

use Bixie\Framework\Utils\Query;
use YOOtheme\Framework\Application;
use YOOtheme\Framework\ApplicationAware;

class ShipmentGlsProvider extends ApplicationAware {

	protected $class = 'Bixie\Devos\Model\Shipment\ShipmentGls';
	protected $table = '@dv_shipment_gls';

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
	 * @return ShipmentGls
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
	 * @return bool|ShipmentGls
	 */
	public function find ($id) {
		return $this['db']->fetchObject("SELECT * FROM {$this->table} WHERE id = :id", compact('id'), $this->class);
	}

	/**
	 * Gets the shipment object by domestic_parcel_number_nl.
	 * @param  int $domestic_parcel_number_nl
	 * @return bool|ShipmentGls
	 */
	public function findDomesticParcelNumberNl ($domestic_parcel_number_nl) {
		return $this['db']->fetchObject(
			"SELECT * FROM {$this->table} WHERE domestic_parcel_number_nl = :domestic_parcel_number_nl",
			compact('domestic_parcel_number_nl'),
			$this->class
		);
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
		if (!isset($store['pdf_path'])) {
			$store['pdf_path'] = '';
		}
		if (!isset($store['zpl_template'])) {
			$store['zpl_template'] = '';
		}
		unset($store['pdf_url']);
		unset($store['png_url']);
		foreach (['date_of_shipping', 'created', 'modified'] as $dateField) {
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
	 * @return int
	 */
	public function lastParcelNumber ($gls_customer_number) {
		$query = Query::query('@dv_shipment_gls', 'parcel_number')
			->where('gls_customer_number = :gls_customer_number', compact('gls_customer_number'))
			->orderBy('parcel_number', 'DESC')
			->setLimit(0, 1);
		$result = $this['db']->fetchAssoc((string) $query, $query->getParams());
		return (int) $result['parcel_number'];
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
