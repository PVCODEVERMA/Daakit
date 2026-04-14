<?php

class Reports_model extends MY_model
{

    public function __construct()
    {
        parent::__construct();
    }

    function daily_order_summary($user_id = false, $limit = 50, $offset = 0, $filter = array())
    {
        if (!$user_id)
            return false;

        $this->db->select("DATE_FORMAT(FROM_UNIXTIME(order_shipping.created), '%Y-%m-%d') as shipment_date, "
            . "sum(case when (order_shipping.ship_status = 'in transit' || order_shipping.ship_status = 'out for delivery' ) then 1 else 0 end) as in_transit,"
            . "sum(case when (order_shipping.ship_status = 'delivered') then 1 else 0 end) as delivered,"
             . "sum(case when (order_shipping.ship_status = 'rto' and order_shipping.rto_status = 'in transit') then 1 else 0 end) as rto_in_transit,"
            . "sum(case when (order_shipping.ship_status = 'rto' and order_shipping.rto_status = 'delivered') then 1 else 0 end) as rto_delivered,"
            . "sum(case when (order_shipping.ship_status = 'exception') then 1 else 0 end) as exception,"
            . "sum(case when (order_shipping.ship_status != 'booked' && order_shipping.ship_status != 'pending pickup') then 1 else 0 end) as total,");

        if (!empty($filter['start_date'])) {
            $this->db->where("order_shipping.created >= '" . $filter['start_date'] . "'");
        }

        if (!empty($filter['end_date'])) {
            $this->db->where("order_shipping.created <= '" . $filter['end_date'] . "'");
        }

        if (!empty($filter['courier_id'])) {
            $this->db->where('order_shipping.courier_id', $filter['courier_id']);
        }

        if (!empty($filter['product_name'])) {
            $this->db->like('order_products.product_name', $filter['product_name']);
        }

        if (!empty($filter['pay_method'])) {
            $this->db->where('orders.order_payment_type', $filter['pay_method']);
        }


        $this->db->where('order_shipping.ship_status !=', 'cancelled');

        $this->db->where('order_shipping.user_id', $user_id);

        $this->db->group_by('shipment_date');

        $this->db->limit($limit);
        $this->db->offset($offset);

        $this->db->order_by('shipment_date', 'desc');

        $this->db->join('orders', 'orders.id = order_shipping.order_id', 'LEFT');

        $q = $this->db->get('order_shipping');
        return $q->result();
    }

    function count_daily_order_summary($user_id = false, $filter = array())
    {
        if (!$user_id)
            return false;

        $this->db->select("DATE_FORMAT(FROM_UNIXTIME(order_shipping.created), '%Y-%m-%d') as shipment_date, "
            . "sum(case when (order_shipping.ship_status = 'in transit') then 1 else 0 end) as in_transit,"
            . "sum(case when (order_shipping.ship_status = 'delivered') then 1 else 0 end) as delivered,"
            . "sum(case when (order_shipping.ship_status = 'rto' and order_shipping.rto_status = 'in transit') then 1 else 0 end) as rto_in_transit,"
            . "sum(case when (order_shipping.ship_status = 'rto' and order_shipping.rto_status = 'delivered') then 1 else 0 end) as rto_delivered,"
            . "sum(case when (order_shipping.ship_status = 'exception') then 1 else 0 end) as exception,"
            . "sum(case when (order_shipping.ship_status != 'booked' && order_shipping.ship_status != 'pending pickup') then 1 else 0 end) as total,");

        if (!empty($filter['start_date'])) {
            $this->db->where("order_shipping.created >= '" . $filter['start_date'] . "'");
        }

        if (!empty($filter['end_date'])) {
            $this->db->where("order_shipping.created <= '" . $filter['end_date'] . "'");
        }

        if (!empty($filter['courier_id'])) {
            $this->db->where('order_shipping.courier_id', $filter['courier_id']);
        }

        if (!empty($filter['product_name'])) {
            $this->db->like('order_products.product_name', $filter['product_name']);
        }

        if (!empty($filter['pay_method'])) {
            $this->db->where('orders.order_payment_type', $filter['pay_method']);
        }


        $this->db->where('order_shipping.ship_status !=', 'cancelled');

        $this->db->where('order_shipping.user_id', $user_id);

        $this->db->group_by('shipment_date');

        $this->db->order_by('shipment_date', 'desc');

        $this->db->join('orders', 'orders.id = order_shipping.order_id', 'LEFT');

        $q = $this->db->get('order_shipping');
        //echo $this->db->last_query();exit;
        return $q->num_rows();
    }

    function productWiseStatusDistribution($user_id = false, $start_date = false, $end_date = false)
    {
        if (!$user_id)
            return false;

        if (!$start_date)
            $start_date = strtotime("yesterday");

        if (!$end_date)
            $end_date = time();


        $this->db->select("order_products.product_name as product_name,"
            . "sum(case when (order_shipping.ship_status = 'in transit' || order_shipping.ship_status = 'out for delivery' ) then 1 else 0 end) as in_transit,"
            . "sum(case when (order_shipping.ship_status = 'delivered') then 1 else 0 end) as delivered,"
            . "sum(case when (order_shipping.ship_status = 'rto' and order_shipping.rto_status = 'in transit') then 1 else 0 end) as rto_in_transit,"
            . "sum(case when (order_shipping.ship_status = 'rto' and order_shipping.rto_status = 'delivered') then 1 else 0 end) as rto_delivered,"
            . "sum(case when (order_shipping.ship_status = 'exception') then 1 else 0 end) as exception,"
            . "sum(case when (order_shipping.ship_status != 'booked' && order_shipping.ship_status != 'pending pickup') then 1 else 0 end) as total,");


        $this->db->where('order_shipping.created >= ', $start_date);
        $this->db->where('order_shipping.created <= ', $end_date);
        $this->db->where('order_shipping.user_id', $user_id);
        $this->db->where('order_products.product_name !=', '');
        $this->db->group_by('order_products.product_name');
        $this->db->where('order_shipping.ship_status != ', 'cancelled');

        $this->db->order_by('total', 'desc');

        $this->db->join('orders', 'orders.id = order_shipping.order_id', 'LEFT');
        $this->db->join('order_products', 'order_products.order_id = orders.id', 'LEFT');

        $q = $this->db->get('order_shipping');
        return $q->result();
    }

    function pincode_wise_summary($user_id = false, $filter = array())
    {
        if (!$user_id)
            return false;

        $this->db->select("substr(orders.shipping_zip,1,2) as pincode_first_letter, "
            . "sum(case when (order_shipping.ship_status = 'in transit' || order_shipping.ship_status = 'out for delivery' ) then 1 else 0 end) as in_transit,"
            . "sum(case when (order_shipping.ship_status = 'delivered') then 1 else 0 end) as delivered,"
            . "sum(case when (order_shipping.ship_status = 'rto') then 1 else 0 end) as rto,"
            . "sum(case when (order_shipping.ship_status = 'exception') then 1 else 0 end) as exception,"
            . "sum(case when (order_shipping.ship_status != 'booked' && order_shipping.ship_status != 'pending pickup') then 1 else 0 end) as total,");

        if (!empty($filter['start_date'])) {
            $this->db->where("order_shipping.created >= '" . $filter['start_date'] . "'");
        }

        if (!empty($filter['end_date'])) {
            $this->db->where("order_shipping.created <= '" . $filter['end_date'] . "'");
        }

        if (!empty($filter['courier_id'])) {
            $this->db->where('order_shipping.courier_id', $filter['courier_id']);
        }


        if (!empty($filter['pay_method'])) {
            $this->db->where('orders.order_payment_type', $filter['pay_method']);
        }


        $this->db->where('order_shipping.ship_status !=', 'cancelled');

        $this->db->where('order_shipping.user_id', $user_id);

        $this->db->group_by('pincode_first_letter');


        $this->db->order_by('total', 'desc');

        $this->db->join('orders', 'orders.id = order_shipping.order_id', 'LEFT');

        $q = $this->db->get('order_shipping');
        return $q->result();
    }

    function courierWiseStateStatusDistribution($user_id = false, $pincodes = array(), $start_date = false, $end_date = false)
    {
        if (!$user_id || empty($pincodes))
            return false;

        if (!$start_date)
            $start_date = strtotime("yesterday");

        if (!$end_date)
            $end_date = time();


        $this->db->select("courier.name as courier_name,"
            . "substr(orders.shipping_zip,1,2) as pincode_first_letter,"
            . "sum(case when (order_shipping.ship_status = 'in transit' || order_shipping.ship_status = 'out for delivery' ) then 1 else 0 end) as in_transit,"
            . "sum(case when (order_shipping.ship_status = 'delivered') then 1 else 0 end) as delivered,"
            . "sum(case when (order_shipping.ship_status = 'rto') then 1 else 0 end) as rto,"
            . "sum(case when (order_shipping.ship_status = 'exception') then 1 else 0 end) as exception,"
            . "sum(case when (order_shipping.ship_status != 'booked' && order_shipping.ship_status != 'pending pickup') then 1 else 0 end) as total,");


        $this->db->where('order_shipping.created >= ', $start_date);
        $this->db->where('order_shipping.created <= ', $end_date);

        //$this->db->having("pincode_first_letter in (" . implode(',', $pincodes) . ")");

        $like = array();
        foreach ($pincodes as $pin) {
            $like[] = " orders.shipping_zip like '" . $pin . "%' ";
        }

        $like = implode('OR', $like);

        $this->db->where(" ({$like}) ");

        $this->db->where('order_shipping.user_id', $user_id);

        $this->db->where('order_shipping.ship_status != ', 'cancelled');

        $this->db->order_by('total', 'desc');
        $this->db->group_by('courier_name');

        $this->db->join('orders', 'orders.id = order_shipping.order_id', 'LEFT');
        $this->db->join('courier', 'courier.id = order_shipping.courier_id', 'LEFT');

        $q = $this->db->get('order_shipping');

        return $q->result();
    }

    function shipmentsByUserID($filter = array())
    {


        $this->db->select("orders.*, order_shipping.*, order_products.*, order_shipping.id as shipment_id, orders.order_id as order_number,order_shipping.created as shipment_date, warehouse.name as warehouse_name, warehouse.contact_name as warehouse_contact_name, warehouse.phone as warehouse_phone, warehouse.address_1 as warehouse_address_1, warehouse.address_2 as warehouse_address_2, warehouse.city as warehouse_city, warehouse.state as warehouse_state,
         warehouse.zip as warehouse_zip

         ");

        //group_concat(ndr.shipment_id,'<->',ndr_action.attempt,'<->',ndr_action.event_time,'<->',ndr_action.remarks SEPARATOR '|||') as remarks
        if (!empty($filter['user_id'])) {
            $this->db->where('order_shipping.user_id', $filter['user_id']);
        }

        if (!empty($filter['start_date'])) {
            $this->db->where("order_shipping.created >= '" . $filter['start_date'] . "'");
        }

        if (!empty($filter['end_date'])) {
            $this->db->where("order_shipping.created <= '" . $filter['end_date'] . "'");
        }

        $this->db->join('orders', 'orders.id = order_shipping.order_id', 'left');
        $this->db->join('order_products', 'order_products.order_id = orders.id', 'left');
        $this->db->join('warehouse', 'warehouse.id = order_shipping.warehouse_id', 'left');
        $this->db->join('ndr', "ndr.shipment_id = order_shipping.id", 'left');
        $this->db->join('ndr_action', "ndr_action.ndr_id = ndr.id and ndr_action.source='courier'", 'left');
        $this->db->order_by('order_shipping.created', 'desc');
        // $this->db->group_by('order_shipping.id');
        $this->db->from('order_shipping');
        return $query = $this->db->get_compiled_select();
    }
}
