<?php
use ZPHP\ZPHP;
$rootPath = dirname(__DIR__);
require '/Users/weijianhua/Sites/test/swoole/zphp-master'. DIRECTORY_SEPARATOR . 'ZPHP' . DIRECTORY_SEPARATOR . 'ZPHP.php';
ZPHP::run($rootPath);