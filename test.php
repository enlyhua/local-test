<?php

class mysql
{
    public function connect($db)
    {
        echo '连接到数据库 db';
    }
}

class sqlproxy
{
    private $target;

    public function __construct($tar)
    {
        $this->target[] = new $tar();
    }

    public function __call($name, $arguments)
    {
        foreach ($this->target as $obj) {
            $r = new ReflectionClass($obj);
            if ($method = $r->getMethod($name)) {
                if ($method->isPublic() && !$method->isAbstract()) {
                    echo '方法前拦截记录';
                    $method->invoke($obj, $args);
                    echo '方法后拦截';
                }
            }
        }
    }
}

$obj = new sqlproxy('mysql');
$obj->connect('member');