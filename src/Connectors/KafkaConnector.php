<?php

namespace Sadeq\LaravelQueueKafka\Connectors;

use RdKafka\Producer;
use Sadeq\LaravelQueueKafka\KafkaHelper;
use Sadeq\LaravelQueueKafka\ProducerHandler;
use Sadeq\LaravelQueueKafka\Queue\KafkaQueue;
use Illuminate\Contracts\Events\Dispatcher;
use Illuminate\Queue\Connectors\ConnectorInterface;

class KafkaConnector implements ConnectorInterface
{
    protected $config;

    protected $sleepOnError;

    protected $dispatcher;

    public function __construct(Dispatcher $dispatcher)
    {
        $this->dispatcher = $dispatcher;
    }

    public function connect(array $config): KafkaQueue
    {
        $this->config = $config;
        $this->sleepOnError = $config['sleep_on_error'] ?? 5;

        $conf = KafkaHelper::producerConfig();
        return new kafkaQueue(new ProducerHandler(new Producer($conf)), $this->config['topics']);
    }
}
