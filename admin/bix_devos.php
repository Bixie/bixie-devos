<?php

use Bixie\Devos\Application;
use YOOtheme\Framework\Joomla\Option;

global $bix_devos;

if ($bix_devos) {
    return $bix_devos;
}

$loader = require __DIR__ . '/vendor/autoload.php';
$config = require __DIR__ . '/config.php';

$app = new Application($config);
$app['autoloader']  = $loader;
$app['path.cache']  = rtrim(JPATH_SITE, '/').'/media/bix_devos';
$app['component']   = 'com_'.$app['name'];
$app['permissions'] = array('core.manage' => 'manage_devos');
$app['option'] = function ($app) {
    return new Option($app['db'], 'com_bix_devos');
};

$app->on('init', function ($event, $app) {
    
    $controller = JFactory::getApplication()->input->get('controller');
    $option = JFactory::getApplication()->input->get('option');

    if ($option == 'com_config' && $controller == 'config.display.modules') {
        $app['scripts']->add('widgetkit-joomla', 'assets/js/joomla.js', array('widgetkit-application'));
    }

    if ($app['admin'] && $app['component'] == JAdministratorHelper::findOption()) {
        $app->trigger('init.admin', array($app));
    }

    if ($app['request']->get('option') != 'com_installer') {
        $app['config']->add(JComponentHelper::getParams($app['component'])->toArray());
    }
});

$app->on('init.admin', function ($event, $app) {
    JHtmlBehavior::keepalive();
    JHtml::_('jquery.framework');


//    $app['styles']->add('widgetkit-joomla', 'assets/css/joomla.css');
//    $app['scripts']->add('widgetkit-joomla', 'assets/js/joomla.js', array('widgetkit-application'));
//    $app['scripts']->add('widgetkit-joomla-media', 'assets/js/joomla.media.js', array('widgetkit-joomla'));
//    $app['scripts']->add('uikit-upload');

}, 10);

$app->on('view', function ($event, $app) {
//    $app['config']->set('theme.support', $app['joomla.config']->get('widgetkit'));
});

$app->boot();

return $bix_devos = $app;
