<?php
/**
 * User: shenzhe
 * Date: 13-6-17
 */


namespace ZPHP\Server\Adapter;

use ZPHP\Protocol\Request;
use ZPHP\Protocol\Factory as ZProtocol;
use ZPHP\Socket\Factory as SFactory;
use ZPHP\Core\Config;
use ZPHP\Core\Factory as CFactory;
use ZPHP\Server\IServer;

class Socket implements IServer
{
    public function run()
    {
        //读取 socket 配置
        $config = Config::get('socket');
        if (empty($config)) {
            throw new \Exception("socket config empty");
        }
        //获取socket实例
        $socket = SFactory::getInstance($config['adapter'], $config);
        if (method_exists($socket, 'setClient')) {
            //client_class, 应该叫callback_class, 我们实际创建的callback 层
            $client = CFactory::getInstance($config['client_class']);
            $socket->setClient($client);
        }
        //个体 Request 对象设置协议,我们接收到的请求走哪种具体协议
        Request::setServer(ZProtocol::getInstance(Config::getField('socket', 'protocol')));
        Request::setLongServer();
        Request::setHttpServer(0);
        // run
        $socket->run();
    }
}