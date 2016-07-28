<?php

namespace Bixie\Framework\Plugin;

use Bixie\Framework\Application;

interface PluginInterface
{
    /**
     * Main bootstrap method.
     *
     * @param Application $app
     */
    public function main(Application $app);
}
