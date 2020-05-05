<?php

Environment::configure('development',
    array('CAKE_ENV' => 'development'),
    array(
        'debug' => 2,
        'Cache.disable' => false,

        /* Database Details */
        'DATABASE' => array(
            'default' => array(
                'MYSQL_DB_HOST' => 'mysql',
                'MYSQL_USERNAME' => 'root',
                'MYSQL_PASSWORD' => 'root',
                'MYSQL_DB_NAME' => 'webdata_extract',
            ),
            'redis' => array(
                'REDIS_DB_HOST' => 'redis',
            ),
        ),

        'PhantomJS' => [
            'host' => 'phantomjs',
            'port' => '4445'
        ],

        'ChromeDriver' => [
            'host' => 'chromedriver',
            'port' => '4444'
        ],

        /* RabbitMQ */
        'RabbitMQ' => array(
            'host' => 'rabbitmq',
            'port' => 5672,
            'username' => 'guest',
            'password' => 'guest',
        ),
    ),
    function () {
        $cacheConfig = array(
            'engine' => 'Redis',
            'server' => 'redis',
            'database' => 3,
            'port' => 6379,
            'password' => false,
            'duration' => '+999 days',
        );

        Cache::config('session', array_merge($cacheConfig, array('prefix' => 'data_extract_cake_session_', 'duration' => (60 * 60), 'database' => '4')));
        Cache::config('_cake_core_', array_merge($cacheConfig, array('prefix' => 'data_extract_cake_core_')));
        Cache::config('_cake_model_', array_merge($cacheConfig, array('prefix' => 'data_extract_cake_model_')));

        Configure::write('Session', array(
            'defaults' => 'cache',
            'timeout' => 60,
            'persistent' => true,
            'start' => true,
            'checkAgent' => false,
            'handler' => array(
                'config' => 'session',
            ),
        ));
    }
);
