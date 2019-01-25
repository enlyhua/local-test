<?php
/**
 * Created by PhpStorm.
 * User: weijianhua
 * Date: 18/6/30
 * Time: 下午8:48
 */


$process = new swoole_process(function(swoole_process $pro) {
//    echo '111' . PHP_EOL;
    $pro->exec('/usr/local/Cellar/php/7.2.5/bin/php', [
        __DIR__.'/../http_server/http_server.php'
    ]);
}, false);

$pid = $process->start();

echo 'child pid ' . $pid . PHP_EOL;

//回收子进程
swoole_process::wait();