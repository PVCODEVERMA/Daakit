<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Warehouse_lib extends MY_lib
{

    public function __construct()
    {
        parent::__construct();

        $this->CI->load->model('warehouse_model');
    }

    public function __call($method, $arguments)
    {
        if (!method_exists($this->CI->warehouse_model, $method)) {
            throw new Exception('Undefined method warehouse_model::' . $method . '() called');
        }

        return call_user_func_array([$this->CI->warehouse_model, $method], $arguments);
    }

    function createUpdateWarehouseWithCourier($warehouse_id = false, $update = false)
    {
        //for now only for delhivery
        if (!$warehouse_id)
            return false;

        $warehouse = $this->getByID($warehouse_id);
        if (empty($warehouse))
            return false;

        $data = array(
            'phone' => $warehouse->phone,
            'city' => $warehouse->city,
            'state' => $warehouse->state,
            'pin' => $warehouse->zip,
            'address_1' => $warehouse->address_1,
            'address_2' => $warehouse->address_2,
            'contact_person' => $warehouse->contact_name,
            'name' => 'DKT_' . $warehouse_id,
        );
        $this->CI->load->library('shipping/delhivery');

        if ($update) {
            
            //delhivery surface
            $delhivery = new Delhivery(array('mode' => 'delhivery_surface'));
            $delhivery->createUpdateWarehouse($data, true);

            //delhivery surface 2 kg
            $delhivery = new Delhivery(array('mode' => 'delhivery_surface_2kg'));
            $delhivery->createUpdateWarehouse($data, true);

            //delhivery surface 5 kg
            $delhivery = new Delhivery(array('mode' => 'delhivery_surface_5kg'));
            $delhivery->createUpdateWarehouse($data, true);

            //delhivery surface 10 kg
            $delhivery = new Delhivery(array('mode' => 'delhivery_surface_10kg'));
            $delhivery->createUpdateWarehouse($data, true);

        } else {
            //delhivery surface
            $delhivery = new Delhivery(array('mode' => 'delhivery_surface'));
            $delhivery->createUpdateWarehouse($data);

            //delhivery surface 2 kg
            $delhivery = new Delhivery(array('mode' => 'delhivery_surface_2kg'));
            $delhivery->createUpdateWarehouse($data);

            //delhivery surface 5 kg
            $delhivery = new Delhivery(array('mode' => 'delhivery_surface_5kg'));
            $delhivery->createUpdateWarehouse($data);

            //delhivery surface 10 kg
            $delhivery = new Delhivery(array('mode' => 'delhivery_surface_10kg'));
            $delhivery->createUpdateWarehouse($data);

        }

        return true;
    }

    function toogleStatus($warehouse_id = false, $user_id = false)
    {
        if (empty($warehouse_id) || empty($user_id)) {
            $this->error = 'No records found';
            return false;
        }

        $warehouse = $this->getByID($warehouse_id);

        if (empty($warehouse) || $warehouse->user_id != $user_id) {
            $this->error = 'No records found';
            return false;
        }

        if ($warehouse->is_default == '1') {
            $this->error = 'Can\'t update primary warehouse';
            return false;
        }

        $save = array(
            'active' => ($warehouse->active == '1') ? '0' : '1',
        );

        $this->update($warehouse_id, $save);
        return true;
    }

    function makeDefault($warehouse_id = false, $user_id = false)
    {
        if (empty($warehouse_id) || empty($user_id)) {
            $this->error = 'No records found';
            return false;
        }

        $warehouse = $this->getByID($warehouse_id);

        if (empty($warehouse) || $warehouse->user_id != $user_id) {
            $this->error = 'No records found';
            return false;
        }

        if ($warehouse->active == '0') {
            $this->error = 'Can\'t assign inactive warehouse as primary';
            return false;
        }

        $this->markDefault($warehouse_id, $user_id);
        return true;
    }

    function getUserWarehouseAll($user_id = false)
    {
    }
}
