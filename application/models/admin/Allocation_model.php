<?php

class Allocation_model extends MY_model
{

    public function __construct()
    {
        parent::__construct();
        $this->table = 'flat_price_allocation_rules';
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

    function getUserfilters($user_id = false, $active = false, $id = false, $plan_id = false)
    {
        $this->db->select("flat_price_allocation_rules.*, users.fname, users.lname");
        
        if ($id != '')
            $this->db->where('flat_price_allocation_rules.id', $id);
        
        if ($user_id)
            $this->db->where("find_in_set($user_id, flat_price_allocation_rules.user_id)");
        
        if ($plan_id)
            $this->db->where("find_in_set($plan_id, flat_price_allocation_rules.plan_id)");

        if ($active)
            $this->db->where('flat_price_allocation_rules.status', '1');

        $this->db->where('zone', null);
        $this->db->join('users', 'users.id = flat_price_allocation_rules.user_id', 'LEFT');
        $this->db->order_by('priority', 'asc');
        $q = $this->db->get($this->table);
        return $q->result();
    }

    function deleteByPlanId($plan_id = false)
    {
        if (!$plan_id)
            return false;

        $this->db->where('plan_id', $plan_id);
        $this->db->delete($this->table);

        return true;
    }

    function getUserfiltersByPlanId($plan_id = false, $active = false)
    {
        if (!$plan_id)
            return false;

        $this->db->where('plan_id', $plan_id);
        if ($active)
            $this->db->where('status', '1');

        $this->db->where('zone !=', null);
        $this->db->order_by('id', 'asc');
        $q = $this->db->get($this->table);
        return $q->result();
    }

    function batchInsert($save = array())
    {
        if (empty($save))
            return false;

        $this->db->insert_batch($this->table, $save);
        return true;
    }
}
