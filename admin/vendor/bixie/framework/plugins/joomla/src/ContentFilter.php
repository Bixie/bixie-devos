<?php

namespace Bixie\Framework\Joomla;

use Bixie\Framework\Filter\FilterInterface;

class ContentFilter implements FilterInterface
{
    /**
     * {@inheritdoc}
     */
    public function filter($value)
    {
        return \JHtmlContent::prepare($value);
    }
}
