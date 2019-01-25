<?php
/**
 * Created by PhpStorm.
 * User: weijianhua
 * Date: 18/6/27
 * Time: 下午2:48
 */

$ws = new swoole_websocket_server('0.0.0.0', 9501);

$ws->on('Open', function(swoole_websocket_server $ws, $request) {
    echo 'server : handshake success with fd '. $request->fd.PHP_EOL;
});

$ws->on('Message', function(swoole_websocket_server $ws, $frame) {
    echo 'receive from '.$frame->fd . ' data : '.$frame->data.' opcode :'. $frame->opcode . ' fin : '. $frame->finish.PHP_EOL;
    $ws->push($frame->fd, 'this is server');
});

$ws->on('Close', function($ws, $fd) {
    echo 'client close fd :' . $fd.PHP_EOL;
});

$ws->start();