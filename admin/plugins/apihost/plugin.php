<?php

$config = array(

    'name' => 'bixie/apihost',

    'main' => 'Bixie\\Devos\\Apihost\\ApihostPlugin',

    'autoload' => array(

        'Bixie\\Devos\\Apihost\\' => 'src'

    )

);

return defined('_JEXEC') ? $config : false;