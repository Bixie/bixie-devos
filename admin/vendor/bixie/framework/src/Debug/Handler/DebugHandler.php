<?php

namespace Bixie\Framework\Debug\Handler;

use Monolog\Handler\AbstractHandler;
use Monolog\Handler\HandlerInterface;

class DebugHandler extends AbstractHandler implements HandlerInterface
{
    /**
     * @var array
     */
    protected $records = [];

    /**
     * {@inheritdoc}
     */
    public function handle(array $record)
    {
        if ($record['level'] < $this->level) {
            return false;
        }

        $keys = [
            'message',
			'show_code',
            'level',
            'level_name',
            'context',
            'channel'
        ];
		$record['show_code'] = false;

		if (!isset($this->records[$record['channel']])) {
			$this->records[$record['channel']] = array();
		}

        $this->records[$record['channel']][] = array_intersect_key($record, array_flip($keys));

        return false;
    }

    /**
     * {@inheritdoc}
     */
    public function collect($channel = null)
    {
        if ($channel && isset($this->records[$channel])) {
			return $this->records[$channel];
		}
		return $this->records;
    }

}
