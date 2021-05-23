<?php

return [
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
    ]
];
