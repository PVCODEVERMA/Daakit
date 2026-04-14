<?php

class MY_model extends CI_model
{
    var $table;

    public function __construct()
    {
        parent::__construct();
    }

    function getByID($id = false)
    {
        if (!$id)
            return false;
        
        $this->db->reset_query();

        $this->db->where('id', (int) $id);
        $query = $this->db->get($this->table);
        if ($query->num_rows() == 1)
            return $query->row();

        return FALSE;
    }
}