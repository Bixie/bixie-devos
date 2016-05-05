<?php

namespace Bixie\Gls\Data;


use Bixie\Framework\Traits\HydrateTrait;

class Address {

	use HydrateTrait;
	/**
	 * @var string ALTADR, DELADR, PICADR, PTVDELADR, PTVPICADR, PTVREQADR, REQADR, RETADR
	 */
	public $AddressType;

	public $Name1;

	public $Name2;

	public $Name3;

	public $Street1;

	public $Street2;

	public $HouseNo;

	public $HouseNoExt;

	public $ZipCode;

	public $City;

	public $CountryCode;

	public $CountryName;

	public static function create ($data = []) {
		return self::hydrate($data, 'Bixie\Gls\Data\Address');
	}

}