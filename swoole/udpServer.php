<?php
/**
 * Created by PhpStorm.
 * User: weijianhua
 * Date: 18/6/10
 * Time: 下午5:05
 */

$host = '127.0.0.1';
$port = 9502;

$serv = new swoole_server($host, $port, SWOOLE_PROCESS, SWOOLE_SOCK_UDP);

$serv->on('packet', function($serv, $data, $clientInfo) {
    $serv->send($clientInfo['address'], $clientInfo['port'], "Server : ".$data);
    var_dump($clientInfo);
});

$serv->start();