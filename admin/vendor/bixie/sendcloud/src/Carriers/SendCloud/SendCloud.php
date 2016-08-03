<?php

namespace Bixie\SendCloud\Carriers\SendCloud;

use Picqer\Carriers\SendCloud\Connection;

class SendCloud {

    /**
     * The HTTP connection
     *
     * @var Connection
     */
    protected $connection;

    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
    }

    public function invoices()
    {
        return new Invoice($this->connection);
    }

    public function labels()
    {
        return new Label($this->connection);
    }

    public function parcels($data = [])
    {
        return new Parcel($this->connection, $data);
    }

    public function shippingMethods()
    {
        return new ShippingMethod($this->connection);
    }

    public function users()
    {
        return new User($this->connection);
    }

}