<?php

namespace Bixie\Framework\Routing;

use YOOtheme\Framework\Application;
use YOOtheme\Framework\Event\Event;
use YOOtheme\Framework\Event\EventSubscriberInterface;

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
