<?php

namespace Bixie\Devos\Controller;

use Bixie\Devos\Model\Sender\Sender;
use Bixie\Devos\Model\Shipment\ShipmentGls;
use Bixie\Framework\Routing\Controller;

class DashboardController extends Controller {

	public function indexAction () {

		\JToolbarHelper::title('De Vos diensten beheer', 'bix-devos');

		if ($this['user']->hasPermission('manage_devos')) {
			\JToolbarHelper::preferences('com_bix_devos');
		}

		$data = [
            'sc_user' => $this['sendcloud']->getUser(),
        ];

		$this['scripts']->add('devos-data', sprintf('var $data = %s;', json_encode($data)), '', 'string');

		return $this['view']->render('views/admin/dashboard.php', $data);
	}

	public function shipmentsAction () {

		\JToolbarHelper::title('De Vos diensten beheer - Verzendingen GLS', 'bix-devos');

		if ($this['user']->hasPermission('manage_devos')) {
			\JToolbarHelper::preferences('com_bix_devos');
		}
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
			'countries' => $this['countries'],
			'sender_states' => Sender::getStates()
		];
		$this['scripts']->add('devos-data', sprintf('var $data = %s;', json_encode($data)), '', 'string');

		return $this['view']->render('views/admin/shipments.php', $data);
	}

	public function sendcloudAction () {

		\JToolbarHelper::title('De Vos diensten beheer - Verzendingen Sendcloud', 'bix-devos');

//        $parcels = $this->app['sendcloud']->getParcels();

		if ($this['user']->hasPermission('manage_devos')) {
			\JToolbarHelper::preferences('com_bix_devos');
		}
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
			'countries' => $this['countries'],
            'sc_shipping_methods' => $this['sendcloud']->getShippingMethods(),
			'sender_states' => Sender::getStates()
		];
		$this['scripts']->add('devos-data', sprintf('var $data = %s;', json_encode($data)), '', 'string');

		return $this['view']->render('views/admin/shipments-sendcloud.php', $data);
	}

	public function getSettingsAction () {
		return $this['response']->json($this['config.fetch']);
	}

	public function saveSettingsAction ($data) {
		$this['config']->add($data);
		return $this['response']->json($this['config.save']->toArray());
	}

	public static function getRoutes () {
		return array(
			array('index', 'indexAction', 'GET', array('access' => 'manage_devos')),
			array('/api/config', 'getSettingsAction', 'GET', array('access' => 'manage_devos')),
			array('/api/config', 'saveSettingsAction', 'POST', array('access' => 'manage_devos')),
			array('/shipments', 'shipmentsAction', 'GET', array('access' => 'client_devos')),
			array('/sendcloud', 'sendcloudAction', 'GET', array('access' => 'client_devos'))
		);
	}
}
