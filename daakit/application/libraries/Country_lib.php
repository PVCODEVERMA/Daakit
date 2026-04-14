<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Country_lib extends MY_lib {

    public function __construct() {
        parent::__construct();
        $this->CI->load->model('country_model');
    }

    public function __call($method, $arguments) {
        if (!method_exists($this->CI->country_model, $method)) {
            throw new Exception('Undefined method country_model::' . $method . '() called');
        }

        return call_user_func_array([$this->CI->country_model, $method], $arguments);
    }
}