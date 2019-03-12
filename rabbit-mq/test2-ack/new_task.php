<?php
/**
 * Created by PhpStorm.
 * User: WeiDaoDao
 * Date: 2019/1/25
 * Time: 3:03 PM
 *  https://segmentfault.com/a/1190000011829380
 *
 *  1.把它的投递模式选项设置为2(持久)
 *  2.发送到持久化的交换器
 *  3.到达持久化的队列
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
//现在我们要标记我们的消息持续通过设置delivery_mode = 2消息属性
$msg = new AMQPMessage($data, ['delivery_mode' => AMQPMessage::DELIVERY_MODE_PERSISTENT]);
//$msg = new AMQPMessage($data);

//5.发布消息
//默认的或无名的交换
$ret = $channel->basic_publish($msg, '', 'task_queue_1');
var_dump($ret);
echo " [x] Sent ", $data, "\n";

//6.关闭信道
$channel->close();

//7.关闭连接
$connection->close();