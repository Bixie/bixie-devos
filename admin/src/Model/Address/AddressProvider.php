<?php

namespace Bixie\Devos\Model\Address;

use Bixie\Framework\Application;
use Bixie\Framework\ApplicationAware;
use Bixie\Framework\Utils\Query;

class AddressProvider extends ApplicationAware {

	protected $class = 'Bixie\Devos\Model\Address\Address';
	protected $table = '@dv_address';

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
	 * @return bool|Address
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
	 * @return bool|Address
	 */
	public function find ($id) {
		return $this['db']->fetchObject("SELECT * FROM {$this->table} WHERE id = :id", compact('id'), $this->class);
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
	 * Gets all product objects.
	 * @param Query $query
	 * @param array  $params
	 * @param int    $start
	 * @param int    $limit
	 * @return array
	 */
	public function query (Query $query, $start = 0, $limit = 0) {
		$objects = [];

		if ($start || $limit) {
            $query->setLimit($start, $limit);
		}

		/** @var Address $object */
        foreach ($this['db']->fetchAllObjects((string) $query, $query->getParams(), $this->class) as $object) {
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
        foreach (['additional_text'] as $key) {
            if (!isset($store[$key])) {
                $store[$key] = '';
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
