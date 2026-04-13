<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Plans_lib extends MY_lib
{

    public function __construct()
    {
        parent::__construct();
        $this->CI->load->model('admin/plans_model');
    }

    public function __call($method, $arguments)
    {
        if (!method_exists($this->CI->plans_model, $method)) {
            throw new Exception('Undefined method plans_model::' . $method . '() called');
        }

        return call_user_func_array([$this->CI->plans_model, $method], $arguments);
    }
}
