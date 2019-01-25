<?php
/**
 * Created by PhpStorm.
 * User: lancelot
 * Date: 16-7-30
 * Time: 下午7:41
 *
 * 应用:
 * 提供公共服务，对外的端口
 * 提供管理服务，对内的端口
 */

class Server
{
    private $serv;


    // 运行客户端 ， php client.php ,  php length_client.php
    public function __construct() {
        //1.创建 swoole_server, 使用长度协议，并绑定了回调函数
        $this->serv = new swoole_server("0.0.0.0", 9501);
        $this->serv->set(array(
            'worker_num' => 1,
            'daemonize' => false,
            'max_request' => 10000,
            'dispatch_mode' => 2,
            'package_max_length' => 8192,
            'open_length_check'=> true,
            'package_length_offset' => 0,
            'package_body_offset' => 4,
            'package_length_type' => 'N'
        ));

        $this->serv->on('Start', array($this, 'onStart'));
        $this->serv->on('Connect', array($this, 'onConnect'));
        $this->serv->on('Receive', array($this, 'onReceive'));
        $this->serv->on('Close', array($this, 'onClose'));

        //2.创建了监听第二个端口
        $port = $this->serv->listen("0.0.0.0", 9502, SWOOLE_SOCK_TCP);
        $port->set(
            [
                'open_eof_split'=> true, // 使用 eof 协议
                'package_eof' => "\r\n"
            ]
        );
        //设置独立的回调
        $port->on('Receive', array($this, 'onTcpReceive'));

        $this->serv->start();
    }

    public function onStart( $serv ) {
        echo "Start\n";
    }

    public function onConnect( $serv, $fd, $from_id ) {
        echo "Client {$fd} connect\n";

    }

    public function onReceive( swoole_server $serv, $fd, $from_id, $data ) {
        $length = unpack("N" , $data)[1];
        echo "Length = {$length}\n";
        $msg = substr($data,-$length);
        echo "Get Message From Client {$fd}:{$msg}\n";
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


    public function onClose( $serv, $fd, $from_id ) {
        echo "Client {$fd} close connection\n";
    }
}

new Server();