<?php
/**
 * Created by PhpStorm.
 * User: weijianhua
 * Date: 18/6/14
 * Time: 上午12:30
 */

$serv = new swoole_http_server('127.0.0.1', 9502);

$serv->on('Request', function($request, $response) {
    var_dump($request->get);
    var_dump($request->post);
    var_dump($request->cookie);
    var_dump($request->files);
    var_dump($request->header);
    var_dump($request->server);

    $response->cookie('User', 'Swoole');
    $response->header('X-Server', 'Swoole');
    $response->end('<h1>Hello,Swoole</h1>');
});

$serv->start();

// 注意
// 除了 header 和 server 外， 其他4个变量可能没有赋值，因此使用前需要用 isset 判定