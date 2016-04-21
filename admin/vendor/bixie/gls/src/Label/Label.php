<?php
/* *
 *	Bixie Printshop
 *  Label.php
 *	Created on 7-12-2015 19:51
 *  
 *  @author Matthijs
 *  @copyright Copyright (C)2015 Bixie.nl
 *
 */


namespace Bixie\Gls\Label;


use Bixie\Devos\Model\Sender\Sender;
use Bixie\Devos\Model\Shipment\ShipmentGls;
use Bixie\Gls\Label\Zpl\Template as ZplTemplate;
use Bixie\Gls\Label\Pdf\Template as PdfTemplate;
use Bixie\Gls\Label\Png\Template as PngTemplate;

class Label {
	/**
	 * @var string
	 */
	public $template;
	/**
	 * @var ShipmentGls
	 */
	protected $shipment;
	/**
	 * @var Sender
	 */
	protected $sender;

	/**
	 * Label constructor.
	 * @param ShipmentGls $shipment
	 * @param Sender      $sender
	 */
	public function __construct (ShipmentGls $shipment, Sender $sender) {
		$this->template = $shipment['label_template'] ?: 'gls_default';
		$this->shipment = $shipment;
		$this->sender = $sender;
	}

	/**
	 * @return string
	 */
	public function createZplLabel () {
		$stream = json_decode($this->shipment->getGlsStream(), 1);
		if (empty($stream['zpl_raw'])) {
			throw new \InvalidArgumentException(sprintf('Geen ZPL template gevonden %s.', $this->shipment['domestic_parcel_number_nl']));
		}
		return (new ZplTemplate($stream['zpl_raw']))->addSenderLogo(($this->sender['image'] ? JPATH_ROOT . $this->sender['image'] : ''))->render();
	}

	/**
	 * @return string
	 */
	public function createPdfLabel () {
		if (empty($this->shipment['zpl_template'])) {
			throw new \InvalidArgumentException(sprintf('Geen ZPL template gevonden %s.', $this->shipment['domestic_parcel_number_nl']));
		}
		return (new PdfTemplate($this->shipment['zpl_template']))->render();
	}

	/**
	 * @return string
	 */
	public function createPngLabel () {
		if (empty($this->shipment['zpl_template'])) {
			throw new \InvalidArgumentException(sprintf('Geen ZPL template gevonden %s.', $this->shipment['domestic_parcel_number_nl']));
		}
		return (new PngTemplate($this->shipment['zpl_template']))->render();
	}


}