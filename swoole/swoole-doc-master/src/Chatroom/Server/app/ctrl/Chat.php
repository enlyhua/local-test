<?php

namespace ctrl;

use Swoole\Core\Config as ZConfig,
    db\Factory as DBFactory;

class Chat extends BaseController
{
    private $redis;
    protected $server;

    public function __construct($server)
    {
        $this->server = $server;
        $this->redis = DBFactory::getInstance('Chat');
    }

    public function online($params) {
    	$fd = $params['fd'];
    	$name = $params['name'];

        echo $name . PHP_EOL;
    	$data = json_encode( array(
    		'op' => 'online',
    		'fd' => $fd,
    		'name' => $name
    	));
        //上线的话，会在 redis 中存储新的连接描述符信息,并且存储个人信息
        $this->redis->online( $fd );
        $this->redis->setUserInfo( $fd , array( 'name' => $name ) );
        //然后给其他的用户发通知
        $fd_list = $this->redis->getFdList();
        unset($fd_list[array_search( $fd ,$fd_list)]);
    	$this->sendMessage( $fd_list , $data );
        //给当前用户发送 onlineList
        $this->getOnlineList($params);
    }

    public function offline($params) {
    	$fd = $params['fd'];

    	$data = json_encode( array(
    		'op' => 'offline',
    		'fd' => $fd,
    	));
        //把离线的描述符从 redis 中删除
        $this->redis->offline( $fd );
        //通知剩下的人
    	$this->sendMessage( $this->redis->getFdList( $this->redis->getChannel( $fd ) ), $data );
    }

    //修改一个人所在的频道
    public function changeChannel($params) {
    	$fd = $params['fd'];
    	$from = $params['from'];
    	$to = $params['to'];

    	$data = json_encode( array(
    		'op' => 'online',
    		'fd' => $fd,
    		'name' => $this->redis->getUserInfo( $fd , "name")
    	));

        $fd_list = $this->redis->getFdList($to);
        unset($fd_list[array_search( $fd ,$fd_list)]);
    	$this->sendMessage( $fd_list, $data );

    	if( !$this->redis->enterChannel( $fd , $to ) ) {
    		return;
    	}
    }

    //具体发送消息
    public function send($params) {
    	$fd = $params['fd'];
        //发送给谁,默认全局频道
    	$sendto = $params['sendto'];
    	$msg = $params['msg'];
    	$data = json_encode( array(
    		'op' => 'recv',
    		'from' => $fd,
    		'msg' => $msg
    	));
        $fd_list = $this->redis->getFdList($sendto);
        unset($fd_list[array_search( $fd ,$fd_list)]);
        $this->sendMessage( $fd_list, $data );
    }

    //获取在线信息
    public function getOnlineList($params) {
        $fd = $params['fd'];
        $fd_list = $this->redis->getFdList();
        unset($fd_list[array_search( $fd ,$fd_list)]);
        $list = array();
        foreach ($fd_list as $f) {
            $list[$f] = $this->redis->getUserInfo( $f , "name");
        }
        $data = json_encode( array(
            'op' => 'onlineList',
            'list' => $list
        ));
        $this->sendMessage( array( $fd ), $data );
    }

    //
    private function sendMessage( $fd_list , $msg ) {
 		$server = $this->server;
        $data = array(
            'ctrl' => 'Chat',
            'task' => 'sendMessage',
            'data' => array(
                'list' => $fd_list,
                'data' => $msg
            )
           
        );
        $server->task( \json_encode( $data ) );
    }
}