<?php
/**
 * Created by PhpStorm.
 * User: WeiDaoDao
 * Date: 2019/3/12
 * Time: 4:24 PM
 */

require_once __DIR__ . '/../../vendor/autoload.php';

use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Wire\AMQPTable;
use PhpAmqpLib\Channel\AMQPChannel;

$hosts = require __DIR__ . '/../multi_hosts.php';

//1.连接到rabbitmq
$connection = AMQPStreamConnection::create_connection(
    $hosts,
    [
//        'insist' => false,
//        'login_method' => 'AMQPLAIN',
//        'login_response' => null,
//        'locale' => 'en_US',
        'connection_timeout' => 3.0,
        'read_write_timeout' => 3.0,
//        'context' => null,
//        'keepalive' => false,
//        'heartbeat' => 0
    ]
);

$channel = $connection->channel();

$channel->exchange_declare('delay_exchange', 'direct', false, false, false);

$channel->queue_declare('delay_queue',false,true,false,false,false);
$channel->queue_bind('delay_queue', 'delay_exchange','delay_exchange');

echo ' [*] Waiting for logs. To exit press CTRL+C', "\n";

$callback = function($msg){
    echo ' [x] ',$msg->delivery_info['routing_key'], ':', $msg->body, "\n";
};

$channel->basic_consume('delay_queue', '', false, true, false, false, $callback);

while(count($channel->callbacks)) {
    $channel->wait();
}

$channel->close();
$connection->close();