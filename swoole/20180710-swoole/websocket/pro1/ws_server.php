<?php
/**
 * Created by PhpStorm.
 * User: weijianhua
 * Date: 18/6/15
 * Time: 下午10:15
 */

class Server
{
    private $server;

    private $pdo;

    public function __construct()
    {
        $this->server = new swoole_websocket_server(HOST, PORT);
        $this->server->set(
            [
                'worker_num'=>8,
                'dispatch_mode'=>2,
                'daemonize'=>0,
            ]
        );

        $this->server->on('Message', [$this, 'onMessage']);
        $this->server->on('Open', [$this, 'update']);
        $this->server->on('WorkerStart', [$this, 'onWorkerStart']);
//        $this->server->on('handshake', [$this, 'user_handshake']);
        $this->server->start();
    }

    public function onWorkerStart(swoole_server $server, $worker_id)
    {
        // 不区分 worker 还是 task worker
        $dbms='mysql';     //数据库类型
        $host='localhost'; //数据库主机名
        $dbName='test';    //使用的数据库
        $user='root';      //数据库连接用户名
        $pass='123456';          //对应的密码
        $dsn="$dbms:host=$host;dbname=$dbName";
        $this->pdo = new PDO($dsn, $user, $pass);

        //给第一个worker 进程设置定时器
        if ($worker_id == 0) {
            $this->server->tick(500, [$this, 'onTick']);
        }

        //每个 worker 对象都绑定了 pdo 连接
        $this->pdo = new PDO($dsn, $user, $pass);
    }

    public function user_handshake(swoole_http_request $request, swoole_http_response $response)
    {
        //自定义握手规则，没有设置则用系统内置的(只支持写 version : 13 的)
        if (!isset($request->header['sec-websocket-key'])) {
            $response->end();
            return false;
        }

        if (0 === preg_match('#^[+/0-9A-Za-z]{21}[AQgw]==$#', $request->header['sec-websocket-key'])
            || 16 !== strlen(base64_decode($request->header['sec-websocket-key']))
        ) {
            $response->end();
            return false;
        }

        $key = base64_encode(sha1($request->header['sec-websocket-key'], true));
        $header = [
            'Upgrade' => 'websocket',
            'Connection' => 'Upgrade',
            'Sec-websocket-Accept' => $key,
            'Sec-websocket-Version' => '13',
            'KeepAlive' => 'off'
        ];

        foreach ($header as $key => $val) {
            $response->header($key, $val);
        }

        $response->status(101);
        $response->end();
        return true;
    }

    public function onMessage(swoole_websocket_server $server, $frame)
    {
        $this->update();
    }

    /**
     * 当握手成功后调用这个方法
     * 将数据库的数据读取出来，发送给所有客户端
     */
    public function update()
    {
        global $cfg_table;
        $result = [];

        foreach ($cfg_table as $table => $field) {
            $result[$table] = $this->select($table, $field);
        }

        var_dump($result);

        foreach ($this->server->connections as $connection) {
            $this->server->push($connection, json_encode($result));
        }
    }

    public function select($table, $field)
    {
        $field_list = implode(',', $field);
        $sql = "select {$field} from {$table}";

        try {
            $statement = $this->pdo->prepare($sql);
            $statement->execute();
            $result = $statement->fetAll(PDO::FETCH_ASSOC);
            if ($result == false) {
                return [];
            }
            return $result;
        } catch (\Exception $e) {
            return [];
        }
    }

    /**
     * onTick 函数每次运行都会从 tmp_record 读取 是否升级
     */
    public function onTick()
    {
        $sql = "select is_update from tmp_record limit 1";
        $update = 'update tmp_record set is_update = 0';

        try {
            $statement = $this->pdo->prepare($sql);
            $statement->execute();
            $result = $statement->fetch(PDO::FETCH_ASSOC);
            if ($result == false) {
                return ;
            }

            if ($result['is_update'] == 1) {
                // 说明 db 数据有变化
                $this->update();
            }

            $statement = $this->pdo->prepare($update);
            $statement->execute();
        } catch (\Exception $e) {

        }
    }
}