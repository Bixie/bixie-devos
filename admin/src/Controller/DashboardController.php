<?php

namespace Bixie\Devos\Controller;

use Bixie\Gls\GlsException;
use YOOtheme\Framework\Routing\Controller;
use YOOtheme\Framework\Routing\Exception\HttpException;

class DashboardController extends Controller {

	public function indexAction () {

		\JToolBarHelper::title('De Vos diensten beheer', 'bix-devos');

		if ($this['user']->hasPermission('manage_devos')) {
			\JToolBarHelper::preferences('com_bix_devos');
		}

		$shipment = $this['shipmentgls']->find(3);
		$label = new \Bixie\Gls\Data\Label($shipment);

		$data = [
			'label' => $label->getTemplateContents()
		];
		$this['scripts']->add('devos-data', sprintf('var $data = %s;', json_encode($data)), '', 'string');

		return $this['view']->render('views/admin/dashboard.php', $data);
	}

	public static function getRoutes () {
		return array(
			array('index', 'indexAction', 'GET', array('access' => 'manage_devos'))
		);
	}
}
