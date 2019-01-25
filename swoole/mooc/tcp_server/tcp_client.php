<?php
/**
 * Created by PhpStorm.
 * User: weijianhua
 * Date: 18/6/24
 * Time: 下午9:47
 */

$client = new swoole_client(SWOOLE_SOCK_TCP);

$host = '127.0.0.1';
$port = 9501;

if (!$client->connect($host, $port)) {
    echo '连接失败'.PHP_EOL;
    exit;
}

fwrite(STDOUT, '请输入消息: ');

$msg = trim(fgets(STDIN));

$result = $client->send($msg);

var_dump($result);

$receive = $client->recv();
var_dump($receive);