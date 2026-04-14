<?php

namespace App\Lib;

class BaseLib
{
    protected $CI;
    protected $data;
    protected $error;

    public function __construct()
    {
        // Load the CI instance
        $this->CI = &get_instance();
    }

    function get_error()
    {
        return $this->error;
    }
}
