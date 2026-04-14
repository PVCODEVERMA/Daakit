<?php

class Wallet_model extends MY_model
{

    public function __construct()
    {
        parent::__construct();
        $this->history_table = 'wallet_history';

        $this->slave = $this->load->database('slave', TRUE);
    }

    function insert_history($save = array())
    {
        if (empty($save))
            return false;

        $save['created'] = time();

        $this->db->insert($this->history_table, $save);
        return $this->db->insert_id();
    }

    function fetchByUserID($limit = 50, $offset = 0, $filter = array())
    {

        $this->slave->select('wallet_history.*,users.fname,users.lname,users.company_name,users.id as userid,order_shipping.awb_number as awb_number, payments.gateway');

        if (!empty($filter['txn_for'])) {
            switch (strtolower($filter['txn_for'])) {
                case 'all_payments':
                    $this->slave->where_in('txn_for', array('cod', 'neft', 'recharge'));
                    break;
                case 'recharge_razorpay':
                    $this->slave->where_in('txn_for', 'recharge');
                    $this->slave->where('payments.gateway', 'razorpay');
                    break;
                case 'recharge_paytm':
                    $this->slave->where_in('txn_for', 'recharge');
                    $this->slave->where('payments.gateway', "paytm");
                    break;

                case 'recharge_hdfc':
                    $this->slave->where_in('txn_for', 'recharge');
                    $this->slave->where('payments.gateway', "hdfc_razorpay");
                    break;    

                case 'ivr_number':
                    $this->slave->where('txn_for', 'addon');
                    $this->slave->where('txn_ref', $filter['txn_for']);
                    break;
                case 'ivr_call':
                    $this->slave->where('txn_for', 'addon');
                    $this->slave->where('txn_ref', $filter['txn_for']);
                    break;
                case 'whatsapp':
                    $this->slave->where('txn_for', 'addon');
                    $this->slave->where('txn_ref', 'whatsapp');
                    break;
                case 'all_communication':
                    $this->slave->where_in('txn_for', ['sms','whatsapp','email','ivr']);
                    break;

                default:
                    $this->slave->where('txn_for', $filter['txn_for']);
            }
        }




        if (!empty($filter['shipment_id'])) {
            $this->slave->where('ref_id', $filter['shipment_id']);
            $this->slave->where('txn_for', 'shipment');
        }

        if (!empty($filter['seller_id'])) {
            $this->slave->where('users.id', $filter['seller_id']);
        }

        if (!empty($filter['start_date'])) {
            $this->slave->where("wallet_history.created >= '" . $filter['start_date'] . "'");
        }
        if (!empty($filter['end_date'])) {
            $this->slave->where("wallet_history.created <= '" . $filter['end_date'] . "'");
        }
        $this->slave->limit($limit);
        $this->slave->offset($offset);

        $this->slave->order_by('wallet_history.created', 'desc');
        $this->slave->join('payments', 'payments.id = wallet_history.ref_id', 'LEFT');
        $this->slave->join('users', 'users.id = wallet_history.user_id', 'LEFT');
        $this->slave->join('order_shipping', 'order_shipping.id = wallet_history.ref_id', 'LEFT');
        $q = $this->slave->get($this->history_table);

        return $q->result();
    }

    function fetchByUserIDCommunication($seller_id, $limit = 50, $offset = 0, $filter = array())
    {
        $this->slave->select("
            h.*,
            n.order_number,
            n.awb_number,
            n.response,
            n.sent_at,
            n.delivered_at,
            n.read_at,
            n.delivery_status,
            n.remarks,
            CASE 
                WHEN h.txn_for = 'whatsapp' 
                    THEN TIMESTAMPDIFF(SECOND, n.created_at, STR_TO_DATE(n.sent_at, '%Y-%m-%d %H:%i:%s'))
                WHEN h.txn_for IN ('sms', 'email') 
                    THEN TIMESTAMPDIFF(SECOND, n.created_at, STR_TO_DATE(n.delivered_at, '%Y-%m-%d %H:%i:%s'))
                ELSE NULL
            END AS aging_seconds
        ", false);

        // filters
        
        if (!empty($filter['txn_for'])) {
            switch (strtolower($filter['txn_for'])) {
                case 'all_communication':
                    $this->slave->where_in('h.txn_for', ['sms', 'whatsapp', 'email', 'ivr']);
                    break;
                default:
                    $this->slave->where('h.txn_for', $filter['txn_for']);
            }
        }

        if (!empty($filter['start_date'])) {
            $this->slave->where("h.created >=", $filter['start_date']);
        }

        if (!empty($filter['end_date'])) {
            $this->slave->where("h.created <=", $filter['end_date']);
        }

        $this->slave->limit($limit);
        $this->slave->offset($offset);

        $this->slave->where('h.user_id', $seller_id);

        // INNER JOIN instead of LEFT JOIN
        $this->slave->join(
            'tbl_notification_responses n',
            'h.request_id = n.request_id',
            'INNER',
            false
        );

        $this->slave->order_by('h.id', 'desc');

        $q = $this->slave->get('tbl_communication_wallet_history h');
        return $q->result();
    }

    function countByUserID($filter = array())
    {
        $this->slave->select('count(*) as total');

        if (!empty($filter['txn_for'])) {

            if (strtolower(trim($filter['txn_for'])) == "all_communication") {
                $this->slave->where_in('txn_for', array('sms','email','whatsapp','ivr'));
            }
            else if (strtolower(trim($filter['txn_for'])) == "all_payments") {
                $this->slave->where_in('txn_for', array('cod', 'neft', 'recharge'));
            } else if (strtolower(trim($filter['txn_for'])) == "razorpay" || strtolower(trim($filter['txn_for'])) == "paytm") {
                $this->slave->join('payments', 'payments.id = wallet_history.ref_id', 'LEFT');
                if (strtolower(trim($filter['txn_for'])) == "paytm") {
                    $this->slave->where('payments.gateway', "paytm");
                } else {
                    $this->slave->where('payments.gateway', "razorpay");
                }
                $this->slave->where_in('txn_for', array('recharge'));
            } elseif ($filter['txn_for'] == 'ivr_number' || $filter['txn_for'] == 'ivr_call') {
                $this->slave->where('txn_for', 'addon');
                $this->slave->where('txn_ref', $filter['txn_for']);
            } else {
                $this->slave->where('txn_for', $filter['txn_for']);
            }
        }
        if (!empty($filter['shipment_id'])) {
            $this->slave->where('ref_id', $filter['shipment_id']);
            $this->slave->where('txn_for', 'shipment');
        }
        if (!empty($filter['seller_id'])) {
            $this->slave->where('users.id', $filter['seller_id']);
        }
        if (!empty($filter['start_date'])) {
            $this->slave->where("wallet_history.created >= '" . $filter['start_date'] . "'");
        }
        if (!empty($filter['end_date'])) {
            $this->slave->where("wallet_history.created <= '" . $filter['end_date'] . "'");
        }
        //$this->slave->where('wallet_history.txn_for', 'recharge');
        $this->slave->join('users', 'users.id = wallet_history.user_id', 'LEFT');
        $this->slave->join('order_shipping', 'order_shipping.id = wallet_history.ref_id', 'LEFT');
        $q = $this->slave->get($this->history_table);
        // echo $this->slave->last_query();exit;
        return $q->row()->total;
    }

    function shippingChargesLogs($limit = 50, $offset = 0, $filter = array())
    {
        $this->db->select('wallet_history.id as wallet_id, wallet_history.notes as wallet_note, order_shipping.*, courier.name as courier_name,users.fname,users.lname,users.company_name');
        if (!empty($filter['awb_no'])) {
            $this->db->where_in('order_shipping.awb_number', $filter['awb_no']);
        }
        $this->db->limit($limit);
        $this->db->offset($offset);
        $this->db->where('wallet_history.shipment_id !=', '0');
        $this->db->order_by('wallet_history.created', 'desc');
        $this->db->join('order_shipping', 'order_shipping.id = wallet_history.shipment_id', 'LEFT');
        $this->db->join('courier', 'courier.id = order_shipping.courier_id', 'LEFT');
        $this->db->join('users', 'users.id = wallet_history.user_id', 'LEFT');
        $q = $this->db->get($this->history_table);
        return $q->result();
    }

    function countShippingChargesLogs($filter = array())
    {
        $this->db->select('count(*) as total');
        if (!empty($filter['awb_no'])) {
            $this->db->where_in('order_shipping.awb_number', $filter['awb_no']);
        }
        $this->db->where('wallet_history.shipment_id !=', '0');
        $this->db->order_by('wallet_history.created', 'desc');
        $this->db->join('order_shipping', 'order_shipping.id = wallet_history.shipment_id', 'LEFT');
        $this->db->join('courier', 'courier.id = order_shipping.courier_id', 'LEFT');
        $q = $this->db->get($this->history_table);
        return $q->row()->total;
    }

    function sellerWallerRechargeNAdjustments($user_id = false, $limit = 50, $offset = 0)
    {

        if (!$user_id)
            return false;


        $this->db->where_in('txn_for', array('recharge', 'neft', 'cod'));

        $this->db->where('user_id', $user_id);


        $this->db->order_by('wallet_history.created', 'desc');

        $q = $this->db->get($this->history_table);
        return $q->result();
    }

    function consolidated_wallet($filter = array())
    {

        $this->slave->select(
            "u.id as user_id,"
                . "u.fname as user_fname,"
                . "u.lname as user_lname,"
                . "u.company_name as company_name,"
                . "u.wallet_balance,"
                . "sum(case when (tbl_wallet_history.type = 'credit') then tbl_wallet_history.amount else 0 end) as credit_amount,"
                . "sum(case when (tbl_wallet_history.type = 'debit') then tbl_wallet_history.amount else 0 end) as debit_amount"
        );


        if (!empty($filter['seller_id'])) {
            $this->slave->where('u.id', $filter['seller_id']);
        }
        if (!empty($filter['start_date'])) {
            $this->slave->where("tbl_wallet_history.created >= '" . $filter['start_date'] . "'");
        }
        if (!empty($filter['end_date'])) {
            $this->slave->where("tbl_wallet_history.created <= '" . $filter['end_date'] . "'");
        }

        if (!empty($filter['txn_type'])) {
            $this->slave->where_in('tbl_wallet_history.txn_for', $filter['txn_type']);
        }
        if (!empty($filter['txn_ref_type'])) {
            $this->slave->where_in('tbl_wallet_history.txn_ref', $filter['txn_ref_type']);
        }
       
        $this->slave->group_by('u.id');

        $this->slave->join('users as u', 'u.id = tbl_wallet_history.user_id','LEFT');
        $this->slave->from($this->history_table . ' as tbl_wallet_history ');

        return $query =   $this->slave->get_compiled_select();
    }


    function exportDetailedWalletReport($filter = array())
    {
        if (!empty($filter['awb_no'])) {
            $this->slave->select('order_shipping.id');
            $this->slave->where_in('order_shipping.awb_number', array_map('trim', explode(',', $filter['awb_no'])));
            $this->slave->from('order_shipping');
            $where_clause = $this->slave->get_compiled_select();
            $this->slave->where("wallet_history.ref_id IN ($where_clause)");
            $this->slave->where("wallet_history.txn_for", "shipment");
        }

        $this->slave->select('wallet_history.*,users.fname,users.lname,users.company_name,users.id as user_id, order_shipping.id as shipment_id ,order_shipping.order_type , order_shipping.awb_number');

        if (!empty($filter['start_date'])) {
            $this->slave->where("wallet_history.created >= '" . $filter['start_date'] . "'");
        }
        if (!empty($filter['end_date'])) {
            $this->slave->where("wallet_history.created <= '" . $filter['end_date'] . "'");
        }
        if (!empty($filter['seller_id'])) {
            $this->slave->where('wallet_history.user_id', $filter['seller_id']);
        }

        if (!empty($filter['txn_type'])) {

            $this->slave->where_in('wallet_history.txn_for', $filter['txn_type']);
        }
        if (!empty($filter['order_type'])) {
            $this->slave->where_in('order_shipping.order_type', $filter['order_type']);
            
        }
        if (!empty($filter['txn_ref_type'])) {
            $this->slave->where_in('wallet_history.txn_ref', $filter['txn_ref_type']);
        }

       
        $this->slave->order_by('wallet_history.created', 'desc');
        $this->slave->join('users', 'users.id = wallet_history.user_id','LEFT');
        $this->slave->join('order_shipping', "wallet_history.txn_for='shipment' and order_shipping.id = wallet_history.ref_id",'LEFT');
        $this->slave->from($this->history_table);
 
        return  $query =   $this->slave->get_compiled_select();
    }

}
