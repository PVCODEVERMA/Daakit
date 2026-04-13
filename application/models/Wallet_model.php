<?php

class Wallet_model extends MY_model
{

    public function __construct()
    {
        parent::__construct();
        $this->table = 'wallet_history';
    }

    function insert_history($save = array())
    {
        if (empty($save))
            return false;

        $save['created'] = time();

        $this->db->insert($this->table, $save);
        return $this->db->insert_id();
    }

    function fetchByUserID($user_id = false, $limit = 50, $offset = 0, $filter = array())
    {
        if (!$user_id)
            return false;

        $this->db->select('wallet_history.*, order_shipping.awb_number as awb_number');

        if (!empty($filter['txn_for'])) {
            switch (strtolower($filter['txn_for'])) {
                case 'recharge_razorpay':
                    $this->db->where_in('txn_for', 'recharge');
                    $this->db->where('payments.gateway', 'razorpay');
                    break;
                case 'recharge_paytm':
                    $this->db->where_in('txn_for', 'recharge');
                    $this->db->where('payments.gateway', "paytm");
                    break;

                case 'recharge_hdfc':
                    $this->db->where_in('txn_for', 'recharge');
                    $this->db->where('payments.gateway', "hdfc_razorpay");
                    break;
                case 'all_communication':
                    $this->db->where_in('txn_for', ['sms','whatsapp','email','ivr']);
                    break;    
                default:
                    $this->db->where('txn_for', $filter['txn_for']);
            }
        }



        if (!empty($filter['txn_ref'])) {
            $this->db->where('txn_ref', $filter['txn_ref']);
        }

        if (!empty($filter['shipment_id'])) {
            $this->db->where('ref_id', $filter['shipment_id']);
            $this->db->where('txn_for', 'shipment');
        }

        if (!empty($filter['start_date'])) {
            $this->db->where("wallet_history.created >= '" . $filter['start_date'] . "'");
        }

        if (!empty($filter['end_date'])) {
            $this->db->where("wallet_history.created <= '" . $filter['end_date'] . "'");
        }

        if (!empty($filter['awb_no'])) {
            $this->db->where_in('order_shipping.awb_number', $filter['awb_no']);
        }

        $this->db->limit($limit);
        $this->db->offset($offset);
        $this->db->where('wallet_history.user_id', $user_id);
        $this->db->join('payments', 'payments.id = wallet_history.ref_id', 'LEFT');

        $this->db->join('order_shipping', 'order_shipping.id = wallet_history.ref_id', 'LEFT');
        $this->db->order_by('wallet_history.id', 'desc');


        $q = $this->db->get($this->table);
        return $q->result();
    }

    function countByUserID($user_id = false, $filter = array())
    {
        if (!$user_id)
            return false;

        $this->db->select('count(*) as total');

        if (!empty($filter['txn_for'])) {
            switch (strtolower($filter['txn_for'])) {
                case 'recharge_razorpay':
                    $this->db->where_in('txn_for', 'recharge');
                    $this->db->where('payments.gateway', 'razorpay');
                    break;
                case 'recharge_paytm':
                    $this->db->where_in('txn_for', 'recharge');
                    $this->db->where('payments.gateway', "paytm");
                    break;

                case 'recharge_hdfc':
                    $this->db->where_in('txn_for', 'recharge');
                    $this->db->where('payments.gateway', "hdfc_razorpay");
                    break;
                case 'all_communication':
                    $this->db->where_in('txn_for', ['sms','email','whatsapp','ivr']);
                    break;    
                default:
                    $this->db->where('txn_for', $filter['txn_for']);
            }
        }

        if (!empty($filter['txn_ref'])) {
            $this->db->where('txn_ref', $filter['txn_ref']);
        }

        if (!empty($filter['shipment_id'])) {
            $this->db->where('ref_id', $filter['shipment_id']);
            $this->db->where('txn_for', 'shipment');
        }

        if (!empty($filter['start_date'])) {
            $this->db->where("wallet_history.created >= '" . $filter['start_date'] . "'");
        }

        if (!empty($filter['end_date'])) {
            $this->db->where("wallet_history.created <= '" . $filter['end_date'] . "'");
        }

        if (!empty($filter['awb_no'])) {
            $this->db->where_in('order_shipping.awb_number', $filter['awb_no']);
        }
        
        $this->db->join('payments', 'payments.id = wallet_history.ref_id', 'LEFT');
        $this->db->join('order_shipping', 'order_shipping.id = wallet_history.ref_id', 'LEFT');
        $this->db->where('wallet_history.user_id', $user_id);
        $this->db->order_by('wallet_history.id', 'desc');

        $q = $this->db->get($this->table);
        return $q->row()->total;
    }

    function walletDeductionsForShipment($id = false)
    {
        if (!$id)
            return false;

        $this->db->select(
            "(SUM(CASE WHEN type = 'debit' THEN amount else 0 END) - SUM(CASE WHEN type = 'credit' THEN amount else 0 END)) as total_charges, "
                . "(SUM(CASE WHEN (type = 'debit' and txn_ref = 'freight' ) THEN amount else 0 END) - SUM(CASE WHEN (type = 'credit' and txn_ref = 'freight' ) THEN amount else 0 END)) as freight_charges,"
                . "(SUM(CASE WHEN (type = 'debit' and txn_ref = 'cod' ) THEN amount else 0 END) - SUM(CASE WHEN (type = 'credit' and txn_ref = 'cod' ) THEN amount else 0 END)) as cod_charges,"
                . "(SUM(CASE WHEN (type = 'debit' and txn_ref = 'freight_weight' ) THEN amount else 0 END) - SUM(CASE WHEN (type = 'credit' and txn_ref = 'freight_weight' ) THEN amount else 0 END)) as extra_weight_charges,"
                . "(SUM(CASE WHEN (type = 'debit' and txn_ref = 'rto' ) THEN amount else 0 END) - SUM(CASE WHEN (type = 'credit' and txn_ref = 'rto' ) THEN amount else 0 END)) as rto_charges,"
                . "(SUM(CASE WHEN (type = 'debit' and txn_ref = 'rto_weight' ) THEN amount else 0 END) - SUM(CASE WHEN (type = 'credit' and txn_ref = 'rto_weight' ) THEN amount else 0 END)) as rto_weight_charges,"
        );
        $this->db->where('txn_for', 'shipment');
        $this->db->where('ref_id', $id);

        $q = $this->db->get($this->table);
        return $q->row();
    }

// function fetchByUserIDCommunication($user_id = false, $limit = 50, $offset = 0, $filter = array())
//     {
//         if (!$user_id)
//             return false;

//         $this->db->select('communication_wallet_history.*');

//         if (!empty($filter['txn_for'])) {
//             switch (strtolower($filter['txn_for'])) {
//                 case 'recharge_razorpay':
//                     $this->db->where_in('txn_for', 'recharge');
//                     $this->db->where('payments.gateway', 'razorpay');
//                     break;
//                 case 'recharge_paytm':
//                     $this->db->where_in('txn_for', 'recharge');
//                     $this->db->where('payments.gateway', "paytm");
//                     break;

//                 case 'recharge_hdfc':
//                     $this->db->where_in('txn_for', 'recharge');
//                     $this->db->where('payments.gateway', "hdfc_razorpay");
//                     break;    
//                 default:
//                     $this->db->where('txn_for', $filter['txn_for']);
//             }
//         }



//         if (!empty($filter['txn_ref'])) {
//             $this->db->where('txn_ref', $filter['txn_ref']);
//         }

//         if (!empty($filter['shipment_id'])) {
//             $this->db->where('ref_id', $filter['shipment_id']);
//             $this->db->where('txn_for', 'shipment');
//         }

//         if (!empty($filter['start_date'])) {
//             $this->db->where("communication_wallet_history.created >= '" . $filter['start_date'] . "'");
//         }

//         if (!empty($filter['end_date'])) {
//             $this->db->where("communication_wallet_history.created <= '" . $filter['end_date'] . "'");
//         }

//         $this->db->limit($limit);
//         $this->db->offset($offset);
//         $this->db->where('communication_wallet_history.user_id', $user_id);
//         $this->db->join('payments', 'payments.id = communication_wallet_history.ref_id', 'LEFT');

//         $this->db->join('order_shipping', 'order_shipping.id = communication_wallet_history.ref_id', 'LEFT');
//         $this->db->order_by('communication_wallet_history.id', 'desc');


//         $q = $this->db->get('communication_wallet_history');
//         return $q->result();
//     }

function fetchByUserIDCommunication($user_id = false, $limit = 50, $offset = 0, $filter = array())
    {
        if (!$user_id)
            return false;

        $this->db->select("
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
                    $this->db->where_in('h.txn_for', ['sms', 'whatsapp', 'email', 'ivr']);
                    break;
                default:
                    $this->db->where('h.txn_for', $filter['txn_for']);
            }
        }

        if (!empty($filter['txn_ref'])) {
            $this->db->where('h.txn_ref', $filter['txn_ref']);
        }

        if (!empty($filter['shipment_id'])) {
            $this->db->where('h.ref_id', $filter['shipment_id']);
            $this->db->where('h.txn_for', 'shipment');
        }

        if (!empty($filter['start_date'])) {
            $this->db->where("h.created >=", $filter['start_date']);
        }

        if (!empty($filter['end_date'])) {
            $this->db->where("h.created <=", $filter['end_date']);
        }

        /* // special filter: date of created (converted from UNIXTIME)
        if (!empty($filter['created_date'])) {
            $this->db->where("DATE(FROM_UNIXTIME(h.created)) =", $filter['created_date']);
        } */

        $this->db->limit($limit);
        $this->db->offset($offset);

        $this->db->where('h.user_id', $user_id);

        // INNER JOIN instead of LEFT JOIN
        $this->db->join(
            'tbl_notification_responses n',
            'h.request_id = n.request_id',
            'INNER',
            false
        );

        $this->db->order_by('h.id', 'desc');

        $q = $this->db->get('tbl_communication_wallet_history h');
        return $q->result();
    }

}
