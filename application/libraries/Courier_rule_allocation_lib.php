<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Courier_rule_allocation_lib extends MY_lib
{
    protected $user_id = false;
    protected $user_data = false;
    protected $user_filters = false;
    protected $courier_id = false;
    protected $payment_mode = false;
    protected $zone = false;
    protected $weight = false;
    protected $plan_id = false;
    protected $qccheck = false;
    protected $skip_couriers = false;

    public function __construct()
    {
        parent::__construct();
        $this->CI->load->model('courier_allocation_model');
    }

    public function __call($method, $arguments)
    {
        if (!method_exists($this->CI->courier_allocation_model, $method)) {
            throw new Exception('Undefined method courier_allocation_model::' . $method . '() called');
        }

        return call_user_func_array([$this->CI->courier_allocation_model, $method], $arguments);
    }

    function _getCustomCourierRule($user_id = false, $custom_plan = false, $order = array(), $warehouse = array(), $skip_couriers = array())
    {
        if (empty($user_id) || empty($custom_plan) || empty($order))
            return false;

        $allocation = new Courier_rule_allocation_lib();
        $allocation->setUserID($user_id);
        $allocation->setPaymentMode($order->order_payment_type);
        $allocation->setCourierID($custom_plan);
        $allocation->setCourierType($custom_plan);
        $allocation->setPickupPincode($warehouse->zip);
        $allocation->setDeliveryPincode($order->shipping_zip);
        $allocation->setWeight($order->package_weight);
        $allocation->setQCCheck($order->qccheck);
        $allocation->setSkipCourier($skip_couriers);

        return $allocation->getRuleBasedCourier();
    }

    function setUserID($value = false)
    {
        if (!$value)
            return false;

        $this->user_id = $value;
    }

    function setPaymentMode($value = false)
    {
        $this->payment_mode = strtolower($value);
    }

    function setCourierID($value = false)
    {
        $this->courier_id = $value;
        $this->user_filters = $this->getUserfilters($this->user_id, true,$this->courier_id);
    }

    function setCourierType($value = false)
    {
        $value = implode('_', array_chunk(explode('_', $value), 2)[0]);
        $this->courier_type = strtolower($value);
    }
    function setPickupPincode($value = false)
    {
        $this->pickup_pincode = $value;
    }

    function setDeliveryPincode($value = false)
    {
        $this->delivery_pincode = $value;
    }

    function getZone()
    {
        $pricing = new Pricing_lib();

        $pricing->setOrigin($this->pickup_pincode);
        $pricing->setDestination($this->delivery_pincode);

        $this->zone = $pricing->calculateZone();
        return $this->zone;
    }

    function setWeight($value = false)
    {
        if (!$value)
            return false;
        $this->weight = $value;
    }

    function setQCCheck($value = false)
    {
        if (!$value)
            return false;
        $this->qccheck = $value;
    }

    function setSkipCourier($value = false)
    {
        if (!$value)
            return false;
        $this->skip_couriers = $value;
    }

    function getRuleBasedCourier()
    {
        $rule = $this->getMathchingRule();

        if (!$rule)
            return false;

        return $this->getCourierForRule($rule);
    }

    function getMathchingRule()
    {
        if (empty($this->user_filters))
            return false;

        foreach ($this->user_filters as $filter) {
            if ($this->checkIfRuleMatch($filter)) {
                return $filter;
            }
        }
    }

    function getCourierForRule(object $rule)
    {
        $user_couriers = $this->CI->courier_lib->userAvailableCouriers($this->user_id);
        if (!$user_couriers)
            return false;

        $courier_id = (!empty($rule->courier_to)) ? $rule->courier_to : false;

        if (empty($courier_id))
            return false;

        if (!$this->checkIfServicable($courier_id))
            return false;

        if (!isset($user_couriers[$courier_id]))
            return false;


        return $courier_id;
    }

    function checkIfRuleMatch(object $rule)
    {
        if (!$rule)
            return false;

        if (empty($rule->conditions))
            return false;

        $userArr = !empty($rule->user_id) ? explode(",", $rule->user_id) : '';
        if (!empty($userArr) && !in_array($this->user_id, $userArr))
            return false;

        $matches = array();

        foreach ($rule->conditions as $condition) {
            $matches[] = $this->checkIfConditionMatch($condition);
        }

        if ($rule->filter_type == 'or') {
            foreach ($matches as $match) {
                if ($match)
                    return true;
            }
            return false;
        }

        if ($rule->filter_type == 'and') {
            foreach ($matches as $match) {
                if (!$match)
                    return false;
            }
            return true;
        }
    }

    function checkIfConditionMatch(object $condition)
    {
        if (!$condition)
            return false;

        $fields = array(
            'courier_type' => $this->courier_type,
            'payment_type' => $this->payment_mode,
            'pickup_pincode' => $this->pickup_pincode,
            'delivery_pincode' => $this->delivery_pincode,
            'zone' => $this->getZone(),
            'weight' => $this->weight,
        );

        if (!isset($fields[$condition->field]))
            return false;

        $field_value = strtolower($fields[$condition->field]);

        if (empty($field_value))
            return false;

        $comparison = $condition->condition;

        $comparison_value = strtolower($condition->value);

        switch ($comparison) {
            case 'is':
                if ($field_value == $comparison_value)
                    return true;
                break;
            case 'is_not':
                if ($field_value != $comparison_value)
                    return true;
                break;
            case 'is_not':
                if ($field_value != $comparison_value)
                    return true;
                break;
            case 'greater_than':
                if ($field_value > $comparison_value)
                    return true;
                break;
            case 'less_than':
                if ($field_value < $comparison_value)
                    return true;
                break;
            case 'starts_with':
                if (substr($field_value, 0, strlen($comparison_value)) === $comparison_value)
                    return true;
                break;
            case 'contain':
                if (strpos($field_value, $comparison_value) !== false)
                    return true;
                break;
            case 'any_of':
                $comparison_value = array_map('trim', explode(',', $comparison_value));
                if (in_array($field_value, $comparison_value))
                    return true;
                break;
            default:
                return false;
        }

        return false;
    }

    function getUserfilters($user_id = false, $active = false,$courier_id= false)
    {
        if (!$user_id)
            return false;

        $filters = $this->CI->courier_allocation_model->getUserfilters($user_id, $active,$courier_id);
        if (!empty($filters))
            foreach ($filters as $fil) {
                $fil->conditions = $this->decodeConditions($fil->conditions);
            }

        return $filters;
    }

    function getByID($id = false)
    {
        if (!$id)
            return false;

        $filter = $this->CI->courier_allocation_model->getByID($id);
        if (!empty($filter))
            $filter->conditions = $this->decodeConditions($filter->conditions);

        return $filter;
    }

    function decodeConditions($conditions = false)
    {
        if (!$conditions)
            return false;

        return json_decode(base64_decode($conditions));
    }

    function checkIfServicable(int $courier_id)
    {
        $pincode_lib = new Pincode_lib();
        if (!$pincode_lib->checkPickupServiceByCourier($this->pickup_pincode, $courier_id))
            return false;

        if (!$pincode_lib->checkPincodeServiceByCourier($this->delivery_pincode, $courier_id, $this->payment_mode))
            return false;

        return true;
    }
}