<?php
/**
 * Created by PhpStorm.
 * User: WeiDaoDao
 * Date: 2019/3/12
 * Time: 4:41 PM
 * https://zhuanlan.zhihu.com/p/35164499
 *
 * 初始化 30s 队列
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

$channel = $connection->channel();

$exchange_name = $routing_key = 'delay_exchange_30';
$queue_name = 'delay_queue_30';

$channel->exchange_declare($exchange_name, 'direct',false,false,false);

$tale = new AMQPTable();
//设置x-dead-letter-exchange
$tale->set('x-dead-letter-exchange', $exchange_name);
//设置x-dead-letter-routing-key
$tale->set('x-dead-letter-routing-key', $routing_key);

//设置队列过期时间
//$tale->set('x-message-ttl', $expiration);
$channel->queue_declare($queue_name,false,true,false,false,false,$tale);
$channel->queue_bind($queue_name, $exchange_name,$routing_key);