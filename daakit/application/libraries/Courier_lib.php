<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Courier_lib extends MY_lib
{

    public function __construct()
    {
        parent::__construct();

        $this->CI->load->model('courier_model');
    }

    public function __call($method, $arguments)
    {
        if (!method_exists($this->CI->courier_model, $method)) {
            throw new Exception('Undefined method courier_model::' . $method . '() called');
        }

        return call_user_func_array([$this->CI->courier_model, $method], $arguments);
    }

    function isUSerApprovedToCourier($user_id = false, $courier_id = false)
    {
        if (!$user_id || !$courier_id)
            return false;

        $courier = $this->getByID($courier_id);

        if (empty($courier))
            return false;

        if ($courier->status == '0') {
            //courier is not active
            return false;
        }

        //courier require approval
        $approved_courier = $this->approvedToUser($user_id);

        $approved_to_user = array();
        $disabled_couriers = array();

        if (!empty($approved_courier) && !empty($approved_courier->courier_ids)) {
            $approved_to_user = explode(',', $approved_courier->courier_ids);
        }

        if (!empty($approved_courier) && !empty($approved_courier->disabled_couriers)) {
            $disabled_couriers = explode(',', $approved_courier->disabled_couriers);
        }

        if ($courier->require_approval == '1' && !in_array($courier->id, $approved_to_user))
            return false;

        if (in_array($courier->id, $disabled_couriers))
            return false;

        return true;
    }

    function userAvailableCouriers($user_id = false, $order_type = 'ecom')
    {
        if (!$user_id)
            return false;

        $all_couriers = $this->list_couriers('', $order_type);
        $approved_to_user = array();
        $disabled_couriers = array();

        $approved_courier = $this->approvedToUser($user_id);
        if (!empty($approved_courier) && !empty($approved_courier->courier_ids)) {
            $approved_to_user = explode(',', $approved_courier->courier_ids);
        }

        if (!empty($approved_courier) && !empty($approved_courier->disabled_couriers)) {
            $disabled_couriers = explode(',', $approved_courier->disabled_couriers);
        }

        $return = array();

        foreach ($all_couriers as $key => $courier) {
            if ($courier->require_approval == '1' && !in_array($courier->id, $approved_to_user))
                unset($all_couriers[$key]);
            elseif (in_array($courier->id, $disabled_couriers))
                unset($all_couriers[$key]);
            else
                $return[$courier->id] = $courier;
        }

        return $return;
    }
}