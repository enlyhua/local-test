<?php
/**
 * Created by PhpStorm.
 * User: weijianhua
 * Date: 18/6/29
 * Time: 下午10:17
 */

$content = date('Y-m-d H:i:s');

swoole_async_writefile(__DIR__ . '/1.log', $content, function($filename) {
    echo 'write ok' . PHP_EOL;
}, FILE_APPEND);

echo 'start writefile' . PHP_EOL;