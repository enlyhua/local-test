<?php
/**
 * Created by PhpStorm.
 * User: lancelot
 * Date: 16-7-30
 * Time: 下午10:31
 */

require "vendor/autoload.php";

use Hprose\Swoole\Server as SwooleServer;
use Hprose\Swoole\Socket\Service;


class Server
{
    private $serv;

    public function __construct() {
        //1.对外提供了 http 服务,监控 9501 端口
        $this->serv = new swoole_http_server("0.0.0.0", 9501);
        $this->serv->set(array(
            'worker_num' => 1,
            'daemonize' => false,
            'max_request' => 10000,
            'dispatch_mode' => 2,
        ));

        $this->serv->on('Start', array($this, 'onStart'));
        $this->serv->on('Connect', array($this, 'onConnect'));
        $this->serv->on('Request', array($this, 'onRequest'));
        $this->serv->on('Close', array($this, 'onClose'));

        // add rpc port
        //2. rpc 端口
        $port = $this->serv->listen("0.0.0.0", 9502, SWOOLE_SOCK_TCP);
        $port->set(
            //Hprose 内部没有使用协议解析，它自己解析数据
            //但是如果我们不设置协议数据，会复用父类的 http 协议解析，所以设置 open_eof_split 为 false,
            // 这样就可以兼容 Hprose 协议
            ['open_eof_split'=> false,]
        );

        //我们怎么讲一个新监听的端口把它作为 rpc 解析呢，就要用到 Hprose 的 Service 对象
        // 每个 Service 对象 ，把对象传递进去,然后给传递的对象绑定回调函数, 或者传递tcp的服务进去
        // 这样就是伪造了一个 rpc 服务，并传递了一个端口, 并且绑定了一个函数，如 upload
        // 这个时候 这个 upload, 这个 upload  就可以通过 9502 端口被远程客户端访问到

        $rpc_service = new Service();
        $rpc_service->socketHandle($port);
        // 可以根据业务添加更多函数
        $rpc_service->addFunction(array($this, 'upload'));


        // add udp port
        // 创建 udp 端口, 绑定 packet 回调
        //所有发送到这个端口的广播消息,都会在这里接收到，如，一个服务上线了，它发送了自己的信息过来，这里可以监听处理
        //所有的请求作为 rpc 请求走到这边提供服务，所有服务的统计数据，可以通过http 服务获取到,这样我们在一个服务里面，提供了3个端口
        //以及3种不同协议的服务，分别用来web的数据统计，rpc的微服务，还有广播消息的处理
        // 提供了 web 服务给给 web 页面采集数据，其他服务上线发送广播消息，由这个服务统计接收，汇总。下线也会通知
        // 还有心跳监听，发一个 upd 包过去，等待回调.这边的服务还会提供一些服务，他们的日志异常信息会通过 rpc 服务上报
        $udp_port =  $this->serv->listen("0.0.0.0", 9503, SWOOLE_SOCK_UDP);
        $udp_port->on('packet', function ($serv, $data, $addr) {
            var_dump($data, $addr);
            
        });
        $this->serv->start();
    }

    public function onStart( $serv ) {
        echo "Start\n";
    }

    public function onConnect( $serv, $fd, $from_id ) {
        echo "Client {$fd} connect\n";

    }

    public function onRequest($request, $response) {
        var_dump($request->fd);

    }

    public function upload($data ) {
        var_dump($data);
        return $data;
    }


    public function onClose( $serv, $fd, $from_id ) {
        echo "Client {$fd} close connection\n";
    }
}

new Server();