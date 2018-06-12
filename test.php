<?php


$a = 0;
$b = null;

if (empty($a)) {
    echo 'a is empty';
} else {
    echo 'a is not empty';
}


if (empty($b)) {
    echo 'b is empty';
} else {
    echo 'b is not empty';
}

die;

phpinfo();

$a = '123';
echo $a;

die;
var_dump($_POST);


$ret = [
    'code'=>0,
    'msg'=>'成功'
];

echo json_encode($ret);
return;
die('123');

class ShardingHelper
{
    public static function getShardDB($id)
    {
        $id = (int)$id;
        $shard_id = ($id >> 45) & 0B111111111111;
        if ($shard_id <= 0 || $shard_id > 4096) {
            return false;
        }
        return "shard{$shard_id}";
    }
}

echo '<pre>';
$userID = '144150372447945048';

$recordingID = '288265560523802077';

var_dump(decbin($userID));
var_dump(decbin($recordingID));

die;

$ret = ShardingHelper::getShardDB($recordingID);

var_dump($ret);