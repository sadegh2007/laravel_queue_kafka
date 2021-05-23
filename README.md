Kafka Queue driver for Laravel

## Installation

You can install this package via composer using this command:

```
composer require sadeq/laravel-queue-kafka
```

The package will automatically register itself.

Add connection to `config/queue.php`:

```php
'connections' => [
    // ...

    'kafka' => [
        'driver' => 'kafka',
        'sleep_on_error' => env('KAFKA_SLEEP_ON_ERROR', 5),
        'topics' => env('KAFKA_TOPICS', 'default'),
        'debug' => env('KAFKA_DEBUG', false),
        'brokers' => env('KAFKA_BROKERS', 'localhost:9092'),
        'auto_commit' => 'false',
        'group_id' => 'myConsumerGroup',
        'consumer' => [
            'sasl.username' => env('KAFKA_USERNAME', null),
            'sasl.password' => env('KAFKA_PASSWORD', null),
            'auto.offset.reset' => 'smallest',
            'security.protocol' => env('KAFKA_SECURITY_PROTOCOL', 'SASL_SSL'),
            'sasl.mechanisms' => env('KAFKA_SASL_MECHANISMS', 'SCRAM-SHA-256'),
            'compression.type' => 'gzip',
        ],
        'producer' => [
            'sasl.username' => env('KAFKA_USERNAME', null),
            'sasl.password' => env('KAFKA_PASSWORD', null),
            'security.protocol' => env('KAFKA_SECURITY_PROTOCOL', 'SASL_SSL'),
            'sasl.mechanisms' => env('KAFKA_SASL_MECHANISMS', 'SCRAM-SHA-256'),
            'compression.type' => 'gzip',
            'log_level' => LOG_DEBUG,
            'debug' => 'all'
        ]
    ],

    // ...
],
```

## Lumen Usage

For Lumen usage the service provider should be registered manually as follow in `bootstrap/app.php`:

```php
$app->register(Sadeq\LaravelQueueKafka\KafkaServiceProvider::class);
```

## Consuming Messages

There are one ways of consuming messages for now.

1. `kafka:consume` command which is provided by this package.

you can specify topics with command option `--topics=default`

## Contribution

You can contribute to this package by discovering bugs and opening issues. Please, add to which version of package you create pull request or issue.