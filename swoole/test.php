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
        $this->serv->on('Connect', [$this, 'onConnect']);
        $this->serv->on('Receive', [$this, 'onReceive']);
        $this->serv->on('Close', [$this, 'onClose']);
        $this->serv->on('Task', [$this, 'onTask']);
        $this->serv->on('Finish', [$this, 'onFinish']);
    }

    public function onStart($serv)
    {
        //1.开始
        echo 'onStart' . PHP_EOL;
    }

    public function onConnect($serv, $fd, $from_id)
    {
        //2.有客户端连接进来
        echo 'Client : ' . $fd . 'connect.' . PHP_EOL;
    }

    public function onClose($serv, $fd, $from_id)
    {
        echo 'Client : ' . $fd . ' close connection.';
    }

    public function onReceive(swoole_server $serv, $fd, $from_id, $data)
    {
        $this->test = new Test();
        var_dump($this->test);

        //3.从客户端接收信息
        echo 'Get Message from Client ' . $fd . ' : ' . $data . PHP_EOL;

        // $data 用于传递给 task 的数据
        $data = [
            'task' => 'task_1',
            'params' => $data,
            'fd' => $fd
        ];
        //任务投递
//        $serv->task(json_encode($data)); // task 只能传递字符串
        $serv->task(serialize($this->test)); // task 只能传递字符串
    }

    public function onTask($serv, $task_id, $from_id, $data)
    {
        echo 'This Task ' . $task_id . ' from worker ' . $from_id . PHP_EOL;
        // 4. onTask 回调中会收到这个任务
        /*echo 'This Task ' . $task_id . ' from worker ' . $from_id . PHP_EOL;
        echo 'data : ' . $data . PHP_EOL;
        $data = json_decode($data, true);
        echo 'Receice Task ' . $data['task'] . PHP_EOL;
        var_dump($data['params']);
        // 给客户端发送数据
        $serv->send($data['fd'], ' hello Task ');*/

        $data = unserialize($data);
        $data->index = 2;
        var_dump($data);
        var_dump($this->test); // 这个为 null

        //返回给 worker 进程
        return 'Finish';
    }

    public function onFinish($serv, $task_id, $data)
    {

        //5. 收到 , $data 是 onTask 返回的数据
        echo 'Task ' . $task_id . ' finish .' . PHP_EOL;
        echo 'result : ' . $data . PHP_EOL;
        var_dump($this->test); // index 还是 0, 说明传递的是对象的拷贝
    }

    public function start()
    {
        $this->serv->start();
    }
}

$server = new Server();
$server->start();
