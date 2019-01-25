<?php
/**
 * Created by PhpStorm.
 * User: weijianhua
 * Date: 18/6/10
 * Time: 下午8:54
 */

$host = '127.0.0.1';
$port = 9502;

$ws = new swoole_websocket_server($host, $port);

$ws->on('open', function($ws, $request) {
    var_dump($request->fd, $request->get, $request->server);
    $ws->push($request->fd, 'hello,welcome'.PHP_EOL);
});

$ws->on('message', function($ws, $frame) {
    echo 'Message : '.$frame->data . PHP_EOL;
    $ws->push($frame->fd, 'server : '.$frame->data);
});

$ws->on('close', function($ws, $fd) {
    echo 'client id :'.$fd.' close'.PHP_EOL;
});

$ws->start();