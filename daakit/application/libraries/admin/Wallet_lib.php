<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Wallet_lib extends MY_lib {

    public function __construct() {
        parent::__construct();

        $this->CI->load->model('admin/wallet_model');
    }

    public function __call($method, $arguments) {
        if (!method_exists($this->CI->wallet_model, $method)) {
            throw new Exception('Undefined method wallet_model::' . $method . '() called');
        }

        return call_user_func_array([$this->CI->wallet_model, $method], $arguments);
    }
}

?>