<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Billing_lib extends MY_lib
{

    public function __construct()
    {
        parent::__construct();
    }

    function rechargeFromRemittance($user_id = false, $amount = false)
    {
        if (!$user_id || !$amount)
            return false;

        //select all shipments for this user
        //remittance not paid
        //t + remittance cycle
        //

        $this->CI->load->library('admin/shipping_lib',NULL, 'admin_ship_lib');
        $shipments = $this->CI->admin_ship_lib->rechargeableShipmentsOfUser($user_id);
        if (empty($shipments)) {
            $this->error = 'No Available Remittance';
            return false;
        }

        

        array_multisort(array_column($shipments, 'order_amount'), SORT_ASC, $shipments);


        $calculated_amount = 0;
        $shipment_ids = array();

        foreach ($shipments as $shipment) {
            $calculated_amount += $shipment->order_amount;
            $shipment_ids[] = $shipment->id;
            if ($calculated_amount >= $amount)
                break;
        }

        $calculated_amount = round($calculated_amount, 2);

        if ($calculated_amount < $amount) {
            $this->error = 'Your recharge limit is ' . $calculated_amount;
            return false;
        }


        $this->CI->load->library('admin/remittance_lib');
        if (!$this->CI->remittance_lib->createRemittanceByShipmentID($shipment_ids, true, true)) {
            $this->error = 'Unable to process.';
            return false;
        }

        return true;
    }
}
