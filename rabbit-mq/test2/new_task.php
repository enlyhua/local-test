<?php
/**
 * Created by PhpStorm.
 * User: WeiDaoDao
 * Date: 2019/1/25
 * Time: 3:03 PM
 */

require_once __DIR__ . '/../../vendor/autoload.php';

use PhpAmqpLib\Message\AMQPMessage;
use PhpAmqpLib\Connection\AMQPStreamConnection;

//1.连接到rabbitmq
$connection = new AMQPStreamConnection('118.25.49.26', 5672, 'test', '123456');

//2.获取信道
$channel = $connection->channel();

$data = implode(' ', array_slice($argv,1));

if (empty($data)) {
    $data = 'Hello World';
}

//3.声明交换器

//4.创建消息
$msg = new AMQPMessage($data);

//5.发布消息
$channel->basic_publish($msg, '', 'hello');

echo " [x] Sent ", $data, "\n";

//6.关闭信道
$channel->close();

//7.关闭连接
$connection->close();