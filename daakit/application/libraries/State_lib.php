<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class State_lib extends MY_lib {

    public function __construct() {
        parent::__construct();
        $this->CI->load->model('state_model');
    }

    public function __call($method, $arguments) {
        if (!method_exists($this->CI->state_model, $method)) {
            throw new Exception('Undefined method state_model::' . $method . '() called');
        }

        return call_user_func_array([$this->CI->state_model, $method], $arguments);
    }
}