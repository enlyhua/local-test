<?php
/**
 * Created by PhpStorm.
 * User: WeiDaoDao
 * Date: 2019/3/14
 * Time: 11:42 PM
 */

$ip = '127.0.0.1';

$ser = new swoole_server($ip, 9501);

$ser->on('connect', function($ser, $fd) {
    var_dump($fd);
    echo "client : connect.\n";
});

$ser->on('receive', function($ser, $fd, $from_id, $data) {
    var_dump($from_id);
    var_dump($fd);
    $ser->send($fd, "server:".$data);
});

$ser->on('close', function($ser, $fd) {
    var_dump($fd);
    echo "client:close.\n";
});

$ser->start();