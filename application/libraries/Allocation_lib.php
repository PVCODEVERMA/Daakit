<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Allocation_lib extends MY_lib
{
    protected $user_id = false;
    protected $user_data = false;
    protected $custom_plan = false;
    protected $product_name = false;
    protected $product_sku = false;
    protected $payment_mode = false;
    protected $order_amount = 0;
    protected $pickup_pincode = false;
    protected $delivery_pincode = false;
    protected $zone = false;
    protected $weight = 0;
    protected $user_filters = false;
    protected $length = 10;
    protected $height = 10;
    protected $breadth = 10;
    protected $is_api_order = false;
    protected $is_cred_user = false;
    protected $cred_users = false;
    protected $order_details = false;
    protected $all_errors = false;

    protected $skip_courier = array();

    public function __construct($is_api_order = false)
    {
        parent::__construct();
        $this->CI->load->library('user_lib');
        $this->CI->load->library('pincode_lib');
        $this->CI->load->library('pricing_lib');
        $this->CI->load->library('courier_lib');
        $this->CI->load->library('plans_lib');

        $this->CI->load->model('allocation_model');

        $this->is_api_order = $is_api_order;
        $this->dg_order = false;
        $this->cred_users = [52429,66933,68549];
        $this->order_details = false;
        $this->all_errors = [];

        add_filter('order_ship.available_couriers', $this, '_checkAutoShip', 1);
        add_filter('order_ship.courier_filter', $this, '_getAutoShipCourier', 1);
    }

    function _checkAutoShip($couriers = array(), $user_id = false)
    {
        if (!$user_id)
            return $couriers;

        //check if user has auto ship rules
        $user_rule = $this->getUserfilters($user_id, true);
        if (empty($user_rule) && !in_array($user_id, $this->cred_users)){
            $user_couriers = $this->CI->courier_lib->userAvailableCouriers($user_id);
            if (!empty($user_couriers)) {
                foreach ($user_couriers as $key => $courier) {
                    if (!array_key_exists($courier->id, $couriers)) {
                        unset($couriers[$key]);
                    }
                }
            }
            return $couriers;
        }

        $autoship = (object) array(
            'id' => 'autoship',
            'courier_id' => 'autoship',
            'name' => "<i class='mdi mdi-flash'></i> Autoship"
        );

        array_unshift($couriers, $autoship);

        return $couriers;
    }

    function _getAutoShipCourier($courier_id = false, $order = false, $warehouse = false, $is_api_order = false)
    {
        if (empty($order) || empty($warehouse))
            return $courier_id;

        if ($courier_id != 'autoship')
            return $courier_id;

        /*if ($courier_id != 'autoship') {
            $cr_id = $courier_id;

            $chk_courier_id = $this->checkIfServicable($courier_id);

            $cr = "($cr_id): ";
            $this->all_errors[$cr_id] = $cr . $this->get_error();

            if(!empty($this->all_errors)) {
                $save = [
                    'order_id' => $order->order_id,
                    'response_data' => json_encode($this->all_errors),
                    'created' => time()
                ];

                $this->CI->db->insert('cred_errors', $save);

                do_action('log.create', 'shipment', [
                    'action' => 'shipment_errors',
                    'ref_id' => $order->order_id,
                    'user_id' => $this->user_id,
                    'data' => $this->all_errors
                ]);
            }

            return $chk_courier_id;
        }*/

        $allocation = new Allocation_lib($is_api_order);

        $allocation->setOrderDetails($order);

        $allocation->setUserID($order->user_id);

        $allocation->setProductName($order->order_products_grouped);
        $allocation->setProductSKU($order->order_sku_grouped);

        $allocation->setPaymentMode($order->order_payment_type);
        $allocation->setOrderAmount($order->order_amount);

        $allocation->setPickupPincode($warehouse->zip);
        $allocation->setDeliveryPincode($order->shipping_zip);

        $allocation->setWeight($order->package_weight);
        $allocation->setLength($order->package_length);
        $allocation->setBreadth($order->package_breadth);
        $allocation->setHeight($order->package_height);
        $allocation->setOrderSource($order->order_source);
        if (!empty($order->dg_order))
            $allocation->setDangersGoodsFlag($order->dg_order);

        return $allocation->getRuleBasedCourier();
    }

    public function __call($method, $arguments)
    {
        if (!method_exists($this->CI->allocation_model, $method)) {
            throw new Exception('Undefined method allocation_model::' . $method . '() called');
        }

        return call_user_func_array([$this->CI->allocation_model, $method], $arguments);
    }

    function setOrderDetails($value = false)
    {
        $this->order_details = $value;
    }

    function setUserID($value = false)
    {
        if (!$value)
            return false;

        $this->user_id = $value;

        $this->is_cred_user = false;

        if(in_array($this->user_id, $this->cred_users)) {
            $this->is_cred_user = 1;
        }
        $this->user_data = $this->CI->user_lib->getByID($this->user_id);
        $this->custom_plan = $this->CI->plans_lib->getCustomPlanByName($this->user_data->pricing_plan);
        $this->user_filters = $this->getUserfilters($this->user_id, true);

    }

    function setProductName($value = false)
    {
        $this->product_name = strtolower($value);
    }

    function setProductSKU($value = false)
    {
        $this->product_sku = strtolower($value);
    }

    function setPaymentMode($value = false)
    {
        $this->payment_mode = strtolower($value);
    }

    function setOrderAmount($value = false)
    {
        $this->order_amount = $value;
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

    function setLength($value = false)
    {
        if (!$value)
            return false;

        $this->length = $value;
    }
    
    function setBreadth($value = false)
    {
        if (!$value)
            return false;

        $this->breadth = $value;
    }
    function setHeight($value = false)
    {
        if (!$value)
            return false;

        $this->height = $value;
    }

    function setOrderSource($value = false)
    {
        $this->order_source = $value;
    }

    function setDangersGoodsFlag($value = false)
    {
        $this->dg_order = $value;
    }

    function setSkipCourier(int $courier_id)
    {
        $this->skip_courier[] = $courier_id;
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

        // if (empty($rule->courier_priority_5)) {
        //     $rule->courier_priority_5 = '5';
        //     $rule->courier_priority_6 = '15';
        //     $rule->courier_priority_7 = '1';
        //     $rule->courier_priority_8 = '3';
        //     $rule->courier_priority_9 = '4';
        //     $rule->courier_priority_10 = '79';
        //     $rule->courier_priority_11 = '80';
        //     $rule->courier_priority_12 = '10';
        // }
        if(!empty($this->custom_plan) && $this->custom_plan->plan_type=='smart')
        {
            $plan_pricing = $this->CI->plans_lib->getSmartPlanById($this->custom_plan->id,'1');
            if(empty($plan_pricing))
                return false;

            $plan_pricing=array_column($plan_pricing,'courier_type_weight');

            for ($i = 1; $i <= 8; $i++) {
                $courier_priority = 'courier_priority_' . $i;
                $courier_id = (!empty($rule->{$courier_priority})) ? $rule->{$courier_priority} : false; 
                
                if (empty($courier_id))
                    continue;
                   
                if (!in_array($courier_id, $plan_pricing)) 
                    continue;
                
                return $courier_id;
            }    
        }else{
            for ($i = 1; $i <= 8; $i++) {
                $courier_priority = 'courier_priority_' . $i;
                $courier_id = (!empty($rule->{$courier_priority})) ? $rule->{$courier_priority} : false;
    
                if (empty($courier_id))
                    continue;
    
                if (!array_key_exists($courier_id, $user_couriers)) {
                    continue;
                }
                $courier = $this->CI->courier_lib->getByID($courier_id);
    
                $cr = "$courier->display_name($courier_id): ";
    
                if (in_array($courier_id, $this->skip_courier)) { // || !isset($user_couriers[$courier_id])
                    $this->all_errors[$courier_id] = $cr . 'skip courier';
                    continue;
                }
    
                //check if courier is servicable
                if (!$this->checkIfServicable($courier_id)) {
                    $this->all_errors[$courier_id] = $cr . $this->get_error();
                    continue;
                }
                return $courier_id;
            }
        }

        if(!empty($this->all_errors)) {
            do_action('log.create', 'AUTOSHIP_RULE', [
                'action' => 'autoship_rule_log',
                'ref_id' => !empty($this->order_details->order_no) ? $this->order_details->order_no : '',
                'user_id' => $this->user_id,
                'data' => ['rule_matched'=>$rule->id,'error'=>$this->all_errors]
            ]);
        }
    }

    function checkIfRuleMatch(object $rule)
    {
        if (!$rule)
            return false;

        if (empty($rule->conditions))
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
            'payment_type' => $this->payment_mode,
            'order_amount' => $this->order_amount,
            'pickup_pincode' => $this->pickup_pincode,
            'delivery_pincode' => $this->delivery_pincode,
            'zone' => $this->getZone(),
            'weight' => $this->weight,
            'product_name' => $this->product_name,
            'product_sku' => $this->product_sku,
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

    function getUserfilters($user_id = false, $active = false ,$user_plan= 0)
    {
        if (!$user_id)
            return false;

        if(!empty($this->custom_plan) && $this->custom_plan->plan_type=='smart')
            $user_plan=1;

        $filters = $this->CI->allocation_model->getUserfilters($user_id, $active , $user_plan);
        if (!empty($filters)) {
            foreach ($filters as $fil) {
                $fil->conditions = $this->decodeConditions($fil->conditions);
            }
        } else if ($this->is_cred_user) {
            //set default rules set inside config.
            $this->CI->load->config('allocation');
            $filters = $this->CI->config->item('cred_allocation_rules');
            $filters = json_decode(json_encode($filters));
        } else if ($this->is_api_order) {
            //set default rules set inside config.
            $this->CI->load->config('allocation');
            $filters = $this->CI->config->item('default_allocation_rules');
            $filters = json_decode(json_encode($filters));
        }

        return $filters;
    }

    function getByID($id = false)
    {
        if (!$id)
            return false;

        $filter = $this->CI->allocation_model->getByID($id);
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
        if ($this->payment_mode == 'reverse') {
            //pincodes will reverse for reverse pickup

            //check if pickup available
            if (!$pincode_lib->checkReversePickupServiceByCourier($this->delivery_pincode, $courier_id)) {
                $this->error = 'Pickup pincode (' . $this->delivery_pincode . ') not available.';
                return false;
            }

            //check if delivery available
            if (!$pincode_lib->checkReversePincodeServiceByCourier($this->pickup_pincode, $courier_id)) {
                $this->error = 'Destination pincode (' . $this->pickup_pincode . ') not available.';
                return false;
            }

            return true;
        }

        if (!$pincode_lib->checkPickupServiceByCourier($this->pickup_pincode, $courier_id)) {
            $this->error = 'Pickup pincode (' . $this->pickup_pincode . ') not available.';
            return false;
        }

        if (!$pincode_lib->checkPincodeServiceByCourier($this->delivery_pincode, $courier_id, $this->payment_mode)) {
            $this->error = 'Destination pincode (' . $this->delivery_pincode . ') not available.';
            return false;
        }

        return true;
    }

    private function executeCredAllocation($skip_condition = false) {
        $zone = $this->getZone();
        if (empty($zone) || empty($this->order_details))
            return false;

        $order = $this->order_details;

        $this->CI->load->config('allocation');

        $percentage = $this->CI->config->item('cred_load_allocation_percentage');
        if($percentage)
            ksort($percentage);

        $percentage_slab = array();
        $percentage_weight = 0;
        foreach ($percentage as $c_weight => $c_percentage) {
            if($c_weight >= $order->package_weight) {
                $percentage_weight = $c_weight;
                $percentage_slab = $c_percentage;
                // asort($percentage_slab);
                break;
            }
        }

        if(empty($percentage_slab) || empty($percentage_weight))
            return false;

        $filters = $this->CI->config->item('cred_load_allocation_rules');
        $filters = json_decode(json_encode($filters));
        if(empty($filters))
            return false;

        $rule_matched = array();
        foreach ($filters as $r_weight => $r_data) {
            if($r_weight >= $order->package_weight) {
                $rule_matched = $r_data;
                break;
            }
        }

        if (empty($rule_matched))
            return false;

        if(empty($zone_couriers = $rule_matched->{$zone}))
            return false;

        $weight_slab = !empty($rule_matched->weight) ? (array) $rule_matched->weight : '';

        $count_shipments = $this->CI->allocation_model->countShipments($this->user_id, $weight_slab, $zone, 'ecom');

        $count_shipments_display_name = $this->CI->allocation_model->countShipmentsByDisplayName($this->user_id, $weight_slab, $zone, 'ecom');

        $percentage_shipment = array();
        $count_shipments_display_name_arr = array();
        foreach ($count_shipments_display_name as $shipment) {
            $percentage_shipment[strtolower($shipment->display_name)] = (!empty($count_shipments->total) && !empty($shipment->total)) ? round(($shipment->total * 100) / $count_shipments->total, 2) : 0;

            $count_shipments_display_name_arr[strtolower($shipment->display_name)] = $shipment->total;
        }

        $user_couriers = $this->CI->courier_lib->userAvailableCouriers($this->user_id);
        if (!$user_couriers)
            return false;

        if ($this->dg_order == '1') {
            $zone_couriers = $rule_matched->dg_order;
        }

        /*do_action('log.create', 'CRED', [
            'action' => 'cred_courier_rule_details',
            'ref_id' => $order->id,
            'user_id' => $this->user_id,
            'data' => array(
                'percentage_slab' => $percentage_slab,
                'percentage_weight' => $percentage_weight,
                'percentage_shipment' => $percentage_shipment,
                'count_shipments_display_name_arr' => $count_shipments_display_name_arr,
                'user_couriers' => $user_couriers,
                'zone_couriers' => $zone_couriers
            )
        ]);*/

        for ($i = 1; $i <= count((array) $zone_couriers); $i++) {
            $courier_priority = 'courier_priority_' . $i;
            $courier_id = (!empty($zone_couriers->{$courier_priority})) ? $zone_couriers->{$courier_priority} : false;

            if (empty($courier_id))
                continue;

            $courier = $this->CI->courier_lib->getByID($courier_id);

            $c_display_name = strtolower($courier->display_name);

            /*do_action('log.create', 'CRED', [
                'action' => 'cred_courier_id',
                'ref_id' => $order->id,
                'user_id' => $this->user_id,
                'courier' => $courier->display_name,
                'courier_id' => $courier_id,
                'data' => $order->order_id
            ]);*/

            $cr = "$courier->display_name($courier_id): ";

            if(empty($skip_condition)) {
                if (in_array($courier_id, $this->skip_courier) || !isset($user_couriers[$courier_id]))
                    continue;

                if(!array_key_exists($c_display_name, $percentage_slab)) {
                    $this->all_errors[$courier_id] = $cr . 'Courier not available.';
                    continue;
                }

                if (($this->dg_order == '1') && ($courier->courier_type != 'surface')) {
                    $this->all_errors[$courier_id] = $cr . 'DG order not shipped.';
                    continue;
                }

                if(empty($this->dg_order) && !empty($percentage_shipment[$c_display_name]) && !empty($percentage_slab[$c_display_name]) && ceil($percentage_shipment[$c_display_name]) > $percentage_slab[$c_display_name]) {
                    $this->all_errors[$courier_id] = $cr . 'Courier % is exceed (' . $percentage_shipment[$c_display_name] . ' instead of ' . $percentage_slab[$c_display_name] . ')';
                    continue;
                }
            }

            //check if courier is servicable
            if (!$this->checkIfServicable($courier_id)) {
                $this->all_errors[$courier_id] = $cr . $this->get_error();
                continue;
            }

            return $courier_id;
        }
    }
}