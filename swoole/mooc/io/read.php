<?php
/**
 * Created by PhpStorm.
 * User: weijianhua
 * Date: 18/6/29
 * Time: 下午10:07
 */

$result = swoole_async_readfile(__DIR__ . '/1.txt', function($filename, $content){
    echo 'filename : ' . $filename . PHP_EOL;
    echo 'content : ' . $content . PHP_EOL;

});


var_dump($result);

echo 'start ' . PHP_EOL;