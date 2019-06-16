<?php
/**
 * Created by PhpStorm.
 * User: WeiDaoDao
 * Date: 2019/3/23
 * Time: 4:00 PM
 */
/**
第一次认证：
1.第一次登录，用户从浏览器输入用户名/密码，提交后到服务器的登录处理的Action层（Login Action）；
2.Login Action调用认证服务进行用户名密码认证，如果认证通过，Login Action层调用用户信息服务获取用户信息（包括完整的用户信息及对应权限信息）；Login Action调用认证服务进行用户名密码认证，如果认证通过，Login Action层调用用户信息服务获取用户信息（包括完整的用户信息及对应权限信息）；
3.返回用户信息后，Login Action从配置文件中获取Token签名生成的秘钥信息，进行Token的生成；返回用户信息后，Login Action从配置文件中获取Token签名生成的秘钥信息，进行Token的生成；
4.生成Token的过程中可以调用第三方的JWT Lib生成签名后的JWT数据；生成Token的过程中可以调用第三方的JWT Lib生成签名后的JWT数据；
5.完成JWT数据签名后，将其设置到COOKIE对象中，并重定向到首页，完成登录过程；
 */

require_once __DIR__ . '/../vendor/autoload.php';

$name = $_POST['name'];
$password = $_POST['password'];

if (empty($name) || empty($password)) die('name or password not null');

$header = [
    'typ' => 'JWT', //声明类型，这里是jwt
    'alg' => 'HS256', //声明加密的算法 通常直接使用 HMAC SHA256
];

//验证通过，生成 token
$payload = [
    "iss" => "John Wu JWT",        // Issuer，该JWT的签发者，个人理解随意定义
    'sub' => 'sub: jwt所面向的用户', //jwt所面向的用户
    "aud"=> "localhost",    // Audience，该JWT的接收者，个人理解服务器标识，可以写你的域名
    "exp"=> time()+7200,          // Expiration Time，过期时间
    "nbf"=> time()+60,     //该时间之前不接收处理该Token
    "iat"=> time(),          // issued At签发时间
    "uid"=> $name,//以下是用户id等信息，不要放敏感信息和太多信息，一般放用户id，和用户名
    "username"=> $password,
    'mobile' => '13122315219',
    'app' => 'xyf',
];

$headerString  = base64_encode(json_encode($header));
$payloadString = base64_encode(json_encode($payload));
$encodedString = $headerString . '.' . $payloadString;


$secret  = 'localhost';

$signature = hash_hmac('sha256', $encodedString, $secret, false);
$token = $encodedString . '.' . $signature ;
echo strlen($token);

setcookie('x-access-token', $token,time() + 7200);//将生成的token设置到cookie里