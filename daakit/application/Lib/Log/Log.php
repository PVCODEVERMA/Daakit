<?php

namespace App\Lib\Log;

use Aws\CloudWatchLogs\CloudWatchLogsClient;
use Maxbanton\Cwh\Handler\CloudWatch;
use Monolog\Logger;
use Monolog\Formatter\JsonFormatter;

use Monolog\Handler\StreamHandler;

use App\Lib\BaseLib;

class Log extends BaseLib
{
    protected $region = 'ap-south-1';
    protected $key;
    protected $secret;
    protected $group_name;
    protected $stream = 'default';
    protected $handlers = [];

    public function __construct()
    {
        parent::__construct();
    }

    function _initHandler($stream = NULL)
    {
        $this->key = $this->CI->config->item('aws_access_key');
        $this->secret = $this->CI->config->item('aws_secret_key');
        $this->group_name = $this->CI->config->item('aws_log_group_name');

        $sdkParams = [
            'region' => $this->region,
            'version' => 'latest',
            'credentials' => [
                'key' => $this->key,
                'secret' => $this->secret,
                'token' => '', // token is optional
            ]
        ];
        

        // Instantiate AWS SDK CloudWatch Logs Client
        $client = new CloudWatchLogsClient($sdkParams);

        // Log group name, will be created if none
        $groupName = $this->group_name;

        // Log stream name, will be created if none
        $streamName = $stream;

        // Days to keep logs, 14 by default. Set to `null` to allow indefinite retention.
        $retentionDays = null;

        // Instantiate handler (tags are optional)
        $handler = new CloudWatch($client, $groupName, $streamName, $retentionDays, 10000, ['my-awesome-tag' => 'tag-value']);

        // Optionally set the JsonFormatter to be able to access your log messages in a structured way
        $handler->setFormatter(new JsonFormatter());

        return $this->handlers[$stream] = $handler;
    }

    private function getStreamHandler($stream = NULL)
    {
        return (isset($this->handlers[$stream])) ? $this->handlers[$stream] : $this->_initHandler($stream);
    }

    function save($entity_type = 'default', $data = null, $level = 'INFO')
    {
        $level = !empty(($level)) ? $level : 'INFO';



        // // Set handler
        // if (in_array(strtolower(ENVIRONMENT), ['development'])) {

        $data = json_encode($data);
        $local_log = new Logger($entity_type);
        $date = date('YmdH');
        $local_log->pushHandler(new StreamHandler("application/logs/{$date}.log"));
        $local_log->$level($data);
        return true;

        //do not execute below for now.
        //}

        $log = new Logger($entity_type);
        $log->pushHandler($this->getStreamHandler($entity_type));
        $log->pushProcessor(function ($record) {
            $record['action'] = $record['message'];
            $record['ref_id'] = !empty($record['context']['ref_id']) ? $record['context']['ref_id'] : NULL;
            return $record;
        });

        $action = !empty($data['action']) ? $data['action'] : 'default';

        // Add records to the log
        //$log->$level($title, $data);
        $log->$level($action, $data);
        // $log->reset();
    }
}
