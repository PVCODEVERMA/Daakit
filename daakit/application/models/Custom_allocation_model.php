<?php

class Custom_allocation_model extends MY_model
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

    function getUserfilters($user_id = false, $active = false, $plan_id = false)
    {
        if (!$user_id)
            return false;

        $this->db->where("(find_in_set($user_id, user_id)")->or_where("find_in_set(0, user_id))");
        $this->db->where("(find_in_set($plan_id, plan_id)")->or_where("find_in_set(0, plan_id))");
        // $this->db->where_in('user_id', array($user_id, 0))->where_in('plan_id', array($plan_id, 0));
        
        if ($active)
            $this->db->where('status', '1');

        $this->db->where('zone', null);
        $this->db->order_by('user_id', 'desc');
        $this->db->order_by('plan_id', 'desc');
        $this->db->order_by('priority', 'asc');
        $q = $this->db->get($this->table);
        return $q->result();
    }

    function getUserfiltersByFilters($plan_id = false, $filter_name = false, $zone = false, $active = false)
    {
        if (!$plan_id || !$filter_name || !$zone)
            return false;

        $this->db->where('plan_id', $plan_id);
        $this->db->where('filter_name', $filter_name);
        $this->db->where('zone', $zone);

        if ($active)
            $this->db->where('status', '1');

        $this->db->order_by('priority', 'asc');
        $q = $this->db->get($this->table);
        return $q->result();
    }
}
