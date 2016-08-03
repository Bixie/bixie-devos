<?php

namespace Bixie\SendCloud\Carriers\SendCloud;

use Bixie\SendCloud\Carriers\SendCloud\Query\FindAllStatic;

class ShippingMethod extends \Picqer\Carriers\SendCloud\ShippingMethod implements \JsonSerializable
{

    use JsonSerializableTrait, FindAllStatic;

}