<?php
/**
 * Created by PhpStorm.
 * User: weijianhua
 * Date: 17/8/10
 * Time: 下午9:54
 */

class Kafka
{
    public $broker_list = "106.15.74.99";
    public $topic = "topic";
    public $partition = 0;
    public $logFile = "/tmp/kafka.log";

    protected $producer = null;
    protected $consumer = null;

    /**
     * Kafka constructor.
     */
    public function __construct()
    {
        if ( empty($this->broker_list) ) {
            throw new Exception("broker not config");
        }
        $rk = new \RdKafka\Producer();
        if ( empty($rk) ) {
            throw new Exception("producer error.");
        }
        $rk->setLogLevel(LOG_DEBUG);
        if ( !$rk->addBrokers($this->broker_list) ) {
            throw new Exception("producer add broker error.");
        }
        $this->producer = $rk;
    }

    /**
     * @param array $message
     * @return mixed
     */
    public function send($message = [])
    {
        $topic = $this->producer->newTopic($this->topic);
        return $topic->produce(RD_KAFKA_PARTITION_UA, $this->partition, json_encode($message));
    }

    /**
     * @param $object
     * @param $callback
     */
    public function consumer($object, $method)
    {
        $conf =new \Rdkafka\Conf();
        $conf->set('group.id', 0);
        $conf->set('metadata.broker.list', $this->broker_list);
        $topicConf = new \RdKafka\TopicConf();
        $topicConf->set('auto.offset.reset','smallest');
        $conf->setDefaultTopicConf($topicConf);
        $consumer = new \RdKafka\KafkaConsumer($conf);
        $consumer->subscribe(array($this->topic));
        echo 'waiting for messages ... \n';
        while (true) {
            $message = $consumer->consume(120*1000);
            switch ($message->err) {
                case RD_KAFKA_RESP_ERR_NO_ERROR :
                    echo 'message payload \n'.PHP_EOL;
                    error_log('kafka consumer log'.PHP_EOL,3,'/tmp/kafka.log');
                    error_log(print_r($message->payload,true).PHP_EOL,3,'/tmp/kafka.log');
                    call_user_func_array(array($object,$method),array($message->payload));
                    break;
                default :
                    echo 'no message'.PHP_EOL;
                    break;
            }
            sleep(1);
        }
    }
}