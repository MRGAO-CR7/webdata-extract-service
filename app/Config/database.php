<?php

class DATABASE_CONFIG
{
    /**
     * Read the connection info from the environment.
     **/
    public function __construct()
    {
        $this->default = array(
            'datasource' => 'Database/Mysql',
            'persistent' => false,
            'host' => $this->read('DATABASE.default.MYSQL_DB_HOST'),
            'login' => $this->read('DATABASE.default.MYSQL_USERNAME'),
            'password' => $this->read('DATABASE.default.MYSQL_PASSWORD'),
            'database' => $this->read('DATABASE.default.MYSQL_DB_NAME'),
            'prefix' => '',
            'encoding' => 'utf8',
        );
        $this->test = array(
            'datasource' => 'Database/Mysql',
            'persistent' => false,
            'host' => '127.0.0.1',
            'login' => 'root',
            'password' => '',
            'database' => 'test',
            'prefix' => '',
            'encoding' => 'utf8',
        );
        $this->redis = array(
            'datasource' => 'Redis.RedisSource',
            'host' => $this->read('DATABASE.redis.REDIS_DB_HOST'),
            'port' => 6379,
            'password' => '',
            'database' => 0,
            'timeout' => 0,
            'persistent' => false,
            'unix_socket' => '',
            'prefix' => '',
        );
    }
    /**
     * Allows reading of a key from env() or Configure::read() as appropriate.
     *
     * @param $key key being read
     * @param $default default value in case env() and Configure::read() fail
     **/
    public function read($key, $default = null)
    {
        $value = env($key);
        if ($value !== null) {
            return $value;
        }

        $value = Configure::read($key);
        if ($value !== null) {
            return $value;
        }

        return $default;
    }
}
