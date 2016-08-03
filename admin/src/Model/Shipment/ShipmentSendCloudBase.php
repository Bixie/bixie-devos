<?php

namespace Bixie\Devos\Model\Shipment;

use Bixie\Framework\Traits\HydrateTrait;

class ShipmentSendCloudBase  {

	use HydrateTrait;

	public static function create ($data = []) {
		return self::hydrate(array_merge([
			'data' => [
			    'track_trace' => '',
			    'sender_name' => '',
			    'sender_phone' => ''
			]
		], $data), 'Bixie\Devos\Model\Shipment\ShipmentSendCloud');
	}


	public function mergeData ($data) {
		return $this->merge($data, 'Bixie\Devos\Model\Shipment\ShipmentSendCloud');
	}

}
