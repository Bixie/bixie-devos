<?php

namespace Bixie\Devos\Controller;

use Bixie\Devos\Model\Sender\Sender;
use Bixie\Devos\Model\Shipment\ShipmentGls;
use Bixie\Framework\PostcodeLookup\PostcodeLookup;
use Bixie\Framework\Routing\StreamedResponse;
use Bixie\Framework\User\User;
use Bixie\Framework\Utils\Query;
use Bixie\Framework\Routing\Controller;
use Bixie\Framework\Routing\Exception\HttpException;

class ShipmentApiController extends Controller {

	public function indexGlsAction ($filter = [], $page = 0) {
		$return = new \ArrayObject;

		$query = $this->getQuery($filter);

		$limit = isset($filter['limit']) ? (int)$filter['limit'] : 10;
		$return['total'] = $this['shipmentgls']->count($query);
		$return['pages'] = ceil($return['total'] / $limit);
		$return['page'] = max(0, min($return['pages'] - 1, $page));
		$start = $return['page'] * $limit;

		$return['shipments'] = $this['shipmentgls']->query($query, $start, $limit);

		return $this['response']->json($return);

	}

	public function csvGlsAction ($filter = []) {

		$query = $this->getQuery($filter);

		$filename = sprintf('verzendingen_%s_%s', $filter['created_from'], $filter['created_to']);

		$shipments = $this['shipmentgls']->query($query);
		$ignore = ['data','parcel','events','gls_stream','pdf_path','zpl_template'];
		$response = new StreamedResponse();
		$response->setCallback(function() use ($shipments, $ignore, $filename) {

			$handle = fopen('php://output', 'w');
			fputcsv($handle, array_keys(ShipmentGls::create()->toArray(['track_trace' => ''], $ignore)), ';');

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

		if ($data = $this->saveShipment($data)) {
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

			$return['shipment'] = $this->sendShipment($shipment);


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
			/** @var Sender $sender */
			if (!$sender = $this['sender']->find($shipment->getSenderId())) {
				throw new \Exception(sprintf('Verzender id %d niet gevonden.', $shipment->getSenderId()));
			}

			$this['gls']->createLabel($shipment, $sender);

			$this->app['shipmentgls']->save($shipment->toArray());

			$return['shipment'] = $shipment;


		} catch (\Exception $e) {
			$return['error'] = $e->getMessage();

		}


		return $this['response']->json($return, 200);

	}


	public function createShipmentGlsAction ($data) {

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

	public function createBulkShipmentGlsAction ($shipments) {

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


	public function labelPngShipmentGlsAction ($domestic_parcel_number_nl) {

		/** @var ShipmentGls $shipment */
		if (!$shipment = $this['shipmentgls']->findDomesticParcelNumberNl($domestic_parcel_number_nl)) {
			throw new HttpException(404, sprintf('Verzending nr %d niet gevonden.', $domestic_parcel_number_nl));
		}

		/** @var User $user */
		$user = $this['users']->get();
		if (!$user->hasPermission('manage_devos') && $shipment->getKlantnummer() != $user['klantnummer']) {
			throw new HttpException(403, 'Geen rechten om deze verzending te bekijken');
		}

		if (!$png_string = $this['gls']->pngLabel($shipment)) {
			throw new HttpException(400, sprintf('Fout in PNG verzending %d.', $domestic_parcel_number_nl));
		}

		return $this['response']->file($png_string, 200, [], false, 'inline');

	}


	public function pdfShipmentGlsAction ($domestic_parcel_number_nl, $string = false) {

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
        if (empty($data['date_of_shipping'])) {
            $data['date_of_shipping'] = (new \DateTime())->format('Y-m-d H:i:s');
        }

        /** @var User $user */
		if (!$this['admin']) {
			$user = $this['users']->get();
			if (!$user['klantnummer']) {
				throw new HttpException(403, 'Geen klantnummer bekend');
			}

			$data['klantnummer'] = $user['klantnummer'];
			$data['gls_customer_number'] = $user['gls_customer_number'] ?: $this['config']['gls_customer_number'];

            if (empty($data['id']) && $user['mail_admin_onparcel'] != 0) {
                $maildata = $this['mail']->getMaildata($this['config']['parcel_mail']);
                $maildata['subject'] = sprintf('Pakket aangemeld door %s namens %s', $user->getName(), $data['sender_name_1'] );
                $maildata['body'] = $this['view']->render('views/mail/parcel_sent.php', ['data' => $data, 'user' => $user]);
                $this['mail']->sendMail($maildata);
            }
		}

		if ($data = $this['shipmentgls']->save($data)) {
			return $data;
		}
		return false;
	}

	/**
	 * @param ShipmentGls $shipment
	 * @return ShipmentGls
	 * @throws \Exception
	 */
	protected function sendShipment (ShipmentGls $shipment) {

		$existingGls = $shipment->getGlsParcelNumber();
		if (!empty($existingGls)) {
			throw new \Exception(sprintf('Verzending heeft al een GLS nummer: %s.', $existingGls));
		}

		$this['gls']->createShipment($shipment);

		$shipment['track_trace'] = $this['gls']->getTrackTrace($shipment);

		$this->app['shipmentgls']->save($shipment->toArray());

		return $shipment;
	}

	/**
	 * @param array $data
	 * @return ShipmentGls|bool
	 * @throws \Exception
	 */
	protected function createShipment ($data) {
		if ($data = $this->saveShipment($data)) {

			/** @var ShipmentGls $shipment */
			if (!$shipment = $this['shipmentgls']->find($data['id'])) {
				throw new \Exception(sprintf('Verzending id %d niet gevonden.', $data['id']));
			}

			$shipment = $this->sendShipment($shipment);

			/** @var Sender $sender */
			if (!$sender = $this['sender']->find($shipment->getSenderId())) {
				throw new \Exception(sprintf('Verzender id %d niet gevonden.', $shipment->getSenderId()));
			}
			$this['gls']->createLabel($shipment, $sender);

			$this->app['shipmentgls']->save($shipment->toArray());

			return $shipment;
		}
		return false;
	}

	/**
	 * @param $filter
	 * @return Query
	 */
	protected function getQuery ($filter) {
		$query = Query::query('@dv_shipment_gls', '*');
		$filter = array_merge(array_fill_keys([
			'search', 'state', 'gls_customer_number', 'klantnummer', 'sender_id', 'created_from', 'created_to', 'order', 'dir', 'limit'
		], ''), $filter);

		/**
		 * @var string $search
		 * @var string $klantnummer
		 * @var int    $sender_id
		 * @var string $gls_customer_number
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
			'gls_parcel_number', 'domestic_parcel_number_nl', 'sender_name_1', 'customer_reference',
			'receiver_name_1', 'receiver_zip_code', 'receiver_street', 'receiver_place', 'receiver_email'
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

		if ($gls_customer_number) {
			$query->where('gls_customer_number = :gls_customer_number', ['gls_customer_number' => $gls_customer_number]);
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
			'gls_parcel_number', 'sender_name_1', 'receiver_zip_code', 'modified', 'created'
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
			array('/api/shipment', 'indexGlsAction', 'GET', array('access' => 'client_devos')),
			array('/api/shipment/csv', 'csvGlsAction', 'GET', array('access' => 'client_devos')),
			array('/api/shipment/:id', 'getShipmentGlsAction', 'GET', array('access' => 'client_devos')),
			array('/api/shipment/save', 'saveShipmentGlsAction', 'POST', array('access' => 'client_devos')),
			array('/api/shipment/send/:id', 'sendShipmentGlsAction', 'POST', array('access' => 'client_devos')),
			array('/api/shipment/label/:id', 'labelShipmentGlsAction', 'POST', array('access' => 'client_devos')),
			array('/api/shipment/create', 'createShipmentGlsAction', 'POST', array('access' => 'client_devos')),
			array('/api/shipment/createbulk', 'createBulkShipmentGlsAction', 'POST', array('access' => 'client_devos')),
			array('/api/shipment/png/:domestic_parcel_number_nl', 'labelPngShipmentGlsAction', 'GET', array('access' => 'client_devos')),
			array('/api/shipment/pdf/:domestic_parcel_number_nl', 'pdfShipmentGlsAction', 'GET', array('access' => 'client_devos')),
			array('/api/shipment/:id', 'deleteContentAction', 'DELETE', array('access' => 'client_devos')),
			array('/api/shipment/postcode', 'postcodeCheckAction', 'POST', array('access' => 'client_devos'))
		);
	}

}
