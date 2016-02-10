<?php

$config = array(

    'name' => 'bixie/joomlacomponent',

    'main' => 'Bixie\\Framework\\Joomlacomponent\\JoomlacomponentPlugin',

    'autoload' => array(

        'Bixie\\Framework\\Joomlacomponent\\' => 'src'

    )

);

return defined('_JEXEC') ? $config : false;