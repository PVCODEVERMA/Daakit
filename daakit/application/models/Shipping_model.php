<?php

class Shipping_model extends MY_model
{

    public function __construct()
    {
        parent::__construct();
        $this->table = 'order_shipping';
        $this->table_mps = 'order_mps';
    }

    function insert($save = array())
    {
        if (empty($save))
            return false;

        $save['created'] = time();
        $save['modified'] = time();

        $this->db->insert($this->table, $save);
        return $this->db->insert_id();
    }

    function getByOrderID($order_id = false, $order_type = false)
    {
        if (!$order_id)
            return false;

        $this->db->where('order_id', $order_id);
        $this->db->where('ship_status !=', 'cancelled');

        $this->db->order_by('id', 'desc');
        $this->db->limit(1);
        $q = $this->db->get($this->table);
        return $q->row();
    }

    function update($id = false, $update = false)
    {
        if (!$id || !$update)
            return false;

        $update['modified'] = time();

        $this->db->where('id', $id);
        $this->db->set($update);
        $this->db->update($this->table);
        return true;
    }

    function getByUserID($user_id = false, $limit = 50, $offset = 0, $filter = array())
    {
        if (!$user_id)
            return false;

        $this->db->select("tbl_orders.*, group_concat(tbl_order_products.product_name) as products, sum(tbl_order_products.product_qty) as prod_qty, group_concat(tbl_order_products.product_sku) as products_sku, tbl_order_shipping.awb_number as awb_number, tbl_order_shipping.courier_id as courier_id, tbl_order_shipping.id as shipping_id, tbl_order_shipping.warehouse_id as warehouse_id, tbl_order_shipping.dg_order as dg_order, tbl_order_shipping.ship_status as ship_status, tbl_order_shipping.message as ship_message, tbl_order_shipping.zone as zone, tbl_order_shipping.created as shipping_created, tbl_order_shipping.modified as shipping_modified, courier.name as courier_name, courier.code as courier_code, courier.display_name as courier_display_name, tbl_order_shipping.receipt_amount as receipt_amount, tbl_order_shipping.delivered_time as delivered_time, tbl_order_shipping.charged_weight as charged_weight, tbl_order_shipping.extra_weight_charges as extra_weight_charges, tbl_order_shipping.courier_fees as courier_fees, tbl_order_shipping.cod_fees as cod_fees, tbl_order_shipping.total_fees as total_fees, tbl_order_shipping.fees_refunded as fees_refunded, tbl_order_shipping.rto_extra_weight_charges as rto_extra_weight_charges, tbl_order_shipping.rto_charges as rto_charges,order_shipping.rto_date as rto_date, tbl_order_shipping.cod_reverse_amount as cod_reverse_amount, tbl_order_shipping.remittance_id as remittance_id, tbl_order_shipping.status_updated_at as status_updated_at, tbl_order_shipping.rto_status as rto_status, tbl_order_shipping.rto_awb as rto_awb, tbl_order_shipping.applied_tags as shipment_applied_tags, tbl_order_shipping.edd_time as edd_time,order_shipping.insurance_price, tbl_order_shipping.updated_charges, tbl_order_shipping.calculated_weight");

        if (!empty($filter['order_type'])) {
            $this->db->where_in('orders.order_type', $filter['order_type']);
        }

        if (!empty($filter['order_ids'])) {
            $this->db->where_in('orders.order_no', $filter['order_ids']);
        }

        if (!empty($filter['shipment_ids'])) {
            $this->db->where_in('tbl_order_shipping.id', $filter['shipment_ids']);
        }

        if (!empty($filter['tags'])) {
            $filter['tags'] = trim(str_replace("'", "\'", $filter['tags']));
            $this->db->where(" (find_in_set('{$filter['tags']}', tbl_order_shipping.applied_tags))");
        }

        if (!empty($filter['search_query'])) {
            $query = trim(str_replace("'", "\'", $filter['search_query']));
            $this->db->where(" (CONCAT(orders.shipping_fname, ' ', orders.shipping_lname) like '%{$query}%' or orders.shipping_phone like '%{$query}%' or orders.shipping_address like '%{$query}%' or orders.shipping_address_2 like '%{$query}%' or orders.shipping_city  like '%{$query}%' or orders.shipping_state like '%{$query}%' or order_products.product_name like '%{$query}%' or orders.order_tags like '%{$query}%'  ) ");
        }

        if (!empty($filter['pay_method'])) {
            $this->db->where('orders.order_payment_type', $filter['pay_method']);
        }

        if (!empty($filter['start_date'])) {
            $this->db->where("order_shipping.created >= '" . $filter['start_date'] . "'");
        }

        if (!empty($filter['end_date'])) {
            $this->db->where("order_shipping.created <= '" . $filter['end_date'] . "'");
        }

        if (!empty($filter['courier_id'])) {
            $this->db->where('order_shipping.courier_id', $filter['courier_id']);
        }

        if (!empty($filter['awb_no'])) {
            $this->db->where_in('order_shipping.awb_number', $filter['awb_no']);
        }

        if (!empty($filter['ship_status'])) {
            $this->db->where('order_shipping.ship_status', $filter['ship_status']);
        }

        if (!empty($filter['rto_status'])) {
            $this->db->where('order_shipping.rto_status', $filter['rto_status']);
        }

        if (!empty($filter['ship_status_in'])) {
            $this->db->where_in('order_shipping.ship_status', $filter['ship_status_in']);
        }

        if (!empty($filter['ship_status_not_in'])) {
            $this->db->where_not_in('order_shipping.ship_status', $filter['ship_status_not_in']);
        }

        if (!empty($filter['stuck'])) {
            $this->db->where('order_shipping.status_updated_at <', strtotime('-3 days midnight'));
            $this->db->where('order_shipping.status_updated_at !=', '');
        }

        if (!empty($filter['channel_id'])) {
            if ($filter['channel_id'] == 'custom')
                $this->db->where(" (orders.channel_id is NULL or orders.channel_id not in (select id from user_channels where user_id = '{$user_id}')) ");
            else
                $this->db->where('orders.channel_id', $filter['channel_id']);
        }

        if (!empty($filter['state_in'])) {
            $wh = array();
            foreach ($filter['state_in'] as $s_in) {
                $wh[] = " orders.shipping_zip like '{$s_in}%'";
            }

            $this->db->where(" ( " . implode(' OR ', $wh) . ")");
        }

        if (!empty($filter['warehouse_id'])) {
            $this->db->where('order_shipping.warehouse_id', $filter['warehouse_id']);
        }

        if (!empty($filter['product_name'])) {
            $this->db->like('tbl_order_products.product_name', $filter['product_name']);
        }

        $this->db->limit($limit);
        $this->db->offset($offset);

        $this->db->where('order_shipping.user_id', $user_id);

        $this->db->group_by('tbl_order_products.order_id, tbl_order_shipping.id');

        $this->db->join('orders', 'orders.id = order_shipping.order_id');
        $this->db->join('order_products', 'tbl_order_products.order_id = orders.id');
        $this->db->join('courier', 'courier.id = order_shipping.courier_id');
        //$this->db->join('user_channels', 'user_channels.id = orders.channel_id');

        $this->db->order_by('tbl_order_shipping.created', 'desc');

        $q = $this->db->get($this->table);

        if ($q->result())
            return $q->result();
        else
            return false;
    }

    function countByUserID($user_id = false, $filter = array())
    {
        if (!$user_id)
            return false;

        $this->db->select('count(DISTINCT tbl_order_shipping.id) as total');

        if (!empty($filter['order_type'])) {
            $this->db->where_in('tbl_orders.order_type', $filter['order_type']);
        }

        if (!empty($filter['order_ids'])) {
            $this->db->where_in('tbl_orders.order_no', $filter['order_ids']);
        }

        if (!empty($filter['search_query'])) {
            $query = trim(str_replace("'", "\'", $filter['search_query']));
            $this->db->where(" (CONCAT(tbl_orders.shipping_fname, ' ', tbl_orders.shipping_lname) like '%{$query}%' or tbl_orders.shipping_phone like '%{$query}%' or tbl_orders.shipping_address like '%{$query}%' or tbl_orders.shipping_address_2 like '%{$query}%' or orders.shipping_city  like '%{$query}%' or tbl_orders.shipping_state like '%{$query}%' or tbl_order_products.product_name like '%{$query}%' or tbl_orders.order_tags like '%{$query}%'  ) ");
        }

        if (!empty($filter['tags'])) {
            $filter['tags'] = trim(str_replace("'", "\'", $filter['tags']));
            $this->db->where(" (find_in_set('{$filter['tags']}', tbl_order_shipping.applied_tags))");
        }

        if (!empty($filter['pay_method'])) {
            $this->db->where('tbl_orders.order_payment_type', $filter['pay_method']);
        }

        if (!empty($filter['start_date'])) {
            $this->db->where("tbl_order_shipping.created >= '" . $filter['start_date'] . "'");
        }

        if (!empty($filter['end_date'])) {
            $this->db->where("tbl_order_shipping.created <= '" . $filter['end_date'] . "'");
        }

        if (!empty($filter['courier_id'])) {
            $this->db->where('tbl_order_shipping.courier_id', $filter['courier_id']);
        }

        if (!empty($filter['awb_no'])) {
            $this->db->where_in('tbl_order_shipping.awb_number', $filter['awb_no']);
        }

        if (!empty($filter['ship_status'])) {
            $this->db->where('tbl_order_shipping.ship_status', $filter['ship_status']);
        }

        if (!empty($filter['rto_status'])) {
            $this->db->where('tbl_order_shipping.rto_status', $filter['rto_status']);
        }

        if (!empty($filter['warehouse_id'])) {
            $this->db->where('tbl_order_shipping.warehouse_id', $filter['warehouse_id']);
        }

        if (!empty($filter['product_name'])) {
            $this->db->like('tbl_order_products.product_name', $filter['product_name']);
        }

        if (!empty($filter['ship_status_in'])) {
            $this->db->where_in('tbl_order_shipping.ship_status', $filter['ship_status_in']);
        }

        if (!empty($filter['ship_status_not_in'])) {
            $this->db->where_not_in('tbl_order_shipping.ship_status', $filter['ship_status_not_in']);
        }

        if (!empty($filter['stuck'])) {
            $this->db->where('tbl_order_shipping.status_updated_at <', strtotime('-3 days midnight'));
            $this->db->where('tbl_order_shipping.status_updated_at !=', '');
        }

        if (!empty($filter['channel_id'])) {
            if ($filter['channel_id'] == 'custom')
                $this->db->where(" (tbl_orders.channel_id is NULL or tbl_orders.channel_id not in (select id from tbl_user_channels where user_id = '{$user_id}')) ");
            else
                $this->db->where('tbl_orders.channel_id', $filter['channel_id']);
        }

        if (!empty($filter['state_in'])) {
            $wh = array();
            foreach ($filter['state_in'] as $s_in) {
                $wh[] = " tbl_orders.shipping_zip like '{$s_in}%'";
            }

            $this->db->where(" ( " . implode(' OR ', $wh) . ")");
        }

        $this->db->where('tbl_order_shipping.user_id', $user_id);

        //$this->db->group_by('tbl_order_products.order_id');

        $this->db->join('orders', 'orders.id = order_shipping.order_id');
        $this->db->join('order_products', 'tbl_order_products.order_id = orders.id');
        $this->db->join('courier', 'courier.id = order_shipping.courier_id');
        //$this->db->join('user_channels', 'user_channels.id = orders.channel_id');

        $this->db->order_by('order_shipping.created', 'desc');

        $q = $this->db->get($this->table);

        return $q->row()->total;
    }

    function countByUserIDStatusGrouped($user_id = false, $filter = array())
    {
        if (!$user_id)
            return false;

        $this->db->select("tbl_order_shipping.ship_status as ship_status, count(DISTINCT tbl_order_shipping.id) as total_count");

        if (!empty($filter['order_type'])) {
            $this->db->where_in('orders.order_type', $filter['order_type']);
        }

        if (!empty($filter['order_ids'])) {
            $this->db->where_in('orders.order_no', $filter['order_ids']);
        }

        if (!empty($filter['tags'])) {
            $filter['tags'] = trim(str_replace("'", "\'", $filter['tags']));
            $this->db->where(" (find_in_set('{$filter['tags']}', tbl_order_shipping.applied_tags))");
        }

        if (!empty($filter['search_query'])) {
            $query = trim(str_replace("'", "\'", $filter['search_query']));
            $this->db->where(" (orders.customer_name like '%{$query}%' or orders.shipping_phone like '%{$query}%' or orders.shipping_address like '%{$query}%' or orders.shipping_address_2 like '%{$query}%' or orders.shipping_city  like '%{$query}%' or orders.shipping_state like '%{$query}%' or order_products.product_name like '%{$query}%' or orders.order_tags like '%{$query}%'  ) ");
        }

        if (!empty($filter['pay_method'])) {
            $this->db->where('orders.order_payment_type', $filter['pay_method']);
        }

        if (!empty($filter['start_date'])) {
            $this->db->where("order_shipping.created >= '" . $filter['start_date'] . "'");
        }

        if (!empty($filter['end_date'])) {
            $this->db->where("order_shipping.created <= '" . $filter['end_date'] . "'");
        }

        if (!empty($filter['courier_id'])) {
            $this->db->where('order_shipping.courier_id', $filter['courier_id']);
        }

        if (!empty($filter['awb_no'])) {
            $this->db->where_in('order_shipping.awb_number', $filter['awb_no']);
        }

        if (!empty($filter['warehouse_id'])) {
            $this->db->where('order_shipping.warehouse_id', $filter['warehouse_id']);
        }

        if (!empty($filter['product_name'])) {
            $this->db->like('tbl_order_products.product_name', $filter['product_name']);
        }

        if (!empty($filter['ship_status_in'])) {
            $this->db->where_in('order_shipping.ship_status', $filter['ship_status_in']);
        }

        if (!empty($filter['ship_status_not_in'])) {
            $this->db->where_not_in('order_shipping.ship_status', $filter['ship_status_not_in']);
        }

        if (!empty($filter['stuck'])) {
            $this->db->where('order_shipping.status_updated_at <', strtotime('-3 days midnight'));
            $this->db->where('order_shipping.status_updated_at !=', '');
        }

        if (!empty($filter['channel_id'])) {
            if ($filter['channel_id'] == 'custom')
                $this->db->where(" (orders.channel_id is NULL or orders.channel_id not in (select id from user_channels where user_id = '{$user_id}')) ");
            else
                $this->db->where('orders.channel_id', $filter['channel_id']);
        }

        if (!empty($filter['state_in'])) {
            $wh = array();
            foreach ($filter['state_in'] as $s_in) {
                $wh[] = " orders.shipping_zip like '{$s_in}%'";
            }

            $this->db->where(" ( " . implode(' OR ', $wh) . ")");
        }

        $this->db->where('order_shipping.user_id', $user_id);
        $this->db->group_by('order_shipping.ship_status');

        $this->db->join('orders', 'orders.id = order_shipping.order_id', 'LEFT');
        $this->db->join('order_products', 'tbl_order_products.order_id = orders.id');
        //$this->db->join('user_channels', 'user_channels.id = orders.channel_id');

        $this->db->order_by('order_shipping.created', 'desc');
        $q = $this->db->get($this->table);

        return $q->result();
    }


    function exportByUserID($user_id = false, $limit = 50, $offset = 0, $filter = array())
    {
        if (!$user_id)
            return false;

        $this->db->select("orders.*,
        order_products.product_name,
        order_products.product_qty,
        order_products.product_price,
        order_products.product_sku,
        order_shipping.created as shipping_created,
        order_shipping.id as shipment_id,
        order_shipping.courier_id,
        order_shipping.awb_number,
        order_shipping.rto_awb,
        order_shipping.ship_status,
        order_shipping.status_updated_at,
        order_shipping.delivered_time,
        order_shipping.remittance_id,
        order_shipping.rto_status,
        order_shipping.zone as shipping_zone,
        order_shipping.courier_fees,
        order_shipping.cod_fees,
        order_shipping.total_fees,
        order_shipping.rto_charges,
        order_shipping.edd_time,
        courier.name as courier_name,
        warehouse.name as warehouse_name,
        warehouse.city as warehouse_city,
        warehouse.state as warehouse_state,
        warehouse.zip as warehouse_pincode,
        remittance.payment_date as remittance_date,
        shipment_tracking.last_ndr_reason,
        shipment_tracking.total_ofd_attempts,
        shipment_tracking.delivery_attempt_count,
        shipment_tracking.ofd_attempt_1_date as first_delivery_attempt_date,
        shipment_tracking.last_attempt_date,
        shipment_tracking.pickup_time,
        shipment_tracking.rto_mark_date,
        shipment_tracking.rto_delivered_date,
        essential_order,
        order_shipping.is_insurance,
        order_shipping.insurance_price");

        if (!empty($filter['order_type'])) {
            $this->db->where_in('orders.order_type', $filter['order_type']);
        }

        if (!empty($filter['order_ids'])) {
            $this->db->where_in('orders.order_no', $filter['order_ids']);
        }

        if (!empty($filter['shipment_ids'])) {
            $this->db->where_in('order_shipping.id', $filter['shipment_ids']);
        }

        if (!empty($filter['tags'])) {
            $filter['tags'] = trim(str_replace("'", "\'", $filter['tags']));
            $this->db->where(" (find_in_set('{$filter['tags']}', tbl_order_shipping.applied_tags))");
        }

        if (!empty($filter['search_query'])) {
            $query = trim(str_replace("'", "\'", $filter['search_query']));
            $this->db->where(" (CONCAT(orders.shipping_fname, ' ', orders.shipping_lname) like '%{$query}%' or orders.shipping_phone like '%{$query}%' or orders.shipping_address like '%{$query}%' or orders.shipping_address_2 like '%{$query}%' or orders.shipping_city  like '%{$query}%' or orders.shipping_state like '%{$query}%' or order_products.product_name like '%{$query}%' or orders.order_tags like '%{$query}%'  ) ");
        }

        if (!empty($filter['pay_method'])) {
            $this->db->where('orders.order_payment_type', $filter['pay_method']);
        }

        if (!empty($filter['start_date'])) {
            $this->db->where("order_shipping.created >= '" . $filter['start_date'] . "'");
        }

        if (!empty($filter['end_date'])) {
            $this->db->where("order_shipping.created <= '" . $filter['end_date'] . "'");
        }

        if (!empty($filter['courier_id'])) {
            $this->db->where('order_shipping.courier_id', $filter['courier_id']);
        }

        if (!empty($filter['awb_no'])) {
            $this->db->where_in('order_shipping.awb_number', $filter['awb_no']);
        }

        if (!empty($filter['ship_status'])) {
            $this->db->where('order_shipping.ship_status', $filter['ship_status']);
        }

        if (!empty($filter['rto_status'])) {
            $this->db->where('order_shipping.rto_status', $filter['rto_status']);
        }

        if (!empty($filter['ship_status_in'])) {
            $this->db->where_in('order_shipping.ship_status', $filter['ship_status_in']);
        }

        if (!empty($filter['ship_status_not_in'])) {
            $this->db->where_not_in('order_shipping.ship_status', $filter['ship_status_not_in']);
        }

        if (!empty($filter['stuck'])) {
            $this->db->where('order_shipping.status_updated_at <', strtotime('-3 days midnight'));
            $this->db->where('order_shipping.status_updated_at !=', '');
        }

        if (!empty($filter['channel_id'])) {
            if ($filter['channel_id'] == 'custom')
                $this->db->where(" (orders.channel_id is NULL or orders.channel_id not in (select id from user_channels where user_id = '{$user_id}')) ");
            else
                $this->db->where('orders.channel_id', $filter['channel_id']);
        }

        if (!empty($filter['state_in'])) {
            $wh = array();
            foreach ($filter['state_in'] as $s_in) {
                $wh[] = " orders.shipping_zip like '{$s_in}%'";
            }

            $this->db->where(" ( " . implode(' OR ', $wh) . ")");
        }

        if (!empty($filter['warehouse_id'])) {
            $this->db->where('order_shipping.warehouse_id', $filter['warehouse_id']);
        }

        if (!empty($filter['product_name'])) {
            $this->db->like('tbl_order_products.product_name', $filter['product_name']);
        }

        $this->db->limit($limit);
        $this->db->offset($offset);

        $this->db->where('orders.user_id', $user_id);
        $this->db->group_by('tbl_order_products.order_id, tbl_order_shipping.id');
        $this->db->join('orders', 'orders.id = order_shipping.order_id');
        $this->db->join('order_products', 'tbl_order_products.order_id = orders.id', 'LEFT');
        $this->db->join('warehouse', 'warehouse.id = order_shipping.warehouse_id', 'LEFT');
        $this->db->join('courier', 'courier.id = order_shipping.courier_id', 'LEFT');
        $this->db->join('shipment_tracking', 'order_shipping.id = shipment_tracking.shipment_id', 'LEFT');
        $this->db->join('remittance', 'remittance.id = order_shipping.remittance_id', 'LEFT');

        $this->db->order_by('order_shipping.created', 'desc');

        $this->db->from($this->table);
       // echo $this->db->last_query();exit;
        return $query = $this->db->get_compiled_select();
    }

    function markPickupRequested($ship_ids = array())
    {
        if (empty($ship_ids))
            return false;

        $this->db->where_in('id', $ship_ids);
        $this->db->where('ship_status', 'booked');
        $this->db->set('ship_status', 'pending pickup');
        $this->db->set('pending_pickup_date', time());
        $this->db->update($this->table);
        return true;
    }

    function getByAWB($awb = false, $user_id = false)
    {
        if (!$awb)
            return false;

        $this->db->where('awb_number', (string) $awb);
        if ($user_id)
            $this->db->where('user_id', (int) $user_id);

        $this->db->limit(1);
        $q = $this->db->get($this->table);
        return $q->row();
    }

    function getByRtoAWB($awb = false, $user_id = false)
    {
        if (!$awb)
            return false;

        $this->db->where('rto_awb', (string) $awb);
        if ($user_id)
            $this->db->where('user_id', (int) $user_id);

        $this->db->limit(1);
        $q = $this->db->get($this->table);
        return $q->row();
    }

    function getByAWBsMultiple($awbs = false)
    {
        if (!$awbs)
            return false;

        $this->db->where_in('awb_number', $awbs);
        $q = $this->db->get($this->table);
        return $q->result();
    }

    function getByAWBsMultipleWithUserID($user_id = false, $awbs = false)
    {
        if (!$user_id || !$awbs)
            return false;

        $this->db->where('user_id', (int) $user_id);
        $this->db->where_in('awb_number', $awbs);
        $q = $this->db->get($this->table);
        return $q->result();
    }

    function getByIDBulk($ids = false)
    {
        if (!$ids)
            return false;

        $this->db->where_in('id', $ids);
        $q = $this->db->get($this->table);
        return $q->result();
    }

    function tracableOrders($filter = array())
    {
        $this->slave = $this->load->database('slave', TRUE);

        $this->slave->select('id, courier_id');
        $this->slave->where('created >', strtotime('-6 months'));
        if (!empty($filter['status_in'])) {
            $this->slave->where_in('ship_status', $filter['status_in']);
        }

        if (!empty($filter['status_not_in'])) {
            $this->slave->where_not_in('ship_status', $filter['status_not_in']);
        }

        if (!empty($filter['rto_status_in'])) {
            $this->slave->where_in('rto_status', $filter['rto_status_in']);
        }

        if (!empty($filter['courier_in'])) {
            $this->slave->where_in('courier_id', $filter['courier_in']);
        }

        if (!empty($filter['before_last_tracking'])) {
            $this->slave->where('last_tracking_time <', $filter['before_last_tracking']);
        }

        $this->slave->order_by('created', 'desc');
        $q = $this->slave->get($this->table);
        return $q->result();
    }

    function dueFromCourier($user_id = false)
    {
        if (!$user_id)
            return false;

        $this->db->select("count(*) as total, sum(tbl_orders.order_amount) as total_due");

        $this->db->where('order_shipping.user_id', $user_id);
        $this->db->where('order_shipping.ship_status', 'delivered');
        $this->db->where("order_shipping.receipt_id", '0');
        $this->db->where("order_shipping.remittance_id", '0');
        $this->db->where('orders.order_payment_type', 'COD');

        $this->db->join('orders', 'orders.id = order_shipping.order_id', 'LEFT');

        $q = $this->db->get('order_shipping');
        return $q->row();
    }

    function nextRemittance($user_id = false)
    {
        if (!$user_id)
            return false;

        $this->db->select("sum(tbl_orders.order_amount) as total_due");

        $this->db->where('order_shipping.user_id', $user_id);
        $this->db->where("order_shipping.remittance_id", '0');
        $this->db->where('orders.order_payment_type', 'COD');

        $this->db->where('order_shipping.ship_status', 'delivered');
        $this->db->where('order_shipping.delivered_time <= UNIX_TIMESTAMP() - 24*60*60*(tbl_users.remittance_cycle)');

        $this->db->join('orders', 'orders.id = order_shipping.order_id', 'LEFT');
        $this->db->join('users', 'users.id = order_shipping.user_id', 'LEFT');

        $q = $this->db->get('order_shipping');
        return $q->row();
    }

    function totalRemittanceDue($user_id = false)
    {
        if (!$user_id)
            return false;

        $this->db->select("sum(tbl_orders.order_amount) as total_due");

        $this->db->where('order_shipping.user_id', $user_id);
        $this->db->where("order_shipping.remittance_id", '0');
        $this->db->where('orders.order_payment_type', 'COD');

        $this->db->where('order_shipping.ship_status', 'delivered');

        $this->db->join('orders', 'orders.id = order_shipping.order_id', 'LEFT');

        $q = $this->db->get('order_shipping');
        return $q->row();
    }

    function shipmentDetailsBulkIds($user_id = false, $limit = 50, $offset = 0, $filter = array())
    {
        if (!$user_id)
            return false;

        $this->db->select("order_shipping.id as shipping_id, "
            . "order_shipping.created as shipping_created, "
            . "orders.order_no as order_id, "
            . "courier.name as courier_name, "
            . "order_shipping.awb_number as awb_number, tbl_order_shipping.receipt_amount as receipt_amount, tbl_order_shipping.delivered_time as delivered_time,  tbl_orders.order_amount as order_amount ");
        if (!empty($filter['shipment_ids'])) {
            $this->db->where_in('order_shipping.id', $filter['shipment_ids']);
        }

        $this->db->limit($limit);
        $this->db->offset($offset);

        $this->db->where('orders.user_id', $user_id);
        $this->db->where('order_shipping.ship_status !=', 'cancelled');

        $this->db->join('orders', 'orders.id = order_shipping.order_id');
        $this->db->join('courier', 'courier.id = order_shipping.courier_id');

        $this->db->order_by('order_shipping.created', 'desc');

        $q = $this->db->get($this->table);

        return $q->result();
    }

    function fetchAPIShipments($filter = array())
    {
        $this->db->select("order_shipping.*, orders.order_no as order_number,shipment_tracking.picked_date as ship_pickup_time,shipment_tracking.pickup_time as ship_pickup_time_alt,shipment_tracking.edd_time as ship_edd_time,shipment_tracking.delivered_time as ship_delivered_time,shipment_tracking.rto_mark_date as ship_rto_mark_date,shipment_tracking.shipped_date as ship_shipped_date, shipment_tracking.last_ndr_reason as last_ndr_reason ");

        if (!empty($filter['id'])) {
            $this->db->where('order_shipping.id', $filter['id']);
        }

        if (!empty($filter['awb_number'])) {
            $this->db->where('order_shipping.awb_number', $filter['awb_number']);
        }

        if (!empty($filter['start_date'])) {
            $this->db->where("order_shipping.created >= '" . $filter['start_date'] . "'");
        }

        if (!empty($filter['end_date'])) {
            $this->db->where("order_shipping.created <= '" . $filter['end_date'] . "'");
        }

        if (!empty($filter['limit'])) {
            $this->db->limit($filter['limit']);
        }

        if (!empty($filter['offset'])) {
            $this->db->offset($filter['offset']);
        }

        if (!empty($filter['user_id'])) {
            $this->db->where('order_shipping.user_id', $filter['user_id']);
        }

        if (!empty($filter['order_by'])) {
            $this->db->order_by($filter['order_by'], (!empty($filter['order_dir']) ? $filter['order_dir'] : 'DESC'));
        }

        $this->db->join('orders', 'orders.id = order_shipping.order_id');
        $this->db->join('shipment_tracking', 'shipment_tracking.shipment_id = order_shipping.id','LEFT');

        $q = $this->db->get($this->table);
       // echo $this->db->last_query(); die;
        return $q->result();
    }

    function countAPIShipments($filter = array())
    {
        $this->db->select("count(DISTINCT order_shipping.id) as total");

        if (!empty($filter['id'])) {
            $this->db->where('order_shipping.id', $filter['id']);
        }

        if (!empty($filter['start_date'])) {
            $this->db->where("order_shipping.created >= '" . $filter['start_date'] . "'");
        }

        if (!empty($filter['end_date'])) {
            $this->db->where("order_shipping.created <= '" . $filter['end_date'] . "'");
        }

        if (!empty($filter['user_id'])) {
            $this->db->where('order_shipping.user_id', $filter['user_id']);
        }

        $this->db->join('orders', 'orders.id = order_shipping.order_id');

        $q = $this->db->get($this->table);

        return $q->row()->total;
    }

    function fetchUnshipped($user_id = false, $filter = array())
    {
        if (!$user_id)
            return false;

        $this->db->select('order_shipping.id as id');

        if (!empty($filter['start_date'])) {
            $this->db->where("order_shipping.created >= '" . $filter['start_date'] . "'");
        }

        if (!empty($filter['end_date'])) {
            $this->db->where("order_shipping.created <= '" . $filter['end_date'] . "'");
        }

        $this->db->where_in('order_shipping.ship_status', array('new', 'booked', 'pending pickup'));

        $this->db->where('order_shipping.user_id', $user_id);

        $this->db->join('orders', 'orders.id = order_shipping.order_id');

        $this->db->order_by('order_shipping.created', 'asc');

        $q = $this->db->get($this->table);

        return $q->result();
    }

    function fetchRTOShipmentsforInvoice($user_id = false, $filter = array())
    {
        if (!$user_id)
            return false;

        $this->db->select("orders.*, group_concat(order_products.product_name)  as products, tbl_order_shipping.awb_number as awb_number, tbl_order_shipping.id as shipping_id, tbl_order_shipping.ship_status as ship_status, tbl_order_shipping.message as ship_message, tbl_order_shipping.created as shipping_created, courier.name as courier_name, courier.code as courier_code, tbl_order_shipping.receipt_amount as receipt_amount, tbl_order_shipping.delivered_time as delivered_time, tbl_order_shipping.charged_weight as charged_weight, tbl_order_shipping.extra_weight_charges as extra_weight_charges, tbl_order_shipping.courier_fees as courier_fees, tbl_order_shipping.cod_fees as cod_fees, tbl_order_shipping.total_fees as total_fees, tbl_order_shipping.fees_refunded as fees_refunded, tbl_order_shipping.rto_extra_weight_charges as rto_extra_weight_charges , tbl_order_shipping.rto_charges as rto_charges,order_shipping.rto_date as rto_date, tbl_order_shipping.cod_reverse_amount as cod_reverse_amount");

        if (!empty($filter['start_date'])) {
            $this->db->where("order_shipping.rto_date >= '" . $filter['start_date'] . "'");
        }

        if (!empty($filter['end_date'])) {
            $this->db->where("order_shipping.rto_date <= '" . $filter['end_date'] . "'");
        }

        //shipment is older than date
        $this->db->where("order_shipping.created <= '" . $filter['start_date'] . "'");

        $this->db->where('order_shipping.rto_charges >', '0');
        $this->db->where('order_shipping.ship_status', 'rto');

        $this->db->where('order_shipping.user_id', $user_id);

        $this->db->group_by('tbl_order_products.order_id, tbl_order_shipping.id');

        $this->db->join('orders', 'orders.id = order_shipping.order_id');
        $this->db->join('order_products', 'tbl_order_products.order_id = orders.id');
        $this->db->join('courier', 'courier.id = order_shipping.courier_id');
        //$this->db->join('user_channels', 'user_channels.id = orders.channel_id');

        $this->db->order_by('order_shipping.created', 'desc');

        $q = $this->db->get($this->table);

        return $q->result();
    }

    function getShipmentByID($shipment_id = false)
    {
        if (!$shipment_id)
            return false;

        $this->db->select('order_shipping.id as shipping_id, tbl_order_shipping.created as shipment_created, orders.order_no as order_number, tbl_order_shipping.*, orders.*, courier.*, users.*');
        $this->db->where('order_shipping.id', $shipment_id);

        $this->db->limit(1);
        $this->db->join('orders', 'orders.id = order_shipping.order_id');
        $this->db->join('order_products', 'tbl_order_products.order_id = orders.id');
        $this->db->join('courier', 'courier.id = order_shipping.courier_id');
        $this->db->join('users', 'users.id = orders.user_id');

        $q = $this->db->get($this->table);

        return $q->row();
    }

    function getShipmentByOrderID($order_id = false)
    {
        if (!$order_id)
            return false;

        $this->db->select('order_shipping.id as shipping_id, tbl_order_shipping.created as shipment_created, orders.order_no as order_number, tbl_order_shipping.label, tbl_order_shipping.*, orders.*, courier.*, users.*');
        $this->db->where('order_shipping.order_id', $order_id);
        $this->db->where('order_shipping.ship_status !=', 'cancelled');
        $this->db->join('orders', 'orders.id = order_shipping.order_id');
        $this->db->join('order_products', 'tbl_order_products.order_id = orders.id');
        $this->db->join('courier', 'courier.id = order_shipping.courier_id');
        $this->db->join('users', 'users.id = orders.user_id');
        $this->db->limit(1);
        $q = $this->db->get($this->table);

        return $q->row();
    }


    function get_data_code($code = array())
    {
        if (empty($code))
            return false;

            $this->db->select("id,weight_locked,weight,length,breadth,height");
            $this->db->where('product_details_code', $code);
            //$this->db->where('weight_locked', '2');
            $q = $this->db->get('product_details');
            return $q->row();
    }


    function getByOrderNoMobile($order_no = false, $mobile = false, $user_id = false)
    {
        if (!$order_no || !$mobile)
            return false;

        $this->db->select("orders.order_no as order_id, tbl_order_shipping.awb_number as awb_number, tbl_order_shipping.ship_status as ship_status, group_concat(order_products.product_name) as products");

        if ($user_id)
            $this->db->where('orders.user_id', $user_id);

        $this->db->where('order_shipping.ship_status !=', 'cancelled');
        $this->db->where("orders.id='$order_no' OR orders.order_no='$order_no'");
        $this->db->where('orders.shipping_phone', $mobile);

        $this->db->group_by('orders.id');
        $this->db->order_by('orders.id', 'desc');

        $this->db->join('order_products', 'tbl_order_products.order_id = orders.id');
        $this->db->join('order_shipping', 'order_shipping.order_id = orders.id');
        
        $q = $this->db->get('orders');

        return $q->result();
    }

    function getByOrderNo($order_no = false, $user_id = false)
    {
        if (!$order_no )
            return false;

        $this->db->select("orders.order_no as order_id ,orders.order_payment_type as order_payment_type ,tbl_orders.order_amount as order_amount, tbl_order_shipping.awb_number as awb_number, tbl_order_shipping.ship_status as ship_status, group_concat(order_products.product_name) as products");

        if ($user_id)
            $this->db->where('orders.user_id', $user_id);

        $this->db->where('order_shipping.ship_status !=', 'cancelled');
        $this->db->where("(orders.id='$order_no' OR orders.order_no='$order_no')");
        //$this->db->where('orders.shipping_phone', $mobile);

        $this->db->group_by('orders.id');
        $this->db->order_by('orders.id', 'desc');

        $this->db->join('order_products', 'tbl_order_products.order_id = orders.id');
        $this->db->join('order_shipping', 'order_shipping.order_id = orders.id');
        
        $q = $this->db->get('orders');
        //echo $this->db->last_query(); die;
        return $q->result();
    }

    function getAllTags($user_id = false)
    {
        if (!$user_id)
            return false;

        $this->db->select("group_concat(order_shipping.applied_tags) as applied_tags");

        $this->db->where('order_shipping.user_id', $user_id);
        $this->db->where('order_shipping.applied_tags !=', '');

        $this->db->where('order_shipping.created >', strtotime('-1 month'));

        $this->db->order_by('order_shipping.id', 'desc');

        $this->db->join('orders', 'orders.id = order_shipping.order_id');
        return $this->db->get($this->table)->row();
    }

    function getProcessingShipmentsByID($shipment_id = false)
    {
        if (!$shipment_id)
            return false;

        // Select columns from orders and shipment tables
        $this->db->select('o.*, s.id as shipment_id, s.courier_id, s.warehouse_id, s.message, s.allocation_skip_courier, s.ship_status, s.awb_number, s.courier_slab, s.dg_order');
        // Where clause for shipment_id
        $this->db->where('s.id', $shipment_id);

        // Join clauses with prefixed table names
        $this->db->join('orders as o', 'order_id=o.id ', 'left');
        $this->db->join('allocation_rules as r', 'r.user_id = o.user_id', 'left');

        // Get query result from prefixed shipment table
        $q = $this->db->get('order_shipping as s');
        // Return the first row of the result
        return $q->row();
    }


    // function getProcessingShipmentsByID($shipment_id = false)
    // {
    //     if (!$shipment_id)
    //         return false;

    //     $this->db->select('o.*, s.id as shipment_id, s.courier_id, s.warehouse_id, s.message, s.allocation_skip_courier, s.ship_status, s.awb_number, s.courier_slab, s.dg_order');

    //     $this->db->where('s.id', $shipment_id);

    //     $this->db->join('orders as o', 'o.id = s.order_id');
    //     $this->db->join('allocation_rules as r', 'r.user_id = s.user_id', 'left');

    //     $q = $this->db->get($this->table . ' as s');
    //     return $q->row();
    // }

    function checkIfWarehouseShipmentExists($user_id = false, $warehouse_id = false)
    {
        if (!$user_id || !$warehouse_id)
            return false;

        $this->db->select('id');

        $this->db->where('s.user_id', $user_id);
        $this->db->where('s.warehouse_id', $warehouse_id);
        $this->db->where('s.ship_status !=', 'cancelled');
        $this->db->limit('1');

        $q = $this->db->get($this->table . ' as s');
        $shipment = $q->row();
        return !empty($shipment) ? TRUE : FALSE;
    }

    function getUsersForAutoPickups()
    {
        $this->slave = $this->load->database('slave', TRUE);

        $this->slave->select('user_id');

        $this->slave->where('created >', strtotime('- 7 days midnight'));
        $this->slave->where('created <', strtotime('today midnight'));

        $this->slave->where('ship_status', 'pending pickup');

        $this->slave->group_by('user_id');

        $q = $this->slave->get($this->table);

        return $q->result();
    }

    function getUserShipmentsForAutoPickups($user_id = false)
    {
        if (!$user_id)
            return false;

        $this->db->select('id');
        $this->db->where('created >', strtotime('- 7 days midnight'));
        $this->db->where('created <', strtotime('today midnight'));

        $this->db->where('user_id', $user_id);

        $this->db->where('ship_status', 'pending pickup');

        $q = $this->db->get($this->table);

        return $q->result();
    }

    function countShipment($user_id = false)
    {
        if (!$user_id)
            return false;

        $this->db->select('count(*) as total');
        $this->db->where('user_id', $user_id);
        $where_in = array('cancelled');
        $this->db->where_not_in('ship_status', $where_in);

        $q = $this->db->get($this->table);
        return $q->row();
    }

    function totalselectedorders($shipingid = false)
    {
        if (!$shipingid)
            return false;

        $this->db->select("orders.*, group_concat(order_products.product_name) as products, sum(order_products.product_qty) as prod_qty, group_concat(order_products.product_sku)  as products_sku,order_shipping.awb_number as awb_number, tbl_order_shipping.id as shipping_id, tbl_order_shipping.ship_status as ship_status, tbl_order_shipping.message as ship_message, tbl_order_shipping.created as shipping_created, courier.name as courier_name, courier.code as courier_code, tbl_order_shipping.receipt_amount as receipt_amount, tbl_order_shipping.delivered_time as delivered_time, tbl_order_shipping.charged_weight as charged_weight, tbl_order_shipping.extra_weight_charges as extra_weight_charges, tbl_order_shipping.courier_fees as courier_fees, tbl_order_shipping.cod_fees as cod_fees, tbl_order_shipping.total_fees as total_fees, tbl_order_shipping.fees_refunded as fees_refunded, tbl_order_shipping.rto_extra_weight_charges as rto_extra_weight_charges, tbl_order_shipping.rto_charges as rto_charges,order_shipping.rto_date as rto_date, tbl_order_shipping.cod_reverse_amount as cod_reverse_amount, tbl_order_shipping.remittance_id as remittance_id, tbl_order_shipping.status_updated_at as status_updated_at, tbl_order_shipping.rto_status as rto_status, tbl_order_shipping.rto_awb as rto_awb, tbl_order_shipping.applied_tags as shipment_applied_tags, tbl_order_shipping.edd_time as edd_time");


        $this->db->where_in('order_shipping.id', $shipingid);
        $this->db->where_not_in('order_shipping.ship_status', ['new', 'cancelled']);

        $this->db->group_by('tbl_order_products.order_id, tbl_order_shipping.id');

        $this->db->join('orders', 'orders.id = order_shipping.order_id');
        $this->db->join('order_products', 'tbl_order_products.order_id = orders.id');
        $this->db->join('courier', 'courier.id = order_shipping.courier_id');
        //$this->db->join('user_channels', 'user_channels.id = orders.channel_id');

        $this->db->order_by('order_shipping.created', 'desc');

        $q = $this->db->get($this->table);

        return $q->result();
    }

    function getByShipingid($shipment_ids = false)
    {
        if (!$shipment_ids)
            return false;

        $shipment = $this->db->select('order_id')
            ->from('order_shipping')
            ->where_in('order_shipping.id', $shipment_ids)
            ->where_not_in('order_shipping.ship_status', ['new', 'cancelled'])
            ->get()
            ->result();

        $order_ids =  array_column($shipment, 'order_id');
        if (!$order_ids)
            return false;

        $product = $this->db->select('product_sku')
            ->from('order_products')
            ->where_in('order_id', $order_ids)
            ->get()
            ->result();

        $skuid =  array_column($product, 'product_sku');
        $this->db->select("order_products.*, sum(product_qty) as product_qty");
        $this->db->where_in('order_id', $order_ids);
        $this->db->group_by('product_name');
        foreach ($product as $sku) {
            if (!empty($sku->product_sku) && $sku->product_sku != 'NULL') {
                $this->db->group_by('product_sku', $sku);
            }
        }


        $q = $this->db->get('order_products');

        $result = $q->result();

        return $result;
    }

    function getShipmentIDsByOrderID($order_id = false)
    {
        if (!$order_id)
            return false;

        $this->db->select("group_concat(order_shipping.id) as ids");

        $this->db->where('order_id', $order_id);
        $this->db->where('ship_status !=', 'cancelled');

        $q = $this->db->get($this->table);
        return $q->row();
    }

    function updateCargo($ids = [], $update = [])
    {
        if (!$ids || !$update)
            return false;

        $update['modified'] = time();

        $this->db->where_in('id', $ids);
        $this->db->set($update);
        $this->db->update($this->table);
        return true;
    }

    function insert_mps($save = array())
    {
        if (empty($save))
            return false;

        $save['created'] = time();
        $save['modified'] = time();

        $this->db->insert($this->table_mps, $save);
        return $this->db->insert_id();
    }

    function getShipmentsByParentAWBNo($awb_number = false)
    {
        if (!$awb_number)
            return false;

        $this->db->where('awb_number', $awb_number);
        $this->db->where('ship_status !=', 'cancelled');
        $q = $this->db->get($this->table_mps);
        return $q->result();
    }

    function updateByParentAWBNo($awb_number = false, $update = false)
    {
        if (!$awb_number || !$update)
            return false;

        $update['modified'] = time();

        $this->db->where('awb_number', $awb_number);
        $this->db->set($update);
        $this->db->update($this->table);
        return true;
    }

    function getByParentAWBNoWithOrderProductId($awb_number = false, $order_id = false)
    {
        if (!$awb_number || !$order_id || !$order_product_id)
            return false;

        $this->db->where('awb_number', $awb_number);
        $this->db->where('order_id', $order_id);
        $q = $this->db->get($this->table);
        return $q->row();
    }

    function updateMPSByParentAWBNo($awb_number, $update)
    {
        if (!$awb_number || !$update)
            return false;

        $this->db->where('order_mps.awb_number', $awb_number);
        $this->db->set($update);
        $this->db->update($this->table_mps);
        return true;
    }

    function saveActualLandingCharges($save = array())
    {
        if (empty($save))
            return false;

        $save['created'] = time();

        $this->db->insert('order_shipping_actual_price', $save);
        return $this->db->insert_id();
    }

    function remove_shopify_orderawb($orderid = '')
    {
        if ($orderid != '') {
            $this->db->select("id");
            $this->db->where('api_order_id', $orderid);
            $q = $this->db->get('orders')->row();
            if ($q->id) {
                $update = array('channel_fulfilled' => '0');
                $this->db->where('order_id', $q->id);
                $this->db->set($update);
                $this->db->update('order_shipping');

                return true;
            }
            return false;
        }
    }

    function fetchOsmAPIShipments($filter = array())
    {
        $this->db->select("order_shipping.*, orders.*, sum(order_products.product_qty) as prod_qty,courier.courier_type, courier.name as courier_name, shipment_tracking.total_ofd_attempts, shipment_tracking.delivery_attempt_count, shipment_tracking.edd_time as delivery_date");

        if (!empty($filter['awb_number'])) {
            $this->db->where('order_shipping.awb_number', $filter['awb_number']);
        }

        if (!empty($filter['user_id'])) {
            $this->db->where('order_shipping.user_id', $filter['user_id']);
        }

        $this->db->group_by('tbl_order_products.order_id, tbl_order_shipping.id');

        $this->db->join('orders', 'orders.id = order_shipping.order_id');
        $this->db->join('courier', 'courier.id = order_shipping.courier_id');
        $this->db->join('order_products', 'tbl_order_products.order_id = orders.id');
        $this->db->join('shipment_tracking', 'shipment_tracking.shipment_id = order_shipping.id', 'LEFT');

        $q = $this->db->get($this->table);
        return $q->result();
    }

    function getUserWiseZoneList($user_id)
    {
        $this->db->select("id,zone,ship_status");
        $this->db->where('user_id', $user_id);
        $this->db->where('zone !=', '');
        $this->db->group_by('zone');
        $query = $this->db->get('order_shipping');
        return $query->result();
    }

    function getUserWiseZoneDetail($user_id, $filter = array())
    {
        $this->db->select(
            "zone,"
                . "sum(case when (order_shipping.ship_status = 'booked') then 1 else 0 end) as unshipped,"
                . "sum(case when (order_shipping.ship_status = 'pending pickup') then 1 else 0 end) as pending,"
                . "sum(case when (order_shipping.ship_status = 'in transit' OR order_shipping.ship_status = 'out for delivery' OR order_shipping.ship_status = 'exception' OR order_shipping.ship_status='lost' OR order_shipping.ship_status ='damaged') then 1 else 0 end) as in_transit,"
                . "sum(case when (order_shipping.ship_status = 'delivered') then 1 else 0 end) as delivered,"
                . "sum(case when (order_shipping.ship_status = 'rto') then 1 else 0 end) as rto,"
                . "sum(case when (order_shipping.ship_status != 'cancelled' AND order_shipping.ship_status != 'new') then 1 else 0 end) as  total_order,"
        );
        if (!empty($filter['start_date'])) {
            $this->db->where("order_shipping.created >= '" . $filter['start_date'] . "'");
        }
        if (!empty($filter['end_date'])) {
            $this->db->where("order_shipping.created <= '" . $filter['end_date'] . "'");
        }
        if (!empty($filter['zone'])) {
            $this->db->where('order_shipping.zone', $filter['zone']);
        }
        $this->db->where('order_shipping.user_id', $user_id);
        $this->db->where('order_shipping.zone !=', '');
        $this->db->group_by('order_shipping.zone');
        $query = $this->db->get('order_shipping');
        return $query->result();
    }

    function getShipmentsByIds($ship_ids = false, $courier_ids = false)
    {
        if (!$ship_ids || !$courier_ids)
            return false;

        $this->db->where_in('id', $ship_ids);
        $this->db->where_in('courier_id', $courier_ids);
        $q = $this->db->get($this->table);
        return $q->result();
    }

    function get_pending_ed_shipments()
    {
        $this->db->where('status', '0');
        $q = $this->db->get('escalation_delivery_shipments');
        return $q->result();
    }

    public function get_shipment_bill($ship_id = false)
    {
        if (!$ship_id)
            return false;

        $this->db->where('ship_id', $ship_id);
        $this->db->order_by('id', 'desc');
        $q = $this->db->get('international_shipment_bill');
        return $q->row();
    }

    public function get_cred_ageing_attempted($user_id = false)
    {
        if (!$user_id)
            return false;

        $this->slave = $this->load->database('slave', TRUE);

        $this->slave->select("group_concat(id) AS shipment_ids");
        $this->slave->where('user_id', $user_id);
        $this->slave->where('edd_time > 1666656000');
        $this->slave->where('edd_time <=', strtotime(date('Y-m-d')));
        $this->slave->where_in('ship_status', ['in transit', 'exception', 'out for delivery']);
        $q = $this->slave->get($this->table);
        $shipments = $q->row();

        if (empty($shipment_ids = $shipments->shipment_ids))
            return false;

        $this->slave->select("FROM_UNIXTIME(ofd_attempt_1_date, '%Y-%m-%d') AS ofd_attempt_1_date, FROM_UNIXTIME(last_ndr_date, '%Y-%m-%d') AS last_ndr_date, FROM_UNIXTIME(edd_time, '%Y-%m-%d') AS edd_time, CURDATE() AS curr_date, datediff(CURDATE(), FROM_UNIXTIME(edd_time, '%Y-%m-%d')) AS days_diff, count(shipment_id) AS count_shipment");

        $this->slave->where('edd_time > 1666656000');
        $this->slave->where('total_ofd_attempts > 0');
        $this->slave->where('last_ndr_date != 0');
        $this->slave->where("shipment_id IN ($shipment_ids)");
        $this->slave->group_by('days_diff');
        $this->slave->order_by('days_diff', 'asc');
        $q = $this->slave->get('shipment_tracking');
        return $q->result();
    }

    public function get_cred_ageing_not_attempted($user_id = false)
    {
        if (!$user_id)
            return false;

        $this->slave = $this->load->database('slave', TRUE);

        $this->slave->select("group_concat(id) AS shipment_ids");
        $this->slave->where('user_id', $user_id);
        $this->slave->where('edd_time > 1666656000');
        $this->slave->where('edd_time <=', strtotime(date('Y-m-d')));
        $this->slave->where_in('ship_status', ['in transit', 'exception', 'out for delivery']);
        $q = $this->slave->get($this->table);
        $shipments = $q->row();

        if (empty($shipment_ids = $shipments->shipment_ids))
            return false;

        $this->slave->select("FROM_UNIXTIME(edd_time, '%Y-%m-%d') AS edd_time, CURDATE() AS curr_date, datediff(CURDATE(), FROM_UNIXTIME(edd_time, '%Y-%m-%d')) AS days_diff, count(shipment_id) AS count_shipment");

        $this->slave->where('edd_time > 1666656000');
        $this->slave->where("shipment_id IN ($shipment_ids)");
        $this->slave->group_by('days_diff');
        $this->slave->order_by('days_diff', 'asc');
        $q = $this->slave->get('shipment_tracking');
        return $q->result();
    }

    public function get_cred_ageing_awb_report($user_id = false)
    {
        if (!$user_id)
            return false;

        $this->slave = $this->load->database('slave', TRUE);

        $this->slave->select("group_concat(id) AS shipment_ids");
        $this->slave->where('user_id', $user_id);
        $this->slave->where('edd_time > 1666656000');
        $this->slave->where('edd_time <=', strtotime(date('Y-m-d')));
        $this->slave->where_in('ship_status', ['in transit', 'exception', 'out for delivery']);
        $q = $this->slave->get($this->table);
        $shipments = $q->row();

        if (empty($shipment_ids = $shipments->shipment_ids))
            return false;

        $this->slave->select("shipment_id, courier_id, awb_number, ship_status, FROM_UNIXTIME(shipment_tracking.edd_time, '%Y-%m-%d') AS edd_time, datediff(CURDATE(), FROM_UNIXTIME(shipment_tracking.edd_time, '%Y-%m-%d')) AS days_diff, (case when (total_ofd_attempts > '0' AND last_ndr_date != '0') then 'Attempted' else 'Not Attempted' end) as ageing_status");

        $this->slave->join('order_shipping', 'order_shipping.id = shipment_tracking.shipment_id');

        $this->slave->where('shipment_tracking.edd_time > 1666656000');
        $this->slave->where("shipment_id IN ($shipment_ids)");
        $q = $this->slave->get('shipment_tracking');
        return $q->result();
    }


    // public function rechargeableShipmentsOfUser($user_id = false)
    // {
    //     if (!$user_id)
    //         return false;

    //     $this->db->select("order_shipping.id as id, tbl_orders.order_amount as order_amount");

    //     $this->db->where('orders.user_id', $user_id);
    //     $this->db->where("remittance_id", '0');
    //     $this->db->where('orders.order_payment_type', 'COD');
    //     $this->db->where("order_shipping.ship_status", 'delivered');

    //     $this->db->join('orders', 'orders.id = order_shipping.order_id', 'LEFT');
    //     $q = $this->db->get($this->table);
    //     return $q->result();
    // }

    // function remittanceShipmentList($shipment_ids = array())
    // {
        // if (empty($shipment_ids))
        //     return false;

        // $this->db->select("order_shipping.*, courier.name as courier_name");

        // if (!empty($shipment_ids))
        //     $this->db->where_in('order_shipping.id', $shipment_ids);

        // $this->db->where("order_shipping.ship_status", 'delivered');
        // $this->db->where("remittance_id", '0');
        // $this->db->where('order_shipping.payment_type', 'COD');

        // //$this->db->join('orders', 'orders.id = order_shipping.order_id');
        // $this->db->join('users', 'users.id = order_shipping.user_id');

        // $this->db->join('courier', 'courier.id = order_shipping.courier_id');

        // $this->db->order_by('order_shipping.delivered_time', 'asc');

        // $q = $this->db->get($this->table);
        // return $q->result();
    // }

    function getByUserIDBulkOrder($user_id = false, $limit = 50, $offset = 0, $filter = array()) {
        if (!$user_id)
            return false;

        $this->slave = $this->load->database('slave', TRUE);

        $this->slave->select("order_shipping.id");
        $this->slave->where('order_shipping.user_id', $user_id);

        if (!empty($filter['courier_id'])) {
            $this->slave->where('order_shipping.courier_id', $filter['courier_id']);
        }

        if (!empty($filter['awb_number'])) {
            $this->slave->where_in('order_shipping.awb_number', $filter['awb_number']);
        }

        if (!empty($filter['ship_status_not_in'])) {
            $this->slave->where_not_in('order_shipping.ship_status', $filter['ship_status_not_in']);
        }

        $this->slave->limit($limit);
        $this->slave->offset($offset);
        $q = $this->slave->get($this->table);

        if ($q->result())
            return $q->result();
        else
            return false;
    }

    function getChannelName($channel_id){
        $this->db->select('channel');
        $this->db->where('id',$channel_id);
        $q = $this->db->get('user_channels');
        if(!empty($q->row())){
            return $q->row();
        }
        return false;

    }

    function getExportShipmentByID($shipment_id = false)
    {
        if (!$shipment_id)
            return false;

        $this->db->select('order_shipping.awb_number,order_shipping.ship_status,orders.order_type,orders.shipping_fname,orders.shipping_lname,orders.order_no,tbl_orders.order_amount,orders.collectable_amount,orders.package_weight,orders.order_payment_type, group_concat(tbl_order_products.product_name) as products');
        $this->db->where('order_shipping.id', $shipment_id);

        $this->db->limit(1);
        $this->db->join('orders', 'orders.id = order_shipping.order_id');
        $this->db->join('order_products', 'order_products.order_id = orders.id');
       // $this->db->join('courier', 'courier.id = order_shipping.courier_id');
     
        $q = $this->db->get($this->table);

        return $q->row();
    }

    function getMasterLabelSetting($user_id = false)
    {
        $this->db->where('user_id', $user_id);
        $q = $this->db->get('master_label_setting');
        if (!empty($q->row())) {
            return $q->row();
        }
        return false;
    }
}
