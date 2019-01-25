<?php
/**
 * Created by PhpStorm.
 * User: weijianhua
 * Date: 18/7/1
 * Time: 上午12:48
 */



$http = new swoole_http_server("192.168.0.105", 9501);

$http->on("request", function ($request, $response) {

    echo 'onRequest' . PHP_EOL;

    $redis = new Swoole\Coroutine\Redis();

    $redis->connect('192.168.0.105', 6379);

    $value = $redis->get('aa');
    var_dump($value);

    //调用connect将触发协程切换
    $response->header("Content-Type", "text/plain");
    $response->end($value);
});

$http->start();

