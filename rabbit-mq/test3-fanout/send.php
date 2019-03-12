<?php
/**
 * Created by PhpStorm.
 * User: WeiDaoDao
 * Date: 2019/1/25
 * Time: 4:39 PM
 * https://segmentfault.com/a/1190000013285229
 */

require_once __DIR__ . '/../../vendor/autoload.php';

use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;

//1.连接到rabbitmq
$connection = new AMQPStreamConnection('118.25.49.26', 5672, 'test', '123456');

//2.获取信道
$channel = $connection->channel();

//3.声明交换器
//type 交换器类型
$channel->exchange_declare('logs', 'fanout', false, false, false);


$data = implode(' ', array_slice($argv, 1));
if(empty($data)) $data = "info: Hello World!";

//4.创建消息
$msg = new AMQPMessage($data);

//5.发布消息
$channel->basic_publish($msg, 'logs');

echo " [x] Sent ", $data, "\n";

//6.关闭信道
$channel->close();

//7.关闭连接
$connection->close();