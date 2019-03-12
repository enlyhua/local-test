<?php
/**
 * Created by PhpStorm.
 * User: WeiDaoDao
 * Date: 2019/3/12
 * Time: 5:11 PM
 */
require_once __DIR__ . '/../../vendor/autoload.php';

use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;
use PhpAmqpLib\Wire\AMQPTable;

$hosts = require __DIR__ . '/../multi_hosts.php';
//1.连接到rabbitmq
$connection = AMQPStreamConnection::create_connection(
    $hosts,
    [
        'connection_timeout' => 3.0,
        'read_write_timeout' => 3.0,
    ]
);

//2.获取信道
$channel = $connection->channel();

//3.声明交换器
$exchange_replay_name = $routing_replay_key = 'delay_exchange_replay_30';
$channel->exchange_declare($exchange_replay_name, 'direct',false,false,false);

//4.声明队列
$queue_relay_name = 'delay_queue_replay_30';
$channel->queue_declare($queue_relay_name, false, false, false, false);

//5.绑定
$channel->queue_bind($queue_relay_name, $exchange_replay_name, $routing_replay_key);


//6.消费消息
$callback = function($msg){
    echo " [x] Received ", $msg->body, "\n"; //根据"."数量个数获取延迟时间，单位秒
    sleep(substr_count($msg->body, '.'));  //模拟业务执行时间延迟
    echo " [x] Done", "\n";
    $msg->delivery_info['channel']->basic_ack($msg->delivery_info['delivery_tag']);
};
$result = $channel->basic_consume($queue_relay_name, '', false, false, false, false, $callback);

while(count($channel->callbacks)) {
    echo 'while' . PHP_EOL;
    $channel->wait();
}

//7.关闭信道
$channel->close();

//8.关闭连接
$connection->close();