<?php
/**
 * Created by PhpStorm.
 * User: weijianhua
 * Date: 18/6/10
 * Time: 下午4:51
 */

$host = '127.0.0.1';
$port = 9501;
$serv = new swoole_server($host, $port);

$serv->on('connect', function($serv, $fd) {
    echo 'client : Connect'.PHP_EOL;
});

$serv->on('receive', function($serv, $fd, $from_id, $data) {
    $serv->send($fd, 'Server : '.$data);
});

$serv->on('close', function($serv, $fd) {
    echo 'client : close.'.PHP_EOL;
});

$serv->start();