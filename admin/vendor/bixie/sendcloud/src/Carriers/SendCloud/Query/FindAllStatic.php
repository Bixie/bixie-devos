<?php

namespace Bixie\SendCloud\Carriers\SendCloud\Query;

trait FindAllStatic {

    public function all($params = [])
    {
        $result = $this->connection()->get($this->url);

        return $this->collectionFromResult($result);
    }

    public function collectionFromResult($result)
    {
        $collection = [];
        foreach ($result[$this->namespaces['plural']] as $r)
        {
            $collection[] = new static($this->connection(), $r);
        }

        return $collection;
    }

}