<?php
/**
 * Created by PhpStorm.
 * User: WeiDaoDao
 * Date: 2019/1/29
 * Time: 6:56 PM
 *
 * https://segmentfault.com/a/1190000013489663
 *
 * php send.php "kern.critical" "A critical kernel error"
 */

require_once __DIR__ . '/../../vendor/autoload.php';
use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;

//1.创建连接
$connection = new AMQPStreamConnection('118.25.49.26', 5672, 'test', '123456');

//2.获取信道
$channel = $connection->channel();

//3.声明交换器
$channel->exchange_declare('topic_logs', 'topic', false, false, false);

$routing_key = isset($argv[1]) && !empty($argv[1]) ? $argv[1] : 'anonymous.info';
$data = implode(' ', array_slice($argv, 2));
if(empty($data)) $data = "Hello World!";

//4.创建消息
$msg = new AMQPMessage($data);

var_dump($routing_key);

//5.发送消息
$channel->basic_publish($msg, 'topic_logs', $routing_key);

echo " [x] Sent ",$routing_key,':',$data," \n";

//6.关闭信道
$channel->close();

//7.关闭连接
$connection->close();