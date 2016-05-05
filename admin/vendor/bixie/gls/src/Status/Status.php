<?php

namespace Bixie\Gls\Status;

use Bixie\Devos\Application;
use Bixie\Gls\Data\Address;
use Bixie\Gls\Data\Event;
use Bixie\Gls\Data\EventReason;
use Bixie\Gls\Data\Parcel;
use Bixie\Gls\Data\Service;
use Bixie\Gls\GlsException;
use Bixie\Gls\Status\Ftp\FtpGls;
use JMS\Serializer\Annotation\XmlElement;

class Status {

	public static function getStatusUpdates () {

		$statuses = [];
		try {

			$app = Application::getInstance();

			/** @var FtpGls $ftp */
			$ftp = $app['gls.ftp']();

			if ($filelist = $ftp->getList('/out')) {

				foreach ($filelist as $file) {

					if (preg_match('/ctt_(?P<gls>\d{8})_(?P<from>\d{17})_(?P<to>\d{17})\.xml$/', $file->getRealpath(), $info)) {
						$status = [
							'gls_number' => $info['gls'],
							'date_from' => \DateTime::createFromFormat( 'YmdHis000', $info['from']),
							'date_to' => \DateTime::createFromFormat( 'YmdHis000', $info['to']),
							'parcels' => [],
							'events' => []
						];

						if ($contents = $ftp->getFileContents($file->getRealpath())) {

							$xml = new \SimpleXMLElement($contents);
							//parcels
							foreach ($xml->xpath('Parcels/Parcel') as $parcel) {

								$Parcel = Parcel::create(array_merge(
									self::getAttributes($parcel),
									self::getAttributes($parcel->xpath('Identification')),
									self::getAttributes($parcel->xpath('Product'), 'Product'),
									self::getAttributes($parcel->xpath('PhysicalInformation'))
								));

								foreach ($parcel->xpath('Addresses/Address') as $address) {
									$Parcel->Addresses[] = Address::create(array_merge(self::getAttributes($address), [
										'CountryCode' => (string) $address->Country->attributes()->Code,
										'CountryName' => (string) $address->Country->attributes()->Name
									]));
								}

								foreach ($parcel->xpath('Services/Service') as $service) {
									$Parcel->Services[] = Service::create(self::getAttributes($service));
								}


								$status['parcels'][] = $Parcel;
							}
							//events
							foreach ($xml->xpath('Events/Event') as $event) {

								$Event = Event::create(array_merge(
									self::getAttributes($event),
									self::getAttributes($event->xpath('Identification')),
									self::getAttributes($event->xpath('Location'), 'Location'),
									self::getAttributes($event->xpath('Location/Country'), 'Country')
								));

								foreach ($event->xpath('EventReason') as $eventreason) {
									$Event->EventReason = EventReason::create(array_merge(
										self::getAttributes($eventreason),
										self::getAttributes($eventreason->xpath('Event'), 'Event'),
										self::getAttributes($eventreason->xpath('Reason'), 'Reason')
									));
								}

								$status['events'][] = $Event;
							}

						}

						$statuses[] = $status;
					}

				}

			}

		} catch (GlsException $e) {
			echo $e->getMessage();
		}

		return $statuses;

	}

	/**
	 * @param \SimpleXMLElement|array $xml
	 * @param string                  $prefix
	 * @return array
	 */
	protected static function getAttributes ($xml, $prefix = '') {
		if (is_array($xml) && isset($xml[0])) { //pick first node
			$xml = $xml[0];
		}
		if ($xml) {
			$attrs = (array)$xml->attributes();
			if ($prefix) {
				$ret = [];
				foreach ($attrs['@attributes'] as $key => $value) {
					$ret[$prefix . $key] = $value;
				}
				return $ret;
			}
			return isset($attrs['@attributes']) ? $attrs['@attributes'] : [];
		}
		return [];
	}
}