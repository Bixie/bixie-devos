<?php


namespace Bixie\Gls;


use Bixie\Devos\Model\Shipment\ShipmentGls;
use Bixie\Gls\Data\Broadcast;
use Bixie\Gls\Data\Label;
use Bixie\Gls\Unibox\Socket;
use YOOtheme\Framework\Application;
use YOOtheme\Framework\ApplicationAware;

class Gls extends ApplicationAware {
	/**
	 * @var Socket
	 */
	protected $socket;

	public function __construct (Application $app) {
		$this->app = $app;
		$this->socket = new Socket(
			$app['config']['glsserver'],
			($app['config']['gls_test'] ? $app['config']['glsport_test'] : $app['config']['glsport_live'])
		);
	}


	public function createShipment (ShipmentGls $shipment) {

		try {
			$broadcast = Broadcast::create(array_merge($shipment->toArray(), $shipment->getData()));
			$broadcast['software_version'] = 'V ' . $this->app['version'];

			$broadcast['gls_customer_number'] = $this->app['config']['gls_customer_number'];
			$broadcast['sap_number'] = $this->app['config']['sap_number'];
			$broadcast['mode'] = 'NOPRINT';
//			$broadcast['printer_template'] = 'zebrazpl'; //Datamax,zebrazpl
			//parcel nummer bepalen
			$shipment->setParcelNumber(($this->app['shipmentgls']->lastParcelNumber() + 1));
			$broadcast['domestic_parcel_number_nl'] = $broadcast->getGlsParcelNumber($shipment->getParcelNumber());

			$broadcast->validate();

			$result = $this->socket->send($broadcast);

			$tagData = $broadcast->parseIncomingStream($result);

			$shipment->setGlsStream(json_encode($tagData, true));

			$shipment->mergeData($tagData);

			return $broadcast;

		} catch (GlsException $e) {

			throw new \RuntimeException($e->getMessage(), $e->getCode(), $e);
		}
	}

	public function createLabel (ShipmentGls $shipment) {

		$label = new Label($shipment);

		$pdfString = $label->createPdfLabel();

		$shipment->savePdfString($this->app['path.pdf'], $pdfString);

	}


}
