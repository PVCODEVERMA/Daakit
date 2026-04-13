<?php
class Country_model extends MY_model
{
    public function __construct()
    {
        parent::__construct();
        $this->table = 'country_list';
    }

    function getCountryList()
    {
        $this->db->where('status', '1');
        $this->db->order_by('name', 'asc');
        $q = $this->db->get($this->table);
        return $q->result();
    }

    function getAllCountry()
    {
        $this->db->order_by('name', 'asc');
        $q = $this->db->get($this->table);
        return $q->result();
    }
    
    function getCountry($name = false)
    {
        $this->db->like('name', $name, 'none');
        $this->db->where('status', '1');
        $this->db->order_by('name', 'asc');
        $q = $this->db->get($this->table);
        return $q->row();
    }
}