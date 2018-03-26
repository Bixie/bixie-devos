<?php

namespace Bixie\Devos\Model\Shipment;


use Bixie\Framework\Traits\CreatedModifiedTrait;
use Bixie\Framework\Traits\DataTrait;
use Bixie\Framework\Utils\Arr;
use Bixie\Gls\Data\Event;

class ShipmentSendCloud extends ShipmentSendCloudBase implements ShipmentInterface, \JsonSerializable, \ArrayAccess {

	use CreatedModifiedTrait, DataTrait;

	const SHIPMENTSENDCLOUD_STATE_REMOVED = 0;
	const SHIPMENTSENDCLOUD_STATE_CREATED = 1;
	const SHIPMENTSENDCLOUD_STATE_SCANNED = 2;

	/**
	 * @var integer
	 */
	protected $id = 0;
	/**
	 * @var integer
	 */
	protected $sender_id = 0;
	/**
	 * @var string
	 */
	protected $klantnummer = '';
    /**
     * @var string
     */
    protected $order_number = '';
    /**
     * @var integer
     */
    protected $sendcloud_id = 0;
    /**
	 * @var float
	 */
	protected $weight = 0;
    /**
     * @var string
     */
    protected $name = '';
    /**
     * @var string
     */
    protected $company_name = '';
    /**
     * @var string
     */
    protected $address = '';
    /**
     * @var string
     */
    protected $city = '';
    /**
     * @var string
     */
    protected $postal_code = '';
    /**
     * @var string
     */
    protected $telephone = '';
    /**
     * @var string
     */
    protected $email = '';
    /**
     * @var array
     */
    protected $country = '';
    /**
     * @var integer
     */
    protected $shipment = 0;
    /**
     * @var bool
     */
    protected $requestShipment = 1;
    /**
     * @var string
     */
    protected $tracking_number = '';
	/**
	 * @var int
	 */
	protected $state = 1;
	/**
	 * @var string
	 */
	protected $pdf_path = '';
	/**
	 * @var string
	 */
	protected $zpl_template = '';
	/**
	 * @var array
	 */
	protected $parcel;
	/**
	 * @var array
	 */
	protected $events;

	/**
	 * @return int
	 */
	public function getId () {
		return $this->id;
	}

	/**
	 * @param int $id
	 * @return ShipmentSendCloud
	 */
	public function setId ($id) {
		$this->id = $id;
		return $this;
	}

	/**
	 * @return int
	 */
	public function getSenderId () {
		return $this->sender_id;
	}

	/**
	 * @param int $sender_id
	 * @return ShipmentSendCloud
	 */
	public function setSenderId ($sender_id) {
		$this->sender_id = $sender_id;
		return $this;
	}

	/**
	 * @return string
	 */
	public function getKlantnummer () {
		return $this->klantnummer;
	}

	/**
	 * @param string $klantnummer
	 * @return ShipmentSendCloud
	 */
	public function setKlantnummer ($klantnummer) {
		$this->klantnummer = $klantnummer;
		return $this;
	}

    /**
     * @return string
     */
    public function getOrderNumber () {
        return $this->order_number;
    }

    /**
     * @param string $order_number
     * @return ShipmentSendCloud
     */
    public function setOrderNumber ($order_number) {
        $this->order_number = $order_number;
        return $this;
    }

    /**
     * @return int
     */
    public function getSendcloudId () {
        return $this->sendcloud_id;
    }

    /**
     * @param int $sendcloud_id
     * @return ShipmentSendCloud
     */
    public function setSendcloudId ($sendcloud_id) {
        $this->sendcloud_id = $sendcloud_id;
        return $this;
    }

	/**
	 * @return float
	 */
	public function getWeight () {
		return $this->weight;
	}

	/**
	 * @param float $weight
	 * @return ShipmentSendCloud
	 */
	public function setWeight ($weight) {
		$this->weight = $weight;
		return $this;
	}

    /**
     * @return string
     */
    public function getName () {
        return $this->name;
    }

    /**
     * @param string $name
     * @return ShipmentSendCloud
     */
    public function setName ($name) {
        $this->name = $name;
        return $this;
    }

    /**
     * @return string
     */
    public function getCompanyName () {
        return $this->company_name;
    }

    /**
     * @param string $company_name
     * @return ShipmentSendCloud
     */
    public function setCompanyName ($company_name) {
        $this->company_name = $company_name;
        return $this;
    }

    /**
     * @return string
     */
    public function getAddress () {
        return $this->address;
    }

    /**
     * @param string $address
     * @return ShipmentSendCloud
     */
    public function setAddress ($address) {
        $this->address = $address;
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
     * @return ShipmentSendCloud
     */
    public function setCity ($city) {
        $this->city = $city;
        return $this;
    }

    /**
     * @return string
     */
    public function getPostalCode () {
        return $this->postal_code;
    }

    /**
     * @param string $postal_code
     * @return ShipmentSendCloud
     */
    public function setPostalCode ($postal_code) {
        $this->postal_code = $postal_code;
        return $this;
    }

    /**
     * @return string
     */
    public function getTelephone () {
        return $this->telephone;
    }

    /**
     * @param string $telephone
     * @return ShipmentSendCloud
     */
    public function setTelephone ($telephone) {
        $this->telephone = $telephone;
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
     * @return ShipmentSendCloud
     */
    public function setEmail ($email) {
        $this->email = $email;
        return $this;
    }

    /**
     * @return array
     */
    public function getCountry () {
        return $this->country;
    }

    /**
     * @param array $country
     * @return ShipmentSendCloud
     */
    public function setCountry ($country) {
        $this->country = $country;
        return $this;
    }

    /**
     * @return int
     */
    public function getShipment () {
        return $this->shipment;
    }

    /**
     * @param int $shipment
     * @return ShipmentSendCloud
     */
    public function setShipment ($shipment) {
        $this->shipment = $shipment;
        return $this;
    }

    /**
     * @return bool
     */
    public function getRequestShipment () {
        return $this->requestShipment;
    }

    /**
     * @param bool $requestShipment
     * @return ShipmentSendCloud
     */
    public function setRequestShipment ($requestShipment) {
        $this->requestShipment = $requestShipment;
        return $this;
    }

    /**
     * @return string
     */
    public function getTrackingNumber () {
        return $this->tracking_number;
    }

    /**
     * @param string $tracking_number
     * @return ShipmentSendCloud
     */
    public function setTrackingNumber ($tracking_number) {
        $this->tracking_number = $tracking_number;
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
	 * @return ShipmentSendCloud
	 */
	public function setState ($state) {
		$this->state = $state;
		return $this;
	}

	/**
	 * @return string
	 */
	public function getPdfPath () {
		return $this->pdf_path;
	}

	/**
	 * @param string $pdf_path
	 * @return ShipmentSendCloud
	 */
	public function setPdfPath ($pdf_path) {
		$this->pdf_path = $pdf_path;
		return $this;
	}

	/**
	 * @return string
	 */
	public function getZplTemplate () {
		return $this->zpl_template;
	}

	/**
	 * @param string $zpl_template
	 * @return ShipmentSendCloud
	 */
	public function setZplTemplate ($zpl_template) {
		$this->zpl_template = $zpl_template;
		return $this;
	}

	/**
	 * @return array
	 */
	public function getParcel () {
		if (!isset($this->parcel) || is_string($this->parcel)) {
			$this->parcel = json_decode($this->parcel, true) ?: [];
		}
		return $this->parcel;
	}

	/**
	 * @param array $parcel
	 */
	public function setParcel ($parcel) {
		$this->parcel = $parcel;
	}

	/**
	 * @return array
	 */
	public function getEvents () {
		if (!isset($this->events) || is_string($this->events)) {
			$this->events = json_decode($this->events, true) ?: [];
		}
		return $this->events;
	}

	/**
	 * @param Event $event
	 */
	public function addEvent (Event $event) {
		$this->getEvents();
		$this->offsetSet('gls_status', $event->EventReason->Description);
		$this->events[$event->EventTimeStamp] = $event;
	}

	/**
	 * @param array $events
	 */
	public function setEvents ($events) {
		$this->events = $events;
	}

    /**
	 * @return string
	 */
	public function getPdfUrl () {
		if (!empty($this->pdf_path)) {
			$u = \JRoute::_('index.php?option=com_bix_devos&p=/api/sendcloud/pdf/' . $this->tracking_number, false);
			return \JUri::root() . ltrim($u, '/');
		}
		return '';
	}

    /**
	 * @return string
	 */
	public function getPngUrl () {
		if (!empty($this->data['png_path'])) {
			return \JUri::root() . str_replace(JPATH_ROOT.'/', '', $this->data['png_path']);
		}
		return '';
	}

    /**
	 * @param $basePath
	 * @param $pdfString
	 */
	public function savePdfString ($basePath, $pdfString) {
		if (empty($pdfString) || empty($this->tracking_number)) {
			throw new \InvalidArgumentException(sprintf('Geen tracking_number of pdf-string leeg'));
		}
		$this->pdf_path = $this->filePath($basePath) . '/' . $this->tracking_number . '.pdf';
		if (!@file_put_contents($this->pdf_path, $pdfString)) {
			throw new \RuntimeException(sprintf('Fout bij opslaan %s.pdf', $this->tracking_number));
		}
	}

    /**
	 * @param $basePath
	 * @param $imageString
	 */
	public function saveImageString ($basePath, $imageString) {
		if (empty($imageString) || empty($this->domestic_parcel_number_nl)) {
			throw new \InvalidArgumentException(sprintf('Geen parcel_number of png-string leeg'));
		}
		$png_path = $this->filePath($basePath) . '/' . $this->tracking_number . '.png';
		if (!@file_put_contents($png_path, $imageString)) {
			throw new \RuntimeException(sprintf('Fout bij opslaan %s.pdf', $this->tracking_number));
		}
		$this->offsetSet('png_path', $png_path);
	}

    /**
	 * @param $base
	 * @return string
	 */
	public function filePath ($base) {
		$filePath = $base . '/s-' . floor($this->id / 100) . '00';
		if (!is_dir($filePath)) {
			mkdir($filePath, 0755, true);
		}
		return $filePath;
	}

    /**
     * @return mixed
     */
    public function getStatusName () {
		return self::getStatuses()[$this->state];
	}
    /**
	 * @return array
	 */
	public static function getStatuses () {
		return [
			self::SHIPMENTSENDCLOUD_STATE_REMOVED => 'Verwijderd',
			self::SHIPMENTSENDCLOUD_STATE_CREATED => 'Aangemaakt',
			self::SHIPMENTSENDCLOUD_STATE_SCANNED => 'Gescand',
		];
	}

    /**
	 * {@inheritdoc}
	 */
	public function toArray ($data = [], $ignore = []) {
		$data = array_merge([
			'id' => $this->id,
			'sender_id' => $this->sender_id,
			'klantnummer' => $this->klantnummer,
			'order_number' => $this->order_number,
			'sendcloud_id' => $this->sendcloud_id,
			'weight' => $this->weight,
			'name' => $this->name,
			'company_name' => $this->company_name,
			'address' => $this->address,
			'city' => $this->city,
			'postal_code' => $this->postal_code,
			'telephone' => $this->telephone,
			'email' => $this->email,
            'country' => $this->getCountry(),
            'shipping_method' => $this->shipment,
            'requestShipment' => $this->requestShipment,
            'data' => $this->getData(),
            'parcel' => $this->getParcel(),
            'events' => $this->getEvents(),
            'state' => $this->state,
            'statusname' => $this->getStatusName(),
            'tracking_number' => $this->tracking_number,
            'pdf_path' => $this->pdf_path,
            'created' => $this->getCreated()->format(DATE_ATOM),
            'created_by' => $this->created_by,
            'modified' => $this->getModified() ? $this->getModified()->format(DATE_ATOM) : '',
			'modified_by' => $this->modified_by
		], $data);
		return array_diff_key($data, array_flip($ignore));
	}

    /**
     * proxy
     * @return string
     */
    public function getCustomerReference () {
        return $this->getOrderNumber();
	}

    /**
	 * Checks if a key exists.
	 * @param  string $key
	 * @return bool
	 */
	public function offsetExists ($key) {
		return (property_exists($this, $key) || Arr::has($this->getData(), $key));
	}

    /**
	 * Gets a value by key.
	 * @param  string $key
	 * @return mixed
	 */
	public function offsetGet ($key) {
		return property_exists($this, $key) ? $this->$key : Arr::get($this->getData(), $key);
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
		unset($data['gls_stream']);
		$data['pdf_url'] = $this->getPdfUrl();
		$data['png_url'] = $this->getPngUrl();
		return $data;
	}
}
