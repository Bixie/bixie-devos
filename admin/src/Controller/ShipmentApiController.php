<?php

namespace Bixie\Devos\Controller;

use Bixie\Devos\Model\Shipment\ShipmentGls;
use Bixie\Framework\User\User;
use Bixie\Framework\Utils\Query;
use YOOtheme\Framework\Routing\Controller;
use YOOtheme\Framework\Routing\Exception\HttpException;

class ShipmentApiController extends Controller {

	public function indexGlsAction ($filter = [], $page = 0) {
		$return = new \ArrayObject;
		$query = Query::query('@dv_shipment_gls', '*');
		$filter = array_merge(array_fill_keys(['search', 'state', 'order', 'dir', 'limit'], ''), $filter);

		/**
		 * @var string $search
		 * @var string $state
		 * @var string $order
		 * @var int $dir
		 * @var int $limit
		 */
		extract($filter, EXTR_SKIP);

		if (is_numeric($state)) {
			$query->where('state = ?', [(int)$state]);
		} elseif ($state != 'all') {
			$query->where(['state > 0']);
		}

		if ($search) $query->where(sprintf('(%s LIKE :search)', implode(' LIKE :search OR ', [
			'gls_parcel_number', 'domestic_parcel_number_nl', 'sender_name_1', 'customer_reference',
			'receiver_name_1', 'receiver_zip_code', 'receiver_street', 'receiver_place'
		])), ['search' => "%{$search}%"]);

		/** @var User $user */
		$user = $this['users']->get();

		if (!$user['klantnummer']) {
			throw new HttpException(403, 'Geen klantnummer bekend');
		}

		$query->where('klantnummer = :klantnummer', ['klantnummer' => $user['klantnummer']]);

		$order_col = in_array($order, [
				'gls_parcel_number', 'sender_name_1', 'receiver_zip_code', 'modified', 'created'
			]) ? $order : 'created';
		$dir = $dir ?: 'DESC';

		$query->orderBy($order_col, $dir);

		$limit = (int)$limit ?: 10;
		$return['total'] = $this['shipmentgls']->count($query);
		$return['pages'] = ceil($return['total'] / $limit);
		$return['page'] = max(0, min($return['pages'] - 1, $page));
		$start = $return['page'] * $limit;

		$return['shipments'] = $this['shipmentgls']->query($query, $start, $limit);

		return $this['response']->json($return);

	}

	public function getShipmentGlsAction ($id) {
		/** @var User $user */
		$user = $this['users']->get();
		if (!$user['klantnummer']) {
			throw new HttpException(403, 'Geen klantnummer bekend');
		}
		if ($id == 0) {

			$object = ShipmentGls::create([
				'klantnummer' => $user['klantnummer'],
				'gls_customer_number' => $user['gls_customer_number']
			]);
			return $this['response']->json($object);
		}
		/** @var ShipmentGls $shipment */
		if ($shipment = $this['shipmentgls']->find($id)) {
			if (!$user->hasPermission('manage_devos') && $shipment->getKlantnummer() != $user['klantnummer']) {
				throw new HttpException(403, 'Geen rechten om deze verzending te bekijken');
			}
			return $this['response']->json($shipment);
		}

		throw new HttpException(404);

	}

	public function saveShipmentGlsAction ($data) {
		$status = !isset($data['id']) || !$data['id'] ? 201 : 200;
		$return = new \ArrayObject;

		/** @var User $user */
		$user = $this['users']->get();
		if (!$user['klantnummer']) {
			throw new HttpException(403, 'Geen klantnummer bekend');
		}

		$data['klantnummer'] = $user['klantnummer'];
		$data['gls_customer_number'] = $user['gls_customer_number'] ? : $this['config']['gls_customer_number'];

		if (empty($data['date_of_shipping'])) {
			$data['date_of_shipping'] = (new \DateTime())->format('Y-m-d H:i:s');
		}

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
			/** @var User $user */
			$user = $this['users']->get();
			if (!$user->hasPermission('manage_devos') && $shipment->getKlantnummer() != $user['klantnummer']) {
				throw new \Exception('Geen rechten om deze verzending te bekijken', 403);
			}

			$existingGls = $shipment->getGlsParcelNumber();
			if (!empty($existingGls)) {
				throw new \Exception(sprintf('Verzending heeft al een GLS nummer: %s.', $existingGls));
			}

			$this['gls']->createShipment($shipment);

			$shipment['track_trace'] = $this['gls']->getTrackTrace($shipment);

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
			/** @var User $user */
			$user = $this['users']->get();
			if (!$user->hasPermission('manage_devos') && $shipment->getKlantnummer() != $user['klantnummer']) {
				throw new \Exception('Geen rechten om deze verzending te bekijken', 403);
			}

			$this['gls']->createLabel($shipment);

			$this->app['shipmentgls']->save($shipment->toArray());

			$return['shipment'] = $shipment;


		} catch (\Exception $e) {
			$return['error'] = $e->getMessage();

		}


		return $this['response']->json($return, 200);

	}

	public function pdfShipmentGlsAction ($domestic_parcel_number_nl) {

		/** @var ShipmentGls $shipment */
		if (!$shipment = $this['shipmentgls']->findDomesticParcelNumberNl($domestic_parcel_number_nl)) {
			throw new HttpException(404, sprintf('Verzending nr %d niet gevonden.', $domestic_parcel_number_nl));
		}

		/** @var User $user */
		$user = $this['users']->get();
		if (!$user->hasPermission('manage_devos') && $shipment->getKlantnummer() != $user['klantnummer']) {
			throw new HttpException(403, 'Geen rechten om deze verzending te bekijken');
		}

		if (!$file = $shipment->getPdfPath() or !file_exists($file)) {
			throw new HttpException(400, sprintf('Verzending nr %d heeft geen gekoppeld PDF bestand.', $domestic_parcel_number_nl));
		}

		return $this['response']->file($file, 200, [], false, 'attachment');

	}


	public static function getRoutes () {
		return array(
			array('/api/shipment', 'indexGlsAction', 'GET', array('access' => 'client_devos')),
			array('/api/shipment/:id', 'getShipmentGlsAction', 'GET', array('access' => 'client_devos')),
			array('/api/shipment/send/:id', 'sendShipmentGlsAction', 'POST', array('access' => 'client_devos')),
			array('/api/shipment/label/:id', 'labelShipmentGlsAction', 'POST', array('access' => 'client_devos')),
			array('/api/shipment/pdf/:domestic_parcel_number_nl', 'pdfShipmentGlsAction', 'GET', array('access' => 'client_devos')),
			array('/api/shipment/save', 'saveShipmentGlsAction', 'POST', array('access' => 'client_devos')),
			array('/api/shipment/:id', 'deleteContentAction', 'DELETE', array('access' => 'client_devos'))
		);
	}
}
