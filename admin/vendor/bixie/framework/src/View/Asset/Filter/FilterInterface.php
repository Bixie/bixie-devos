<?php

namespace Bixie\Framework\View\Asset\Filter;

use Bixie\Framework\View\Asset\AssetInterface;

interface FilterInterface
{
    /**
     * Filter content callback.
     *
     * @param AssetInterface $asset
     */
    public function filterContent(AssetInterface $asset);
}
