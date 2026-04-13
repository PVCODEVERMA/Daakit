<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Remittance_lib extends MY_lib {

    public function __construct() {
        parent::__construct();

        $this->CI->load->model('remittance_model');
    }

    public function __call($method, $arguments) {
        if (!method_exists($this->CI->remittance_model, $method)) {
            throw new Exception('Undefined method remittance_model::' . $method . '() called');
        }

        return call_user_func_array([$this->CI->remittance_model, $method], $arguments);
    }

    function getShippingDetails($remittance_id = false) {

        if (!$remittance_id)
            return false;

        $remittance = $this->getById($remittance_id);

        if (empty($remittance))
            return false;

        $shipping_ids = explode(',', $remittance->shipping_ids);
        if (empty($shipping_ids))
            return false;

        $this->CI->load->library('shipping_lib');
        $shipments = $this->CI->shipping_lib->shipmentDetailsBulkIds($remittance->user_id, 10000, 0, array('shipment_ids' => $shipping_ids));
        return $shipments;
    }

}

?>