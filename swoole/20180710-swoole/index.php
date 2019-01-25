<?php
/**
 * Created by PhpStorm.
 * User: weijianhua
 * Date: 18/6/21
 * Time: 上午12:42
 */

$server = new swoole_server('0.0.0.0', 9501);

$server->set(
    [
        'worker_num' => 2,
        'max_request' => 3,
        'dispatch_mode' => 3,
    ]
);

$server->on('Receive', function($server, $fd, $from_id, $data) {
    $server->send($fd, 'Server : '.$data);
});

$server->start();