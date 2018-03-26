<?php


namespace Bixie\Gls;


use Bixie\Devos\Model\Sender\Sender;
use Bixie\Devos\Model\Shipment\ShipmentGls;
use Bixie\Gls\Data\Broadcast;
use Bixie\Gls\Label\Label;
use Bixie\Gls\Unibox\Socket;
use Bixie\Framework\Application;
use Bixie\Framework\ApplicationAware;

class Gls extends ApplicationAware {
	/**
	 * @var Socket
	 */
	protected $socket;

	protected $glsTracking;

	public function __construct (Application $app) {
		$this->app = $app;
		$this->socket = new Socket(
			$app['config']['glsserver'],
			($app['config']['gls_test'] ? $app['config']['glsport_test'] : $app['config']['glsport_live'])
		);
	}

	public function getGlsTracking () {
		if (!isset($this->glsTracking)) {
			$this->glsTracking = new GlsTracking($this->app['config']['gls_tracking']);
		}
		return $this->glsTracking;
	}

    /**
     * @param ShipmentGls $shipment
     * @return Broadcast
     */
	public function createShipment (ShipmentGls $shipment) {

		try {
			$broadcast = Broadcast::create(array_merge($shipment->toArray(), $shipment->getData()));
			$broadcast['software_version'] = 'V ' . $this->app['version'];

			$broadcast['gls_customer_number'] = $shipment->getGlsCustomerNumber();
			$broadcast['sap_number'] = $this->app['config']['sap_number'];
//			$broadcast['mode'] = 'NOPRINT';
			$broadcast['printer_template'] = 'zebrazpl'; //Datamax,zebrazpl

			//parcel nummer bepalen
			$shipment->setParcelNumber(($this->app['shipmentgls']->lastParcelNumber($shipment->getGlsCustomerNumber()) + 1));
			$broadcast['domestic_parcel_number_nl'] = $broadcast->getDomesticParcelNumber($shipment->getParcelNumber());
			$this->app['shipmentgls']->save([
				'id' => $shipment->getId(),
				'domestic_parcel_number_nl' => $broadcast['domestic_parcel_number_nl'],
				'parcel_number' => $shipment->getParcelNumber()
			]);

			$broadcast->validate();

			$result = $this->socket->send($broadcast);

			$tagData = $broadcast->parseIncomingStream($result);
			$tagData['zpl_raw'] = $broadcast->getTemplate($result);
			$shipment->setGlsStream(json_encode($tagData, true));

			$shipment->mergeData($tagData);

			return $broadcast;

		} catch (GlsException $e) {

			throw new \RuntimeException($e->getMessage(), $e->getCode(), $e);
		}
	}

    /**
     * @param ShipmentGls $shipment
     * @param Sender $sender
     */
	public function createLabel (ShipmentGls $shipment, Sender $sender) {

		$label = new Label($shipment, $sender);

		$shipment->setZplTemplate($label->createZplLabel());
		$shipment->savePdfString($this->app['path.pdf'], $label->createPdfLabel());
		$shipment->saveImageString($this->app['path.image'], $label->createPngLabel());

	}

	public function pngLabel (ShipmentGls $shipment) {
		
		if ($png = $shipment['png_path'] && file_exists($shipment['png_path'])) {
			return $shipment['png_path'];
		}
		
		return '';
		
	}

	public function getTrackTrace (ShipmentGls $shipment) {

		return $this->getGlsTracking()->getUrl($shipment->getGlsCustomerNumber(), $shipment->getDomesticParcelNumberNl());

	}

}
