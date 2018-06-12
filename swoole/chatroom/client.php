<?php
/**
 * Created by PhpStorm.
 * User: weijianhua
 * Date: 18/6/11
 * Time: 下午10:53
 *
 * 模拟聊天室广播
 */

//生成一个具体的描述符，通过 tcp 连接服务器
$socket = stream_socket_client('tcp://127.0.0.1:9501',$errno, $errstr, 30);

function onRead()
{
    global $socket;
    $buffer = stream_socket_recvfrom($socket, 1024);

    if (!$buffer) {
        echo 'Server Close'.PHP_EOL;
        swoole_event_del($socket);
    }

    echo 'Receive '. $buffer.PHP_EOL;
    fwrite(STDOUT, "Enter msg : ".PHP_EOL);
}

function onWrite()
{
    global $socket;
    echo 'on Write'.PHP_EOL;
}

function onInput()
{
    global $socket;
    $msg = trim(fgets(STDIN));

    if ($msg == 'exit') {
        swoole_event_exit();
        exit();
    }

    swoole_event_write($socket, $msg);
    fwrite(STDOUT, 'Enter msg :'.PHP_EOL);
}

swoole_event_add($socket, 'onRead', 'onWrite');
swoole_event_add(STDIN, 'onInput');

fwrite(STDOUT, 'Enter msg :');