<?php

/**
 * 固定包头协议
 * Class Server
 *
 * 用C写的嵌入式程序通信，用二进制的格式通信
 */
class Server
{
    private $serv;

    public function __construct() {
        $this->serv = new swoole_server("0.0.0.0", 9501);
        $this->serv->set(array(
            'worker_num' => 8,
            'daemonize' => false,
            'max_request' => 10000,
            'dispatch_mode' => 2,
            'package_max_length' => 8192,
            'open_length_check'=> true,//打开固定包头协议检测
            //长度信息在 header 里面偏移多少位,0 协议头一开始就是长度
            //swoole 在读的时候，会根据这个长度信息,按照 package_length_type 方法读取数据
            'package_length_offset' => 0,
            // package_body_offset = 4 表示我们这个头信息只有一个 int 4 字节的长度数据
            'package_body_offset' => 4,
            'package_length_type' => 'N'//用哪种方法解包数据
        ));

        $this->serv->on('Start', array($this, 'onStart'));
        $this->serv->on('Connect', array($this, 'onConnect'));
        $this->serv->on('Receive', array($this, 'onReceive'));
        $this->serv->on('Close', array($this, 'onClose'));

        $this->serv->start();
    }

    public function onStart( $serv ) {
        echo "Start\n";
    }

    public function onConnect( $serv, $fd, $from_id ) {
        echo "Client {$fd} connect\n";
       
    }

    public function onReceive( swoole_server $serv, $fd, $from_id, $data ) {
        //解包,就是头4位的长度截取出来
        $length = unpack("N" , $data)[1];
        var_dump($data);
        echo "Length = {$length}\n";
        //再对 $data 进行裁剪，把前面4位裁掉，即协议头信息裁掉
        $msg = substr($data,-$length);
    	echo "Get Message From Client {$fd}:{$msg}\n";
    }

    public function onClose( $serv, $fd, $from_id ) {
        echo "Client {$fd} close connection\n";
    }
}

new Server();