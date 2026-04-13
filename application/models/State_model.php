<?php
class State_model extends MY_model
{
    public function __construct()
    {
        parent::__construct();
        $this->table = 'states';
    }

    function getStateList()
    {
        $this->db->select('state_name, state_code');
        $this->db->where('status', '1');
        $this->db->order_by('state_name', 'asc');
        $q = $this->db->get($this->table);
        return $q->result();
    }

    function getStateName($state_code = false)
    {
        $this->db->select('state_name');
        $this->db->where('state_code', $state_code);
        $this->db->limit(1);
        $q = $this->db->get($this->table);
        return $q->row();
    }

    function getStateCodeByName($state_name = false)
    {
        $this->db->select('state_code');
        $this->db->where('state_name', $state_name);
        $this->db->limit(1);
        $q = $this->db->get($this->table);
        return $q->row();
    }
}