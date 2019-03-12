<?php
/**
 * Created by PhpStorm.
 * User: WeiDaoDao
 * Date: 2019/1/29
 * Time: 6:57 PM
 *
 * https://segmentfault.com/a/1190000013489663
 *
 * php worker.php "#"
 *
 *   *（星号）可以代替一个词。
 *   #（哈希）可以代替零个或更多的单词。
 */

require_once __DIR__ . '/../../vendor/autoload.php';
use PhpAmqpLib\Connection\AMQPStreamConnection;

//1.创建连接
$connection = new AMQPStreamConnection('118.25.49.26', 5672, 'test', '123456');

//2.获取信道
$channel = $connection->channel();

//3.声明交换器
$channel->exchange_declare('topic_logs', 'topic', false, false, false);

//4.声明队列
list($queue_name, ,) = $channel->queue_declare("", false, false, true, false);

$binding_keys = array_slice($argv, 1);
if( empty($binding_keys )) {
    file_put_contents('php://stderr', "Usage: $argv[0] [binding_key]\n");
    exit(1);
}

//5.绑定
foreach($binding_keys as $binding_key) {
    $channel->queue_bind($queue_name, 'topic_logs', $binding_key);
}

echo ' [*] Waiting for logs. To exit press CTRL+C', "\n";

$callback = function($msg){
    echo ' [x] ',$msg->delivery_info['routing_key'], ':', $msg->body, "\n";
};

//6.消费
$channel->basic_consume($queue_name, '', false, true, false, false, $callback);

while(count($channel->callbacks)) {
    $channel->wait();
}

//7.关闭信道
$channel->close();

//8.关闭连接
$connection->close();