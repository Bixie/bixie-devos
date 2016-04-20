<?php


namespace Bixie\Gls\Label;


class Labelary {

	const API_URL = 'http://api.labelary.com/v1/printers/%s/labels/%s/0/';
	const DENSITY = '8dpmm';
	const FORMAT = '4x6';

	/**
	 * @param $zpl
	 * @return mixed
	 * @throws \Exception
	 */
	public function getPdf ($zpl) {
		return $this->callApi($zpl, 'application/pdf');
	}

	/**
	 * @param $zpl
	 * @return mixed
	 * @throws \Exception
	 */
	public function getPng ($zpl) {
		return $this->callApi($zpl, 'image/png');
	}

	/**
	 * @param $zpl
	 * @param $type
	 * @return mixed
	 * @throws \Exception
	 */
	protected function callApi ($zpl, $type) {
		$curl = curl_init();
		curl_setopt($curl, CURLOPT_URL, sprintf(self::API_URL, self::DENSITY, self::FORMAT, $zpl));
		curl_setopt($curl, CURLOPT_POST, TRUE);
		curl_setopt($curl, CURLOPT_POSTFIELDS, $zpl);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, TRUE);
		curl_setopt($curl, CURLOPT_HTTPHEADER, array("Accept: $type"));
		$result = curl_exec($curl);

		if (curl_getinfo($curl, CURLINFO_HTTP_CODE) != 200) {
			throw new \Exception($result);
		}

		curl_close($curl);

		return $result;
	}

}