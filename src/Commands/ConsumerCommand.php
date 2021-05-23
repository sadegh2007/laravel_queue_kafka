<?php

namespace Sadeq\LaravelQueueKafka\Commands;

use Sadeq\LaravelQueueKafka\KafkaHelper;
use Exception;
use RdKafka\Conf;
use RdKafka\Message;
use RdKafka\KafkaConsumer;
use Illuminate\Console\Command;
use Illuminate\Queue\WorkerOptions;
use Sadeq\LaravelQueueKafka\Queue\KafkaQueue;

class ConsumerCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'kafka:consume {--topics= : Whether the topics of kafka}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Kafka consumer';

    protected $worker;

    public function __construct()
    {
        $this->worker = app('queue.worker');
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return void
     * @throws \RdKafka\Exception
     */
    public function handle()
    {
        $conf = KafkaHelper::consumerConfig();
        $consumer = new KafkaConsumer($conf);

        // Subscribe to topic 'inventories'
        // Microservice 1 will push to 'inventories' topic
        $queueName = $this->option('topics') ?? config('queue.connections.kafka.topics');
        $consumer->subscribe(explode(',', $queueName));

        while (true) {
            $message = $consumer->consume(120*1000);
            switch ($message->err) {
                case RD_KAFKA_RESP_ERR_NO_ERROR:
                    $this->processMessage($message);
                    // Commit offsets asynchronously
                    $consumer->commitAsync($message);
                    break;
                case RD_KAFKA_RESP_ERR__PARTITION_EOF:
                    echo "No more messages; will wait for more\n";
                    break;
                case RD_KAFKA_RESP_ERR__TIMED_OUT:
                    echo "Timed out\n";
                    break;
                default:
                    throw new Exception($message->errstr(), $message->err);
                    break;
            }
        }
    }

    /**
     * Process Kafka message
     *
     * @param \RdKafka\Message $kafkaMessage
     * @return void
     * @throws \Throwable
     */
    protected function processMessage(Message $kafkaMessage)
    {
        //$message = $this->decodeKafkaMessage($kafkaMessage);

        //$this->info(json_encode($message));

        $manager = $this->worker->getManager();

        /** @var KafkaQueue $connection */
        $connection = $manager->connection(config('queue.connections.kafka.driver'));
        $job = $connection->setPayload($kafkaMessage->payload)->pop($connection->getConnectionName());

        if (! is_null($job)) {
            $this->worker->process(
                $manager->getName($connection->getConnectionName()),
                $job,
                new WorkerOptions($connection->getConnectionName(), 0, -1, 864000)
            );
        }

        // Lets update the stats
//        Stat::updateOrCreate(
//            ['inventory_id' => $message->body->id],
//            ['make' => $message->body->make, 'model' => $message->body->model]
//        );
    }

    /**
     * Decode kafka message
     *
     * @param \RdKafka\Message $kafkaMessage
     * @return object
     */
    protected function decodeKafkaMessage(Message $kafkaMessage)
    {
        $message = json_decode($kafkaMessage->payload);

        if (isset($message->body) && is_string($message->body)) {
            $message->body = json_decode($message->body);
        }

        return $message;
    }
}
