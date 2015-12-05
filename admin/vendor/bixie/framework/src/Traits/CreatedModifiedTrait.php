<?php

namespace Bixie\Framework\Traits;


trait CreatedModifiedTrait {

	/**
	 * @var integer
	 */
	protected $created_by = 0;
	/**
	 * @var string|\DateTime
	 */
	protected $created = '';
	/**
	 * @var integer
	 */
	protected $modified_by = 0;
	/**
	 * @var string|\DateTime
	 */
	protected $modified = '';

	/**
	 * @return int
	 */
	public function getCreatedBy () {
		return $this->created_by;
	}

	/**
	 * @param int $created_by
	 * @return $this
	 */
	public function setCreatedBy ($created_by) {
		$this->created_by = $created_by;
		return $this;
	}

	/**
	 * @return \Datetime
	 */
	public function getCreated () {
		if (is_string($this->created)) {
			$this->created = new \DateTime($this->created);
		}
		return $this->created;
	}

	/**
	 * @param string $created
	 * @return $this
	 */
	public function setCreated ($created) {
		$this->created = $created;
		return $this;
	}

	/**
	 * @return int
	 */
	public function getModifiedBy () {
		return $this->modified_by;
	}

	/**
	 * @param int $modified_by
	 * @return $this
	 */
	public function setModifiedBy ($modified_by) {
		$this->modified_by = $modified_by;
		return $this;
	}

	/**
	 * @return \Datetime
	 */
	public function getModified () {
		if (is_string($this->modified)) {
			$this->modified = new \DateTime($this->modified);
		}
		return $this->modified;
	}

	/**
	 * @param string $modified
	 * @return $this
	 */
	public function setModified ($modified) {
		$this->modified = $modified;
		return $this;
	}
}