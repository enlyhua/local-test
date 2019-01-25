<?php
/**
 * Created by PhpStorm.
 * User: weijianhua
 * Date: 18/7/18
 * Time: 下午3:53
 */

require '../vendor/autoload.php';

use GeoIp2\Database\Reader;

echo '<pre>';

$dbFile = __DIR__ . '/lib/GeoLite2-Country.mmdb';

$reader = new Reader($dbFile);

//$ip = '61.216.152.36'; // 台湾
$ip = '180.167.169.218'; // 上海

$record = $reader->country($ip);
echo '<pre>';
var_dump($record->country);



