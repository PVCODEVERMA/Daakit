<?php

class Plans_model extends MY_model
{
    public function __construct()
    {
        parent::__construct();
        $this->table = 'pricing_plans';
        $this->details_table = 'plan_details';
        $this->landing_table = 'landing_price';
    }

    function getPlanByName()
    {
        $q = $this->db->get($this->table);
        return $q->row();
    }

    function createPlan($save = array())
    {
        if (empty($save))
            return false;

        $save['created'] = time();
        $save['modified'] = time();

        $this->db->insert($this->table, $save);
        return $this->db->insert_id();
    }

    function updatePlan($id = false, $save = array())
    {
        if (empty($save) || empty($id))
            return false;

        $save['modified'] = time();

        $this->db->set($save);
        $this->db->where('id', $id);
        $this->db->update($this->table);
        return $this->db->insert_id();
    }

    function createLandingPrice($save = array())
    {
        if (empty($save))
            return false;

        $save['created'] = time();
        $save['modified'] = time();

        $this->db->insert($this->landing_table, $save);
        return $this->db->insert_id();
    }

    function updateLandingPrice($id = false, $save = array())
    {
        if (empty($save) || empty($id))
            return false;

        $save['modified'] = time();

        $this->db->set($save);
        $this->db->where('id', $id);
        $this->db->update($this->landing_table);
        return $this->db->insert_id();
    }

    function createPrice($save = array())
    {
        if (empty($save))
            return false;

        $save['created'] = time();
        $save['modified'] = time();

        $this->db->insert($this->details_table, $save);
        return $this->db->insert_id();
    }

    function updatePrice($id = false, $save = array())
    {
        if (empty($save) || empty($id))
            return false;

        $save['modified'] = time();

        $this->db->set($save);
        $this->db->where('id', $id);
        $this->db->update($this->details_table);
        return $this->db->insert_id();
    }


    function deletePlan($id = false)
    {
        if (!$id)
            return false;

        $this->db->where('id', $id);
        $this->db->delete($this->table);

        return true;
    }

    function deletePrice($id = false)
    {
        if (!$id)
            return false;

        $this->db->where('id', $id);
        $this->db->delete($this->details_table);

        return true;
    }

    function getAllPlans()
    {
        $this->db->order_by('id', 'desc');
        $q = $this->db->get($this->table);
        return $q->result();
    }

    function getLandingByCourierAndType($courier = false, $type = false)
    {
        if (!$courier || !$type)
            return false;

        $this->db->where('courier_id', $courier);
        $this->db->where('type', $type);
        $this->db->limit(1);

        $q = $this->db->get($this->landing_table);
        return $q->row();
    }

    function getAllLandingPrice()
    {
        $q = $this->db->get($this->landing_table);
        return $q->result();
    }

    function getPlanDetails($plan_id = false)
    {
        if (!$plan_id)
            return false;

        $this->db->where('plan_id', $plan_id);

        $q = $this->db->get($this->details_table);
        return $q->result();
    }

    function getPlanDetailsByCourierAndType($plan_id = false, $courier_id = false, $type = false)
    {
        if (!$plan_id || !$courier_id || !$type)
            return false;

        $this->db->where('plan_id', $plan_id);
        $this->db->where('courier_id', $courier_id);
        $this->db->where('type', $type);

        $this->db->limit(1);

        $q = $this->db->get($this->details_table);
        return $q->row();
    }

    function getUserCountByPlan()
    {
        $this->db->select('pricing_plan, count(*) as total');
        $this->db->group_by('pricing_plan');
        $this->db->order_by('total', 'desc');

        $q = $this->db->get('users');
        return $q->result();
    }

    function getNegativePricingPlans()
    {
        $this->db->select('');

        $this->db->join('pricing_plans as pp', 'pp.id = pd.plan_id', 'LEFT');
        $q = $this->db->get($this->details_table . ' as pd');
        return $q->result();
    }
}