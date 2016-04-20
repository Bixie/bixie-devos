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

	/**
	 * @param        $gls_customer_number
	 * @param        $domestic_parcel_number_nl
	 * @param string $redirto
	 * @return mixed
	 */
	public function getUrl ($gls_customer_number, $domestic_parcel_number_nl, $redirto = 'Verlader') {
		return sprintf(
			'http://services.gls-netherlands.com/tracking/ttlink.aspx?NVRL=%d&NDOC=%s&TAAL=NL&REDIRTO=%s&CHK=%d',
			$gls_customer_number,
			$domestic_parcel_number_nl,
			$redirto,
			$this->getChecksum($gls_customer_number . $domestic_parcel_number_nl . $redirto)
		);
	}

	/**
	 * ->getUrl(75470057, 75470057000034, 'Verl'); //28455
	 * for position in string:
	 *     asc = ord(position)
	 *     if asc >= 65 and asc <= 90:
	 *         asc = asc - 64
	 *     elif asc >= 48 and asc <= 57:
	 *         asc = asc - 21
	 *     chk = chk + (asc * pos)
	 *     pos = pos + 1
	 *
	 * @param $data
	 * @return int|mixed
	 */
	protected function getChecksum ($data) {
		$chk = $this->encryptionCode;
		for ($i = 1; $i <= strlen($data); $i++) {
			// asci waarde bepalen
			$c = substr($data, ($i - 1), 1);
			$nAsc = ord($c);

			if ($nAsc >= 65 && $nAsc <= 90)
				$nAsc = $nAsc - 64;
			elseif ($nAsc >= 48 && $nAsc <= 57)
				$nAsc = $nAsc - 21;

			$chk += ($i * $nAsc);

		}
		return $chk;
	}
}

