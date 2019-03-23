<?php
/**
 * Created by PhpStorm.
 * User: WeiDaoDao
 * Date: 2019/3/19
 * Time: 11:52 PM
 */

$serv = new Swoole\Server('0.0.0.0', 9501, SWOOLE_BASE, SWOOLE_SOCK_TCP);

$serv->on('WorkerStart', function() {
    echo 'onWorkerStart'.PHP_EOL;
});

$serv->on('Start', function() {
    echo 'start' . PHP_EOL;
});

$serv->on('Task', function() {
    echo 'onTask' . PHP_EOL;
});

$serv->start();