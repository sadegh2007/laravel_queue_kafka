<?php

namespace Sadeq\LaravelQueueKafka;

use RdKafka\Producer;
use Illuminate\Queue\Worker;
use Illuminate\Support\ServiceProvider;
use Sadeq\LaravelQueueKafka\Commands\ConsumerCommand;
use Sadeq\LaravelQueueKafka\Connectors\KafkaConnector;
use Illuminate\Contracts\Debug\ExceptionHandler;

class KafkaServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->mergeConfigFrom(
            __DIR__ . '/Config/kafka.php',
            'queue.connections.kafka'
        );
        
        $this->commands([
            ConsumerCommand::class
        ]);
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        $this->app->bind(Producer::class, function () {
            return new Producer(KafkaHelper::producerConfig());
        });

        $manager = $this->app['queue'];

        $manager->addConnector('kafka', function () {
           return new KafkaConnector($this->app['events']);
        });

        $this->app->extend('queue.worker', function ($worker, $app) {
            return new Worker(
                $app['queue'],
                $app['events'],
                $app[ExceptionHandler::class],
                function () use ($app) {
                    return $app->isDownForMaintenance();
                }
            );
        });
    }
}
