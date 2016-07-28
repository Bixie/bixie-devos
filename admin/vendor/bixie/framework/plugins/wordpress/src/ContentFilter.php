<?php

namespace Bixie\Framework\Wordpress;

use Bixie\Framework\Filter\FilterInterface;

class ContentFilter implements FilterInterface
{
    /**
     * {@inheritdoc}
     */
    public function filter($value)
    {
        return apply_filters('the_content', $value);
    }
}
