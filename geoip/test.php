<?php
/**
 * Created by PhpStorm.
 * User: weijianhua
 * Date: 18/7/18
 * Time: 下午3:16
 */

include("geoip.inc.php");


$gi = geoip_open("GeoIP.dat",GEOIP_STANDARD);

//$ip = '180.169.68.162';
//$ip = '45.77.244.201';
$ip = '69.171.235.101';

$_SERVER['REMOTE_ADDR'] = $ip;
$country_code = geoip_country_code_by_addr($gi, $_SERVER['REMOTE_ADDR']);
echo "Your country code is: $country_code ";