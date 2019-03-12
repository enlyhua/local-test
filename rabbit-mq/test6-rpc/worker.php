<?php
/**
 * Created by PhpStorm.
 * User: WeiDaoDao
 * Date: 2019/1/31
 * Time: 1:56 PM
 *
 * https://segmentfault.com/a/1190000013490939
 *
 * 消息属性
   AMQP协议(0-9-1 protocol)预定义了一套14个属性，去一个消息。大多数属性很少使用，除了以下内容：
   delivery_mode: 将消息标记为持久性。 (with a value of 2) or transient (1). 您可能会从第二个教程中记住这个属性。
   content_type：用来描述编码的MIME类型。例如，对于常用的JSON编码，将此属性设置为应用程序/ JSON是一个很好的做法。
   reply_to：常用的名字一个回调队列。
   correlation_id：有助于将RPC响应与请求关联起来。
 */

require_once __DIR__ . '/../../vendor/autoload.php';

use PhpAmqpLib\Message\AMQPMessage;
use PhpAmqpLib\Connection\AMQPStreamConnection;

//1.获取连接
$connection = new AMQPStreamConnection('118.25.49.26', 5672, 'test', '123456');

//2.获取信道
$channel = $connection->channel();

//3.声明队列
$channel->queue_declare('rpc_queue', false, false, false, false);

//rpc方法
function fib($n) {
    if ($n == 0)
        return 0;
    if ($n == 1)
        return 1;
    return fib($n-1) + fib($n-2);
}


echo " [x] Awaiting RPC requestsn" . PHP_EOL;

//6.回调的方法
$callback = function($req) {
    $n = intval($req->body);
    echo " [.] fib(", $n, ")\n" . PHP_EOL;

    sleep(3);

    //7.创建消息
    $msg = new AMQPMessage(
        (string) fib($n),
        ['correlation_id' => $req->get('correlation_id')]
    );

    //8.发送消息
    $req->delivery_info['channel']->basic_publish($msg, '', $req->get('reply_to'));

    //9.确认消息
    $req->delivery_info['channel']->basic_ack($req->delivery_info['delivery_tag']);
};

$channel->basic_qos(null, 1, null);

//4.将信道设置为接收模式
$channel->basic_consume('rpc_queue', '', false, false, false, false, $callback);

//5.等待客户端
while(count($channel->callbacks)) {
    $channel->wait();
}

//10.关闭信道
$channel->close();

//11.关闭连接
$connection->close();