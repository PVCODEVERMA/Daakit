<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Dash extends User_controller
{

    public function __construct()
    {
        parent::__construct();
    }

    public function index()
    {
        redirect('analytics');
    }

    public function channels()
    {
        $this->layout('dash/channels');
    }

    public function whatsappurl()
    {
        $user_id = $this->user->account_id;
        $this->load->library('whatsappengage_lib');
        $whatsappengage_lib = new Whatsappengage_lib();
        $url = $whatsappengage_lib->backendurl($user_id);
        echo $url;
    }
}