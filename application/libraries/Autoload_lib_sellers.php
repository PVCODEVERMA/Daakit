<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Autoload_lib_sellers extends MY_lib
{

    public function __construct()
    {
        parent::__construct();
        $this->_init();
    }

    function _init()
    {
        $this->CI->config->load('autoload_lib');
        $lib = $this->CI->config->item('autoload_libraries_sellers');
        if (!empty($lib))
            $this->CI->load->library($lib);
    }
}
