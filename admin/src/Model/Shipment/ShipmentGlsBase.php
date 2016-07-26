<?php

namespace Bixie\Devos\Model\Shipment;

use Bixie\Framework\Traits\HydrateTrait;

class ShipmentGlsBase  {

	use HydrateTrait;

	public static function create ($data = []) {
		return self::hydrate(array_merge([
			'data' => [
				'label_template' => 'gls_default',
				'express_service_flag' => '',
				'inbound_country_code' => 'NL'
			]
		], $data), 'Bixie\Devos\Model\Shipment\ShipmentGls');
	}


	public function mergeData ($data) {
		return $this->merge($data, 'Bixie\Devos\Model\Shipment\ShipmentGls');
	}

}
