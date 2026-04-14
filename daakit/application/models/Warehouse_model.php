<?php

class Warehouse_model extends MY_model
{

    public function __construct()
    {
        parent::__construct();
        $this->table = 'warehouse';
    }


    function getUserWarehouseByName($user_id = false, $name = false)
    {
        if (!$user_id || !$name)
            return false;

        $this->db->where('user_id', $user_id);
        $this->db->where('name', $name);
        $this->db->limit(1);
        $this->db->order_by('id', 'desc');
        $q = $this->db->get($this->table);
        return $q->row();
    }

    function getUserWarehouse($user_id = false)
    {
        if (!$user_id)
            return false;

        $this->db->where('user_id', $user_id);
        $this->db->limit(1);
        $this->db->order_by('id', 'asc');
        $q = $this->db->get($this->table);
        return $q->row();
    }

    function getUserWarehouseById($warehouse_id = false)
    {
        if (!$warehouse_id)
            return false;

        $this->db->where('id', $warehouse_id);
        $q = $this->db->get($this->table);
        return $q->row();
    }

    function getUserDefaultWarehouse($user_id = false)
    {
        if (!$user_id)
            return false;

        $this->db->where('user_id', $user_id);
        $this->db->where('is_default', '1');
        $this->db->where('active', '1');
        $this->db->limit(1);
        $q = $this->db->get($this->table);
        return $q->row();
    }

    function getUserAllWarehouse($user_id = false, $only_active = false)
    {
        if (!$user_id)
            return false;

        $this->db->where('user_id', $user_id);

        if ($only_active)
            $this->db->where('active', '1');

        $this->db->order_by('id', 'desc');

        $q = $this->db->get($this->table);
        return $q->result();
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
        if (!$id || empty($save))
            return false;

        $save['modified'] = time();

        $this->db->where('id', $id);
        $this->db->set($save);
        $this->db->update($this->table);
        return true;
    }

    function markDefault($warehouse_id = false, $user_id = false)
    {
        if (empty($warehouse_id) || empty($user_id))
            return false;

        $this->db->where('user_id', $user_id);
        $this->db->set('is_default', '0');
        $this->db->update($this->table);

        $save = array(
            'is_default' => '1'
        );
        $this->update($warehouse_id, $save);
        return true;
    }

    function countByUserID($user_id = false, $only_active = false)
    {
        if (!$user_id)
            return false;

        $this->db->select('count(DISTINCT id) as total');

        $this->db->where('user_id', $user_id);

        if ($only_active)
            $this->db->where('active', '1');

        $q = $this->db->get($this->table);
        return $q->row()->total;
    }

    function getUserWarehouseData($user_id = false, $limit = 50, $offset = 0, $only_active = false)
    {
        if (!$user_id)
            return false;

        $this->db->where('user_id', $user_id);
        $this->db->limit($limit);
        $this->db->offset($offset);

        if ($only_active)
            $this->db->where('active', '1');

        $this->db->order_by('id', 'desc');

        $q = $this->db->get($this->table);
        return $q->result();
    }

    function getUserWarehouseByDetails($user_id = false, $warehouse= false)
    {
        if (!$user_id || !$warehouse)
            return false;

        $this->db->select('id, warehouse_all_details');    
        $this->db->where('user_id', $user_id);
        $this->db->where('warehouse_all_details', $warehouse);
        $this->db->limit(1);
        $this->db->order_by('id', 'desc');
        $q = $this->db->get($this->table);
        return $q->row();
    }

    function getAllWarehouses()
    {
        $this->db->select('id, name, address_1, city, state, phone, zip'); 
        $q = $this->db->get($this->table);
        return $q->result();
    }
}
