<?php

class Remittance_model extends MY_model
{

    public function __construct()
    {
        parent::__construct();
        $this->table = 'remittance';
        $this->ignore_remittance_ids = array('54605','54417','54510','55980','55981','80970','82368','84471','98070','109931','109932','110718','110719','110720','110721','110723','110724','110725','110727','112837','112838','112839','112840','112841','112842','112843','112844','113657','113656','115729','118652','133127','114927','126984');
    }

    function remittedAmount($user_id = false)
    {
        if (!$user_id)
            return false;

        $this->db->select('sum(amount) as  total');

        $this->db->where_not_in('id', $this->ignore_remittance_ids);

        $this->db->where('user_id', $user_id);
        $q = $this->db->get($this->table);
        return $q->row()->total;
    }

    function lastRemittance($user_id = false)
    {
        if (!$user_id)
            return false;

        $this->db->select('amount as  total');
        $this->db->where('user_id', $user_id);
        $this->db->where_not_in('id', $this->ignore_remittance_ids);

        $this->db->order_by('id', 'desc');
        $this->db->limit(1);
        $q = $this->db->get($this->table);
        $total = $q->row();
        return !empty($total) ? $total->total : '0';
    }

    function remittanceHistory($user_id = false)
    {
        if (!$user_id)
            return false;

        $this->db->select('*');
        $this->db->where('user_id', $user_id);
        $this->db->where_not_in('id', $this->ignore_remittance_ids);
        $this->db->order_by('id', 'desc');
        $q = $this->db->get($this->table);

        return $q->result();
    }
}
