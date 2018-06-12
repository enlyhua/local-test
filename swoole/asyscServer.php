<?php
/**
 * Created by PhpStorm.
 * User: weijianhua
 * Date: 18/6/10
 * Time: 下午9:07
 */

$host = '127.0.0.1';
$port = 9501;

$server = new swoole_server($host, $port);

$server->set(['task_worker_num' => 4]);

$server->on('receive', function($server, $fd, $from_id, $data) {
    $task_id = $server->task($data);
    echo 'Dispath AsysnTask : id = '.$task_id.PHP_EOL;
});

$server->on('task', function($server, $task_id, $from_id, $data) {
    echo 'New AsyscTask id ：'.$task_id.PHP_EOL;
    $server->finish($data.' is ok');
});

$server->on('finish', function($server, $task_id, $data) {
    echo 'AsyncTask '.$task_id.' Finish '.$data.PHP_EOL;
});

$server->start();