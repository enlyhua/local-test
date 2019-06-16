<?php
/**
 * Created by PhpStorm.
 * User: WeiDaoDao
 * Date: 2019/3/23
 * Time: 2:38 PM
 * https://blog.csdn.net/qq_24074585/article/details/82828413
 * https://blog.csdn.net/HobHunter/article/details/78524922
 * https://www.jianshu.com/p/576dbf44b2ae
 */

require_once __DIR__ . '/../vendor/autoload.php';

use Lcobucci\JWT\Builder;
use Lcobucci\JWT\Signer\Hmac\Sha256;
use Lcobucci\JWT\Parser;

$builder = new Builder();
$signer = new Sha256();

//设置发行人
$builder->setIssuer('http://example.com');

//设置接收人
$builder->setAudience('http://example.org');

//设置id
$builder->setId('4f1g23a12aa', true);

// 设置生成token的时间
$builder->setIssuedAt(time());

// 设置在60秒内该token无法使用
$builder->setNotBefore(time() + 60);

// 设置过期时间
$builder->setExpiration(time() + 3600);

// 给token设置一个id
$builder->set('uid', 1);

// 对上面的信息使用sha256算法签名
$builder->sign($signer, '签名key');
// 获取生成的token
$token = $builder->getToken();





$token = 'eyJpc3MiOiJKb2huIFd1IEpXVCIsInN1YiI6InN1Yjogand0XHU2MjQwXHU5NzYyXHU1NDExXHU3Njg0XHU3NTI4XHU2MjM3IiwiYXVkIjoibG9jYWxob3N0IiwiZXhwIjoxNTUzMzQwNTI4LCJuYmYiOjE1NTMzMzMzODgsImlhdCI6MTU1MzMzMzMyOCwidWlkIjoidGVzdC0wMSIsInVzZXJuYW1lIjoidGVzdCJ9.34b956057fd25cae861cc937a78bd08e1afdcb9cc1f6dda44cf3256cf083f438';

$parse = (new Parser())->parse($token);
$signer = new Sha256();
$parse->verify($signer,'签名key');// 验证成功返回true 失败false
