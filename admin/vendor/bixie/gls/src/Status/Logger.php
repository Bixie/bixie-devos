<?php

namespace Bixie\Gls\Status;


class Logger implements \JsonSerializable{

	protected  $errors = [];

	protected  $warnings = [];

	protected  $messages = [];

	protected  $counter = [];

	public function error ($message) {
		$this->errors[] = $message;
	}
	
	public function warning ($message) {
		$this->warnings[] = $message;
	}
	
	public function log ($message) {
		$this->messages[] = $message;
	}

	public function count ($counter) {
		if (!isset($this->counter[$counter])) {
			$this->counter[$counter] = 0;
		}
		return $this->counter[$counter]++;
	}


	/**
	 * Return data which should be serialized by json_encode().
	 * @return  mixed
	 * @since   1.0
	 */
	public function jsonSerialize () {
		return [
			'errors' => $this->errors,
			'warnings' => $this->warnings,
			'messages' => $this->messages,
			'counter' => $this->counter,
		];
	}
}