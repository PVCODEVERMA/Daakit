<?php

class Weight_model extends MY_model
{

    public function __construct()
    {
        parent::__construct();
        $this->shipping_table = 'order_shipping';

        $this->slave = $this->load->database('slave', TRUE);
    }

    function updateBulkWeightByAWB($save = array())
    {
        if (empty($save))
            return false;

        $this->db->update_batch($this->shipping_table, $save, 'awb_number');
        return true;
    }

    function getShipmentsByAWBBulk($awbs = array())
    {
        if (empty($awbs))
            return false;


        $this->db->select("id, awb_number, courier_billed_weight");
        $this->db->group_start();
        $sale_ids_chunk = array_chunk($awbs, 1000);
        foreach ($sale_ids_chunk as $awb_ids) {
            $this->db->or_where_in('awb_number', $awb_ids);
        }
        $this->db->group_end();

        $q = $this->db->get($this->shipping_table);
        return $q->result();
    }

    function getUploadHistorySellerWise($filter = array())
    {
        $this->db->select("
        count(*) as total_count,
        u.fname,
        u.lname,
        u.company_name,
        u.id as user_id,        
        ");

        if (!empty($filter['start_date'])) {
            $this->db->where("s.weight_upload_date >= '" . $filter['start_date'] . "'");
        }

        if (!empty($filter['end_date'])) {
            $this->db->where("s.weight_upload_date <= '" . $filter['end_date'] . "'");
        }


        $this->db->order_by('total_count', 'desc');
        $this->db->group_by('u.id');

        $this->db->where('CAST(s.courier_billed_weight AS SIGNED) > ', 500);
        $this->db->where('s.weight_applied_date <= ', 0);
        $this->db->where('CAST(s.courier_billed_weight AS SIGNED) > CAST(o.package_weight AS SIGNED) ');


        $this->db->join('users as u', 'u.id = s.user_id', 'LEFT');
        $this->db->join('orders as o', 'o.id = s.order_id', "LEFT");

        $this->db->from($this->shipping_table . " as s");
        $q = $this->db->get();

        return $q->result();
    }

    function getUploadedHistorySellerWise($filter = array())
    {
        $this->db->select("
        u.fname,
        u.lname,
        u.company_name,
        u.id as user_id,
        u.remittance_on_hold_amount,
        sum(1)  as total_shipments,
        sum(case when (s.pending_weight_charges > 0) then pending_weight_charges else 0 end) as pending_weight_charges,
        sum(case when (s.pending_weight_charges > 0 and s.ship_status='rto') then pending_weight_charges else 0 end) as pending_rto_weight_charges,
        sum(case when (s.extra_weight_charges > 0) then extra_weight_charges else 0 end) as extra_weight_charges,
        sum(case when (s.rto_extra_weight_charges > 0) then rto_extra_weight_charges else 0 end) as rto_extra_weight_charges,
        ");

        if (!empty($filter['start_date'])) {
            $this->db->where("s.weight_applied_date >= '" . $filter['start_date'] . "'");
        }

        if (!empty($filter['end_date'])) {
            $this->db->where("s.weight_applied_date <= '" . $filter['end_date'] . "'");
        }

        if (!empty($filter['seller_id'])) {
            $this->db->where('s.user_id', $filter['seller_id']);
        }

        if (!empty($filter['awb_no'])) {
            $this->db->where_in('s.awb_number', $filter['awb_no']);
        }

        if (!empty($filter['seller_id_in'])) {
            $this->db->where_in('s.user_id', $filter['seller_id_in']);
        }

        $this->db->order_by('pending_weight_charges', 'desc');
        $this->db->group_by('s.user_id');

        $this->db->where('(s.pending_weight_charges>0 or s.extra_weight_charges > 0)');


        $this->db->join('users as u', 'u.id = s.user_id', 'LEFT');

        $this->db->from($this->shipping_table . " as s");
        $q = $this->db->get();

        return $q->result();
    }


    function getWeightUploadHistory($limit = 50, $offset = 0, $filter = array())
    {
        $this->db->select("
        s.id as shipment_id,
        s.awb_number,
        s.weight_upload_date,
        s.courier_billed_weight,
        s.courier_actual_weight,
        s.courier_volumetric_weight,
        s.courier_length,
        s.courier_height,
        s.courier_breadth,
        s.ship_status,
        s.calculated_weight,
        s.created,
        o.order_id,
        o.package_weight,
        o.package_length,
        o.package_height,
        o.package_breadth,
        u.fname,
        u.lname,
        u.company_name,
        u.id as user_id,
        c.name as courier_name,
        group_concat(p.product_name) as product_name,
        ");

        if (!empty($filter['start_date'])) {
            $this->db->where("s.weight_upload_date >= '" . $filter['start_date'] . "'");
        }

        if (!empty($filter['end_date'])) {
            $this->db->where("s.weight_upload_date <= '" . $filter['end_date'] . "'");
        }

        if (!empty($filter['seller_id'])) {
            $this->db->where('s.user_id', $filter['seller_id']);
        }

        if (!empty($filter['seller_id_in'])) {
            $this->db->where_in('s.user_id', $filter['seller_id_in']);
        }


        $this->db->limit($limit);
        $this->db->offset($offset);

        $this->db->order_by('s.weight_upload_date', 'desc');
        $this->db->group_by('s.id');

        $this->db->where('CAST(s.courier_billed_weight AS SIGNED) > ', 500);
        $this->db->where('CAST(s.courier_billed_weight AS SIGNED) > CAST(o.package_weight AS SIGNED)');
        $this->db->where('s.weight_applied_date <= ', 0);
        $this->db->join('orders as o', 'o.id = s.order_id', "LEFT");
        $this->db->join('order_products as p', 'p.order_id = o.id', 'LEFT');
        $this->db->join('users as u', 'u.id = s.user_id', 'LEFT');
        $this->db->join('courier as c', 'c.id = s.courier_id', "LEFT");

        $this->db->from($this->shipping_table . " as s");


        $q = $this->db->get();
        return $q->result();
    }


    function exportWeightUploadHistory($limit = 50, $offset = 0, $filter = array())
    {
        $this->db->select("
        s.id as shipment_id,
        s.awb_number,
        s.weight_upload_date,
        s.courier_billed_weight,
        s.courier_actual_weight,
        s.courier_volumetric_weight,
        s.courier_length,
        s.courier_height,
        s.courier_breadth,
        s.ship_status,
        s.calculated_weight,
        s.created,
        o.order_id,
        o.package_weight,
        o.package_length,
        o.package_height,
        o.package_breadth,
        u.fname,
        u.lname,
        u.company_name,
        u.id as user_id,
        c.name as courier_name,
        group_concat(p.product_name) as product_name,
        ");

        if (!empty($filter['start_date'])) {
            $this->db->where("s.weight_upload_date >= '" . $filter['start_date'] . "'");
        }

        if (!empty($filter['end_date'])) {
            $this->db->where("s.weight_upload_date <= '" . $filter['end_date'] . "'");
        }

        if (!empty($filter['seller_id'])) {
            $this->db->where('s.user_id', $filter['seller_id']);
        }

        if (!empty($filter['seller_id_in'])) {
            $this->db->where_in('s.user_id', $filter['seller_id_in']);
        }


        $this->db->limit($limit);
        $this->db->offset($offset);

        $this->db->order_by('s.weight_upload_date', 'desc');
        $this->db->group_by('s.id');

        $this->db->where('CAST(s.courier_billed_weight AS SIGNED) > ', 500);
        $this->db->where('CAST(s.courier_billed_weight AS SIGNED) > CAST(o.package_weight AS SIGNED)');
        $this->db->where('s.charged_weight <= ', 0);
        $this->db->join('orders as o', 'o.id = s.order_id', "LEFT");
        $this->db->join('order_products as p', 'p.order_id = o.id', 'LEFT');
        $this->db->join('users as u', 'u.id = s.user_id', 'LEFT');
        $this->db->join('courier as c', 'c.id = s.courier_id', "LEFT");

        $this->db->from($this->shipping_table . " as s");

        return $query =   $this->db->get_compiled_select();
    }

    function countWeightUploadHistory($filter = array())
    {
        $this->db->select("count(*) as total");

        if (!empty($filter['start_date'])) {
            $this->db->where("s.weight_upload_date >= '" . $filter['start_date'] . "'");
        }

        if (!empty($filter['end_date'])) {
            $this->db->where("s.weight_upload_date <= '" . $filter['end_date'] . "'");
        }

        if (!empty($filter['seller_id'])) {
            $this->db->where('s.user_id', $filter['seller_id']);
        }

        $this->db->where('CAST(s.courier_billed_weight AS SIGNED) > ', 500);
        $this->db->where('s.charged_weight <= ', 0);

        $this->db->from($this->shipping_table . " as s");
        $q = $this->db->get();
        return $q->row()->total;
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

        $this->slave->where('CAST(courier_billed_weight AS SIGNED) <=', 0);

        $this->slave->group_by('shipment_date');
        $this->slave->order_by('shipment_date', 'desc');

        $q = $this->slave->get($this->shipping_table);


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

        $this->slave->where('CAST(courier_billed_weight AS SIGNED) <=', 0);

        $this->slave->group_by('courier.id');
        $this->slave->order_by('total_count', 'desc');

        $this->slave->join('courier', 'courier.id = order_shipping.courier_id', 'LEFT');

        $q = $this->slave->get($this->shipping_table);

        return $q->result();
    }


    function getWeightUploadedHistory($limit = 50, $offset = 0, $filter = array())
    {
        $this->db->select("
        s.id as shipment_id,
        s.awb_number,
        s.weight_upload_date,
        s.weight_applied_date,
        s.courier_billed_weight,
        s.courier_actual_weight,
        s.courier_volumetric_weight,
        s.courier_length,
        s.courier_height,
        s.courier_breadth,
        s.extra_weight_charges,
        s.rto_extra_weight_charges,
        s.pending_weight_charges,
        s.ship_status,
        s.calculated_weight,
        s.created,
        o.order_id,
        o.package_weight,
        o.package_length,
        o.package_height,
        o.package_breadth,
        u.fname,
        u.lname,
        u.company_name,
        u.id as user_id,
        c.name as courier_name,
        group_concat(p.product_name) as product_name,
        ");

        if (!empty($filter['start_date'])) {
            $this->db->where("s.weight_applied_date >= '" . $filter['start_date'] . "'");
        }

        if (!empty($filter['end_date'])) {
            $this->db->where("s.weight_applied_date <= '" . $filter['end_date'] . "'");
        }

        if (!empty($filter['seller_id'])) {
            $this->db->where('s.user_id', $filter['seller_id']);
        }

        if (!empty($filter['awb_no'])) {
            $this->db->where_in('s.awb_number', $filter['awb_no']);
        }

        if (!empty($filter['seller_id_in'])) {
            $this->db->where_in('s.user_id', $filter['seller_id_in']);
        }

        $this->db->limit($limit);
        $this->db->offset($offset);

        $this->db->order_by('s.weight_applied_date', 'desc');
        $this->db->group_by('s.id');

        $this->db->where('(s.extra_weight_charges > 0 OR s.pending_weight_charges > 0)');

        $this->db->join('orders as o', 'o.id = s.order_id', "LEFT");
        $this->db->join('order_products as p', 'p.order_id = o.id', 'LEFT');
        $this->db->join('users as u', 'u.id = s.user_id', 'LEFT');
        $this->db->join('courier as c', 'c.id = s.courier_id', "LEFT");

        $this->db->from($this->shipping_table . " as s");
        $q = $this->db->get();
        return $q->result();
    }

    function exportWeightUploadedHistory($limit = 50, $offset = 0, $filter = array())
    {
        $this->db->select("
        s.id as shipment_id,
        s.awb_number,
        s.weight_upload_date,
        s.weight_applied_date,
        s.courier_billed_weight,
        s.courier_actual_weight,
        s.courier_volumetric_weight,
        s.pending_weight_charges,
        s.courier_length,
        s.courier_height,
        s.courier_breadth,
        s.extra_weight_charges,
        s.rto_extra_weight_charges,
        s.ship_status,
        s.calculated_weight,
        s.charged_weight,
        s.created,
        o.order_id,
        o.package_weight,
        o.package_length,
        o.package_height,
        o.package_breadth,
        u.fname,
        u.lname,
        u.company_name,
        u.id as user_id,
        c.name as courier_name,
        group_concat(p.product_name) as product_name,
        ");

        if (!empty($filter['start_date'])) {
            $this->db->where("s.weight_applied_date >= '" . $filter['start_date'] . "'");
        }

        if (!empty($filter['end_date'])) {
            $this->db->where("s.weight_applied_date <= '" . $filter['end_date'] . "'");
        }

        if (!empty($filter['seller_id'])) {
            $this->db->where('s.user_id', $filter['seller_id']);
        }

        if (!empty($filter['awb_no'])) {
            $this->db->where_in('s.awb_number', $filter['awb_no']);
        }

        if (!empty($filter['seller_id_in'])) {
            $this->db->where_in('s.user_id', $filter['seller_id_in']);
        }

        $this->db->limit($limit);
        $this->db->offset($offset);

        $this->db->order_by('s.weight_applied_date', 'desc');
        $this->db->group_by('s.id');

        $this->db->where('(s.extra_weight_charges > 0 OR s.pending_weight_charges > 0)');

        $this->db->join('orders as o', 'o.id = s.order_id', "LEFT");
        $this->db->join('order_products as p', 'p.order_id = o.id', 'LEFT');
        $this->db->join('users as u', 'u.id = s.user_id', 'LEFT');
        $this->db->join('courier as c', 'c.id = s.courier_id', "LEFT");

        $this->db->from($this->shipping_table . " as s");
        return $query =   $this->db->get_compiled_select();
    }

    function countWeightUploadedHistory($filter = array())
    {
        $this->db->select("count(*) as total");

        if (!empty($filter['start_date'])) {
            $this->db->where("s.weight_applied_date >= '" . $filter['start_date'] . "'");
        }

        if (!empty($filter['end_date'])) {
            $this->db->where("s.weight_applied_date <= '" . $filter['end_date'] . "'");
        }

        if (!empty($filter['seller_id'])) {
            $this->db->where('s.user_id', $filter['seller_id']);
        }

        if (!empty($filter['awb_no'])) {
            $this->db->where_in('s.awb_number', $filter['awb_no']);
        }

        if (!empty($filter['seller_id_in'])) {
            $this->db->where_in('s.user_id', $filter['seller_id_in']);
        }

        $this->db->where('(s.extra_weight_charges > 0 OR s.pending_weight_charges > 0)');

        $this->db->from($this->shipping_table . " as s");
        $q = $this->db->get();
        return $q->row()->total;
    }

    function getProductWeightHistory($limit = 50, $offset = 0, $filter = array())
    {
        $this->slave->select(
            "
            p.product_name,
            u.company_name,
            u.fname,
            u.lname,
            u.id as seller_id,
            c.id as courier_id,
            c.name as courier_name,
            sum(case when (CAST(s.charged_weight AS SIGNED) <= 500) then 1 else 0 end) as slab_500,
            sum(case when ( CAST(s.charged_weight AS SIGNED) > 500 and CAST(s.charged_weight AS SIGNED) <= 1000 ) then 1 else 0 end) as slab_1000,
            sum(case when ( CAST(s.charged_weight AS SIGNED) > 1000 and CAST(s.charged_weight AS SIGNED) <= 1500 ) then 1 else 0 end) as slab_1500,
            sum(case when ( CAST(s.charged_weight AS SIGNED) > 1500 and CAST(s.charged_weight AS SIGNED) <= 2000 ) then 1 else 0 end) as slab_2000,
            sum(case when ( CAST(s.charged_weight AS SIGNED) > 2000 and CAST(s.charged_weight AS SIGNED) <= 4000 ) then 1 else 0 end) as slab_4000,
            sum(case when ( CAST(s.charged_weight AS SIGNED) > 4000 and CAST(s.charged_weight AS SIGNED) <= 5000 ) then 1 else 0 end) as slab_5000,
            sum(case when ( CAST(s.charged_weight AS SIGNED) > 5000 and CAST(s.charged_weight AS SIGNED) <= 10000 ) then 1 else 0 end) as slab_10000,
            sum(case when ( CAST(s.charged_weight AS SIGNED) > 10000) then 1 else 0 end) as slab_above_10000,
            "
        );

        if (!empty($filter['product'])) {
            $this->slave->like('p.product_name', $filter['product']);
        }

        if (!empty($filter['seller_id'])) {
            $this->slave->where('s.user_id', $filter['seller_id']);
        }

        if (!empty($filter['courier_id'])) {
            $this->slave->where('s.courier_id', $filter['courier_id']);
        }

        if (!empty($filter['start_date'])) {
            $this->slave->where("s.created >= '" . $filter['start_date'] . "'");
        }

        if (!empty($filter['end_date'])) {
            $this->slave->where("s.created <= '" . $filter['end_date'] . "'");
        }

        if (!empty($filter['comparison'])) {
            switch ($filter['comparison']) {
                case 'product':
                    $this->slave->group_by('p.product_name');
                    break;
                case 'seller':
                    $this->slave->group_by('s.user_id');
                    break;
                case 'courier':
                    $this->slave->group_by('s.courier_id');
                    break;
            }
        }

        $this->slave->where('CAST(s.charged_weight AS SIGNED) >', 0);

        $this->slave->limit($limit);
        $this->slave->offset($offset);

        $this->slave->join('orders as o', 'o.id = s.order_id', "LEFT");
        $this->slave->join('order_products as p', 'p.order_id = o.id', 'LEFT');
        $this->slave->join('users as u', 'u.id = s.user_id', 'LEFT');
        $this->slave->join('courier as c', 'c.id = s.courier_id', "LEFT");
        $this->slave->from($this->shipping_table . " as s");
        $q = $this->slave->get();
        return $q->result();
    }

    function countProductWeightHistory($filter = array())
    {

        switch ($filter['comparison']) {
            case 'product':
                $count = "count(DISTINCT p.product_name)";
                break;
            case 'seller':
                $count = "count(DISTINCT s.user_id)";
                break;
            case 'courier':
                $count = "count(DISTINCT s.courier_id)";
                break;
        }

        $this->slave->select($count . ' as total');

        if (!empty($filter['product'])) {
            $this->slave->like('p.product_name', $filter['product']);
        }

        if (!empty($filter['seller_id'])) {
            $this->slave->where('s.user_id', $filter['seller_id']);
        }

        if (!empty($filter['courier_id'])) {
            $this->slave->where('s.courier_id', $filter['courier_id']);
        }

        if (!empty($filter['start_date'])) {
            $this->slave->where("s.created >= '" . $filter['start_date'] . "'");
        }

        if (!empty($filter['end_date'])) {
            $this->slave->where("s.created <= '" . $filter['end_date'] . "'");
        }



        $this->slave->where('CAST(s.charged_weight AS SIGNED) >', 0);
        $this->slave->join('orders as o', 'o.id = s.order_id', "LEFT");
        $this->slave->join('order_products as p', 'p.order_id = o.id', 'LEFT');
        $this->slave->join('users as u', 'u.id = s.user_id', 'LEFT');
        $this->slave->join('courier as c', 'c.id = s.courier_id', "LEFT");
        $this->slave->from($this->shipping_table . " as s");
        $q = $this->slave->get();
        return $q->row()->total;
    }
}
