<?php
/**
 * Created by PhpStorm.
 * User: WeiDaoDao
 * Date: 2019/1/31
 * Time: 1:56 PM
 *
 * https://segmentfault.com/a/1190000013490939
 */

require_once __DIR__ . '/../../vendor/autoload.php';

use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;

class FibonacciRpcClient
{
    private $connection;
    private $channel;
    private $callback_queue;
    private $response;
    private $corr_id;

    public function __construct()
    {
        //1.创建连接
        $this->connection = new AMQPStreamConnection('118.25.49.26', 5672, 'test', '123456');

        //2.获取信道
        $this->channel = $this->connection->channel();

        //3.当客户端启动的时候，它创建一个匿名独享的回调队列,exclusive 同时在声明的时候指定 exclusive参数，确保只有你可以读取队列上的消息
        list($this->callback_queue, ,) = $this->channel->queue_declare("", false, false, true, false);

        //4.等待响应，并消费,将信道设置为接收模式
        $this->channel->basic_consume( $this->callback_queue, '', false, false, false, false, [$this, 'on_response']);
    }

    public function on_response($rep)
    {
        if($rep->get('correlation_id') == $this->corr_id) {
            $this->response = $rep->body;
        }
    }

    public function call($n)
    {
        $this->response = null;
        $this->corr_id = uniqid();//创建唯一id

        //5.创建消息, 客户端为RPC请求设置2个属性：replyTo，设置回调队列名字；correlationId，标记request。
        $msg = new AMQPMessage(
            (string) $n,
            [
                'correlation_id' => $this->corr_id,  //常用的名字一个回调队列。
                'reply_to' => $this->callback_queue,  //有助于将RPC响应与请求关联起来
            ]
        );

        //6.发送消息,请求被发送到rpc_queue队列中。
        $this->channel->basic_publish($msg, '', 'rpc_queue');

        //7.客户端监听回调队列，当有消息时，检查correlationId属性，如果与request中匹配，那就是结果了。
        while(!$this->response) { //为空
            $this->channel->wait();
        }

        return intval($this->response);
    }
}

$fibonacci_rpc = new FibonacciRpcClient();
$response = $fibonacci_rpc->call(3);
echo " [.] Got ", $response, "n", PHP_EOL;