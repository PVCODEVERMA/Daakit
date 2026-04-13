<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Segment_lib extends MY_lib
{

    protected $query = array();

    protected $fields = array(
        'shipping_fname' => 'shipping_fname',
        'shipping_lname' => 'shipping_lname',
        'phone' => 'shipping_phone',
        'address' => 'shipping_address',
        'address_2' => 'shipping_address_2',
        'pincode' => 'shipping_zip',
        'payment_type' => 'order_payment_type',
        'order_amount' => 'order_amount',
        'order_status' => 'fulfillment_status',
        'weight' => 'package_weight',
        'product_name' => 'order_products.product_name',
        'product_sku' => 'order_products.product_sku',
        'product_qty' => 'order_products.product_qty',
    );

    public function __construct()
    {
        parent::__construct();

        $this->CI->load->model('segment_model');

        add_filter('order_filters.list', $this, '_listOrderFilters', 1);
        add_filter('order_filters.apply_filter', $this, '_applyOrderFilter', 1);
    }

    function _listOrderFilters($filters = array(), $user_id = false)
    {

        if (!$user_id)
            return $filters;

        $user_filters = $this->getUserfilters($user_id);


        if (empty($user_filters))
            return $filters;

        foreach ($user_filters as $u_f) {
            $filters[] = array(
                'id' => $u_f->id,
                'name' => $u_f->filter_name,
            );
        }

        return $filters;
    }

    function _applyOrderFilter($filters = array(), $filter_id = false, $user_id = false)
    {
        if (!$user_id || empty($filter_id))
            return $filters;

        $filter = $this->getByID($filter_id);


        if (empty($filter) || $filter->user_id != $user_id)
            return $filters;


        $filter_rules = $filter->conditions;


        if (empty($filter_rules))
            return $filters;




        foreach ($filter_rules as $f_r) {
            $this->apply_rule($f_r);
        }

        $filters['user_filters_query'] = "( " . implode($filter->filter_type, $this->query) . " )";
     

        return $filters;
    }

    function apply_rule(stdclass $f_r)
    {
        if (empty($f_r))
            return false;

        if (!array_key_exists($f_r->field, $this->fields))
            return false;

        $field = $this->fields[$f_r->field];
      

        $values = array_map('trim', explode(',', strtolower($f_r->value)));

        switch ($f_r->condition) {
            case 'is':
            case 'any_of':
                foreach ($values as $value) {
                    $query[] = $field . " = '{$value}'";
                }
                break;
            case 'is_not':
                foreach ($values as $value) {
                    $query[] = $field . " != '{$value}'";
                }
                break;
            case 'contain':
                foreach ($values as $value) {
                    $query[] = $field . " like '%{$value}%'";
                }
                break;
            case 'does_not_contain':
                foreach ($values as $value) {
                    $query[] = $f_r->field . " not like '%{$value}%'";
                }
                break;
            case 'starts_with':
                foreach ($values as $value) {
                    $query[] = $field . " like '{$value}%'";
                }
                break;
            case 'greater_than':
                foreach ($values as $value) {
                    $query[] = $field . " > {$value}";
                }
                break;
            case 'less_than':
                foreach ($values as $value) {
                    $query[] = $field . " < {$value}";
                }
                break;
            case 'words_gt':
                foreach ($values as $value) {
                    $query[] = " length({$field}) > {$value}";
                }
                break;
            case 'words_lt':
                foreach ($values as $value) {
                    $query[] = " length({$field}) < {$value}";
                }
                break;
        }
        if($field=='shipping_zip' && $f_r->condition=='is_not'){
            $this->query[] = " ( " . implode(' AND ', $query) . " ) ";
        }else{
            $this->query[] = " ( " . implode(' OR ', $query) . " ) ";
        }
      
    }

    public function __call($method, $arguments)
    {
        if (!method_exists($this->CI->segment_model, $method)) {
            throw new Exception('Undefined method segment_model::' . $method . '() called');
        }

        return call_user_func_array([$this->CI->segment_model, $method], $arguments);
    }

    function getUserfilters($user_id = false, $active = false)
    {
        if (!$user_id)
            return false;

        $filters = $this->CI->segment_model->getUserfilters($user_id, $active);
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

        $filter = $this->CI->segment_model->getByID($id);
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
}
