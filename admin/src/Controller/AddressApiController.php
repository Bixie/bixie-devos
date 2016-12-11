<?php

namespace Bixie\Devos\Controller;

use Bixie\Framework\Utils\Query;
use Bixie\Framework\Routing\Controller;
use Bixie\Framework\Routing\Exception\HttpException;

class AddressApiController extends Controller {

    /**
     * @param array $filter
     * @param int   $page
     * @return mixed
     */
    public function addressesAction ($filter = [], $page = 0) {
        $return = [];
        $query = Query::query('@dv_address', '*');

        $filter = array_merge(array_fill_keys(['state', 'search', 'order', 'dir', 'limit'], ''), $filter);

        /**
         * @var string $state
         * @var string $search
         * @var string $order
         * @var int $dir
         * @var int $limit
         */
        extract($filter, EXTR_SKIP);

        if (is_numeric($state)) {
            $query->where('state = :state', ['state' => (int)$state]);
        }

        if ($search) $query->where(sprintf('(%s LIKE :search)', implode(' LIKE :search OR ', [
            'contact', 'zip', 'email', 'additional_text', 'street', 'name_1', 'name_2'])), ['search' => "%{$search}%"]);

        $query->where('user_id = :user_id', ['user_id' => $this['users']->get()->getId()]);

        $order_col = in_array($order, [
            'contact', 'name_1', 'name_2', 'email'
        ]) ? $order : 'name_1';
        $dir = $dir ?: 'ASC';

        $query->orderBy($order_col, $dir);

        $limit = $limit ? (int)$limit : 10;
        $return['total'] = $this['address']->count($query);
        $return['pages'] = ceil($return['total'] / $limit);
        $return['page'] = max(0, min($return['pages'] - 1, $page));
        $start = $return['page'] * $limit;

        $return['addresses'] = $this['address']->query($query, $start, $limit);

        return $this['response']->json($return);

    }

	public function saveAddressAction ($data) {
		$status = !isset($data['id']) || !$data['id'] ? 201 : 200;
		$return = new \ArrayObject;

		$data['user_id'] = $this['users']->get()->getId();

		if ($data = $this['address']->save($data)) {
			$return['address'] = $data;

			return $this['response']->json($return, $status);
		}

		throw new HttpException(400);
	}

	public function deleteAddressAction ($id) {
		if ($this['address']->delete($id)) {
			return $this['response']->json(null, 204);
		}
		throw new HttpException(400);
	}


	public static function getRoutes () {
		return array(
			array('/api/address', 'addressesAction', 'GET', array('access' => 'client_devos')),
			array('/api/address/:id', 'getAddressAction', 'GET', array('access' => 'client_devos')),
			array('/api/address/save', 'saveAddressAction', 'POST', array('access' => 'client_devos')),
			array('/api/address/:id', 'deleteAddressAction', 'DELETE', array('access' => 'client_devos'))
		);
	}
}
