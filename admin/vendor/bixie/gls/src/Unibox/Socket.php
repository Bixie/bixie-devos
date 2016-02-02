<?php

namespace Bixie\Gls\Unibox;

use Bixie\Gls\Data\Broadcast;
use Bixie\Gls\GlsException;
use Socket\Raw\Factory as SocketFactory;

class Socket {

	/**
	 * address of GLS unibox
	 * @var string
	 */
	protected $server;

	/**
	 * port to call on unibox
	 * @var string
	 */
	protected $port;

	/**
	 * Parser constructor.
	 * @param string $server
	 * @param int    $port
	 */
	public function __construct ($server = '', $port = 0) {
		$this->server = $server;
		$this->port = (int) $port;
	}

	/**
	 * @param Broadcast $broadcast
	 * @return array
	 * @throws GlsException
	 */
	public function send(Broadcast $broadcast) {
		//todo validate
		return $this->sendViaSocket($broadcast);

	}

	/**
	 *
	 * @param Broadcast $broadcast
	 * @return string
	 */
	private function sendViaSocket(Broadcast $broadcast){

		try {
			$factory = new SocketFactory();

			$socket = $factory->createClient("$this->server:$this->port");

			// send simple HTTP request to remote side
			$socket->write((string)$broadcast);

			// receive response
			$buf = '';
			while ($out = $socket->read(8192)) {
				$buf .= $out;
			}

			$socket->close();

			return $buf;

		} catch (\Exception $e) {

			throw new \RuntimeException(sprintf('GLS Connection error: %s', $e->getMessage()), $e->getCode(), $e);
		}

	}


}