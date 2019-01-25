<?php
/**
 * Created by PhpStorm.
 * User: weijianhua
 * Date: 18/6/14
 * Time: 上午12:34
 */

$serv = new swoole_http_server('127.0.0.1', 9501);

// 设置进程数量
$serv->set(
    ['worker_num' => 1]
);

// 设置进程名
$serv->on('Start', function() {
    swoole_set_process_name('simple_route_master');
});

$serv->on('ManagerStart', function() {
    swoole_set_process_name('simple_route_manager');
});

$serv->on('WorkerStart', function() {
    // 在 worker 进程启动的时候，注册自动加载函数
    swoole_set_process_name('simple_route_worker');
    var_dump(spl_autoload_register(function($class){
        $baseClassPath = \str_replace('\\', DIRECTORY_SEPARATOR, $class).'.php';
        $classPath = __DIR__.'/'.$baseClassPath;
        echo '111111'.PHP_EOL;
        var_dump($baseClassPath);
        var_dump($classPath);
        echo '222222'.PHP_EOL;
        if (is_file($classPath)) {
            require $classPath;
            return;
        }
    }));
});

$serv->on('Request', function($request, $response) {
    $path_info = explode('/', $request->server['path_info']);

    if (isset($path_info[1]) && !empty($path_info[1])) {
        $ctrl = 'ctrl\\'.ucfirst($path_info[1]);
    } else {
        $ctrl = 'ctrl\\Index';
    }

    if (isset($path_info[2])) {
        $action = $path_info[2];
    } else {
        $action = 'index';
    }

    $result = 'Ctrl not found';

    if (class_exists($ctrl)) {
        $class = new $ctrl;
        $result = 'Action not found';

        if (method_exists($class, $action)) {
            $result = $class->$action($request);
        }

        $response->end($result);
    }
});

$serv->start();