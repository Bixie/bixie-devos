<?php

$config = array(

    'name' => 'framework/wordpress',

    'main' => 'Bixie\\Framework\\Wordpress\\WordpressPlugin',

    'autoload' => array(

        'Bixie\\Framework\\Wordpress\\' => 'src'

    )

);

return defined('WPINC') ? $config : false;