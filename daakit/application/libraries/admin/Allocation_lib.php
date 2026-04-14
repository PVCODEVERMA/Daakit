<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Allocation_lib extends MY_lib
{
    public function __construct()
    {
        parent::__construct();
        $this->CI->load->model('admin/allocation_model');
    }

    public function __call($method, $arguments)
    {
        if (!method_exists($this->CI->allocation_model, $method)) {
            throw new Exception('Undefined method allocation_model::' . $method . '() called');
        }

        return call_user_func_array([$this->CI->allocation_model, $method], $arguments);
    }

    function getUserfilters($user_id = false, $active = false, $id = false, $plan_id = false)
    {
        $filters = $this->CI->allocation_model->getUserfilters($user_id, $active, $id, $plan_id);
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
}