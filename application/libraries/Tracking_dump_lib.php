<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Tracking_dump_lib extends MY_lib
{

    public function __construct()
    {
        parent::__construct();

        $this->CI->load->model('tracking_dump_model');
    }

    public function __call($method, $arguments)
    {
        if (!method_exists($this->CI->tracking_dump_model, $method)) {
            throw new Exception('Undefined method tracking_dump_model::' . $method . '() called');
        }

        return call_user_func_array([$this->CI->tracking_dump_model, $method], $arguments);
    }
}
