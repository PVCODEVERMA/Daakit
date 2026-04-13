<?php

class SMS extends MY_lib
{

    var $api_key;
    var $sender_id = '';
    var $route = '4';
    var $country = '91';
    var $sms = array();

    public function __construct()
    {
        $this->CI = &get_instance();
        $this->api_key = $this->CI->config->item('msg91_api_key');
    }
   
}