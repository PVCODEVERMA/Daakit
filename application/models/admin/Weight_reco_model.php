<?php

class Weight_reco_model extends MY_model
{

    public function __construct()
    {
        parent::__construct();
        $this->table = 'weight_reco';
        $this->slave = $this->load->database('slave', TRUE);
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
        return  true;
    }

    function getByShipmentID($id = false)
    {
        if (!$id)
            return false;

        $this->db->where('shipment_id', $id);
        $this->db->limit(1);
        $q = $this->db->get($this->table);
        return $q->row();
    }

    function getRecords($limit = 50, $offset = 0, $filter = array())
    {

        $this->db->select(
            "weight_reco.shipment_id,weight_reco.id,
            weight_reco.user_id,weight_reco.courier_billed_weight,
            weight_reco.courier_vol_weight,weight_reco.courier_length,
            weight_reco.courier_breadth,weight_reco.courier_height,
            weight_reco.upload_remarks,weight_reco.seller_dead_weight,
            weight_reco.seller_volumetric_weight,
            weight_reco.seller_booking_weight,
            weight_reco.seller_package_length,
            weight_reco.seller_package_breadth,
            weight_reco.seller_package_height,
            weight_reco.upload_weight_difference,
            weight_reco.weight_difference_charges,
            weight_reco.weight_new_slab,weight_reco.weight_charges_reverted,
            weight_reco.weight_applied,weight_reco.upload_date,weight_reco.apply_weight_date,
            weight_reco.apply_weight_remarks,weight_reco.applied_to_wallet,
            weight_reco.applied_to_wallet_date,weight_reco.seller_action_status,
            weight_reco.dispute_closure_favour,weight_reco.is_cn_issued,weight_reco.dispute_id,
            weight_reco.created,
            weight_reco.modified,weight_reco.ivr_calling_status`,
            s.awb_number,
            s.ship_status,
            s.created as shipment_created,
            u.id as user_id,
            u.fname,
            u.lname,
            u.company_name,
            c.name as courier_name,
            c.volumetric_divisor,
            am.fname as account_manager_fname,
            am.lname as account_manager_lname
            "
        );

        if (!empty($filter['start_date'])) {
            $this->db->where("weight_reco.upload_date >= " . $filter['start_date'] . "");
        }

        if (!empty($filter['end_date'])) {
            $this->db->where("weight_reco.upload_date <= " . $filter['end_date'] . "");
        }

        if (!empty($filter['seller_id'])) {
            $this->db->where('weight_reco.user_id', $filter['seller_id']);
        }

        if (!empty($filter['seller_id_in'])) {
            $this->db->where_in('weight_reco.user_id', $filter['seller_id_in']);
        }
        if (!empty($filter['manager_id_in'])) {
            $this->db->where_in('u.account_manager_id', $filter['manager_id_in']);
        }

        if (!empty($filter['awb_no'])) {
            $this->db->where_in('s.awb_number', $filter['awb_no']);
        }
        if (!empty($filter['courier_id'])) {
            $this->db->where('s.courier_id', $filter['courier_id']);
        }

        if (!empty($filter['status'])) {
            $this->db->where('weight_reco.seller_action_status', $filter['status']);
        }

        $this->db->limit($limit);
        $this->db->offset($offset);

        $this->db->order_by('weight_reco.id', 'desc');

        $this->db->join('order_shipping as s', 's.id = weight_reco.shipment_id', "LEFT");
        $this->db->join('users as u', 'u.id = s.user_id', 'LEFT');
        $this->db->join('courier as c', 'c.id = s.courier_id', 'LEFT');
        $this->db->join('users as am', 'am.id=u.account_manager_id', 'LEFT');

        $this->db->from('tbl_weight_reco FORCE INDEX (by_upload_date_user_id)');

        $q = $this->db->get();
        return $q->result();
    }

    function countRecords($filter = array())
    {

        $this->db->select("count(*) as total");

        if (!empty($filter['start_date'])) {
            $this->db->where("upload_date >= " . $filter['start_date'] . "");
        }

        if (!empty($filter['end_date'])) {
            $this->db->where("upload_date <= " . $filter['end_date'] . "");
        }

        if (!empty($filter['seller_id'])) {
            $this->db->where('user_id', $filter['seller_id']);
        }

        if (!empty($filter['seller_id_in'])) {
            $this->db->where_in('user_id', $filter['seller_id_in']);
        }

        if (!empty($filter['seller_id_in'])) {
            $this->db->where_in('user_id', $filter['seller_id_in']);
        }
        if (!empty($filter['manager_id_in'])) {
            $this->db->where_in('u.account_manager_id', $filter['manager_id_in']);
        }


        if (!empty($filter['awb_no'])) {
            $this->db->where_in('s.awb_number', $filter['awb_no']);
        }

        if (!empty($filter['courier_id'])) {
            $this->db->where('s.courier_id', $filter['courier_id']);
        }

        if (!empty($filter['status'])) {
            $this->db->where('weight_reco.seller_action_status', $filter['status']);
        }

        $this->db->order_by('weight_reco.id', 'desc');

        $this->db->join('order_shipping as s', 's.id = weight_reco.shipment_id', "LEFT");
        $this->db->join('users as u', 'u.id = weight_reco.user_id', "LEFT");
        $this->db->join('users as am', 'am.id=u.account_manager_id', 'LEFT');
        $this->db->from('tbl_weight_reco  FORCE INDEX (by_upload_date_user_id)');

        $q = $this->db->get();
        return $q->row()->total;
    }

    function exportRecords($limit = 50, $offset = 0, $filter = array())
    {
        


        $this->db->select(
            "tbl_weight_reco.shipment_id,tbl_weight_reco.id,
            tbl_weight_reco.user_id,tbl_weight_reco.courier_billed_weight,
            tbl_weight_reco.courier_vol_weight,tbl_weight_reco.courier_length,
            tbl_weight_reco.courier_breadth,tbl_weight_reco.courier_height,
            tbl_weight_reco.upload_remarks,tbl_weight_reco.seller_dead_weight,
            tbl_weight_reco.seller_volumetric_weight,
            tbl_weight_reco.seller_booking_weight,
            tbl_weight_reco.seller_package_length,
            tbl_weight_reco.seller_package_breadth,
            tbl_weight_reco.seller_package_height,
            tbl_weight_reco.upload_weight_difference,
            tbl_weight_reco.weight_difference_charges,
            tbl_weight_reco.weight_new_slab,tbl_weight_reco.weight_charges_reverted,
            tbl_weight_reco.weight_applied,tbl_weight_reco.upload_date,tbl_weight_reco.apply_weight_date,
            tbl_weight_reco.apply_weight_remarks,tbl_weight_reco.applied_to_wallet,
            tbl_weight_reco.applied_to_wallet_date,tbl_weight_reco.seller_action_status,
            tbl_weight_reco.dispute_closure_favour,tbl_weight_reco.is_cn_issued,tbl_weight_reco.dispute_id,
            tbl_weight_reco.created,
            tbl_weight_reco.modified,tbl_weight_reco.ivr_calling_status`,
            s.awb_number,
            s.ship_status,
            s.created as shipment_created,
            u.id as user_id,
            u.fname,
            u.lname,
            u.company_name,
            c.name as courier_name,
            c.display_name as courier_display_name,
            c.volumetric_divisor,
            group_concat(p.product_name) as product_name,
            group_concat(p.product_sku SEPARATOR'/ ') as product_sku,
            group_concat(p.product_qty ) as product_qty,
            group_concat(p.product_price) as product_price,
            o.order_amount as order_amount,
            o.order_payment_type as order_payment_type,
            am.fname as account_manager_fname,
            am.lname as account_manager_lname,
            esc.created as escalation_creation_date ,
            wad.id as weight_applied_id ,
            wad.length as weight_applied_length,
            wad.height as weight_applied_height ,
            wad.breadth as weight_applied_breadth,
            wad.weight as weight_applied_weight
            "
        );

        if (!empty($filter['start_date'])) {
            $this->db->where("tbl_weight_reco.upload_date >= " . $filter['start_date'] . "");
        }

        if (!empty($filter['end_date'])) {
            $this->db->where("tbl_weight_reco.upload_date <= " . $filter['end_date'] . "");
        }

        if (!empty($filter['seller_id'])) {
            $this->db->where('tbl_weight_reco.user_id', $filter['seller_id']);
        }

        if (!empty($filter['seller_id_in'])) {
            $this->db->where_in('tbl_weight_reco.user_id', $filter['seller_id_in']);
        }
        if (!empty($filter['manager_id_in'])) {
            $this->db->where_in('u.account_manager_id', $filter['manager_id_in']);
        }

        if (!empty($filter['awb_no'])) {
            $this->db->where_in('s.awb_number', $filter['awb_no']);
        }
        if (!empty($filter['courier_id'])) {
            $this->db->where('s.courier_id', $filter['courier_id']);
        }

        if (!empty($filter['status'])) {
            $this->db->where('tbl_weight_reco.seller_action_status', $filter['status']);
        }
        
        $this->db->limit($limit);
        $this->db->offset($offset);

        $this->db->order_by('tbl_weight_reco.id', 'desc');

        $this->db->group_by('tbl_weight_reco.shipment_id');

        $this->db->join('order_shipping as s', 's.id = tbl_weight_reco.shipment_id', "LEFT");
        $this->db->join('orders as o', 'o.id = s.order_id', "LEFT");
        $this->db->join('order_products as p', 'p.order_id = o.id', 'LEFT');
        $this->db->join('users as u', 'u.id = s.user_id', 'LEFT');
        $this->db->join('courier as c', 'c.id = s.courier_id', 'LEFT');
        $this->db->join('users as am', 'am.id=u.account_manager_id', 'LEFT');
        $this->db->join('escalations as esc', 'esc.id = tbl_weight_reco.dispute_id', 'LEFT');
        $this->db->join('weight_applied_details as wad', 'wad.shipment_id = tbl_weight_reco.shipment_id', 'LEFT');
        
        $this->db->from('tbl_weight_reco FORCE INDEX (by_upload_date_user_id)');


        // echo $this->db->last_query();
        // exit;
        return $query =   $this->db->get_compiled_select();
    }


    function getUserPermission($user_id){

        $query = "SELECT `admin_permissions` FROM `users` WHERE id = ".$user_id;
        $this->db->select("admin_permissions");
        $this->db->where('id',$user_id);
        $q = $this->db->get('users');
        return $q->row();

    }

    function monthWisePendingWeightCount($filter = array())
    {
        $this->slave->select(
            "DATE_FORMAT(FROM_UNIXTIME(order_shipping.created), '%Y-%m') as shipment_date,"
                . " count(*) as total_count"
        );

        if (!empty($filter['start_date'])) {
            $this->slave->where("order_shipping.created >= '" . $filter['start_date'] . "'");
        }

        if (!empty($filter['end_date'])) {
            $this->slave->where("order_shipping.created <= '" . $filter['end_date'] . "'");
        }

        $this->slave->where_not_in('order_shipping.ship_status', array('booked', 'new', 'cancelled', 'pending pickup'));


        $this->slave->where('w.shipment_id is NULL');

        $this->slave->group_by('shipment_date');
        $this->slave->order_by('shipment_date', 'desc');

        $this->slave->join('weight_reco as w', 'w.shipment_id = order_shipping.id', "LEFT");

        $q = $this->slave->get('order_shipping');


        return $q->result();
    }


    function courierPendingWeightCount($filter = array())
    {
        $this->slave->select(
            "courier.id as courier_id, "
                . " courier.name as courier_name, "
                . " count(*) as total_count"
        );

        if (!empty($filter['start_date'])) {
            $this->slave->where("order_shipping.created >= '" . $filter['start_date'] . "'");
        }

        if (!empty($filter['end_date'])) {
            $this->slave->where("order_shipping.created <= '" . $filter['end_date'] . "'");
        }

        $this->slave->where_not_in('order_shipping.ship_status', array('booked', 'new', 'cancelled', 'pending pickup'));

        $this->slave->group_by('courier.id');
        $this->slave->order_by('total_count', 'desc');

        $this->slave->where('w.shipment_id is NULL');

        $this->slave->join('weight_reco as w', 'w.shipment_id = order_shipping.id', "LEFT");

        $this->slave->join('courier', 'courier.id = order_shipping.courier_id', 'LEFT');

        $q = $this->slave->get('order_shipping');

        return $q->result();
    }

    function exportPendingWeight($filter = array())
    {
        $this->slave->select(
            "
            c.id as courier_id,
            c.name as courier_name,
            c.display_name as courier_display_name,
            s.awb_number,
            s.created,
            track.weight,

             "

        );

        if (!empty($filter['start_date'])) {
            $this->slave->where("s.created >= '" . $filter['start_date'] . "'");
        }

        if (!empty($filter['end_date'])) {
            $this->slave->where("s.created <= '" . $filter['end_date'] . "'");
        }
        if (!empty($filter['courier_id'])) {
            $this->slave->where("s.courier_id", $filter['courier_id']);
        }

        $this->slave->where_not_in('s.ship_status', array('booked', 'new', 'cancelled', 'pending pickup'));


        $this->slave->where('w.shipment_id is NULL');


        $this->slave->order_by('s.id', 'desc');

        $this->slave->join('weight_reco as w', 'w.shipment_id = s.id', "LEFT");
        $this->slave->join('courier as c', 'c.id = s.courier_id', 'LEFT');
        $this->slave->join('shipment_tracking as track', 'track.shipment_id = s.id', 'LEFT');


        $this->slave->from('order_shipping as s');


        return $query =   $this->slave->get_compiled_select();
    }
}
