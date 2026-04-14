<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Analytics_lib extends MY_lib {

    public function __construct() {
        parent::__construct();

        $this->CI->load->model('analytics_model');
    }

    public function __call($method, $arguments) {
        if (!method_exists($this->CI->analytics_model, $method)) {
            throw new Exception('Undefined method analytics_model::' . $method . '() called');
        }

        return call_user_func_array([$this->CI->analytics_model, $method], $arguments);
    }

}

?>