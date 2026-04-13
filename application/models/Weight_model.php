<?php

class Weight_model extends MY_model
{

    public function __construct()
    {
        parent::__construct();
        $this->table = 'weight_reco';
        $this->tracking ='weight_reco_tracking';
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





    function getByUserID($user_id = false, $limit = 50, $offset = 0, $filter = array())
    {
        if (!$user_id)
            return false;

        $this->db->select("
        tbl_weight_reco.*,
        s.awb_number,
        s.ship_status,        
        s.extra_weight_charges,
        s.rto_extra_weight_charges,
        o.order_no,
        o.id as o_id,
        
        o.package_volumatic_weight,
        c.name as courier_name,
        
        group_concat(p.product_name,' (',p.product_sku,')') as product_name,
        group_concat(p.product_sku) as product_sku,
        ");

        if (!empty($filter['start_date'])) {
            $this->db->where("tbl_weight_reco.apply_weight_date >= '" . $filter['start_date'] . "'");
        }

        if (!empty($filter['end_date'])) {
            $this->db->where("tbl_weight_reco.apply_weight_date <= '" . $filter['end_date'] . "'");
        }

        if (!empty($filter['awb_no'])) {
            $this->db->where_in('s.awb_number', $filter['awb_no']);
        }

        if (!empty($filter['product_name'])) {
            $this->db->like('p.product_name', $filter['product_name']);
        }

        if (!empty($filter['courier_id'])) {
            $this->db->where('s.courier_id', $filter['courier_id']);
        }

        if (!empty($filter['status'])) {
            $status = $filter['status'];
            if ($status == 'accepted')
                $this->db->where_in('seller_action_status', array('accepted', 'auto accepted'));
            else
                $this->db->where('seller_action_status', $filter['status']);
        }

        $this->db->where('tbl_weight_reco.user_id', $user_id);

        $this->db->where('tbl_weight_reco.weight_applied', '1');
        $this->db->where('tbl_weight_reco.seller_action_status  !=', 'no dispute');

        $this->db->limit($limit);
        $this->db->offset($offset);

        $this->db->order_by('tbl_weight_reco.id', 'desc');
        $this->db->group_by('tbl_weight_reco.shipment_id');


        $this->db->join('order_shipping as s', 's.id = tbl_weight_reco.shipment_id', "LEFT");
        $this->db->join('orders as o', 'o.id = s.order_id', "LEFT");
        $this->db->join('order_products as p', 'p.order_id = o.id', 'LEFT');
        $this->db->join('courier as c', 'c.id = s.courier_id', "LEFT");

        $this->db->from($this->table);
        $q = $this->db->get();
       // echo $this->db->last_query();
        //exit;
        return $q->result();
    }

    function countByUserID($user_id = false, $filter = array())
    {
        if (!$user_id)
            return false;

        $this->db->select("count(DISTINCT s.awb_number) as total");

        if (!empty($filter['start_date'])) {
            $this->db->where("tbl_weight_reco.apply_weight_date >= '" . $filter['start_date'] . "'");
        }

        if (!empty($filter['end_date'])) {
            $this->db->where("tbl_weight_reco.apply_weight_date <= '" . $filter['end_date'] . "'");
        }

        if (!empty($filter['awb_no'])) {
            $this->db->where_in('s.awb_number', $filter['awb_no']);
        }

        if (!empty($filter['product_name'])) {
            $this->db->like('p.product_name', $filter['product_name']);
        }

        if (!empty($filter['courier_id'])) {
            $this->db->where('s.courier_id', $filter['courier_id']);
        }

        if (!empty($filter['status'])) {
            $status = $filter['status'];
            if ($status == 'accepted')
                $this->db->where_in('seller_action_status', array('accepted', 'auto accepted'));
            else
                $this->db->where('seller_action_status', $filter['status']);
        }

        $this->db->where('tbl_weight_reco.user_id', $user_id);

        $this->db->where('tbl_weight_reco.weight_applied', '1');
        $this->db->where('tbl_weight_reco.seller_action_status  !=', 'no dispute');

        $this->db->order_by('tbl_weight_reco.id', 'desc');

        $this->db->join('order_shipping as s', 's.id = tbl_weight_reco.shipment_id', "LEFT");
        $this->db->join('orders as o', 'o.id = s.order_id', "LEFT");
        $this->db->join('order_products as p', 'p.order_id = o.id', 'LEFT');
        $this->db->join('courier as c', 'c.id = s.courier_id', "LEFT");

        $this->db->from($this->table);

        $q = $this->db->get();
        return $q->row()->total;
    }



    function exportByUserID($user_id = false, $limit = 50, $offset = 0, $filter = array())
    {
        if (!$user_id)
            return false;

        $this->db->select("
        tbl_weight_reco.*,
        s.awb_number,
        s.ship_status,        
        s.extra_weight_charges,
        s.rto_extra_weight_charges,
        o.order_no,
        c.name as courier_name,
        group_concat(p.product_name) as product_name,
        group_concat(p.product_qty ) as product_qty,
        group_concat(p.product_sku) as product_sku,
        ");

        if (!empty($filter['start_date'])) {
            $this->db->where("tbl_weight_reco.apply_weight_date >= '" . $filter['start_date'] . "'");
        }

        if (!empty($filter['end_date'])) {
            $this->db->where("tbl_weight_reco.apply_weight_date <= '" . $filter['end_date'] . "'");
        }

        if (!empty($filter['awb_no'])) {
            $this->db->where_in('s.awb_number', $filter['awb_no']);
        }

        if (!empty($filter['product_name'])) {
            $this->db->like('p.product_name', $filter['product_name']);
        }

        if (!empty($filter['courier_id'])) {
            $this->db->where('s.courier_id', $filter['courier_id']);
        }

        if (!empty($filter['status'])) {
            $status = $filter['status'];
            if ($status == 'accepted')
                $this->db->where_in('seller_action_status', array('accepted', 'auto accepted'));
            else
                $this->db->where('seller_action_status', $filter['status']);
        }

        $this->db->where('tbl_weight_reco.user_id', $user_id);

        $this->db->where('tbl_weight_reco.weight_applied', '1');
        $this->db->where('tbl_weight_reco.seller_action_status  !=', 'no dispute');

        $this->db->limit($limit);
        $this->db->offset($offset);

        $this->db->order_by('tbl_weight_reco.id', 'desc');
        $this->db->group_by('tbl_weight_reco.shipment_id');


        $this->db->join('order_shipping as s', 's.id = tbl_weight_reco.shipment_id', "LEFT");
        $this->db->join('orders as o', 'o.id = s.order_id', "LEFT");
        $this->db->join('order_products as p', 'p.order_id = o.id', 'LEFT');
        $this->db->join('courier as c', 'c.id = s.courier_id', "LEFT");

        $this->db->from($this->table);

        //echo $this->db->last_query();
        //exit;
        return $query =   $this->db->get_compiled_select();
    }

    function getRecordsForAutoAcceptWeight()
    {
       
        $this->db->select(""
        . "wr.id as id,"
       );
            $this->db->where('wr.seller_action_status', 'open');
            $this->db->where('wr.weight_applied', '1');
            $this->db->where('wr.created>',strtotime('-6 months'));
            $this->db->where('wr.apply_weight_date <= UNIX_TIMESTAMP() - 24*60*60*(case when wd.time_limt !="" then wd.time_limt else 7 END)');
            $this->db->join('weight_dispute_time_limit as wd', 'wd.user_id = wr.user_id', "LEFT");
            $this->db->from($this->table . " as wr");
            $this->db->limit("500");
            $this->db->order_by("wr.created", "desc");
            $q = $this->db->get();
            return $q->result();
           
    }

    function countOpenWeighDisputes($user_id = false)
    {
        if (!$user_id)
            return false;


        $this->db->select('count(*) as total');
        $this->db->where('seller_action_status', 'open');

        $this->db->where('user_id', $user_id);


        $this->db->where('weight_applied', '1');

        $this->db->from($this->table);
        $q = $this->db->get();
        return $q->row()->total;
    }

    function getRecordsFordayWise($day = 1)
    {
        if($day  < 0 || $day  > 7)
            return false;
        $day = 60 * 60 * 24 * $day;
        $this->db->select("
        tbl_weight_reco.*,
        count('tbl_weight_reco.*') as awb_count,
        s.awb_number,
        s.ship_status,        
        s.extra_weight_charges,
        s.rto_extra_weight_charges,
        o.order_no,
        c.display_name,
        u.fname,u.lname,u.phone as user_phone
        ");
        $this->db->where('tbl_weight_reco.seller_action_status', 'open');

        $close_upto_date = date('Y-m-d', time() - $day);

        $this->db->where('tbl_weight_reco.weight_applied', '1');
        $this->db->where('tbl_weight_reco.apply_weight_date >=', strtotime($close_upto_date . ' 00:00:00'));
        $this->db->where('tbl_weight_reco.apply_weight_date <=', strtotime($close_upto_date . ' 23:59:59'));

        //////////////
       // $this->db->where('tbl_weight_reco.user_id', $user_id);

        // $this->db->where('tbl_weight_reco.weight_applied', '1');
       // $this->db->where('tbl_weight_reco.seller_action_status  !=', 'no dispute');


        $this->db->order_by('tbl_weight_reco.id', 'desc');
        $this->db->group_by('tbl_weight_reco.user_id');

        $this->db->join('order_shipping as s', 's.id = tbl_weight_reco.shipment_id', "LEFT");
        $this->db->join('orders as o', 'o.id = s.order_id', "LEFT");
        $this->db->join('courier as c', 'c.id = s.courier_id', "LEFT");
        $this->db->join('users as u', 'u.id = tbl_weight_reco.user_id', "LEFT");

        $this->db->from($this->table);
        $q = $this->db->get();
        return $q->result();
    }

    function createSMSTracking($save = array())
    {
        
        if (empty($save))
            return false;
        $save['created'] = time();
        $save['modified'] = time();
        $this->db->insert($this->tracking, $save);
        return $this->db->insert_id();
    }

    function getSMSTrackingByRecoID($user_id = false, $day =1)
    {
        if(empty($user_id))
            return false;
        $this->db->select('*');
        $this->db->where('user_id', $user_id);
        $this->db->where('day', $day);
        $this->db->where('created >=', strtotime(date('Y-m-d').' 00:00:00'));
        $this->db->where('created <', strtotime(date('Y-m-d').' 23:59:59'));
        $this->db->from($this->tracking);
        $q = $this->db->get();
        return $q->row();
    }

    function get_dispute_time_limit($user_id = false)
    {
        if(empty($user_id))
            return false;
        $this->db->select('*');
        $this->db->where('user_id', $user_id);
        $this->db->from('weight_dispute_time_limit');
        $q = $this->db->get();
        return $q->row();
    }
}
