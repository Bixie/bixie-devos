<?php

use Bixie\Devos\Application;

global $bix_devos;

if ($bix_devos) {
    return $bix_devos;
}

$loader = require __DIR__ . '/vendor/autoload.php';
$config = require __DIR__ . '/config.php';

$app = new Application($config);
$app['autoloader']  = $loader;
$app['path.cache']  = rtrim(JPATH_SITE, '/').'/media/bix_devos';
$app['path.pdf']  = dirname(JPATH_ROOT) . '/pdf';
$app['component']   = 'com_'.$app['name'];
$app['permissions'] = [
	'devos.client' => 'client_devos',
	'core.manage' => 'manage_devos'
];
$app['template']   = function () {
	$db = JFactory::getDbo();
	$db->setQuery( 'SELECT id,template FROM #__template_styles WHERE client_id=0 AND home=1');
	$template = $db->loadObject()->template;
	return file_exists($path = rtrim(JPATH_ROOT, '/')."/templates/".$template."/warp.php") ? $path : false;
};

$app->on('init', function ($event, $app) {
    
    $controller = JFactory::getApplication()->input->get('controller');
    $option = JFactory::getApplication()->input->get('option');

    if ($option == 'com_config' && $controller == 'config.display.modules') {
       // $app['scripts']->add('widgetkit-joomla', 'assets/js/joomla.js', array('widgetkit-application'));
    }

    if ($app['admin'] && $app['component'] == JAdministratorHelper::findOption()) {
        $app->trigger('init.admin', array($app));
    }

    if ($app['request']->get('option') != 'com_installer') {

        $app['config']->add($app['config.fetch']);
    }
});

$app->on('init.admin', function ($event, $app) {
    JHtmlBehavior::keepalive();
    JHtml::_('jquery.framework');

    $app['scripts']->add('uikit');

}, 10);

$app->on('view', function ($event, $app) {
//    $app['config']->set('theme.support', $app['joomla.config']->get('widgetkit'));
});

$app->boot();

return $bix_devos = $app;
