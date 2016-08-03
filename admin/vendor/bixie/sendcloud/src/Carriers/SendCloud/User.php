<?php

namespace Bixie\SendCloud\Carriers\SendCloud;


use Bixie\SendCloud\Carriers\SendCloud\Query\FindOneStatic;

class User extends \Picqer\Carriers\SendCloud\User implements \JsonSerializable
{

    use JsonSerializableTrait, FindOneStatic;

}