<?php

namespace Bixie\Gls\Data;


use Bixie\Framework\Traits\HydrateTrait;

class EventReason {

	use HydrateTrait;

	public $Description;
	
	public $EventCode;
	
	public $EventNumber;
	
	public $ReasonCode;
	
	public $ReasonNumber;

	public static function create ($data = []) {
		return self::hydrate($data, 'Bixie\Gls\Data\EventReason');
	}

}