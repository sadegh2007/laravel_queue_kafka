<?php

namespace Sadeq\LaravelQueueKafka\Jobs;

use Sadeq\LaravelQueueKafka\Queue\KafkaQueue;
use Illuminate\Container\Container;
use Illuminate\Contracts\Queue\Job as JobContract;
use Illuminate\Queue\Jobs\Job;
use Illuminate\Queue\Jobs\JobName;

class KafkaJob extends Job implements JobContract
{
    /**
     * Create a new job instance.
     *
     * @param \Illuminate\Container\Container $container
     * @param KafkaQueue $kafka
     * @param string $job
     * @param string $reserved
     * @param string $connectionName
     * @param string $queue
     * @return void
     */
    public function __construct(Container $container, KafkaQueue $kafka, $job, $reserved, $connectionName, $queue)
    {
        // The $job variable is the original job JSON as it existed in the ready queue while
        // the $reserved variable is the raw JSON in the reserved queue. The exact format
        // of the reserved job is required in order for us to properly delete its data.
        $this->job = $job;
        $this->kafka = $kafka;
        $this->queue = $queue;
        $this->reserved = $reserved;
        $this->container = $container;
        $this->connectionName = $connectionName;

        $this->decoded = $this->payload();
    }

    public function getJobId()
    {
        return $this->decoded['id'] ?? null;
    }

    public function getRawBody()
    {
        return $this->job;
    }

    public function attempts()
    {
        return ($this->decoded['attempts'] ?? null) + 1;
    }

    /**
     * @return KafkaQueue
     */
    public function getKafkaQueue(): KafkaQueue
    {
        return $this->kafka;
    }

    protected function failed($e)
    {
        $payload = json_decode($this->payload()['body'], true);

        [$class, $method] = JobName::parse($payload['job']);

        if (method_exists($this->instance = $this->resolve($class), 'failed')) {
            $this->instance->failed($payload['data'], $e, $payload['uuid'] ?? '');
        }
    }

    public function fire()
    {
        $payload = json_decode($this->payload()['body'], true);

        [$class, $method] = JobName::parse($payload['job']);

        ($this->instance = $this->resolve($class))->{$method}($this, $payload['data']);
    }
}
