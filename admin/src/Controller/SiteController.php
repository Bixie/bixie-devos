<?php

namespace Bixie\Devos\Controller;

use YOOtheme\Framework\Routing\Controller;
use YOOtheme\Framework\Routing\Exception\HttpException;

class SiteController extends Controller {

	public function indexAction () {

		$data = [
		];
		$this['scripts']->add('devos-data', sprintf('var $data = %s;', json_encode($data)), '', 'string');

		return $this['view']->render('views/dashboard.php', $data);
	}

	public static function getRoutes () {
		return array(
			array('/dashboard', 'indexAction', 'GET', array('access' => 'client_devos'))
		);
	}
}
