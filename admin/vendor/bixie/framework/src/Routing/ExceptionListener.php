<?php

namespace Bixie\Framework\Routing;

use Bixie\Framework\Application;
use Bixie\Framework\Event\Event;
use Bixie\Framework\Event\EventSubscriberInterface;

class ExceptionListener implements EventSubscriberInterface
{
    /**
     * Converts an exception to a response.
     *
     * @param Event       $event
     * @param Application $app
     */
    public function exceptionToResponse($event, $app)
    {
        $request = $event['request'];
        $e       = $event['exception'];

        if (stripos($request->headers->get('accept'), 'application/json') !== false) {
            $event['response'] = $app['response']->json(array('message' => $e->getMessage(), 'code' => $e->getCode(), 'error' => true));
        } else {
            $event['response'] = $app['response']->create($e->getMessage());
        }
    }

    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents()
    {
        return array(
            'exception' => 'exceptionToResponse'
        );
    }
}
