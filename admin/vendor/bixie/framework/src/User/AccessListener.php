<?php

namespace Bixie\Framework\User;

use Bixie\Framework\Event\EventSubscriberInterface;
use Bixie\Framework\Routing\Exception\HttpException;

class AccessListener implements EventSubscriberInterface
{
    public function onRequest($event, $app)
    {
        $access = (array) $event['request']->attributes->get('access');

        foreach ($access as $permission) {
            if (!$app['user']->hasPermission($permission)) {
                throw new HttpException(403, 'Insufficient User Rights.');
            }
        }
    }

    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents()
    {
        return array(
            'request' => array('onRequest', -10)
        );
    }
}
