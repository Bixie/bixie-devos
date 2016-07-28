<?php

namespace Bixie\Devos\Model\Sender;


use Bixie\Framework\Traits\CreatedModifiedTrait;
use Bixie\Framework\Traits\DataTrait;

class Sender extends SenderBase implements \JsonSerializable, \ArrayAccess {

	use CreatedModifiedTrait, DataTrait;

	const SENDER_STATE_INACTIVE = 0;
	const SENDER_STATE_ACTIVE = 1;
	/**
	 * @var integer
	 */
	protected $id = 0;
	/**
	 * @var integer
	 */
	protected $user_id = 0;
	/**
	 * @var string
	 */
	protected $sender_name_1 = '';
	/**
	 * @var string
	 */
	protected $sender_name_2 = '';
	/**
	 * @var string
	 */
	protected $sender_street = '';
	/**
	 * @var string
	 */
	protected $sender_country = '';
	/**
	 * @var string
	 */
	protected $sender_zip = '';
	/**
	 * @var string
	 */
	protected $sender_city = '';
	/**
	 * @var string
	 */
	protected $sender_logo = '';
	/**
	 * @var string
	 */
	protected $sender_email = '';
	/**
	 * @var string
	 */
	protected $sender_contact = '';
	/**
	 * @var string
	 */
	protected $sender_phone = '';
	/**
	 * @var string
	 */
	protected $message_subject = '';
	/**
	 * @var int
	 */
	protected $state = 1;
	/**
	 * @var int
	 */
	protected $def = 0;

	public static function getStates () {
		return [
			self::SENDER_STATE_INACTIVE => 'Inactief',
			self::SENDER_STATE_ACTIVE => 'Actief'
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
	public function getUserId () {
		return $this->user_id;
	}

	/**
	 * @param int $user_id
	 * @return Sender
	 */
	public function setUserId ($user_id) {
		$this->user_id = $user_id;
		return $this;
	}


	/**
	 * @return string
	 */
	public function getSenderName1 () {
		return $this->sender_name_1;
	}

	/**
	 * @param string $sender_name_1
	 * @return Sender
	 */
	public function setSenderName1 ($sender_name_1) {
		$this->sender_name_1 = $sender_name_1;
		return $this;
	}

	/**
	 * @return string
	 */
	public function getSenderName2 () {
		return $this->sender_name_2;
	}

	/**
	 * @param string $sender_name_2
	 * @return Sender
	 */
	public function setSenderName2 ($sender_name_2) {
		$this->sender_name_2 = $sender_name_2;
		return $this;
	}

	/**
	 * @return string
	 */
	public function getSenderStreet () {
		return $this->sender_street;
	}

	/**
	 * @param string $sender_street
	 * @return Sender
	 */
	public function setSenderStreet ($sender_street) {
		$this->sender_street = $sender_street;
		return $this;
	}

	/**
	 * @return string
	 */
	public function getSenderCountry () {
		return $this->sender_country;
	}

	/**
	 * @param string $sender_country
	 * @return Sender
	 */
	public function setSenderCountry ($sender_country) {
		$this->sender_country = $sender_country;
		return $this;
	}

	/**
	 * @return string
	 */
	public function getSenderZip () {
		return $this->sender_zip;
	}

	/**
	 * @param string $sender_zip
	 * @return Sender
	 */
	public function setSenderZip ($sender_zip) {
		$this->sender_zip = $sender_zip;
		return $this;
	}

	/**
	 * @return string
	 */
	public function getSenderCity () {
		return $this->sender_city;
	}

	/**
	 * @param string $sender_city
	 * @return Sender
	 */
	public function setSenderCity ($sender_city) {
		$this->sender_city = $sender_city;
		return $this;
	}
	/**
	 * @return string
	 */
	public function getSenderLogo () {
		return $this->sender_logo;
	}

	/**
	 * @param string $sender_logo
	 * @return Sender
	 */
	public function setSenderLogo ($sender_logo) {
		$this->sender_logo = $sender_logo;
		return $this;
	}

	/**
	 * @return string
	 */
	public function getSenderEmail () {
		return $this->sender_email;
	}

	/**
	 * @param string $sender_email
	 * @return Sender
	 */
	public function setSenderEmail ($sender_email) {
		$this->sender_email = $sender_email;
		return $this;
	}

	/**
	 * @return string
	 */
	public function getSenderContact () {
		return $this->sender_contact;
	}

	/**
	 * @param string $sender_contact
	 * @return Sender
	 */
	public function setSenderContact ($sender_contact) {
		$this->sender_contact = $sender_contact;
		return $this;
	}

	/**
	 * @return string
	 */
	public function getSenderPhone () {
		return $this->sender_phone;
	}

	/**
	 * @param string $sender_phone
	 * @return Sender
	 */
	public function setSenderPhone ($sender_phone) {
		$this->sender_phone = $sender_phone;
		return $this;
	}

	/**
	 * @return string
	 */
	public function getMessageSubject () {
		return $this->message_subject;
	}

	/**
	 * @param string $message_subject
	 * @return Sender
	 */
	public function setMessageSubject ($message_subject) {
		$this->message_subject = $message_subject;
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
	 * @return Sender
	 */
	public function setState ($state) {
		$this->state = $state;
		return $this;
	}

	/**
	 * @return int
	 */
	public function getDef () {
		return $this->def;
	}

	/**
	 * @param int $def
	 * @return Sender
	 */
	public function setDef ($def) {
		$this->def = $def;
		return $this;
	}


	/**
	 * {@inheritdoc}
	 */
	public function toArray () {
		return [
			'id' => $this->id,
			'user_id' => $this->user_id,
			'sender_name_1' => $this->sender_name_1,
			'sender_name_2' => $this->sender_name_2,
			'sender_street' => $this->sender_street,
			'sender_city' => $this->sender_city,
			'sender_zip' => $this->sender_zip,
			'sender_country' => $this->sender_country,
			'sender_logo' => $this->sender_logo,
			'sender_email' => $this->sender_email,
			'sender_contact' => $this->sender_contact,
			'sender_phone' => $this->sender_phone,
			'message_subject' => $this->message_subject,
			'data' => $this->getData(),
			'state' => $this->state,
			'def' => $this->def,
			'created' => $this->created,
			'created_by' => $this->created_by,
			'modified' => $this->modified,
			'modified_by' => $this->modified_by
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
