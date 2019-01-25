<?php
/**
 * Created by PhpStorm.
 * User: weijianhua
 * Date: 18/7/1
 * Time: 上午12:28
 */

// 创建内存表
$table = new swoole_table(1024);


// 增加一列
$table->column('id', swoole_table::TYPE_INT);
$table->column('name', swoole_table::TYPE_STRING, 64);
$table->column('age', swoole_table::TYPE_INT);

$table->create();

//插入记录
$table->set('test', ['id' => 1, 'name' => 'weidaodao', 'age' => 18]);

$value = $table->get('test');

$table['test1'] = ['id' => 2, 'name' => 'bbbbb', 'age' => 20];

var_dump($value);

var_dump($table['test1']);

$table->incr('test','id',3);

$value3 = $table->get('test');

var_dump($value3);