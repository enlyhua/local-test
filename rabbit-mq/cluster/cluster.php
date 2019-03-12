<?php
/**
 * Created by PhpStorm.
 * User: WeiDaoDao
 * Date: 2019/3/12
 * Time: 2:12 PM
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

$channel->queue_declare('task_queue', false, true, false, false);

$data = implode(' ', array_slice($argv, 1));

$ttl = 30 * 1000;

if(empty($data)) $data = "Hello World!";

$msg = new AMQPMessage($data,
    [
        'delivery_mode' => AMQPMessage::DELIVERY_MODE_PERSISTENT,
        'expiration' => $ttl,
    ]
);

$channel->basic_publish($msg, '', 'task_queue');

echo " [x] Sent ", $data, "\n";

$channel->close();
$connection->close();