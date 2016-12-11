<?php

namespace Bixie\Devos\Model\Address;


use Bixie\Framework\Traits\CreatedModifiedTrait;
use Bixie\Framework\Traits\DataTrait;

class Address extends AddressBase implements \JsonSerializable, \ArrayAccess {

	use CreatedModifiedTrait, DataTrait;

	const ADDRESS_STATE_INACTIVE = 0;
	const ADDRESS_STATE_ACTIVE = 1;
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
	protected $name_1 = '';
	/**
	 * @var string
	 */
	protected $name_2 = '';
	/**
	 * @var string
	 */
	protected $street = '';
    /**
     * @var string
     */
    protected $zip = '';
    /**
     * @var string
     */
    protected $city = '';
    /**
	 * @var string
	 */
	protected $country = '';
	/**
	 * @var string
	 */
	protected $email = '';
	/**
	 * @var string
	 */
	protected $contact = '';
	/**
	 * @var string
	 */
	protected $phone = '';
	/**
	 * @var string
	 */
	protected $additional_text = '';
	/**
	 * @var int
	 */
	protected $state = 1;

	public static function getStates () {
		return [
			self::ADDRESS_STATE_INACTIVE => 'Inactief',
			self::ADDRESS_STATE_ACTIVE => 'Actief'
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
	 * @return Address
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
	 * @return Address
	 */
	public function setUserId ($user_id) {
		$this->user_id = $user_id;
		return $this;
	}

    /**
     * @return string
     */
    public function getName1 () {
        return $this->name_1;
    }

    /**
     * @param string $name_1
     * @return Address
     */
    public function setName1 ($name_1) {
        $this->name_1 = $name_1;
        return $this;
    }

    /**
     * @return string
     */
    public function getName2 () {
        return $this->name_2;
    }

    /**
     * @param string $name_2
     * @return Address
     */
    public function setName2 ($name_2) {
        $this->name_2 = $name_2;
        return $this;
    }

    /**
     * @return string
     */
    public function getStreet () {
        return $this->street;
    }

    /**
     * @param string $street
     * @return Address
     */
    public function setStreet ($street) {
        $this->street = $street;
        return $this;
    }

    /**
     * @return string
     */
    public function getZip () {
        return $this->zip;
    }

    /**
     * @param string $zip
     * @return Address
     */
    public function setZip ($zip) {
        $this->zip = $zip;
        return $this;
    }

    /**
     * @return string
     */
    public function getCity () {
        return $this->city;
    }

    /**
     * @param string $city
     * @return Address
     */
    public function setCity ($city) {
        $this->city = $city;
        return $this;
    }

    /**
     * @return string
     */
    public function getCountry () {
        return $this->country;
    }

    /**
     * @param string $country
     * @return Address
     */
    public function setCountry ($country) {
        $this->country = $country;
        return $this;
    }

    /**
     * @return string
     */
    public function getEmail () {
        return $this->email;
    }

    /**
     * @param string $email
     * @return Address
     */
    public function setEmail ($email) {
        $this->email = $email;
        return $this;
    }

    /**
     * @return string
     */
    public function getContact () {
        return $this->contact;
    }

    /**
     * @param string $contact
     * @return Address
     */
    public function setContact ($contact) {
        $this->contact = $contact;
        return $this;
    }

    /**
     * @return string
     */
    public function getPhone () {
        return $this->phone;
    }

    /**
     * @param string $phone
     * @return Address
     */
    public function setPhone ($phone) {
        $this->phone = $phone;
        return $this;
    }

    /**
     * @return string
     */
    public function getAdditionalText () {
        return $this->additional_text;
    }

    /**
     * @param string $additional_text
     * @return Address
     */
    public function setAdditionalText ($additional_text) {
        $this->additional_text = $additional_text;
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
	 * @return Address
	 */
	public function setState ($state) {
		$this->state = $state;
		return $this;
	}

    /**
	 * {@inheritdoc}
	 */
	public function toArray () {
		return [
			'id' => $this->id,
			'user_id' => $this->user_id,
			'name_1' => $this->name_1,
			'name_2' => $this->name_2,
			'street' => $this->street,
			'city' => $this->city,
			'zip' => $this->zip,
			'country' => $this->country,
			'email' => $this->email,
			'contact' => $this->contact,
			'phone' => $this->phone,
			'additional_text' => $this->additional_text,
			'data' => $this->getData(),
			'state' => $this->state,
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
        $data['phone'] = '"' . $data['phone'] . '"'; //trick json_encode NUMERIC_CHECK
		return $data;
	}
}
