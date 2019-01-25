<?php
/**
 * Created by PhpStorm.
 * User: weijianhua
 * Date: 18/7/26
 * Time: 下午11:37
 */

class ProcessPool
{
    private $process;

    private $process_list = [];
    private $process_use = [];
    private $min_worker_num = 3;
    private $max_worker_num = 6;

    private $current_num ;

    public function __construct()
    {
        $this->process = new swoole_process([$this, 'run'], false, 2);
        $this->process->start();

        swoole_process::wait();
    }

    public function run()
    {
        $this->current_num = $this->min_worker_num ;

        for ( $i = 0; $i < $this->current_num; $i++) {
            $process = new swoole_process([$this, 'task_run'], false, 2);
            $pid = $process->start();
            $this->process_list[$pid] = $process;
            $this->process_use[$pid] = 0;
        }

        foreach ($this->process_list as $process) {
            swoole_event_add($process->pipe, function($pipe) use ($process) {
                $data = $process->read();
                echo '111' . PHP_EOL;
                var_dump($data);
                $this->process_use[$data] = 0;
            });
        }

        swoole_timer_tick(1000, function($timer_id) {
            static $index = 0;
            $index = $index + 1;
            $flag = true;

        });
    }
}
