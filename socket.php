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

	public function __construct() {
		$this->users = new \SplObjectStorage;
	}

	public function onOpen(ConnectionInterface $conn) {
		$this->users->attach($conn);
		echo "+1  " . count($this->users) . " online\n";
	}

	public function onClose(ConnectionInterface $conn) {
		$this->users->detach($conn);
		echo "-1 - " . count($this->users) . " online\n";
	}

	public function onError(ConnectionInterface $conn, \Exception $e) {
		echo __FUNCTION__ . "\n";
	}

	public function onMessage(ConnectionInterface $from, $msg) {
		foreach ( $this->users as $client ) {
			$client->send($msg);
		}
	}

}

$server = IoServer::factory(new HttpServer(new WsServer(new App())), $port);
$server->run();
