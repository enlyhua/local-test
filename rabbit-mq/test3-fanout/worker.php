<?php
/**
 * Created by PhpStorm.
 * User: WeiDaoDao
 * Date: 2019/1/25
 * Time: 4:54 PM
 * https://segmentfault.com/a/1190000013285229
 *
 * exchange 为 fanout 类型的没有确认
 */

require_once __DIR__ . '/../../vendor/autoload.php';

use PhpAmqpLib\Connection\AMQPStreamConnection;

//1.连接到rabbitmq
$connection = new AMQPStreamConnection('118.25.49.26', 5672, 'test', '123456');

//2.获取信道
$channel = $connection->channel();

//3.声明交换器
$channel->exchange_declare('logs', 'fanout', false, false, false);

//4.声明队列, passive(只想检测队列是否存在), exclusive(队列是否私有), auto_delete(当最后一个消费者取消订阅的时候，队列就会自动移除)
//durable true 设置队列为持久的
list($queue_name, ,) = $channel->queue_declare("", false, false, true, false);

//5.把队列和交换器绑定起来
$channel->queue_bind($queue_name, 'logs');

echo ' [*] Waiting for logs. To exit press CTRL+C', "\n";

$callback = function($msg){
    echo ' [x] ', $msg->body, "\n";
//    sleep(6);  //群发没有确认
//    $msg->delivery_info['channel']->basic_ack($msg->delivery_info['delivery_tag']);
    echo 'Done.' . PHP_EOL;
};

//6.消费消息
$channel->basic_consume($queue_name, '', false, true, false, false, $callback);

while(count($channel->callbacks)) {
    $channel->wait();
}

//7.关闭信道
$channel->close();

//8.关闭连接
$connection->close();