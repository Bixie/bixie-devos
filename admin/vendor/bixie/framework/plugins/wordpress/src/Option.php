<?php

namespace Bixie\Framework\Wordpress;

use Bixie\Framework\Config\Config;

class Option extends Config
{
    /**
     * Constructor.
     *
     * @param string $name
     */
    public function __construct($name)
    {
        parent::__construct(get_option($name, array()));

        $self = $this;

        add_action('shutdown', function () use ($self, $name) {
            update_option($name, $self->toArray());
        });
    }
}
