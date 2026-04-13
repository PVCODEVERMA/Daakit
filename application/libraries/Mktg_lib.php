<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Mktg_lib extends MY_lib {

    public function __construct() {
        parent::__construct();
        $this->CI->load->model('mktg_model');
    }

    public function __call($method, $arguments) {
        if (!method_exists($this->CI->mktg_model, $method)) {
            throw new Exception('Undefined method mktg_model::' . $method . '() called');
        }

        return call_user_func_array([$this->CI->mktg_model, $method], $arguments);
    }

}
