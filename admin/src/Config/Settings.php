<?php

namespace Bixie\Devos\Config;

use YOOtheme\Framework\Config\Config;

class Settings extends Config
{
    /**
     * Constructor.
     *
     * @param $db
     * @param $configkey
     */
    public function __construct($db, $configkey)
    {
        $self = $this;
        $row  = $db->fetchAssoc("SELECT data FROM @dv_settings WHERE configkey = :configkey LIMIT 1", compact('configkey'));

        parent::__construct(json_decode($row['data'], true) ?: []);

        register_shutdown_function(function () use ($self, $db, $row, $configkey) {
            if (($data = (string) $self) != $row['data']) {
                $db->update('@dv_settings', compact('data'), compact('configkey'));
            }
        });
    }
}
