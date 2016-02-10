<?php

namespace Bixie\Gls;


class GlsTracking {

	protected $encryptionCode;

	/**
	 * GlsTracking constructor.
	 * @param array $config
	 */
	public function __construct (array $config) {

		$this->encryptionCode = $config['encryptionCode'];

	}

	public function getUrl ($gls_customer_number, $gls_parcel_number) {

		return sprintf(
			'http://services.gls-netherlands.com/tracking/ttlink.aspx?NVRL=%d&NDOC=%s&TAAL=NL&ADDRESSTYPE=B&CHK=%d',
			$gls_customer_number,
			$gls_parcel_number,
			$this->getChecksum($gls_customer_number . $gls_parcel_number)
		);
	}

	protected function getChecksum ($data) {
		$nChk = '';
		for ($i = 0; $i < strlen($data); $i++) {
			// asci waarde bepalen
			$nAsc = ord(substr($data, $i, 1));

			if ($nAsc >= 65 && $nAsc <= 90)
				$nAsc = $nAsc - 64;
			elseif ($nAsc >= 48 && $nAsc <= 57)
				$nAsc = $nAsc - 21;
			$nChk = $nChk + (($i + 1) * $nAsc);

		}
		return $nChk + $this->encryptionCode;
	}
}