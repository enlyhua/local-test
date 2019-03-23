<?php

mt_rand(0,1);

$worker_num = 16;

for ($i = 0; $i < $worker_num; $i++) {
    $process = new swoole_process('child_async', false, 2);
    $pid = $process->start();
}

function child_async(swoole_process $worker)
{
    mt_srand();
    echo mt_rand(0,100) . PHP_EOL;
    $worker->exit();
}