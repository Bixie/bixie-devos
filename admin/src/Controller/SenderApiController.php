<?php

namespace Bixie\Devos\Controller;

use Bixie\Devos\Model\Sender\Sender;
use Bixie\Framework\Utils\Query;
use YOOtheme\Framework\Routing\Controller;
use YOOtheme\Framework\Routing\Exception\HttpException;

class SenderApiController extends Controller {

	public function sendersAction ($inactive = false) {
		$return = new \ArrayObject;

		$query = Query::query('@dv_sender', '*');
		$query->where('user_id = :user_id', ['user_id' => $this['users']->get()->getId()]);
		if (!$inactive) $query->where('state = 1');
		$query->orderBy('sender_name_1', 'ASC');
		$return['senders'] = $this['sender']->query((string) $query, $query->getParams(), 0, 0);

		return $this['response']->json($return);

	}

	public function saveSenderAction ($data) {
		$status = !isset($data['id']) || !$data['id'] ? 201 : 200;
		$return = new \ArrayObject;

		$data['user_id'] = $this['users']->get()->getId();

		if ($data = $this['sender']->save($data)) {
			$return['sender'] = $data;

			return $this['response']->json($return, $status);
		}

		throw new HttpException(400);
	}

	public function setdefaultSenderAction ($id) {

		$user = $this['users']->get();

		if ($object = $this['sender']->find($id)) {

			if ($object->getUserId() != $user->getId()) {
				throw new HttpException(403, 'Geen toegang voor deze gebruiker');
			}

			$this['sender']->setDefault($id, $user->getId());


			return $this->sendersAction(true);

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
			array('/api/sender/setdefault', 'setdefaultSenderAction', 'POST', array('access' => 'client_devos')),
			array('/api/sender/:id', 'deleteSenderAction', 'DELETE', array('access' => 'client_devos'))
		);
	}
}
