<?php
/**
 * Created by PhpStorm.
 * User: WeiDaoDao
 * Date: 2019/3/15
 * Time: 12:01 AM
 */


$ip = '127.0.0.1';
$server = new swoole_server($ip, 9502, SWOOLE_PROCESS, SWOOLE_SOCK_UDP);

$server->on('Packet', function($server, $data, $clientInfo) {
    $server->sendto($clientInfo['address'], $clientInfo['port'],"server : " . $data);
    var_dump($clientInfo);
});

$server->start();