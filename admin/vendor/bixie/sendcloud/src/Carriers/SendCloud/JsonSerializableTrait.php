<?php

namespace Bixie\SendCloud\Carriers\SendCloud;


trait JsonSerializableTrait {

	/**
	 * @param array $data
	 * @param array $ignore
	 * @return array
	 */
	public function toArray ($data = [], $ignore = []) {
		return array_diff_key(array_merge($this->attributes, $data), array_flip($ignore));
	}

	/**
	 * @return array
	 */
	function jsonSerialize () {
		return $this->toArray();
	}

}