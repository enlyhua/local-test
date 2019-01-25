<?php
/**
 * Created by PhpStorm.
 * User: weijianhua
 * Date: 18/6/27
 * Time: 下午10:57
 */

class Ws
{
    const HOST = '0.0.0.0';
    const PORT = 8812;

    public $ws = null;

    public function __construct()
    {
        $this->ws = new swoole_websocket_server('0.0.0.0', 9501);
        $this->ws->set(
            [
                'task_worker_num'=>4,
            ]
        );
        $this->ws->on('Open', [$this, 'onOpen']);
        $this->ws->on('Message', [$this, 'onMessage']);
        $this->ws->on('Close', [$this, 'onClose']);
        $this->ws->on('Task', [$this, 'onTask']);
        $this->ws->on('Finish', [$this, 'onFinish']);

        $this->ws->start();
    }


    /**
     * 监听 ws 连接事件
     * @param $ws
     * @param $request
     */
    public function onOpen($ws, $request)
    {
        var_dump($request->fd);

    }

    /**
     * 监听 ws 消息事件
     * @param $ws
     * @param $frame
     */
    public function onMessage($ws, $frame)
    {
        echo 'ser-push-message : '.$frame->data.PHP_EOL;
        $data = [
            'task' => 1,
            'fd' => $frame->fd,
        ];
        $ws->task($data);//异步
        $ws->push($frame->fd, 'Server Push : '. date('Y-m-d H:i:s').PHP_EOL);
    }

    /**
     * close
     * @param $ws
     * @param $fd
     */
    public function onClose($ws, $fd)
    {
        echo 'Client : '. $fd.PHP_EOL;
    }

    public function onTask($server, $task_id, $work_id, $data)
    {
        echo 'task : '. PHP_EOL;
        var_dump($data);
        // 耗时场景
        sleep(10);
        return 'task finish';
    }

    public function onFinish($server, $task_id, $data)
    {
        echo 'task ID : ' .$task_id .PHP_EOL;
        echo 'finish data: '.$data.PHP_EOL;
    }
}

$obj = new Ws();