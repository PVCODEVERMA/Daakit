<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class User_api_lib extends MY_lib {

    public function __construct() {
        parent::__construct();

        $this->CI->load->model('user_api_model');
    }

    public function __call($method, $arguments) {
        if (!method_exists($this->CI->user_api_model, $method)) {
            throw new Exception('Undefined method user_api_model::' . $method . '() called');
        }

        return call_user_func_array([$this->CI->user_api_model, $method], $arguments);
    }

    function generateAPI($user_id = false) {
        if (!$user_id)
            return false;

        $exist = $this->getByUserID($user_id);

        $api_key = sha1(time() . rand(1111, 9999)) . $user_id;

        $save_data = array(
            'user_id' => $user_id,
            'api_key' => $api_key,
            'date_created' => time(),
        );
        if (!empty($exist)) {
            $this->update($exist->id, $save_data);
        } else {
            $this->create($save_data);
        }
        return true;
    }

}

?>