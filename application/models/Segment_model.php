<?php

class Segment_model extends MY_model
{

    public function __construct()
    {
        parent::__construct();
        $this->table = 'segments';
    }

    function create($save = array())
    {
        if (empty($save))
            return false;

        $save['created'] = time();
        $save['modified'] = time();



        $this->db->insert($this->table, $save);
        return $this->db->insert_id();
    }

    function update($id = false, $save = array())
    {
        if (empty($save) || empty($id))
            return false;

        $save['modified'] = time();

        $this->db->set($save);
        $this->db->where('id', $id);
        $this->db->update($this->table);
        return true;
    }

    function delete($id = false)
    {
        if (!$id)
            return false;

        $this->db->where('id', $id);
        $this->db->delete($this->table);

        return true;
    }

    function getUserfilters($user_id = false)
    {
        if (!$user_id)
            return false;

        $this->db->where('user_id', $user_id);

        $this->db->order_by('id', 'asc');
        $q = $this->db->get($this->table);
        return $q->result();
    }
}
