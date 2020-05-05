<?php

use PhpAmqpLib\Connection\AMQPConnection;
use PhpAmqpLib\Message\AMQPMessage;

class RabbitMQ
{

    public $exchangeName = 'router';

    private $queueName;

    /**
     * Get the RabbitMQ connection. The connection details are held in the bootstrap file.
     *
     * @access public
     */
    public function getConnection()
    {
        return new AMQPConnection(
            Configure::read('RabbitMQ.host'),
            Configure::read('RabbitMQ.port'),
            Configure::read('RabbitMQ.username'),
            Configure::read('RabbitMQ.password')
        );
    }

    /**
     * Set the queue
     *
     * @access public
     */
    public function setQueue($queueName)
    {
        $this->queueName = $queueName;
    }

    /**
     * Get the queue channel
     *
     * @access public
     */
    public function getChannel($connection)
    {
        $channel = $connection->channel();
        $channel->queue_declare($this->queueName, false, true, false, false);
        $channel->exchange_declare($this->exchangeName, 'direct', false, true, false);
        $channel->queue_bind($this->queueName, $this->exchangeName, $this->queueName);
        return $channel;
    }

    /**
     * Get the queue count
     *
     * @access public
     */
    public function getQueueCount($connection)
    {
        $channel = $connection->channel();
        $result = $channel->queue_declare($this->queueName, true, true, false, false);
        return $result[1];
    }

    /**
     * Publish a message to the queue
     *
     * @access public
     */
    public function publishMessage($channel, $data)
    {
        $msg = new AMQPMessage(
            json_encode($data),
            array('delivery_mode' => 2)
        );
        $channel->basic_publish($msg, $this->exchangeName, $this->queueName);
    }
}
