<?php

use Guzzle\Http\Message\RequestInterface;
use Guzzle\Http\Message\Response;
use Ratchet\ConnectionInterface;
use Ratchet\Http\HttpServer;
use Ratchet\Http\HttpServerInterface;
use Ratchet\MessageComponentInterface;
use Ratchet\Server\IoServer;
use Ratchet\WebSocket\WsServer;

require 'vendor/autoload.php';

$port = (int) @$_SERVER['argv'][1];
if ( !$port ) {
	echo "Need valid port as argument 1.\n";
	exit(1);
}

class HttpApp implements HttpServerInterface {

	public function onOpen(ConnectionInterface $conn, RequestInterface $request = null) {
		echo __FUNCTION__ . "\n";

		$uri = $request->getUrl();
		echo " - $uri\n";

        $rsp = new Response(200, array(), '<pre>oele</pre><p>boele</p>');

        $conn->send($rsp);
        $conn->close();
	}

	public function onClose(ConnectionInterface $conn) {
		echo __FUNCTION__ . "\n";
	}

	public function onError(ConnectionInterface $conn, \Exception $e) {
		echo __FUNCTION__ . "\n";
	}

	public function onMessage(ConnectionInterface $from, $msg) {
		echo __FUNCTION__ . "\n";
	}

}

$server = IoServer::factory(new HttpServer(new HttpApp()), $port);
$server->run();
