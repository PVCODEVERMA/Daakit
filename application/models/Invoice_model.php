<?php

class Invoice_model extends MY_model
{

    public function __construct()
    {
        parent::__construct();
        $this->table = 'invoice';
        $this->cn_table = 'invoice_credits';
        $this->data_table = 'invoice_data';
        $this->inv_error= 'invoice_errors';
    }

    function batchInsertInvoiceData($save = array())
    {
        if (empty($save))
            return false;

        $this->db->insert_batch($this->data_table, $save);
        return true;
    }

    function insert($save = array())
    {
        if (empty($save))
            return false;

        $save['created'] = time();

        $this->db->insert($this->table, $save);
        return $this->db->insert_id();
    }

    function update($id = false, $update = false)
    {
        if (!$id || !$update)
            return false;

        $this->db->where('id', $id);
        $this->db->set($update);
        $this->db->update($this->table);
        return true;
    }

    function insert_credit_note($save = array())
    {
        if (empty($save))
            return false;

        $save['created'] = time();

        $this->db->insert($this->cn_table, $save);
        return $this->db->insert_id();
    }

    function update_credit_note($id = false, $update = false)
    {
        if (!$id || !$update)
            return false;

        $this->db->where('id', $id);
        $this->db->set($update);
        $this->db->update($this->cn_table);
        return true;
    }

    function fetchInvocie($user_id = false, $limit = 50, $offset = 0, $filter = array())
    {
        if (!$user_id)
            return false;

        if (!empty($filter['month'])) {
            $this->db->where('invoice.month', $filter['month']);
        }

        $this->db->where('user_id', $user_id);
        $this->db->limit($limit);
        $this->db->offset($offset);
        $this->db->order_by('invoice.id', 'desc');
        $q = $this->db->get($this->table);
        return $q->result();
    }

    function countInvoice($user_id = false, $filter = array())
    {
        if (!$user_id)
            return false;
        $this->db->select('count(*) as total');

        if (!empty($filter['month'])) {
            $this->db->where('invoice.month', $filter['month']);
        }

        $this->db->where('user_id', $user_id);
        $this->db->order_by('invoice.id', 'desc');
        $q = $this->db->get($this->table);
        return $q->row()->total;
    }

    function fetchCreditNotes($user_id = false, $limit = 50, $offset = 0, $filter = array())
    {
        if (!$user_id)
            return false;

        if (!empty($filter['month'])) {
            $this->db->where('invoice_credits.month', $filter['month']);
        }

        $this->db->where('user_id', $user_id);
        $this->db->limit($limit);
        $this->db->offset($offset);
        $this->db->order_by('invoice_credits.id', 'desc');
        $q = $this->db->get($this->cn_table);
        return $q->result();
    }

    function countCreditNotes($user_id = false, $filter = array())
    {
        if (!$user_id)
            return false;

        $this->db->select('count(*) as total');
        if (!empty($filter['month'])) {
            $this->db->where('invoice_credits.month', $filter['month']);
        }

        $this->db->where('user_id', $user_id);
        $this->db->order_by('invoice_credits.id', 'desc');
        $q = $this->db->get($this->cn_table);
        return $q->row()->total;
    }

    function checkUserInvoiceExists($user_id = false, $month = false, $service_type= false)
    {
        if (!$user_id || !$month)
            return false;

        $this->db->where('user_id', $user_id);
        $this->db->where('month', $month);
        if($service_type) {
            $this->db->where('service_type', $service_type);
        }

        $this->db->order_by('invoice.id', 'desc');
        $this->db->limit(1);
        $q = $this->db->get($this->table);
        return $q->row();
    }

    function checkUserCreditNoteExists($user_id = false, $month = false, $service_type= false)
    {
        if (!$user_id || !$month)
            return false;

        $this->db->where('user_id', $user_id);
        $this->db->where('month', $month);
        if($service_type) {
            $this->db->where('service_type', $service_type);
        }

        $this->db->order_by('id', 'desc');
        $this->db->limit(1);
        $q = $this->db->get($this->cn_table);
        return $q->row();
    }

    function getInsuranceInvoiceTXNs($user_id = false, $filter = array())
    {
        if (!$user_id)
            return false;

        $this->db->select(
            "w.user_id,"
                . "w.ref_id as shipment_id,"
                . "c.name as courier_name,"
                . "o.order_no,"
                . "o.order_payment_type,"
                . "o.shipping_zip,"
                . "o.shipping_city,"
                . "o.package_weight,"
                . "s.ship_status,"
                . "s.awb_number,"
                . "s.insurance_price,"
                . "s.charged_weight,"
                . "w.created,"
                . "w.txn_ref,"
                . "SUM(COALESCE(CASE when w.type = 'debit' and txn_ref = 'insurance' THEN amount END,0)) - SUM(COALESCE(CASE when w.type = 'credit' and txn_ref = 'insurance' THEN amount END,0)) as insurance_charges,"
        );

        if (!empty($filter['start_date'])) {
            $this->db->where("w.created >=", $filter['start_date']);
        }

        if (!empty($filter['end_date'])) {
            $this->db->where("w.created <=", $filter['end_date']);
        }

        $this->db->where('w.user_id', $user_id);
        $this->db->where('w.txn_for', 'shipment');
        $this->db->where('w.ref_id !=', '');
        $this->db->where('txn_ref','insurance');

        $this->db->group_by('w.ref_id');

        $this->db->join('order_shipping as s', 's.id = ref_id', 'left');
        $this->db->join('orders as o', 'o.id = s.order_id', 'left');
        $this->db->join('courier as c', 'c.id = s.courier_id', 'left');
        $this->db->from('wallet_history as w');

        return $this->db->get_compiled_select();
    }

    function getAddonInvoiceTXNs($user_id = false, $filter = array())
    {
        if (!$user_id)
            return false;

        $this->db->select(
            "w.user_id,"
                . "w.ref_id as shipment_id,"
                . "w.created,"
                . "w.txn_ref,"
                . "w.notes,"
                . "SUM(COALESCE(CASE when w.type = 'debit' and txn_ref = 'ivr_call' THEN amount END,0)) - SUM(COALESCE(CASE when w.type = 'credit' and txn_ref = 'ivr_call' THEN amount END,0)) as exotel_charges,"
                . "SUM(COALESCE(CASE when w.type = 'debit' and txn_ref = 'ivr_number' THEN amount END,0)) - SUM(COALESCE(CASE when w.type = 'credit' and txn_ref = 'ivr_number' THEN amount END,0)) as exotel_recurring_charges,"
                . "SUM(COALESCE(CASE when w.type = 'debit' and txn_ref = 'whatsapp' THEN amount END,0)) - SUM(COALESCE(CASE when w.type = 'credit' and txn_ref = 'whatsapp' THEN amount END,0)) as whatsapp_charges,"
        );

        if (!empty($filter['start_date'])) {
            $this->db->where("w.created >=", $filter['start_date']);
        }

        if (!empty($filter['end_date'])) {
            $this->db->where("w.created <=", $filter['end_date']);
        }

        $this->db->where('w.user_id', $user_id);
        $this->db->where('w.txn_for','addon');
        // $this->db->where('w.ref_id !=', '');
        //$this->db->where('txn_ref','addon_charges');

        $this->db->group_by('w.txn_ref');

        //$this->db->join('order_shipping as s', 's.id = w.ref_id', 'left');
        //$this->db->join('orders as o', 'o.id = s.order_id', 'left');
        //$this->db->join('courier as c', 'c.id = s.courier_id', 'left');
        $this->db->from('wallet_history as w');

        return $this->db->get_compiled_select();
    }

    function getInvoiceTXNs($user_id = false, $filter = array())
    {
        if (!$user_id)
            return false;

        $this->db->select(
            "w.user_id,"
                . "w.ref_id as shipment_id,"
                . "c.name as courier_name,"
                . "o.order_no,"
                
                . "o.id as order_db_id,"
                . "wa.city,"
                . "wa.state,"
                . "wa.zip,"
                . "o.order_amount,"

                . "o.order_payment_type,"
                . "o.shipping_zip,"
                . "o.shipping_city,"
                . "o.package_weight,"
                . "s.ship_status,"
                . "s.awb_number,"
                . "s.insurance_price,"
                . "s.charged_weight,"
                . "s.calculated_weight,"
                . "w.created,"
                . "w.txn_ref,"
                . "SUM(COALESCE(CASE when w.type = 'debit' and txn_ref = 'freight' THEN amount END,0)) - SUM(COALESCE(CASE when w.type = 'credit' and txn_ref = 'freight' THEN amount END,0)) as freight_charges,"
                . "SUM(COALESCE(CASE when w.type = 'debit' and txn_ref = 'cod' THEN amount END,0)) - SUM(COALESCE(CASE when w.type = 'credit' and txn_ref = 'cod' THEN amount END,0)) as cod_charges,"
                . "SUM(COALESCE(CASE when w.type = 'debit' and txn_ref = 'rto_freight' THEN amount END,0)) - SUM(COALESCE(CASE when w.type = 'credit' and txn_ref = 'rto_freight' THEN amount END,0)) as rto_freight_charges,"
                . "SUM(COALESCE(CASE when w.type = 'debit' and txn_ref = 'extra_weight' THEN amount END,0)) - SUM(COALESCE(CASE when w.type = 'credit' and txn_ref = 'extra_weight' THEN amount END,0)) as extra_weight_charges,"
                . "SUM(COALESCE(CASE when w.type = 'debit' and txn_ref = 'rto_extra_weight' THEN amount END,0)) - SUM(COALESCE(CASE when w.type = 'credit' and txn_ref = 'rto_extra_weight' THEN amount END,0)) as rto_extra_weight_charges,"
        );

        if (!empty($filter['start_date'])) {
            $this->db->where("w.created >=", $filter['start_date']);
        }

        if (!empty($filter['end_date'])) {
            $this->db->where("w.created <=", $filter['end_date']);
        }

        if (!empty($filter['type'])) {
            $where = $filter['type']=='international'? ['international']:['ecom','cargo']; 
            $this->db->where_in("s.order_type", $where);
        }

        $this->db->where('w.user_id', $user_id);
        $this->db->where('w.txn_for', 'shipment');
        $this->db->where('w.ref_id !=', '');
        $this->db->group_by('w.ref_id');
        $this->db->join('order_shipping as s', 's.id = ref_id', 'left');
        $this->db->join('warehouse as wa', 'wa.id = s.warehouse_id', 'left');
        $this->db->join('orders as o', 'o.id = s.order_id', 'left');
        $this->db->join('courier as c', 'c.id = s.courier_id', 'left');
        $this->db->from('wallet_history as w');
        return  $this->db->get_compiled_select();
    }

    function getInvoiceCNByID($id = false)
    {
        if (!$id)
            return false;

        $this->db->where('id', $id);
        $query = $this->db->get($this->cn_table);
        if ($query->num_rows() == 1)
            return $query->row();

        return FALSE;
    }

    function getLastInvoiceNo()
    {
        $financial_year_to = (date('m') > 3) ? date('Y') +1 : date('Y');
        $financial_year_from = $financial_year_to - 1;
        $start_date = '01-04-'.$financial_year_from;
        $end_date = '31-03-'.$financial_year_to;
        $this->db->select('inv_no');
        $this->db->where("created >=", strtotime(trim($start_date) . ' 00:00:00'));
        $this->db->where("created <=", strtotime(trim($end_date) . ' 23:59:59'));
        $this->db->order_by('id', 'desc');
        $this->db->limit(1);
        $q = $this->db->get($this->table);
        $res = $q->row();
        return (!empty($res->inv_no)) ? $res->inv_no : 0;
    }

    function getLastCnInvoiceNo()
    {
        $financial_year_to = (date('m') > 3) ? date('Y') +1 : date('Y');
        $financial_year_from = $financial_year_to - 1;
        $start_date = '01-04-'.$financial_year_from;
        $end_date = '31-03-'.$financial_year_to;
        $this->db->select('inv_no');
        $this->db->where("created >=", strtotime(trim($start_date) . ' 00:00:00'));
        $this->db->where("created <=", strtotime(trim($end_date) . ' 23:59:59'));
        $this->db->order_by('id', 'desc');
        $this->db->limit(1);
        $q = $this->db->get($this->cn_table);
        $res = $q->row();
        return (!empty($res->inv_no)) ? $res->inv_no : 0;
    }

    function saveInvError($save = array())
    {
        if (empty($save))
            return false;

        $save['created'] = time();

        $this->db->insert($this->inv_error, $save);
        return $this->db->insert_id();
    }

    function deleteInvoice($id =false, $type= false)
    {
        if(empty($id))
            return false;
        $this->db->delete($this->table, array('id' => $id)); 
        return true;
    }

    function deleteCNInvoice($id =false, $type= false)
    {
        if(empty($id))
            return false;
        $this->db->delete($this->cn_table, array('id' => $id)); 
        return true;
    }

    function exportIVRCalls($filter = array())
    {
        $this->db->select("w.amount as wallet_amount, w.created as wallet_date, w.notes as wallet_notes");    
        //$this->db->join('ivr_calls', 'ivr_calls.wallet_history_id = w.id', 'left');
        //$this->db->where('ivr_calls.wallet_history_id > 0');
    
    
        if (!empty($filter['start_date'])) {
            $this->db->where("w.created >=", $filter['start_date']);
        }
        if (!empty($filter['end_date'])) {
            $this->db->where("w.created <=", $filter['end_date']);
        }
        if (!empty($filter['user_id'])) {
            $this->db->where('w.user_id', $filter['user_id']);
        }
        $this->db->where('w.txn_for','addon');
        $this->db->where_in('w.txn_ref', ['ivr_call','ivr_number']);
        
        $q = $this->db->get('wallet_history as w');
        return $q->result();
    }

    function exportWhatsappShipment($filter = array())
    {
        $this->db->select("send_status_whatsapp.id,send_status_whatsapp.shipment_id, os.awb_number,send_status_whatsapp.created"
        );
        $this->db->join('wallet_history as w', 'send_status_whatsapp.shipment_wallet_id = w.id', 'left');
        $this->db->join('order_shipping as os', 'send_status_whatsapp.shipment_id = os.id ', 'left');
        if (!empty($filter['start_date'])) {
            $this->db->where("w.created >=", $filter['start_date']);
        }
        if (!empty($filter['end_date'])) {
            $this->db->where("w.created <=", $filter['end_date']);
        }
        if (!empty($filter['user_id'])) {
            $this->db->where('w.user_id', $filter['user_id']);
        }
        $this->db->where('w.txn_for','addon');
        $this->db->where('w.txn_ref','whatsapp');
        // $this->db->group_by('send_status_whatsapp.shipment_id');
        //$this->db->where("shipment_wallet_id > 0 OR ndr_wallet_id > 0");
       
        $q = $this->db->get('send_status_whatsapp');
        return $q->result();
    }

    function exportWhatsappNDR($filter = array())
    {
        $this->db->select("send_status_whatsapp.id,send_status_whatsapp.shipment_id,os.awb_number,send_status_whatsapp.created");

        $this->db->join('wallet_history as w', 'send_status_whatsapp.ndr_wallet_id = w.id', 'left');
        $this->db->join('order_shipping as os', 'send_status_whatsapp.shipment_id = os.id ', 'left');
        if (!empty($filter['start_date'])) {
            $this->db->where("w.created >=", $filter['start_date']);
        }
        if (!empty($filter['end_date'])) {
            $this->db->where("w.created <=", $filter['end_date']);
        }
        if (!empty($filter['user_id'])) {
            $this->db->where('w.user_id', $filter['user_id']);
        }
        $this->db->where('w.txn_for','addon');
        $this->db->where('w.txn_ref','whatsapp');
        // $this->db->group_by('send_status_whatsapp.shipment_id');
        //$this->db->where("shipment_wallet_id > 0 OR ndr_wallet_id > 0");
       
        $q = $this->db->get('send_status_whatsapp');
        return $q->result();
    }

    function exportWhatsappOrder($filter = array())
    {
        $this->db->select("send_order_whatsapp.*");
        $this->db->join('wallet_history as w', 'send_order_whatsapp.order_wallet_id = w.id', 'left');
        
        if (!empty($filter['start_date'])) {
            $this->db->where("w.created >=", $filter['start_date']);
        }
        if (!empty($filter['end_date'])) {
            $this->db->where("w.created <=", $filter['end_date']);
        }
        if (!empty($filter['user_id'])) {
            $this->db->where('w.user_id', $filter['user_id']);
        }
        $this->db->where('w.txn_for','addon');
        $this->db->where('w.txn_ref','whatsapp');
        $this->db->where("order_wallet_id > 0 ");

        $q = $this->db->get('send_order_whatsapp');
        return $q->result();
    }
    
    function exportWhatsappAbandon($filter = array())
    {
        $this->db->select("send_abandon_whatsapp.*");
        $this->db->join('wallet_history as w', 'send_abandon_whatsapp.abandoned_wallet_id = w.id', 'left');
        if (!empty($filter['start_date'])) {
            $this->db->where("w.created >=", $filter['start_date']);
        }
        if (!empty($filter['end_date'])) {
            $this->db->where("w.created <=", $filter['end_date']);
        }
        if (!empty($filter['user_id'])) {
            $this->db->where('w.user_id', $filter['user_id']);
        }
        $this->db->where('w.txn_for','addon');
        $this->db->where('w.txn_ref','whatsapp');
        $this->db->where("send_abandon_whatsapp.abandoned_wallet_id > 0 ");
        $q = $this->db->get('send_abandon_whatsapp');
        return $q->result();
    }
}