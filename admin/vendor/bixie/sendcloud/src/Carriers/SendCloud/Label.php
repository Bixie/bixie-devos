<?php

namespace Bixie\SendCloud\Carriers\SendCloud;

use Bixie\SendCloud\Carriers\SendCloud\Query\FindOneStatic;

class Label extends \Picqer\Carriers\SendCloud\Label implements \JsonSerializable
{

    use JsonSerializableTrait, FindOneStatic;

    /**
     * Returns the label content (PDF) in A6 format.
     *
     * @return string
     */
    public function labelZplContent()
    {
        $url = str_replace($this->connection->apiUrl(), '', $this->normal_printer);

        return $this->connection->download($url[0]);
    }

}
