<?php
require_once "../vendor/autoload.php";

use Hprose\Swoole\Server;

//希望对外暴露的服务
function hello($name) {
    return "Hello $name!";
}

class Controller
{

    public function sum($a, $b)
    {
        if( !is_int($a) )
        {
            return null;
        }
        if( !is_int($b) )
        {
            return null;
        }
        return $a + $b;
    }

    public function sub(){
        
    }
}

$server = new Server("tcp://0.0.0.0:1314");
$server->setErrorTypes(E_ALL);
$server->setDebugEnabled();
$server->addFunction('hello');
//当我需要将对象中的函数，同样作为接口对外提供
//可以使用 add 方法, 这样对象中的方法都会被提供给外部调用
$server->add(new Controller());
$server->start();
