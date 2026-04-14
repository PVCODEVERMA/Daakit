<?php

class Setting_model extends MY_model {

    public function __construct() {
        parent::__construct();
        $this->table = 'users';
    }

    function getsettingByUserID($user_id = false)
	{
        if (!$user_id)
            return false;
        $this->db->select("users.*,
		company_details.*");
        $this->db->where('users.id', $user_id);
        $this->db->join('company_details', 'company_details.user_id = users.id', 'left');
        $q = $this->db->get($this->table);
        return $q->row();
    }

    function update_user($userid, $save_data = array()){
        if (empty($save_data) || empty($userid))
            return false;
        $save_data['modified'] = time();
        $this->db->set($save_data);
        $this->db->where('id', $userid);
        return $this->db->update($this->table);
    }

    function checkphone($userid){
        if (!$userid)
            return false;
        $this->db->select("users.*");
        $this->db->where('users.id', $userid);
        $q = $this->db->get('users');
        return $q->row();
    }

    function isPhoneExist($newcell){
        
        $this->db->where('phone', $newcell);
        $query = $this->db->get('users');
        if ($query->num_rows() > 0) {
            return true;
        } else {
            return false;
        }
    }
}

?>