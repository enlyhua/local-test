<?php
/**
 * Created by PhpStorm.
 * User: weijianhua
 * Date: 18/6/12
 * Time: 下午11:38
 */

class BaseProcess1
{
    private $process;

    public function __construct()
    {
        // false 不需要重定向输入输出, true 但是需要开启管道
        $this->process = new swoole_process([$this, 'run'], false, true);

        //消息队列不支持异步，只能是同步方式,所以这里没有使用 event 事件, 指定 $key = 123
        if (!$this->process->useQueue(123)) {
            var_dump(swoole_strerror(swoole_errno()));
            exit;
        }

        // 暂时不需要是守护进程
//        $this->process->daemon(true,true);
        $this->process->start();

        while (1) {
            //死循环，不停的从队列读取数据
            $data = $this->process->pop();
            echo 'Receive : '. $data.PHP_EOL;
        }
    }

    public function run($worker)
    {
        //加个定时器，每秒往管道写 hello
        swoole_timer_tick(1000, function($timer_id) {
            static $index = 0;
            $index = $index + 1;
            //子进程每秒往队列 写  hello
            $this->process->push('Hello');
            var_dump($index);

            // 写了10次之后，关闭这个定时器
            if ($index == 10) {
                swoole_timer_clear($timer_id);
            }
        });
    }
}

$baseProcess = new BaseProcess1();

//监听子进程退出信号
swoole_process::signal(SIGCHLD, function($sig) {
    //必须为 false, 非阻塞模式
    while ($ret = swoole_process::wait(false)) {
        echo 'Pid : '.$ret['pid'].PHP_EOL;
    }
});