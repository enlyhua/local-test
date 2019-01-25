<?php
/**
 * Created by PhpStorm.
 * User: weijianhua
 * Date: 18/6/15
 * Time: 下午11:44
 */

class Server
{
    private $server;
    private $process;

    private $async_process = [];

    public function __construct()
    {
        $this->server = new swoole_websocket_server(HOST, PORT);
        $this->server->set(
            [
                'worker_num' => 2,
                'dispatch_mode' => 2,
                'daemonize' => 0,
            ]
        );

        $this->server->on('message', [$this, 'onMessage']);
        // 绑定了 onRequest ,说明可以接收 http 请求
        $this->server->on('request', [$this, 'onRequest']);
        $this->server->on('workerstart', [$this, 'onWorkerStart']);

        $this->process = new swoole_process([$this, 'onProcess'], true);
        //这相当于编程了一个永久的工作进程,会去运行来自客户端的命令请求
        $this->server->addProcess($this->process);
        $this->server->start();
        //必须为 false ,非阻塞模式
    }

    public function onWorkerStart(swoole_server $server, $worker_id)
    {
        swoole_process::signal(SIGCHLD, function($sig) {
            //必须为 false ,非阻塞模式
            while ($ret = swoole_process::wait(false)) {
                echo 'pid : '.$ret['pid'].PHP_EOL;
            }
        });
    }

    /**
     * 获取客户端请求
     * @param swoole_websocket_server $server
     * @param $frame
     */
    public function onMessage(swoole_websocket_server $server, $frame)
    {
        var_dump($frame->data);
        $data = json_decode($frame->data,true);
        var_dump($data);
        //读到客户的命令
        $cmd = $data['cmd'];

        $is_block = isset($data['is_block'])?$data['is_block']:0;
        // 如果是长时间的命令，需要临时创建一个进程去运行
        if ($is_block) {
            if (isset($this->async_process[$frame->fd])) {
                $process = $this->async_process[$frame->fd];
            } else {
                $process = new swoole_process([$this,'onTmpProcess'],true,2);
                $process->start();
                $this->async_process[$frame->fd] = $process;
                //将这个进程的管道设置到事件监听当中,监听子进程发送给父进程的数据
                //将这个数据发送给对应的客户端,客户端就可以收到命令运行的结果
                swoole_event_add($process->pipe, function() use ($process,$frame) {
                    $data = $process->read();
                    var_dump($data);
                    $this->server->push($frame->fd,$data);
                });
            }
            //将实际命令通知给子进程
            $process->write($cmd);
            sleep(1);
        } else {
            $this->process->write($cmd);
            $data = $this->process->read();
            $this->server->push($frame->fd, $data);
        }
    }

    public function onTmpProcess(swoole_process $worker)
    {
        //读取实际的命令
        $cmd = $worker->read();
        $handle = popen($cmd, 'r');
        //设置管道,如果读到父进程的数据，就会通过句柄写入到命令中，gdb 可以运行新的命令
        swoole_event_add($worker->pipe, function() use ($worker, $handle) {
            $cmd = $worker->read();
            if ($cmd == 'exit') {
                $worker->exit();
            }
            fwrite($handle,$cmd);
        });

        //读取命令发出来的消息
        while (!feof($handle)) {
            $buffer = fread($handle,18192);
            echo $buffer.PHP_EOL;
        }
    }

    public function onProcess(swoole_process $worker)
    {
        while (true) {
            $cmd = $worker->read();
            if ($cmd == 'exit') {
                $worker->exit();
                break;
            }

            passthru($cmd);
        }
    }


    /**
     * 客户端可以通过 http 请求我们的服务
     * @param swoole_http_request $request
     * @param swoole_http_response $response
     */
    public function onRequest(swoole_http_request $request, swoole_http_response $response)
    {
        $path_info = $request->server['path_info'];
        if ($path_info === 'shell.html') {
            $request->end(file_get_contents('shell.html'));
        }

        //这里可以将结果进行广播
        //要区分这个是个 websocket 连接还是 http 连接, 因为 http 连接不能 push

        foreach ($this->server->connections as $connection) {
            // 先获取所有连接,每个连接都获取它的连接信息
            $connection_info = $server->connection_info($connection);
            //如果设置了 websocket_status , 并且等于 WEBSOCKET_STATUS_FRAME, 就是 websocket 连接
            //这样就可以 在 websocket 服务中内置了 http 服务, 双协议
            if (isset($connection_info['websocket_status'])
                && $connection_info['websocket_status'] == WEBSOCKET_STATUS_FRAME) {
                //ws
                $this->server->push($connection,json_encode($result));
            }
        }
    }
}