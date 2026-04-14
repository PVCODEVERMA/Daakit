<?php

class Pincode_model extends MY_model
{

    public function __construct()
    {
        parent::__construct();
        $this->table = 'tbl_pincodes';
        $this->pincode_list = 'tbl_pincode_list';
        $this->cred_pincode_table = 'tbl_cred_pincodes';
    }


    function insert($save = array())
    {
        if (empty($save))
            return false;

        $this->db->insert($this->pincode_list, $save);
        return true;
    }

    function serviceablePincodesList()
    {
        $this->db->select("pincodes.pincode, pincodes.city, pincodes.state_code, pincodes.cod, pincodes.prepaid, pincodes.pickup");
        $this->db->where(" (pincodes.cod = 'Y' or pincodes.prepaid = 'Y' or pincodes.pickup = 'Y' ) ");
        $this->db->where(" courier.status = '1' ");

        $this->db->order_by('pincodes.pincode', 'asc');
        $this->db->join('courier', 'courier.id = pincodes.courier_id');

        $this->db->from($this->table);
        return $query =   $this->db->get_compiled_select();
    }

    function getPincodeService($pincode = false, $method = false, $order_type = 'ecom')
    {
        if (!$pincode)
            return false;

        $this->db->where('pincode', $pincode);
        $this->db->where('courier.status', '1');

        $this->db->where('courier.order_type', $order_type);

        if (strtoupper($method) == 'PREPAID')
            $this->db->where('prepaid', 'Y');
        else
            $this->db->where('cod', 'Y');

        $this->db->group_by('courier_id');

        $this->db->join('courier', 'courier.id = pincodes.courier_id');

        $q = $this->db->get($this->table);
        return $q->result();
    }

    function getReversePincodeService($pincode = false, $order_type = 'ecom')
    {
        if (!$pincode)
            return false;

        $this->db->where('pincode', $pincode);
        $this->db->where('courier.status', '1');
        $this->db->where('courier.reverse_pickup', '1');

        $this->db->where('prepaid', 'Y');

        $this->db->where('courier.order_type', $order_type);

        $this->db->group_by('courier_id');

        $this->db->join('courier', 'courier.id = pincodes.courier_id');

        $q = $this->db->get($this->table);
        return $q->result();
    }

    function checkPincodeServiceByCourier($pincode = false, $courier_id = false, $method = false)
    {
        $this->db->where('pincode', $pincode);
        $this->db->where('courier_id', $courier_id);

        if (strtoupper($method) == 'PREPAID')
            $this->db->where('prepaid', 'Y');
        else
            $this->db->where('cod', 'Y');

        $this->db->limit(1);
        $q = $this->db->get($this->table);
        $row = $q->row();
        return !empty($row) ? $row : FALSE;
    }

    function checkReversePincodeServiceByCourier($pincode = false, $courier_id = false)
    {
        $this->db->where('pincode', $pincode);
        $this->db->where('courier_id', $courier_id);
        $this->db->where('courier.reverse_pickup', '1');

        $this->db->where('prepaid', 'Y');

        $this->db->join('courier', 'courier.id = pincodes.courier_id');

        $this->db->limit(1);
        $q = $this->db->get($this->table);
        $row = $q->row();
        return !empty($row) ? $row : FALSE;
    }

    function getPickupService($pincode = false, $order_type = false)
    {
        if (!$pincode)
            return false;

        $this->db->where('pincode', $pincode);
        $this->db->where('courier.status', '1');

        if ($order_type) $this->db->where('courier.order_type', $order_type);

        $this->db->where('pickup', 'Y');

        $this->db->group_by('courier_id');
        $this->db->join('courier', 'courier.id = pincodes.courier_id');

        $q = $this->db->get($this->table);
        return $q->result();
    }

    function getReversePickupAvailabel($pincode = false)
    {
        if (!$pincode)
            return false;

        $this->db->where('pincode', $pincode);
        $this->db->where('courier.status', '1');
        $this->db->where('courier.reverse_pickup', '1');

        $this->db->where('is_reverse_pickup', 'Y');

        $this->db->group_by('courier_id');
        $this->db->join('courier', 'courier.id = pincodes.courier_id');

        $q = $this->db->get($this->table);
        return $q->result();
    }

    function checkPickupServiceByCourier($pincode = false, $courier_id = false)
    {
        $this->db->where('pincode', $pincode);
        $this->db->where('courier_id', $courier_id);

        $this->db->where('pickup', 'Y');

        $this->db->limit(1);
        $q = $this->db->get($this->table);
        $row = $q->row();
        return !empty($row) ? $row : FALSE;
    }

    function checkReversePickupServiceByCourier($pincode = false, $courier_id = false)
    {
        $this->db->where('pincode', $pincode);
        $this->db->where('courier_id', $courier_id);
        $this->db->where('courier.reverse_pickup', '1');

        $this->db->where('is_reverse_pickup', 'Y');

        $this->db->join('courier', 'courier.id = pincodes.courier_id');

        $this->db->limit(1);
        $q = $this->db->get($this->table);
        $row = $q->row();
        return !empty($row) ? $row : FALSE;
    }

    function getDeliveryPinCodeInfo($pincode = false, $courier_id = false)
    {
        $this->db->where('pincode', $pincode);
        $this->db->where('courier_id', $courier_id);

        $this->db->limit(1);
        $q = $this->db->get($this->table);
        $row = $q->row();
        return !empty($row) ? $row : FALSE;
    }

    function get_citystate($pincode)
    {
        $this->db->select("pincode_list.city,pincode_list.state");
        $this->db->where('pincode_list.city !=', '');
        $this->db->where('pincode_list.state !=', '');
        $this->db->where('pincode_list.pincode', $pincode);
        $this->db->limit(1);
        $q = $this->db->get($this->pincode_list);
        return $q->row();
    }

    function get_pincodecitystate($pincode= false)
    {
        $this->db->select("city,state_code");
        $this->db->where('city !=', '');
        $this->db->where('state_code !=', '');
        $this->db->where('pincode', $pincode);
        $this->db->limit(1);
        $q = $this->db->get($this->table);
        return $q->row();
    }

    function get_pincodes_list()
    {
        $this->db->select('pincode, cod, prepaid');
        $this->db->group_by('pincode, cod, prepaid');
        $this->db->order_by('pincode asc');
        $q = $this->db->get('pincodes');
        return $q->result();
    }

    function getCredPickupPincodes()
    {
        $this->db->select('id,pincode');
        $this->db->where('push_time !=', strtotime(date('y-m-d')));
        $this->db->or_where('push_time', Null);
        $this->db->order_by('pincode asc');
        $this->db->limit('1');
        $q = $this->db->get($this->cred_pincode_table);
        return $q->row();
    }

    function credServiceablePincodesList($courier_id = array())
    {
        $this->db->select("pincodes.pincode, pincodes.city, pincodes.state_code, pincodes.cod, pincodes.prepaid, pincodes.pickup");
        $this->db->where(" (pincodes.cod = 'Y' or pincodes.prepaid = 'Y' or pincodes.pickup = 'Y' ) ");
        $this->db->where(" courier.status = '1' ");

        $this->db->order_by('pincodes.pincode', 'asc');
        $this->db->where_in('pincodes.courier_id',$courier_id);
        $this->db->join('courier', 'courier.id = pincodes.courier_id');

        $this->db->from($this->table);
        return $query = $this->db->get_compiled_select();
    }

    function get_user_pincodes_list($courier_ids)
    {
        $this->db->select('courier_id as courierId,pincode, cod, prepaid');
        $this->db->where_in("courier_id",$courier_ids);
        $this->db->group_by('pincode, cod, prepaid');
        $this->db->order_by('pincode asc');
        $q = $this->db->get('pincodes');
        return $q->result();
    }
}