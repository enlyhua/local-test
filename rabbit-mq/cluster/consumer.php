<?php
/**
 * Created by PhpStorm.
 * User: WeiDaoDao
 * Date: 2019/3/12
 * Time: 3:12 PM
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

//$ttl = 30 * 1000;
$tale = new AMQPTable();
$tale->set('x-dead-letter-exchange', 'delay_exchange');
$tale->set('x-dead-letter-routing-key', 'delay_exchange');
//$tale->set('x-message-ttl', $ttl);

//var_dump(get_class($channel));die;
$queue = $channel->queue_declare('task_queue', false, true, false, false, false, $tale);

echo ' [*] Waiting for messages. To exit press CTRL+C', "\n";

$callback = function($msg){
    echo " [x] Received ", $msg->body, "\n";
    sleep(substr_count($msg->body, '.'));
    echo " [x] Done", "\n";
    $msg->delivery_info['channel']->basic_ack($msg->delivery_info['delivery_tag']);
};

$channel->basic_qos(null, 1, null);
$channel->basic_consume('task_queue', '', false, false, false, false, $callback);

while(count($channel->callbacks)) {
    $channel->wait();
}

$channel->close();
$connection->close();

