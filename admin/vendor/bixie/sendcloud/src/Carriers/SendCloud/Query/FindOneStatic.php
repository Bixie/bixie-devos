<?php

namespace Bixie\SendCloud\Carriers\SendCloud\Query;

trait FindOneStatic {
    /**
     * @param $id
     * @return null|static
     */
    public function find($id = null)
    {
        $result = $this->connection()->get($this->url . ($id ? ( '/' . urlencode($id)) : ''));

        if (!array_key_exists($this->namespaces['singular'], $result)) {
            return null;
        }

        return new static($this->connection(), $result[$this->namespaces['singular']]);
    }

    /**
     * @param array $data
     * @return static
     */
    public function fromArray($data = [])
    {
        return new static($this->connection(), $data);
    }

}
