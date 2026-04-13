<?php

class Shipping_model extends MY_model
{
    public function __construct()
    {
        parent::__construct();
        $this->table = 'order_shipping';

        $this->slave = $this->load->database('slave', TRUE);
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

    function getByOrderID($order_id = false)
    {
        if (!$order_id)
            return false;

        $this->db->where('order_id', $order_id);
        $this->db->order_by('id', 'desc');
        $this->db->limit(1);
        $q = $this->db->get($this->table);
        return $q->row();
    }

    //--This function fetch details in order page--//
    function getByOrderhistory($order_id = false)
    {
        $this->db->select("tbl_order_shipping.awb_number as awb_number,
		tbl_order_shipping.id as shipping_id,
		tbl_order_shipping.ship_status as ship_status,
		tbl_order_shipping.in_transit_sms as sms,
		tbl_order_shipping.message as ship_message,
		tbl_order_shipping.created as shipping_created,
		courier.name as courier_name,
		courier.code as courier_code,
        tbl_order_shipping.order_id as orderid,
        tbl_order_shipping.user_id,
        tbl_order_shipping.fees_refunded,
        tbl_order_shipping.zone,
        tbl_users.fname,
        tbl_users.lname,
        tbl_users.company_name");
        $this->db->where('order_id', $order_id);
        $this->db->join('courier', 'courier.id = tbl_order_shipping.courier_id');
        $this->db->join('users', 'tbl_users.id = tbl_order_shipping.user_id');
        $this->db->order_by('tbl_order_shipping.order_id', 'desc');
        $q = $this->db->get($this->table);
        return $q->result();
    }

    function getshipmenttracking($ship_id)
    {
        $this->db->select("shipment_tracking.*");
        $this->db->where('shipment_id', $ship_id);
        $q = $this->db->get('shipment_tracking');
        return $q->result();
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

    function getByUserID($limit = 50, $offset = 0, $filter = array())
    {
        $this->slave->select("orders.*, group_concat(tbl_order_products.product_name)  as products,group_concat(tbl_order_products.product_sku)  as products_sku,count(tbl_order_products.product_qty)  as products_qty,
		tbl_order_shipping.awb_number as awb_number,
		tbl_order_shipping.id as shipping_id,
		tbl_order_shipping.ship_status as ship_status,
		tbl_order_shipping.message as ship_message,
		tbl_order_shipping.created as shipping_created,
		courier.id as courier_ids,
		courier.name as courier_name,
		courier.code as courier_code,
		tbl_order_shipping.receipt_amount as receipt_amount,
		tbl_order_shipping.delivered_time as delivered_time,
		tbl_order_shipping.charged_weight as charged_weight,
		tbl_order_shipping.extra_weight_charges as extra_weight_charges,
		tbl_order_shipping.courier_fees as courier_fees,
		tbl_order_shipping.cod_fees as cod_fees,
		tbl_order_shipping.total_fees as total_fees,
		tbl_order_shipping.fees_refunded as fees_refunded,
		tbl_order_shipping.rto_extra_weight_charges as rto_extra_weight_charges,
		tbl_order_shipping.rto_status as rto_status,
		tbl_order_shipping.rto_charges as rto_charges,
		tbl_order_shipping.rto_date as rto_date,
		tbl_order_shipping.rto_awb as rto_awb,
		tbl_order_shipping.cod_reverse_amount as cod_reverse_amount,
		tbl_users.fname as user_fname,
        tbl_users.lname as user_lname,
        admin_users.fname as manager_fname,
        admin_users.lname as manager_lname,
        tbl_users.company_name,
		tbl_users.id as userid,
		tbl_order_shipping.courier_billed as courier_billed,
        tbl_order_shipping.edd_time as edd_time,
		tbl_order_shipping.status_updated_at as status_updated_at,
		tbl_order_shipping.remittance_id as remittance_id,tbl_order_shipping.insurance_price");

        if (!empty($filter['order_ids'])) {
            $this->slave->where_in('orders.order_no', $filter['order_ids']);
        }

        if (!empty($filter['seller_id'])) {
            $this->slave->where('tbl_order_shipping.user_id', $filter['seller_id']);
        }

        if (!empty($filter['search_query'])) {
            $query = $filter['search_query'];
            $this->slave->where(" (orders.customer_name like '%{$query}%' or orders.shipping_phone like '%{$query}%' or orders.shipping_address like '%{$query}%' or orders.shipping_address_2 like '%{$query}%' or orders.shipping_city  like '%{$query}%' or orders.shipping_state like '%{$query}%' or tbl_order_products.product_name like '%{$query}%' ) ");
        }

        if (!empty($filter['pay_method'])) {
            $this->slave->where('orders.order_payment_type', $filter['pay_method']);
        }

        if (!empty($filter['shipment_id'])) {
            $this->slave->where_in("tbl_order_shipping.id", $filter['shipment_id']);
        }

        if (!empty($filter['date_type_field']) && $filter['date_type_field'] == 'delivered') {
            if (!empty($filter['start_date'])) {
                $this->slave->where("tbl_order_shipping.delivered_time >= '" . $filter['start_date'] . "'");
            }
            if (!empty($filter['end_date'])) {
                $this->slave->where("tbl_order_shipping.delivered_time <= '" . $filter['end_date'] . "'");
            }
        } else {
            if (!empty($filter['start_date'])) {
                $this->slave->where("tbl_order_shipping.created >= '" . $filter['start_date'] . "'");
            }
            if (!empty($filter['end_date'])) {
                $this->slave->where("tbl_order_shipping.created <= '" . $filter['end_date'] . "'");
            }
        }

        if (!empty($filter['courier_id'])) {
            $this->slave->where('tbl_order_shipping.courier_id', $filter['courier_id']);
        }

        if (!empty($filter['awb_no'])) {
            $this->slave->where_in('tbl_order_shipping.awb_number', $filter['awb_no']);
        }

        if (!empty($filter['ship_status'])) {
            $this->slave->where('tbl_order_shipping.ship_status', $filter['ship_status']);
        }

        if (!empty($filter['rto_status'])) {
            $this->slave->where('tbl_order_shipping.rto_status', $filter['rto_status']);
        }

        if (!empty($filter['ship_status_in'])) {
            $this->slave->where_in('tbl_order_shipping.ship_status', $filter['ship_status_in']);
        }

        if (!empty($filter['ship_status_not_in'])) {
            $this->slave->where_not_in('tbl_order_shipping.ship_status', $filter['ship_status_not_in']);
        }

        if (!empty($filter['account_manager_id'])) {
            $this->slave->where('tbl_users.account_manager_id', $filter['account_manager_id']);
        }
        if (!empty($filter['sale_manager_id'])) {
            $this->slave->where_in('tbl_users.sale_manager_id', array_map('intval', $filter['sale_manager_id']));
        }
        if (!empty($filter['accout_manager_in'])) {
            $this->slave->where_in('tbl_users.account_manager_id', array_map('intval', $filter['accout_manager_in']));
        }

        if (!empty($filter['stuck'])) {
            $this->slave->where('tbl_order_shipping.status_updated_at <', strtotime('-3 days midnight'));
            $this->slave->where('tbl_order_shipping.status_updated_at !=', '');
        }

        if (!empty($filter['open_shipments'])) {
            $this->slave->where(
                "(
                     (tbl_order_shipping.ship_status in ('in transit', 'out for delivery', 'exception'))
                     OR (tbl_order_shipping.ship_status = 'rto' and rto_status = 'in transit')
                )"
            );
        }

        if (!empty($filter['weight_uploaded'])) {
            if ($filter['weight_uploaded'] == 'yes')
                $this->slave->where('tbl_order_shipping.courier_billed_weight >', '0');
            if ($filter['weight_uploaded'] == 'no')
                $this->slave->where('tbl_order_shipping.courier_billed_weight <=', '0');
        }

        if (!empty($filter['courier_billed'])) {
            if ($filter['courier_billed'] == 'yes')
                $this->slave->where('tbl_order_shipping.courier_billed >', '0');
            if ($filter['courier_billed'] == 'no')
                $this->slave->where('tbl_order_shipping.courier_billed <=', '0');
        }

        if (!empty($filter['state_in'])) {
            $wh = array();
            foreach ($filter['state_in'] as $s_in) {
                $wh[] = " orders.shipping_zip like '{$s_in}%'";
            }

            $this->slave->where(" ( " . implode(' OR ', $wh) . ")");
        }

        $this->slave->limit($limit);
        $this->slave->offset($offset);
        $this->slave->group_by('order_products.order_id, tbl_order_shipping.id');
        $this->slave->join('orders', 'orders.id = tbl_order_shipping.order_id');
        $this->slave->join('order_products', 'order_products.order_id = orders.id');
        $this->slave->join('courier', 'courier.id = tbl_order_shipping.courier_id');
        $this->slave->join('users', 'tbl_users.id = orders.user_id');
        $this->slave->join("(SELECT id,fname, lname FROM tbl_users where is_admin = '1') admin_users", 'admin_users.id = tbl_users.account_manager_id', 'left');
        $this->slave->order_by('tbl_order_shipping.created', 'desc');
        $q = $this->slave->get($this->table);
        return $q->result();
    }

    function exportShipments($limit = 50, $offset = 0, $filter = array())
    {
        $this->slave->select("
        orders.*,
        group_concat(tbl_order_products.product_name)  as products,
        group_concat(tbl_order_products.product_sku)  as products_sku,
        sum(tbl_order_products.product_qty)  as products_qty,
        tbl_order_shipping.awb_number as awb_number,
        tbl_order_shipping.id as shipping_id,
        tbl_order_shipping.ship_status as ship_status,
        tbl_order_shipping.message as ship_message,
        tbl_order_shipping.created as shipping_created,
        courier.id as courier_ids,
        courier.name as courier_name,
        courier.display_name as courier_display_name,
        courier.aggregator_courier_id as aggregator_courier_id,
        courier.code as courier_code,
        tbl_order_shipping.receipt_amount as receipt_amount,
        tbl_order_shipping.delivered_time as delivered_time,
        tbl_order_shipping.charged_weight as charged_weight,
        tbl_order_shipping.extra_weight_charges as extra_weight_charges,
        tbl_order_shipping.courier_fees as courier_fees,
        tbl_order_shipping.cod_fees as cod_fees,
        tbl_order_shipping.total_fees as total_fees,
        tbl_order_shipping.fees_refunded as fees_refunded,
        tbl_order_shipping.rto_extra_weight_charges as rto_extra_weight_charges,
        tbl_order_shipping.rto_status as rto_status,
        tbl_order_shipping.rto_charges as rto_charges,
        tbl_order_shipping.rto_date as rto_date,
        tbl_order_shipping.cod_reverse_amount as cod_reverse_amount,
        tbl_order_shipping.courier_billed_weight as old_courier_billed_weight,

        tbl_order_shipping.charged_weight as charged_weight,
        tbl_order_shipping.calculated_weight as calculated_weight,
        tbl_order_shipping.weight_dispute_raised,
        tbl_order_shipping.pending_weight_charges,

        weight_reco.courier_billed_weight as courier_billed_weight,
        weight_reco.upload_date as weight_upload_date,
        weight_reco.apply_weight_date as weight_applied_date,
        weight_reco.seller_action_status,
        weight_reco.is_cn_issued as weight_credit_applied,
        weight_reco.applied_to_wallet,
        weight_reco.weight_difference_charges,

        tbl_order_shipping.lost_cn_issued,
        tbl_order_shipping.damaged_cn_issued,
        tbl_order_shipping.pending_pickup_date,
        tbl_users.fname as user_fname,
        tbl_users.lname as user_lname,
        tbl_users.company_name,
        admin_users.fname as manager_fname,
        admin_users.lname as manager_lname,
        tbl_users.id as userid,
        tbl_order_shipping.courier_billed as courier_billed,
        tbl_order_shipping.status_updated_at as status_updated_at,
        tbl_order_shipping.remittance_id as remittance_id,
        tbl_order_shipping.receipt_id as receipt_id,
        tbl_order_shipping.receipt_amount as receipt_amount,
        tbl_order_shipping.zone as zone,
        tbl_order_shipping.edd_time,
        warehouse.name as whname,
        warehouse.phone as whphone,
        warehouse.address_1,
        warehouse.address_2,
        warehouse.city as whcity,warehouse.state as whstate,
        warehouse.zip as whzip,
        IF(tbl_shipment_tracking.pickup_time = 0, tbl_order_shipping.pickup_time, tbl_shipment_tracking.pickup_time) AS pickup_date,
        shipment_tracking.expected_delivery_date as edd,
        shipment_tracking.reached_at_destination_hub as reached_at_destination_date,
        shipment_tracking.last_ndr_reason,
        shipment_tracking.total_ofd_attempts,
        shipment_tracking.delivery_attempt_count,
        shipment_tracking.ofd_attempt_1_date as first_delivery_attempt_date,
        shipment_tracking.last_attempt_date,
        shipment_tracking.rto_mark_date as rto_initiated_date,
        shipment_tracking.rto_delivered_date as rto_delivered_date,
        shipment_tracking.otp_verified as otp_verified,
        shipment_tracking.otp_base_delivery as otp_base_delivery,
        essential_order,
        tbl_order_shipping.insurance_price");

        if (!empty($filter['order_type'])) {
            $this->slave->where('orders.order_type', $filter['order_type']);
        }

        if (!empty($filter['order_ids'])) {
            $this->slave->where_in('orders.order_no', $filter['order_ids']);
        }

        if (!empty($filter['seller_id'])) {
            $this->slave->where('tbl_order_shipping.user_id', $filter['seller_id']);
        }

        if (!empty($filter['search_query'])) {
            $query = $filter['search_query'];
            $this->slave->where(" (orders.customer_name like '%{$query}%' or orders.shipping_phone like '%{$query}%' or orders.shipping_address like '%{$query}%' or orders.shipping_address_2 like '%{$query}%' or orders.shipping_city  like '%{$query}%' or orders.shipping_state like '%{$query}%' or tbl_order_products.product_name like '%{$query}%' ) ");
        }

        if (!empty($filter['pay_method'])) {
            $this->slave->where('orders.order_payment_type', $filter['pay_method']);
        }

        if (!empty($filter['shipment_id'])) {
            $this->slave->where_in("tbl_order_shipping.id", $filter['shipment_id']);
        }

        if (!empty($filter['date_type_field']) && $filter['date_type_field'] == 'delivered') {
            if (!empty($filter['start_date'])) {
                $this->slave->where("tbl_order_shipping.delivered_time >= '" . $filter['start_date'] . "'");
            }
            if (!empty($filter['end_date'])) {
                $this->slave->where("tbl_order_shipping.delivered_time <= '" . $filter['end_date'] . "'");
            }
        } else {
            if (!empty($filter['start_date'])) {
                $this->slave->where("tbl_order_shipping.created >= '" . $filter['start_date'] . "'");
            }

            if (!empty($filter['end_date'])) {
                $this->slave->where("tbl_order_shipping.created <= '" . $filter['end_date'] . "'");
            }
        }

        if (!empty($filter['courier_id'])) {
            $this->slave->where('tbl_order_shipping.courier_id', $filter['courier_id']);
        }

        if (!empty($filter['awb_no'])) {
            // $this->slave->where_in('tbl_order_shipping.awb_number', $filter['awb_no']);
            $this->slave->group_start();
            $awb_ids_chunk = array_chunk($filter['awb_no'], 200);
            foreach ($awb_ids_chunk as $chunk_awb_ids) {
                $this->slave->or_where_in('tbl_order_shipping.awb_number', $chunk_awb_ids);
            }
            $this->slave->group_end();
        }

        if (!empty($filter['ship_status'])) {
            $this->slave->where('tbl_order_shipping.ship_status', $filter['ship_status']);
        }

        if (!empty($filter['rto_status'])) {
            $this->slave->where('tbl_order_shipping.rto_status', $filter['rto_status']);
        }

        if (!empty($filter['ship_status_in'])) {
            $this->slave->where_in('tbl_order_shipping.ship_status', $filter['ship_status_in']);
        }

        if (!empty($filter['ship_status_not_in'])) {
            $this->slave->where_not_in('tbl_order_shipping.ship_status', $filter['ship_status_not_in']);
        }

        if (!empty($filter['stuck'])) {
            $this->slave->where('tbl_order_shipping.status_updated_at <', strtotime('-3 days midnight'));
            $this->slave->where('tbl_order_shipping.status_updated_at !=', '');
        }

        if (!empty($filter['open_shipments'])) {
            $this->slave->where(
                "(
                     (tbl_order_shipping.ship_status in ('in transit', 'out for delivery', 'exception'))
                     OR (tbl_order_shipping.ship_status = 'rto' and rto_status = 'in transit')
                )"
            );
        }

        if (!empty($filter['account_manager_id'])) {
            $this->slave->where('tbl_users.account_manager_id', $filter['account_manager_id']);
        }

        if (!empty($filter['manager_id'])) {
            $this->slave->where_in('tbl_users.account_manager_id', array_map('intval', $filter['manager_id']));
        }

        if (!empty($filter['accout_manager_in'])) {
            $this->slave->where_in('tbl_users.account_manager_id', array_map('intval', $filter['accout_manager_in']));
        }

        if (!empty($filter['sale_manager_id'])) {
            $this->slave->where_in('tbl_users.sale_manager_id', array_map('intval', $filter['sale_manager_id']));
        }

        if (!empty($filter['weight_uploaded'])) {
            if ($filter['weight_uploaded'] == 'yes')
                $this->slave->where('tbl_order_shipping.courier_billed_weight >', '0');
            if ($filter['weight_uploaded'] == 'no')
                $this->slave->where('tbl_order_shipping.courier_billed_weight <=', '0');
        }

        if (!empty($filter['courier_billed'])) {
            if ($filter['courier_billed'] == 'yes')
                $this->slave->where('tbl_order_shipping.courier_billed >', '0');
            if ($filter['courier_billed'] == 'no')
                $this->slave->where('tbl_order_shipping.courier_billed <=', '0');
        }

        if (!empty($filter['state_in'])) {
            $wh = array();
            foreach ($filter['state_in'] as $s_in) {
                $wh[] = " orders.shipping_zip like '{$s_in}%'";
            }

            $this->slave->where(" ( " . implode(' OR ', $wh) . ")");
        }

        $this->slave->limit($limit);
        $this->slave->offset($offset);

        $this->slave->group_by('order_products.order_id, tbl_order_shipping.id');
        $this->slave->join('orders', 'orders.id = tbl_order_shipping.order_id');
        $this->slave->join('warehouse', 'warehouse.id = tbl_order_shipping.warehouse_id');
        $this->slave->join('order_products', 'order_products.order_id = orders.id');
        $this->slave->join('courier', 'courier.id = tbl_order_shipping.courier_id');
        $this->slave->join('weight_reco', 'weight_reco.shipment_id = tbl_order_shipping.id', 'left');
        $this->slave->join('users', 'tbl_users.id = orders.user_id');
        $this->slave->join("(SELECT id,fname, lname FROM tbl_users where is_admin = '1') admin_users", 'admin_users.id = tbl_users.account_manager_id', 'left');
        $this->slave->join('shipment_tracking', 'tbl_order_shipping.id = shipment_tracking.shipment_id', 'LEFT');
        $this->slave->order_by('tbl_order_shipping.created', 'desc');
        $this->slave->from($this->table);
        return $query =   $this->slave->get_compiled_select();
    }

    function exportShipmentsNew($limit = 50, $offset = 0, $filter = array())
    {
           $user_id=$awb_no='';
            if (!empty($filter['seller_id'])) {
                $user_id=" and tbl_order_shipping.user_id=". $filter['seller_id'];
        }

        if (!empty($filter['date_type_field']) && $filter['date_type_field'] == 'delivered') {
            if (!empty($filter['start_date'])) {
                $statrt_date=" tbl_order_shipping.delivered_time >= '" . $filter['start_date'] . "'";
            }
            if (!empty($filter['end_date'])) {
                $end_date=" tbl_order_shipping.delivered_time <= '" . $filter['end_date'] . "'";
            }
        } else {
            if (!empty($filter['start_date'])) {
                $statrt_date="'".$filter['start_date']."'" ;
            }
            if (!empty($filter['end_date'])) {
               $end_date="'".$filter['end_date']."'" ;
            }
        }

        // echo "start date--".$statrt_date;
        // echo "end date--".$end_date; die;

       
      

        $allAwb = array();
        if (!empty($filter['awb_no'])) {
            foreach($filter['awb_no'] as $awb)
            {
              $asdsad="'$awb'";
              array_push($allAwb,$asdsad);
            }
          
            $awb_no=" and tbl_order_shipping.awb_number in(".implode(",",$allAwb).")";
        }

       return $qry="SELECT awb_number,`order_id`,package_weight,courier_name,ship_status,shipping_created,charged_weight,extra_weight_charges,courier_fees,cod_fees,total_fees,fees_refunded,rto_extra_weight_charges,rto_charges,cod_reverse_amount,courier_billed,insurance_price,company_name,os_id   FROM (SELECT `tbl_order_shipping`.`order_id`,`tbl_order_shipping`.`id` as os_id,`tbl_order_shipping`.`awb_number` as `awb_number`, `tbl_order_shipping`.`ship_status` as `ship_status`,`tbl_orders`.`package_weight`,(select `tbl_courier`.`name` from `tbl_courier` where `tbl_courier`.`id` = `tbl_order_shipping`.`courier_id` ) as `courier_name`,
        `tbl_order_shipping`.`created` as `shipping_created`,  `tbl_order_shipping`.`charged_weight` as `charged_weight`,
        `tbl_order_shipping`.`extra_weight_charges` as `extra_weight_charges`, `tbl_order_shipping`.`courier_fees` as `courier_fees`,
         `tbl_order_shipping`.`cod_fees` as `cod_fees`, `tbl_order_shipping`.`total_fees` as `total_fees`,
         `tbl_order_shipping`.`fees_refunded` as `fees_refunded`, `tbl_order_shipping`.`rto_extra_weight_charges` as `rto_extra_weight_charges`,
         `tbl_order_shipping`.`rto_charges` as `rto_charges`, `tbl_order_shipping`.`cod_reverse_amount` as `cod_reverse_amount`,
         `tbl_order_shipping`.`courier_billed` as `courier_billed`, `tbl_order_shipping`.`insurance_price`,tbl_users.company_name as company_name
        FROM `tbl_order_shipping`
        LEFT JOIN `tbl_orders` ON `tbl_order_shipping`.`order_id` = `tbl_orders`.`id`
        LEFT JOIN `tbl_order_products` ON `tbl_orders`.`id` = `tbl_order_products`.`order_id`
        LEFT JOIN `tbl_users` ON `tbl_users`.`id` = `tbl_orders`.`user_id`
        WHERE `tbl_order_shipping`.`created` >= $statrt_date AND `tbl_order_shipping`.`created` <= $end_date $awb_no $user_id
       
        ORDER BY `tbl_order_shipping`.`created` DESC   ) AS t1 GROUP BY order_id,os_id  LIMIT ".$limit." ";

        //die;
        


    //     $query = $this->slave->query("SELECT awb_number,`order_id`,package_weight,courier_name,ship_status,shipping_created,charged_weight,extra_weight_charges,courier_fees,cod_fees,total_fees,fees_refunded,rto_extra_weight_charges,rto_charges,cod_reverse_amount,courier_billed,insurance_price,company_name,os_id   FROM (SELECT `tbl_order_shipping`.`order_id`,`tbl_order_shipping`.`id` as os_id,`tbl_order_shipping`.`awb_number` as `awb_number`, `tbl_order_shipping`.`ship_status` as `ship_status`,`tbl_orders`.`package_weight`,(select `tbl_courier`.`name` from `tbl_courier` where `tbl_courier`.`id` = `tbl_order_shipping`.`courier_id` ) as `courier_name`,
    //     `tbl_order_shipping`.`created` as `shipping_created`,  `tbl_order_shipping`.`charged_weight` as `charged_weight`,
    //     `tbl_order_shipping`.`extra_weight_charges` as `extra_weight_charges`, `tbl_order_shipping`.`courier_fees` as `courier_fees`,
    //      `tbl_order_shipping`.`cod_fees` as `cod_fees`, `tbl_order_shipping`.`total_fees` as `total_fees`,
    //      `tbl_order_shipping`.`fees_refunded` as `fees_refunded`, `tbl_order_shipping`.`rto_extra_weight_charges` as `rto_extra_weight_charges`,
    //      `tbl_order_shipping`.`rto_charges` as `rto_charges`, `tbl_order_shipping`.`cod_reverse_amount` as `cod_reverse_amount`,
    //      `tbl_order_shipping`.`courier_billed` as `courier_billed`, `tbl_order_shipping`.`insurance_price`,tbl_users.company_name as company_name
    //     FROM `tbl_order_shipping`
    //     LEFT JOIN `tbl_orders` ON `tbl_order_shipping`.`order_id` = `tbl_orders`.`id`
    //     LEFT JOIN `tbl_order_products` ON `tbl_orders`.`id` = `tbl_order_products`.`order_id`
    //     LEFT JOIN `tbl_users` ON `tbl_users`.`id` = `tbl_orders`.`user_id`
    //     WHERE `tbl_order_shipping`.`created` >= $statrt_date AND `tbl_order_shipping`.`created` <= $end_date $awb_no $user_id
       
    //     ORDER BY `tbl_order_shipping`.`created` DESC LIMIT ".$limit." offset $offset ) AS t1 GROUP BY order_id,os_id");
    //    return $this->slave->last_query();

    }

    function countByUserID($filter = array())
    {
        $this->slave->select('count(DISTINCT tbl_order_shipping.id) as total');

        if (!empty($filter['order_type'])) {
            $this->slave->where('orders.order_type', $filter['order_type']);
        }

        if (!empty($filter['order_ids'])) {
            $this->slave->where_in('orders.order_no', $filter['order_ids']);
        }

        if (!empty($filter['seller_id'])) {
            $this->slave->where('tbl_order_shipping.user_id', $filter['seller_id']);
        }

        if (!empty($filter['accout_manager_in'])) {
            $this->slave->where_in('tbl_users.account_manager_id', array_map('intval', $filter['accout_manager_in']));
        }

        if (!empty($filter['search_query'])) {
            $query = $filter['search_query'];
            $this->slave->where(" (orders.customer_name like '%{$query}%' or orders.shipping_phone like '%{$query}%' or orders.shipping_address like '%{$query}%' or orders.shipping_address_2 like '%{$query}%' or orders.shipping_city  like '%{$query}%' or orders.shipping_state like '%{$query}%' or tbl_order_products.product_name like '%{$query}%'  ) ");
        }

        if (!empty($filter['pay_method'])) {
            $this->slave->where('orders.order_payment_type', $filter['pay_method']);
        }

        if (!empty($filter['shipment_id'])) {
            $this->slave->where_in("tbl_order_shipping.id", $filter['shipment_id']);
        }

        if ((!empty($filter['date_type_field'])) && $filter['date_type_field'] == 'delivered') {
            if (!empty($filter['start_date'])) {
                $this->slave->where("tbl_order_shipping.delivered_time >= '" . $filter['start_date'] . "'");
            }
            if (!empty($filter['end_date'])) {
                $this->slave->where("tbl_order_shipping.delivered_time <= '" . $filter['end_date'] . "'");
            }
        } else {
            if (!empty($filter['start_date'])) {
                $this->slave->where("tbl_order_shipping.created >= '" . $filter['start_date'] . "'");
            }
            if (!empty($filter['end_date'])) {
                $this->slave->where("tbl_order_shipping.created <= '" . $filter['end_date'] . "'");
            }
        }

        if (!empty($filter['courier_id'])) {
            $this->slave->where('tbl_order_shipping.courier_id', $filter['courier_id']);
        }

        if (!empty($filter['awb_no'])) {
            $this->slave->where_in('tbl_order_shipping.awb_number', $filter['awb_no']);
        }

        if (!empty($filter['ship_status'])) {
            $this->slave->where('tbl_order_shipping.ship_status', $filter['ship_status']);
        }

        if (!empty($filter['rto_status'])) {
            $this->slave->where('tbl_order_shipping.rto_status', $filter['rto_status']);
        }
        if (!empty($filter['ship_status_in'])) {
            $this->slave->where_in('tbl_order_shipping.ship_status', $filter['ship_status_in']);
        }

        if (!empty($filter['ship_status_not_in'])) {
            $this->slave->where_not_in('tbl_order_shipping.ship_status', $filter['ship_status_not_in']);
        }

        if (!empty($filter['stuck'])) {
            $this->slave->where('tbl_order_shipping.status_updated_at <', strtotime('-3 days midnight'));
            $this->slave->where('tbl_order_shipping.status_updated_at !=', '');
        }

        if (!empty($filter['open_shipments'])) {
            $this->slave->where(
                "(
                     (tbl_order_shipping.ship_status in ('in transit', 'out for delivery', 'exception'))
                     OR (tbl_order_shipping.ship_status = 'rto' and rto_status = 'in transit')
                )"
            );
        }

        if (!empty($filter['weight_uploaded'])) {
            if ($filter['weight_uploaded'] == 'yes')
                $this->slave->where('tbl_order_shipping.courier_billed_weight >', '0');
            if ($filter['weight_uploaded'] == 'no')
                $this->slave->where('tbl_order_shipping.courier_billed_weight <=', '0');
        }

        if (!empty($filter['courier_billed'])) {
            if ($filter['courier_billed'] == 'yes')
                $this->slave->where('tbl_order_shipping.courier_billed >', '0');
            if ($filter['courier_billed'] == 'no')
                $this->slave->where('tbl_order_shipping.courier_billed <=', '0');
        }

        if (!empty($filter['account_manager_id'])) {
            $this->slave->where('tbl_users.account_manager_id', $filter['account_manager_id']);
        }
        if (!empty($filter['sale_manager_id'])) {
            $this->slave->where_in('tbl_users.sale_manager_id', array_map('intval', $filter['sale_manager_id']));
        }

        $this->slave->join('orders', 'orders.id = tbl_order_shipping.order_id');
        $this->slave->join('order_products', 'order_products.order_id = orders.id');
        $this->slave->join('courier', 'courier.id = tbl_order_shipping.courier_id');
        $this->slave->join('users', 'tbl_users.id = orders.user_id');
        $this->slave->join("(SELECT id,fname, lname FROM tbl_users where is_admin = '1') admin_users", 'admin_users.id = tbl_users.account_manager_id', 'left');
        $this->slave->order_by('tbl_order_shipping.created', 'desc');
        $q = $this->slave->get($this->table);
        //echo $this->slave->last_query(); die;
        return $q->row()->total;
    }

    function countByUserIDNew($filter = array())
    {
      
        $user_id=$awb_no='';
        if (!empty($filter['seller_id'])) {
           $user_id=" and tbl_order_shipping.user_id=". $filter['seller_id'];
        }

      
        
            if (!empty($filter['start_date'])) {
                //$this->slave->where("tbl_order_shipping.created >= '" . $filter['start_date'] . "'");
                $start_date="'".$filter['start_date']."'" ;
            }
            if (!empty($filter['end_date'])) {
                //$this->slave->where("tbl_order_shipping.created <= '" . $filter['end_date'] . "'");
                $end_date="'".$filter['end_date']."'" ;
            }
        

            $allAwb = array();
        if (!empty($filter['awb_no'])) {
            foreach($filter['awb_no'] as $awb)
            {
              $asdsad="'$awb'";
              array_push($allAwb,$asdsad);
            }
          
            $awb_no=" and tbl_order_shipping.awb_number in(".implode(",",$allAwb).")";
        }



        $query = $this->slave->query("SELECT count(  total )as totl FROM (SELECT `tbl_order_shipping`.`id` as total
        FROM `tbl_order_shipping`
        WHERE `tbl_order_shipping`.`created` >= $start_date AND `tbl_order_shipping`.`created` <= $end_date $awb_no $user_id
        ORDER BY `tbl_order_shipping`.`created` DESC ) AS t1");
       // echo "count query====> ".$this->slave->last_query(); die;
        return $data= $query->result();
       

    
       // return $q->row()->total;
    }

    function countByUserIDStatusGrouped($filter = array())
    {
        $this->slave->select("tbl_order_shipping.ship_status as ship_status, count(DISTINCT tbl_order_shipping.id) as total_count");

        if (!empty($filter['order_type'])) {
            $this->slave->where('orders.order_type', $filter['order_type']);
        }

        if (!empty($filter['order_ids'])) {
            $this->slave->where_in('orders.order_no', $filter['order_ids']);
        }

        if (!empty($filter['pay_method'])) {
            $this->slave->where('orders.order_payment_type', $filter['pay_method']);
        }

        if (!empty($filter['shipment_id'])) {
            $this->slave->where_in("tbl_order_shipping.id", $filter['shipment_id']);
        }

        if (!empty($filter['start_date'])) {
            $this->slave->where("tbl_order_shipping.created >= '" . $filter['start_date'] . "'");
        }

        if (!empty($filter['end_date'])) {
            $this->slave->where("tbl_order_shipping.created <= '" . $filter['end_date'] . "'");
        }

        if (!empty($filter['courier_id'])) {
            $this->slave->where('tbl_order_shipping.courier_id', $filter['courier_id']);
        }

        if (!empty($filter['awb_no'])) {
            $this->slave->where_in('tbl_order_shipping.awb_number', $filter['awb_no']);
        }

        if (!empty($filter['seller_id'])) {
            $this->slave->where('tbl_order_shipping.user_id', $filter['seller_id']);
        }

        if (!empty($filter['ship_status_in'])) {
            $this->slave->where_in('tbl_order_shipping.ship_status', $filter['ship_status_in']);
        }

        if (!empty($filter['ship_status_not_in'])) {
            $this->slave->where_not_in('tbl_order_shipping.ship_status', $filter['ship_status_not_in']);
        }

        if (!empty($filter['stuck'])) {
            $this->slave->where('tbl_order_shipping.status_updated_at <', strtotime('-3 days midnight'));
            $this->slave->where('tbl_order_shipping.status_updated_at !=', '');
        }

        if (!empty($filter['open_shipments'])) {
            $this->slave->where(
                "(
                     (tbl_order_shipping.ship_status in ('in transit', 'out for delivery', 'exception'))
                     OR (tbl_order_shipping.ship_status = 'rto' and rto_status = 'in transit')
                )"
            );
        }

        if (!empty($filter['search_query'])) {
            $query = $filter['search_query'];
            $this->slave->where(" (orders.customer_name like '%{$query}%' or orders.shipping_phone like '%{$query}%' or orders.shipping_address like '%{$query}%' or orders.shipping_address_2 like '%{$query}%' or orders.shipping_city  like '%{$query}%' or orders.shipping_state like '%{$query}%' or tbl_order_products.product_name like '%{$query}%' ) ");
        }

        if (!empty($filter['weight_uploaded'])) {
            if ($filter['weight_uploaded'] == 'yes')
                $this->slave->where('tbl_order_shipping.courier_billed_weight >', '0');
            if ($filter['weight_uploaded'] == 'no')
                $this->slave->where('tbl_order_shipping.courier_billed_weight <=', '0');
        }

        if (!empty($filter['courier_billed'])) {
            if ($filter['courier_billed'] == 'yes')
                $this->slave->where('tbl_order_shipping.courier_billed >', '0');
            if ($filter['courier_billed'] == 'no')
                $this->slave->where('tbl_order_shipping.courier_billed <=', '0');
        }

        if (!empty($filter['account_manager_id'])) {
            $this->slave->where('tbl_users.account_manager_id', $filter['account_manager_id']);
        }
        if (!empty($filter['sale_manager_id'])) {
            $this->slave->where_in('tbl_users.sale_manager_id', array_map('intval', $filter['sale_manager_id']));
        }
        if (!empty($filter['accout_manager_in'])) {
            $this->slave->where_in('tbl_users.account_manager_id', array_map('intval', $filter['accout_manager_in']));
        }

        $this->slave->group_by('tbl_order_shipping.ship_status');

        $this->slave->join('orders', 'orders.id = tbl_order_shipping.order_id');
        $this->slave->join('order_products', 'order_products.order_id = orders.id', 'LEFT');
        $this->slave->order_by('tbl_order_shipping.created', 'desc');
        $this->slave->join('users', 'tbl_users.id = tbl_order_shipping.user_id');
        $this->slave->join("(SELECT id,fname, lname FROM tbl_users where is_admin = '1') admin_users", 'admin_users.id = tbl_users.account_manager_id', 'left');
        $q = $this->slave->get($this->table);
        return $q->result();
    }

    function markPickupRequested($ship_ids = array())
    {
        if (empty($ship_ids))
            return false;

        $this->db->where_in('id', $ship_ids);
        $this->db->where('ship_status', 'booked');
        $this->db->set('ship_status', 'Pending Pickup');
        $this->db->update($this->table);
        return true;
    }

    function getByAWB($awb = false)
    {
        if (!$awb)
            return false;

        $this->db->where('awb_number', $awb);
        $this->db->limit(1);
        $q = $this->db->get($this->table);
        return $q->row();
    }

    function getByIDBulk($ids = false)
    {
        if (!$ids)
            return false;

        $this->db->where_in('id', $ids);
        $q = $this->db->get($this->table);
        return $q->result();
    }

    function checkReceiptsBulk($awbs = array(), $courier_id = false)
    {
        if (empty($courier_id))
            return false;

        $this->db->where_in('awb_number', $awbs);
        $this->db->where_in('courier_id', $courier_id);
        $q = $this->db->get($this->table);
        return $q->result();
    }

    function updateReceiptBYAwb($data = false)
    {
        if (!$data)
            return false;

        $this->db->update_batch($this->table, $data, 'id');

        return true;
    }

    function updateBilledAmountbyAWB($data = false)
    {
        if (!$data)
            return false;

        $this->db->where('courier_billed', '0');
        $this->db->update_batch($this->table, $data, 'awb_number');

        return true;
    }

    function getByReceiptID($id = false)
    {
        if (!$id)
            return false;

        $this->db->select('tbl_users.id as user_id, tbl_users.fname as user_fname, tbl_users.lname as user_lname, tbl_users.company_name as user_company, tbl_order_shipping.*');

        //$this->db->join('orders', 'orders.id = tbl_order_shipping.order_id', 'LEFT');
        $this->db->join('users', 'tbl_users.id = tbl_order_shipping.user_id', 'LEFT');

        $this->db->where('receipt_id', $id);
        $q = $this->db->get($this->table);
        return $q->result();
    }

    function sellerRecordsbyreceiptID($id = false)
    {
        if (!$id)
            return false;

        $this->db->select('tbl_users.fname as user_fname, tbl_users.lname as user_lname, tbl_users.company_name as user_company, sum(receipt_amount) as receipt_amount');
        $this->db->where('receipt_id', $id);
        $this->db->join('orders', 'orders.id = tbl_order_shipping.order_id', 'LEFT');
        $this->db->join('users', 'tbl_users.id = orders.user_id', 'LEFT');

        $this->db->group_by('tbl_users.id');
        $q = $this->db->get($this->table);
        return $q->result();
    }

    function sellerDues()
    {
        $this->db->select("tbl_users.id as user_id, tbl_users.fname as user_fname, tbl_users.lname as user_lname, tbl_users.company_name as user_company, sum(tbl_order_shipping.order_total_amount) as user_total");

        $this->db->where('tbl_order_shipping.ship_status', 'delivered');
        $this->db->where("tbl_order_shipping.receipt_id", '0');
        $this->db->where('tbl_order_shipping.payment_type', 'COD');
        // $this->db->where('orders.order_payment_type', 'COD');
        $this->db->where('tbl_order_shipping.created > ', strtotime('first day of -6 months midnight'));

       // $this->db->join('orders', 'orders.id = tbl_order_shipping.order_id', 'LEFT');
        $this->db->join('users', 'tbl_users.id = tbl_order_shipping.user_id', 'LEFT');

        $this->db->group_by('tbl_users.id');

        $this->db->order_by('user_total', 'desc');
        $q = $this->db->get($this->table);
        return $q->result();
    }

    function SellerAwbwiseShipmentReport($seller_ids)
    {
      
        $this->db->select("tbl_order_shipping.id,tbl_order_shipping.order_total_amount, tbl_order_shipping.awb_number, tbl_order_shipping.created, tbl_order_shipping.user_id, tbl_order_shipping.courier_id,courier.name as courier_name, tbl_users.fname as user_fname, tbl_users.lname as user_lname, tbl_users.company_name as user_company,tbl_order_shipping.delivered_time");
        $this->db->where('tbl_order_shipping.ship_status', 'delivered');
        $this->db->where("tbl_order_shipping.receipt_id", '0');
        $this->db->where('tbl_order_shipping.payment_type', 'COD');
        // $this->db->where('orders.order_payment_type', 'COD');
        $this->db->where('tbl_order_shipping.created > ', strtotime('first day of -6 months midnight'));
        $this->db->where_in('tbl_order_shipping.user_id', $seller_ids);

       // $this->db->join('orders', 'orders.id = tbl_order_shipping.order_id', 'LEFT');
        $this->db->join('courier', 'courier.id = tbl_order_shipping.courier_id', 'LEFT');
        $this->db->join('users', 'tbl_users.id = tbl_order_shipping.user_id', 'LEFT');

        $this->db->order_by('tbl_order_shipping.order_total_amount', 'desc');

        $q = $this->db->get($this->table);
        return $q->result();
    }

    function courierDues()
    {
        $this->slave->select('courier.id as courier_id, courier.name as courier_name, sum(tbl_order_shipping.order_total_amount) as due_total');

        $this->slave->where('tbl_order_shipping.ship_status', 'delivered');
        $this->slave->where("tbl_order_shipping.receipt_id", 0);
        $this->slave->where('tbl_order_shipping.payment_type', 'COD');
        $this->slave->where('tbl_order_shipping.created > ', strtotime('first day of -6 months midnight'));

        $this->slave->join('courier', 'courier.id = tbl_order_shipping.courier_id', 'LEFT');
        $this->slave->group_by('tbl_order_shipping.courier_id');
        $this->slave->order_by('due_total', 'desc');
        $q = $this->slave->get($this->table . ' force INDEX (by_ship_status_created) ');
    
        return $q->result();
    }

    function CourierAwbwiseShipmentReport($courier_ids)
    {  
        $this->slave->select("tbl_order_shipping.id, tbl_order_shipping.awb_number, tbl_order_shipping.created, tbl_order_shipping.user_id, tbl_order_shipping.courier_id,courier.name as courier_name, tbl_users.fname as user_fname, tbl_users.lname as user_lname, tbl_users.company_name as user_company, tbl_order_shipping.order_total_amount,tbl_order_shipping.delivered_time");

        $this->slave->where('tbl_order_shipping.ship_status', 'delivered');
        $this->slave->where("tbl_order_shipping.receipt_id", '0');
        $this->slave->where('tbl_order_shipping.payment_type', 'COD');
        $this->slave->where_in('tbl_order_shipping.courier_id', $courier_ids);   
        $this->slave->where('tbl_order_shipping.created > ', strtotime('first day of -6 months midnight'));

        $this->slave->join('courier', 'courier.id = tbl_order_shipping.courier_id', 'LEFT');
        $this->slave->join('users', 'tbl_users.id = tbl_order_shipping.user_id', 'LEFT');
        $this->slave->order_by('tbl_order_shipping.order_total_amount', 'desc');

        $q = $this->slave->get($this->table . ' force INDEX (by_ship_status_created) ');
     
        return $q->result();
    }

    function sellerPendingTotals($filters = array())
    {
        
        $this->db->select('tbl_users.id as user_id, tbl_users.fname as user_fname, tbl_users.lname as user_lname, tbl_users.company_name as user_company, tbl_users.wallet_balance as wallet_balance, tbl_users.remittance_cycle as remittance_cycle, tbl_users.remittance_on_hold_amount, '
            . "sum(case when (receipt_amount > '0' and receipt_id != 0 and remittance_id = 0 ) then tbl_order_shipping.order_total_amount else 0 end) as receipt_uploaded,"
            . "sum(case when (tbl_order_shipping.ship_status = 'delivered' and tbl_order_shipping.receipt_id = 0 and tbl_order_shipping.payment_type = 'COD'  ) then tbl_order_shipping.order_total_amount else 0 end) as seller_expected,"
            . "sum(case when (tbl_order_shipping.ship_status = 'delivered' and tbl_order_shipping.receipt_id = 0 and tbl_order_shipping.payment_type = 'COD' and tbl_order_shipping.remittance_id != 0  ) then tbl_order_shipping.order_total_amount else 0 end) as early_paid,"
            . "sum(case when (tbl_order_shipping.ship_status = 'delivered' and tbl_order_shipping.remittance_id = 0 and tbl_order_shipping.payment_type = 'COD' and tbl_order_shipping.delivered_time <= UNIX_TIMESTAMP() - 24*60*60*(tbl_users.remittance_cycle)  ) then tbl_order_shipping.order_total_amount else 0 end) as remittance_cycle_total,"
            . "sum(case when (tbl_order_shipping.ship_status = 'delivered' and tbl_order_shipping.remittance_id = 0 and tbl_order_shipping.payment_type = 'COD' ) then tbl_order_shipping.order_total_amount else 0 end) as total_cod_due");

        $this->db->where('tbl_users.freeze_remittance', '0');
        $this->db->where('tbl_order_shipping.payment_type', 'COD');
        if (!empty($filters['seller_id'])) {
            $this->db->where_in('tbl_order_shipping.user_id', $filters['seller_id']);
        }

        if (!empty($filters['seller_ids'])) {
            $this->db->where_in('tbl_order_shipping.user_id', $filters['seller_ids']);
        }
        if(isset($filters['ignore_seller_id'])){
            if(count($filters['ignore_seller_id']) > 0 ){
                $this->db->where_not_in('tbl_order_shipping.user_id',$filters['ignore_seller_id']); 
            }
        }
       

        if (!empty($filters['remittance_cycles'])) {
            $this->db->where_in('tbl_users.remittance_cycle', $filters['remittance_cycles']);
        }

        $this->db->join('users', 'tbl_users.id = tbl_order_shipping.user_id', 'LEFT');

        $this->db->group_by('tbl_users.id');

        $this->db->having('receipt_uploaded >', '0');
        $this->db->or_having('seller_expected >', '0');
        $this->db->or_having('early_paid >', '0');
        $this->db->or_having('remittance_cycle_total >', '0');

        $this->db->order_by('remittance_cycle_total', 'desc');
        $q = $this->db->get($this->table);
        return $q->result();
    }

    function sellerRemittanceAnalysis()
    {
        //first get seller last shipment date
        $this->slave->where_not_in('tbl_order_shipping.ship_status', array('new', 'booked', 'cancelled', 'pending pickup'));
        $this->slave->select("tbl_order_shipping.user_id as user_id, DATE_FORMAT(FROM_UNIXTIME(MAX(tbl_order_shipping.created)), '%Y-%m-%d') as last_shipment_date");
        $this->slave->group_by("tbl_order_shipping.user_id");
        $this->slave->from('order_shipping');

        $join_clouse = $this->slave->get_compiled_select();

        $this->slave->select(
            'tbl_users.id as user_id, tbl_users.fname as user_fname,
         tbl_users.lname as user_lname,
          tbl_users.company_name as user_company, tbl_users.wallet_balance as wallet_balance,'
                . "sum(case when (tbl_order_shipping.ship_status = 'delivered') then tbl_order_shipping.order_total_amount else 0 end) as total_delivered_value,"
                . "sum(case when (tbl_order_shipping.ship_status = 'delivered' and tbl_order_shipping.payment_type = 'COD') then tbl_order_shipping.order_total_amount else 0 end) as cod_delivered_value,"
                . "sum(case when (tbl_order_shipping.remittance_id != 0) then tbl_order_shipping.order_total_amount else 0 end) as remitted_amount,"
                . "sum(case when (tbl_order_shipping.fees_refunded = '0') then (tbl_order_shipping.total_fees + tbl_order_shipping.extra_weight_charges + tbl_order_shipping.rto_charges - tbl_order_shipping.cod_reverse_amount + tbl_order_shipping.rto_extra_weight_charges) else 0 end) as total_freight,"
                . "sum(case when (tbl_order_shipping.ship_status IN ('in transit','out for delivery','exception')) then 1 else 0 end) as in_transit_shipments,"
                . "sum(case when (tbl_order_shipping.ship_status = 'delivered' and tbl_order_shipping.remittance_id = 0 and tbl_order_shipping.payment_type = 'COD' and tbl_order_shipping.delivered_time <= " . (strtotime('- 7 days midnight') - 1) . "  ) then tbl_order_shipping.order_total_amount else 0 end) as projected_remittance,"
                . "sum(case when (tbl_order_shipping.ship_status = 'delivered' and tbl_order_shipping.receipt_id = 0 and tbl_order_shipping.payment_type = 'COD'  ) then tbl_order_shipping.order_total_amount else 0 end) as seller_expected,"
                . "sum(case when (tbl_order_shipping.ship_status = 'delivered' and tbl_order_shipping.receipt_id = 0 and tbl_order_shipping.payment_type = 'COD' and tbl_order_shipping.remittance_id != 0  ) then tbl_order_shipping.order_total_amount else 0 end) as early_paid,"
                . " sd.last_shipment_date as last_shipment_date"
        );

        $this->slave->join("({$join_clouse}) sd", 'sd.user_id = tbl_order_shipping.user_id', 'LEFT');
        //$this->slave->join('orders', 'orders.id = tbl_order_shipping.order_id', 'LEFT');
        $this->slave->join('users', 'tbl_users.id = tbl_order_shipping.user_id', 'LEFT');

        $this->slave->group_by('tbl_users.id');

        $this->slave->order_by('total_delivered_value', 'desc');
        $q = $this->slave->get($this->table);
        //pr($this->slave->last_query());die;
        return $q->result();
    }

    function payableAmountOfUser($user_id = false)
    {
        if (!$user_id)
            return false;

        $this->db->select("sum(order_amount) as order_amount");

        $this->db->where('orders.user_id', $user_id);
        $this->db->where("remittance_id", '0');
        $this->db->where('receipt_id >', '0');
        $this->db->join('orders', 'orders.id = tbl_order_shipping.order_id', 'LEFT');
        $q = $this->db->get($this->table);
        return $q->row();
    }

    function rechargeableShipmentsOfUser($user_id = false)
    {
        if (!$user_id)
            return false;

        $this->db->select("tbl_order_shipping.id as id, orders.order_amount as order_amount");

        $this->db->where('orders.user_id', $user_id);
        $this->db->where("remittance_id", '0');
        $this->db->where('orders.order_payment_type', 'COD');
        $this->db->where("tbl_order_shipping.ship_status", 'delivered');

        $this->db->join('orders', 'orders.id = tbl_order_shipping.order_id', 'LEFT');
        $q = $this->db->get($this->table);
        return $q->result();
    }

    function payableShipmentsOfUser($user_id = false, $shipment_ids = array())
    {
        if (empty($user_id) && empty($shipment_ids))
            return false;

        $this->db->select("tbl_order_shipping.*, courier.name as courier_name, orders.order_amount as order_amount, orders.user_id as user_id");

        if ($user_id)
            $this->db->where('orders.user_id', $user_id);

        if (!empty($shipment_ids))
            $this->db->where_in('tbl_order_shipping.id', $shipment_ids);

        $this->db->where("remittance_id", '0');
        $this->db->where('receipt_id !=', '0');
        $this->db->where('orders.order_payment_type', 'COD');
        $this->db->join('orders', 'orders.id = tbl_order_shipping.order_id');

        $this->db->join('courier', 'courier.id = tbl_order_shipping.courier_id');

        $this->db->order_by('tbl_order_shipping.created', 'asc');

        $q = $this->db->get($this->table);
        return $q->result();
    }

    function expectedShipmentsOfUser($user_id = false, $shipment_ids = array())
    {
        if (empty($user_id) && empty($shipment_ids))
            return false;

        $this->db->select("tbl_order_shipping.*, courier.name as courier_name");

        if ($user_id)
            $this->db->where('tbl_order_shipping.user_id', $user_id);

        if (!empty($shipment_ids))
            $this->db->where_in('tbl_order_shipping.id', $shipment_ids);

        $this->db->where("tbl_order_shipping.ship_status", 'delivered');
        $this->db->where("remittance_id", '0');
        $this->db->where('receipt_id', '0');
        $this->db->where('tbl_order_shipping.payment_type', 'COD');
        //$this->db->join('orders', 'orders.id = tbl_order_shipping.order_id');

        $this->db->join('courier', 'courier.id = tbl_order_shipping.courier_id');

        $this->db->order_by('tbl_order_shipping.delivered_time', 'asc');

        $q = $this->db->get($this->table);
        return $q->result();
    }

    function remittanceCycleAWbs($user_id = false, $shipment_ids = array())
    {
        if (empty($user_id) && empty($shipment_ids))
            return false;

        $this->db->select("tbl_order_shipping.*, courier.name as courier_name");

        if ($user_id)
            $this->db->where('tbl_order_shipping.user_id', $user_id);

        if (!empty($shipment_ids))
            $this->db->where_in('tbl_order_shipping.id', $shipment_ids);

        $this->db->where("tbl_order_shipping.ship_status", 'delivered');
        $this->db->where("remittance_id", '0');
        $this->db->where('tbl_order_shipping.payment_type', 'COD');
        $this->db->where('tbl_order_shipping.delivered_time <= UNIX_TIMESTAMP() - 24*60*60*(tbl_users.remittance_cycle)');

        $this->db->join('users', 'tbl_users.id = tbl_order_shipping.user_id');

        $this->db->join('courier', 'courier.id = tbl_order_shipping.courier_id');

        $this->db->order_by('tbl_order_shipping.delivered_time', 'asc');

        $q = $this->db->get($this->table);
        return $q->result();
    }

    function remittanceShipmentList($shipment_ids = array())
    {
        if (empty($shipment_ids))
            return false;

        $this->db->select("tbl_order_shipping.*, courier.name as courier_name");

        if (!empty($shipment_ids))
            $this->db->where_in('tbl_order_shipping.id', $shipment_ids);

        $this->db->where("tbl_order_shipping.ship_status", 'delivered');
        $this->db->where("remittance_id", '0');
        $this->db->where('tbl_order_shipping.payment_type', 'COD');

        //$this->db->join('orders', 'orders.id = tbl_order_shipping.order_id');
        $this->db->join('users', 'tbl_users.id = tbl_order_shipping.user_id');

        $this->db->join('courier', 'courier.id = tbl_order_shipping.courier_id');

        $this->db->order_by('tbl_order_shipping.delivered_time', 'asc');

        $q = $this->db->get($this->table);
        return $q->result();
    }

    function updateRemittanceID($shipping_ids = array(), $remittance_id = false)
    {
        if (empty($shipping_ids) || !$remittance_id)
            return false;

        $this->db->where_in('id', $shipping_ids);
        $this->db->set('remittance_id', $remittance_id);
        $this->db->update($this->table);
        return true;
    }

    function shipmentDetailsBulkIds($limit = 50, $offset = 0, $filter = array())
    {
        $this->db->select("tbl_order_shipping.id as shipping_id, "
            . "tbl_order_shipping.created as shipping_created, "
            . "orders.order_no as order_id, "
            . "courier.name as courier_name, "
            . "tbl_order_shipping.awb_number as awb_number, tbl_order_shipping.receipt_amount as receipt_amount, tbl_order_shipping.delivered_time as delivered_time, orders.order_amount as order_amount ");

        if (!empty($filter['shipment_ids'])) {
            $this->db->where_in('tbl_order_shipping.id', $filter['shipment_ids']);
        }

        $this->db->limit($limit);
        $this->db->offset($offset);

        $this->db->where('tbl_order_shipping.ship_status !=', 'cancelled');

        $this->db->join('orders', 'orders.id = tbl_order_shipping.order_id');
        $this->db->join('courier', 'courier.id = tbl_order_shipping.courier_id');

        $this->db->join('users', 'tbl_users.id = orders.user_id', 'LEFT');
        $this->db->order_by('tbl_order_shipping.created', 'desc');

        $q = $this->db->get($this->table);

        return $q->result();
    }

    function getShipmentsforWeightRec($filter = array())
    {
        $this->db->select("orders.*, group_concat(tbl_order_products.product_name)  as products, tbl_order_shipping.awb_number as awb_number, tbl_order_shipping.id as shipping_id, tbl_order_shipping.ship_status as ship_status, tbl_order_shipping.message as ship_message, tbl_order_shipping.created as shipping_created, courier.name as courier_name, courier.code as courier_code,tbl_users.fname as user_fname, tbl_users.lname as user_lname,tbl_users.company_name,tbl_users.id as userid, tbl_order_shipping.charged_weight as charged_weight, tbl_order_shipping.extra_weight_charges as extra_weight_charges, tbl_order_shipping.courier_billed as courier_billed");

        if (!empty($filter['shipment_ids'])) {
            $this->db->where_in('tbl_order_shipping.awb_number', $filter['shipment_ids']);
        }

        $this->db->group_by('order_products.order_id, tbl_order_shipping.id');

        $this->db->join('orders', 'orders.id = tbl_order_shipping.order_id');

        $this->db->join('order_products', 'order_products.order_id = orders.id');

        $this->db->join('courier', 'courier.id = tbl_order_shipping.courier_id');

        $this->db->join('users', 'tbl_users.id = orders.user_id');

        $this->db->order_by('tbl_order_shipping.created', 'desc');

        $q = $this->db->get($this->table);
        return $q->result();
    }

    function expectedDues($filter = array())
    {
        if (empty($filter))
            return false;

        $this->slave->select("tbl_order_shipping.*, courier.name as courier_name, tbl_users.fname as user_fname, tbl_users.lname as user_lname, tbl_users.company_name as user_company");

        if (!empty($filter['courier_id'])) {
            $this->slave->where('tbl_order_shipping.courier_id', $filter['courier_id']);
        }

        if (!empty($filter['seller_id'])) {
            $this->slave->where('tbl_order_shipping.user_id', $filter['seller_id']);
        }

        $this->slave->where('tbl_order_shipping.ship_status', 'delivered');
        $this->slave->where("tbl_order_shipping.receipt_id", '0');
        $this->slave->where('tbl_order_shipping.payment_type', 'COD');

        //$this->slave->join('orders', 'orders.id = tbl_order_shipping.order_id');

        $this->slave->join('courier', 'courier.id = tbl_order_shipping.courier_id');

        $this->slave->join('users', 'tbl_users.id = tbl_order_shipping.user_id');

        $this->slave->order_by('tbl_order_shipping.created', 'desc');

        $this->slave->from($this->table);
        return $query = $this->slave->get_compiled_select();
    }

    function calculateTotalPayableRemittance($user_id = false)
    {
        $this->db->select("sum(o.order_amount) as remittance_due");
        $this->db->where('s.user_id', $user_id);

        $this->db->where('s.ship_status', 'delivered');
        $this->db->where('s.remittance_id <=', '0');

        $this->db->where('o.order_payment_type', 'COD');

        $this->db->join('orders as o', 'o.id = s.order_id');

        $q = $this->db->get('order_shipping as s');
        
        return $q->row()->remittance_due;
    }

    public function getOrderId($ship_id)
    {
        $this->db->select("order_id");
        $this->db->from('order_shipping');
        $this->db->where('tbl_order_shipping.id', $ship_id);
        return $this->db->get()->row()->order_id;
    }

    public  function getLastShipmentActivity($user_id)
    {
        $this->db->select('created');
        $where_in = array('new', 'booked', 'cancelled', 'pending pickup');
        $this->db->where('tbl_order_shipping.user_id', $user_id);
        $this->db->where_not_in('tbl_order_shipping.ship_status', $where_in);
        $this->db->order_by('tbl_order_shipping.created', 'desc');
        $this->db->limit(1);
        $this->db->offset(0);
        $this->db->from('order_shipping');
        return $this->db->get()->row();
    }

    function sellerPendingRemittanceAwbwise($limit = 100000, $offset = 0, $filter = null)
    {
        $dateval = strtotime("tomorrow") - 1;
        if (!empty($filter["filter"]["date_val"])) {
            $dateval = strtotime($filter["filter"]["date_val"] . " 23:59:59");
        }
        if (!empty($filter['seller_id'])) {
            $this->slave->where_in('tbl_order_shipping.user_id', $filter['seller_id']);
        }
        //pr(implode(',',$filter['seller_ids']),1);
        if (!empty(($filter['seller_ids']))) {
            $this->slave->where_in('tbl_order_shipping.user_id',$filter['seller_ids']);
        }
        if(!empty($filter['ignore_seller_id'])){
            if(count($filter['ignore_seller_id']) > 0 ){
                $this->slave->where_not_in('tbl_order_shipping.user_id',$filter['ignore_seller_id']);
            }
        }
        if (!empty($filter['remittance_cycles'])) {
            $this->slave->where_in('tbl_users.remittance_cycle', $filter['remittance_cycles']);
        }
        if (!empty($filter['operation_verify'])) {
            $this->slave->where_in('awb_operation_verify.ops_verify', $filter['operation_verify']);
            $this->slave->join('awb_operation_verify', 'awb_operation_verify.shipping_id = tbl_order_shipping.id');
        }
        $this->slave->select('tbl_order_shipping.awb_number, tbl_users.id as user_id, CONCAT(tbl_users.fname, " ", ifnull(tbl_users.lname,"")) as user_name, tbl_users.company_name as user_company, tbl_users.wallet_balance as wallet_balance, tbl_users.remittance_cycle as remittance_cycle, tbl_users.remittance_on_hold_amount, tbl_order_shipping.order_total_amount, tbl_order_shipping.ship_status, tbl_order_shipping.payment_type, courier.name as courier_name,courier.status as courier_status,tbl_order_shipping.receipt_id as receiptId,tbl_order_shipping.receipt_amount as receiptAmount,tbl_order_shipping.delivered_time as delivered_time,tbl_order_shipping.pickup_time as pickup_time');
        $this->slave->where('tbl_order_shipping.ship_status', 'delivered');
        $this->slave->where('tbl_order_shipping.remittance_id', '0');
        $this->slave->where('tbl_order_shipping.payment_type', 'COD');
        $this->slave->where('tbl_order_shipping.delivered_time <= (' . $dateval . ' - 24*60*60*(tbl_users.remittance_cycle))');
        $this->slave->where('tbl_users.freeze_remittance', '0');
        $this->slave->from('order_shipping FORCE INDEX (by_delivered_time)');
        $this->slave->join('users', 'tbl_users.id = tbl_order_shipping.user_id');
        $this->slave->join('courier', 'courier.id = tbl_order_shipping.courier_id');
        // $this->slave->order_by('tbl_order_shipping.id');
        // $this->slave->limit($limit);
        // $this->slave->offset($offset);
        return $query = $this->slave->get_compiled_select();
    }

    function checkvalidorderid($order_id)
    {
        if (!$order_id)
            return false;
        $this->db->select("count(*) as total");
        $this->db->where('id', $order_id);
        $q = $this->db->get("orders");
        return $q->result();
    }

    function getdatafromorderid($order_id)
    {
        if (!$order_id)
            return false;
        $this->db->select("shipping_phone, shipping_address, shipping_address_2, shipping_city, shipping_state, shipping_country, shipping_zip");
        $this->db->where('id', $order_id);
        $q = $this->db->get("orders");
        return $q->result();
    }

    function create($save = array())
    {
        if (empty($save))
            return false;
        $save['created'] = time();
        return $this->db->insert('ekart_logs', $save);
    }

    function updaterec($id = false, $save = array())
    {
        if (empty($save) || empty($id))
            return false;

        $save['modified'] = time();

        $this->db->set($save);
        $this->db->where('id', $id);
        $this->db->update('orders');
        return true;
    }
    function getByUserIDNew($limit = 50, $offset = 0, $filter = array())
    {
           $user_id=$awb_no='';
            if (!empty($filter['seller_id'])) {
                $user_id=" and tbl_order_shipping.user_id=". $filter['seller_id'];
        }

        if (!empty($filter['date_type_field']) && $filter['date_type_field'] == 'delivered') {
            if (!empty($filter['start_date'])) {
                $statrt_date=" tbl_order_shipping.delivered_time >= '" . $filter['start_date'] . "'";
            }
            if (!empty($filter['end_date'])) {
                $end_date=" tbl_order_shipping.delivered_time <= '" . $filter['end_date'] . "'";
            }
        } else {
            if (!empty($filter['start_date'])) {
                $statrt_date="'".$filter['start_date']."'" ;
            }
            if (!empty($filter['end_date'])) {
               $end_date="'".$filter['end_date']."'" ;
            }
        }

        // echo "start date--".$statrt_date;
        // echo "end date--".$end_date; die;

       
      

        $allAwb = array();
        if (!empty($filter['awb_no'])) {
            foreach($filter['awb_no'] as $awb)
            {
              $asdsad="'$awb'";
              array_push($allAwb,$asdsad);
            }
          
            $awb_no=" and tbl_order_shipping.awb_number in(".implode(",",$allAwb).")";
        }


        $query = $this->slave->query("SELECT awb_number,`order_id`,package_weight,courier_name,ship_status,shipping_created,charged_weight,extra_weight_charges,courier_fees,cod_fees,total_fees,fees_refunded,rto_extra_weight_charges,rto_charges,cod_reverse_amount,courier_billed,insurance_price,company_name  FROM (SELECT `tbl_order_shipping`.`order_id`,`tbl_order_shipping`.`awb_number` as `awb_number`, `tbl_order_shipping`.`ship_status` as `ship_status`,`tbl_orders`.`package_weight`,(select `tbl_courier`.`name` from `tbl_courier` where `tbl_courier`.`id` = `tbl_order_shipping`.`courier_id` ) as `courier_name`,
        `tbl_order_shipping`.`created` as `shipping_created`,  `tbl_order_shipping`.`charged_weight` as `charged_weight`,
        `tbl_order_shipping`.`extra_weight_charges` as `extra_weight_charges`, `tbl_order_shipping`.`courier_fees` as `courier_fees`,
         `tbl_order_shipping`.`cod_fees` as `cod_fees`, `tbl_order_shipping`.`total_fees` as `total_fees`,
         `tbl_order_shipping`.`fees_refunded` as `fees_refunded`, `tbl_order_shipping`.`rto_extra_weight_charges` as `rto_extra_weight_charges`,
         `tbl_order_shipping`.`rto_charges` as `rto_charges`, `tbl_order_shipping`.`cod_reverse_amount` as `cod_reverse_amount`,
         `tbl_order_shipping`.`courier_billed` as `courier_billed`, `tbl_order_shipping`.`insurance_price`,tbl_users.company_name as company_name
        FROM `tbl_order_shipping`
        LEFT JOIN `tbl_orders` ON `tbl_order_shipping`.`order_id` = `tbl_orders`.`id`
        LEFT JOIN `tbl_order_products` ON `tbl_orders`.`id` = `tbl_order_products`.`order_id`
        LEFT JOIN `tbl_users` ON `tbl_users`.`id` = `tbl_orders`.`user_id`
        WHERE `tbl_order_shipping`.`created` >= $statrt_date AND `tbl_order_shipping`.`created` <= $end_date $awb_no $user_id
       
        ORDER BY `tbl_order_shipping`.`created` DESC LIMIT ".$limit." offset $offset ) AS t1 GROUP BY order_id");
        //echo "new query====> ".$this->slave->last_query(); die;
        return $data= $query->result();
    }

    function insertEdShipment($save = array())
    {
        if (empty($save))
            return false;

        $this->db->insert('escalation_delivery_shipments', $save);
        return $this->db->insert_id();
    }

    function bulkInsertEdShipment($save = array())
    {
        if (empty($save))
            return false;

        $this->db->insert_batch('escalation_delivery_shipments', $save);
        return true;
    }

    public function upload_shipment_bill($save= array())
    {
        if (empty($save))

            return false;

        $save['created'] = time();
        $save['modified'] = time();

        $this->db->insert('international_shipment_bill', $save);
        return $this->db->insert_id();
    }

    public function get_shipment_bill($ship_id)
    {
        if (!$ship_id)
            return false;
        $this->db->where('ship_id', $ship_id);
        $this->db->order_by('id', 'desc');
        $q = $this->db->get('international_shipment_bill');
        return $q->result();
    }

    function getCourierWiseAwbCount()
    {
        $this->slave->where('remittance_id','0');
        $this->slave->where('tbl_order_shipping.ship_status', 'delivered');
        $this->slave->where('tbl_order_shipping.payment_type', 'COD');
        $this->slave->select('tbl_order_shipping.courier_id,(count(DISTINCT tbl_order_shipping.id)-count(`awb_operation_verify`.`shipping_id`)) as total');
        $this->slave->join('awb_operation_verify', '(`tbl_order_shipping`.`id` = `awb_operation_verify`.`shipping_id` AND `ops_verify`!="0")', 'left');
        $this->slave->group_by('tbl_order_shipping.courier_id');
        $this->slave->order_by('total', 'desc');
        $q = $this->slave->get($this->table);
        //pr($this->slave->last_query(),1);
        return $q->result();
    }

    function getCourierWiseAwbRecords($filter = array())
    {
        if (!empty($filter['courier_id'])) {
            $this->slave->where('tbl_order_shipping.courier_id', $filter['courier_id']);
        }

        $this->slave->where('tbl_order_shipping.remittance_id','0');
        $this->slave->where('tbl_order_shipping.ship_status', 'delivered');
        $this->slave->where('tbl_order_shipping.payment_type', 'COD');
        $this->slave->select('
                            courier.name as courier_name,
                            tbl_users.id as seller_id,
                            tbl_users.company_name,
                            tbl_order_shipping.awb_number as awb_number,
                            tbl_order_shipping.id as shipping_id,
                            tbl_order_shipping.ship_status as ship_status,
                            tbl_order_shipping.order_total_amount as total_amount,
                            tbl_order_shipping.created as shipping_created,
                            awb_operation_verify.`ops_verify`,
                            tbl_order_shipping.delivered_time as delivered_time');
        $this->slave->join('users', 'tbl_users.id = tbl_order_shipping.user_id');
        $this->slave->join('courier', 'tbl_order_shipping.courier_id = courier.id', 'left');
        $this->slave->join('awb_operation_verify', '(`tbl_order_shipping`.`id` = `awb_operation_verify`.`shipping_id`)', 'left');
        $this->slave->order_by('tbl_order_shipping.courier_id', 'asc');
        $q = $this->slave->get($this->table);
        return $q->result();
    }  
    
    function getCourierWiseUnverifiedAwbCount($ops_status='2')
    {
        $this->slave->where('ops_verify',$ops_status);
        $this->slave->select('awb_operation_verify.courier_id,count(awb_operation_verify.id) as total');
        $this->slave->group_by('awb_operation_verify.courier_id');
        $this->slave->order_by('total', 'desc');
        $q = $this->slave->get('awb_operation_verify');
        return $q->result();
    }

    function exportUnverifiedaAwsRecords($filter = array())
    {
        if (!empty($filter['courier_id'])) {
            $this->slave->where('tbl_order_shipping.courier_id', $filter['courier_id']);
        }
        if (!empty($filter['ops_verify'])) {
            $this->slave->where('awb_operation_verify.ops_verify', $filter['ops_verify']);
        }
        if (!empty($filter['awb_number'])) {
            $this->slave->where('awb_operation_verify.awb_number', $filter['awb_number']);
        }

        $this->slave->select('
                            courier.name as courier_name,
                            tbl_users.id as seller_id,
                            tbl_users.company_name,
                            tbl_order_shipping.awb_number as awb_number,
                            tbl_order_shipping.id as shipping_id,
                            tbl_order_shipping.ship_status as ship_status,
                            tbl_order_shipping.order_total_amount as total_amount,
                            tbl_order_shipping.created as shipping_created,
                            awb_operation_verify.`ops_verify`,
                            tbl_order_shipping.delivered_time as delivered_time');
        $this->slave->join('order_shipping', '`awb_operation_verify`.`shipping_id` = `tbl_order_shipping`.`id`');
        $this->slave->join('users', 'tbl_users.id = tbl_order_shipping.user_id');
        $this->slave->join('courier', 'tbl_order_shipping.courier_id = courier.id', 'left');
        $this->slave->order_by('tbl_order_shipping.courier_id', 'asc');
        $q = $this->slave->get('awb_operation_verify');
        return $q->result();
    }  


    function remittanceShipmentListByAWB($awb_nos = array())
    {
        if (empty($awb_nos))
            return false;

        $this->db->select("tbl_order_shipping.*, courier.name as courier_name,awb_operation_verify.ops_verify");

        if (!empty($awb_nos))
            $this->db->where_in('tbl_order_shipping.awb_number', $awb_nos);

        $this->db->where("tbl_order_shipping.ship_status", 'delivered');
        //$this->db->where("remittance_id", '0');
        $this->db->where('tbl_order_shipping.payment_type', 'COD');

        $this->db->join('users', 'tbl_users.id = tbl_order_shipping.user_id');
        $this->db->join('courier', 'courier.id = tbl_order_shipping.courier_id');
        $this->db->join('awb_operation_verify', 'tbl_order_shipping.awb_number = awb_operation_verify.awb_number','left');
        
        $this->db->order_by('tbl_order_shipping.delivered_time', 'asc');
        $q = $this->db->get($this->table);
        return $q->result();
    }

    function getCourierWeight($id = false)
{
    if (!$id)
        return false;

    $this->db->select("weight");
    $this->db->where('shipment_id', $id);
    $this->db->limit(1);
    $q = $this->db->get('shipment_tracking');
    return $q->row();
}

    function getCourierExpectedAmount($id = false)
    {
        $this->db->select("SUM(CASE WHEN (receipt_amount > '0' AND receipt_id != 0) THEN tbl_order_shipping.order_total_amount ELSE 0 END) AS receipt_uploaded, SUM(tbl_order_shipping.order_total_amount) AS courier_expected");
        $this->db->where('payment_type', 'COD');
        $this->db->where('ship_status', 'delivered');
        $this->db->limit(1);
        $q = $this->db->get('tbl_order_shipping');
        return $q->row();
    }
}