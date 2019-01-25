<?php
/**
 * Created by PhpStorm.
 * User: weijianhua
 * Date: 18/6/29
 * Time: 下午10:49
 */

class AsyncMySQL
{
    public $dbSource = '';

    public $dbConfig = [];

    public function __construct()
    {
        $this->dbSource = new Swoole\Mysql();

        $this->dbConfig = [
            'host' => '127.0.0.1',
            'port' => 3306,
            'user' => 'root',
            'password' => '123456',
            'database' => 'test',
            'charset' => 'utf8'
        ];
    }

    public function update()
    {

    }

    public function add()
    {

    }

    public function execute($id, $name)
    {
        $this->dbSource->connect($this->dbConfig, function($db, $result) {

            echo '开始连接' . PHP_EOL;

            if ($result == false) {
                var_dump($db->connect_errno);
                var_dump($db->connect_error);
                die('connect error');
            }

            $sql = 'select * from test';

            $db->query($sql, function($db, $result) {

                if ($result === false) {
                    var_dump($db->error);
                    var_dump($db->errno);
                    die('query error');
                } else if ($result === true) {
                    echo '影响的行数为 : ' . $db->affected_rows . PHP_EOL;
                } else {
                    echo '查询到的数据为 : ' . PHP_EOL;
                    var_dump($result);
                }

                $db->close();

            });

        });

        return true;
    }
}

$obj = new AsyncMySQL();
$result = $obj->execute(1, 'async mysql');

var_dump($result);

echo 'start' . PHP_EOL;