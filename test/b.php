<?php

function autoload2($className)
{
    echo '2222<br/>';

    echo $className . '<br/>';

    $path = __DIR__ . '/' . $className . '.php';
    echo $path . '<br/>';

    if (is_file($path)) {
        echo '路径是 2222： ' . $path . '<br/>';
        require $path;
    }
}

spl_autoload_register('autoload2');