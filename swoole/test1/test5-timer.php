<?php
/**
 * Created by PhpStorm.
 * User: WeiDaoDao
 * Date: 2019/3/16
 * Time: 4:45 PM
 */

swoole_timer_tick(2000, function($timer_id) {
    echo 'tick - 2000ms\n';
});

swoole_timer_after(3000, function($timer_id) {
    echo 'after 3000\n';
});