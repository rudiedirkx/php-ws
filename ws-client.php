<?php

use Ratchet\Client\Connector;
use Ratchet\Client\WebSocket;
use React\EventLoop\Factory as ReactFactory;

require 'vendor/autoload.php';

echo "Before\n";

// SYNC //

$loop = ReactFactory::create();

$connector = new Connector($loop);
$connection = $connector('wss://php-ws.home.hotblocks.nl/sws');
$connection->then(function(WebSocket $conn) {
	echo "Connected. Sending message...\n";
	$conn->send('Hello ' . rand());
	echo "Sent message\n";
	$conn->close();
}, function(DomainException $ex) {
	echo "Could not connect: {$ex->getMessage()}\n";
});

$loop->run();

// ASYNC //

// \Ratchet\Client\connect('wss://php-ws.home.hotblocks.nl/ws')->then(function(WebSocket $conn) {
// 	echo "Connected. Sending message...\n";
// 	$conn->send('Hello ' . rand());
// 	echo "Sent message\n";
// 	$conn->close();
// }, function ($e) {
// 	echo "Could not connect: {$e->getMessage()}\n";
// });

// END //

echo "After\n";
