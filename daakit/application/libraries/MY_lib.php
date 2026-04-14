<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class MY_lib {

    protected $data;
    protected $error;
    protected $CI;

    public function __construct() {
        $this->CI = & get_instance();
    }

    function get_error() {
        return $this->error;
    }

    function default_load() {
       
    }

}
