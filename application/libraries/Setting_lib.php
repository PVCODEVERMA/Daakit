<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Setting_lib extends MY_lib {

    public function __construct() {
        parent::__construct();
        $this->CI->load->model('setting_model');
    }

    public function __call($method, $arguments) {
        if (!method_exists($this->CI->setting_model, $method)) {
            throw new Exception('Undefined method setting_model::' . $method . '() called');
        }
        return call_user_func_array([$this->CI->setting_model, $method], $arguments);
    }
}
?>
