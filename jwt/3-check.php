<?php
/**
 * Created by PhpStorm.
 * User: WeiDaoDao
 * Date: 2019/3/23
 * Time: 5:29 PM
 */

$token = $_COOKIE['x-access-token'];

$jwt = explode('.', $token);

//$header =  json_decode(base64_decode($jwt[0]), true);
//$payload = json_decode(base64_decode($jwt[1]), true);

//$headerString  = base64_encode(json_encode($header));
//$payloadString = base64_encode(json_encode($payload));
$headerString = $jwt[0];
$payloadString = $jwt[1];
$user_string = $jwt[2];

$encodedString = $headerString . '.' . $payloadString;

$secret  = 'localhost';
$known_string = hash_hmac('sha256', $encodedString, $secret);

$result = hash_equals($known_string, $user_string);

var_dump($user_string);
var_dump($known_string);
var_dump($result);