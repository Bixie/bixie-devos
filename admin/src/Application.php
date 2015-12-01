<?php

namespace Bixie\Devos;

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

//        $this['content']   = new ContentProvider($this);

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

        // combine assets
        if (!$this['debug']) {
//            $this['styles']->combine('wk-styles', 'widgetkit-*' , array('CssImportResolver', 'CssRewriteUrl', 'CssImageBase64'));
//            $this['scripts']->combine('wk-scripts', 'widgetkit-*')->combine('uikit', 'uikit*')->combine('angular', 'angular*')->combine('application', 'application{,-translator,-templates}');
        }

        // site event
        if (!$this['admin']) {
            $this->trigger('init.site', array($this));
        }
    }

    public function initSite()
    {
        // scripts
//        $this['scripts']->register('uikit', 'vendor/assets/uikit/js/uikit.min.js');
    }

    public function initAdmin()
    {
        // widgetkit
//        $this['styles']->add('widgetkit-admin', 'assets/css/admin.css');
//        $this['scripts']->add('widgetkit-fields', 'assets/js/fields.js', array('angular'));
//        $this['scripts']->add('widgetkit-application', 'assets/js/application.js', array('uikit', 'uikit-notify', 'uikit-nestable', 'uikit-sortable', 'application-translator', 'angular-resource', 'widgetkit-fields'));
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
