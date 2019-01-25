<?php
/**
 * Created by PhpStorm.
 * User: WeiDaoDao
 * Date: 2019/1/16
 * Time: 6:15 PM
 */

require_once __DIR__ . '/../../vendor/autoload.php';

use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;

//1.连接到rabbitmq
$connection = new AMQPStreamConnection('118.25.49.26', 5672, 'test', '123456');

//2.获取信道
$channel = $connection->channel();

//3.声明交换器

//4.创建消息
$msg = new AMQPMessage('Hello World!');

//5.发布消息
$channel->basic_publish($msg, '', 'hello');

echo " [x] Sent 'Hello World!'\n";

//6.关闭信道
$channel->close();

//7.关闭连接
$connection->close();