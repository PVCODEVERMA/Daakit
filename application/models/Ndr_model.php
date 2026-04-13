<?php

class Ndr_model extends MY_model
{

    public function __construct()
    {
        parent::__construct();
        $this->table = 'ndr';
        $this->action_table = 'ndr_action';
        $this->ed_shipment_table = 'escalation_delivery_shipments';
    }

    function insert($save = array())
    {
        if (empty($save))
            return false;

        $save['created'] = time();

        $this->db->insert($this->table, $save);
        return $this->db->insert_id();
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

    function getByActionID($id = false)
    {
        if (!$id)
            return false;

        $this->db->where('id', $id);
        $this->db->limit(1);
        $q = $this->db->get($this->action_table);

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

    function getBuyerLastAction($ndr_id = false)
    {
        if (!$ndr_id)
            return false;

        $this->db->where('ndr_id', $ndr_id);
        $this->db->where('source', 'buyer');
        $this->db->order_by('id', 'desc');
        $this->db->limit(1);
        $q = $this->db->get($this->action_table);
        return $q->row();
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

    function ndrActionHistory($ndr_id = false)
    {
        if (!$ndr_id)
            return false;

        $this->db->where('ndr_id', $ndr_id);
        $this->db->order_by('id', 'desc');
        $q = $this->db->get($this->action_table);
        return $q->result();
    }

    function getByUserID($user_id = false, $limit = 50, $offset = 0, $filter = array())
    {
        if (!$user_id)
            return false;

        $this->db->select(
            'tbl_ndr.*,'
                . ' tbl_order_shipping.awb_number,'
                . ' tbl_order_shipping.ship_status,'
                . ' tbl_order_shipping.rto_status as rto_status, '
                . ' orders.channel_id as channel_id,'
                . ' group_concat(tbl_order_products.product_name)  as products,'
                . ' orders.order_no as order_number,'
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
                . ' courier.name as courier_name,'
                . ' tbl_ndr.last_action as ndr_action,'
                . ' tbl_ndr.last_action_by as ndr_source,'
                . ' tbl_ndr.total_attempts as ndr_attempt,'
                . ' tbl_ndr.latest_remarks as ndr_remarks, '
                . ' tbl_ndr.applied_tags as ndr_applied_tags '
        );

        $this->db->limit($limit);
        $this->db->offset($offset);

        $action_source = false;
        $ship_status = false;
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
                    $action_source = array('courier');
            }
        } else {
            $action_source = array('courier','seller', 'buyer');
        }


        if ($ship_status) {
            $this->db->where('tbl_order_shipping.ship_status', $ship_status);
        } else {
            $this->db->where_not_in('tbl_order_shipping.ship_status', $this->config->item('shipment_closed_status'));
        }

        if ($action_source && empty($filter['export_all'])) {
            $this->db->where_in('tbl_ndr.last_action_by', $action_source);
        }


        if (!empty($filter['pay_method'])) {
            $this->db->where('orders.order_payment_type', $filter['pay_method']);
        }

        if (!empty($filter['tags'])) {
            $this->db->where(" (find_in_set('{$filter['tags']}', tbl_ndr.applied_tags))");
        }

        if (!empty($filter['start_date'])) {
            $this->db->where("tbl_ndr.created >= '" . $filter['start_date'] . "'");
        }

        if (!empty($filter['end_date'])) {
            $this->db->where("tbl_ndr.created <= '" . $filter['end_date'] . "'");
        }

        if (!empty($filter['courier_id'])) {
            $this->db->where('tbl_order_shipping.courier_id', $filter['courier_id']);
        }

        if (!empty($filter['attempts'])) {
            $this->db->where('tbl_ndr.total_attempts', $filter['attempts']);
        }

        if (!empty($filter['order_ids'])) {
            $this->db->where_in('orders.order_no', $filter['order_ids']);
        }

        if (!empty($filter['ndr_id'])) {
            $this->db->where_in('tbl_ndr.id', $filter['ndr_id']);
        }

        if (!empty($filter['awb_no'])) {
            $this->db->where_in('tbl_order_shipping.awb_number', $filter['awb_no']);
        }

        if (!empty($filter['channel_id'])) {
            if ($filter['channel_id'] == 'custom')
                $this->db->where(" (orders.channel_id is NULL or orders.channel_id not in (select id from user_channels where user_id = '{$user_id}')) ");
            else
                $this->db->where('orders.channel_id', $filter['channel_id']);
        }


        $this->db->where('tbl_ndr.user_id', $user_id);
        $this->db->where('tbl_order_shipping.ship_status !=', 'cancelled');

        $this->db->group_by('order_products.order_id, tbl_order_shipping.id');

        $this->db->join('tbl_order_shipping', 'tbl_order_shipping.id = tbl_ndr.shipment_id');
        $this->db->join('orders', 'orders.id = tbl_order_shipping.order_id');
        $this->db->join('tbl_order_products', 'order_products.order_id = orders.id');
        $this->db->join('courier', 'courier.id = tbl_order_shipping.courier_id');


        $this->db->order_by('tbl_ndr.id', 'desc');


        $q = $this->db->get($this->table);

        //echo $this->db->last_query();
        // exit;

        return $q->result();
    }

    function countByUserID($user_id = false, $filter = array())
    {
        if (!$user_id)
            return false;

        $this->db->select('count(DISTINCT tbl_order_shipping.id) as total');

        $action_source = false;
        $ship_status = false;
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
                    $action_source = array('courier');
            }
        } else {
            $action_source = array('courier');
        }

        if ($ship_status) {
            $this->db->where('tbl_order_shipping.ship_status', $ship_status);
        } else {
            $this->db->where_not_in('tbl_order_shipping.ship_status', $this->config->item('shipment_closed_status'));
        }

        if ($action_source) {
            $this->db->where_in('tbl_ndr.last_action_by', $action_source);
        }

        if (!empty($filter['pay_method'])) {
            $this->db->where('orders.order_payment_type', $filter['pay_method']);
        }

        if (!empty($filter['tags'])) {
            $this->db->where(" (find_in_set('{$filter['tags']}', tbl_ndr.applied_tags))");
        }

        if (!empty($filter['start_date'])) {
            $this->db->where("tbl_ndr.created >= '" . $filter['start_date'] . "'");
        }

        if (!empty($filter['end_date'])) {
            $this->db->where("tbl_ndr.created <= '" . $filter['end_date'] . "'");
        }

        if (!empty($filter['courier_id'])) {
            $this->db->where('tbl_order_shipping.courier_id', $filter['courier_id']);
        }

        if (!empty($filter['attempts'])) {
            $this->db->where('tbl_ndr.total_attempts', $filter['attempts']);
        }

        if (!empty($filter['order_ids'])) {
            $this->db->where_in('orders.order_no', $filter['order_ids']);
        }

        if (!empty($filter['ndr_id'])) {
            $this->db->where_in('tbl_ndr.id', $filter['ndr_id']);
        }

        if (!empty($filter['awb_no'])) {
            $this->db->where_in('tbl_order_shipping.awb_number', $filter['awb_no']);
        }

        if (!empty($filter['channel_id'])) {
            if ($filter['channel_id'] == 'custom')
                $this->db->where(" (orders.channel_id is NULL or orders.channel_id not in (select id from user_channels where user_id = '{$user_id}')) ");
            else
                $this->db->where('orders.channel_id', $filter['channel_id']);
        }


        $this->db->where('tbl_ndr.user_id', $user_id);
        $this->db->where('tbl_order_shipping.ship_status !=', 'cancelled');

        //$this->db->group_by('order_products.order_id, tbl_order_shipping.id');

        $this->db->join('tbl_order_shipping', 'tbl_order_shipping.id = tbl_ndr.shipment_id');
        $this->db->join('orders', 'orders.id = tbl_order_shipping.order_id');


        $this->db->order_by('tbl_ndr.id', 'desc');
        $q = $this->db->get($this->table);

        return $q->row()->total;
    }

    function countByUserIDStatusGrouped($user_id = false, $filter = array())
    {

        $this->db->select(""
            . "sum(case when (tbl_order_shipping.ship_status = 'delivered') then 1 else 0 end) as delivered,"
            . "sum(case when (tbl_order_shipping.ship_status = 'rto') then 1 else 0 end) as rto,"
            . "sum(case when (tbl_ndr.last_action = 'ndr' and tbl_order_shipping.ship_status not in ('rto', 'delivered','lost','damaged')) then 1 else 0 end) as action_required,"
            . "sum(case when (tbl_ndr.last_action != 'ndr' and tbl_order_shipping.ship_status not in ('rto', 'delivered','lost','damaged')) then 1 else 0 end) as action_requested,");

        if (!empty($filter['pay_method'])) {
            $this->db->where('orders.order_payment_type', $filter['pay_method']);
        }

        if (!empty($filter['start_date'])) {
            $this->db->where("tbl_ndr.created >= '" . $filter['start_date'] . "'");
        }

        if (!empty($filter['tags'])) {
            $this->db->where(" (find_in_set('{$filter['tags']}', tbl_ndr.applied_tags))");
        }

        if (!empty($filter['end_date'])) {
            $this->db->where("tbl_ndr.created <= '" . $filter['end_date'] . "'");
        }

        if (!empty($filter['courier_id'])) {
            $this->db->where('tbl_order_shipping.courier_id', $filter['courier_id']);
        }

        if (!empty($filter['attempts'])) {
            $this->db->where('tbl_ndr.total_attempts', $filter['attempts']);
        }

        if (!empty($filter['order_ids'])) {
            $this->db->where_in('orders.order_no', $filter['order_ids']);
        }

        if (!empty($filter['ndr_id'])) {
            $this->db->where_in('tbl_ndr.id', $filter['ndr_id']);
        }

        if (!empty($filter['awb_no'])) {
            $this->db->where_in('tbl_order_shipping.awb_number', $filter['awb_no']);
        }

        if (!empty($filter['channel_id'])) {
            if ($filter['channel_id'] == 'custom')
                $this->db->where(" (orders.channel_id is NULL or orders.channel_id not in (select id from user_channels where user_id = '{$user_id}')) ");
            else
                $this->db->where('orders.channel_id', $filter['channel_id']);
        }

        $this->db->where('tbl_ndr.user_id', $user_id);
        $this->db->where('tbl_order_shipping.ship_status !=', 'cancelled');

        //$this->db->group_by('tbl_order_shipping.ship_status');

        $this->db->join('tbl_order_shipping', 'tbl_order_shipping.id = tbl_ndr.shipment_id');
        $this->db->join('orders', 'orders.id = tbl_order_shipping.order_id');


        $this->db->order_by('tbl_ndr.id', 'desc');
        $q = $this->db->get($this->table);
        //echo $this->db->last_query();die;
        return $q->row();
    }

    function ndrsheetforCourier($filter = array())
    {

        $this->slave = $this->load->database('slave', TRUE);

        $this->slave->select("
            tbl_order_shipping.courier_id,
            tbl_order_shipping.awb_number,
            tbl_ndr.last_action as ndr_action,
            tbl_ndr.latest_remarks as ndr_remarks, 
            tbl_ndr.last_action_by as ndr_source,
            ndr_action.customer_details_name, 
            ndr_action.customer_contact_phone,
            CONCAT(ndr_action.customer_details_address_1,' ', ndr_action.customer_details_address_2) as change_customer_address,
            tbl_order_shipping.ship_status, 
            shipment_tracking.last_attempt_date,
            shipment_tracking.last_ndr_reason,
            shipment_tracking.total_ofd_attempts,
            shipment_tracking.delivery_attempt_count,
            orders.shipping_fname,
            orders.shipping_lname,
            orders.shipping_address,
            orders.shipping_address_2,
            orders.shipping_phone,
            orders.shipping_city,
            orders.shipping_state,
            orders.shipping_zip,
            orders.shipping_phone,
            users.ndr_action_type
        ");

        $this->slave->where_not_in('tbl_order_shipping.ship_status', $this->config->item('shipment_closed_status'));

        $this->slave->where_in('tbl_ndr.last_action_by', array('seller', 'buyer'));

        if (!empty($filter['start_date'])) {
            $this->slave->where("tbl_ndr.last_event >= '" . $filter['start_date'] . "'");
        }

        if (!empty($filter['end_date'])) {
            $this->slave->where("tbl_ndr.last_event <= '" . $filter['end_date'] . "'");
        }

        if (!empty($filter['courier_ids'])) {
            $this->slave->where_in('tbl_order_shipping.courier_id', $filter['courier_ids']);
        }

        $this->slave->where('tbl_order_shipping.ship_status !=', 'cancelled');

        $this->slave->where('shipment_tracking.total_ofd_attempts <', '3');

        $this->slave->group_by('tbl_order_shipping.id');

        $this->slave->join('ndr_action', 'ndr_action.id = tbl_ndr.last_action_id', 'LEFT');
        $this->slave->join('users', 'users.id = tbl_ndr.user_id');
        $this->slave->join('tbl_order_shipping', 'tbl_order_shipping.id = tbl_ndr.shipment_id');
        $this->slave->join('orders', 'orders.id = tbl_order_shipping.order_id', 'LEFT');
        $this->slave->join('shipment_tracking', 'shipment_tracking.shipment_id = tbl_order_shipping.id', 'LEFT');

        $this->slave->order_by('tbl_ndr.id', 'desc');


        $q = $this->slave->get($this->table);
        return $q->result();
    }


    function getNdrForCourierPush($filter = array())
    {

        $this->slave = $this->load->database('slave', TRUE);

        $this->slave->select("tbl_ndr.id");

        $this->slave->where_not_in('tbl_order_shipping.ship_status', $this->config->item('shipment_closed_status'));

        $this->slave->where_in('tbl_ndr.last_action_by', array('seller', 'buyer'));

        if (!empty($filter['start_date'])) {
            $this->slave->where("tbl_ndr.last_event >= '" . $filter['start_date'] . "'");
        }

        if (!empty($filter['end_date'])) {
            $this->slave->where("tbl_ndr.last_event <= '" . $filter['end_date'] . "'");
        }

        if (!empty($filter['courier_ids'])) {
            $this->slave->where_in('tbl_order_shipping.courier_id', $filter['courier_ids']);
        }

        $this->slave->where('tbl_ndr.push_api_status', '0');

        $this->slave->where_not_in('tbl_order_shipping.ship_status', ['cancelled', 'delivered', 'rto', 'lost', 'damaged']);

        $this->slave->join('tbl_order_shipping', 'tbl_order_shipping.id = tbl_ndr.shipment_id');


        $this->slave->order_by('tbl_ndr.id', 'desc');


        $q = $this->slave->get($this->table);

        

        return $q->result();
    }

    function getAllTags($user_id = false)
    {
        if (!$user_id)
            return false;

        $this->db->select("group_concat(applied_tags) as applied_tags");

        $this->db->where('user_id', $user_id);
        $this->db->where('applied_tags !=', '');

        $this->db->where('created >', strtotime('-1 month'));

        $this->db->order_by('id', 'desc');

        return $this->db->get($this->table)->row();
    }

    function getAllNDR($shipment_ids = array())
    {
        if (empty($shipment_ids))
            return false;

        $this->db->where_in('tbl_ndr.shipment_id', $shipment_ids);

        $this->db->join('ndr_action', "ndr_action.ndr_id = tbl_ndr.id and ndr_action.source='courier'");
        return $this->db->get('ndr')->result();
    }

    function getIVRforCaller($caller_id = false, $limit = 50, $offset = 0, $filter = array())
    {
        if (!$caller_id)
            return false;

        if (empty($filter))
            return false;

        $this->db->select(
            'tbl_ndr.*,'
                . 'orders.order_no as order_number,'
                . ' tbl_ndr.last_action as ndr_action,'
                . ' tbl_ndr.last_action_by as ndr_source,'
                . ' tbl_ndr.total_attempts as ndr_attempt,'
                . ' tbl_ndr.latest_remarks as ndr_remarks, '
                . ' tbl_ndr.applied_tags as ndr_applied_tags, '
                . ' tbl_ndr.caller_status as caller_status, '
                . ' user_channels.channel_name as channel_name, '
                . ' group_concat(tbl_order_products.product_name)  as products,'
        );

        $this->db->limit($limit);
        $this->db->offset($offset);

        $this->db->where('tbl_ndr.caller_id', $caller_id);

        $this->db->where_not_in('tbl_order_shipping.ship_status', $this->config->item('shipment_closed_status'));

        $this->db->where('tbl_ndr.last_action_by', 'courier');



        if (!empty($filter['seller_ids']))
            $this->db->where_in('tbl_ndr.user_id', $filter['seller_ids']);

        if (!empty($filter['sku_in']))
            $this->db->where_in('order_products.product_sku', $filter['sku_in']);


        if (!empty($filter['call_status']))
            $this->db->where('tbl_ndr.caller_status', $filter['call_status']);


        $this->db->group_by('tbl_ndr.id');

        $this->db->join('tbl_order_shipping', 'tbl_order_shipping.id = tbl_ndr.shipment_id');
        $this->db->join('orders', 'orders.id = tbl_order_shipping.order_id');
        $this->db->join('tbl_order_products', 'order_products.order_id = orders.id');
        $this->db->join('user_channels', 'user_channels.id = orders.channel_id');


        $this->db->order_by('tbl_ndr.id', 'desc');


        $q = $this->db->get($this->table);


        return $q->result();
    }

    function countIVRforCaller($caller_id = false, $filter = array())
    {
        if (!$caller_id)
            return false;

        if (empty($filter))
            return false;

        $this->db->select('count(tbl_ndr.id) as total');


        $this->db->where('tbl_ndr.caller_id', $caller_id);

        $this->db->where_not_in('tbl_order_shipping.ship_status', $this->config->item('shipment_closed_status'));

        $this->db->where('tbl_ndr.last_action_by', 'courier');



        if (!empty($filter['seller_ids']))
            $this->db->where_in('tbl_ndr.user_id', $filter['seller_ids']);

        if (!empty($filter['call_status']))
            $this->db->where('tbl_ndr.caller_status', $filter['call_status']);


        if (!empty($filter['sku_in']))
            $this->db->where_in('order_products.product_sku', $filter['sku_in']);



        $this->db->join('tbl_order_shipping', 'tbl_order_shipping.id = tbl_ndr.shipment_id');

        $this->db->join('orders', 'orders.id = tbl_order_shipping.order_id');
        $this->db->join('tbl_order_products', 'order_products.order_id = orders.id');
        $this->db->join('user_channels', 'user_channels.id = orders.channel_id');


        $this->db->order_by('tbl_ndr.id', 'desc');


        $q = $this->db->get($this->table);


        return $q->row()->total;
    }

    function getNDRforDailer($filter = array())
    {

        if (empty($filter))
            return false;

        $this->db->select(
            'tbl_ndr.*'
        );

        $this->db->where_not_in('tbl_order_shipping.ship_status', $this->config->item('shipment_closed_status'));


        $this->db->where('tbl_ndr.last_action_by', 'courier');
        $this->db->where('tbl_ndr.caller_id', '0');

        $this->db->limit(1);

        if (!empty($filter['seller_ids']))
            $this->db->where_in('tbl_ndr.user_id', $filter['seller_ids']);

        if (!empty($filter['sku_in']))
            $this->db->where_in('order_products.product_sku', $filter['sku_in']);

        $this->db->where('tbl_order_shipping.ship_status !=', 'cancelled');

        $this->db->order_by('tbl_ndr.id', 'asc');

        $this->db->join('tbl_order_shipping', 'tbl_order_shipping.id = tbl_ndr.shipment_id');
        $this->db->join('orders', 'orders.id = tbl_order_shipping.order_id');
        $this->db->join('tbl_order_products', 'order_products.order_id = orders.id');



        $q = $this->db->get($this->table);


        return $q->row();
    }

    function assignCaller($ndr_id = false, $caller_id = false)
    {
        if (!$ndr_id || !$caller_id)
            return false;

        $this->db->where('id', $ndr_id);
        $this->db->set('caller_id', $caller_id);
        $this->db->update($this->table);
        return true;
    }

    function getByUserIDndrAPI($user_id = false, $limit = 50, $offset = 0, $filter = array())
    {
        if (!$user_id)
            return false;

        $this->db->select(
            'tbl_ndr.*,'
                . ' tbl_order_shipping.awb_number,'
                . ' tbl_order_shipping.ship_status,'
                . ' tbl_order_shipping.rto_status as rto_status'
        );

        $this->db->limit($limit);
        $this->db->offset($offset);

        $ship_status = array('rto', 'delivered');
        $action_source = array('courier');
      
        if (!empty($filter['awb_number'])) {
            $this->db->where_in('tbl_order_shipping.awb_number', $filter['awb_number']);
        }

        if (!empty($filter['last_action'])) {
            $this->db->where('tbl_ndr.last_action', $filter['last_action']);
        }

        $this->db->where_not_in('tbl_order_shipping.ship_status', $ship_status);
        $this->db->where_in('tbl_ndr.last_action_by', $action_source);
        $this->db->where('tbl_ndr.user_id', $user_id);
        $this->db->where('tbl_order_shipping.ship_status !=', 'cancelled');
        $this->db->group_by('tbl_order_shipping.id');
        $this->db->join('tbl_order_shipping', 'tbl_order_shipping.id = tbl_ndr.shipment_id');
        $this->db->order_by('tbl_ndr.id', 'desc');
        $q = $this->db->get($this->table);
        return $q->result();
    }

    function unactioned_ndrsheetforCourier($filter = array())
    {
        $this->slave = $this->load->database('slave', TRUE);

        $this->slave->select("
            tbl_order_shipping.courier_id,
            tbl_order_shipping.awb_number,
            tbl_ndr.last_action as ndr_action,
            tbl_ndr.latest_remarks as ndr_remarks, 
            tbl_ndr.last_action_by as ndr_source, 
            tbl_order_shipping.ship_status, 
            shipment_tracking.last_attempt_date,
            shipment_tracking.last_ndr_reason,
            shipment_tracking.total_ofd_attempts,
            shipment_tracking.delivery_attempt_count,
            orders.shipping_fname,
            orders.shipping_lname,
            orders.shipping_address,
            orders.shipping_address_2,
            orders.shipping_phone,
            orders.shipping_city,
            orders.shipping_state,
            orders.shipping_zip,
            orders.shipping_phone,
            users.ndr_action_type
        ");
        
        $this->slave->where_not_in('tbl_order_shipping.ship_status', $this->config->item('shipment_closed_status'));

        $this->slave->where('tbl_ndr.last_action_by', 'courier');

        if (!empty($filter['start_date'])) {
            $this->slave->where("tbl_ndr.last_event >= '" . strtotime('yesterday 00:00:00') . "'");
        }
        
        if (!empty($filter['end_date'])) {
            $this->slave->where("tbl_ndr.last_event <= '" . strtotime('yesterday 23:59:59') . "'");
        }

        if (!empty($filter['courier_ids'])) {
            $this->slave->where_in('tbl_order_shipping.courier_id', $filter['courier_ids']);
        }

        $this->slave->where('tbl_order_shipping.ship_status !=', 'cancelled');

        $this->slave->where('shipment_tracking.total_ofd_attempts <', '3');

        $this->slave->group_by('tbl_order_shipping.id');

        $this->slave->join('users', 'users.id = tbl_ndr.user_id');
        $this->slave->join('tbl_order_shipping', 'tbl_order_shipping.id = tbl_ndr.shipment_id');
        $this->slave->join('orders', 'orders.id = tbl_order_shipping.order_id', 'LEFT');
        $this->slave->join('shipment_tracking', 'shipment_tracking.shipment_id = tbl_order_shipping.id', 'LEFT');

        $this->slave->order_by('tbl_ndr.id', 'desc');

        $q = $this->slave->get($this->table);
        return $q->result();
    }

    function getUnPushNdr($user_id){
        $arr = array();
        $filter['start_date'] = strtotime("-30 days midnight");
        $filter['end_date'] = strtotime("tomorrow midnight") - 1;

        if (!$user_id)
            return false;

        $this->db->select('tbl_ndr.id');

        $action_source = false;
        $ship_status = false;
        $action_source = array('courier');
        $this->db->where_not_in('tbl_order_shipping.ship_status', $this->config->item('shipment_closed_status'));
        

        if ($action_source) {
            $this->db->where_in('tbl_ndr.last_action_by', $action_source);
        }

        if (!empty($filter['start_date'])) {
            $this->db->where("tbl_ndr.created >= '" . $filter['start_date'] . "'");
        }

        if (!empty($filter['end_date'])) {
            $this->db->where("tbl_ndr.created <= '" . $filter['end_date'] . "'");
        }

        $this->db->where('tbl_ndr.user_id', $user_id);
        $this->db->where('tbl_ndr.push_api_status', '0');
        $this->db->where('tbl_order_shipping.ship_status !=', 'cancelled');


        $this->db->join('tbl_order_shipping', 'tbl_order_shipping.id = tbl_ndr.shipment_id');
        $this->db->order_by('tbl_ndr.id', 'desc');
        $q = $this->db->get($this->table);
        $result = $q->result_array();
        if(!empty($result)){
        $arr = array_column($result,"id");
        }
        return $arr;
    }

    function getEdShipmentByID($ed_id = false)
    {
        if (!$ed_id)
            return false;

        $this->db->where('id', $ed_id);
        $q = $this->db->get($this->ed_shipment_table);

        return $q->row();
    }

    function getEdShipmentByAwbNumber($awb_number = false, $save = array())
    {
        if (empty($save) || empty($awb_number))
            return false;

        $this->db->set($save);
        $this->db->where('awb_number', $awb_number);
        $this->db->update($this->ed_shipment_table);
        return true;
    }
}