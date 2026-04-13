<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Invoice_lib extends MY_lib {

    public function __construct() {
        parent::__construct();
        $this->CI->load->model('admin/invoice_model');
    }

    public function __call($method, $arguments) {
        if (!method_exists($this->CI->invoice_model, $method)) {
            throw new Exception('Undefined method invoice_model::' . $method . '() called');
        }
        return call_user_func_array([$this->CI->invoice_model, $method], $arguments);
    }

}

?>