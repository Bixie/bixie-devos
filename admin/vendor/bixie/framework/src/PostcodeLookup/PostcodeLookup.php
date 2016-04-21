<?php

namespace Bixie\Framework\PostcodeLookup;


class PostcodeLookup {
	/**
	 * @var string
	 */
	protected $api_url;

	/**
	 * @var string
	 */
	protected $api_name;

	/**
	 * @var string
	 */
	protected $api_secret;

	/**
	 * PostcodeLookup constructor.
	 * @param $api_url
	 * @param $api_name
	 * @param $api_secret
	 */
	public function __construct ($api_url, $api_name, $api_secret) {
		$this->api_url = $api_url;
		$this->api_name = $api_name;
		$this->api_secret = $api_secret;
	}

	public function lookup ($postcode, $huisnr, $toev) {

		if (!$this->api_url || !$this->api_name || !$this->api_secret) {
			throw new \InvalidArgumentException('Instellingen niet compleet.');
		}

		return $this->send($postcode, $huisnr, $toev);
	}

	protected function send ($postcode, $huisnr, $toev) {
		$url = sprintf('%s/rest/addresses/%s/%s/%s', $this->api_url, urlencode($postcode), urlencode($huisnr), urlencode($toev));
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 3);
		curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_ANY);
		curl_setopt($ch, CURLOPT_USERPWD, $this->api_name .':'. $this->api_secret);
		curl_setopt($ch, CURLOPT_USERAGENT, 'BixiePostcodeLookup');
		$result = curl_exec($ch);
		$result = json_decode($result, true);
		if (false === $result || curl_getinfo($ch, CURLINFO_HTTP_CODE) != 200) {
			switch (@$result['exceptionId'])
			{
				case 'PostcodeNl_Controller_Address_InvalidPostcodeException':
					$exception = 'Geen geldige postcode';
					break;
				case 'PostcodeNl_Service_PostcodeAddress_AddressNotFoundException':
					$exception = 'Combinatie van postcode en huisnummer bestaat niet';
					break;
				default:
					$exception = 'Postcode niet gevonden';
					break;
			}

			throw new \Exception($exception, 404);
		}

		curl_close($ch);

		return $result;

	}

}