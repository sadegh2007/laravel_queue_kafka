<?php

namespace Sadeq\LaravelQueueKafka\Queue;

use Exception;
use Illuminate\Queue\Queue;
use Illuminate\Support\Str;
use Illuminate\Container\Container;
use Sadeq\LaravelQueueKafka\Jobs\KafkaJob;
use Sadeq\LaravelQueueKafka\ProducerHandler;
use Illuminate\Contracts\Queue\Queue as QueueContract;

class KafkaQueue extends Queue implements QueueContract
{
    protected $producer;

    /**
     * The name of the default queue.
     *
     * @var string
     */
    protected $topics;

    /**
     * @var string
     */
    protected $payload;

    public function __construct(ProducerHandler $producer, $topics, $payload = null)
    {
        $this->producer = $producer;
        $this->topics = $topics;
        $this->payload = $payload;
    }

    public function size($queue = null)
    {
        return $this->producer->getProducer()->getOutQLen();
    }

    public function push($job, $data = '', $queue = null)
    {
        $this->pushRaw($this->createPayload($job, $queue, $data), $queue);
    }

    public function pushRaw($payload, $queue = null, array $options = [])
    {
        try {
            $this->producer->setTopic($this->topics)
                ->send(
                    $payload,
                    Str::uuid(),
                    $options
                );
        } catch (Exception $ex) {
        }
    }

    public function later($delay, $job, $data = '', $queue = null)
    {
        // TODO: Implement later() method.
    }

    public function pop($queue = null)
    {
        return new KafkaJob(
            $this->container ?? Container::getInstance(),
            $this,
            $this->payload,
            null,
            $this->getConnectionName(),
            $queue ?? $this->topics
        );
    }

    public function setPayload($payload)
    {
        $this->payload = $payload;
        return $this;
    }
}

