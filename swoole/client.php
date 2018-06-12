<?php
/**
 * Created by PhpStorm.
 * User: weijianhua
 * Date: 18/6/10
 * Time: 下午9:15
 */

$client = new swoole_client(SWOOLE_SOCK_TCP);

if (!$client->connect('127.0.0.1',9501,0,5)) {
    die('connect failed');
}

if (!$client->send('hello,world')) {
    die('send failed');
}

$data = $client->recv();

if (!$data) {
    die('recv failed');
}

echo $data;

$client->close();