<?php

namespace Bixie\Devos;

use Bixie\Devos\Model\Sender\SenderProvider;
use Bixie\Devos\Model\Shipment\ShipmentGlsProvider;
use Bixie\Devos\Config\Settings;
use Bixie\Framework\Routing\ResponseListener;
use Bixie\Framework\Routing\ResponseProvider;
use Bixie\Framework\User\User;
use Bixie\Devos\User\UserProvider;
use Bixie\Gls\Gls;
use YOOtheme\Framework\Application as BaseApplication;
use YOOtheme\Framework\Event\EventSubscriberInterface;

class Application extends BaseApplication implements EventSubscriberInterface
{

    /**
     * Constructor.
     *
     * @param array $values
     */
    public function __construct(array $values = array())
    {
        parent::__construct($values);

		$this['plugins']->addPath($this['path'].'/plugins/*/plugin.php');
		$this['plugins']->addPath($this['path.vendor'].'/bixie/framework/plugins/*/plugin.php');

		//configstorage
		$this['settings'] = function ($app) {
			return new Settings($app['db'], 'config');
		};
		$this['config.fetch'] = function ($app) {
			return $app['settings']->toArray();
		};
		$this['config.save'] = function ($app) {
			$app['settings']->add($app['config']->toArray());
			return $app['config'];
		};

		//providers
		$this['shipmentgls']   = new ShipmentGlsProvider($this);
		$this['sender']   = new SenderProvider($this);

		$this->extend('locator', function ($locator, $app) {
            return $locator->addPath('', $app['path']);
        });

        $this->on('boot', function ($event, $app) {

			$app['response'] = function ($app) {
				return new ResponseProvider($app['url']);
			};

		});

		$this['events']->subscribe($this);
        $this['events']->subscribe(new ResponseListener);
    }

    public function init()
    {
        // controller
        $this['controllers']->add('Bixie\Devos\Controller\DashboardController');
        $this['controllers']->add('Bixie\Devos\Controller\ShipmentController');
        $this['controllers']->add('Bixie\Devos\Controller\SiteController');
        $this['controllers']->add('Bixie\Devos\Controller\SenderApiController');
        $this['controllers']->add('Bixie\Devos\Controller\ShipmentApiController');


        // combine assets
        if (!$this['debug']) {
//            $this['styles']->combine('wk-styles', 'widgetkit-*' , array('CssImportResolver', 'CssRewriteUrl', 'CssImageBase64'));
//            $this['scripts']->combine('wk-scripts', 'widgetkit-*')->combine('uikit', 'uikit*')->combine('angular', 'angular*')->combine('application', 'application{,-translator,-templates}');
        }

		//gls
		$this['gls'] = new Gls($this);
		//override userprovider
		$this['users'] = function ($app) {
			return new UserProvider($app, $app['component'], isset($app['permissions']) ? $app['permissions'] : array());
		};

		/** @var User $user */
		$user = $this['users']->get();
		$config = [
			'csrf' =>  $this['csrf']->generate(),
			'locale' => 'nl-NL',
			'user' => [
				'id' => $user->getId(),
				'username' => $user->getUsername(),
				'name' => $user->getName(),
				'bedrijfsnaam' => $user['bedrijfsnaam'],
				'gls_customer_number' => $user['gls_customer_number'] ? : $this['config']['gls_customer_number'],
				'klantnummer' => $user['klantnummer']
			],
			'current' => \JUri::current(),
			'url' => 'index.php'
		];
		$this['scripts']->add('devos-config', sprintf('var $config = %s;', json_encode($config)), '', 'string');

		$this['scripts']->register('vue', 'assets/js/vue.js');
		$this['scripts']->register('uikit', 'vendor/assets/uikit/js/uikit.min.js');
		$this['scripts']->register('uikit-tooltip', 'vendor/assets/uikit/js/components/tooltip.min.js', ['uikit']);
		$this['scripts']->register('uikit-notify', 'vendor/assets/uikit/js/components/notify.min.js', ['uikit']);
		$this['scripts']->register('uikit-upload', 'vendor/assets/uikit/js/components/upload.min.js', ['uikit']);
		$this['scripts']->register('uikit-pagination', 'vendor/assets/uikit/js/components/pagination.min.js', ['uikit']);

		// site event
        if (!$this['admin']) {
            $this->trigger('init.site', array($this));
        }
    }

    public function initSite()
    {
        // scripts
		$this['joomla']->set('uikit', true);
		$this['scripts']->add('vue', 'assets/js/vue.js', ['uikit-tooltip', 'uikit-notify']);
    }

    public function initAdmin()
    {
		$this['styles']->add('devos-admin', 'assets/css/admin.css');

        $this['scripts']->add('devos-admin-dashboard', 'assets/js/admin-dashboard.js', array('vue'));
    }

    public static function getSubscribedEvents()
    {
        return array(
            'init'        => array('init', -5),
            'init.site'   => 'initSite',
            'init.admin'  => 'initAdmin',
//            'view.render' => 'viewRender'
        );
    }
}
