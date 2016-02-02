<?php

namespace Bixie\Devos\Controller;

use Bixie\Devos\Model\Shipment\ShipmentGls;
use Bixie\Framework\Utils\Query;
use YOOtheme\Framework\Routing\Controller;
use YOOtheme\Framework\Routing\Exception\HttpException;

class ShipmentController extends Controller {

	public function shipmentsAction () {

		\JToolBarHelper::title('De Vos diensten beheer - Verzendingen', 'bix-devos');

		if ($this['user']->hasPermission('manage_devos')) {
			\JToolBarHelper::preferences('com_bix_devos');
		}

		$data = [
		];
		$this['scripts']->add('devos-data', sprintf('var $data = %s;', json_encode($data)), '', 'string');

		return $this['view']->render('views/admin/shipments.php', $data);
	}

	public function indexGlsAction ($page = 0) {
		$return = new \ArrayObject;
		$query = Query::query('@dv_shipment_gls', '*');
		$query->where('state = 1');
		$start = 0;
		$limit = 0;
		$return['page'] = $page;
//		$return['total'] = $this['db']->count((string) $query, $query->getParams());
		$return['shipments'] = $this['shipmentgls']->query((string) $query, $query->getParams(), $start, $limit);

		return $this['response']->json($return);

	}

	public function getShipmentGlsAction ($id) {
		if ($id == 0) {
			$object = ShipmentGls::create($this['config']['sender_address']);
			return $this['response']->json($object);
		}
		if ($object = $this['shipmentgls']->find($id)) {
			return $this['response']->json($object);
		}

		throw new HttpException(404);

	}

	public function saveShipmentGlsAction ($data) {
		$status = !isset($data['id']) || !$data['id'] ? 201 : 200;
		$return = new \ArrayObject;

		if ($data = $this['shipmentgls']->save($data)) {
			$return['shipment'] = $data;

			return $this['response']->json($return, $status);
		}

		throw new HttpException(400);
	}

	public function sendShipmentGlsAction ($id) {
		$return = new \ArrayObject;

		try {
			/** @var ShipmentGls $shipment */
			if (!$shipment = $this['shipmentgls']->find($id)) {
				throw new \Exception(sprintf('Verzending id %d niet gevonden.', $id));
			}
			$existingGls = $shipment->getGlsParcelNumber();
			if (!empty($existingGls)) {
				throw new \Exception(sprintf('Verzending heeft al een GLS nummer: %s.', $existingGls));
			}

			$broadcast = $this['gls']->createShipment($shipment);

			$this->app['shipmentgls']->save($shipment->toArray());

			$return['shipment'] = $shipment;


		} catch (\Exception $e) {
			$return['error'] = $e->getMessage();

		}


		return $this['response']->json($return, 200);

	}


	public function labelShipmentGlsAction ($id) {
		$return = new \ArrayObject;

		try {
			/** @var ShipmentGls $shipment */
			if (!$shipment = $this['shipmentgls']->find($id)) {
				throw new \Exception(sprintf('Verzending id %d niet gevonden.', $id));
			}

			$this['gls']->createLabel($shipment);

			$this->app['shipmentgls']->save($shipment->toArray());

			$return['shipment'] = $shipment;


		} catch (\Exception $e) {
			$return['error'] = $e->getMessage();

		}


		return $this['response']->json($return, 200);

	}


	public static function getRoutes () {
		return array(
			array('/shipments', 'shipmentsAction', 'GET', array('access' => 'manage_devos')),
			array('/shipmentgls', 'indexGlsAction', 'GET', array('access' => 'manage_devos')),
			array('/shipmentgls/:id', 'getShipmentGlsAction', 'GET', array('access' => 'manage_devos')),
			array('/shipmentgls/send/:id', 'sendShipmentGlsAction', 'POST', array('access' => 'manage_devos')),
			array('/shipmentgls/label/:id', 'labelShipmentGlsAction', 'POST', array('access' => 'manage_devos')),
			array('/shipmentgls/save', 'saveShipmentGlsAction', 'POST', array('access' => 'manage_devos')),
			array('/shipmentgls/:id', 'deleteContentAction', 'DELETE', array('access' => 'manage_devos'))
		);
	}
}
