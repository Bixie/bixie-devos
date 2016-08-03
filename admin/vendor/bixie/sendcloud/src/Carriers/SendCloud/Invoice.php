<?php

namespace Bixie\SendCloud\Carriers\SendCloud;


use Bixie\SendCloud\Carriers\SendCloud\Query\FindAllStatic;
use Bixie\SendCloud\Carriers\SendCloud\Query\FindOneStatic;

class Invoice extends \Picqer\Carriers\SendCloud\Invoice implements \JsonSerializable {

    use JsonSerializableTrait, FindOneStatic, FindAllStatic;


}