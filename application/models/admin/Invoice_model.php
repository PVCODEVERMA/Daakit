<?php

class Invoice_model extends MY_model
{

    public function __construct()
    {
        parent::__construct();
        $this->table = 'invoice';
        $this->cn_table = 'invoice_credits';
        $this->data_table = 'invoice_data';
    }

    function insert($save = array())
    {
        if (empty($save))
            return false;

        $save['created'] = time();

        $this->db->insert($this->table, $save);
        return $this->db->insert_id();
    }

    function batchInsertInvoiceData($save = array())
    {
        if (empty($save))
            return false;

        $this->db->insert_batch($this->data_table, $save);
        return true;
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

    function fetchInvocie($limit = 50, $offset = 0, $filter = array())
    {         
        $this->db->select('invoice.*,users.id as sellerid,users.fname as firstname,users.lname as lastname,users.company_name as company_name, users.email as user_email, company_details.cmp_gstno, company_details.cmp_state,company_details.cmp_address,company_details.cmp_city, legal_entity.legal_name,legal_entity.legal_gstno,legal_entity.legal_address,legal_entity.legal_city,legal_entity.legal_state,legal_entity.legal_pincode');

        if (!empty($filter['seller_id'])) {
            $this->db->where('invoice.user_id', $filter['seller_id']);
        }

        if (!empty($filter['month'])) {
            $this->db->where('invoice.month', $filter['month']);
        }
        if (!empty($filter['invoice_type'])) {
            $this->db->where('invoice.invoice_type', strtolower($filter['invoice_type']));
        }
        if (!empty($filter['service_type'])) {
            $this->db->where('invoice.service_type', strtolower($filter['service_type']));
        }

        $this->db->limit($limit);
        $this->db->offset($offset);

        $this->db->order_by('invoice.id', 'desc');
        $this->db->join('users', 'users.id = invoice.user_id', 'LEFT');
        $this->db->join('company_details', 'company_details.user_id = users.id', 'LEFT');
        $this->db->join('legal_entity', 'legal_entity.user_id = users.id', 'LEFT');
        $q = $this->db->get($this->table);
        return $q->result();
    }

    function countInvoice($filter = array())
    {
        $this->db->select('count(*) as total');

        if (!empty($filter['seller_id'])) {
            $this->db->where('invoice.user_id', $filter['seller_id']);
        }

        if (!empty($filter['month'])) {
            $this->db->where('invoice.month', $filter['month']);
        }

        if (!empty($filter['invoice_type'])) {
            $this->db->where('invoice.invoice_type', strtolower($filter['invoice_type']));
        }
        if (!empty($filter['service_type'])) {
            $this->db->where('invoice.service_type', strtolower($filter['service_type']));
        }
        $this->db->order_by('invoice.id', 'desc');
        $this->db->join('users', 'users.id = invoice.user_id', 'LEFT');
        $q = $this->db->get($this->table);

        return $q->row()->total;
    }

    function fetchInvoiceMonthrouped()
    {
        $this->db->select('month');
        $this->db->group_by('month');
        $this->db->order_by('invoice.id', 'desc');

        $q = $this->db->get($this->table);
        return $q->result();
    }

    function fetchCreditNotesMonthrouped()
    {
        $this->db->select('month');
        $this->db->group_by('month');
        $this->db->order_by('invoice_credits.id', 'desc');

        $q = $this->db->get($this->cn_table);
        return $q->result();
    }


    function fetchCreditNotes($limit = 50, $offset = 0, $filter = array())
    {
        $this->db->select('invoice_credits.*, users.id as sellerid,users.fname as firstname,users.lname as lastname,users.company_name as company_name, users.email as user_email, company_details.cmp_gstno, company_details.cmp_state,company_details.cmp_address,company_details.cmp_city, legal_entity.legal_name,legal_entity.legal_gstno,legal_entity.legal_address,legal_entity.legal_city,legal_entity.legal_state,legal_entity.legal_pincode');

        if (!empty($filter['seller_id'])) {
            $this->db->where('invoice_credits.user_id', $filter['seller_id']);
        }

        if (!empty($filter['month'])) {
            $this->db->where('invoice_credits.month', $filter['month']);
        }
        if (!empty($filter['invoice_type'])) {
            $this->db->where('invoice_credits.invoice_type', strtolower($filter['invoice_type']));
        }
        if (!empty($filter['service_type'])) {
            $this->db->where('invoice_credits.service_type', strtolower($filter['service_type']));
        }
        $this->db->limit($limit);
        $this->db->offset($offset);

        $this->db->order_by('invoice_credits.id', 'desc');
        $this->db->join('users', 'users.id = invoice_credits.user_id', 'LEFT');
        $this->db->join('company_details', 'company_details.user_id = users.id', 'LEFT');
        $this->db->join('legal_entity', 'legal_entity.user_id = users.id', 'LEFT');
        $q = $this->db->get($this->cn_table);
        return $q->result();
    }

    function countCreditNotes($filter = array())
    {
        $this->db->select('count(*) as total');

        if (!empty($filter['seller_id'])) {
            $this->db->where('invoice_credits.user_id', $filter['seller_id']);
        }

        if (!empty($filter['month'])) {
            $this->db->where('invoice_credits.month', $filter['month']);
        }

        if (!empty($filter['invoice_type'])) {
            $this->db->where('invoice_credits.invoice_type', strtolower($filter['invoice_type']));
        }
        if (!empty($filter['service_type'])) {
            $this->db->where('invoice_credits.service_type', strtolower($filter['service_type']));
        }
        $this->db->order_by('invoice_credits.id', 'desc');
        $this->db->join('users', 'users.id = invoice_credits.user_id', 'LEFT');
        $q = $this->db->get($this->cn_table);

        return $q->row()->total;
    }

    function exportInvoiceDataReport($filter = array())
    {
        $this->db->select('invoice_data.*,users.id as sellerid,users.fname as firstname,users.lname as lastname,users.company_name as company_name,invoice.month,invoice.id as invoice_id, invoice.gstno, invoice.invoice_no');

        if (!empty($filter['seller_id'])) {
            $this->db->where('invoice.user_id', $filter['seller_id']);
        }
        if (!empty($filter['month'])) {
            $this->db->where('invoice.month', $filter['month']);
        }

        if (!empty($filter['invoice_type'])) {
            $this->db->where('invoice.invoice_type', $filter['invoice_type']);
        }
        if (!empty($filter['service_type'])) {
            $this->db->where('invoice.service_type', strtolower($filter['service_type']));
        }

        $this->db->where('invoice_data.type', 'invoice');
        $this->db->order_by('invoice.id', 'desc');
        $this->db->join('invoice', 'invoice.id = invoice_data.ref_id', 'INNER');
        $this->db->join('users', 'users.id = invoice.user_id', 'LEFT');
        // $this->db->join('company_details', 'company_details.user_id = users.id', 'LEFT');
        $this->db->from($this->data_table);
        return $this->db->get_compiled_select();
    }

    function exportCreditNoteDataReport($filter = array())
    {
        $this->db->select('invoice_data.*,users.id as sellerid,users.fname as firstname,users.lname as lastname,users.company_name as company_name,invoice_credits.month,invoice_credits.id as invoice_id,invoice_credits.gstno, invoice_credits.invoice_no');

        if (!empty($filter['seller_id'])) {
            $this->db->where('invoice_credits.user_id', $filter['seller_id']);
        }
        if (!empty($filter['month'])) {
            $this->db->where('invoice_credits.month', $filter['month']);
        }

        if (!empty($filter['invoice_type'])) {
            $this->db->where('invoice_credits.invoice_type', $filter['invoice_type']);
        }
        if (!empty($filter['service_type'])) {
            $this->db->where('invoice_credits.service_type', strtolower($filter['service_type']));
        }
        
        $this->db->where('invoice_data.type', 'credit');
        $this->db->order_by('invoice_credits.id', 'desc');
        $this->db->join('invoice_credits', 'invoice_credits.id = invoice_data.ref_id', 'INNER');
        $this->db->join('users', 'users.id = invoice_credits.user_id', 'LEFT');
        $this->db->from($this->data_table);
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
}
