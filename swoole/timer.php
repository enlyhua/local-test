<?php
/**
 * Created by PhpStorm.
 * User: weijianhua
 * Date: 18/6/10
 * Time: 下午9:03
 */

swoole_timer_tick(2000, function($timer_id) {
    echo 'tick-2000 ms'.PHP_EOL;
});

swoole_timer_after(3000, function() {
    echo 'after 3000 ms'.PHP_EOL;
});