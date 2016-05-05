<?php

namespace Bixie\Gls\Data;

use Bixie\Framework\Traits\HydrateTrait;

class Parcel {

	use HydrateTrait;

	public $InsertTimeStamp;

	public $CustomerID;

	public $ParcelNumber;

	public $UniQueNo;

	public $Reference;

	public $ProductCode;

	public $ProductName;

	public $Weight;
	
	/**
	 * @var Address[]
	 */
	public $Addresses;
	/**
	 * @var Service[]
	 */
	public $Services;

	/**
	 * @param array $data
	 * @return Parcel
	 */
	public static function create ($data = []) {
		return self::hydrate($data, 'Bixie\Gls\Data\Parcel');
	}



}