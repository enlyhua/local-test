<?php
/**
 * Created by PhpStorm.
 * User: weijianhua
 * Date: 18/8/11
 * Time: 下午10:29
 */

require_once dirname(__FILE__) . '/Classes/PHPExcel.php';

// 通用评论
$inputFileName = __DIR__ . '/inFile/3.xlsx';

$outputFileName = __DIR__ . '/outFile/3.txt';

$inputFileType = PHPExcel_IOFactory::identify($inputFileName);
$objReader = PHPExcel_IOFactory::createReader($inputFileType);
$objPHPExcel = $objReader->load($inputFileName);
$sheet = $objPHPExcel->getSheet(0);
$data = $sheet->toArray();


try {
    $dbms='mysql';     //数据库类型
    $host='localhost'; //数据库主机名
    $dbName='test';    //使用的数据库
    $user='root';      //数据库连接用户名
    $pass='123456';          //对应的密码
    $dsn="$dbms:host=$host;dbname=$dbName";

    $dbh = new PDO($dsn, $user, $pass); //初始化一个PDO对象

    $textStr = '';

    foreach ($data as $index=>$value) {
        $textStr .= $value[0] . PHP_EOL;
    }

    file_put_contents($outputFileName, $textStr);


} catch (\Exception $e) {
    var_dump($e->getMessage());
    die('exception');
}