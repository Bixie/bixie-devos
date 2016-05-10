<?php

namespace Bixie\Devos\Model\GlsTracking;

use Bixie\Framework\Utils\Query;
use YOOtheme\Framework\Application;
use YOOtheme\Framework\ApplicationAware;

class GlsTrackingProvider extends ApplicationAware {

	protected $class = 'Bixie\Devos\Model\GlsTracking\GlsTracking';
	protected $table = '@dv_gls_tracking';

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
	 * @return GlsTracking
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
	 * @return bool|GlsTracking
	 */
	public function find ($id) {
		return $this['db']->fetchObject("SELECT * FROM {$this->table} WHERE id = :id", compact('id'), $this->class);
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

		/** @var GlsTracking $object */
		foreach ($this['db']->fetchAllObjects($query, $query->getParams(), $this->class) as $object) {
			$objects[$object->getId()] = $object;
		}

		return $objects;
	}

	/**
	 * Gets latest filenames
	 * @param \DateInterval  $period
	 * @return array
	 */
	public function lastFilenames (\DateInterval $period = null) {
		$filenames = [];
		$date_from = new \DateTime();
		$date_from->sub(($period ?: new \DateInterval('P1M')));

		$query = Query::query('@dv_gls_tracking', 'id, filename, state')
			->where('date_from > :date_from', ['date_from' => $date_from->format('Y-m-d H:i:s')]);

		$res = $this['db']->fetchAll($query, $query->getParams());
		foreach ($res as $row) {
			$filenames[$row['filename']] = $row;
		}

		return $filenames;
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
	public function save (&$data) {
		$store = $data;
		$store['parcels'] = isset($store['parcels']) ? json_encode($store['parcels'], true): '[]';
		$store['events'] = isset($store['events']) ? json_encode($store['events'], true): '[]';
		$store['data'] = isset($store['data']) ? json_encode($store['data'], true): '[]';
		foreach (['created', 'date_from', 'date_to'] as $dateField) {
			if (isset($store[$dateField]) && $store[$dateField] instanceof \DateTime) {
				$store[$dateField] = $store[$dateField]->format('Y-m-d H:i:s');
			}
		}
		if (!isset($store['id']) || !$store['id']) {
			$store['created'] = $this['date']->format('', 'Y-m-d H:i:s');
			$this['db']->insert($this->table, $store);
			$data['id'] = $this['db']->lastInsertId();
		} else {
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
