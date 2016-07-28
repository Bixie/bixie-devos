<?php

$config = array(

    'name' => 'framework/joomla',

    'main' => 'Bixie\\Framework\\Joomla\\JoomlaPlugin',

    'autoload' => array(

        'Bixie\\Framework\\Joomla\\' => 'src'

    )

);

return defined('_JEXEC') ? $config : false;