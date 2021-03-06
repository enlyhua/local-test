<?php
/**
 * Created by PhpStorm.
 * User: weijianhua
 * Date: 18/6/14
 * Time: 下午11:06
 *
 * 文件上传
 */

$serv = new swoole_http_server('127.0.0.1', 9501);

$serv->set([
    'max_package_length' => 20000000
]);

$serv->on('Request', function($request, $response) {
    if ($request->server['request_method'] == 'GET') {
        return ;
    }

    var_dump($request->files);

    $file = $request->files['file'];
    $file_name = $file['name'];
    $file_tmp_path = $file['tmp_name'];

    $upload_path = __DIR__.'/uploader/';

    if (!file_exists($upload_path)) {
        mkdir($upload_path);
    }

    move_uploaded_file($file_tmp_path, $upload_path.$file_name);
    $response->end('<h1>Upload Success</h1>');

});

$serv->start();