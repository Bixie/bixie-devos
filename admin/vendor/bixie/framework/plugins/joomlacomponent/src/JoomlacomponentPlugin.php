<?php

namespace Bixie\Framework\Joomlacomponent;

use Bixie\Framework\Routing\BinaryFileResponse;
use Bixie\Framework\Routing\StreamedResponse;
use JAdministratorHelper, JDocument, JRequest;
use YOOtheme\Framework\Application;
use YOOtheme\Framework\Plugin\Plugin;
use YOOtheme\Framework\Routing\JsonResponse;
use YOOtheme\Framework\Routing\RawResponse;

class JoomlacomponentPlugin extends Plugin
{
    /**
     * {@inheritdoc}
     */
    public function main(Application $app)
    {

        $app->on('boot', array($this, 'boot'));
    }

    /**
     * Callback for 'boot' event.
     */
    public function boot($event, $app)
    {

        if (isset($app['component'])) {
            $this->registerComponent($app);
        }

        // has "onAfterInitialise" been fired? 'lib_joomla' is being set after the event
        if ($app['joomla.language']->getPaths('lib_joomla')) {
            $this->init();
        } else {
            $app['joomla']->registerEvent('onAfterRoute', array($this, 'init'));
        }

        // using onBeforeCompileHead as onBeforeRender is triggered too early on some circumstances
        $app['joomla']->registerEvent('onBeforeCompileHead', function () use ($app) {
            $app->trigger('view', array($app));
        });
    }

    /**
     * Callback to initialize app.
     */
    public function init()
    {
        $this['plugins']->load();
        $this->app->trigger('init', array($this->app));
    }


	/**
	 * Registers Joomla component integration.
	 * @param Application $app
	 */
    protected function registerComponent(Application $app)
    {
        $app['joomla']->registerEvent('onAfterDispatch', function () use ($app) {

            if ($app['component'] !== ($app['admin'] ? JAdministratorHelper::findOption() : $app['joomla']->input->get('option'))) {
                return;
            }

            $response = $app->handle(null, false);

            if ($response->getStatus() != 200) {
                $app['joomla']->setHeader('status', $response->getStatus());
            }

            if ($response instanceof JsonResponse) {
                JRequest::setVar('format', 'json');
                $app['joomla']->loadDocument(JDocument::getInstance('json')->setBuffer((string) $response));
            } elseif ($response instanceof BinaryFileResponse || $response instanceof StreamedResponse ) {
                JRequest::setVar('format', 'raw');
                $app['joomla']->loadDocument(JDocument::getInstance('file')
					->setMimeEncoding($response->headers->get('Content-Type'))
					->setBuffer($response->send()));
            } elseif ($response instanceof RawResponse) {
                JRequest::setVar('format', 'raw');
                $app['joomla']->loadDocument(JDocument::getInstance('raw')->setBuffer((string) $response));
            } else {
                $app['joomla.document']->setBuffer((string) $response, 'component');
            }

        });
    }
}
