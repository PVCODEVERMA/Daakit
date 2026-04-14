<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Success extends User_controller {

    public function __construct() {
        parent::__construct();
    }

    public function index()
	{
        $this->load->library('profile_lib');
        
        $profile = $this->profile_lib->getprofileByUserID($this->user->account_id);
        $data['service_type']=$profile->service_type==1?$profile->service_type:0;
        $this->load->view('dash/success',$data);
        
    }
    
   
}
