<?php

class Analytics_model extends MY_model
{

    public function __construct()
    {
        parent::__construct();

        $this->slave = $this->load->database('slave', TRUE);
    }

    function countUsers($start_date = false, $end_date = false)
    {
        if (!$start_date)
            $start_date = strtotime("yesterday");

        if (!$end_date)
            $end_date = time();
        $start_date = date('Y-m-d', $start_date) . ' 00:00:00';
        $end_date = date('Y-m-d', $end_date) . ' 23:59:59';
        $this->slave->select('count(*) as total');
        $this->slave->where('created >=', $start_date);
        $this->slave->where('parent_id', 0);
        $this->slave->where('created <=', $end_date);

        $q = $this->slave->get('users');
        return $q->row()->total;
    }

    function countactiveUsers($start_date = false, $end_date = false)
    {
        if (!$start_date)
            $start_date = strtotime("yesterday");

        if (!$end_date)
            $end_date = time();
        $start_date = date('Y-m-d', $start_date) . ' 00:00:00';
        $end_date = date('Y-m-d', $end_date) . ' 23:59:59';
        $this->slave->select('count(*) as total');
        $this->slave->where('created >=', $start_date);
        $this->slave->where('is_admin', '0');
        $this->slave->where('verified', '1');
        $this->slave->where('created <=', $end_date);
        $q = $this->slave->get('users');
        return $q->row()->total;
    }

    function countOrders($start_date = false, $end_date = false)
    {
        if (!$start_date)
            $start_date = strtotime("yesterday");

        if (!$end_date)
            $end_date = time();

        $this->slave->select('count(DISTINCT tbl_orders.id) as total');
        $this->slave->where('order_date >=', $start_date);
        $this->slave->where('fulfillment_status !=', 'cancelled');
        $this->slave->where('order_date <=', $end_date);
        $q = $this->slave->get('orders');
        return $q->row()->total;
    }

    function countCancelledOrders($start_date = false, $end_date = false)
    {
        if (!$start_date)
            $start_date = strtotime("yesterday");

        if (!$end_date)
            $end_date = time();
        $this->slave->select('count(DISTINCT tbl_orders.id) as total');
        $this->slave->where('order_date >=', $start_date);
        $this->slave->where('fulfillment_status', 'cancelled');
        $this->slave->where('order_date <=', $end_date);
        $q = $this->slave->get('orders');
        return $q->row()->total;
    }

    function countShipments($start_date = false, $end_date = false)
    {
        if (!$start_date)
            $start_date = strtotime("yesterday");

        if (!$end_date)
            $end_date = time();
        $this->slave->select('count(DISTINCT tbl_order_shipping.id) as total');
        $this->slave->where('created >=', $start_date);
        $this->slave->where('created <=', $end_date);
        $this->slave->where('ship_status !=', 'cancelled');
        $this->slave->where('ship_status !=', 'new');
        $q = $this->slave->get('order_shipping');
        return $q->row()->total;
    }

    function countPayments($start_date = false, $end_date = false)
    {
        if (!$start_date)
            $start_date = strtotime("yesterday");

        if (!$end_date)
            $end_date = time();
        $this->slave->select('sum(amount) as total');
        $this->slave->where('created >=', $start_date);
        $this->slave->where('created <=', $end_date);
        $this->slave->where('paid', '1');
        $q = $this->slave->get('payments');
        return $q->row()->total;
    }

    function sellerWiseOrdersCount($start_date = false, $end_date = false)
    {
        if (!$start_date)
            $start_date = strtotime("yesterday");

        if (!$end_date)
            $end_date = time();

        $this->slave->select(
            ""
                . "sum(case when (fulfillment_status = 'new') then 1 else 0 end) as new,"
                . "sum(case when (fulfillment_status = 'booked') then 1 else 0 end) as booked,"
                . "sum(1) as total,"
                . "users.id as user_id, users.company_name as company_name,"
        );
        $this->slave->where('order_date >=', $start_date);
        $this->slave->where('order_date <=', $end_date);
        $this->slave->where('orders.fulfillment_status !=', 'cancelled');
        $this->slave->order_by('total', 'desc');
        $this->slave->group_by('users.id');
        $this->slave->join('users', 'users.id = orders.user_id', 'LEFT');
        $q = $this->slave->get('orders');
        return $q->result();
    }

    function sellerWiseShipmentCount($start_date = false, $end_date = false)
    {
        if (!$start_date)
            $start_date = strtotime("yesterday");

        if (!$end_date)
            $end_date = time();

        $this->slave->select(
            ""
                . "sum(case when (tbl_order_shipping.ship_status = 'booked') then 1 else 0 end) as booked,"
                . "sum(case when (tbl_order_shipping.ship_status = 'pending pickup') then 1 else 0 end) as pending_pickup,"
                . "sum(case when (tbl_order_shipping.ship_status = 'in transit') then 1 else 0 end) as in_transit,"
                . "sum(case when (tbl_order_shipping.ship_status = 'delivered') then 1 else 0 end) as delivered,"
                . "sum(case when (tbl_order_shipping.ship_status = 'rto') then 1 else 0 end) as rto,"
                . "sum(case when (tbl_order_shipping.ship_status = 'exception') then 1 else 0 end) as exception,"
                . "sum(1) as total,"
                . "users.id as user_id, users.company_name as company_name, "
        );
        $this->slave->where('order_shipping.created >=', $start_date);
        $this->slave->where('order_shipping.created <=', $end_date);
        $this->slave->where('tbl_order_shipping.ship_status !=', 'cancelled');
        $this->slave->group_by('users.id');
        $this->slave->order_by('total', 'desc');
        $this->slave->join('orders', 'tbl_orders.id = order_shipping.order_id', 'LEFT');
        $this->slave->join('users', 'users.id = orders.user_id', 'LEFT');
        $q = $this->slave->get('order_shipping');
        return $q->result();
    }

    function courierWiseStatusDistribution($start_date = false, $end_date = false)
    {
        if (!$start_date)
            $start_date = strtotime("yesterday");

        if (!$end_date)
            $end_date = time();

        $this->slave->select(
            ""
                . "sum(case when (tbl_order_shipping.ship_status = 'booked') then 1 else 0 end) as booked,"
                . "sum(case when (tbl_order_shipping.ship_status = 'pending pickup') then 1 else 0 end) as pending_pickup,"
                . "sum(case when (tbl_order_shipping.ship_status = 'in transit') then 1 else 0 end) as in_transit,"
                . "sum(case when (tbl_order_shipping.ship_status = 'delivered') then 1 else 0 end) as delivered,"
                . "sum(case when (tbl_order_shipping.ship_status = 'rto') then 1 else 0 end) as rto,"
                . "sum(case when (tbl_order_shipping.ship_status = 'exception') then 1 else 0 end) as exception,"
                . "sum(case when (tbl_order_shipping.ship_status = 'out for delivery') then 1 else 0 end) as out_for_delivery,"
                . "sum(1) as total,"
                . "order_shipping.courier_id as courier_id,"
        );
        $this->slave->where('order_shipping.created >= ', $start_date);
        $this->slave->where('order_shipping.created <= ', $end_date);
        $this->slave->where('tbl_order_shipping.ship_status != ', 'cancelled');
        $this->slave->group_by('order_shipping.courier_id');
        $this->slave->order_by('total', 'desc');
        $q = $this->slave->get('order_shipping');
        return $q->result();
    }

    function countcurrentmonthShipments()
    {
        $start_date = strtotime(date('Y-m-01') . ' 00:00:00');

        $end_date = strtotime(date('Y-m-t') . ' 23:59:59');

        $this->slave->select('count(DISTINCT tbl_order_shipping.id) as total');
        $this->slave->where('created >=', $start_date);
        $this->slave->where('created <=', $end_date);
        $this->slave->where('ship_status !=', 'cancelled');
        $this->slave->where('ship_status !=', 'new');
        $q = $this->slave->get('order_shipping');
        return $q->row()->total;
    }

    // for b2b Order Functions
    function countcargoOrders($start_date = false, $end_date = false)
    {
        if (!$start_date)
            $start_date = strtotime("yesterday");

        if (!$end_date)
            $end_date = time();
        $this->slave->select('count(DISTINCT tbl_orders.id) as total');
        $this->slave->where('order_date >=', $start_date);
        $this->slave->where('order_date <=', $end_date);
        $this->slave->where('order_type =', 'cargo');
        $q = $this->slave->get('orders');
        return $q->row()->total;
    }

    function countcargoShipments($start_date = false, $end_date = false)
    {
        if (!$start_date)
            $start_date = strtotime("yesterday");

        if (!$end_date)
            $end_date = time();

        $this->slave->select('count(DISTINCT tbl_order_shipping.id) as total, sum(order_shipping.calculated_weight) as calculated_weight');
        $this->slave->where('order_shipping.created >=', $start_date);
        $this->slave->where('order_shipping.created <=', $end_date);
        $this->slave->where('tbl_order_shipping.ship_status !=', 'cancelled');
        $this->slave->where('tbl_order_shipping.ship_status !=', 'new');
        $this->slave->where('order_shipping.order_type =', 'cargo');
        $q = $this->slave->get('order_shipping');
        return $q->row();
    }

    function sellerWiseOrderscargoCount($start_date = false, $end_date = false)
    {
        if (!$start_date)
            $start_date = strtotime("yesterday");

        if (!$end_date)
            $end_date = time();

        $this->slave->select(
            ""
                . "sum(case when (fulfillment_status = 'new') then 1 else 0 end) as new,"
                . "sum(case when (fulfillment_status = 'booked') then 1 else 0 end) as booked,"
                . "sum(1) as total,"
                . "users.id as user_id, users.company_name as company_name,"
        );
        $this->slave->where('order_date >=', $start_date);
        $this->slave->where('order_date <=', $end_date);
        $this->slave->where('orders.order_type =', 'cargo');
        $this->slave->where('orders.fulfillment_status !=', 'cancelled');
        $this->slave->order_by('total', 'desc');
        $this->slave->group_by('users.id');
        $this->slave->join('users', 'users.id = orders.user_id', 'LEFT');
        $q = $this->slave->get('orders');
        return $q->result();
    }

    function sellerWiseShipmentcargoCount($start_date = false, $end_date = false)
    {
        if (!$start_date)
            $start_date = strtotime("yesterday");

        if (!$end_date)
            $end_date = time();

        $this->slave->select(
            ""
                . "sum(case when (tbl_order_shipping.ship_status = 'booked') then 1 else 0 end) as booked,"
                . "sum(case when (tbl_order_shipping.ship_status = 'pending pickup') then 1 else 0 end) as pending_pickup,"
                . "sum(case when (tbl_order_shipping.ship_status = 'in transit') then 1 else 0 end) as in_transit,"
                . "sum(case when (tbl_order_shipping.ship_status = 'delivered') then 1 else 0 end) as delivered,"
                . "sum(case when (tbl_order_shipping.ship_status = 'rto') then 1 else 0 end) as rto,"
                . "sum(case when (tbl_order_shipping.ship_status = 'exception') then 1 else 0 end) as exception,"
                . "sum(1) as total,"
                . "users.id as user_id, users.company_name as company_name, "
        );
        $this->slave->where('order_shipping.created >=', $start_date);
        $this->slave->where('order_shipping.created <=', $end_date);
        $this->slave->where('tbl_order_shipping.ship_status !=', 'cancelled');
        $this->slave->where('orders.order_type =', 'cargo');
        $this->slave->group_by('users.id');
        $this->slave->order_by('total', 'desc');
        $this->slave->join('orders', 'tbl_orders.id = order_shipping.order_id', 'LEFT');
        $this->slave->join('users', 'users.id = orders.user_id', 'LEFT');
        $q = $this->slave->get('order_shipping');

        return $q->result();
    }

    function courierWiseStatuscargoDistribution($start_date = false, $end_date = false)
    {
        if (!$start_date)
            $start_date = strtotime("yesterday");

        if (!$end_date)
            $end_date = time();

        $this->slave->select(
            ""
                . "sum(case when (tbl_order_shipping.ship_status = 'booked') then 1 else 0 end) as booked,"
                . "sum(case when (tbl_order_shipping.ship_status = 'pending pickup') then 1 else 0 end) as pending_pickup,"
                . "sum(case when (tbl_order_shipping.ship_status = 'in transit') then 1 else 0 end) as in_transit,"
                . "sum(case when (tbl_order_shipping.ship_status = 'delivered') then 1 else 0 end) as delivered,"
                . "sum(case when (tbl_order_shipping.ship_status = 'rto') then 1 else 0 end) as rto,"
                . "sum(case when (tbl_order_shipping.ship_status = 'exception') then 1 else 0 end) as exception,"
                . "sum(case when (tbl_order_shipping.ship_status = 'out for delivery') then 1 else 0 end) as out_for_delivery,"
                . "sum(1) as total,"
                . "order_shipping.courier_id as courier_id,"
        );
        $this->slave->where('order_shipping.created >= ', $start_date);
        $this->slave->where('order_shipping.created <= ', $end_date);
        $this->slave->where('tbl_order_shipping.ship_status != ', 'cancelled');
        $this->slave->where('order_shipping.order_type =', 'cargo');
        $this->slave->group_by('order_shipping.courier_id');
        $this->slave->order_by('total', 'desc');
        $q = $this->slave->get('order_shipping');
        return $q->result();
    }

    // for International Order Functions
    function countIntOrders($start_date = false, $end_date = false)
    {
        if (!$start_date)
            $start_date = strtotime("yesterday");

        if (!$end_date)
            $end_date = time();
        $this->slave->select('count(DISTINCT tbl_orders.id) as total');
        $this->slave->where('order_date >=', $start_date);
        $this->slave->where('order_date <=', $end_date);
        $this->slave->where('order_type =', 'international');
        $q = $this->slave->get('orders');
        return $q->row()->total;
    }
    function countIntShipments($start_date = false, $end_date = false)
    {
        if (!$start_date)
            $start_date = strtotime("yesterday");

        if (!$end_date)
            $end_date = time();
        $this->slave->select('count(DISTINCT tbl_order_shipping.id) as total');
        $this->slave->where('created >=', $start_date);
        $this->slave->where('created <=', $end_date);
        $this->slave->where('ship_status !=', 'cancelled');
        $this->slave->where('ship_status !=', 'new');
        $this->slave->where('order_type =', 'international');
        $q = $this->slave->get('order_shipping');
        return $q->row()->total;
    }

    function sellerWiseOrdersIntCount($start_date = false, $end_date = false)
    {
        if (!$start_date)
            $start_date = strtotime("yesterday");

        if (!$end_date)
            $end_date = time();

        $this->slave->select(
            ""
                . "sum(case when (fulfillment_status = 'new') then 1 else 0 end) as new,"
                . "sum(case when (fulfillment_status = 'booked') then 1 else 0 end) as booked,"
                . "sum(1) as total,"
                . "users.id as user_id, users.company_name as company_name,"
        );
        $this->slave->where('order_date >=', $start_date);
        $this->slave->where('order_date <=', $end_date);
        $this->slave->where('orders.order_type =', 'international');
        $this->slave->where('orders.fulfillment_status !=', 'cancelled');
        $this->slave->order_by('total', 'desc');
        $this->slave->group_by('users.id');
        $this->slave->join('users', 'users.id = orders.user_id', 'LEFT');
        $q = $this->slave->get('orders');
        return $q->result();
    }

    function sellerWiseShipmentIntCount($start_date = false, $end_date = false)
    {
        if (!$start_date)
            $start_date = strtotime("yesterday");

        if (!$end_date)
            $end_date = time();

        $this->slave->select(
            ""
                . "sum(case when (tbl_order_shipping.ship_status = 'booked') then 1 else 0 end) as booked,"
                . "sum(case when (tbl_order_shipping.ship_status = 'pending pickup') then 1 else 0 end) as pending_pickup,"
                . "sum(case when (tbl_order_shipping.ship_status = 'in transit') then 1 else 0 end) as in_transit,"
                . "sum(case when (tbl_order_shipping.ship_status = 'delivered') then 1 else 0 end) as delivered,"
                . "sum(case when (tbl_order_shipping.ship_status = 'rto') then 1 else 0 end) as rto,"
                . "sum(case when (tbl_order_shipping.ship_status = 'exception') then 1 else 0 end) as exception,"
                . "sum(1) as total,"
                . "users.id as user_id, users.company_name as company_name, "
        );
        $this->slave->where('order_shipping.created >=', $start_date);
        $this->slave->where('order_shipping.created <=', $end_date);
        $this->slave->where('tbl_order_shipping.ship_status !=', 'cancelled');
        $this->slave->where('orders.order_type =', 'international');
        $this->slave->group_by('users.id');
        $this->slave->order_by('total', 'desc');
        $this->slave->join('orders', 'tbl_orders.id = order_shipping.order_id', 'LEFT');
        $this->slave->join('users', 'users.id = orders.user_id', 'LEFT');
        $q = $this->slave->get('order_shipping');

        return $q->result();
    }

    function courierWiseStatusIntDistribution($start_date = false, $end_date = false)
    {
        if (!$start_date)
            $start_date = strtotime("yesterday");

        if (!$end_date)
            $end_date = time();

        $this->slave->select(
            ""
                . "sum(case when (tbl_order_shipping.ship_status = 'booked') then 1 else 0 end) as booked,"
                . "sum(case when (tbl_order_shipping.ship_status = 'pending pickup') then 1 else 0 end) as pending_pickup,"
                . "sum(case when (tbl_order_shipping.ship_status = 'in transit') then 1 else 0 end) as in_transit,"
                . "sum(case when (tbl_order_shipping.ship_status = 'delivered') then 1 else 0 end) as delivered,"
                . "sum(case when (tbl_order_shipping.ship_status = 'rto') then 1 else 0 end) as rto,"
                . "sum(case when (tbl_order_shipping.ship_status = 'exception') then 1 else 0 end) as exception,"
                . "sum(case when (tbl_order_shipping.ship_status = 'out for delivery') then 1 else 0 end) as out_for_delivery,"
                . "sum(1) as total,"
                . "order_shipping.courier_id as courier_id,"
        );
        $this->slave->where('order_shipping.created >= ', $start_date);
        $this->slave->where('order_shipping.created <= ', $end_date);
        $this->slave->where('tbl_order_shipping.ship_status != ', 'cancelled');
        $this->slave->where('order_shipping.order_type =', 'international');
        $this->slave->group_by('order_shipping.courier_id');
        $this->slave->order_by('total', 'desc');
        $q = $this->slave->get('order_shipping');
        return $q->result();
    }

    function courierWiseOrdersCount($user_id = false, $start_date = false, $end_date = false)
    {
        if (!$user_id)
            return false;

        if (!$start_date)
            $start_date = strtotime("midnight");

        if (!$end_date)
            $end_date = strtotime("tomorrow midnight") - 1;

        $this->slave->select('shipment_tracking.shipment_id, shipment_tracking.pickup_time, count(order_shipping.warehouse_id) as total_orders, order_shipping.warehouse_id, order_shipping.courier_id');
        $this->slave->where('shipment_tracking.pickup_time >= ', $start_date);
        $this->slave->where('shipment_tracking.pickup_time <= ', $end_date);
        $this->slave->where('order_shipping.user_id', $user_id);
        $this->slave->group_by(array('order_shipping.warehouse_id', 'courier.display_name'));
        $this->slave->order_by('courier.display_name', 'asc');
        $this->slave->join('order_shipping', 'shipment_tracking.shipment_id = tbl_order_shipping.id');
        $this->slave->join('courier', 'courier.id = order_shipping.courier_id');

        $q = $this->slave->get('shipment_tracking');
        return $q->result();
    }

    function get_cred_fm_awb_report($user_id = false, $start_date = false, $end_date = false)
    {
        if (!$user_id)
            return false;

        if (!$start_date)
            $start_date = strtotime("yesterday midnight");

        if (!$end_date)
            $end_date = strtotime("midnight") - 1;

        $this->slave->select('shipment_tracking.shipment_id, FROM_UNIXTIME(shipment_tracking.pickup_time, "%Y-%m-%d") AS pickup_time, FROM_UNIXTIME(shipment_tracking.edd_time, "%Y-%m-%d") AS edd_time, order_shipping.awb_number, tbl_order_shipping.ship_status, order_shipping.courier_id, courier.display_name');
        $this->slave->where('shipment_tracking.pickup_time >= ', $start_date);
        $this->slave->where('shipment_tracking.pickup_time <= ', $end_date);
        $this->slave->where('order_shipping.user_id', $user_id);
        $this->slave->order_by('courier.display_name', 'asc');
        $this->slave->join('order_shipping', 'shipment_tracking.shipment_id = tbl_order_shipping.id');
        $this->slave->join('courier', 'courier.id = order_shipping.courier_id');

        $q = $this->slave->get('shipment_tracking');
        return $q->result();
    }

    function courierWisePickupStatus($user_id = false, $start_date = false, $end_date = false, $courier_ids = array(), $warehouse_id = array())
    {
        if (!$user_id)
            return false;

        if (!$start_date)
            $start_date = strtotime("midnight");

        if (!$end_date)
            $end_date = strtotime("tomorrow midnight") - 1;

        $this->slave->select('pickups.warehouse_id, pickups.courier_id, pickups.pickup_done, courier.display_name');
        $this->slave->where('pickups.created >= ', $start_date);
        $this->slave->where('pickups.created <= ', $end_date);
        $this->slave->where('pickups.user_id', $user_id);
        $this->slave->where_in('pickups.warehouse_id', $warehouse_id);
        $this->slave->where_in('pickups.courier_id', $courier_ids);
        $this->slave->group_by(array('pickups.warehouse_id', 'courier.display_name'));
        $this->slave->order_by('courier.display_name', 'asc');
        $this->slave->join('courier', 'courier.id = pickups.courier_id');

        $q = $this->slave->get('pickups');
        return $q->result();
    }
}