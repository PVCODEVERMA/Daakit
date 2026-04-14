<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class User_api extends User_controller {

    public function __construct() {
        parent::__construct();

        $this->userHasAccess('settings');
        $this->load->library('user_api_lib');
    }

    function index() {
        if ($this->input->post('generate') == 'generate') {
            $this->user_api_lib->generateAPI($this->user->account_id);
        }

        $api_details = $this->user_api_lib->getByUserID($this->user->account_id);
        $this->data['api_details'] = $api_details;
        $this->layout('user_api/index');
    }

}
