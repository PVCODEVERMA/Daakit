<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Export_db
{

    protected $CI;
    protected $mysqli;
    protected $uresult;
    protected $db;

    public function __construct($db_group = false)
    {
        $this->CI = &get_instance();
        $this->db = $this->CI->db;
        if ($db_group)
            $this->db = $this->CI->load->database($db_group, TRUE);
        $this->init();
    }

    private function init()
    {
        $this->mysqli  = new mysqli($this->db->hostname, $this->db->username, $this->db->password, $this->db->database);
    }

    function query($query = false)
    {
        if (!$query)
            return false;

        $this->uresult = $this->mysqli->query($query, MYSQLI_USE_RESULT);
    }

    function next()
    {
        return $this->uresult->fetch_object();
    }
}
