<?php

class Escalation_model extends MY_model
{

    protected $filters = array();

    protected $limit = 50;
    protected $offset = 0;

    protected $return_query = false;

    public function __construct()
    {
        parent::__construct();
        $this->table = 'escalations';
        $this->action_table = 'escalation_action';

        $this->slave = $this->load->database('default', TRUE);
        //pr($this->slave,1);
    }

     function getEscalationByRefIDType($ref_id = false, $type = false, $sub_type = false)
    {
        if (!$ref_id || !$type)
            return false;

        $this->db->where('ref_id', $ref_id);
        $this->db->where('type', $type);

        if ($sub_type)
            $this->db->where('sub_type', $sub_type);

        $this->db->limit(1);
        $q = $this->db->get($this->table);
        return $q->row();
    }


    function insert($save = array())
    {
        if (empty($save))
            return false;

        $save['created'] = time();

        $this->db->insert($this->table, $save);
        return $this->db->insert_id();
    }

    function update($id = false, $save = array())
    {
        if (empty($save) || empty($id))
            return false;

        $this->db->set($save);
        $this->db->where('id', $id);
        $this->db->update($this->table);
        return true;
    }


    function insert_action($save = array())
    {
        if (empty($save))
            return false;

        $save['created'] = time();

        $this->db->insert($this->action_table, $save);
        return $this->db->insert_id();
    }

    function setLimit(int $value)
    {
        $this->limit = $value;
    }

    function applyLimit()
    {
        $this->slave->limit($this->limit);
    }

    function setOffset(int $value)
    {
        $this->offset = $value;
    }

    function applyOffset()
    {
        $this->slave->offset($this->offset);
    }

    function setStatusFilter($value = false)
    {
        $this->filters['status'] = $value;
    }

    function applyStatusFilter()
    {
        if (!empty($this->filters['status']))
            switch (strtolower($this->filters['status'])) {
                case 'seller replied':
                    $this->slave->where('escalations.last_action_by', 'seller');
                    $this->slave->where_not_in('escalations.escalation_status', array('new', 'closed'));
                    break;
                case 'reopened':
                    $this->slave->where('escalations.last_action_by', 'seller');
                    $this->slave->where_in('escalations.escalation_status', array('closed'));
                    break;
                case 'internally_escalated':
                        $this->slave->where('escalations.internally_escalated', 1);
                        break;

                default:
            
                    $this->slave->where('escalation_status', $this->filters['status']);
            }
    }

    function applyStatusInFilter()
    {
        if (!empty($this->filters['status_in'])) {
            $strsql = '';

            foreach ($this->filters['status_in'] as $status) {
                switch (strtolower($status)) {
                    case 'seller replied':
                        $strsql .= (!empty($strsql)) ?  " OR " : "";
                        $strsql .= "(escalations.last_action_by LIKE 'seller' AND 'escalations.escalation_status' NOT IN('new', 'closed'))";
                        break;
                    case 'reopened':
                        $strsql .= (!empty($strsql)) ?  " OR " : "";
                        $strsql .= "(escalations.last_action_by LIKE 'seller' AND escalations.escalation_status LIKE 'closed')";
                        break;
                    default:
                        $strsql .= (!empty($strsql)) ?  " OR " : "";
                        $strsql .= "escalation_status LIKE '" . $status . "'";
                }
            }
            $this->slave->where($strsql);
        }
    }

    function setMultiStatusFilter(array $values)
    {
        $this->filters['status_in'] = $values;
    }

    function applyMultiStatusFilter()
    {
        $status_in = !empty($this->filters['status_in']) ? $this->filters['status_in'] : [];
       // pr($status_in);

        if (!empty($status_in)) {
            if(in_array('seller replied', $status_in) && in_array('reopened', $status_in)) {
                $sr_key = array_search('seller replied', $status_in);
                $ro_key = array_search('reopened', $status_in);
                unset($status_in[$sr_key]);
                unset($status_in[$ro_key]);

                $strsql = "((escalations.last_action_by = 'seller' AND 'escalations.escalation_status' NOT IN ('new', 'closed')) OR (escalations.last_action_by = 'seller' AND escalations.escalation_status IN ('closed')))";
                if (!empty($status_in)) {
                    $status_in = implode("','", $status_in);
                    $strsql = "((escalations.last_action_by = 'seller' AND 'escalations.escalation_status' NOT IN ('new', 'closed')) OR (escalations.last_action_by = 'seller' AND escalations.escalation_status IN ('closed')) OR (escalations.escalation_status IN ('$status_in')))";
                }

                $this->slave->where($strsql);

                $status_in = [];
            } else if(in_array('seller replied', $status_in)) {
                $sr_key = array_search('seller replied', $status_in);
                unset($status_in[$sr_key]);

                $strsql = "((escalations.last_action_by = 'seller' AND 'escalations.escalation_status' NOT IN ('new', 'closed')))";
                if (!empty($status_in)) {
                    $status_in = implode("','", $status_in);
                    $strsql = "((escalations.last_action_by = 'seller' AND 'escalations.escalation_status' NOT IN ('new', 'closed')) OR (escalations.escalation_status IN ('$status_in')))";
                }

                $this->slave->where($strsql);

                $status_in = [];
            } else if(in_array('reopened', $status_in)) {
                $ro_key = array_search('reopened', $status_in);
                unset($status_in[$ro_key]);

                $strsql = "((escalations.last_action_by = 'seller' AND escalations.escalation_status IN ('closed')))";
                if (!empty($status_in)) {
                    $status_in = implode("','", $status_in);
                    $strsql = "((escalations.last_action_by = 'seller' AND escalations.escalation_status IN ('closed')) OR (escalations.escalation_status IN ('$status_in')))";
                }

                $this->slave->where($strsql);

                $status_in = [];
            } else {}

            if (!empty($status_in))
                $this->slave->where_in('escalations.escalation_status', $status_in);
        }
    }

    function setMultiStatusNotInFilter(array $values)
    {
        $this->filters['status_not_in'] = $values;
    }

    function applyMultiStatusNotinFilter()
    {
        if (!empty($this->filters['status_not_in']))
            $this->slave->where_not_in('escalation_status', $this->filters['status_not_in']);
    }

    function setCourierFilter($value = false)
    {
        $this->filters['courier_id'] = $value;
    }

    function applyCourierFilter()
    {
        if (!empty($this->filters['courier_id']))
            $this->slave->where('order_shipping.courier_id', $this->filters['courier_id']);
    }

    function setMultiCourierFilter(array $values)
    {
        $this->filters['courier_id_in'] = $values;
    }


     function applyMultiCourierFilter()
    {
        if (!empty($this->filters['courier_id_in']))
            $this->slave->where_in('order_shipping.courier_id', $this->filters['courier_id_in']);
    }

    function setParentCourierFilter($value = false)
    {
        $this->filters['parent_courier_display_name'] = $value;
    }

    function applyParentCourierFilter($value = false)
    {
        if (!empty($this->filters['parent_courier_display_name'])) {
            $get_all_ids = $this->db->select('id')->from('courier')->where('display_name',$this->filters['parent_courier_display_name'])->get()->result();
            $ids = [];
            foreach($get_all_ids as $idd)
            {
                $ids[] = $idd->id;
            }
            $this->slave->where_in('order_shipping.courier_id', $ids);
        }
    }

    function setStartDateFilter(int $start_date)
    {
        $this->filters['start_date'] = $start_date;
    }

    function setEndDateFilter(int $end_date)
    {
        $this->filters['end_date'] = $end_date;
    }

    function applyDateFilter()
    {
        if (!empty($this->filters['start_date']))
            $this->slave->where("escalations.created >= " . $this->filters['start_date']);
        if (!empty($this->filters['end_date']))
            $this->slave->where("escalations.created <= " . $this->filters['end_date']);
    }

    function setSellerFilter($value = false)
    {
        $this->filters['seller_id'] = $value;
    }

    function applySellerFilter()
    {
        if (!empty($this->filters['seller_id']))
            $this->slave->where('escalations.user_id', $this->filters['seller_id']);
    }

    function setMultiSellerFilter(array $values)
    {
        $this->filters['seller_id_in'] = $values;
    }

    function applyMultiSellerFilter()
    {
        if (!empty($this->filters['seller_id_in']))
            $this->slave->where_in('escalations.user_id', $this->filters['seller_id_in']);
    }

    function setEscalationIDFilter(array $values)
    {
        $this->filters['escalation_id_in'] = $values;
    }

     function setProductIDFilter(array $values)
    {
        $this->filters['product_id_in'] = $values;
    }

    function applyEscalationIDFilter()
    {
        if (!empty($this->filters['escalation_id_in']))
            $this->slave->where_in('escalations.id', $this->filters['escalation_id_in']);
    }

      function applyProductIDFilter()
    {
        if (!empty($this->filters['product_id_in']))
            $this->slave->where_in('product_details.id', $this->filters['product_id_in']);
    }


    function setMultiEscalationIDFilter(array $values)
    {
        $this->filters['escalation_id_in'] = $values;
    }

    function applyMultiEscalationIDFilter()
    {
        if (!empty($this->filters['escalation_id_in']))
            $this->slave->where_in('escalations.id', $this->filters['escalation_id_in']);
    }

    function setManagerFilter($value = false)
    {
        $this->filters['manager_id'] = $value;
    }

    function setSupportCategoryFilter($value = false)
    {
        $this->filters['support_category'] = $value;
    }

    function applySupportCategoryFilter()
    {
        if (!empty($this->filters['support_category']))
            $this->slave->where_in('users.support_category', $this->filters['support_category']);
    }

    function applyManagerFilter()
    {
        if (!empty($this->filters['manager_id']))
            $this->slave->where('users.account_manager_id', $this->filters['manager_id']);
    }

    function setMultiManagerFilter(array $values)
    {
        $this->filters['manager_id_in'] = $values;
    }
    function setMultiAssignedFilter(array $values)
    {
        $this->filters['assign_to'] = $values;
    }

    function applyMultiManagerFilter()
    {
        if (!empty($this->filters['manager_id_in']))
            $this->slave->where_in('users.account_manager_id', $this->filters['manager_id_in']);
    }

    function applyMultiAssignedFilter()
    {
        if (!empty($this->filters['assign_to']))
            $this->slave->where_in('escalations.assign_to', $this->filters['assign_to']);
    }

    function setRaisedByFilter($value = false)
    {
        $this->filters['raised_by'] = $value;
    }

    function applyRaisedByFilter()
    {
        if (!empty($this->filters['raised_by']))
            $this->slave->where('escalations.created_by', $this->filters['raised_by']);
    }


    function setEscTypeFilter($value = false)
    {
        $this->filters['escalation_type'] = $value;
    }

    function applyEscTypeFilter()
    {
        if (!empty($this->filters['escalation_type']))
            $this->slave->where('escalations.type', $this->filters['escalation_type']);
    }

    function setEscSubTypeFilter($value = false)
    {
        $this->filters['escalation_sub_type'] = $value;
    }

    function applyEscSubTypeFilter()
    {
        if (!empty($this->filters['escalation_sub_type']))
            $this->slave->where('escalations.sub_type', $this->filters['escalation_sub_type']);
    }

    function setMultiEscalationsFilter(array $values)
    {

        $this->filters['escalation_in_type'] = $values;
    }

    function applyMultiEscalationsFilter()
    {
        if (!empty($this->filters['escalation_in_type']))
            $this->slave->where_in('escalations.type', $this->filters['escalation_in_type']);
    }
    function setMultiPriorityFilter(array $values)
    {
        $this->filters['priority_in'] = $values;
    }

    function applyMultiPriorityFilter()
    {
        if (!empty($this->filters['priority_in']))
            $this->slave->where_in('escalations.priority', $this->filters['priority_in']);
    }

    function setOrderType($value = false)
    {
        $this->filters['order_type'] = $value;
    }

    function applyOrderType()
    {
        if (!empty($this->filters['order_type']))
        $this->slave->where('courier.order_type', $this->filters['order_type']);
       // $this->filters['order_type'] = $value;
    }

    function applyFilters()
    {
        $this->applyMultiStatusFilter();
        $this->applyMultiStatusNotinFilter();
        $this->applyCourierFilter();
        $this->applyMultiCourierFilter();
        $this->applyParentCourierFilter();
        $this->applyDateFilter();
        $this->applySellerFilter();
        $this->applyMultiSellerFilter();
        $this->applyEscalationIDFilter();
        $this->applyProductIDFilter();
        $this->applyMultiEscalationIDFilter();
        $this->applyManagerFilter();
        $this->applyMultiManagerFilter();
        $this->applyMultiAssignedFilter();
        $this->applyRaisedByFilter();
        $this->applyEscTypeFilter();
        $this->applyEscSubTypeFilter();
        $this->applySupportCategoryFilter();
        $this->applyMultiEscalationsFilter();
        $this->applyMultiPriorityFilter();
        $this->applyOrderType() ;
    }



    function getEscalationAction($esc_id = false)
    {
        if (!$esc_id)
            return false;

        $this->slave->select('escalation_action.*, users.fname, users.lname,usersassignto.fname assign_to_fname, usersassignto.lname assign_to_lname');

        $this->slave->where('escalation_id', $esc_id);
        $this->slave->order_by('id', 'desc');

        $this->slave->join('users', 'users.id = escalation_action.action_user_id', 'LEFT');
        $this->slave->join('users usersassignto', '(escalation_action.assign_to !=0 and usersassignto.id = escalation_action.assign_to)', 'LEFT');
        $q = $this->slave->get($this->action_table);
        return $q->result();
    }


    function escalationStatsCal()
    {
        $this->slave->select(
            "e.type as escalation_type,"
                . "sum(case when (e.created >= " . strtotime('-3 days midnight') . " ) then 1 else 0 end) as less_than_3,"
                . "sum(case when (e.created < " . strtotime('-3 days midnight') . " and e.created >= " . strtotime('-5 days midnight') . " ) then 1 else 0 end) as less_than_5,"
                . "sum(case when (e.created < " . strtotime('-5 days midnight') . " and e.created >= " . strtotime('-10 days midnight') . " ) then 1 else 0 end) as less_than_10,"
                . "sum(case when ( e.created < " . strtotime('-10 days midnight') . ") then 1 else 0 end) as more_than_10,"
                . "sum(case
                 when (e.type = 'pickup' and e.created < " . (time() - $this->config->item('esc_pickup_tat')) . " ) then 1
                 when (e.type = 'tech' and e.created < " . (time() - $this->config->item('esc_tech_tat')) . " ) then 1
                 when (e.type = 'weight' and e.created < " . (time() - $this->config->item('esc_weight_tat')) . " ) then 1
                 when (e.type = 'billing' and e.created < " . (time() - $this->config->item('esc_billing_tat')) . " ) then 1
                 when (e.type = 'callback' and e.created < " . (time() - $this->config->item('esc_callback_tat')) . " ) then 1
                  else 0 end) as tat_breached,"
                . "sum(1)  as total_escalations"
        );

        $this->slave->where_not_in('e.escalation_status', array('pending from seller', 'cancelled', 'closed', 'deleted'));
        $this->slave->where('e.type !=', 'shipment');
        $this->slave->group_by('e.type');

        $this->slave->order_by('total_escalations', 'desc');

        $q = $this->slave->get($this->table . ' as e');

        return $q->result();
    }

    function escalationStatsCalAccountManagerWise()
    {
        $this->slave->select(
            "e.type as escalation_type,"
                . "sum(case when (e.created >= " . strtotime('-3 days midnight') . " ) then 1 else 0 end) as less_than_3,"
                . "sum(case when (e.created < " . strtotime('-3 days midnight') . " and e.created >= " . strtotime('-5 days midnight') . " ) then 1 else 0 end) as less_than_5,"
                . "sum(case when (e.created < " . strtotime('-5 days midnight') . " and e.created >= " . strtotime('-10 days midnight') . " ) then 1 else 0 end) as less_than_10,"
                . "sum(case when ( e.created < " . strtotime('-10 days midnight') . " ) then 1 else 0 end) as more_than_10,"
                . "sum(case
                 when (e.type = 'pickup' and e.created < " . (time() - $this->config->item('esc_pickup_tat')) . " ) then 1
                 when (e.type = 'tech' and e.created < " . (time() - $this->config->item('esc_tech_tat')) . " ) then 1
                 when (e.type = 'weight' and e.created < " . (time() - $this->config->item('esc_weight_tat')) . " ) then 1
                 when (e.type = 'billing' and e.created < " . (time() - $this->config->item('esc_billing_tat')) . " ) then 1
                 when (e.type = 'callback' and e.created < " . (time() - $this->config->item('esc_callback_tat')) . " ) then 1
                  else 0 end) as tat_breached,"
                . "sum(1)  as total_escalations,"
                . "am.fname as am_fname,"
                . "am.lname as am_lname,"
                . "am.id as am_id,"
        );

        $this->slave->where_not_in('e.escalation_status', array('pending from seller', 'cancelled', 'closed', 'deleted'));
        $this->slave->where('e.type !=', 'shipment');
        $this->slave->group_by('e.type, u.account_manager_id');

        $this->slave->order_by('total_escalations', 'desc');

        $this->slave->join('users as u', 'u.id = e.user_id', 'LEFT');
        $this->slave->join('users as am', 'am.id = u.account_manager_id', 'LEFT');

        $q = $this->slave->get($this->table . ' as e');

        return $q->result();
    }

    function shipemntEscalationStatsCal()
    {
        $this->slave->select(
            "e.sub_type as shipment_type,"
                . "sum(case when (e.created >= " . strtotime('-3 days midnight') . " ) then 1 else 0 end) as less_than_3,"
                . "sum(case when (e.created < " . strtotime('-3 days midnight') . " and e.created >= " . strtotime('-5 days midnight') . " ) then 1 else 0 end) as less_than_5,"
                . "sum(case when (e.created < " . strtotime('-5 days midnight') . " and e.created >= " . strtotime('-10 days midnight') . " ) then 1 else 0 end) as less_than_10,"
                . "sum(case when ( e.created < " . strtotime('-10 days midnight') . " ) then 1 else 0 end) as more_than_10,"
                . "sum(case
                 when (e.sub_type = 're-attempt - fake remarks' and e.created < " . (time() - $this->config->item('esc_shipment_re_attempt_fr')) . " ) then 1
                 when (e.sub_type = 'urgent delivery' and e.created < " . (time() - $this->config->item('esc_shipment_urgent_delivery')) . " ) then 1
                 when (e.sub_type = 're-attempt' and e.created < " . (time() - $this->config->item('esc_shipment_re_attempt')) . " ) then 1
                 when (e.sub_type = 'stuck in transit' and e.created < " . (time() - $this->config->item('esc_shipment_stuck_shipment')) . " ) then 1
                 when (e.sub_type = 'hold shipment' and e.created < " . (time() - $this->config->item('esc_shipment_hold_shipment')) . " ) then 1
                 when (e.sub_type = 'lost or damaged' and e.created < " . (time() - $this->config->item('esc_shipment_lost_damanged')) . " ) then 1
                 when (e.sub_type = 'status mismatch' and e.created < " . (time() - $this->config->item('esc_shipment_status_mismatch')) . " ) then 1
                 when (e.sub_type = 'proof of delivery' and e.created < " . (time() - $this->config->item('esc_shipment_proof_of_delivery')) . " ) then 1
                 when (e.sub_type = 'self collect - branch address required' and e.created < " . (time() - $this->config->item('esc_shipment_self_collect')) . " ) then 1
                 when (e.sub_type = 'rto instruction' and e.created < " . (time() - $this->config->item('esc_shipment_rto_instructions')) . " ) then 1
                 when (e.sub_type = 'freight charges reversal' and e.created < " . (time() - $this->config->item('esc_shipment_charges_reversal')) . " ) then 1
                 when (e.sub_type = 'change payment type' and e.created < " . (time() - $this->config->item('esc_shipment_change_payment_type')) . " ) then 1
                 when (e.sub_type = 'other' and e.created < " . (time() - $this->config->item('esc_shipment_others')) . " ) then 1
                  else 0 end) as tat_breached,"
                . "sum(1)  as total_escalations"
        );

        $this->slave->where_not_in('e.escalation_status', array('pending from seller', 'cancelled', 'closed', 'deleted'));
        $this->slave->where('e.type', 'shipment');
        $this->slave->group_by('e.sub_type');

        $this->slave->order_by('total_escalations', 'desc');

        $q = $this->slave->get($this->table . ' as e');

        return $q->result();
    }

    function shipemntEscalationCourierWise()
    {
        $this->slave->select(
            "e.sub_type as shipment_type,"
                . "c.name as courier_name,"
                . "c.id as courier_id,"
                . "sum(1)  as total_escalations,"
                . "sum(case when (e.created >= " . strtotime('-3 days midnight') . " ) then 1 else 0 end) as less_than_3,"
                . "sum(case when (e.created < " . strtotime('-3 days midnight') . " and e.created >= " . strtotime('-5 days midnight') . " ) then 1 else 0 end) as less_than_5,"
                . "sum(case when (e.created < " . strtotime('-5 days midnight') . " and e.created >= " . strtotime('-10 days midnight') . " ) then 1 else 0 end) as less_than_10,"
                . "sum(case when ( e.created < " . strtotime('-10 days midnight') . " ) then 1 else 0 end) as more_than_10,"
                . "sum(case
                 when (e.sub_type = 're-attempt - fake remarks' and e.created < " . (time() - $this->config->item('esc_shipment_re_attempt_fr')) . " ) then 1
                 when (e.sub_type = 'urgent delivery' and e.created < " . (time() - $this->config->item('esc_shipment_urgent_delivery')) . " ) then 1
                 when (e.sub_type = 're-attempt' and e.created < " . (time() - $this->config->item('esc_shipment_re_attempt')) . " ) then 1
                 when (e.sub_type = 'stuck in transit' and e.created < " . (time() - $this->config->item('esc_shipment_stuck_shipment')) . " ) then 1
                 when (e.sub_type = 'hold shipment' and e.created < " . (time() - $this->config->item('esc_shipment_hold_shipment')) . " ) then 1
                 when (e.sub_type = 'lost or damaged' and e.created < " . (time() - $this->config->item('esc_shipment_lost_damanged')) . " ) then 1
                 when (e.sub_type = 'status mismatch' and e.created < " . (time() - $this->config->item('esc_shipment_status_mismatch')) . " ) then 1
                 when (e.sub_type = 'proof of delivery' and e.created < " . (time() - $this->config->item('esc_shipment_proof_of_delivery')) . " ) then 1
                 when (e.sub_type = 'self collect - branch address required' and e.created < " . (time() - $this->config->item('esc_shipment_self_collect')) . " ) then 1
                 when (e.sub_type = 'rto instruction' and e.created < " . (time() - $this->config->item('esc_shipment_rto_instructions')) . " ) then 1
                 when (e.sub_type = 'freight charges reversal' and e.created < " . (time() - $this->config->item('esc_shipment_charges_reversal')) . " ) then 1
                 when (e.sub_type = 'change payment type' and e.created < " . (time() - $this->config->item('esc_shipment_change_payment_type')) . " ) then 1
                 when (e.sub_type = 'other' and e.created < " . (time() - $this->config->item('esc_shipment_others')) . " ) then 1
                  else 0 end) as tat_breached,"
        );

        $this->slave->where_not_in('e.escalation_status', array('pending from seller', 'cancelled', 'closed', 'deleted'));
        $this->slave->where('e.type', 'shipment');

        $this->slave->join('order_shipping as s', 's.id = e.ref_id', 'LEFT');
        $this->slave->join('courier as c', 'c.id = s.courier_id', 'LEFT');
        $this->slave->group_by('e.sub_type, s.courier_id');

        $this->slave->order_by('total_escalations', 'desc');

        $q = $this->slave->get($this->table . ' as e');

        return $q->result();
    }


    function shipemntEscalationAccountManagerWise()
    {
        $this->slave->select(
            "e.sub_type as shipment_type,"
                . "am.fname as am_fname,"
                . "am.lname as am_lname,"
                . "am.id as am_id,"
                . "sum(1)  as total_escalations,"
                . "sum(case when (e.created >= " . strtotime('-3 days midnight') . " ) then 1 else 0 end) as less_than_3,"
                . "sum(case when (e.created < " . strtotime('-3 days midnight') . " and e.created >= " . strtotime('-5 days midnight') . " ) then 1 else 0 end) as less_than_5,"
                . "sum(case when (e.created < " . strtotime('-5 days midnight') . " and e.created >= " . strtotime('-10 days midnight') . " ) then 1 else 0 end) as less_than_10,"
                . "sum(case when ( e.created < " . strtotime('-10 days midnight') . " ) then 1 else 0 end) as more_than_10,"
                . "sum(case
                 when (e.sub_type = 're-attempt - fake remarks' and e.created < " . (time() - $this->config->item('esc_shipment_re_attempt_fr')) . " ) then 1
                 when (e.sub_type = 'urgent delivery' and e.created < " . (time() - $this->config->item('esc_shipment_urgent_delivery')) . " ) then 1
                 when (e.sub_type = 're-attempt' and e.created < " . (time() - $this->config->item('esc_shipment_re_attempt')) . " ) then 1
                 when (e.sub_type = 'stuck in transit' and e.created < " . (time() - $this->config->item('esc_shipment_stuck_shipment')) . " ) then 1
                 when (e.sub_type = 'hold shipment' and e.created < " . (time() - $this->config->item('esc_shipment_hold_shipment')) . " ) then 1
                 when (e.sub_type = 'lost or damaged' and e.created < " . (time() - $this->config->item('esc_shipment_lost_damanged')) . " ) then 1
                 when (e.sub_type = 'status mismatch' and e.created < " . (time() - $this->config->item('esc_shipment_status_mismatch')) . " ) then 1
                 when (e.sub_type = 'proof of delivery' and e.created < " . (time() - $this->config->item('esc_shipment_proof_of_delivery')) . " ) then 1
                 when (e.sub_type = 'self collect - branch address required' and e.created < " . (time() - $this->config->item('esc_shipment_self_collect')) . " ) then 1
                 when (e.sub_type = 'rto instruction' and e.created < " . (time() - $this->config->item('esc_shipment_rto_instructions')) . " ) then 1
                 when (e.sub_type = 'freight charges reversal' and e.created < " . (time() - $this->config->item('esc_shipment_charges_reversal')) . " ) then 1
                 when (e.sub_type = 'change payment type' and e.created < " . (time() - $this->config->item('esc_shipment_change_payment_type')) . " ) then 1
                 when (e.sub_type = 'other' and e.created < " . (time() - $this->config->item('esc_shipment_others')) . " ) then 1
                  else 0 end) as tat_breached,"
        );

        $this->slave->where_not_in('e.escalation_status', array('pending from seller', 'cancelled', 'closed', 'deleted'));
        $this->slave->where('e.type', 'shipment');

        $this->slave->group_by('e.sub_type, u.account_manager_id');

        $this->slave->join('users as u', 'u.id = e.user_id', 'LEFT');
        $this->slave->join('users as am', 'am.id = u.account_manager_id', 'LEFT');


        $this->slave->order_by('total_escalations', 'desc');

        $q = $this->slave->get($this->table . ' as e');

        return $q->result();
    }


    function getWeightEscIDUsingAWBNumber($awb_number = false)
    {
        if (!$awb_number)
            return false;
        $this->slave->select('e.id');

        $this->slave->limit(1);

        $this->slave->where('e.type', 'weight');

        $this->slave->where('s.awb_number', $awb_number);

        $this->slave->join('escalations as e', " e.ref_id = s.id and e.type='weight'");
        $q = $this->slave->get('order_shipping as s');
        return $q->row()->id;
    }

    function update_start_log_date($id, $save = array())
    {

        if (empty($save) || empty($id))
            return false;

        $this->db->set($save);
        $this->db->where('id', $id);
        $this->db->where('esc_started_date', '0');
        $this->db->update($this->table);
        return true;
    }

    function update_closed_log_date($id, $save = array())
    {

        if (empty($save) || empty($id))
            return false;

        $this->db->set($save);
        $this->db->where('id', $id);
        $this->db->where('esc_closed_date', '0');
        $this->db->update($this->table);
        return true;
    }


    /*function getEscalationActionAssignTo($esc_id = false)
    {
        if (!$esc_id)
            return false;

        $this->db->select('escalation_action.id, users.fname assign_to_fname, users.lname assign_to_lname');

        $this->db->where('escalation_id', $esc_id);
        $this->db->order_by('id', 'desc');

        $this->db->join('usersassignto', 'users.id = escalation_action.assign_to', 'LEFT');
        $q = $this->db->get($this->action_table);
        return $q->result();
    }*/
    function countMyTicketEscalations()
    {
        $this->slave->select('count(*) as total');


        $this->applyFilters();

        $this->applyStatusFilter();

        $this->slave->join('escalation_action', 'escalation_action.id=escalations.last_action_id');
        $this->slave->join('users', 'users.id = escalations.user_id', 'LEFT');
        $q = $this->slave->get($this->table);
        return $q->row()->total;
    }
    function getMyTicketEscalations()
    {
        $this->slave->select(
            "
		users.fname as user_fname,
        users.lname as user_lname,
        users.id as user_id,
		users.company_name as user_company,
		users.email as company_emails,
		users.phone as company_phone,
		users.account_manager_id as manager_id,
        escalations.created as escalation_date,
        escalations.priority as escalation_priority,
        escalation_status as escalation_status,
        escalations.id as escalation_id,
        escalations.created_by as created_by,
        escalations.type as escalations_type
        "
        );

        $this->applyFilters();

        $this->applyStatusFilter();
        $this->applyMultiStatusFilter();
        $this->applyMultiStatusNotinFilter();

        $this->applyLimit();
        $this->applyOffset();

        $this->slave->order_by('escalations.id', 'desc');

        $this->slave->join('escalation_action', 'escalation_action.id=escalations.last_action_id');
        $this->slave->join('users', 'users.id = escalations.user_id', 'LEFT');
        $q = $this->slave->get($this->table);



        return $q->result();
    }

    function getCsvMyticketEscalations()
    {
        $this->slave->select(
            " users.id as user_id,
            users.company_name as user_company,
            users.account_manager_id as manager_id,
            escalation_action.esc_status,
            escalation_action.remarks,
            escalations.priority as escalation_priority,
            escalations.created as escalation_date,
            escalation_status as escalation_status,
            escalations.created_by as created_by,
            escalations.id as escalation_id,
            users.support_category,
            escalations.esc_started_date AS first_action_date,
            escalations.esc_closed_date AS first_close_date,
            escalations.type as escalations_type"

        );

        $this->applyFilters();
        $this->applyStatusFilter();
        $this->applyMultiStatusFilter();
        $this->applyMultiStatusNotinFilter();

        $this->slave->order_by('escalations.id', 'desc');

        $this->slave->join('escalation_action', 'escalation_action.id=escalations.last_action_id');
        $this->slave->join('users', 'users.id = escalations.user_id', 'LEFT');
        $this->slave->from($this->table);

        return $query =   $this->slave->get_compiled_select();
    }
    function countTicketsStatusGrouped()
    {
        $this->slave->select('escalations.escalation_status as escalation_status, count(*) as total');

        $this->applyFilters();

        $this->slave->group_by('escalations.escalation_status');

        $this->slave->join('escalation_action', 'escalation_action.id=escalations.last_action_id');

        $this->slave->join('users', 'users.id = escalations.user_id', 'LEFT');

        $q = $this->slave->get($this->table);


        return $q->result();
    }
    function countMyTicketReplied($filter = array())
    {
        $this->slave->select('count(*) as total');

        $this->slave->where_not_in('escalation_status', array('new', 'closed'));
        $this->slave->where('escalations.last_action_by', 'seller');

        $this->applyFilters();

        $this->slave->join('escalation_action', 'escalation_action.id=escalations.last_action_id');

        $this->slave->join('users', 'users.id = escalations.user_id', 'LEFT');

        $q = $this->slave->get($this->table);


        return $q->row()->total;
    }

    function count_internally_escelated()
    {

        $this->slave->select('count(*) as total');

        $this->applyFilters();
        $this->slave->where('escalations.internally_escalated', 1);

        $this->slave->join('escalation_action', 'escalation_action.id=escalations.last_action_id');
        $this->slave->join('order_shipping', 'order_shipping.id = escalations.ref_id', 'LEFT');
        $this->slave->join('weight_reco', 'weight_reco.shipment_id = escalations.ref_id', 'LEFT');
        $this->slave->join('users', 'users.id = escalations.user_id', 'LEFT');
        $this->slave->join('courier', 'courier.id = order_shipping.courier_id', 'LEFT');
        $q = $this->slave->get($this->table);
       return $q->row()->total;


    }
    function countMyTicketReopened()
    {
        $this->slave->select('count(*) as total');

        $this->slave->where_in('escalation_status', array('closed'));
        $this->slave->where('escalations.last_action_by', 'seller');

        $this->applyFilters();

        $this->slave->join('escalation_action', 'escalation_action.id=escalations.last_action_id');

        $this->slave->join('users', 'users.id = escalations.user_id', 'LEFT');

        $q = $this->slave->get($this->table);
        return $q->row()->total;
    }

    function getAllEscalationTypes()
    {
        $this->slave->select('e.type');
        $this->slave->group_by('e.type');
        $this->slave->order_by('e.type', 'asc');
        $q = $this->slave->get($this->table . ' as e');
        return $q->result();
    }

    function getEscalationSellerReport()
    {
        $escalations_type = $this->getAllEscalationTypes();

        if (empty($escalations_type))
            return false;

        $query = "";
        foreach ($escalations_type as $escalation) {
            $type = $escalation->type;

            $query .= ", sum(case when (escalations.type = '$type' and escalations.escalation_status = 'new') then 1 else 0 end) as " . $type . "_new, ";
            $query .= "sum(case when (escalations.type = '$type' and (escalations.escalation_status NOT IN ('new','closed'))) then 1 else 0 end) as " . $type . "_open, ";
            $query .= "sum(case when (escalations.type = '$type' and escalations.escalation_status = 'closed') then 1 else 0 end) as " . $type . "_closed";
        }

        $this->slave->select("escalations.user_id, users.account_manager_id, users.company_name, users.fname, users.lname, am.fname as am_fname, am.lname as am_lname $query");

        $this->slave->where('escalations.escalation_status !=', 'deleted');

        $this->slave->group_by('escalations.user_id');

        $this->slave->join('users as users', 'users.id = escalations.user_id');
        $this->slave->join('users as am', 'am.id = users.account_manager_id');

        $this->slave->order_by('am_fname, company_name, fname', 'asc');

        $this->applyFilters();

        $q = $this->slave->get($this->table . ' as escalations');

        return $q->result();
    }

    function getCsvEscalationSellerReport()
    {
        $escalations_type = $this->getAllEscalationTypes();

        if (empty($escalations_type))
            return false;

        $query = "";
        foreach ($escalations_type as $escalation) {
            $type = $escalation->type;

            $query .= ", sum(case when (escalations.type = '$type' and escalations.escalation_status = 'new') then 1 else 0 end) as " . $type . "_new, ";
            $query .= "sum(case when (escalations.type = '$type' and (escalations.escalation_status NOT IN ('new','closed'))) then 1 else 0 end) as " . $type . "_open, ";
            $query .= "sum(case when (escalations.type = '$type' and escalations.escalation_status = 'closed') then 1 else 0 end) as " . $type . "_closed";
        }

        $this->slave->select("users.company_name, users.fname, users.lname, am.fname as am_fname, am.lname as am_lname $query");

        $this->slave->where('escalations.escalation_status !=', 'deleted');

        $this->slave->group_by('escalations.user_id');

        $this->slave->join('users as users', 'users.id = escalations.user_id');
        $this->slave->join('users as am', 'am.id = users.account_manager_id');

        $this->slave->order_by('am_fname, company_name, fname', 'asc');

        $this->applyFilters();

        $this->slave->from($this->table);

        return $query = $this->slave->get_compiled_select();
    }

    function update_action($id, $save = array())
    {
      
        if (empty($save) || empty($id))
            return false;
        $this->db->set($save);
        $this->db->where('id', $id);
        $this->db->update('escalations');
        return true;

    }
}
