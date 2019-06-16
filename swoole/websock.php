<?php

$server = new swoole_websocket_server('0.0.0.0', 9501);

$server->on('open', function(swoole_websocket_server $server, $request) {
    echo "server : handshake success with fd {$request->fd}" . PHP_EOL;
});

$server->on('message', function(swoole_websocket_server $server, $frame) {
    echo "receive from {$frame->fd} : {$frame->data}, opcode : {$frame->opcode}, fin:{$frame->finish}" . PHP_EOL;
    $server->push($frame->fd, "this is server");
});

$server->on('close', function($server, $fd) {
    echo "client {$fd} closed" . PHP_EOL;
});

$server->start();