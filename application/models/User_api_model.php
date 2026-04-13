<?php

class User_api_model extends MY_model {

    public function __construct() {
        parent::__construct();
        $this->table = 'api_keys';
    }

    function create($save = array()) {
        if (empty($save))
            return false;

        $this->db->insert($this->table, $save);
        return $this->db->insert_id();
    }

    function update($id = false, $save = array()) {
        if (!$id || empty($save))
            return false;

        $this->db->where('id', $id);
        $this->db->set($save);
        $this->db->update($this->table);
        return true;
    }

    function getByUserID($user_id = false) {
        if (!$user_id)
            return false;

        $this->db->where('user_id', $user_id);
        $this->db->limit(1);
        $q = $this->db->get($this->table);
        return $q->row();
    }

}
