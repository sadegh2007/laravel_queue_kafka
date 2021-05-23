<?php

namespace Sadeq\LaravelQueueKafka;

use RdKafka\Conf;

class KafkaHelper
{
    public static function producerConfig(): Conf
    {
        $conf = new Conf();
        $conf->set('metadata.broker.list', config('queue.connections.kafka.brokers', '127.0.0.1'));

        $producer_config = config('queue.connections.kafka.producer', []);
        foreach ($producer_config as $key => $value) {
            $conf->set($key, $value);
        }

        if (config('queue.connections.kafka.debug', false)) {
            $conf->set('log_level', config('queue.connections.kafka.log_level', LOG_DEBUG));
            $conf->set('debug', 'all');
        }

        return $conf;
    }

    public static function consumerConfig() : Conf
    {
        $conf = new Conf();
        $conf->set('metadata.broker.list', config('queue.connections.kafka.brokers', '127.0.0.1'));
        $conf->set('group.id', config('queue.connections.kafka.group_id'));

        $consumer_config = config('queue.connections.kafka.consumer', []);
        foreach ($consumer_config as $key => $value) {
            $conf->set($key, $value);
        }

        if (config('queue.connections.kafka.debug', false)) {
            $conf->set('log_level', config('queue.connections.kafka.log_level', LOG_DEBUG));
            $conf->set('debug', 'all');
        }

        return $conf;
    }
}
