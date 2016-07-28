<?php

namespace Bixie\Devos\Controller;

use Bixie\Framework\Utils\Query;
use Bixie\Framework\Routing\Controller;
use Bixie\Framework\Routing\Exception\HttpException;

class GlsTrackingApiController extends Controller {
	/**
	 * @param array $filter
	 * @param int   $page
	 * @return mixed
	 */
	public function glsTrackingsAction ($filter = [], $page = 0) {
		$return = new \ArrayObject;
		$query = Query::query('@dv_gls_tracking', '*');
		$filter = array_merge(array_fill_keys(['state', 'order', 'dir', 'limit'], ''), $filter);

		/**
		 * @var string $state
		 * @var string $order
		 * @var int $dir
		 * @var int $limit
		 */
		extract($filter, EXTR_SKIP);

		if (is_numeric($state)) {
			$query->where('state = :state', ['state' => (int)$state]);
		}

		$order_col = in_array($order, [
			'gls_number', 'date_from', 'filename', 'created'
		]) ? $order : 'created';
		$dir = $dir ?: 'DESC';

		$query->orderBy($order_col, $dir);
		$query->orderBy('date_from', 'desc');

		$limit = (int)$limit ?: 10;
		$return['total'] = $this['glstracking']->count($query);
		$return['pages'] = ceil($return['total'] / $limit);
		$return['page'] = max(0, min($return['pages'] - 1, $page));
		$start = $return['page'] * $limit;

		$return['glstrackings'] = array_values($this['glstracking']->query($query, $start, $limit));

		return $this['response']->json($return);

	}

	/**
	 * @return mixed
	 */
	public function syncGlsTrackingAction () {
		$return = new \ArrayObject;

		$existing_files = $this['glstracking']->lastFilenames();

		$statuses  = $this['gls.status']->getStatusUpdates($existing_files);

		$return['trackings'] = $this['gls.status']->processStatuses($statuses);

		return $this['response']->json($return);
		
	}

	/**
	 * @param $id
	 * @return mixed
	 */
	public function deleteGlsTrackingAction ($id) {
		if ($this['glstracking']->delete($id)) {
			return $this['response']->json(null, 204);
		}
		throw new HttpException(400);
	}

	/**
	 * @return array
	 */
	public static function getRoutes () {
		return array(
			array('/api/glstracking', 'glsTrackingsAction', 'GET', array('access' => 'manage_devos')),
			array('/api/glstracking/sync', 'syncGlsTrackingAction', 'GET', array('access' => 'manage_devos')),
			array('/api/glstracking/:id', 'deleteGlsTrackingAction', 'DELETE', array('access' => 'manage_devos'))
		);
	}
}
