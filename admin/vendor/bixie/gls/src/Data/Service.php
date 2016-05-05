<?php

namespace Bixie\Gls\Data;


use Bixie\Framework\Traits\HydrateTrait;

class Service {

	use HydrateTrait;

	public $Code;
	
	public $Name;
	
	public $Info;

	public static function create ($data = []) {
		return self::hydrate($data, 'Bixie\Gls\Data\Service');
	}

}