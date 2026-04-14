<?php

defined('BASEPATH') or exit('No direct script access allowed');

function customError($errno, $errstr)
{
}

//set error handler
set_error_handler("customError");
class Pipeline extends Front_controller
{

    public function __construct()
    {
        parent::__construct();
    }
}
