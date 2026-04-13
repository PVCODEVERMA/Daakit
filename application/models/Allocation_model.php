<?php

class Allocation_model extends MY_model
{

    public function __construct()
    {
        parent::__construct();
        $this->table = 'allocation_rules';
    }

    function create($save = array())
    {
        if (empty($save))
            return false;

        $save['created'] = time();
        $save['modified'] = time();

        $this->db->insert($this->table, $save);
        return $this->db->insert_id();
    }

    function update($id = false, $save = array())
    {
        if (empty($save) || empty($id))
            return false;

        $save['modified'] = time();

        $this->db->set($save);
        $this->db->where('id', $id);
        $this->db->update($this->table);
        return true;
    }

    function delete($id = false)
    {
        if (!$id)
            return false;

        $this->db->where('id', $id);
        $this->db->delete($this->table);

        return true;
    }

    function getUserfilters($user_id = false, $active = false , $user_plan = 0)
    {
        if (!$user_id)
            return false;

        $this->db->where('user_id', $user_id);
        if ($active)
            $this->db->where('status', '1');

        $this->db->where('user_plan', $user_plan);

        $this->db->order_by('priority', 'asc');
        $q = $this->db->get($this->table);
        //pr($this->db->last_query(),1);
        return $q->result();
    }

    function countShipments($user_id = false, $calculated_weight = false, $zone = false, $order_type = false)
    {
        if (!$user_id)
            return false;

        if (!empty($order_type)) {
            $this->db->where('order_shipping.order_type', $order_type);
        }

        if (!empty($zone)) {
            $this->db->where('order_shipping.zone', $zone);
        }

        if (isset($calculated_weight['min']) && isset($calculated_weight['max'])) {
            $this->db->where('calculated_weight >=', (int) $calculated_weight['min']);
            $this->db->where('calculated_weight <=', (int) $calculated_weight['max']);
        }

        $this->db->select('count(*) as total');
        $this->db->where('user_id', $user_id);
        $this->db->where('order_shipping.created >', strtotime('today'));
        $this->db->where('order_shipping.created <', strtotime('tomorrow') - 1);
        $this->db->where_not_in('ship_status', array('cancelled','new'));
        $q = $this->db->get('order_shipping');
        return $q->row();
    }

    function countShipmentsByDisplayName($user_id = false, $calculated_weight = false, $zone = false, $order_type = false)
    {
        if (!$user_id)
            return false;

        if (!empty($order_type)) {
            $this->db->where('order_shipping.order_type', $order_type);
        }

        if (!empty($zone)) {
            $this->db->where('order_shipping.zone', $zone);
        }

        if (isset($calculated_weight['min']) && isset($calculated_weight['max'])) {
            $this->db->where('calculated_weight >=', (int) $calculated_weight['min']);
            $this->db->where('calculated_weight <=', (int) $calculated_weight['max']);
        }

        $this->db->select("display_name, count(*) as total");
        $this->db->where('user_id', $user_id);
        $this->db->where('order_shipping.created >', strtotime('today'));
        $this->db->where('order_shipping.created <', strtotime('tomorrow') - 1);
        $this->db->where_not_in('ship_status', array('cancelled','new'));
        $this->db->join('courier', 'courier.id = order_shipping.courier_id');
        $this->db->group_by('display_name');
        $q = $this->db->get('order_shipping');
        return $q->result();
    }
}
