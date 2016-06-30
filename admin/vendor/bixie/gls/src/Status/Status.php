<?php

namespace Bixie\Gls\Status;

use Bixie\Devos\Model\GlsTracking\GlsTracking;
use Bixie\Devos\Model\Shipment\ShipmentGls;
use Bixie\Gls\Data\Address;
use Bixie\Gls\Data\Event;
use Bixie\Gls\Data\EventReason;
use Bixie\Gls\Data\Parcel;
use Bixie\Gls\Data\Service;
use Bixie\Gls\GlsException;
use Bixie\Gls\Status\Ftp\FtpGls;
use YOOtheme\Framework\ApplicationAware;

class Status extends ApplicationAware {

	protected $processedShipmentGls;

	/**
	 * Status constructor.
	 * @param $app
	 */
	public function __construct ($app) {
		$this->app = $app;
	}

	public function getStatusUpdates ($existing_files = []) {

		$statuses = [];
		try {

			/** @var FtpGls $ftp */
			$ftp = $this['gls.ftp']();

			if ($filelist = $ftp->getList('/out')) {

				foreach ($filelist as $file) {

					if (preg_match('/ctt_(?P<gls>\d{8})_(?P<from>\d{17})_(?P<to>\d{17})\.xml$/', $file->getRealpath(), $info)) {
						$status = [
							'id' => 0,
							'filename' => basename($file->getRealpath()),
							'gls_number' => $info['gls'],
							'date_from' => \DateTime::createFromFormat( 'YmdHis000', $info['from']),
							'date_to' => \DateTime::createFromFormat( 'YmdHis000', $info['to']),
							'parcels' => [],
							'events' => []
						];

						//skip
						if (isset($existing_files[$status['filename']])) {
							$status['id'] = $existing_files[$status['filename']]['id'];
							if ($existing_files[$status['filename']]['state'] == GlsTracking::GLSTRACKING_STATE_SUCCESS) {
								continue;
							}
						}
						
						if ($contents = $ftp->getFileContents($file->getRealpath(), $this['path.xml'], $this['config']['gls_ftp_remove'])) {

							$xml = new \SimpleXMLElement($contents);
							//parcels
							foreach ($xml->xpath('Parcels/Parcel') as $parcel) {

								$Parcel = Parcel::create(array_merge(
									$this->getAttributes($parcel),
									$this->getAttributes($parcel->xpath('Identification')),
									$this->getAttributes($parcel->xpath('Product'), 'Product'),
									$this->getAttributes($parcel->xpath('PhysicalInformation'))
								));

								foreach ($parcel->xpath('Addresses/Address') as $address) {
									$Parcel->Addresses[] = Address::create(array_merge($this->getAttributes($address), [
										'CountryCode' => (string) $address->Country->attributes()->Code,
										'CountryName' => (string) $address->Country->attributes()->Name
									]));
								}

								foreach ($parcel->xpath('Services/Service') as $service) {
									$Parcel->Services[] = Service::create($this->getAttributes($service));
								}


								$status['parcels'][] = $Parcel;
							}
							//events
							foreach ($xml->xpath('Events/Event') as $event) {

								$Event = Event::create(array_merge(
									$this->getAttributes($event),
									$this->getAttributes($event->xpath('Identification')),
									$this->getAttributes($event->xpath('Location'), 'Location'),
									$this->getAttributes($event->xpath('Location/Country'), 'Country')
								));

								foreach ($event->xpath('EventReason') as $eventreason) {
									$Event->EventReason = EventReason::create(array_merge(
										$this->getAttributes($eventreason),
										$this->getAttributes($eventreason->xpath('Event'), 'Event'),
										$this->getAttributes($eventreason->xpath('Reason'), 'Reason')
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

	public function processStatuses ($statuses) {
		$trackings = [];
		foreach ($statuses as $status) {
			$tracking = [
				'filename' => $status['filename'],
				'error' => false
			];
			try {

				$parcels = $status['parcels'];
				$events = $status['events'];

				//save empty file log
				unset($status['parcels']);
				unset($status['events']);
				$this['glstracking']->save($status);

				$this->processedShipmentGls = [];

				$status['parcels'] = $this->processParcels($parcels);
				$status['events'] = $this->processEvents($events);

				foreach ($this->processedShipmentGls as $shipmentGls) {
					$this['shipmentgls']->save($shipmentGls->toArray());
				}

				$status['state'] = GlsTracking::GLSTRACKING_STATE_SUCCESS;
				$this['glstracking']->save($status);


			} catch (\Exception $e) {

				$tracking['error'] = $e->getMessage();

			}

			$trackings[] = $tracking;

		}

		return $trackings;
	}

	/**
	 * @param Parcel[] $parcels
	 * @return array
	 */
	protected function processParcels ($parcels) {
		$log = new Logger();

		foreach ($parcels as $parcel) {

			if ($shipmentGls = $this['shipmentgls']->findDomesticParcelNumberNl($parcel->ParcelNumber)) {

				$shipmentGls->setParcel($parcel);
				$this->processedShipmentGls[$shipmentGls->getId()] = $shipmentGls;

				$log->count('parcels');
			} else {
				$log->error(sprintf('Pakket %s niet gevonden in database!', $parcel->ParcelNumber));
			}

		}
		return $log;
	}

	protected function processEvents ($events) {
		$log = new Logger();
		
		foreach ($events as $event) {

			if ($shipmentGls = $this['shipmentgls']->findDomesticParcelNumberNl($event->ParcelNumber)) {

				$shipmentGls->addEvent($event);
				$shipmentGls->setState(ShipmentGls::SHIPMENTGLS_STATE_SCANNED);
				$this->processedShipmentGls[$shipmentGls->getId()] = $shipmentGls;

				$log->count('events');
			} else {
				$log->error(sprintf('Pakket %s niet gevonden in database!', $event->ParcelNumber));
			}

		}

		return $log;
	}

	/**
	 * @param \SimpleXMLElement|array $xml
	 * @param string                  $prefix
	 * @return array
	 */
	protected function getAttributes ($xml, $prefix = '') {
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