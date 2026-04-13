<?php

class Logs_model extends MY_model
{
    public function __construct()
    {
        parent::__construct();
        $this->table = 'logs';
    }

    function batchInsert($save = array())
    {
        if (empty($save))
            return false;

        return true;
    }

    function insert($save = array())
    {
        if (empty($save))
            return false;

        $this->db->insert($this->table, $save);
        return true;
    }
}
