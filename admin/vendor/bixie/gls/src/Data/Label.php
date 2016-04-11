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


namespace Bixie\Gls\Data;


use Bixie\Devos\Model\Shipment\ShipmentGls;
use Bixie\Framework\Utils\Arr;
use Bixie\Gls\Zpl\ZplTemplate;
use Dompdf\Dompdf;

class Label implements \ArrayAccess {
	/**
	 * @var string
	 */
	public $template;
	/**
	 * @var array
	 */
	protected $data = [];

	/**
	 * Label constructor.
	 * @param ShipmentGls $shipment
	 */
	public function __construct (ShipmentGls $shipment) {
		$this->template = $shipment['label_template'] ?: 'gls_default';
		$this->data = array_merge(
			json_decode($shipment->getGlsStream(), 1),
			$shipment->toArray(),
			$shipment->getData()
		);
	}

	public function createZplLabel () {
		if (empty($this->data['zpl_raw'])) {
			throw new \InvalidArgumentException(sprintf('Geen ZPL template gevonden %s.', $this->data['domestic_parcel_number_nl']));
		}
		return (new ZplTemplate($this->data['zpl_raw']))->render();
	}

	public function createPdfLabel () {
		// instantiate and use the dompdf class
		$dompdf = new Dompdf();
		$dompdf->loadHtml($this->getTemplateContents());

		// (Optional) Setup the paper size and orientation
		$dompdf->setPaper(array(0, 0, 297.64, 419.53), 'portrait');

		// Render the HTML as PDF
		$dompdf->render();

		// Get the generated PDF file contents
		return $dompdf->output();
	}

	public function getTemplateContents () {
		$path = dirname(dirname(__DIR__)) . '/templates/' . $this->template . '.php';
		if (!file_exists($path)) {
			throw new \InvalidArgumentException(sprintf('Geen template gevonden voor %s.', $this->template));
		}
		$label = $this;
		//set the barcodes
		$primary_code = (new \TCPDF2DBarcode($label['primary_code'], 'DATAMATRIX'))->getBarcodePngData(2, 2, array(0,0,0));
		$primary_code = 'data:image/png;base64,' . base64_encode($primary_code);

		$secondary_code = (new \TCPDF2DBarcode($label['secondary_code'], 'DATAMATRIX'))->getBarcodePngData(2, 2, array(0,0,0));
		$secondary_code = 'data:image/png;base64,' . base64_encode($secondary_code);

		$domestic_parcel_number_nl = (new \TCPDFBarcode($label['domestic_parcel_number_nl'], 'I25'))->getBarcodePngData(2, 50, array(0,0,0));
		$domestic_parcel_number_nl = 'data:image/png;base64,' . base64_encode($domestic_parcel_number_nl);

		$gls_parcel_number = '';
		if (is_numeric($label['gls_parcel_number'])) {
			$gls_parcel_number = (new \TCPDFBarcode($label['gls_parcel_number'], 'I25'))->getBarcodePngData(2, 30, array(0,0,0));
			$gls_parcel_number = 'data:image/png;base64,' . base64_encode($gls_parcel_number);
		}
		//get content
		ob_start();
		include($path);
		$contents = ob_get_clean();
		return $contents;

	}

	/**
	 * Checks if a key exists.
	 * @param  string $key
	 * @return bool
	 */
	public function offsetExists ($key) {
		return Arr::has($this->data, $key);
	}

	/**
	 * Gets a value by key.
	 * @param  string $key
	 * @return mixed
	 */
	public function offsetGet ($key) {
		return Arr::get($this->data, $key);
	}

	/**
	 * Sets a value.
	 * @param string $key
	 * @param string $value
	 */
	public function offsetSet ($key, $value) {
		Arr::set($this->data, $key, $value);
	}

	/**
	 * Unset a value.
	 * @param string $key
	 */
	public function offsetUnset ($key) {
		Arr::remove($this->data, $key);
	}

}