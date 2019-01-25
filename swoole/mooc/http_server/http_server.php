<?php
/**
 * Created by PhpStorm.
 * User: weijianhua
 * Date: 18/6/25
 * Time: 下午10:35
 */


$http = new swoole_http_server('0.0.0.0', 8811);

$http->set(
    [
        'enable_static_handler' => true,
        'document_root' => '/Users/weijianhua/Sites/test/swoole/mooc/2',//静态资源存放路径
    ]
);

$http->on('Request', function($request, $response) {
        var_dump($request->get);
        $response->end('<h1> HTTP Server'.'</h1>');
});

$http->start();