<?php

namespace task\Adapter;

use task\Task;
use database\RedisFactory as RFactory;
use common\PushClient;

class Chat extends Task
{

	public function __construct() {

	}

    // 根据 list 遍历所有的描述符，给每个描述符发送数据
	public function sendMessage() {
		$list = $this->params['list'];
		$data = $this->params['data'];
		foreach ($list as $fd) {
			$this->server->push( $fd , $data );
		}
	}
}