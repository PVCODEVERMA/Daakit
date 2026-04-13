<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Wallet_adjustment_lib extends MY_lib
{

    public function __construct()
    {
        parent::__construct();

        $this->CI->load->model('admin/wallet_adjustment_model');
    }

    public function __call($method, $arguments)
    {
        if (!method_exists($this->CI->wallet_adjustment_model, $method)) {
            throw new Exception('Undefined method wallet_adjustment_model::' . $method . '() called');
        }

        return call_user_func_array([$this->CI->wallet_adjustment_model, $method], $arguments);
    }
}
