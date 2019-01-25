<?php
/**
 * Created by PhpStorm.
 * User: weijianhua
 * Date: 18/6/12
 * Time: 下午11:57
 */

class ProcessPool
{
    //当任务的数目大于进程的数据，就会动态扩展,没有做进程的关闭
    //注意子进程变成僵尸进程


    private $process;

    private $process_list = []; // 子进程数组
    private $process_use = []; // 标记进程是否在使用中
    private $min_worker_num = 3; // 动态进程池的最大最小数目
    private $max_worker_num = 6;

    private $current_num ; // 当前 worker 数据

    public function __construct()
    {
        $this->process = new swoole_process([$this, 'run'], false, 2);
        $this->process->start();

        swoole_process::wait();
    }

    public function run()
    {
        $this->current_num = $this->min_worker_num;

        //首先创造任务进程池，创建 3个 worker 进程
        for ($i = 0; $i < $this->current_num; $i++) {
            $process = new swoole_process([$this, 'task_run'], false, 2);
            //1.首先启动一个父进程
            $pid = $process->start();
            $this->process_list[$pid] = $process;
            $this->process_use[$pid] = 0;
        }

        //给 worker 进程添加事件回调
        //需要读到里面发出来的消息
        foreach ($this->process_list as $process) {
            swoole_event_add($process->pipe, function($pipe) use ($process) {
                //当子进程处理完任务后，告诉父进程我已经闲下来了
                $data = $process->read();
                echo '1111'.PHP_EOL;
                var_dump($data);
                $this->process_use[$data] = 0;
            });
        }

        //在父进程中， 通过 tick 方法去分发任务
        // 每1秒发送一个任务
        // 定时器不是硬件时间，epoll 的 timeout
        swoole_timer_tick(1000, function($timer_id) {
            static $index = 0;
            $index = $index + 1;
            $flag = true;

            //当它发送时，会遍历 use 数组，查看哪个进程是没有被使用的
            // 如果找到，标记为1，并发任务发送给它
            foreach ($this->process_use as $pid=>$used) {
                if ($used == 0) {
                    $flag = false;
                    $this->process_use[$pid] = 1;
                    $this->process_list[$pid]->write($index.' Hello.');
                    break;
                }
            }


            //如果当期所有子进程都在忙,而且当期进程池并没有满的情况下
            if ($flag && $this->current_num < $this->max_worker_num) {
                //就会启动新的进程,这个进程会处理新的任务
                $process = new swoole_process([$this, 'task_run'], false, 2);
                $pid = $process->start();
                $this->process_list[$pid] = $process ;
                $this->process_use[$pid] = 1;
                $this->process_list[$pid]->write($index.' Hello ');
                echo ' '.PHP_EOL;
                $this->current_num++;
            }

            echo '2222222222222222222'.PHP_EOL;
            var_dump($index);
            echo '5555555555555555555'.PHP_EOL;

            //如果执行完所有的10次任务之后，关闭所有的子进程,关闭当前的定时器,并且推出当前的子进程
            if ($index == 10) {
                foreach ($this->process_list as $process) {
                    $process->write("exit");
                }
                swoole_timer_clear($timer_id);
                $this->process->exit();
            }
        });
    }

    //任务,通过管道读到了来自父进程的任务之后,就进行处理
    public function task_run($worker)
    {
        //可以用这个的方式实现连接池,可以在这里创建 pdo 连接 , 接收 sql ，pdo 处理, 但是太复杂
        //process 连接池， task worker 连接池 区别 ：
        // task worker 数据是固定 的,新版本 swoole 也提供了动态设置
        // process 实现的话，不够稳定, 主进程另外还需要有通路接收任务, task 就比较简单，本身就是一个服务，直接常驻内存
        // 可以直接通过 tcp 来接收请求, 所以真正做个任务连接池，最好还是 task 连接池
        // swoole_process->exec 可以执行外部程序,当命令是持续的，比如 top，通过管道,做成外部监控器
        // task 更专注于执行一个异步任务

        // 在 swoole_server 里面，task 进程和 worker 进程共用了同一个 reactor ,包括发送消息的缓存使用的是同一个
        // 如果说 task 里面的任务使用非常长，比如一个 task 可能要执行一分钟，但是另外一边来自服务器的任务可能几秒就有一个
        // 这样就会导致大量的 task 任务阻塞在进程间通信的队列上，worker和 task 进程通信的队列上，如果这个缓冲区满了，worker 进程
        // 也没办法读来自客户端的消息了,这个服务相当于挂了，所以 task 不能执行非常长的任务
        swoole_event_add($worker->pipe, function($pipe) use ($worker) {
            $data = $worker->read();

            var_dump('aaaaaaaaaaaaaaaaaaaa'.PHP_EOL);
            var_dump($worker->pid . " : ". $data);

            if ($data == 'exit') {
                $worker->exit();
                exit;
            }
            //假设需要5秒处理完任务
            sleep(5);
            //返回告诉父进程我已经执行完毕了
            $worker->write("". $worker->pid);
        });
    }
}

$ProcessPool = new ProcessPool();