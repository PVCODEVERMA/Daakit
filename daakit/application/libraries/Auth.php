<?php

class Auth extends MY_lib {

    var $_authpool = array();
    var $session_expire = 14400;

    function __construct() {
        parent::__construct();
        // Load the saved session
        if ($this->CI->session->userdata('user_info') !== FALSE || $this->CI->session->userdata('user_info') != '') {
            $this->_authpool = $this->CI->session->userdata('user_info'); //save session info to variable
        } else {
            // Or init a new session
            $this->_init_properties();
        }
    }    

    private function _init_properties() {
        $this->_authpool = false;
    }

    //check if user is logged in or not
    function logged_in() {

        // if no session, no expiry, or expiry is less than this time
        if (!$this->_authpool || !$this->_authpool->expire || $this->_authpool->expire < time()) { //no session exists
            $this->destroy();
            return false;
        } else { // session exists
            $this->_authpool->expire = time() + $this->session_expire; //update the session
            $this->save_session($this->_authpool);
            return $this->_authpool;
        }
    }

    // Saves customer data in the 
    function save_session($data) {
        $this->_authpool = $data;
        $this->CI->session->sess_expiration = time() + 14400; // expires in 4 hours
        $this->CI->session->set_userdata('user_info', $this->_authpool);
    }

    /**
     * Destroy  the user
     *
     */
    function destroy() {
        $this->_init_properties();
        $this->CI->session->unset_userdata('user_info');
    }

}
