<?php

namespace Bixie\Devos\Model\GlsTracking;


use Bixie\Framework\Traits\DataTrait;
use Bixie\Framework\Traits\HydrateTrait;

class GlsTracking implements \JsonSerializable, \ArrayAccess {

	use DataTrait, HydrateTrait;

	const GLSTRACKING_STATE_FAIL = 0;
	const GLSTRACKING_STATE_SUCCESS = 1;
	/**
	 * @var integer
	 */
	protected $id = 0;
	/**
	 * @var integer
	 */
	protected $state = 0;
	/**
	 * @var string
	 */
	protected $filename;
	/**
	 * @var string|\DateTime
	 */
	protected $created = '';
	/**
	 * @var integer
	 */
	protected $gls_number = 0;
	/**
	 * @var string|\DateTime
	 */
	protected $date_from = '';
	/**
	 * @var string|\DateTime
	 */
	protected $date_to = '';
	/**
	 * @var string|array
	 */
	protected $parcels;
	/**
	 * @var string|array
	 */
	protected $events;


	public static function create ($data = []) {
		return self::hydrate($data, 'Bixie\Devos\Model\GlsTracking\GlsTracking');
	}

	public function mergeData ($data) {
		return $this->merge($data, 'Bixie\Devos\Model\GlsTracking\GlsTracking');
	}

	public static function getStates () {
		return [
			self::GLSTRACKING_STATE_FAIL => 'Mislukt',
			self::GLSTRACKING_STATE_SUCCESS => 'Geslaagd'
		];
	}

	/**
	 * @return int
	 */
	public function getId () {
		return $this->id;
	}

	/**
	 * @param int $id
	 * @return Sender
	 */
	public function setId ($id) {
		$this->id = $id;
		return $this;
	}

	/**
	 * @return int
	 */
	public function getState () {
		return $this->state;
	}

	/**
	 * @param int $state
	 * @return GlsTracking
	 */
	public function setState ($state) {
		$this->state = $state;
		return $this;
	}

	/**
	 * @return string
	 */
	public function getFilename () {
		return $this->filename;
	}

	/**
	 * @param string $filename
	 * @return GlsTracking
	 */
	public function setFilename ($filename) {
		$this->filename = $filename;
		return $this;
	}

	/**
	 * @return int
	 */
	public function getGlsNumber () {
		return $this->gls_number;
	}

	/**
	 * @param int $gls_number
	 * @return GlsTracking
	 */
	public function setGlsNumber ($gls_number) {
		$this->gls_number = $gls_number;
		return $this;
	}

	/**
	 * @return \DateTime|string
	 */
	public function getDateFrom () {
		if (is_string($this->date_from)) {
			$this->date_from = new \DateTime($this->date_from);
		}
		return $this->date_from;
	}

	/**
	 * @param \DateTime|string $date_from
	 * @return GlsTracking
	 */
	public function setDateFrom ($date_from) {
		$this->date_from = $date_from;
		return $this;
	}

	/**
	 * @return \DateTime|string
	 */
	public function getDateTo () {
		if (is_string($this->date_to)) {
			$this->date_to = new \DateTime($this->date_to);
		}
		return $this->date_to;
	}

	/**
	 * @param \DateTime|string $date_to
	 * @return GlsTracking
	 */
	public function setDateTo ($date_to) {
		$this->date_to = $date_to;
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
	 * @return array
	 */
	public function getParcels () {
		if (is_string($this->parcels)) {
			$this->parcels = json_decode($this->parcels, true) ?: [];
		}
		return $this->parcels;
	}

	/**
	 * @param array $parcels
	 */
	public function setParcels ($parcels) {
		$this->parcels = $parcels;
	}

	/**
	 * @return array
	 */
	public function getEvents () {
		if (is_string($this->events)) {
			$this->events = json_decode($this->events, true) ?: [];
		}
		return $this->events;
	}

	/**
	 * @param array $events
	 */
	public function setEvents ($events) {
		$this->events = $events;
	}

	/**
	 * {@inheritdoc}
	 */
	public function toArray () {
		return [
			'id' => $this->id,
			'state' => $this->state,
			'created' => $this->getCreated()->format(\DateTime::ATOM),
			'filename' => $this->filename,
			'gls_number' => $this->gls_number,
			'date_from' => $this->getDateFrom()->format(\DateTime::ATOM),
			'date_to' => $this->getDateTo()->format(\DateTime::ATOM),
			'parcels' => $this->getParcels(),
			'events' => $this->getEvents(),
			'data' => $this->getData()
		];
	}

	/**
	 * Specify data which should be serialized to JSON
	 * @link  http://php.net/manual/en/jsonserializable.jsonserialize.php
	 * @return mixed data which can be serialized by <b>json_encode</b>,
	 * which is a value of any type other than a resource.
	 * @since 5.4.0
	 */
	function jsonSerialize () {
		$data = $this->toArray();
		return $data;
	}
}
