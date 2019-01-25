<?php
/**
 * Created by PhpStorm.
 * User: weijianhua
 * Date: 18/6/24
 * Time: 下午9:03
 */

$server = new swoole_server('127.0.0.1', 9501);

$server->on('Connect', function($server, $fd, $reactor_id) {
    echo 'Client ' . $fd . ' connection' . PHP_EOL;
    echo '来自哪个 reactor : ' . $reactor_id . PHP_EOL;
});

$server->on('Receive', function($server, $fd, $reactor_id, $data) {
    echo 'Receive from fd = '.$fd.PHP_EOL;
    echo 'Receive from reactor_id = '.$reactor_id.PHP_EOL;
    echo 'Receive data '.$data.PHP_EOL;

    $server->send($fd, '发送给'.$fd.' 接收到的数据是 data = '.$data.PHP_EOL);
});

$server->on('Close', function($server, $fd, $reactor_id) {
    echo 'Close fd = '.$fd.PHP_EOL;
    echo 'Close reactor_id = '.$reactor_id.PHP_EOL;
    echo 'Close Connection'.PHP_EOL;
});

$server->start();