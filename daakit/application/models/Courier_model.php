<?php

class Courier_model extends MY_model
{

    public function __construct()
    {
        parent::__construct();
        $this->table = 'courier';
        $this->user_courier = 'user_couriers';
    }

    function list_couriers($active = false, $order_type = 'ecom')
    {
        $this->db->where('status', '1');
        $this->db->where('order_type', $order_type);
        $this->db->where('is_deleted', '0');
        $this->db->order_by('name', 'asc');
        $q = $this->db->get($this->table);
        return $q->result();
    }

    function list_couriers_easyecom($active = false, $order_type = 'ecom')
    {
        $this->db->where_not_in('easyecom_code', '0');
        $this->db->where('status', '1');
        $this->db->where('order_type', $order_type);
        $this->db->where('is_deleted', '0');
        $this->db->order_by('name', 'asc');
        $q = $this->db->get($this->table);
        return $q->result();
    }

    function getByCode($active = false, $order_type = 'ecom')
    {
        $this->db->where_not_in('easyecom_code', '0');
        $this->db->where('status', '1');
        $this->db->where('order_type', $order_type);
        $this->db->where('is_deleted', '0');
        $this->db->order_by('name', 'asc');
        $q = $this->db->get($this->table);
        return $q->result();
    }

    function list_cargo_couriers($active = false, $order_type = 'cargo')
    {
        $this->db->where('status', '1');
        $this->db->where('order_type', $order_type);
        $this->db->where('is_deleted', '0');
        $this->db->order_by('name', 'asc');
        $q = $this->db->get($this->table);
        return $q->result();
    }

    function list_int_couriers($active = false, $order_type = 'international')
    {
        $this->db->where('status', '1');
        $this->db->where('order_type', $order_type);
        $this->db->where('is_deleted', '0');
        $this->db->order_by('name', 'asc');
        $q = $this->db->get($this->table);
        return $q->result();
    }

    function listAllCouriers($order_type = false)
    {
        if($order_type != '') { $this->db->where('order_type', $order_type); }
        $this->db->where('is_deleted', '0');
        $this->db->order_by('id', 'asc');
        $q = $this->db->get($this->table);
        return $q->result();
    }

    function showingToUsers($order_type = false)
    {
        if($order_type != '') { $this->db->where('order_type', $order_type); }
        $this->db->where('show_to_users', '1');
        $this->db->where('is_deleted', '0');
        $this->db->order_by('name', 'ASC');
        $q = $this->db->get($this->table);
        return $q->result();
    }

    function parentCourier($order_type = false)
    {
        $this->db->select('display_name');
        if($order_type != '') {
            $this->db->where('order_type', $order_type);
        }
        $this->db->where('status', '1');
        $this->db->where('show_to_users', '1');
        $this->db->where('is_deleted', '0');
        $this->db->group_by('display_name');
        $this->db->order_by('name', 'ASC');
        $q = $this->db->get('courier');
        return $q->result();
    }

    function approvedToUser($user_id = false)
    {
        if (!$user_id)
            return false;

        $this->db->where('user_id', $user_id);
        $this->db->limit(1);
        $q = $this->db->get($this->user_courier);
        return $q->row();
    }

    function getCouriersByWeightSlabAndCourierType($order_type = false)
    {
        $this->db->select('weight, additional_weight, courier_type,courier_alias');
        if($order_type != '') { $this->db->where('order_type', $order_type); }
        $this->db->order_by('courier_type asc');
        $this->db->order_by('abs(weight) asc');
        $this->db->group_by(array('weight','courier_type'));
        $q = $this->db->get($this->table);
        return $q->result();
    }

    function getAllCouriersDetail() {
        $this->db->select('id, display_name, name');
        $this->db->where('show_to_users', '1');
        $this->db->where('is_deleted', '0');
        $this->db->where('status','1'); 
        $this->db->order_by('name', 'ASC');
        $query = $this->db->get('courier'); 
        return $query->result(); 
    }

    function getAllOrderCouriersDetail($user_id = false, $filter = array()) {
        if (!$user_id)
            return false;

        $this->db->select(""
        . "sum(case when (order_shipping.ship_status = 'booked') then 1 else 0 end) as booked,"
        . "sum(case when (order_shipping.ship_status = 'pending pickup') then 1 else 0 end) as pending_pickup,"
        . "sum(case when (order_shipping.ship_status = 'in transit' || order_shipping.ship_status = 'out for delivery' || order_shipping.ship_status = 'exception' || order_shipping.ship_status='lost' || order_shipping.ship_status ='damaged') then 1 else 0 end) as in_transit,"
        . "sum(case when (order_shipping.ship_status = 'delivered') then 1 else 0 end) as delivered,"
        . "sum(case when (order_shipping.ship_status = 'rto') then 1 else 0 end) as rto,"
        . "sum(case when (order_shipping.ship_status != 'cancelled' AND order_shipping.ship_status != 'new') then 1 else 0 end) as  product_qty,"
        . "order_shipping.courier_id as courier_id,");

        if (!empty($filter['start_date'])) {
            $this->db->where("order_shipping.created >= '" . $filter['start_date'] . "'");
        }

        if (!empty($filter['end_date'])) {
            $this->db->where("order_shipping.created <= '" . $filter['end_date'] . "'");
        }

        if (!empty($filter['name'])) {
            $this->db->where('courier.name', $filter['name']);
        }
        if (!empty($filter['payment_type'])) {
            $this->db->where('order_shipping.payment_type', $filter['payment_type']);
        }

        $this->db->order_by('product_qty', 'desc');
        $this->db->join('courier','order_shipping.courier_id = courier.id');
        $this->db->join('orders', 'orders.id = order_shipping.order_id', 'LEFT');
        $this->db->where('order_shipping.user_id =', $user_id);
        $this->db->where('order_shipping.shipment_type =','ecom');
        $this->db->group_by('order_shipping.courier_id');
        $query = $this->db->get('order_shipping');
        return $query->result();
    } 
   
    function getByCourierid($courier_id = false)
    {
        if (!$courier_id)
            return false;

        $this->db->select('id, name, display_name,courier_type');
        $this->db->where('id', $courier_id);
        $this->db->limit(1);
        $q = $this->db->get($this->table);
        return $q->row();
    }

    function getCouriersByDisplayName($order_type = false) {
        $this->db->select('id, name, display_name,courier_type');
        if($order_type != '') { $this->db->where('order_type', $order_type); }
        $query = $this->db->get($this->table); 
        return $query->result(); 
    }

    function getLocationname($code) {
        
        $this->db->select('state_name,city_name');
        $this->db->where('service_center', $code);
        $this->db->limit(1);
        $query = $this->db->get('sort_codes'); 
        return $query->result(); 
    }

   public function get_active_couriers()
    {
        return $this->db
            ->select('id, name')
            ->from('tbl_courier')
            ->where('status', '1')
            ->get()
            ->result();
    }

    public function get_user_disabled_couriers($user_id)
    {
        $row = $this->db->get_where('tbl_user_couriers', ['user_id' => $user_id])->row();

        if ($row && $row->disabled_couriers) {
            return explode(',', $row->disabled_couriers);
        }

        return [];
    }

    public function update_user_disabled_couriers($user_id, $disabled_ids = [])
    {
        $disabled_str = is_array($disabled_ids) ? implode(',', $disabled_ids) : '';

        $exists = $this->db->get_where('tbl_user_couriers', ['user_id' => $user_id])->row();

        if ($exists) {
            return $this->db->where('user_id', $user_id)->update('tbl_user_couriers', ['disabled_couriers' => $disabled_str]);
        } else {
            return $this->db->insert('tbl_user_couriers', [
                'user_id' => $user_id,
                'disabled_couriers' => $disabled_str
            ]);
        }
    }

    public function get_active_courier_ids() {
  $result = $this->db->select('id')->from('tbl_courier')->where('status', '1')->get()->result_array();
  return array_column($result, 'id');
}
}