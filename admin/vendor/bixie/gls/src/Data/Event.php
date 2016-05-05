<?php

namespace Bixie\Gls\Data;

use Bixie\Framework\Traits\HydrateTrait;

class Event {

	use HydrateTrait;

	public $Source;

	public $InsertTimeStamp;

	public $EventTimeStamp;

	public $CustomerID;

	public $ParcelNumber;

	public $UniQueNo;

	public $GPNumber;

	public $LocationCode;

	public $LocationName;

	public $CountryCode;

	public $CountryName;

	/**
	 * @var EventReason
	 */
	public $EventReason;

	/**
	 * @param array $data
	 * @return Parcel
	 */
	public static function create ($data = []) {
		return self::hydrate($data, 'Bixie\Gls\Data\Event');
	}



}