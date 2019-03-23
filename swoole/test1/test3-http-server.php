<?php
/**
 * Created by PhpStorm.
 * User: WeiDaoDao
 * Date: 2019/3/16
 * Time: 4:25 PM
 */

$http = new swoole_http_server('0.0.0.0', 9501);

$http->on('request', function($request, $response) {
    var_dump($request->get, $request->post);
    $response->header('Content-Type', 'text/html;charset=utf-8');
    $response->end('<h1>Hello,Swoole' . rand(1000,9999).'</h1>');
});
$http->start();