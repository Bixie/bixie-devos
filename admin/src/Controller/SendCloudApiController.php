<?php

namespace Bixie\Devos\Controller;

use Bixie\Devos\Model\Sender\Sender;
use Bixie\Devos\Model\Shipment\ShipmentSendCloud;
use Bixie\Framework\PostcodeLookup\PostcodeLookup;
use Bixie\Framework\Routing\StreamedResponse;
use Bixie\Framework\User\User;
use Bixie\Framework\Utils\Query;
use Bixie\Framework\Routing\Controller;
use Bixie\Framework\Routing\Exception\HttpException;
use Bixie\SendCloud\Carriers\SendCloud\Parcel;

class SendCloudApiController extends Controller {

	public function indexSendCloudAction ($filter = [], $page = 0) {
		$return = new \ArrayObject;

		$query = $this->getQuery($filter);

		$limit = isset($filter['limit']) ? (int)$filter['limit'] : 10;
		$return['total'] = $this['shipmentsendcloud']->count($query);
		$return['pages'] = ceil($return['total'] / $limit);
		$return['page'] = max(0, min($return['pages'] - 1, $page));
		$start = $return['page'] * $limit;

		$return['shipments'] = $this['shipmentsendcloud']->query($query, $start, $limit);

		return $this['response']->json($return);

	}

	public function csvSendCloudAction ($filter = []) {

		$query = $this->getQuery($filter);

		$filename = sprintf('verzendingen_%s_%s', $filter['created_from'], $filter['created_to']);

		$shipments = $this['shipmentsendcloud']->query($query);
		$ignore = ['data','parcel','events','sendcloud_stream','pdf_path','zpl_template'];
		$response = new StreamedResponse();
		$response->setCallback(function() use ($shipments, $ignore, $filename) {

			$handle = fopen('php://output', 'w');
			fputcsv($handle, array_keys(ShipmentSendCloud::create()->toArray(['track_trace' => ''], $ignore)), ';');

			foreach ($shipments as $shipment) {
				$a = array_values($shipment->toArray(['track_trace' => $shipment['track_trace']], $ignore));
				fputcsv($handle, $a, ';');
			}

			fclose($handle);
		});

		$response->setStatus(200);
		$response->headers->set('Content-Type', 'text/csv; charset=utf-8');
		$response->headers->set('Content-Disposition','attachment; filename="'.$filename.'.csv"');

		return $response;
	}

	public function getShipmentSendCloudAction ($id) {
		/** @var User $user */
		$user = $this['users']->get();
		if (!$user['klantnummer']) {
			throw new HttpException(403, 'Geen klantnummer bekend');
		}
		if ($id == 0) {

			$object = ShipmentSendCloud::create([
				'klantnummer' => $user['klantnummer'],
				'sendcloud_customer_number' => $user['sendcloud_customer_number']
			]);
			return $this['response']->json($object);
		}
		/** @var ShipmentSendCloud $shipment */
		if ($shipment = $this['shipmentsendcloud']->find($id)) {
			if (!$user->hasPermission('manage_devos') && $shipment->getKlantnummer() != $user['klantnummer']) {
				throw new HttpException(403, 'Geen rechten om deze verzending te bekijken');
			}
			return $this['response']->json($shipment);
		}

		throw new HttpException(404);

	}

	public function saveShipmentSendCloudAction ($data) {
		$status = !isset($data['id']) || !$data['id'] ? 201 : 200;
		$return = new \ArrayObject;

		if ($data = $this->saveShipment($data)) {
			$return['shipment'] = $data;

			return $this['response']->json($return, $status);
		}

		throw new HttpException(400);
	}

	public function sendShipmentSendCloudAction ($id) {
		$return = new \ArrayObject;

		try {
			/** @var ShipmentSendCloud $shipment */
			if (!$shipment = $this['shipmentsendcloud']->find($id)) {
				throw new \Exception(sprintf('Verzending id %d niet gevonden.', $id));
			}
			/** @var User $user */
			$user = $this['users']->get();
			if (!$user->hasPermission('manage_devos') && $shipment->getKlantnummer() != $user['klantnummer']) {
				throw new \Exception('Geen rechten om deze verzending te bekijken', 403);
			}

			$return['shipment'] = $this->sendShipment($shipment);


		} catch (\Exception $e) {
			$return['error'] = $e->getMessage();

		}


		return $this['response']->json($return, 200);

	}

	public function labelShipmentSendCloudAction ($id) {
		$return = new \ArrayObject;

		try {
			/** @var ShipmentSendCloud $shipment */
			if (!$shipment = $this['shipmentsendcloud']->find($id)) {
				throw new \Exception(sprintf('Verzending id %d niet gevonden.', $id));
			}
			/** @var User $user */
			$user = $this['users']->get();
			if (!$user->hasPermission('manage_devos') && $shipment->getKlantnummer() != $user['klantnummer']) {
				throw new \Exception('Geen rechten om deze verzending te bekijken', 403);
			}
			/** @var Sender $sender */
			if (!$sender = $this['sender']->find($shipment->getSenderId())) {
				throw new \Exception(sprintf('Verzender id %d niet gevonden.', $shipment->getSenderId()));
			}

			$this['sendcloud']->createLabel($shipment, $sender);

			$this->app['shipmentsendcloud']->save($shipment->toArray());

			$return['shipment'] = $shipment;


		} catch (\Exception $e) {
			$return['error'] = $e->getMessage();

		}


		return $this['response']->json($return, 200);

	}


	public function createShipmentSendCloudAction ($data) {

		$return = new \ArrayObject;
		try {

			if ($data = $this->saveShipment($data)) {

				$return['shipment'] = $this->createShipment($data);

				return $this['response']->json($return, 201);
			}

		} catch (\Exception $e) {
			$return['error'] = $e->getMessage();

		}
		return $this['response']->json($return, 200);

	}

	public function createBulkShipmentSendCloudAction ($shipments) {

		$return = new \ArrayObject;
		$created = [];
		try {

			foreach ($shipments as $data) {
				if ($data = $this->saveShipment($data)) {

					$created[] = $this->createShipment($data);

				}

			}
			$return['shipments'] = $created;
			return $this['response']->json($return, 201);

		} catch (\Exception $e) {
			$return['error'] = $e->getMessage();

		}

		return $this['response']->json($return, 200);
	}


	public function labelPngShipmentSendCloudAction ($tracking_number) {

		/** @var ShipmentSendCloud $shipment */
		if (!$shipment = $this['shipmentsendcloud']->findDomesticParcelNumberNl($domestic_parcel_number_nl)) {
			throw new HttpException(404, sprintf('Verzending nr %d niet gevonden.', $domestic_parcel_number_nl));
		}

		/** @var User $user */
		$user = $this['users']->get();
		if (!$user->hasPermission('manage_devos') && $shipment->getKlantnummer() != $user['klantnummer']) {
			throw new HttpException(403, 'Geen rechten om deze verzending te bekijken');
		}

		if (!$png_string = $this['sendcloud']->pngLabel($shipment)) {
			throw new HttpException(400, sprintf('Fout in PNG verzending %d.', $domestic_parcel_number_nl));
		}

		return $this['response']->file($png_string, 200, [], false, 'inline');

	}


	public function pdfShipmentSendCloudAction ($tracking_number, $string = false) {

		/** @var ShipmentSendCloud $shipment */
		if (!$shipment = $this['shipmentsendcloud']->findTrackingNumber($tracking_number)) {
			throw new HttpException(404, sprintf('Verzending nr %d niet gevonden.', $tracking_number));
		}

		/** @var User $user */
		$user = $this['users']->get();
		if (!$user->hasPermission('manage_devos') && $shipment->getKlantnummer() != $user['klantnummer']) {
			throw new HttpException(403, 'Geen rechten om deze verzending te bekijken');
		}

		if (!$file = $shipment->getPdfPath() or !file_exists($file)) {
			throw new HttpException(400, sprintf('Verzending nr %d heeft geen gekoppeld PDF bestand.', $tracking_number));
		}

		return $this['response']->file($file, 200, [], false, $string ? 'inline' :'attachment');

	}

	public function postcodeCheckAction ($postcode, $huisnr, $toev = '') {
		$return = new \ArrayObject;
		$settings = $this['settings'];
		$lookup = new PostcodeLookup($settings['pc_api_url'], $settings['pc_api_name'], $settings['pc_api_secret']);

		try {

			$return['result'] = $lookup->lookup($postcode, $huisnr, $toev);

			return $this['response']->json($return, 200);

		} catch (\Exception $e) {
			throw new HttpException(400, $e->getMessage(), $e, $e->getCode());
		}

	}

	/**
	 * @param array $data
	 * @return array|bool
	 */
	protected function saveShipment ($data) {
		/** @var User $user */
		if (!$this['admin']) {
			$user = $this['users']->get();
			if (!$user['klantnummer']) {
				throw new HttpException(403, 'Geen klantnummer bekend');
			}

			$data['klantnummer'] = $user['klantnummer'];
//            if (empty($data['id']) && $user['mail_admin_onparcel'] !== 0) {
//                $maildata = $this['mail']->getMaildata($this['config']['parcel_mail']);
//                $maildata['subject'] = sprintf('Pakket aangemeld door %s namens %s', $user['name'], $data['sender_name_1'] );
//                $maildata['body'] = $this['view']->render('views/mail/parcel_sent.php', ['data' => $data, 'user' => $user]);
//                $this['mail']->sendMail($maildata);
//            }
		}

		if ($data = $this['shipmentsendcloud']->save($data)) {
			return $data;
		}
		return false;
	}

	/**
	 * @param ShipmentSendCloud $shipment
	 * @return ShipmentSendCloud
	 * @throws \Exception
	 */
	protected function sendShipment (ShipmentSendCloud $shipment) {

		$existingSendCloud = $shipment->getSendcloudId();
		if (!empty($existingSendCloud)) {
			throw new \Exception(sprintf('Verzending heeft al een SENDCLOUD nummer: %s.', $existingSendCloud));
		}
        /** @var Parcel $parcel */
		if (!$parcel = $this['sendcloud']->createShipment($shipment)) {
            throw new \Exception('Fout in soundcload API');
        }
        $parcel->setParcelData($shipment);
        $shipment['track_trace'] = $parcel->getTrackingUrl();

        $this->app['shipmentsendcloud']->save($shipment->toArray());

		return $shipment;
	}

	/**
	 * @param array $data
	 * @return ShipmentSendCloud|bool
	 * @throws \Exception
	 */
	protected function createShipment ($data) {
		if ($data = $this->saveShipment($data)) {

			/** @var ShipmentSendCloud $shipment */
			if (!$shipment = $this['shipmentsendcloud']->find($data['id'])) {
				throw new \Exception(sprintf('Verzending id %d niet gevonden.', $data['id']));
			}

			$shipment = $this->sendShipment($shipment);

			/** @var Sender $sender */
			if (!$sender = $this['sender']->find($shipment->getSenderId())) {
				throw new \Exception(sprintf('Verzender id %d niet gevonden.', $shipment->getSenderId()));
			}
			$this['sendcloud']->createLabel($shipment, $sender);

			$this->app['shipmentsendcloud']->save($shipment->toArray());

			return $shipment;
		}
		return false;
	}

	/**
	 * @param $filter
	 * @return Query
	 */
	protected function getQuery ($filter) {
		$query = Query::query('@dv_shipment_sendcloud', '*');
		$filter = array_merge(array_fill_keys([
			'search', 'state', 'sendcloud_customer_number', 'klantnummer', 'sender_id', 'created_from', 'created_to', 'order', 'dir', 'limit'
		], ''), $filter);

		/**
		 * @var string $search
		 * @var string $klantnummer
		 * @var int    $sender_id
		 * @var string $sendcloud_customer_number
		 * @var string $created_from
		 * @var string $created_to
		 * @var string $state
		 * @var string $order
		 * @var int    $dir
		 * @var int    $limit
		 */
		extract($filter, EXTR_SKIP);

		if (is_numeric($state)) {
			$query->where('state = :state', ['state' =>(int)$state]);
		} elseif ($state != 'all') {
			$query->where(['state > 0']);
		}

		if ($search) $query->where(sprintf('(%s LIKE :search)', implode(' LIKE :search OR ', [
			'sendcloud_id', 'klantnummer', 'name', 'company_name',
			'order_number', 'address', 'postal_code', 'tracking_number', 'city', 'email'
		])), ['search' => "%{$search}%"]);

		if (!$this['admin']) {

			/** @var User $user */
			$user = $this['users']->get();

			if (!$user['klantnummer']) {
				throw new HttpException(403, 'Geen klantnummer bekend');
			}

			$query->where('klantnummer = :klantnummer', ['klantnummer' => $user['klantnummer']]);
		} elseif ($klantnummer) {

			$query->where('klantnummer = :klantnummer', ['klantnummer' => $klantnummer]);
		}

		if ($sendcloud_customer_number) {
			$query->where('sendcloud_customer_number = :sendcloud_customer_number', ['sendcloud_customer_number' => $sendcloud_customer_number]);
		}

		if ($sender_id) {
			$query->where('sender_id = :sender_id', ['sender_id' => $sender_id]);
		}

		if ($created_from) {
			$created_from = $this->dateToSql($created_from);
			$query->where('created > :created_from', ['created_from' => $created_from]);
		}
		if ($created_to) {
			$created_to = $this->dateToSql($created_to);
			$query->where('created < :created_to', ['created_to' => $created_to]);
		}

		$order_col = in_array($order, [
			'sendcloud_parcel_number', 'sender_name_1', 'receiver_zip_code', 'modified', 'created'
		]) ? $order : 'created';
		$dir = $dir ?: 'DESC';

		$query->orderBy($order_col, $dir);
		return $query;
	}

	public function dateToSql ($date_string) {
		return (new \DateTime($date_string, new \DateTimeZone(\JFactory::$config->get('offset'))))->setTimezone(new \DateTimeZone('UTC'))->format('Y-m-d H:i:s');
	}

	/**
	 * @return array
	 */
	public static function getRoutes () {
		return array(
			array('/api/sendcloud', 'indexSendCloudAction', 'GET', array('access' => 'client_devos')),
			array('/api/sendcloud/csv', 'csvSendCloudAction', 'GET', array('access' => 'client_devos')),
			array('/api/sendcloud/:id', 'getShipmentSendCloudAction', 'GET', array('access' => 'client_devos')),
			array('/api/sendcloud/save', 'saveShipmentSendCloudAction', 'POST', array('access' => 'client_devos')),
			array('/api/sendcloud/send/:id', 'sendShipmentSendCloudAction', 'POST', array('access' => 'client_devos')),
			array('/api/sendcloud/label/:id', 'labelShipmentSendCloudAction', 'POST', array('access' => 'client_devos')),
			array('/api/sendcloud/create', 'createShipmentSendCloudAction', 'POST', array('access' => 'client_devos')),
			array('/api/sendcloud/createbulk', 'createBulkShipmentSendCloudAction', 'POST', array('access' => 'client_devos')),
			array('/api/sendcloud/png/:tracking_number', 'labelPngShipmentSendCloudAction', 'GET', array('access' => 'client_devos')),
			array('/api/sendcloud/pdf/:tracking_number', 'pdfShipmentSendCloudAction', 'GET', array('access' => 'client_devos')),
			array('/api/sendcloud/:id', 'deleteContentAction', 'DELETE', array('access' => 'client_devos')),
			array('/api/sendcloud/postcode', 'postcodeCheckAction', 'POST', array('access' => 'client_devos'))
		);
	}

}
