<?php

namespace Bixie\Devos;

use Bixie\Devos\Model\Sender\SenderProvider;
use Bixie\Devos\Model\Shipment\ShipmentGlsProvider;
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

        $this['shipmentgls']   = new ShipmentGlsProvider($this);
        $this['sender']   = new SenderProvider($this);

        $this->extend('locator', function ($locator, $app) {
            return $locator->addPath('', $app['path']);
        });

        $this->on('boot', function ($event, $app) {

            $app['plugins']->addPath($app['path'].'/plugins/*/*/plugin.php');

		});

        $this['events']->subscribe($this);
    }

    public function init()
    {
        // controller
        $this['controllers']->add('Bixie\Devos\Controller\DashboardController');
        $this['controllers']->add('Bixie\Devos\Controller\ShipmentController');
        $this['controllers']->add('Bixie\Devos\Controller\SiteController');
        $this['controllers']->add('Bixie\Devos\Controller\SiteApiController');


        // combine assets
        if (!$this['debug']) {
//            $this['styles']->combine('wk-styles', 'widgetkit-*' , array('CssImportResolver', 'CssRewriteUrl', 'CssImageBase64'));
//            $this['scripts']->combine('wk-scripts', 'widgetkit-*')->combine('uikit', 'uikit*')->combine('angular', 'angular*')->combine('application', 'application{,-translator,-templates}');
        }

		//gls
		$this['gls'] = new Gls($this);

		$config = [
			'csrf' =>  $this['csrf']->generate(),
			'locale' => 'nl-NL',
			'current' => \JUri::current(),
			'url' => 'index.php'
		];
		$this['scripts']->add('devos-config', sprintf('var $config = %s;', json_encode($config)), '', 'string');

		//todo minify options
		$this['scripts']->register('vue', 'assets/js/vue.js');
		$this['scripts']->register('uikit', 'vendor/assets/uikit/js/uikit.js');
		$this['scripts']->register('uikit-tooltip', 'vendor/assets/uikit/js/components/tooltip.js', ['uikit']);
		$this['scripts']->register('uikit-notify', 'vendor/assets/uikit/js/components/notify.js', ['uikit']);
		$this['scripts']->register('uikit-upload', 'vendor/assets/uikit/js/components/upload.js', ['uikit']);

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
