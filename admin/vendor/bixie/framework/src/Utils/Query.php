<?php


namespace Bixie\Framework\Utils;


class Query {
	/**
	 * @var \JDatabaseQuery
	 */
	protected $query;
	/**
	 * @var array
	 */
	protected $params = array();
	/**
	 * @var \JDatabaseDriver
	 */
	protected static $db;

	/**
	 * Query constructor.
	 * @param $query
	 */
	public function __construct ($query) {
		$this->query = $query;
	}

	/**
	 * @return string
	 */
	public function __toString () {
		return (string) $this->query;
	}

	/**
	 * @param string $table
	 * @param string $select
	 * @return Query
	 */
	public static function query ($table, $select = '*') {
		if (!isset(static::$db)) {
			static::$db = \JFactory::getDBO();
		}
		return (new Query(static::$db->getQuery(true)->from($table)->select($select)));
	}

	/**
	 * @param string  $select
	 * @param array  $params
	 * @return $this
	 */
	public function select ($select, $params = array()) {
		$this->addParams($params);
		$this->query->select($select);
		return $this;
	}

	/**
	 * @param mixed  $conditions
	 * @param array  $params
	 * @param string $glue
	 * @return $this
	 */
	public function where ($conditions, $params = array(), $glue = 'AND') {
		$this->addParams($params);
		$this->query->where($conditions, $glue);
		return $this;
	}

	/**
	 * @param string  $key
	 * @param array  $set
	 * @param string $glue
	 * @return $this
	 */
	public function whereInSet ($key, $set = array(), $glue = 'AND') {
		//$this->addParams($params); //todo update Yframework??
		$this->query->where("$key IN ('" . implode("','", $set) . "')", $glue);
		return $this;
	}

	/**
	 * @param string  $condition
	 * @param array  $params
	 * @param string $type
	 * @return $this
	 */
	public function join ($condition, $params = array(), $type = 'INNER') {
		$this->addParams($params);
		$this->query->join($type, $condition);
		return $this;
	}

	/**
	 * @param $columns
	 * @return $this
	 */
	public function order ($columns) {
		$this->query->order($columns);
		return $this;
	}

	/**
	 * @param $columns
	 * @return $this
	 */
	public function group ($columns) {
		$this->query->group($columns);
		return $this;
	}

	/**
	 * @param array $params
	 * @return $this
	 */
	public function addParams ($params) {
		$this->params = array_merge($this->params, $params);
		return $this;
	}
	/**
	 * @return array
	 */
	public function getParams () {
		return $this->params;
	}

}