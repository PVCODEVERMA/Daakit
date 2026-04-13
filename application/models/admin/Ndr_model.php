<?php

class Ndr_model extends MY_model
{

    public function __construct()
    {
        parent::__construct();
        $this->table = 'ndr';
        $this->action_table = 'ndr_action';

        $this->slave = $this->load->database('slave', TRUE);
    }

    function insert($save = array())
    {
        if (empty($save))
            return false;

        $save['created'] = time();

        $this->db->insert($this->table, $save);
        return $this->db->insert_id();
    }

        public function Add_NDR_Action($data = array())
{
     if (empty($data))
            return false;


     $data['created'] = time();


    $this->db->insert($this->action_table, $data);

    if ($this->db->affected_rows() > 0) {
        return $this->db->insert_id(); // return new action ID
    } else {
        return false;
    }
}

 function getNdrLastAction($ndr_id = false)
    {
        if (!$ndr_id)
            return false;

        $this->db->where('ndr_id', $ndr_id);
        $this->db->order_by('id', 'desc');
        $this->db->limit(1);
        $q = $this->db->get($this->action_table);
        return $q->row();
    }


    function insert_action($save = array())
    {
        if (empty($save))
            return false;

        $save['created'] = time();

        $this->db->insert($this->action_table, $save);
        return $this->db->insert_id();
    }

    function update($id = false, $save = array())
    {
        if (empty($save) || empty($id))
            return false;

        $this->db->set($save);
        $this->db->where('id', $id);
        $this->db->update($this->table);
        return true;
    }

    function update_action($id = false, $save = array())
    {
        if (empty($save) || empty($id))
            return false;

        $this->db->set($save);
        $this->db->where('id', $id);
        $this->db->update($this->action_table);
        return true;
    }

    function getByShippingID($shipping_id = false)
    {
        if (!$shipping_id)
            return false;

        $this->db->where('shipment_id', $shipping_id);
        $this->db->order_by('id', 'desc');
        $this->db->limit(1);
        $q = $this->db->get($this->table);

        return $q->row();
    }

    function ndrCourierLastAction($ndr_id = false)
    {
        if (!$ndr_id)
            return false;

        $this->db->where('ndr_id', $ndr_id);
        $this->db->where('source', 'courier');
        $this->db->order_by('id', 'desc');
        $this->db->limit(1);
        $q = $this->db->get($this->action_table);
        return $q->row();
    }

    function ndrActionHistory($ndr_id = false)
    {
        if (!$ndr_id)
            return false;

        $this->db->where('ndr_id', $ndr_id);
        $this->db->order_by('id', 'desc');
        $q = $this->db->get($this->action_table);
        return $q->result();
    }

    function getByUserID($limit = 50, $offset = 0, $filter = array())
    {
        $this->slave->select(
            'ndr.*,'
                . ' tbl_order_shipping.awb_number,'
                . ' tbl_order_shipping.ship_status,'
                . ' tbl_order_shipping.rto_status,'
                . ' tbl_order_shipping.rto_awb,'
                . ' orders.channel_id as channel_id,'
                . ' group_concat(tbl_order_products.product_name)  as products,'
                . ' tbl_orders.order_no as order_number,'
                . ' orders.order_amount as order_amount,'
                . ' orders.order_payment_type as order_payment_type,'
                . ' orders.shipping_fname as shipping_fname,'
                . ' orders.shipping_lname as shipping_lname,'
                . ' orders.shipping_fname as shipping_fname,'
                . ' orders.shipping_lname as shipping_lname,'
                . ' orders.shipping_phone as shipping_phone,'
                . ' orders.shipping_address as shipping_address,'
                . ' orders.shipping_address_2 as shipping_address_2,'
                . ' orders.shipping_city as shipping_city,'
                . ' orders.shipping_state as shipping_state, '
                . ' orders.shipping_zip as shipping_zip, '
                . ' tbl_order_shipping.pickup_time as pickup_time, '
                . ' courier.id as courier_ids,'
                . ' courier.name as courier_name,'
                . ' tbl_ndr.last_action as ndr_action,'
                . ' tbl_ndr.last_action_by as ndr_source,'
                . ' ndr.total_attempts as ndr_attempt,'
                . ' ndr.latest_remarks as ndr_remarks, '
                . ' ndr.applied_tags as ndr_applied_tags,'
                . ' users.fname as user_fname,'
                . ' users.lname as user_lname,'
                . ' users.company_name as sellercompany,'
                . ' users.account_manager_id as manager_id,'
                . ' admin_users.fname as manager_fname,'
                . ' admin_users.lname as manager_lname,'
        );

        $this->slave->limit($limit);
        $this->slave->offset($offset);

        $ship_status = '';
        $action_source = array();

        if (!empty($filter['status'])) {
            switch ($filter['status']) {
                case 'pending':
                    $action_source = array('courier');
                    break;
                case 'submitted':
                    $action_source = array('seller', 'buyer');
                    break;
                case 'delivered':
                    $ship_status = 'delivered';
                    break;
                case 'rto':
                    $ship_status = 'rto';
                    break;
                default:
                    break;
            }
        }

        if ($ship_status) {
            $this->slave->where('tbl_order_shipping.ship_status', $ship_status);
        } else if ($action_source) {
            $this->slave->where_not_in('tbl_order_shipping.ship_status', $this->config->item('shipment_closed_status'));
        }

        if ($action_source && empty($filter['export_all'])) {
            $this->slave->where_in('tbl_ndr.last_action_by', $action_source);
        }

        if (!empty($filter['pay_method'])) {
            $this->slave->where('orders.order_payment_type', $filter['pay_method']);
        }

        if (!empty($filter['tags'])) {
            $this->slave->where(" (find_in_set('{$filter['tags']}', ndr.applied_tags))");
        }

        if (!empty($filter['seller_id'])) {
            $this->slave->where('orders.user_id', $filter['seller_id']);
        }

        if (!empty($filter['manager_id'])) {
            $this->slave->where_in('users.account_manager_id', array_map('intval', $filter['manager_id']));
        }

        if (!empty($filter['start_date'])) {
            $this->slave->where("ndr.created >= '" . $filter['start_date'] . "'");
        }

        if (!empty($filter['end_date'])) {
            $this->slave->where("ndr.created <= '" . $filter['end_date'] . "'");
        }

        if (!empty($filter['courier_id'])) {
            $this->slave->where('tbl_order_shipping.courier_id', $filter['courier_id']);
        }

        if (!empty($filter['attempts'])) {
            $this->slave->where('ndr.total_attempts', $filter['attempts']);
        }

        if (!empty($filter['ndr_type'])) {
            $this->slave->where('users.ndr_action_type', $filter['ndr_type']);
        }

        if (!empty($filter['order_ids'])) {
            $this->slave->where_in('tbl_orders.order_no', $filter['order_ids']);
        }

        if (!empty($filter['ndr_id'])) {
            $this->slave->where_in('ndr.id', $filter['ndr_id']);
        }

        if (!empty($filter['awb_no'])) {
            $this->slave->where_in('tbl_order_shipping.awb_number', $filter['awb_no']);
        }

        if (!empty($filter['channel_id'])) {
            if ($filter['channel_id'] == 'custom')
                $this->slave->where(" (orders.channel_id is NULL or orders.channel_id not in (select id from user_channels where user_id = '{$user_id}')) ");
            else
                $this->slave->where('orders.channel_id', $filter['channel_id']);
        }

        $this->slave->where('tbl_order_shipping.ship_status !=', 'cancelled');
        $this->slave->group_by('order_products.order_id, tbl_order_shipping.id');
        $this->slave->join('order_shipping', 'tbl_order_shipping.id = ndr.shipment_id');
        $this->slave->join('users', 'users.id = ndr.user_id');
        $this->slave->join("(
            SELECT    id,fname, lname 
             FROM      tbl_users
            where is_admin = '1'
        ) admin_users", 'admin_users.id = users.account_manager_id', 'left');
        $this->slave->join('orders', 'orders.id = tbl_order_shipping.order_id');
        $this->slave->join('order_products', 'order_products.order_id = orders.id');
        $this->slave->join('courier', 'courier.id = tbl_order_shipping.courier_id');
        $this->slave->order_by('ndr.id', 'desc');
        $q = $this->slave->get($this->table);
        //echo $this->slave->last_query();die;
        return $q->result();
    }

    function countByUserID($filter = array())
    {
        $this->slave->select('count(DISTINCT tbl_order_shipping.id) as total');

        $ship_status = '';
        $action_source = array();

        if (!empty($filter['status'])) {
            switch ($filter['status']) {
                case 'pending':
                    $action_source = array('courier');
                    break;
                case 'submitted':
                    $action_source = array('seller', 'buyer');
                    break;
                case 'delivered':
                    $ship_status = 'delivered';
                    break;
                case 'rto':
                    $ship_status = 'rto';
                    break;
                default:
                    break;
            }
        }

        if ($ship_status) {
            $this->slave->where('tbl_order_shipping.ship_status', $ship_status);
        } else if ($action_source) {
            $this->slave->where_not_in('tbl_order_shipping.ship_status', $this->config->item('shipment_closed_status'));
        }

        if ($action_source) {
            $this->slave->where_in('tbl_ndr.last_action_by', $action_source);
        }

        if (!empty($filter['pay_method'])) {
            $this->slave->where('orders.order_payment_type', $filter['pay_method']);
        }

        if (!empty($filter['tags'])) {
            $this->slave->where(" (find_in_set('{$filter['tags']}', ndr.applied_tags))");
        }

        if (!empty($filter['start_date'])) {
            $this->slave->where("ndr.created >= '" . $filter['start_date'] . "'");
        }

        if (!empty($filter['end_date'])) {
            $this->slave->where("ndr.created <= '" . $filter['end_date'] . "'");
        }

        if (!empty($filter['courier_id'])) {
            $this->slave->where('tbl_order_shipping.courier_id', $filter['courier_id']);
        }

        if (!empty($filter['attempts'])) {
            $this->slave->where('ndr.total_attempts', $filter['attempts']);
        }
        if (!empty($filter['ndr_type'])) {
            $this->slave->where('users.ndr_action_type', $filter['ndr_type']);
        }

        if (!empty($filter['order_ids'])) {
            $this->slave->where_in('tbl_orders.order_no', $filter['order_ids']);
        }

        if (!empty($filter['ndr_id'])) {
            $this->slave->where_in('ndr.id', $filter['ndr_id']);
        }

        if (!empty($filter['awb_no'])) {
            $this->slave->where_in('tbl_order_shipping.awb_number', $filter['awb_no']);
        }

        if (!empty($filter['seller_id'])) {
            $this->slave->where('orders.user_id', $filter['seller_id']);
        }

        if (!empty($filter['manager_id'])) {
            $this->slave->where_in('users.account_manager_id', array_map('intval', $filter['manager_id']));
        }

        if (!empty($filter['channel_id'])) {
            if ($filter['channel_id'] == 'custom')
                $this->slave->where(" (orders.channel_id is NULL or orders.channel_id not in (select id from user_channels where user_id = '{$user_id}')) ");
            else
                $this->slave->where('orders.channel_id', $filter['channel_id']);
        }
        $this->slave->where('tbl_order_shipping.ship_status !=', 'cancelled');
        $this->slave->join('order_shipping', 'tbl_order_shipping.id = ndr.shipment_id');
        $this->slave->join('users', 'users.id = ndr.user_id');
        $this->slave->join('orders', 'orders.id = tbl_order_shipping.order_id');
        $this->slave->order_by('ndr.id', 'desc');
        $q = $this->slave->get($this->table);
        return $q->row()->total;
    }

    function countByUserIDStatusGrouped($filter = array())
    {
        $this->slave->select(""
            . "sum(case when (tbl_order_shipping.ship_status = 'delivered') then 1 else 0 end) as delivered,"
            . "sum(case when (tbl_order_shipping.ship_status = 'rto') then 1 else 0 end) as rto,"
            . "sum(case when (tbl_ndr.last_action = 'ndr' and tbl_order_shipping.ship_status not in ('rto', 'delivered','lost','damaged')) then 1 else 0 end) as action_required,"
            . "sum(case when (tbl_ndr.last_action != 'ndr' and tbl_order_shipping.ship_status not in ('rto', 'delivered','lost','damaged')) then 1 else 0 end) as action_requested,");

        if (!empty($filter['pay_method'])) {
            $this->slave->where('orders.order_payment_type', $filter['pay_method']);
        }

        if (!empty($filter['start_date'])) {
            $this->slave->where("ndr.created >= '" . $filter['start_date'] . "'");
        }

        if (!empty($filter['tags'])) {
            $this->slave->where(" (find_in_set('{$filter['tags']}', ndr.applied_tags))");
        }

        if (!empty($filter['end_date'])) {
            $this->slave->where("ndr.created <= '" . $filter['end_date'] . "'");
        }

        if (!empty($filter['courier_id'])) {
            $this->slave->where('tbl_order_shipping.courier_id', $filter['courier_id']);
        }

        if (!empty($filter['attempts'])) {
            $this->slave->where('ndr.total_attempts', $filter['attempts']);
        }

        if (!empty($filter['ndr_type'])) {
            $this->slave->where('users.ndr_action_type', $filter['ndr_type']);
        }

        if (!empty($filter['order_ids'])) {
            $this->slave->where_in('tbl_orders.order_no', $filter['order_ids']);
        }

        if (!empty($filter['ndr_id'])) {
            $this->slave->where_in('ndr.id', $filter['ndr_id']);
        }

        if (!empty($filter['seller_id'])) {
            $this->slave->where('orders.user_id', $filter['seller_id']);
        }

        if (!empty($filter['manager_id'])) {
            $this->slave->where_in('users.account_manager_id', array_map('intval', $filter['manager_id']));
        }

        if (!empty($filter['awb_no'])) {
            $this->slave->where_in('tbl_order_shipping.awb_number', $filter['awb_no']);
        }

        if (!empty($filter['channel_id'])) {
            if ($filter['channel_id'] == 'custom')
                $this->slave->where(" (orders.channel_id is NULL or orders.channel_id not in (select id from user_channels where user_id = '{$user_id}')) ");
            else
                $this->slave->where('orders.channel_id', $filter['channel_id']);
        }
        $this->slave->where('tbl_order_shipping.ship_status !=', 'cancelled');
        $this->slave->join('order_shipping', 'tbl_order_shipping.id = ndr.shipment_id');
        $this->slave->join('users', 'users.id = ndr.user_id');
        $this->slave->join('orders', 'orders.id = tbl_order_shipping.order_id');
        $this->slave->order_by('ndr.id', 'desc');
        $q = $this->slave->get($this->table);
        return $q->row();
    }

    function getNdrCsvdetails($filter = array())
    {
        $this->slave->select("ndr.*,
        tbl_ndr_action.customer_details_name as details_name,
        tbl_ndr_action.customer_details_address_1 as details_address_1,
        tbl_ndr_action.customer_details_address_2 as details_address_2,
        tbl_ndr_action.customer_contact_phone as contact_phone,
        tbl_order_shipping.awb_number,
        tbl_order_shipping.ship_status,
        tbl_order_shipping.rto_status,
        tbl_order_shipping.rto_awb,
        orders.channel_id as channel_id,
        tbl_orders.order_no as order_number,
        orders.order_amount as order_amount,
        orders.order_payment_type as order_payment_type,
        orders.shipping_fname as shipping_fname,
        orders.shipping_lname as shipping_lname,
        orders.shipping_fname as shipping_fname,
        orders.shipping_lname as shipping_lname,
        orders.shipping_phone as shipping_phone,
        orders.shipping_address as shipping_address,
        orders.shipping_address_2 as shipping_address_2,
        orders.shipping_city as shipping_city,
        orders.shipping_state as shipping_state,
        orders.shipping_zip as shipping_zip,
        group_concat(tbl_order_products.product_name) as products,
        tbl_order_shipping.pickup_time as pickup_time,
        courier.id as courier_ids,
        courier.name as courier_name,
        users.fname as user_fname,
        users.lname as user_lname,
        users.ndr_action_type,
        users.company_name as sellercompany,
        users.account_manager_id as manager_id,
        admin_users.fname as manager_fname,
        admin_users.lname as manager_lname,
        group_concat(tbl_ndr_action.created, '<->',tbl_ndr_action.action, '<->',tbl_ndr_action.remarks,'<->',tbl_ndr_action.attempt,'<->',tbl_ndr_action.push_ndr_status,'<->',tbl_ndr_action.push_ndr_message,'<->',tbl_ndr_action.source  SEPARATOR '|||||') as remarks,
        shipment_tracking.*,
        shipment_tracking.pickup_time as pickupTime,
        tbl_order_shipping.pending_pickup_date as pickupRequest
        ");

        //datediff(from_unixtime(shipment_tracking.pickup_time),from_unixtime(tbl_order_shipping.pending_pickup_date)) age_to_reqtime
        $action_source = array();
        $ship_status = '';

        if (!empty($filter['status'])) {
            switch ($filter['status']) {
                case 'pending':
                    $action_source = array('courier');
                    break;
                case 'submitted':
                    $action_source = array('seller', 'buyer');
                    break;
                case 'delivered':
                    $ship_status = 'delivered';
                    break;
                case 'rto':
                    $ship_status = 'rto';
                    break;
                default:
                    break;
            }
        }

        if ($ship_status) {
            $this->slave->where('tbl_order_shipping.ship_status', $ship_status);
        } else if ($action_source) {
            $this->slave->where_not_in('tbl_order_shipping.ship_status', $this->config->item('shipment_closed_status'));
        }

        if ($action_source && empty($filter['export_all'])) {
            $this->slave->where_in('tbl_ndr.last_action_by', $action_source);
        }

        if (!empty($filter['pay_method'])) {
            $this->slave->where('orders.order_payment_type', $filter['pay_method']);
        }

        if (!empty($filter['tags'])) {
            $this->slave->where(" (find_in_set('{$filter['tags']}', ndr.applied_tags))");
        }

        if (!empty($filter['seller_id'])) {
            $this->slave->where('orders.user_id', $filter['seller_id']);
        }

        if (!empty($filter['manager_id'])) {
            $this->slave->where_in('users.account_manager_id', array_map('intval', $filter['manager_id']));
        }

        if (!empty($filter['start_date'])) {
            $this->slave->where("ndr.created >= '" . $filter['start_date'] . "'");
        }

        if (!empty($filter['end_date'])) {
            $this->slave->where("ndr.created <= '" . $filter['end_date'] . "'");
        }

        if (!empty($filter['courier_id'])) {
            $this->slave->where('tbl_order_shipping.courier_id', $filter['courier_id']);
        }

        if (!empty($filter['attempts'])) {
            $this->slave->where('ndr.total_attempts', $filter['attempts']);
        }

        if (!empty($filter['ndr_type'])) {
            $this->slave->where('users.ndr_action_type', $filter['ndr_type']);
        }

        if (!empty($filter['order_ids'])) {
            $this->slave->where_in('tbl_orders.order_no', $filter['order_ids']);
        }

        if (!empty($filter['ndr_id'])) {
            $this->slave->where_in('ndr.id', $filter['ndr_id']);
        }

        if (!empty($filter['awb_no'])) {
            $this->slave->where_in('tbl_order_shipping.awb_number', $filter['awb_no']);
        }

        if (!empty($filter['channel_id'])) {
            if ($filter['channel_id'] == 'custom')
                $this->slave->where(" (orders.channel_id is NULL or orders.channel_id not in (select id from user_channels where user_id = '{$user_id}')) ");
            else
                $this->slave->where('orders.channel_id', $filter['channel_id']);
        }

        $this->slave->where('tbl_order_shipping.ship_status !=', 'cancelled');
        $this->slave->group_by('order_products.order_id, tbl_order_shipping.id');
        $this->slave->join('order_shipping', 'tbl_order_shipping.id = ndr.shipment_id');
        //$this->slave->join('ndr_action', 'tbl_ndr_action.ndr_id=ndr.id', 'LEFT');
        
        $this->slave->join('ndr_action', 'tbl_ndr_action.id = tbl_ndr.last_action_id', 'LEFT');
        
        $this->slave->join('users', 'users.id = ndr.user_id');
        $this->slave->join("users admin_users", 'admin_users.id = users.account_manager_id', 'left');
        $this->slave->join('orders', 'orders.id = tbl_order_shipping.order_id');
        $this->slave->join('order_products', 'order_products.order_id = orders.id');
        $this->slave->join('courier', 'courier.id = tbl_order_shipping.courier_id');
        $this->slave->join('shipment_tracking', 'shipment_tracking.shipment_id = ndr.shipment_id');

        $this->slave->order_by('ndr.id', 'desc');
        $this->slave->from($this->table);
        return $query =   $this->slave->get_compiled_select();
        //$q = $this->slave->get($this->table);
        //echo $this->slave->last_query();die;
        return $q->result();
    }
}
