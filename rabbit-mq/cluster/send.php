<?php
/**
 * Created by PhpStorm.
 * User: WeiDaoDao
 * Date: 2019/3/12
 * Time: 4:53 PM
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

//2.获取连接
$channel = $connection->channel();

//3.声明交换器
$exchange_name = $routing_key = 'delay_exchange_send_30';
$channel->exchange_declare($exchange_name, 'direct',false,false,false);


/**
 * 声明死信队列发送到哪个队列
 */
$exchange_replay_name = $routing_replay_key = 'delay_exchange_replay_30';

$tale = new AMQPTable();
//设置x-dead-letter-exchange
$tale->set('x-dead-letter-exchange', $exchange_replay_name);
//设置x-dead-letter-routing-key
$tale->set('x-dead-letter-routing-key', $routing_replay_key);

//4.声明队列
$queue_name = 'delay_queue_send_30';
$channel->queue_declare($queue_name,false,true,false,false,false,$tale);

//5.绑定
$channel->queue_bind($queue_name, $exchange_name, $routing_key);

//过期时间
$ttl = 10 * 1000;
$data = '设置 30s 过期 ..' .PHP_EOL;
$msg = new AMQPMessage($data,
    [
        'delivery_mode' => AMQPMessage::DELIVERY_MODE_PERSISTENT,
        'expiration' => $ttl, //消息过期时间
    ]
);

//6.发送数据
$ret = $channel->basic_publish($msg, $exchange_name, $routing_key);

//6.关闭信道
$channel->close();

//7.关闭连接
$connection->close();