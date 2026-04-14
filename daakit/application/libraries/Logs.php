<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Logs 
{
    public function __construct()
    {
        $this->CI = &get_instance();


    }

    function create($channel = 'log', $message = false, $data = array(),$userId = false)
    {
        $data = json_encode($data);
        // $log = new Logger($channel);
        // $date = date('YmdH');
        // $log->pushHandler(new StreamHandler("daakitlogs/{$date}.log"));
        // $log->debug($message . '=> ' . $data);
        $save = array(
            'user_id' => ($_SESSION['user_info']->user_id)??$userId,
            'action_type' => !empty($message) ? $message : $channel,
            'log_data' => $data,
        );
       save_system_log($save);
    }

    function createUserLog($channel = 'log', $message = false, $data = array())
    {
        $data = json_encode($data);
        $log = new Logger($channel);
        $date = date('Ymd');
        $log->pushHandler(new StreamHandler("deltauserlogs/{$date}.log"));
        $log->debug($message . '=> ' . $data);
    }
}
