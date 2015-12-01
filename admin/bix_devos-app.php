<?php

defined('_JEXEC') or die;

if ($component = JComponentHelper::getComponent('com_bix_devos', true) and $component->enabled) {
    return include(__DIR__ . '/bix_devos.php');
}

return false;