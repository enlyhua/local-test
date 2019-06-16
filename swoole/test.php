<?php

use Swoole\Redis\Server;

$server = new Server('127.0.0.1', 9501);

$server->setHandler('Set', function($fd, $data) use ($server) {
    $server->array($data[0], $data[1]);
    return $server::format(Server::INT, 1);
});

$server->setHandler('Get', function ($fd, $data) use ($server) {
    $db->query($sql, function($db, $result) use ($fd) {
        $server->send($fd, Server::format(Server::LIST, $result));
    });
});