<?php

class Wallet_adjustment_model extends MY_model
{

    public function __construct()
    {
        parent::__construct();
        $this->table = 'wallet_adjustment_by';
    }

    function create($save = array())
    {
        if (empty($save))
            return false;

        $save['created'] = time();

        $this->db->insert($this->table, $save);
        return $this->db->insert_id();
    }
}

?>
