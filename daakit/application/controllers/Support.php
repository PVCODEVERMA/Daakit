<?php

defined('BASEPATH') or exit('No direct script access allowed');



class Support extends User_controller
{

    public function __construct()
    {
        parent::__construct();
    }

    function index()
    {
        $this->layout('support/index');
    }

    // function guide()
    // {
    //     $this->layout('support/guide');
    // }
}
