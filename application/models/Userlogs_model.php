<?php

class Userlogs_model extends MY_model
{

    public function __construct()
    {
        parent::__construct();
        $this->table = 'user_login_logs';
    }


    function insertLogs($save = array())
    {
        if (empty($save))
            return false;

        self::previousLogout($save['created_date'],$save['user_id']);
        $this->db->insert($this->table, $save);
        return $this->db->insert_id();
    }

    function previousLogout($datetime,$user_id)
    {
        if (empty($datetime))
            return false;
        $update=['logout_time'=>time(),'is_active'=>'0'];
        $this->db->where('created_date <=',strtotime('-1 day',$datetime));
        $this->db->where('user_id',$user_id);
        $this->db->where('logout_time is null');
        $this->db->update($this->table,$update);
        return true;
    }

    function upateLogs($user_log_id,$update)
    {
        if (empty($user_log_id))
            return false;

        $this->db->where('id', $user_log_id);
        $this->db->update($this->table,$update);
        return true;
    }  
    
    function getUserLogHistoryByUserId($user_id)
    {
        if (empty($user_id))
            return false;
            
        $this->db->where('user_id',$user_id);
        $this->db->order_by('created_date', 'desc');
        $this->db->limit('10');
        $q = $this->db->get($this->table);
        return $q->result();
    }   
    
    function insertExotelLogs($save = array())
    {
        if (empty($save))
            return false;

        $this->db->insert('exotel_number_log', $save);
        //return $this->db->insert_id();
    }
}
