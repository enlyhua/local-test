<?php
/**
 * Created by PhpStorm.
 * User: lancelot
 * Date: 16-7-30
 * Time: 下午8:13
 * websocket 本质上也可以作为 http 服务，只要绑定了 onRequest 回调
 */
class Server
{
    private $serv;

    /**
     * @var PDO
     */
    private $pdo;

    public function __construct()
    {
        $this->serv = new swoole_websocket_server("0.0.0.0", 9501);
        $this->serv->set([
            'worker_num' => 1,
            'dispatch_mode' => 2,
            'daemonize' => 0,
        ]);

        $this->serv->on('message', array($this, 'onMessage'));
        //可以作为 http 服务
        $this->serv->on('Request', array($this, 'onRequest'));

        //新创建一个端口，不给指定任何协议，会复用父类端口
        $port1 = $this->serv->listen("0.0.0.0", 9503, SWOOLE_SOCK_TCP);
        $port1->set(
            [
                'open_eof_split'=> true,
                'package_eof' => "\r\n"
            ]
        );
        $port1->on('Receive', array($this, 'onTcpReceive'));

        $this->serv->start();
    }

    /**
     * 这里可以做什么？
     * 1.websocket 连接过来可以保持住的,我们可以主动的给客户端push 消息
     */
    public function onMessage(swoole_websocket_server $_server, $frame)
    {
        foreach($_server->connections as $fd)
        {
            $info = $_server->connection_info($fd);
            var_dump($info);
        }
    }

    /**
     * 2.当我们接收 http 请求过来的时候, $this->serv->connections 获取所有的连接
     */
    public function onRequest($request, $response)
    {
        // 获取连接的描述符,有了描述符，就可以给客户端发送数据了
        // 因为 websocket 调用 push 方法发送， tcp 调用 send 方法发送, 而 http 用 response 对象的 end 发送
        foreach($this->serv->connections as $fd)
        {
            $info = $this->serv->connection_info($fd);
            // server_port 这用来判定这个连接是通过哪个端口连接到服务器的
            // 所以通过这个判定，用哪个发送方式, 这样就可以做不一样的广播
            switch($info['server_port'])
            {
                case 9501:
                {
                    // http 和 websocket 公用一个端口, http 不能用push ，所以额外需要一个判定
                    // websocket
                    if($info['websocket_status'])
                    {

                    }
                    // http
                    $response->end("");
                }

                case 9503:
                {
                    // TCP
                }
            }

            var_dump($info);
        }
    }

    public function onTcpReceive( swoole_server $serv, $fd, $from_id, $data ) {
        var_dump($data);
        $data_list = explode("\r\n", $data);
        foreach ($data_list as $msg) {
            if( !empty($msg) ) {
                echo "Get Message From Client {$fd}:{$msg}\n";
            }

        }
    }


}

new Server();