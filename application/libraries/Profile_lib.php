<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Profile_lib extends MY_lib {

    public function __construct() {
        parent::__construct();
        $this->CI->load->model('profile_model');
    }

    public function __call($method, $arguments) {
        if (!method_exists($this->CI->profile_model, $method)) {
            throw new Exception('Undefined method profile_model::' . $method . '() called');
        }
        return call_user_func_array([$this->CI->profile_model, $method], $arguments);
    }
}
?>
