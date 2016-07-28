<?php

namespace Bixie\Framework;

use Bixie\Framework\Config\Config;
use Bixie\Framework\Csrf\DefaultCsrfProvider;
use Bixie\Framework\Event\Event;
use Bixie\Framework\Event\EventDispatcher;
use Bixie\Framework\Filter\FilterManager;
use Bixie\Framework\Plugin\PluginManager;
use Bixie\Framework\Plugin\Loader\EventLoader;
use Bixie\Framework\Resource\ResourceLocator;
use Bixie\Framework\Routing\ControllerCollection;
use Bixie\Framework\Routing\ExceptionListener;
use Bixie\Framework\Routing\Exception\HttpExceptionInterface;
use Bixie\Framework\Routing\Response;
use Bixie\Framework\Routing\ResponseListener;
use Bixie\Framework\Routing\Request;
use Bixie\Framework\Routing\Route;
use Bixie\Framework\Routing\Router;
use Bixie\Framework\Routing\RouterListener;
use Bixie\Framework\Translation\Translator;
use Bixie\Framework\User\AccessListener;

class Application extends Container
{
    /**
     * @var bool
     */
    protected $booted = false;

    /**
     * Constructor.
     *
     * @param array $values
     */
    public function __construct(array $values = array())
    {
        parent::__construct();

        $this['events'] = function() {

            $events = new EventDispatcher;
            $events->subscribe(new AccessListener);
            $events->subscribe(new ExceptionListener);
            $events->subscribe(new ResponseListener);
            $events->subscribe(new RouterListener);

            return $events;
        };

        $this['plugins'] = function($app) {

            $manager = new PluginManager($app);
            $manager->addLoader(new EventLoader($app));
            $manager->addPath($app['path.vendor'].'/yootheme/framework/plugins/*/plugin.php');

            return $manager;
        };

        $this['router'] = function($app) {
            return new Router($app['controllers']);
        };

        $this['controllers'] = function($app) {
            return new ControllerCollection($app);
        };

        $this['csrf'] = function() {
            return new DefaultCsrfProvider();
        };

        $this['locator'] = function() {
            return new ResourceLocator();
        };

        $this['config'] = function() {
            return new Config();
        };

        $this['user'] = function($app) {
            return $app['users']->get();
        };

        $this['filter'] = function() {
            return new FilterManager();
        };

        $this['translator'] = function($app) {

            $translator = new Translator($app['locator']);

            if (isset($app['locale'])) {
                $translator->setLocale($app['locale']);
            }

            return $translator;
        };

        $values = array_replace(array(
            'app' => $this,
            'debug' => false,
            'version' => null),
        $values);

        foreach ($values as $key => $value) {
            $this[$key] = $value;
        }
    }

    /**
     * Adds an event listener.
     *
     * @param string   $event
     * @param callable $listener
     * @param int      $priority
     */
    public function on($event, $listener, $priority = 0)
    {
        $this['events']->on($event, $listener, $priority);
    }

    /**
     * Triggers an event.
     *
     * @param  string $event
     * @param  array  $arguments
     * @return Event
     */
    public function trigger($event, array $arguments = array())
    {
        return $this['events']->trigger($event, $arguments);
    }

    /**
     * Loads all plugins and triggers 'boot' event.
     *
     * @return self
     */
    public function boot()
    {
        if ($this->booted) {
            return;
        }

        $this->booted = true;

        $this['plugins']->load();
        $this['events']->trigger('boot', array($this));

        return $this;
    }

    /**
     * Handles a request and converts it to a response.
     *
     * @param  Request $request
     * @param  bool    $send
     * @return Response|null
     */
    public function handle(Request $request = null, $send = true)
    {
        $request = $request ?: $this['request'];

        try {
            $response = $this->handleRaw($request);
        } catch (\Exception $e) {
            $response = $this->handleException($e, $request);
        }

        return $send ? $response->send() : $response;
    }

    /**
     * @param  Request $request
     * @return null|Response
     */
    protected function handleRaw(Request $request)
    {
        $response = null;
        $event    = $this['events']->trigger(new Event('request', compact('request')), array($this));

        if (isset($event['response'])) {

            $response = $event['response'];

        } else {

            $callable = $request->attributes->get('_callable');
            $response = call_user_func_array($callable, $this['controllers']->getArguments($request, $callable));
            $event    = $this['events']->trigger(new Event('response', compact('response', 'request')), array($this));

            if (isset($event['response'])) {
                $response = $event['response'];
            }
        }

        if (!$response instanceof Response) {
            throw new \LogicException('Response must be of type Bixie\Framework\Routing\Response.');
        }

        return $response;
    }

    /**
     * Handles an exception by trying to convert it to a Response.
     *
     * @param  \Exception $exception
     * @param  Request    $request
     * @throws \Exception
     * @return Response
     */
    protected function handleException(\Exception $exception, $request)
    {
        $event = $this['events']->trigger(new Event('exception', compact('exception', 'request')), array($this));
        $exception = $event['exception'];

        if (!isset($event['response'])) {
            throw $exception;
        }

        $response = $event['response'];

        if ($exception instanceof HttpExceptionInterface) {
            $response->setStatus($exception->getStatus());
        } else {
            $response->setStatus(500);
        }

        return $response;
    }
}
