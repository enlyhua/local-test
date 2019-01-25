<?php

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
            //规定了一个数据包所有拥有的最大长度,默认8192
            //为什么要固定这个?
            //在 swoole 当中，为了给每个连接做协议的拆分，需要有一个数据的缓冲区，这个缓冲区是用来缓存读到的数据的
            //这里规定的大小的话，每个连接都有这样一个缓冲区,当服务器连接变多的时候，需要对缓冲区做限制
            'package_max_length' => 8192,
            //check,split 都是开启对 eof 的检测
//            'open_eof_check'=> true,
            'open_eof_split'=> true, // 现在更推荐这个
            //具体规定什么样 标记标记结尾
            'package_eof' => "\r\n"
            //package_max_length,open_eof_check,package_eof 只要声明了这3个配置
            //在 swoole 的底层就会针对发送过来的数据，进行这样的协议处理。一直接收数据，知道2种情况的发生
            //1.检测到 package_eof; 2.超过 package_max_length 大小,抛弃连接，请求非法
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

    //1.虽然外面开启的 eof 检测,但实际上也不是一个个接收。可能一次性接收很多
    //这是因为 swoole 只开始 open_eof_check 时，它不是从前往后找的，它是从接收到的数据的尾端往前找
    //把好几个包当中一个内容发送上来，这种情况需要在代码里面手工的拆分
    public function onReceive( swoole_server $serv, $fd, $from_id, $data ) {
        //接收到数据后，根据 eof 进行二次拆分

    	/*$data_list = explode("\r\n", $data);
    	foreach ($data_list as $msg) {
    		if( !empty($msg) ) {
    			echo "Get Message From Client {$fd}:{$msg}\n";
    		}
    		
    	}*/

        //如果我们不想这么做，可以开启 open_eof_split
        // open_eof_split 做的事情就是，如果一次性收到很多包，它不是一次性都回调回来
        //而是先自己做一次拆分,在底层实现 explode 方法
        var_dump($data);
    }

    public function onClose( $serv, $fd, $from_id ) {
        echo "Client {$fd} close connection\n";
    }
}

new Server();