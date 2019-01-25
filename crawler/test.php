<?php
/**
 * Created by PhpStorm.
 * User: WeiDaoDao
 * Date: 2018/11/19
 * Time: 5:44 PM
 */

$url = 'http://www.wangshu.la/books/106/106859/23527427.html';

//网页内容
$content = file_get_contents($url);

//小说内容
$patternContent = '/\<dd id="contents"\>([\s\S]*?)\<\/dd\>/';

$countContent = preg_match($patternContent, $content, $result1);

if ($countContent ==0 || $countContent === false) {
    die('出错');
}

//小说标题
$patternTitle = '/\<h[1]\>([\s\S]*?)\<\/h[1]\>/';
$countTitle = preg_match($patternTitle, $content, $result2);

print_r($result2[1]);
echo '<br/>';
print_r($result1[1]);

die;

$body = print_r($result[1],true);

file_put_contents('./aa.txt',$body);