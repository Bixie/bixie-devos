<?php

namespace Bixie\Framework\Routing;

use Bixie\Framework\Application;
use Bixie\Framework\Event\Event;
use Bixie\Framework\Event\EventSubscriberInterface;

class ResponseListener implements EventSubscriberInterface
{
    /**
     * Converts a string to a response.
     *
     * @param Event       $event
     * @param Application $app
     */
    public function stringToResponse($event, $app)
    {
        $response = $event['response'];

		if ($response && is_string($response)) {

			$event['response'] = $app['response']->create($response);
			$event->stopPropagation();
		}

        if (null !== $response && $response instanceof BinaryFileResponse) {

            $event['response'] = $response->prepare($event['request']);
            $event->stopPropagation();
        }
    }

    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents()
    {
        return array(
            'response' => array('stringToResponse', -20)
        );
    }
}
