<?php
/**
 * Created by PhpStorm.
 * User: weijianhua
 * Date: 18/6/5
 * Time: 下午11:33
 */

require '../vendor/autoload.php';


use Elasticsearch\ClientBuilder;


$client = ClientBuilder::create()->setHosts(['192.168.0.103:9200'])->build();


$params = [
    'index' => 'my_index',
    'type' => 'my_type',
    'id' => 'my_id',
    'body' => ['testField' => 'abc']
];

$response = $client->index($params);
var_dump($response);