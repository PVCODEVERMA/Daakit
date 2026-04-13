<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Userlogs_lib extends MY_lib
{
    
    public function __construct()
    {
        parent::__construct();
      
        $this->CI->load->model('userlogs_model');
    }

    public function __call($method, $arguments)
    {
        if (!method_exists($this->CI->userlogs_model, $method)) {
            throw new Exception('Undefined method userlogs_model::' . $method . '() called');
        }

        return call_user_func_array([$this->CI->userlogs_model, $method], $arguments);
    }
}
