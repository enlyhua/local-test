<?php

class Client
{
	private $client;


	public function __construct() {
		$this->client = new swoole_client(SWOOLE_SOCK_TCP);
	}

	//1.eof
	/*public function connect() {
		if( !$this->client->connect("127.0.0.1", 9501 , 1) ) {
			echo "Error: {$fp->errMsg}[{$fp->errCode}]\n";
		}

		$msg_eof = "This is a Msg\r\n";

		$i = 0;
		while( $i < 100 ) {
			$this->client->send( $msg_eof );
			$i ++;
		}
	}*/

	//2.固定爆头
	public function connect() {
		if( !$this->client->connect("127.0.0.1", 9501 , 1) ) {
			echo "Error: {$fp->errMsg}[{$fp->errCode}]\n";
		}

		$msg_normal = "This is a Msg";
		//需要在数据的前面加上协议头，也就是长度信息, 加上 N 选项，pack 后为一个二进制流，长度加上内容
		$msg_length = pack("N" , strlen($msg_normal) ). $msg_normal;

		$i = 0;
		while( $i < 100 ) {
			$this->client->send( $msg_length );
			$i ++;
		}
	}
}

$client = new Client();
$client->connect();

