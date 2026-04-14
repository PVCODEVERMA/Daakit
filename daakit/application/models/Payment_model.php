<?php

class Payment_model extends MY_model {

    public function __construct() {
        parent::__construct();
        $this->table = 'payments';
        $this->neft_table = 'neft_payments';
    }

    function insert($save = array()) {
        if (empty($save))
            return false;

        $save['created'] = time();

        $this->db->insert($this->table, $save);
        return $this->db->insert_id();
    }

    function update($id = false, $save = array()) {
        if (empty($save) || empty($id))
            return false;

        $this->db->set($save);
        $this->db->where('id', $id);
        $this->db->update($this->table);
        return true;
    }

    function markAsPaid($id = false, $gateway_id = false, $gateway = '') {
        if (!$id)
            return false;

        $this->db->where('id', $id);
        $this->db->set('paid', '1');

        if ($gateway_id)
            $this->db->set('gateway_payment_id', $gateway_id);

        if ($gateway)
            $this->db->set('gateway', $gateway);

        $this->db->update($this->table);
        return true;
    }

    function saveNeftPayment($save = array()) {
        if (empty($save))
            return false;

        $save['created'] = time();

        $this->db->insert($this->neft_table, $save);
        return $this->db->insert_id();
    }

    function countByNeftPayment($filter = array())
    {
        $this->db->select('count(DISTINCT neft_payments.id) as total');

        if (!empty($filter['seller_id'])) {
            $this->db->where('neft_payments.user_id', $filter['seller_id']);
        }


        if (!empty($filter['start_date'])) {
            $this->db->where("neft_payments.created >= '" . $filter['start_date'] . "'");
        }

        if (!empty($filter['end_date'])) {
            $this->db->where("neft_payments.created <= '" . $filter['end_date'] . "'");
        }

        if (!empty($filter['utr_no'])) {
            $this->db->where('neft_payments.utr_number', $filter['utr_no']);
        }


        $this->db->join('users', 'users.id = neft_payments.user_id');
        $this->db->order_by('neft_payments.created', 'desc');
        $q = $this->db->get($this->neft_table);
        return $q->row()->total;
    }

    function getByNeftPayment($limit = 50, $offset = 0, $filter = array())
    {

        $this->db->select("neft_payments.*, 
		users.fname as user_fname,
        users.lname as user_lname,
		users.company_name,
		users.id as userid");

        if (!empty($filter['seller_id'])) {
            $this->db->where('neft_payments.user_id', $filter['seller_id']);
        }


        if (!empty($filter['start_date'])) {
            $this->db->where("neft_payments.created >= '" . $filter['start_date'] . "'");
        }

        if (!empty($filter['end_date'])) {
            $this->db->where("neft_payments.created <= '" . $filter['end_date'] . "'");
        }

        if (!empty($filter['utr_no'])) {
            $this->db->where('neft_payments.utr_number', $filter['utr_no']);
        }

        $this->db->limit($limit);
        $this->db->offset($offset);
        $this->db->join('users', 'users.id = neft_payments.user_id');
        $this->db->order_by('neft_payments.created', 'desc');
        $q = $this->db->get($this->neft_table);
        return $q->result();
    }

    function getByNeftPaymentExport($filter = array())
    {

        $this->db->select("neft_payments.*, 
		users.fname as user_fname,
        users.lname as user_lname,
		users.company_name,
		users.id as userid");

        if (!empty($filter['seller_id'])) {
            $this->db->where('neft_payments.user_id', $filter['seller_id']);
        }


        if (!empty($filter['start_date'])) {
            $this->db->where("neft_payments.created >= '" . $filter['start_date'] . "'");
        }

        if (!empty($filter['end_date'])) {
            $this->db->where("neft_payments.created <= '" . $filter['end_date'] . "'");
        }
        if (!empty($filter['utr_no'])) {
            $this->db->where('neft_payments.utr_number', $filter['utr_no']);
        }

        $this->db->join('users', 'users.id = neft_payments.user_id');
        $this->db->order_by('neft_payments.created', 'desc');
        $q = $this->db->from($this->neft_table);
        return  $query =   $this->db->get_compiled_select();
    }

    function countPaidUser($id = false) {
       
        if (!$id)
            return false;
            
         $this->db->select('count(*) as total');
        $this->db->where('user_id', $id);
        $this->db->where('paid', '1');
        $q = $this->db->get($this->table);
        return $q->row()->total;
    }
}
