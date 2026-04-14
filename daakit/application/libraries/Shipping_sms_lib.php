<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Shipping_sms_lib extends MY_lib
{

    public function __construct()
    {
        parent::__construct();

        $this->CI->load->model('shipping_sms_model');
    }

    public function __call($method, $arguments)
    {
        if (!method_exists($this->CI->shipping_sms_model, $method)) {
            throw new Exception('Undefined method shipping_sms_model::' . $method . '() called');
        }

        return call_user_func_array([$this->CI->shipping_sms_model, $method], $arguments);
    }
}