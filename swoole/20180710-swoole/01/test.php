<?php
/**
 * Created by PhpStorm.
 * User: weijianhua
 * Date: 18/6/10
 * Time: 下午9:51
 */

class Test
{
    public $index = 0;
}

class Server
{
    private $serv;

    private $test;

    public function __construct()
    {
        $this->serv = new swoole_server('0.0.0.0', 9501);
        $this->serv->set(
            [
                'worker_num' => 8,
                'daemonize' => false,
                'max_request' => 10000,
                'dispatch_mode' => 2,
                'task_worker_num' => 8,
            ]
        );

        $this->serv->on('Start', [$this, 'onStart']);
        $this->serv->on('WorkerStart', [$this, 'onWorkerStart']);
        $this->serv->on('Connect', [$this, 'onConnect']);
        $this->serv->on('Receive', [$this, 'onReceive']);
        $this->serv->on('Close', [$this, 'onClose']);
        $this->serv->on('Task', [$this, 'onTask']);
        $this->serv->on('Finish', [$this, 'onFinish']);
        $this->serv->on('ManagerStart', [$this, 'onManagerStart']);
    }

    public function onWorkerStart($server, $worker_id)
    {

    }


    public function onStart($serv)
    {
        //1.开始
        echo 'onStart' . PHP_EOL;
    }

    public function onConnect($serv, $fd, $worker_id)
    {
        //2.有客户端连接进来
        echo 'Client : ' . $fd . ' connect.' . PHP_EOL;
        echo '来自 '. $worker_id . ' reactor 线程'.PHP_EOL;
    }

    public function onClose($serv, $fd, $from_id)
    {
        echo 'Client : ' . $fd . ' close connection.';
    }

    public function onReceive(swoole_server $serv, $fd, $from_id, $data)
    {
        echo 'on Receive ' . PHP_EOL;
        $this->test = new Test();
        var_dump($this->test);

        //3.从客户端接收信息
        echo '接收到来自客户端的 id 为 : ' . $fd . PHP_EOL;
        echo '来自 reactor 线程 id : '. $from_id . PHP_EOL;
        echo '接收到的数据为 : ' . $data . PHP_EOL;

        $serv->task(serialize($this->test));

        // $data 用于传递给 task 的数据
        /*$data = [
            'task' => 'task_1',
            'params' => $data,
            'fd' => $fd
        ];

        //任务投递
        $taskResult = $serv->task(json_encode($data)); // task 只能传递字符串*/
//        echo ' task result : ' . $taskResult . PHP_EOL;
        // task 只能传递对象的拷贝, 通过序列化
//        $serv->task(serialize($this->test)); // task 只能传递字符串
    }

    public function onTask($serv, $task_id, $worker_id, $data)
    {
        echo 'on Task' . PHP_EOL;

        echo 'task id 为 : '. $task_id . PHP_EOL;
        echo '来自 worker id : ' . $worker_id . PHP_EOL;

        $data = unserialize($data);

//        $data = json_decode($data, true);

        echo '接收到的数据为 : '. PHP_EOL;

        var_dump($data);
        $data->index = 2;

        echo '$this->test : '. PHP_EOL;
        var_dump($this->test);

        // 4. onTask 回调中会收到这个任务
        // 给客户端发送数据
//        $serv->send($data['fd'], ' hello Task ');

//        $data = unserialize($data);
//        $data->index = 2;
//        var_dump($data);
//        var_dump($this->test); // 这个为 null

        //返回给 worker 进程
        return 'Finish';
    }

    public function onFinish($serv, $task_id, $data)
    {
        echo 'on Finish ' . PHP_EOL;
        echo 'task id : ' . $task_id . PHP_EOL;
        echo '返回的数据为 : '. $data . PHP_EOL;
    }

    public function start()
    {
        $this->serv->start();
    }

    public function onManagerStart($server)
    {
        echo 'onManagerStart ' . PHP_EOL;
    }
}

$server = new Server();
$server->start();
