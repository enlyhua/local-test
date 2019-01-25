<?php
/**
 * Created by PhpStorm.
 * User: weijianhua
 * Date: 18/6/10
 * Time: 下午7:49
 */

$host = '0.0.0.0';
$port = 9501;

$server = new swoole_http_server($host, $port);

$server->on('request', function($request, $response) {
    var_dump($request->get, $request->post);
    $response->header('Content-Type', 'text/html;charset=utf-8');
    $response->end('<h1>Hello,Swoole'.rand(1000,9999).'</h1>');
});

$server->start();