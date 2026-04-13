<?php

class Plans_model extends MY_model
{
    public function __construct()
    {
        parent::__construct();
        $this->table = 'pricing_plans';
        $this->details_table = 'plan_details';
        $this->landing_table = 'landing_price';
        $this->actual_landing_table = 'landing_price_actual';
    }

    function getPlanByName($name = false)
    {
        if (!$name)
            return false;

        $this->db->where('plan_name', $name);

        $this->db->limit(1);

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

    function getLandingPrice($courier_id, $type)
     {
    $this->db->where('courier_id', $courier_id);
    $this->db->where('type', $type);
    $this->db->limit(1);

    $q = $this->db->get('tbl_landing_price');
    return $q->row();
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


    function getAllPlans($plan_type = "")
    {
        $this->db->order_by('id', 'desc');
        if(!empty($plan_type))
            $this->db->where('plan_type', $plan_type);

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
        if (!$plan_id || !$type)
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

    function getUserPlanDetailByPlanType($planType)
    {
        if(!empty($planType))
            $this->db->where('tbl_pricing_plans.plan_type', $planType);

        $this->db->select("users.id AS seller_id,company_name AS company,CONCAT_WS(' ',fname,lname) as seller,tbl_pricing_plans.id AS plan_id, tbl_pricing_plans.plan_name plan, `tbl_pricing_plans`.`plan_type`");
        $this->db->join('pricing_plans', 'users.pricing_plan = pricing_plans.plan_name');
        $this->db->order_by('tbl_users.id', 'asc');
        $q = $this->db->get('users');
        return $q->result();
    }

    function getUserCountPlanDetail()
    {

        $this->db->select("`pp`.`id` AS `plan_id`, `pp`.`plan_name` `plan`,`pp`.`plan_type`,COUNT(1) AS total_users_count");
        $this->db->join('pricing_plans as pp', 'usr.pricing_plan = pp.plan_name');
        $this->db->group_by('`pp`.`plan_name`');
        $this->db->order_by('`pp`.`id`', 'asc');
        $q = $this->db->get('users as usr');
        return $q->result();
    }

    function getMarginPlanDetails()
    {
        $this->db->select("plan_details.id as plan_detail_id, plan_details.courier_id, courier.name, plan_details.plan_id, pricing_plans.plan_name, pricing_plans.plan_type,plan_details.type,plan_details.zone1,plan_details.zone2,plan_details.zone3,plan_details.zone4,plan_details.zone5,plan_details.min_cod,plan_details.cod_percent");
        $this->db->join('courier', 'courier.id = plan_details.courier_id');
        $this->db->join('pricing_plans ', 'pricing_plans.id=plan_details.plan_id');
        $this->db->order_by('plan_details.id', 'asc');
        $q = $this->db->get('plan_details');
        return $q->result();
    }

    function getLandingPlanDetails()
    {
        $this->db->select("landing_price.id as landing_price_id,landing_price.courier_id,courier.name,landing_price.type,landing_price.zone1,landing_price.zone2,landing_price.zone3,landing_price.zone4,landing_price.zone5,landing_price.min_cod,landing_price.cod_percent");
        $this->db->join('courier', 'courier.id=landing_price.courier_id');
        $this->db->order_by('landing_price.id', 'asc');
        $q = $this->db->get('landing_price');
        return $q->result_array();
    }

    function getPlanDetailsByPlanCourierTypeAndWeight($plan_id = false, $courier_id = false, $type = false, $courier_type = false, $weight = false)
    {
        if (!$plan_id || !$type || !$courier_type || !$weight)
            return false;

        $this->db->where('plan_id', $plan_id);
        $this->db->where('courier_id', $courier_id);
        $this->db->where('type', $type);
        $this->db->where('courier_type', $courier_type);
        $this->db->where('weight', $weight);

        $this->db->limit(1);

        $q = $this->db->get($this->details_table);
        return $q->row();
    }

    function getAllSmartPlans()
    {
        $this->db->where('plan_type', 'smart');
        $this->db->order_by('plan_name', 'asc');
        $q = $this->db->get($this->table);
        return $q->result();
    }

    function getCustomPlanByName($name = false)
    {
        if (!$name)
            return false;

        $this->db->where('plan_name', $name);
        $this->db->where('plan_type', 'smart');
        $this->db->limit(1);
        $q = $this->db->get($this->table);
        return $q->row();
    }

    function getSmartPlanById($plan_id = false, $status = false)
    {
        if (!$plan_id)
            return false;

        $this->db->select("CONCAT(courier_type, '_', weight, '_', additional_weight) AS courier_type_weight", FALSE);
        $this->db->where('plan_id', $plan_id);
        if($status != '') { $this->db->where('status', $status); }
        $this->db->group_by(['courier_type','weight','additional_weight']);
        $q = $this->db->get($this->details_table);
        return $q->result();
    }

    function getSmartPlanDetails($plan_id = false)
    {
        if (!$plan_id)
            return false;

        $this->db->where('plan_id', $plan_id);
        $this->db->where('courier_id', '0');
        $q = $this->db->get($this->details_table);
        return $q->result();
    }

    function deleteSmartPrice($id = false)
    {
        if (!$id)
            return false;

        $this->db->where('plan_id', $id);
        $this->db->where('courier_id', '0');
        $this->db->delete($this->details_table);
        return true;
    }

    function getActualLandingByCourierAndType($courier = false, $type = false)
    {
        if (!$courier || !$type)
            return false;

        $this->db->where('courier_id', $courier);
        $this->db->where('type', $type);
        $this->db->limit(1);

        $q = $this->db->get($this->actual_landing_table);
        return $q->row();
    }

    function createActualLandingPrice($save = array())
    {
        if (empty($save))
            return false;

        $save['created'] = time();
        $save['modified'] = time();

        $this->db->insert($this->actual_landing_table, $save);
        return $this->db->insert_id();
    }

    function updateActualLandingPrice($id = false, $save = array())
    {
        if (empty($save) || empty($id))
            return false;

        $save['modified'] = time();

        $this->db->set($save);
        $this->db->where('id', $id);
        $this->db->update($this->actual_landing_table);
        return $this->db->insert_id();
    }

    function getAllActualLandingPrice()
    {
        $q = $this->db->get($this->actual_landing_table);
        return $q->result();
    }

    function getActualLandingByCourierId($courier_id = false)
    {
        if (!$courier_id)
            return false;

        $this->db->where('courier_id', $courier_id);
        $q = $this->db->get($this->actual_landing_table);
        return $q->result();
    }
}
