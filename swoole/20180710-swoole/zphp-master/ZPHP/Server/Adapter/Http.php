<?php
/**
 * User: shenzhe
 * Date: 13-6-17
 */


namespace ZPHP\Server\Adapter;

use ZPHP\Core;
use ZPHP\Server\IServer;
use ZPHP\Protocol;

class Http implements IServer
{

    //依赖于 nginx 请求, nginx 会把一个请求代理过来
    public function run()
    {
        Protocol\Request::setServer(
            Protocol\Factory::getInstance(
                //默认使用 http
                Core\Config::getField('Project', 'protocol', 'Http')
            )
        );
        //解析请求
        Protocol\Request::parse($_REQUEST);
        //直接进入 Route 层, 将请求转发给控制器
        return Core\Route::route();
    }

}