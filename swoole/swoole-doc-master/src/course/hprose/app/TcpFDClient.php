<?php
require_once "../vendor/autoload.php";

use \Hprose\Future;
use \Hprose\Swoole\Client; //
use \HProse\Http\Client; // 提供同步方式
use \Hprose\Socket\Client; //提供同步方式, 这2个不支持协程

$test = new Client("tcp://127.0.0.1:1314");
//设置协程的一个属性
$test->fullDuplex = true;
//将一个函数打包成异步调用的包装
$var_dump = Future\wrap("var_dump");

// hproce 提供的协程调用方式
Future\co(function() use ($test) {
    try {
        //结果需要使用 yield 方式标记
        var_dump((yield $test->hello("yield world1")));
        var_dump((yield $test->hello("yield world2")));
        var_dump((yield $test->hello("yield world3")));
        var_dump((yield $test->hello("yield world4")));
        var_dump((yield $test->hello("yield world5")));
        var_dump((yield $test->hello("yield world6")));
    }
    catch (\Exception $e) {
        echo ($e);
    }
});

//刚刚包装好的异步的 var_dump 方法
//当调用完这个函数之后，不会等待这个函数返回,直接执行下面的
$var_dump($test->hello("async world1"));
$var_dump($test->hello("async world2"));
$var_dump($test->hello("async world3"));
$var_dump($test->hello("async world4"));
$var_dump($test->hello("async world5"));
$var_dump($test->hello("async world6"));

//异步的链式调用
//调用远程服务，异步回调等待结果
//如果 第二个请求依赖第一个请求，因此出现协程,可以指定运行结果出现在什么地方,就是 yield 关键字
$test->sum(1,2)
->then(function($result) use ($test) {
    //收到结果后，再次返回一个对象，然后继续一个一个的
    //这里的好处是，当我们需要依赖一些服务串行的运行时，也就是说下一步依赖上一步操作结果的时候可以使用这样的方式
    var_dump($result);
    return $test->sum($result , 1);
})
->then(function($result) use ($test) {
    var_dump($result);
    return $test->sum($result , 1);
})
->then(function($result) use ($test) {
    var_dump($result);
    return $test->sum($result, 1);
})
->then(function($result) use ($test) {
    var_dump($result);
    return $test->sum($result , 1);
})
->then(function($result) use ($test) {
    var_dump($result);
    return $test->sum($result , 1);
})
->then(function($result) {
    var_dump($result);
});
