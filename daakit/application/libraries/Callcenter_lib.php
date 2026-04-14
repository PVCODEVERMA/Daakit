<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Callcenter_lib extends MY_lib {
    public function __construct()
    {
        parent::__construct();

        $this->CI->load->model('ndr_model');
    }


    public function __call($method, $arguments)
    {
        if (!method_exists($this->CI->ndr_model, $method)) {
            throw new Exception('Undefined method ndr_model::' . $method . '() called');
        }

        return call_user_func_array([$this->CI->ndr_model, $method], $arguments);
    }
}