<?php

namespace Bixie\Devos\Controller;

use Bixie\Devos\Model\Address\Address;
use Bixie\Devos\Model\Sender\Sender;
use Bixie\Devos\Model\Shipment\ShipmentGls;
use Bixie\Framework\Routing\Controller;

class SiteController extends Controller {

	public function indexAction () {
		$now = new \DateTime();
		$created_from = new \DateTime($now->format('Y-m-01'));
		$created_to = clone $created_from;
		$created_to->add(new \DateInterval('P1M'))->sub(new \DateInterval('P1D'));

		$data = [
			'exportFilter' => [
				'created_from' => $created_from->format('Y-m-d'),
				'created_to' => $created_to->format('Y-m-d'),
				'state' => ShipmentGls::SHIPMENTGLS_STATE_SCANNED
			],
            'sc_shipping_methods' => $this['sendcloud']->getShippingMethods(),
            'countries' => $this['countries'],
			'address_states' => Address::getStates(),
			'sender_states' => Sender::getStates()
		];
		$this['scripts']->add('devos-data', sprintf('var $data = %s;', json_encode($data)), '', 'string');

		return $this['view']->render('views/dashboard.php', $data);
	}

	public static function getRoutes () {
		return array(
			array('/dashboard', 'indexAction', 'GET', array('access' => 'client_devos'))
		);
	}
}
