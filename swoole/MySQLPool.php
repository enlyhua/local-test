<?php
/**
 * Created by PhpStorm.
 * User: weijianhua
 * Date: 18/6/10
 * Time: 下午10:36
 * 异步 mysql 操作
 */

class MySQLPool
{
    private $serv;
    private $pdo;

    public function __construct()
    {
        $this->serv = new swoole_server('0.0.0.0', 9501);
        $this->serv->set(
            [
                'worker_num' => 8,
                'daemonize' => false,
                'max_request' => 10000,
                'dispatch_mode' => 3,
                'task_worker_num' => 8,
            ]
        );

        $this->serv->on('WorkerStart', [$this, 'onWorkerStart']);
        $this->serv->on('Connect', [$this, 'onConnect']);
        $this->serv->on('Receive', [$this, 'onReceive']);
        $this->serv->on('Close', [$this, 'onClose']);
        $this->serv->on('Task', [$this, 'onTask']);
        $this->serv->on('Finish', [$this, 'onFinish']);
        $this->serv->start();
    }

    public function onConnect($serv, $fd, $from_id)
    {
        echo 'Client : '.$fd.'connect'.PHP_EOL;
    }

    public function onClose($serv, $fd, $from_id)
    {
        echo 'Client '. $fd . ' close connection'.PHP_EOL;
    }

    public function onWorkerStart($serv, $worker_id)
    {
        //会在 worker 创建之初被回调，并不区分自己的 worker 还是 task worker , 所以数目是 8+8 = 16
        // $serv->taskWorker 只有在 taskWorker 创建 pdo 连接
        if ($serv->taskworker) {
            $dbms='mysql';     //数据库类型
            $host='localhost'; //数据库主机名
            $dbName='test';    //使用的数据库
            $user='root';      //数据库连接用户名
            $pass='123456';          //对应的密码
            $dsn="$dbms:host=$host;dbname=$dbName";
            echo 'onWorkerStart'.PHP_EOL;
            $this->pdo = new PDO($dsn, $user, $pass);
        } else {
            echo ' Worker Process'.PHP_EOL;
        }

        // 定时器的使用
        if ($worker_id == 0) {
            swoole_timer_tick(10000, function($timer_id, $pamars) {
                echo 'timer running'.PHP_EOL;
                var_dump($pamars);
            }, 'Hello');
        }

    }

    public function onReceive(swoole_server $serv, $fd, $from_id, $data)
    {
        echo 'onReceive'.PHP_EOL;

        swoole_timer_after(3000, function() use ($serv, $fd){
            echo 'Timer after'.PHP_EOL;
            $serv->send($fd, "hello,latter".PHP_EOL);
        });

        $task = [
            'sql' => 'insert into users values (null, ?, ?, ?)',
            'params' => ['swoole', '123456', 'swoole@qq.com'],
            'fd' => $fd
        ];

        $serv->task(json_encode($task));
    }

    public function onTask($serv, $task_id, $from_id, $data)
    {
        try {
            echo 'Ontask'.PHP_EOL;
            $data = json_decode($data, true);
            var_dump($data);

            $statement = $this->pdo->prepare($data['sql']);
            $statement->execute($data['params']);
            $serv->send($data['fd'], 'insert success'.PHP_EOL);
            return true;

        } catch (\Exception $e) {
            var_dump($e->getMessage());
            return false;
        }
    }

    public function onFinish($serv, $task_id, $data)
    {
        echo 'Finish.'.PHP_EOL;
        var_dump('result : '. $data);
    }
}

$mysqlPool = new MySQLPool();
