<?php

namespace Bixie\Devos\Controller;

use Bixie\Devos\Model\GlsTracking\GlsTracking;
use YOOtheme\Framework\Routing\Controller;

class GlsTrackingController extends Controller {

	public function glsTrackingAction () {

		\JToolbarHelper::title('De Vos diensten beheer - GLS Tracking', 'bix-devos');

		if ($this['user']->hasPermission('manage_devos')) {
			\JToolbarHelper::preferences('com_bix_devos');
		}

		$data = [
			'glstracking_states' => GlsTracking::getStates()
		];

		$this['scripts']->add('devos-data', sprintf('var $data = %s;', json_encode($data)), '', 'string');

		return $this['view']->render('views/admin/gls-tracking.php', $data);
	}


	public static function getRoutes () {
		return array(
			array('/gls-tracking', 'glsTrackingAction', 'GET', array('access' => 'manage_devos'))
		);
	}
}
