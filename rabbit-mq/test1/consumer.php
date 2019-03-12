<?php
/**
 * Created by PhpStorm.
 * User: WeiDaoDao
 * Date: 2019/1/16
 * Time: 6:19 PM
 * https://segmentfault.com/a/1190000011825148
 */

require_once __DIR__ . '/../../vendor/autoload.php';

use PhpAmqpLib\Connection\AMQPStreamConnection;

//1.连接到rabbitmq
$connection = new AMQPStreamConnection('118.25.49.26', 5672, 'test', '123456');

//2.获取信道
$channel = $connection->channel();

//3.声明交换器

//4.声明队列, passive(只想检测队列是否存在),
//durable 决定了rabbitMQ是否需要在崩溃或者重启之前重新创建队列(或者交换器),默认为false
//exclusive(队列是否私有),
//auto_delete(当最后一个消费者取消订阅的时候，队列就会自动移除)
$channel->queue_declare('hello', false, false, false, false);

echo ' [*] Waiting for messages. To exit press CTRL+C', "\n";

//当我们收到消息时，我们的回调函数将通过接收到返回的消息传递。
$callback = function($msg) {
    echo " 回调 --- [x] Received ", $msg->body, "\n";
};

//当调用basic_consume，我们的代码会阻塞,将信道置为接收模式
$result = $channel->basic_consume('hello', '', false, true, false, false, $callback);

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