<?php
/**
 * Created by PhpStorm.
 * User: weijianhua
 * Date: 18/7/10
 * Time: 下午5:09
 */

$server = new swoole_server('127.0.0.1', 9501);

$server->on('Start', function($server) {
    echo '1. Start'.PHP_EOL;
});

$server->on('WorkerStart', function($server, $workerID) {
    echo '2.WorerStart' . PHP_EOL;
});

$server->on();