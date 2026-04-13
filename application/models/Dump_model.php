<?php

class Dump_model extends MY_model
{

    public function __construct()
    {
        parent::__construct();
        $this->table = 'orders_dump';
    }

    function insertDump($save = array())
    {
        if (empty($save))
            return false;

        $save['created'] = time();

        $this->db->insert($this->table, $save);
        return $this->db->insert_id();
    }

    function delete($id = false)
    {
        if (!$id)
            return false;

        $this->db->where('id', $id);
        $this->db->delete($this->table);

        return true;
    }

    function get_pending_orders()
    {
        $this->db->select('id');
        $this->db->where('created <', strtotime('-5 hours'));
        $q = $this->db->get($this->table);

        return $q->result();
    }
}
