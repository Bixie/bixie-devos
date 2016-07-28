<?php

namespace Bixie\Devos\Model\Shipment;


use Bixie\Framework\Traits\CreatedModifiedTrait;
use Bixie\Framework\Traits\DataTrait;
use Bixie\Framework\Utils\Arr;
use Bixie\Gls\Data\Event;

class ShipmentGls extends ShipmentGlsBase implements \JsonSerializable, \ArrayAccess {

	use CreatedModifiedTrait, DataTrait;

	const PRODUCT_BUSINESS_PARCEL = 'BP';
	const PRODUCT_EXPRESS_PARCEL = 'EP';
	const PRODUCT_EURO_BUSINESS_PARCEL = 'EBP';
	
	const SHIPMENTGLS_STATE_REMOVED = 0;
	const SHIPMENTGLS_STATE_CREATED = 1;
	const SHIPMENTGLS_STATE_SCANNED = 2;

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
	protected $gls_customer_number = '';
	/**
	 * @var integer
	 */
	protected $parcel_number = 0;
	/**
	 * @var string
	 */
	protected $product_short_description = 'BP';
	/**
	 * @var string
	 */
	protected $receiver_zip_code = '';
	/**
	 * @var string
	 */
	protected $gls_parcel_number = 0;
	/**
	 * @var float
	 */
	protected $parcel_weight = 0;
	/**
	 * @var \DateTime
	 */
	protected $date_of_shipping = '';
	/**
	 * @var string
	 */
	protected $domestic_parcel_number_nl = '';
	/**
	 * @var string
	 */
	protected $receiver_phone = '';
	/**
	 * @var string
	 */
	protected $receiver_email = '';
	/**
	 * @var string
	 */
	protected $receiver_contact = '';
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
	protected $sender_email = '';
	/**
	 * @var string
	 */
	protected $customer_reference = '';
	/**
	 * @var string
	 */
	protected $receiver_name_1 = '';
	/**
	 * @var string
	 */
	protected $receiver_name_2 = '';
	/**
	 * @var string
	 */
	protected $receiver_name_3 = '';
	/**
	 * @var string
	 */
	protected $receiver_street = '';
	/**
	 * @var string
	 */
	protected $receiver_place = '';
	/**
	 * @var string
	 */
	protected $additional_text_1 = '';
	/**
	 * @var string
	 */
	protected $additional_text_2 = '';
	/**
	 * @var int
	 */
	protected $parcel_sequence = 1;
	/**
	 * @var int
	 */
	protected $parcel_quantity = 1;
	/**
	 * @var string
	 */
	protected $sap_number = '';
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
	 * @var string
	 */
	protected $gls_stream = '';
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
	 * @return ShipmentGls
	 */
	public function setId ($id) {
		$this->id = $id;
		return $this;
	}

	/**
	 * @return int
	 */
	public function getParcelNumber () {
		return $this->parcel_number;
	}

	/**
	 * @param int $parcel_number
	 * @return ShipmentGls
	 */
	public function setParcelNumber ($parcel_number) {
		$this->parcel_number = $parcel_number;
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
	 * @return ShipmentGls
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
	 * @return ShipmentGls
	 */
	public function setKlantnummer ($klantnummer) {
		$this->klantnummer = $klantnummer;
		return $this;
	}

	/**
	 * @return string
	 */
	public function getGlsCustomerNumber () {
		return $this->gls_customer_number;
	}

	/**
	 * @param string $gls_customer_number
	 * @return ShipmentGls
	 */
	public function setGlsCustomerNumber ($gls_customer_number) {
		$this->gls_customer_number = $gls_customer_number;
		return $this;
	}

	/**
	 * @return string
	 */
	public function getProductShortDescription () {
		return $this->product_short_description;
	}

	/**
	 * @param string $product_short_description
	 * @return ShipmentGls
	 */
	public function setProductShortDescription ($product_short_description) {
		$this->product_short_description = $product_short_description;
		return $this;
	}

	/**
	 * @return string
	 */
	public function getReceiverZipCode () {
		return $this->receiver_zip_code;
	}

	/**
	 * @param string $receiver_zip_code
	 * @return ShipmentGls
	 */
	public function setReceiverZipCode ($receiver_zip_code) {
		$this->receiver_zip_code = $receiver_zip_code;
		return $this;
	}

	/**
	 * @return int
	 */
	public function getGlsParcelNumber () {
		return $this->gls_parcel_number;
	}

	/**
	 * @param int $gls_parcel_number
	 * @return ShipmentGls
	 */
	public function setGlsParcelNumber ($gls_parcel_number) {
		$this->gls_parcel_number = $gls_parcel_number;
		return $this;
	}

	/**
	 * @return float
	 */
	public function getParcelWeight () {
		return $this->parcel_weight;
	}

	/**
	 * @param float $parcel_weight
	 * @return ShipmentGls
	 */
	public function setParcelWeight ($parcel_weight) {
		$this->parcel_weight = $parcel_weight;
		return $this;
	}

	/**
	 * @return \Datetime
	 */
	public function getDateOfShipping () {
		if (is_string($this->date_of_shipping)) {
			$this->date_of_shipping = new \DateTime($this->date_of_shipping);
		}
		return $this->date_of_shipping;
	}

	/**
	 * @param string $date_of_shipping
	 * @return ShipmentGls
	 */
	public function setDateOfShipping ($date_of_shipping) {
		$this->date_of_shipping = $date_of_shipping;
		return $this;
	}

	/**
	 * @return string
	 */
	public function getDomesticParcelNumberNl () {
		return $this->domestic_parcel_number_nl;
	}

	/**
	 * @param string $domestic_parcel_number_nl
	 * @return ShipmentGls
	 */
	public function setDomesticParcelNumberNl ($domestic_parcel_number_nl) {
		$this->domestic_parcel_number_nl = $domestic_parcel_number_nl;
		return $this;
	}

	/**
	 * @return string
	 */
	public function getReceiverPhone () {
		return $this->receiver_phone;
	}

	/**
	 * @param string $receiver_phone
	 * @return ShipmentGls
	 */
	public function setReceiverPhone ($receiver_phone) {
		$this->receiver_phone = $receiver_phone;
		return $this;
	}

	/**
	 * @return string
	 */
	public function getRecieverEmail () {
		return $this->receiver_email;
	}

	/**
	 * @param string $receiver_email
	 * @return ShipmentGls
	 */
	public function setRecieverEmail ($receiver_email) {
		$this->receiver_email = $receiver_email;
		return $this;
	}

	/**
	 * @return string
	 */
	public function getReceiverContact () {
		return $this->receiver_contact;
	}

	/**
	 * @param string $receiver_contact
	 * @return ShipmentGls
	 */
	public function setReceiverContact ($receiver_contact) {
		$this->receiver_contact = $receiver_contact;
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
	 * @return ShipmentGls
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
	 * @return ShipmentGls
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
	 * @return ShipmentGls
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
	 * @return ShipmentGls
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
	 * @return ShipmentGls
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
	 * @return ShipmentGls
	 */
	public function setSenderCity ($sender_city) {
		$this->sender_city = $sender_city;
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
	 * @return ShipmentGls
	 */
	public function setSenderEmail ($sender_email) {
		$this->sender_email = $sender_email;
		return $this;
	}

	/**
	 * @return string
	 */
	public function getCustomerReference () {
		return $this->customer_reference;
	}

	/**
	 * @param string $customer_reference
	 * @return ShipmentGls
	 */
	public function setCustomerReference ($customer_reference) {
		$this->customer_reference = $customer_reference;
		return $this;
	}

	/**
	 * @return string
	 */
	public function getReceiverName1 () {
		return $this->receiver_name_1;
	}

	/**
	 * @param string $receiver_name_1
	 * @return ShipmentGls
	 */
	public function setReceiverName1 ($receiver_name_1) {
		$this->receiver_name_1 = $receiver_name_1;
		return $this;
	}

	/**
	 * @return string
	 */
	public function getReceiverName2 () {
		return $this->receiver_name_2;
	}

	/**
	 * @param string $receiver_name_2
	 * @return ShipmentGls
	 */
	public function setReceiverName2 ($receiver_name_2) {
		$this->receiver_name_2 = $receiver_name_2;
		return $this;
	}

	/**
	 * @return string
	 */
	public function getReceiverName3 () {
		return $this->receiver_name_3;
	}

	/**
	 * @param string $receiver_name_3
	 * @return ShipmentGls
	 */
	public function setReceiverName3 ($receiver_name_3) {
		$this->receiver_name_3 = $receiver_name_3;
		return $this;
	}

	/**
	 * @return string
	 */
	public function getReceiverStreet () {
		return $this->receiver_street;
	}

	/**
	 * @param string $receiver_street
	 * @return ShipmentGls
	 */
	public function setReceiverStreet ($receiver_street) {
		$this->receiver_street = $receiver_street;
		return $this;
	}

	/**
	 * @return string
	 */
	public function getReceiverPlace () {
		return $this->receiver_place;
	}

	/**
	 * @param string $receiver_place
	 * @return ShipmentGls
	 */
	public function setReceiverPlace ($receiver_place) {
		$this->receiver_place = $receiver_place;
		return $this;
	}

	/**
	 * @return string
	 */
	public function getAdditionalText1 () {
		return $this->additional_text_1;
	}

	/**
	 * @param string $additional_text_1
	 * @return ShipmentGls
	 */
	public function setAdditionalText1 ($additional_text_1) {
		$this->additional_text_1 = $additional_text_1;
		return $this;
	}

	/**
	 * @return string
	 */
	public function getAdditionalText2 () {
		return $this->additional_text_2;
	}

	/**
	 * @param string $additional_text_2
	 * @return ShipmentGls
	 */
	public function setAdditionalText2 ($additional_text_2) {
		$this->additional_text_2 = $additional_text_2;
		return $this;
	}

	/**
	 * @return int
	 */
	public function getParcelSequence () {
		return $this->parcel_sequence;
	}

	/**
	 * @param string $parcel_sequence
	 * @return ShipmentGls
	 */
	public function setParcelSequence ($parcel_sequence) {
		$this->parcel_sequence = $parcel_sequence;
		return $this;
	}

	/**
	 * @return int
	 */
	public function getParcelQuantity () {
		return $this->parcel_quantity;
	}

	/**
	 * @param string $parcel_quantity
	 * @return ShipmentGls
	 */
	public function setParcelQuantity ($parcel_quantity) {
		$this->parcel_quantity = $parcel_quantity;
		return $this;
	}

	/**
	 * @return string
	 */
	public function getSapNumber () {
		return $this->sap_number;
	}

	/**
	 * @param string $sap_number
	 * @return ShipmentGls
	 */
	public function setSapNumber ($sap_number) {
		$this->sap_number = $sap_number;
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
	 * @return ShipmentGls
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
	 * @return ShipmentGls
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
	 * @return ShipmentGls
	 */
	public function setZplTemplate ($zpl_template) {
		$this->zpl_template = $zpl_template;
		return $this;
	}

	/**
	 * @return string
	 */
	public function getGlsStream () {
		return $this->gls_stream;
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
	 * @param string $gls_stream
	 * @return ShipmentGls
	 */
	public function setGlsStream ($gls_stream) {
		$this->gls_stream = $gls_stream;
		return $this;
	}

	/**
	 * @return string
	 */
	public function getPdfUrl () {
		if (!empty($this->pdf_path)) {
			$u = \JRoute::_('index.php?option=com_bix_devos&p=/api/shipment/pdf/' . $this->domestic_parcel_number_nl, false);
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
		if (empty($pdfString) || empty($this->domestic_parcel_number_nl)) {
			throw new \InvalidArgumentException(sprintf('Geen parcel_number of pdf-string leeg'));
		}
		$this->pdf_path = $this->filePath($basePath) . '/' . $this->domestic_parcel_number_nl . '.pdf';
		if (!@file_put_contents($this->pdf_path, $pdfString)) {
			throw new \RuntimeException(sprintf('Fout bij opslaan %s.pdf', $this->parcel_number));
		}
	}

	/**
	 * @param $basePath
	 * @param $pngString
	 */
	public function savePngString ($basePath, $pngString) {
		if (empty($pngString) || empty($this->domestic_parcel_number_nl)) {
			throw new \InvalidArgumentException(sprintf('Geen parcel_number of pdf-string leeg'));
		}
		$png_path = $this->filePath($basePath) . '/' . $this->domestic_parcel_number_nl . '.png';
		if (!@file_put_contents($png_path, $pngString)) {
			throw new \RuntimeException(sprintf('Fout bij opslaan %s.pdf', $this->parcel_number));
		}
		$this->offsetSet('png_path', $png_path);
	}

	/**
	 * @param $base
	 * @return string
	 */
	protected function filePath ($base) {
		$filePath = $base . '/s-' . floor($this->id / 100) . '00';
		if (!is_dir($filePath)) {
			mkdir($filePath, 0755, true);
		}
		return $filePath;
	}

	public function getStatusName () {
		return self::getStatuses()[$this->state];
	}
	/**
	 * @return array
	 */
	public static function getStatuses () {
		return [
			self::SHIPMENTGLS_STATE_REMOVED => 'Verwijderd',
			self::SHIPMENTGLS_STATE_CREATED => 'Aangemaakt',
			self::SHIPMENTGLS_STATE_SCANNED => 'Gescand',
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
			'gls_customer_number' => $this->gls_customer_number,
			'parcel_number' => $this->parcel_number,
			'product_short_description' => $this->product_short_description,
			'receiver_zip_code' => $this->receiver_zip_code,
			'gls_parcel_number' => $this->gls_parcel_number,
			'parcel_weight' => $this->parcel_weight,
			'date_of_shipping' => $this->getDateOfShipping()->format('Y-m-d H:i:s'),
			'domestic_parcel_number_nl' => $this->domestic_parcel_number_nl,
			'receiver_phone' => $this->receiver_phone,
			'receiver_email' => $this->receiver_email,
			'receiver_contact' => $this->receiver_contact,
			'sender_name_1' => $this->sender_name_1,
			'sender_name_2' => $this->sender_name_2,
			'sender_street' => $this->sender_street,
			'sender_city' => $this->sender_city,
			'sender_zip' => $this->sender_zip,
			'sender_country' => $this->sender_country,
			'sender_email' => $this->sender_email,
			'customer_reference' => $this->customer_reference,
			'receiver_name_1' => $this->receiver_name_1,
			'receiver_name_2' => $this->receiver_name_2,
			'receiver_name_3' => $this->receiver_name_3,
			'receiver_street' => $this->receiver_street,
			'receiver_place' => $this->receiver_place,
			'additional_text_1' => $this->additional_text_1,
			'additional_text_2' => $this->additional_text_2,
			'parcel_sequence' => $this->parcel_sequence,
			'parcel_quantity' => $this->parcel_quantity,
			'sap_number' => $this->sap_number,
			'data' => $this->getData(),
			'parcel' => $this->getParcel(),
			'events' => $this->getEvents(),
			'state' => $this->state,
			'statusname' => $this->getStatusName(),
			'gls_stream' => $this->gls_stream,
			'pdf_path' => $this->pdf_path,
			'zpl_template' => $this->zpl_template,
			'created' => $this->created,
			'created_by' => $this->created_by,
			'modified' => $this->modified,
			'modified_by' => $this->modified_by
		], $data);
		return array_diff_key($data, array_flip($ignore));
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
