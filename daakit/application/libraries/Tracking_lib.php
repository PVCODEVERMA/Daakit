<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Tracking_lib extends MY_lib
{

    public function __construct()
    {
        parent::__construct();

        $this->CI->load->model('tracking_model');
    }

    public function __call($method, $arguments)
    {
        if (!method_exists($this->CI->tracking_model, $method)) {
            throw new Exception('Undefined method tracking_model::' . $method . '() called');
        }

        return call_user_func_array([$this->CI->tracking_model, $method], $arguments);
    }

    function createUpdateShipmentTracking($shipment_id = false, $save = array())
    {
        if (!$shipment_id || empty($save))
            return false;

        $record = $this->getTrackingBYShipmentID($shipment_id);
        if (empty($record)) {
            $this->insertShipmentTracking($save);
        } else {
            $this->updateShipmentTracking($record->id, $save);
        }

        return false;
    }

    function copyTracking($shipment_id = false)
    {
        if (!$shipment_id)
            return false;

        $this->CI->load->library('shipping_lib');
        $shipment = $this->CI->shipping_lib->getByID($shipment_id);

        if (empty($shipment) || empty($shipment->awb_number)) {
            $this->CI->shipping_lib->update($shipment_id, array('tracking_moved' => '2'));
            return false;
        }

        if ($shipment->tracking_moved == '1') {
            $this->CI->shipping_lib->update($shipment_id, array('tracking_moved' => '4'));
            return false;
        }


        $trackings = $this->getByAWB($shipment->awb_number);
        if (empty($trackings)) {
            $this->CI->shipping_lib->update($shipment_id, array('tracking_moved' => '3'));
            return false;
        }

        $save = array();
        foreach ($trackings as $tracking) {
            $save[$tracking->event_time] = $tracking;
        }

        $this->batchInsertNewDB($save);

        $this->CI->shipping_lib->update($shipment_id, array('tracking_moved' => '1'));
        return true;
    }

    function schedule_copy_tracking()
    {
        $this->CI->db->where('tracking_moved', '0');
        $this->CI->db->order_by('id', 'asc');
        $this->CI->db->limit(1);

        $q = $this->CI->db->get('order_shipping');

        $shipment =  $q->row();

        if (empty($shipment)) {
            return false;
        }

        do_action('tracking.delete_duplicate', $shipment->id);
    }


    function encrypt($data,$encryption_method,$key) {
        $key = $key;
        $plaintext = $data;
        $ivlen = openssl_cipher_iv_length($cipher = $encryption_method);
        $iv = openssl_random_pseudo_bytes($ivlen);
        $ciphertext_raw = openssl_encrypt($plaintext, $cipher, $key, $options = OPENSSL_RAW_DATA, $iv);
        $hmac = hash_hmac('sha256', $ciphertext_raw, $key, $as_binary = true);
        $ciphertext = base64_encode($iv . $hmac . $ciphertext_raw);
        return $ciphertext;
    } 
}
