<?php
/**
 * Created by PhpStorm.
 * User: weijianhua
 * Date: 18/7/1
 * Time: 下午4:58
 */

$config = [
    'timeout' => 1.5
];

$client = new swoole_redis($config);


$client->connect('192.168.0.105', 6379, function(swoole_redis $redis, $result) {
    if ($result === false) {
        echo 'redis connect error' . PHP_EOL;

        var_dump($redis->errCode);
        var_dump($redis->errMsg);

        return false;
    }

    $redis->set('test-redis', 'aaa', function(swoole_redis $redis, $result) {
        if ($result === false) {
            echo 'set error' . PHP_EOL;
            return false;
        }

        echo 'set success' . PHP_EOL;
    });
});

echo 'start async redis' . PHP_EOL;

$client->close();
