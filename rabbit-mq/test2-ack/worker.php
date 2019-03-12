<?php
/**
 * Created by PhpStorm.
 * User: WeiDaoDao
 * Date: 2019/1/25
 * Time: 3:17 PM
 *
 * https://segmentfault.com/a/1190000011829380
 *
 * 1.可以开多个消费者，消息是轮询发送的
 * 2.可以开启 ack 模式，消费者中止都未确认的消息后很快会被重新分配。
 *
 * 注意：
 *  RabbitMQ不允许你重新定义现有队列用不同的参数，将返回一个错误的任何程序
 *
 * php new_task.php msg1........
 */

require_once __DIR__ . '/../../vendor/autoload.php';

use PhpAmqpLib\Connection\AMQPStreamConnection;

//1.连接到rabbitmq
$connection = new AMQPStreamConnection('118.25.49.26', 5672, 'test', '123456');

//2.获取信道
$channel = $connection->channel();

//3.声明交换器

//4.声明队列, passive(只想检测队列是否存在), exclusive(队列是否私有), auto_delete(当最后一个消费者取消订阅的时候，队列就会自动移除)
//durable true 设置队列为持久的
$channel->queue_declare('task_queue_1', false, false, false, false);

//为了改变这个分配方式，我们可以调用basic_qos方法，设置参数prefetch_count = 1。这告诉RabbitMQ不要在一个时间给一个消费者多个消息。
//或者，换句话说，在处理和确认以前的消息之前，不要向消费者发送新消息。相反，它将发送给下一个仍然不忙的消费者。
//$channel->basic_qos(null, 1, null);

echo ' [*] Waiting for messages. To exit press CTRL+C', "\n";

//当我们收到消息时，我们的回调函数将通过接收到返回的消息传递。

$callback = function($msg){
    echo " [x] Received ", $msg->body, "\n"; //根据"."数量个数获取延迟时间，单位秒
    sleep(substr_count($msg->body, '.'));  //模拟业务执行时间延迟
    echo " [x] Done", "\n";
    $msg->delivery_info['channel']->basic_ack($msg->delivery_info['delivery_tag']);
};

//当调用basic_consume，我们的代码会阻塞。
//no_ack true意味着没有ACK, false 开启消息确认
$result = $channel->basic_consume('task_queue_1', '', false, false, false, false, $callback);

while(count($channel->callbacks)) {
    echo 'while' . PHP_EOL;
    $channel->wait();
}

//5.把队列和交换器绑定起来

//6.消费消息

//7.关闭信道
$channel->close();

//8.关闭连接
$connection->close();