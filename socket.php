<?php

use Ratchet\ConnectionInterface;
use Ratchet\Http\HttpServer;
use Ratchet\MessageComponentInterface;
use Ratchet\Server\IoServer;
use Ratchet\WebSocket\WsServer;

require 'vendor/autoload.php';

$port = (int) @$_SERVER['argv'][1];
if ( !$port ) {
	echo "Need valid port as argument 1.\n";
	exit(1);
}

class App implements MessageComponentInterface {

	protected $users;
	protected $history = [];

	public function __construct() {
		$this->users = new \SplObjectStorage;
	}

	public function onOpen(ConnectionInterface $client) {
		$this->users->attach($client);
		echo "+1  " . count($this->users) . " online\n";

		$this->sendAll($this->mem() . "[" . count($this->users) . " users online, including you]");
	}

	public function onClose(ConnectionInterface $client) {
		$this->users->detach($client);
		echo "-1 - " . count($this->users) . " online\n";

		$this->sendAll($this->mem() . "[" . count($this->users) . " users online, including you]");
	}

	public function onError(ConnectionInterface $client, \Exception $e) {
		echo __FUNCTION__ . "\n";
	}

	public function onMessage(ConnectionInterface $from, $msg) {
		$this->sendAll($this->mem() . $msg);
	}

	protected function sendAll($msg) {
		$this->history[] = $msg;

		foreach ( $this->users as $client ) {
			$client->send($msg);
		}
	}

	protected function mem() {
		return '[' . number_format(memory_get_peak_usage() / 1e6, 2) . 'MB] ';
	}

}

$server = IoServer::factory(new HttpServer(new WsServer(new App())), $port);
$server->run();
