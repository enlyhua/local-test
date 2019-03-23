<?php
/**
 * Created by PhpStorm.
 * User: WeiDaoDao
 * Date: 2019/3/16
 * Time: 4:50 PM
 */

$server = new swoole_server('127.0.0.1', 9501);

$server->set([
    'task_worker_num' => 4,
]);

$server->on('receive', function($server, $fd, $from_id, $data) {
    $task_id = $server->task($data);
    echo 'dispath asynctask : id = ' . $task_id . PHP_EOL;
});

$server->on('task', function($server, $task_id, $from_id, $data) {
    echo 'new asyncTask : id=' . $task_id . PHP_EOL;
    $server->finish("$data => ok");
});