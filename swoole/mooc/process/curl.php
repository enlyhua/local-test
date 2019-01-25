<?php
/**
 * Created by PhpStorm.
 * User: weijianhua
 * Date: 18/6/30
 * Time: 下午9:37
 */


$startTime = time();
echo 'start time '.$startTime . PHP_EOL;

$urls = [
    'http://www.baidu.com',
    'http://www.sina.com',
    'http://www.qq.com',
];

$workers = [];

for ($i = 0; $i < 3; $i++) {

    // 子进程
    $pro = new swoole_process(function(swoole_process $worker) use($i, $urls) {
        $content = curlData($urls[$i]);
        // 输出到管道
        var_dump($content);
//        $worker->write();

    },true);

    $pid = $pro->start();
    $workers[$pid] = $pro;
}

foreach ($workers as $process) {
    echo $process->read();
    echo ' ' . PHP_EOL;
}

// 模拟请求
function curlData($url) {
    sleep(1);
    return $url . ' success ' . PHP_EOL;
}

$endTime = time();

echo 'end time '. $endTime . PHP_EOL;

echo $endTime - $startTime;

