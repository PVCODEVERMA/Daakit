<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Orders_lib extends MY_lib {

    public function __construct() {
        parent::__construct();

        $this->CI->load->model('admin/orders_model');
    }

    public function __call($method, $arguments){
        if (!method_exists($this->CI->orders_model, $method)){
            throw new Exception('Undefined method orders_model::' . $method . '() called');
        }
        return call_user_func_array([$this->CI->orders_model, $method], $arguments);
    }
	
	public function newordermark($orderId = false)
	{
        $update = array(
            'fulfillment_status' => 'new'
        );
        $this->update($orderId, $update);
        return true;
    }
}

?>