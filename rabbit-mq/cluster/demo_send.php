<?php
/**
 * Created by PhpStorm.
 * User: WeiDaoDao
 * Date: 2019/3/12
 * Time: 4:36 PM
 */

require_once __DIR__ . '/../../vendor/autoload.php';

use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;

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
$expiration = 0;

$channel->exchange_declare('delay_exchange', 'direct',false,false,false);

$channel->queue_declare('delay_queue',false,true,false,false,false);
$channel->queue_bind('delay_queue', 'delay_exchange','delay_exchange');


$msg = new AMQPMessage($expiration,array(
    'delivery_mode' => AMQPMessage::DELIVERY_MODE_PERSISTENT,
));

//直接投递到delay_exchange
$channel->basic_publish($msg,'delay_exchange','delay_exchange');
echo date('Y-m-d H:i:s')." [x] 发送一条0毫秒后执行的数据! ".PHP_EOL;

$channel->close();
$connection->close();

