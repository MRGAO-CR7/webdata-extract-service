<?php

Environment::configure(
    'gcp',
    array('CAKE_ENV' => 'gcp'),
    array(
        'debug'             => 0,
        'Cache.disable'     => false,
        'App.fullBaseUrl'   => 'https://apps.ericgao.com',
        'LoginURL' => 'https://apps.ericgao.com',

        /* Database Details */
        'DATABASE' => array(
            'default' => array(
                'MYSQL_DB_HOST' => '192.168.8.1',
                'MYSQL_USERNAME' => 'data_extract_user',
                'MYSQL_PASSWORD' => '*********',
                'MYSQL_DB_NAME' => 'console',
            ),
            'redis' => array(
                'REDIS_DB_HOST' => '192.168.8.1',
            )
        ),

        'PhantomJS' => [
            'host' =>  '192.168.8.1',//'phantomjs-a.arpa.ericgao.com',
            'port' => '4444'
        ],

        'ChromeDriver' => [
            'host' => '192.168.8.1',
            'port' => '4444',
        ],
    ),
    function () {
        $cacheConfig = array(
            'engine'    => 'Redis',
            'server'    => '192.168.8.1',
            'database'  => 3,
            'port'      => 6379,
            'password'  => false,
            'duration'  => '+999 days'
        );

        Cache::config('session', array_merge($cacheConfig, array('prefix' => 'data_extract_cake_session_', 'duration' => (60*60), 'database' => '4')));
        Cache::config('_cake_core_', array_merge($cacheConfig, array('prefix' => 'data_extract_cake_core_')));
        Cache::config('_cake_model_', array_merge($cacheConfig, array('prefix' => 'data_extract_cake_model_')));

        Configure::write('Session', array(
            'defaults' => 'cache',
            'timeout' => 240,
            'persistent' => true,
            'start' => true,
            'checkAgent' => false,
            'handler' => array(
                'config' => 'session'
            )
        ));
    }
);
