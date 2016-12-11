<?php

namespace Bixie\Devos\Model\Address;

use Bixie\Framework\Traits\HydrateTrait;

class AddressBase  {

	use HydrateTrait;

	public static function create ($data = []) {
		return self::hydrate(array_merge([
			'data' => [
			]
		], $data), 'Bixie\Devos\Model\Sender\Address');
	}


	public function mergeData ($data) {
		return $this->merge($data, 'Bixie\Devos\Model\Sender\Address');
	}

}
