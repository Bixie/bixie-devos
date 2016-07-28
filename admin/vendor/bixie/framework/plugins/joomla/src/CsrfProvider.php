<?php

namespace Bixie\Framework\Joomla;

use Bixie\Framework\Csrf\DefaultCsrfProvider;

class CsrfProvider extends DefaultCsrfProvider
{
    /**
     * {@inheritdoc}
     */
    public function generate()
    {
        return \JFactory::getSession()->getToken();
    }
}
