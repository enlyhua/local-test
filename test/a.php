<?php

function autoload1($className)
{
    echo '1111<br/>';

    echo $className . '<br/>';

    $path = __DIR__ . '/' . $className . '.php';
    echo $path . '<br/>';
    if (is_file($path)) {
        echo '路径是 1111： ' . $path . '<br/>';
        require $path;
    }
}

spl_autoload_register('autoload1');