<?php
/**
 * Created by PhpStorm.
 * User: weijianhua
 * Date: 18/6/9
 * Time: 上午1:26
 */

//创建Server对象，监听 127.0.0.1:9999端口
$serv = new swoole_server('127.0.0.1', 9999);
//监听连接进入事件
$serv->on('connect', function (swoole_server $serv, $fd) {
    echo 'Client: Connect.' . PHP_EOL;
});
//监听数据接收事件
$serv->on('receive', function (swoole_server $serv, $fd, $fromId, $data) {
    $serv->send($fd, 'Server: ' . $data);
});
//监听连接关闭事件
$serv->on('close', function (swoole_server $serv, $fd) {
    echo 'Client: Close.' . PHP_EOL;
});
//启动服务器
$serv->start();