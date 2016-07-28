<?php

namespace Bixie\Framework\Wordpress;

use Bixie\Framework\Csrf\DefaultCsrfProvider;

class CsrfProvider extends DefaultCsrfProvider
{
    /**
     * {@inheritdoc}
     */
    public function generate()
    {
        return wp_create_nonce();
    }
}
