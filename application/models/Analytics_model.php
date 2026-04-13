<?php

class Analytics_model extends MY_model
{

    public function __construct()
    {
        parent::__construct();
    }

    function countShipmentsAppliedWeight($user_id = false)
    {
        if (!$user_id)
            return false;

        $this->db->select("count(DISTINCT s.id) as total");

        $this->db->where('s.weight_applied_date > ', strtotime("- 6 days midnight"));
        $this->db->where('s.weight_dispute_accepted', '0');
        $this->db->where('s.weight_dispute_raised', '0');
        $this->db->where('(s.extra_weight_charges > 0 || s.pending_weight_charges > 0)', NULL, FALSE);

        $this->db->where('s.user_id', $user_id);

        $this->db->from(" tbl_order_shipping as s");
        $q = $this->db->get();
        return $q->row()->total;
    }

    function userShipmentStats($user_id = false, $start_date = false, $end_date = false)
    {
        if (!$user_id)
            return false;

        if (!$start_date)
            $start_date = strtotime("yesterday");

        if (!$end_date)
            $end_date = time();

        $this->db->select(""
            . "sum(case when (tbl_order_shipping.ship_status = 'delivered') then 1 else 0 end) as delivered,"
            . "sum(case when (tbl_order_shipping.ship_status = 'delivered' and tbl_orders.order_payment_type!='reverse') then tbl_order_shipping.order_total_amount else 0 end) as revenue,"
            . "sum(case when (tbl_order_shipping.ship_status = 'rto') then 1 else 0 end) as rto,"
            . "sum(case when (tbl_order_shipping.ship_status in ('in transit','out for delivery','delivered','exception','rto')) then 1 else 0 end) as total_delivered_shipments,"
            . "sum(1) as total_shipments,"
            . "sum(case when (tbl_orders.order_payment_type = 'COD') then 1 else 0 end) as cod_shipments,"
            . "sum(case when (tbl_orders.order_payment_type = 'Prepaid') then 1 else 0 end) as prepaid_shipments,");

        $this->db->where('tbl_order_shipping.created >= ', $start_date);
        $this->db->where('tbl_order_shipping.ship_status != ', 'cancelled');
        $this->db->where('tbl_order_shipping.created <= ', $end_date);
        $this->db->where('tbl_order_shipping.user_id', $user_id);
        $this->db->join('tbl_orders', 'tbl_orders.id = tbl_order_shipping.order_id', 'LEFT');

        $q = $this->db->get('tbl_order_shipping');
        return $q->row();
    }

    function courierWiseMonthlyStatusCounts($user_id, $start_date, $end_date)
    {
        if (!$user_id) return [];

        $this->db->select("
            c.display_name,

            SUM(CASE WHEN os.ship_status = 'pending pickup' THEN 1 ELSE 0 END) AS pending_pickup,
            SUM(CASE WHEN os.ship_status = 'booked' THEN 1 ELSE 0 END) AS booked,
            SUM(CASE WHEN os.ship_status = 'out for delivery' THEN 1 ELSE 0 END) AS out_for_delivery,
            SUM(CASE WHEN os.ship_status = 'delivered' THEN 1 ELSE 0 END) AS delivered,
            SUM(CASE WHEN os.ship_status = 'in transit' THEN 1 ELSE 0 END) AS in_transit,
            SUM(CASE WHEN os.ship_status = 'rto' THEN 1 ELSE 0 END) AS rto
        ");

        $this->db->from('tbl_order_shipping os');
        $this->db->join('tbl_courier c', 'c.id = os.courier_id', 'LEFT');

        $this->db->where('os.user_id', $user_id);
        $this->db->where('os.created >=', $start_date);
        $this->db->where('os.created <=', $end_date);

        $this->db->where_in('os.ship_status', [
            'pending pickup',
            'booked',
            'out for delivery',
            'delivered',
            'in transit',
            'rto'
        ]);

        $this->db->group_by('os.courier_id');
        $this->db->order_by('c.display_name', 'ASC');

        return $this->db->get()->result();
    }

    function topDestinations($user_id = false, $start_date = false, $end_date = false)
    {
        if (!$user_id)
            return false;

        if (!$start_date)
            $start_date = strtotime("yesterday");

        if (!$end_date)
            $end_date = time();

        $this->db->select("count(DISTINCT tbl_order_shipping.id) as total, tbl_orders.shipping_city as city");

        $this->db->where('tbl_order_shipping.created >= ', $start_date);
        $this->db->where('tbl_order_shipping.created <= ', $end_date);
        $this->db->where('tbl_order_shipping.user_id', $user_id);
        $this->db->where('tbl_order_shipping.ship_status != ', 'cancelled');

        $this->db->group_by('tbl_orders.shipping_city');

        $this->db->order_by('total', 'desc');
        $this->db->limit(15);

        $this->db->join('tbl_orders', 'tbl_orders.id = tbl_order_shipping.order_id', 'LEFT');

        $q = $this->db->get('tbl_order_shipping');
        return $q->result();
    }

    function courierWiseStatusDistribution($user_id = false, $start_date = false, $end_date = false)
    {
        if (!$user_id)
            return false;

        if (!$start_date)
            $start_date = strtotime("yesterday");

        if (!$end_date)
            $end_date = time();

        $this->db->select(""
            . "sum(case when (tbl_order_shipping.ship_status = 'booked') then 1 else 0 end) as booked,"
            . "sum(case when (tbl_order_shipping.ship_status = 'pending pickup') then 1 else 0 end) as pending_pickup,"
            . "sum(case when (tbl_order_shipping.ship_status = 'in transit' || tbl_order_shipping.ship_status = 'out for delivery' || tbl_order_shipping.ship_status = 'exception') then 1 else 0 end) as in_transit,"
            . "sum(case when (tbl_order_shipping.ship_status = 'delivered') then 1 else 0 end) as delivered,"
            . "sum(case when (tbl_order_shipping.ship_status = 'rto') then 1 else 0 end) as rto,"
            . "sum(1) as total,"
            . "tbl_order_shipping.courier_id as courier_id,");



        $this->db->where('tbl_order_shipping.created >= ', $start_date);
        $this->db->where('tbl_order_shipping.created <= ', $end_date);
        $this->db->where('tbl_order_shipping.ship_status != ', 'cancelled');
        $this->db->where('tbl_order_shipping.user_id', $user_id);

        $this->db->group_by('tbl_order_shipping.courier_id');

        $this->db->order_by('total', 'desc');


        $this->db->join('tbl_orders', 'tbl_orders.id = tbl_order_shipping.order_id', 'LEFT');

        $q = $this->db->get('tbl_order_shipping');
        return $q->result();
    }

    function productWiseStatusDistribution($user_id = false, $start_date = false, $end_date = false)
    {
        if (!$user_id)
            return false;

        if (!$start_date)
            $start_date = strtotime("yesterday");

        if (!$end_date)
            $end_date = time();

        //select top 10 products by order count

        $this->db->select(""
            . "sum(case when (tbl_order_shipping.ship_status = 'booked') then 1 else 0 end) as booked,"
            . "sum(case when (tbl_order_shipping.ship_status = 'pending pickup') then 1 else 0 end) as pending_pickup,"
            . "sum(case when (tbl_order_shipping.ship_status = 'in transit' || tbl_order_shipping.ship_status = 'out for delivery' || tbl_order_shipping.ship_status = 'exception') then 1 else 0 end) as in_transit,"
            . "sum(case when (tbl_order_shipping.ship_status = 'delivered') then 1 else 0 end) as delivered,"
            . "sum(case when (tbl_order_shipping.ship_status = 'rto') then 1 else 0 end) as rto,"
            . "sum(1) as total,"
            . "tbl_order_products.product_name as product_name,"
            . "MIN(tbl_order_products.product_sku) as pro_sku");

        $this->db->where('tbl_order_shipping.created >= ', $start_date);
        $this->db->where('tbl_order_shipping.created <= ', $end_date);
        $this->db->where('tbl_order_shipping.user_id', $user_id);
        $this->db->where('tbl_order_products.product_name !=', '');
        $this->db->group_by('tbl_order_products.product_name');
        $this->db->where('tbl_order_shipping.ship_status != ', 'cancelled');

        $this->db->limit(10);
        $this->db->order_by('total', 'desc');

        $this->db->join('tbl_orders', 'tbl_orders.id = tbl_order_shipping.order_id', 'LEFT');
        $this->db->join('tbl_order_products', 'tbl_order_products.order_id = tbl_orders.id', 'LEFT');

        $q = $this->db->get('tbl_order_shipping');

        return $q->result();
    }

    function user_channel_integrated($user_id = false)
    {
        if (!$user_id)
            return false;

        $this->db->where('user_id', $user_id);
        $this->db->limit(1);

        $q = $this->db->get('tbl_user_channels');

        return ($q->num_rows() == 1) ? true : false;
    }

    function user_warehouse_integrated($user_id = false)
    {
        if (!$user_id)
            return false;

        $this->db->where('user_id', $user_id);
        $this->db->limit(1);

        $q = $this->db->get('tbl_warehouse');

        return ($q->num_rows() == 1) ? true : false;
    }

    function user_recharge_check($user_id = false)
    {
        if (!$user_id)
            return false;

        $this->db->where('id', $user_id);
        $this->db->limit(1);

        $q = $this->db->get('tbl_users');

        return $q->row();
    }

    function user_kyc_check($user_id = false)
    {
        if (!$user_id)
            return false;

        $this->db->where('user_id', $user_id);
        $this->db->limit(1);

        $q = $this->db->get('tbl_company_details');

        return ($q->num_rows() == 1) ? $q->row() : false;
    }

    function user_shipment_check($user_id = false)
    {
        if (!$user_id)
            return false;

        $this->db->where('user_id', $user_id);
        $this->db->limit(1);
        $this->db->where('ship_status !=', 'cancelled');
        $q = $this->db->get('tbl_order_shipping');
        return ($q->num_rows() == 1) ? true : false;
    }
    function getallagreements_org($date)
    {
        $this->db->select(
            ""
                . " `tbl_agreements`.id,agreements.section_name,agreements.version,agreements.change_description,agreements.doc_link,agreements.publish_on"
        );
        $this->db->where('created >=', $date);
        $this->db->order_by('id', 'DESC');
        $q = $this->db->get('agreements ');
        //echo $this->db->last_query();
        return $q->result();
    }

    function getallagreements_count($user_id = false)
    {
        if (!$user_id)
            return false;

        $this->db->where('seller_id', $user_id);
        $this->db->limit(1);
        $q = $this->db->get('agreements_accept');
        //echo $this->db->last_query(); die;
        return ($q->num_rows() == 1) ? true : false;
    }

    function getallagreements($date, $userid)
    {
        $this->db->select(
            ""
                . " DISTINCT(`tbl_agreements`.id)  as id,agreements.section_name,agreements.version,agreements.change_description,agreements.doc_link,agreements.publish_on"
        );

        $this->db->where('agreements`.`created >=', $date);
        $this->db->join('agreements_accept', 'agreements.id=agreements_accept.agreement_id', 'LEFT');
        $this->db->group_by('agreements.id');
        $this->db->order_by('agreements.id', 'DESC');
        $q = $this->db->get('agreements ');
        return $q->result();
    }

    function getallagreements_dash($date, $userid)
    {
        $this->db->select(
            ""
                . " DISTINCT(`tbl_agreements`.id)  as id,agreements.section_name,agreements.version,agreements.change_description,agreements.doc_link,agreements.publish_on"
        );

        $this->db->where('agreements`.`created >=', $date);
        $this->db->join('agreements_accept', 'agreements.id=agreements_accept.agreement_id', 'LEFT');
        $this->db->group_by('agreements.id');
        $this->db->order_by('agreements.id', 'DESC');
        $this->db->limit('1');
        $q = $this->db->get('agreements ');
        //echo $this->db->last_query();  die;
        return $q->result();
    }

    function getallagreements_bef($date, $userid)
    {
        $this->db->select(
            ""
                . " DISTINCT(`tbl_agreements`.id)  as id,agreements.section_name,agreements.version,agreements.change_description,agreements.doc_link,agreements.publish_on"
        );

        $this->db->where('agreements`.`created <=', $date);
        $this->db->join('agreements_accept', 'agreements.id=agreements_accept.agreement_id', 'LEFT');
        $this->db->group_by('agreements.id');
        $this->db->order_by('agreements.id', 'DESC');
        $this->db->limit('1');
        $q = $this->db->get('agreements ');
        return $q->result();
    }


    function getallagreements_date($id)
    {
        $this->db->select(
            ""
                . " `tbl_agreements`.id,agreements.section_name,agreements.version,agreements.change_description,agreements.doc_link,agreements.publish_on"
        );
        $this->db->where('id <=', $id);
        $this->db->order_by('id', 'DESC');
        $q = $this->db->get('agreements ');
        return $q->result();
    }
    function getallagreements_limit($date)
    {
        $this->db->select(
            ""
                . " `tbl_agreements`.id,agreements.section_name,agreements.version,agreements.change_description,agreements.doc_link,agreements.publish_on"
        );
        if (!empty($date)) {
            $this->db->where('created <=', $date);
        }
        $this->db->limit(1);
        $this->db->order_by('id', 'DESC');
        $q = $this->db->get('agreements ');
        return $q->result();
    }

    function getallagreements_accpt($agreement_id, $user_id)
    {
        $this->db->select("agreements_accept.*,users.fname,users.lname");
        $this->db->where('agreement_id', $agreement_id);
        $this->db->where('seller_id', $user_id);
        $this->db->join('tbl_users', 'users.id = agreements_accept.seller_id', 'LEFT');
        $q = $this->db->get('agreements_accept ');
        // echo $this->db->last_query(); die;
        return ($q->num_rows() == 1) ? $q->row() : 0;
    }

    function get_agreements($agreement_id = false)
    {
        if (!$agreement_id)
            return false;
        $this->db->select("*");
        $this->db->where('id', $agreement_id);
        $q = $this->db->get('agreements ');
        return $q->result();
    }

    function get_acceptence($agreement_id = false, $user_id = false)
    {
        if (!$agreement_id)
            return false;
        $this->db->select("*");
        $this->db->group_by('agreement_id');
        $this->db->where('agreement_id', $agreement_id);
        $this->db->where('seller_id', $user_id);
        $q = $this->db->get('agreements_accept ');
        return ($q->num_rows() == 1) ? 1 : 0;
    }

    function insert_acceptence($id = false, $url = false)
    {
        if (empty($id))
            return false;

        $headers = getallheaders();
        if (isset($headers['X-Forwarded-For']) && $headers['X-Forwarded-For'] != "") {
            $ip_address = $headers['X-Forwarded-For'];
        } else {
            $ip_address = $_SERVER['REMOTE_ADDR'];
        }

        $save['acceptence_date'] = date("Y-m-d H:i:s");
        $save['created']         = time();
        $save['agreement_id']    = $id;
        $save['agreement_url']    = $url;
        $save['ip_address']      = $ip_address;
        $save['seller_id']       = $this->session->userdata('user_info')->user_id;
        $this->db->insert('agreements_accept', $save);
        return true;
    }

    function getAgreementContent()
    {
        $this->db->limit(1);
        $q = $this->db->get('tbl_agreement_html_content ');
        return $q->row();
    }
}
