<?php

namespace Bixie\Devos\Controller;

use Bixie\Devos\Model\Sender\Sender;
use Bixie\Framework\Utils\Query;
use YOOtheme\Framework\Routing\Controller;
use YOOtheme\Framework\Routing\Exception\HttpException;

class SiteApiController extends Controller {

	public function sendersAction ($page = 0) {
		$return = new \ArrayObject;


		$query = Query::query('@dv_sender', '*');
		$query->where('user_id = :user_id', ['user_id' => $this['users']->get()->getId()]);
		$query->where('state = 1');
		$start = 0;
		$limit = 0;
		$return['page'] = $page;
//		$return['total'] = $this['db']->count((string) $query, $query->getParams());
		$return['senders'] = $this['sender']->query((string) $query, $query->getParams(), $start, $limit);

		return $this['response']->json($return);

	}

	public function getSenderAction ($id) {
		if ($id == 0) {
			$object = Sender::create([
				'user_id' => $this['users']->get()->getId(),
				'sender_country' => 'NL'
			]);
			return $this['response']->json($object);
		}
		if ($object = $this['sender']->find($id)) {
			return $this['response']->json($object);
		}

		throw new HttpException(404);
	}

	public function saveSenderAction ($data) {
		$status = !isset($data['id']) || !$data['id'] ? 201 : 200;
		$return = new \ArrayObject;

		$user = $this['users']->get();
		if (!$user->hasPermission('manage_devos')) {
			$data['user_id'] = $user->getId();
		}

		if ($data = $this['sender']->save($data)) {
			$return['sender'] = $data;

			return $this['response']->json($return, $status);
		}

		throw new HttpException(400);
	}

	public function deleteSenderAction ($id) {
		if ($this['sender']->delete($id)) {
			return $this['response']->json(null, 204);
		}
		throw new HttpException(400);
	}


	public static function getRoutes () {
		return array(
			array('/api/sender', 'sendersAction', 'GET', array('access' => 'client_devos')),
			array('/api/sender/:id', 'getSenderAction', 'GET', array('access' => 'client_devos')),
			array('/api/sender/save', 'saveSenderAction', 'POST', array('access' => 'client_devos')),
			array('/api/sender/:id', 'deleteSenderAction', 'DELETE', array('access' => 'client_devos'))
		);
	}
}
