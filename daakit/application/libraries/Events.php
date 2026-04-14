<?php

defined('BASEPATH') or exit('No direct script access allowed');

use PhpAmqpLib\Message\AMQPMessage;

class Events
{

    // Default private vars
    private $CI;
    // Default protected vars
    protected $config;
    // Default public vars
    public $connexion;
    public $channel;
    public $show_output;
    public $item;

    public function __construct(array $config = array())
    {
        // Load the CI instance
        $this->CI = &get_instance();

        // Load the RabbitMQ helper
        $this->CI->load->helper('rabbitmq');

        // Define if we have to show outputs or not
        $this->show_output = (!empty($config['show_output']));

        // Define the config global
        $this->config = (!empty($config)) ? $config : array();
        $this->item = null;

        // Initialize the connection
        $this->initialize($this->config);
    }

    /**
     * initialize : Initialize the configuration of the Library
     * @method initialize
     * @author Romain GALLIEN <romaingallien.rg@gmail.com>
     * @param  array      $config Library configuration
     */
    public function initialize(array $config = array())
    {
        // We check if we have a config given then we initialize the connection
        if (!empty($config)) {

            try {

                $this->config = $config['rabbitmq'];
                $this->connexion = new PhpAmqpLib\Connection\AMQPStreamConnection($this->config['host'], $this->config['port'], $this->config['user'], $this->config['pass'], $this->config['vhost']);
                $this->channel = $this->connexion->channel();
            } catch (Exception $e) {
                return false;
            }
        } else {
            output_message('Invalid configuration file', 'error', 'x');
        }
    }

    function queue_declare($queue = null, $permanent = false)
    {
        list($queue_name,,) = $this->channel->queue_declare($queue, false, $permanent, false, (empty($queue)) ? true : false, false, null, null);
        return $queue_name;
    }

    function exchange_declare($exchange = null, $type = 'topic', $permanent = false)
    {
        $this->channel->exchange_declare($exchange, $type, false, $permanent, false);
    }

    function set_item($data = null)
    {
        $data = (is_array($data)) ? json_encode($data) : $data;
        $this->item = new AMQPMessage($data, array('delivery_mode' => AMQPMessage::DELIVERY_MODE_PERSISTENT));
    }

    function basic_publish($queue = null, $exchange = null)
    {
        $this->channel->basic_publish($this->item, $exchange, $queue);
    }

    function bind_key($queue_name = null, $exchange = null, $binding_key = array())
    {
        foreach ($binding_key as $b_key)
            $this->channel->queue_bind($queue_name, $exchange, $b_key);
    }

    function send_to_queue($queue = null, $data = null, $permanent = false)
    {
        // We check if the queue is not empty then we declare the queue
        if (!empty($queue)) {
            $this->queue_declare($queue, $permanent);
            // We declare the queue

            $this->set_item($data);

            $this->basic_publish($queue);

            ($this->show_output) ? output_message('Pushing "' . $this->item->body . '" to "' . $queue . '" queue -> OK', null, '+') : true;
        } else {
            output_message('You did not specify the [queue] parameter', 'error', 'x');
        }
    }

    public function consume($queue = null, $permanent = false, array $callback = array(), $need_acknowledgment = false)
    {
        // We check if the queue is not empty then we declare the queue
        if (!empty($queue)) {

            $this->queue_declare($queue, $permanent);

            // Limit the number of unacknowledged
            if ($need_acknowledgment)
                $this->channel->basic_qos(null, 1, null);
            // Define consuming with 'process' callback
            $this->channel->basic_consume($queue, '', false, $need_acknowledgment, false, false, $callback);

            // Continue the process of CLI command, waiting for others instructions
            while (count($this->channel->callbacks)) {
                $this->channel->wait();
            }
        } else {
            output_message('You did not specify the [queue] parameter', 'error', 'x');
        }
    }

    function send_to_exchange($exchange = null, $data = null, $permanent = false, $routing_key = null)
    {
        // We check if the queue is not empty then we declare the queue
        if (!empty($exchange)) {
            $this->exchange_declare($exchange, 'topic', $permanent);
            // We declare the queue
            // If the informations given are in an array, we convert it in json format

            $this->set_item($data);

            $this->basic_publish($routing_key, $exchange);

            ($this->show_output) ? output_message('Pushing "' . $this->item->body . '" to "' . $exchange . '" queue -> OK', null, '+') : true;
        } else {
            output_message('You did not specify the  parameter', 'error', 'x');
        }
    }

    public function consume_exchange($exchange = null, $queue = null, $permanent = false, $binding_key = array(), array $callback = array(), $need_acknowledgment = false)
    {
        // We check if the queue is not empty then we declare the queue
        if (!empty($exchange)) {

            $this->exchange_declare($exchange, 'topic', $permanent);

            $queue_name = $this->queue_declare($queue, (!empty($queue)) ? true : false);

            $this->bind_key($queue_name, $exchange, $binding_key);


            $this->channel->basic_qos(null, 1, null);

            // Define consuming with 'process' callback
            //$need_acknowledgment = false means need ack and true means no ack
            $this->channel->basic_consume($queue, '', false, $need_acknowledgment, false, false, $callback);

            // Continue the process of CLI command, waiting for others instructions
            while (count($this->channel->callbacks)) {
                $this->channel->wait();
            }
        } else {
            output_message('You did not specify the  parameter', 'error', 'x');
        }
    }

    public function basic_ack($msg = false)
    {
        $msg->delivery_info['channel']->basic_ack($msg->delivery_info['delivery_tag']);
    }

    public function __destruct()
    {
        // Close the channel
        if (!empty($this->channel)) {
            $this->channel->close();
        }

        // Close the connexion
        if (!empty($this->connexion)) {
            $this->connexion->close();
        }
    }
}

/* End of file Rabbitmq.php */
/* Location: ./application/librairies/Rabbitmq.php */
