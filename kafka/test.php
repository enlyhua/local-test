<?php
/**
 * Created by PhpStorm.
 * User: weijianhua
 * Date: 18/8/29
 * Time: 下午6:58
 */

$rk = new RdKafka\Producer();
$rk->setLogLevel(LOG_DEBUG);
$rk->addBrokers("47.93.76.136");
$topic = $rk->newTopic("test");
$message = 'tese,test';
$topic->produce(RD_KAFKA_PARTITION_UA, 0, json_encode($message));

var_dump($topic);
die;