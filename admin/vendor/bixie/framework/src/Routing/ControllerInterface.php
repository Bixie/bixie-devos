<?php

namespace Bixie\Framework\Routing;

interface ControllerInterface
{
    /**
     * Returns an array of route mappings.
     *
     * @return array
     */
    public static function getRoutes();
}
