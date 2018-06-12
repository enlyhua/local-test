<?php
/**
 * Created by PhpStorm.
 * User: weijianhua
 * Date: 18/6/11
 * Time: 下午10:53
 */

class Server
{
    private $serv;
    private $test;

    public function __construct()
    {
        $this->serv = new swoole_server('0.0.0.0', 9501);
        $this->serv->set(
            [
                'worker_num' => 1,
            ]
        );
        $this->serv->on('Start', [$this, 'onStart']);
        $this->serv->on('Connect', [$this, 'onConnect']);
        $this->serv->on('Receive', [$this, 'onReceive']);
        $this->serv->on('Close', [$this, 'onClose']);
        $this->serv->start();
    }

    public function onStart($serv)
    {
        echo 'onStart .'.PHP_EOL;
    }

    public function onConnect($serv, $fd, $from_id)
    {
        echo 'Client '. $fd. ' connect'.PHP_EOL;
    }

    public function onReceive(swoole_server $serv, $fd, $from_id, $data)
    {
        echo 'get message from client '.$fd.PHP_EOL;

        foreach ($serv->connections as $connection) {
            if ($fd != $connection) {
                $serv->send($connection, $data);
            }
        }
    }

    public function onClose($serv, $fd, $from_id)
    {
        echo 'client '.$fd.' close connection'.PHP_EOL;
    }
}

$server = new Server();


