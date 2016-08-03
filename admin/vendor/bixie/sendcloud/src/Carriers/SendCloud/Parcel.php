<?php

namespace Bixie\SendCloud\Carriers\SendCloud;


use Bixie\Devos\Model\Shipment\ShipmentSendCloud;
use Bixie\SendCloud\Carriers\SendCloud\Query\FindOneStatic;

class Parcel extends \Picqer\Carriers\SendCloud\Parcel implements \JsonSerializable
{

    use JsonSerializableTrait, FindOneStatic;

    public function setParcelData (ShipmentSendCloud &$shipment) {
        $shipment->setSendcloudId($this->id);
        $shipment->setTrackingNumber($this->tracking_number);
    }
}
