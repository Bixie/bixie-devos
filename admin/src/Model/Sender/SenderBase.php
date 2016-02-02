<?php

namespace Bixie\Devos\Model\Sender;

use Bixie\Framework\Traits\HydrateTrait;

class SenderBase  {

	use HydrateTrait;

	public static function create ($data = []) {
		return self::hydrate(array_merge([
			'data' => [
			]
		], $data), 'Bixie\Devos\Model\Sender\Sender');
	}


	public function mergeData ($data) {
		return $this->merge($data, 'Bixie\Devos\Model\Sender\Sender');
	}

}
