<?php

class Escalation_model extends MY_model
{

    public function __construct()
    {
        parent::__construct();
        $this->table = 'escalations';
        $this->action_table = 'escalation_action';
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

    function getUserEscalations($user_id = false, $limit = 50, $offset = 0, $filter = array())
    {
        if (!$user_id)
            return false;

        $this->db->select("
            escalations.*,
            pickups.id as pickup_id,
            pickups.pickup_done as pickup_done,
            order_shipping.awb_number as awb_number,
            order_shipping.ship_status  as ship_status,
            order_shipping.charged_weight  as charged_weight,
            ");

        $this->db->where('escalations.user_id', $user_id);
        $this->db->order_by('escalations.last_action_id', 'desc');

        if (!empty($filter['start_date'])) {
            $this->db->where("escalations.created >= '" . $filter['start_date'] . "'");
        }

        if (!empty($filter['end_date'])) {
            $this->db->where("escalations.created <= '" . $filter['end_date'] . "'");
        }

        if (!empty($filter['status'])) {
            $this->db->where('escalations.escalation_status', $filter['status']);
        }

        if (!empty($filter['escalation_id'])) {
            $this->db->where_in('escalations.id', $filter['escalation_id']);
        }

        if (!empty($filter['type'])) {
            $this->db->where('escalations.type', $filter['type']);
        }

        if (!empty($filter['sub_type'])) {
            $this->db->where('escalations.sub_type', $filter['sub_type']);
        }

        if (!empty($filter['awb_number'])) {
            $this->db->where_in('order_shipping.awb_number', $filter['awb_number']);
        }

        $this->db->limit($limit);
        $this->db->offset($offset);

        $this->db->where('escalations.escalation_status !=', 'deleted');
        $this->db->where('escalations.type != ', 'internal');

        $this->db->join('pickups', "escalations.type = 'pickup' && pickups.id = escalations.ref_id", 'LEFT');
        $this->db->join('order_shipping', "(escalations.type = 'shipment' or escalations.type = 'weight') && order_shipping.id = escalations.ref_id", 'LEFT');
        $q = $this->db->get($this->table);

        // /echo $this->db->last_query();

        return $q->result();
    }


    function countUserEscalations($user_id = false, $filter = array())
    {
        if (!$user_id)
            return false;

        $this->db->select('count(DISTINCT escalations.id) as total');

        $this->db->where('escalations.user_id', $user_id);

        if (!empty($filter['start_date'])) {
            $this->db->where("escalations.created >= '" . $filter['start_date'] . "'");
        }

        if (!empty($filter['end_date'])) {
            $this->db->where("escalations.created <= '" . $filter['end_date'] . "'");
        }

        if (!empty($filter['status'])) {
            $this->db->where('escalations.escalation_status', $filter['status']);
        }

        if (!empty($filter['type'])) {
            $this->db->where('escalations.type', $filter['type']);
        }

        if (!empty($filter['sub_type'])) {
            $this->db->where('escalations.sub_type', $filter['sub_type']);
        }

        if (!empty($filter['escalation_id'])) {
            $this->db->where_in('escalations.id', $filter['escalation_id']);
        }

        if (!empty($filter['awb_number'])) {
            $this->db->where_in('order_shipping.awb_number', $filter['awb_number']);
        }

        $this->db->where('escalations.escalation_status !=', 'deleted');
        $this->db->where('escalations.type != ', 'internal');

        $this->db->join('pickups', "escalations.type = 'pickup' && pickups.id = escalations.ref_id", 'LEFT');
        $this->db->join('order_shipping', "(escalations.type = 'shipment' or escalations.type = 'weight') && order_shipping.id = escalations.ref_id", 'LEFT');
        $q = $this->db->get($this->table);

        // /echo $this->db->last_query();

        return $q->row()->total;
    }


    function getEscalationAction($esc_id = false)
    {
        if (!$esc_id)
            return false;

        $this->db->where('escalation_id', $esc_id);
        $this->db->order_by('id', 'asc');
        $q = $this->db->get($this->action_table);
        return $q->result();
    }

    function getEscilationDetails($id = false)
    {
        if (!$id)
            return false;

        $this->db->select("
            escalations.*,
            pickups.id as pickup_id,
            pickups.pickup_done as pickup_done,
            order_shipping.awb_number as awb_number,
            order_shipping.ship_status  as ship_status,
            order_shipping.charged_weight  as charged_weight,
            ");

        $this->db->where('escalations.id', $id);


        $this->db->join('pickups', "escalations.type = 'pickup' && pickups.id = escalations.ref_id", 'LEFT');
        $this->db->join('order_shipping', "(escalations.type = 'shipment' or escalations.type = 'weight') && order_shipping.id = escalations.ref_id", 'LEFT');
        $q = $this->db->get($this->table);

        // echo $this->db->last_query();

        return $q->row();
    }
    function getPickupDoneEscalation()
    {
        $this->slave = $this->load->database('slave', TRUE);

        $this->slave->select("e.id as escalation_id");
        $this->slave->where('p.pickup_done', '1');

        $this->slave->where_not_in('e.escalation_status', array('closed', 'deleted'));
        $this->slave->join('pickups as p', "e.type = 'pickup' && p.id = e.ref_id");
        $q = $this->slave->from($this->table . ' as e');

        $q = $this->slave->get();

        return $q->result();
    }

    function getShipmentDoneEscalation()
    {
        $this->slave = $this->load->database('slave', TRUE);

        $this->slave->select("e.id as escalation_id");

        $this->slave->where_in('s.ship_status', array('delivered'));

        $this->slave->where_not_in('e.escalation_status', array('closed', 'deleted'));

        $this->slave->where_in('e.sub_type', array('re-attempt', 're-attempt - fake remarks', 'urgent delivery', 'stuck in transit'));

        $this->slave->join('order_shipping as s', "e.type = 'shipment' && s.id = e.ref_id");
        $q = $this->slave->from($this->table . ' as e');

        $q = $this->slave->get();

        return $q->result();
    }

    function getEscalationForEmail()
    {
        $this->slave = $this->load->database('slave', TRUE);

        $this->slave->select("
        e.user_id as user_id,
        e.id as escalation_id,
        ea.id as escalation_action_id,
        u.fname,
        u.lname,
        u.company_name,
        u.email,
        ");
        $this->slave->where('ea.notified', '0');
        $this->slave->where('ea.assign_to', '0');
        $this->slave->where('ea.action_by', 'delta');

        $this->slave->where('e.type != ', 'internal');

        $this->slave->group_by('e.id');

        $this->slave->limit(100);

        $this->slave->join('escalations as e', 'e.id = ea.escalation_id', 'LEFT');
        $this->slave->join('users as u', 'u.id = e.user_id', 'LEFT');

        $q = $this->slave->get(' escalation_action as ea');

        return $q->result();
    }

    function getUSerbyEscID($esc_id = false)
    {
        if (!$esc_id)
            return false;

        $this->db->select('e.*, u.phone as phone, ');
        $this->db->where('e.id', $esc_id);
        $this->db->limit(1);
        $this->db->join('users as u', 'u.id = e.user_id', 'LEFT');
        $q = $this->db->get($this->table . ' as e');
        return $q->row();
    }


    function checkIfCallbackRecordExists($user_id = false, $department = false)
    {
        if (!$user_id ||  !$department)
            return false;

        $this->db->where('user_id', $user_id);
        $this->db->where('sub_type', $department);
        $this->db->where('type', 'callback');
        $this->db->where('escalation_status', 'new');

        $this->db->limit(1);
        $q = $this->db->get($this->table);
        return $q->row();
    }

    function mark_as_notified($ids = array())
    {
        if (empty($ids))
            return false;

        $this->db->set('notified', '1');
        $this->db->where_in('id', $ids);
        $this->db->update($this->action_table);
        return true;
    }

    function getAllEscalation()
    {
        $this->db->select("e.id as escalation_id, attachments");
        $this->db->where('attachments !=', '');
        $this->db->where('attachments NOT LIKE', '%amazonaws.com%');
        $this->db->from($this->action_table . ' as e');
        $this->db->group_by('attachments');
        $this->db->limit(50);
        $q = $this->db->get();
        return $q->result();
    }

    function updateAction($attachment, $save = array())
    {
        if (empty($save) || empty($attachment)) {
            return false;
        }

        $this->db->set($save);
        $this->db->where('attachments', $attachment);
        $this->db->update($this->action_table);
        return true;
    }

    function getShipmentEscalationTickets($shipment_id = false, $sub_type = array())
    {
        if (!$shipment_id && empty($sub_type)) {
            return false;
        }

        $this->slave = $this->load->database('slave', TRUE);

        $this->slave->select("{$this->table}.id as escalation_id, {$this->table}.sub_type");

        $this->slave->where_not_in("{$this->table}.escalation_status", array('closed', 'deleted'));

        $this->slave->where_in("{$this->table}.sub_type", $sub_type);

        $this->slave->join("order_shipping as s", "s.id = {$this->table}.ref_id");

        $this->slave->where('s.id', $shipment_id);

        $this->slave->where("{$this->table}.type", 'shipment');

        $q = $this->slave->from($this->table);

        $q = $this->slave->get();

        return $q->result();
    }

    function getUserActionReqEscalations($user_id = false, $filter = array())
    {
        if (!$user_id)
            return false;
        if (!empty($filter['start_date'])) {
            $this->db->where("created >= '" . $filter['start_date'] . "'");
        }
        if (!empty($filter['end_date'])) {
            $this->db->where("created <= '" . $filter['end_date'] . "'");
        }

        $this->db->select("count(*) as total");
        $this->db->where('escalations.user_id', $user_id);
        $this->db->where('escalations.escalation_status =', 'pending from seller');
        $q = $this->db->get($this->table);
        //echo $this->db->last_query();
        return $q->result();
    }


    function getEscalationId($ref_id = false, $type = false)
    {
        if (empty($ref_id) || empty($type))
            return false;
        $this->db->select("id as esc_id");
        $this->db->where('escalations.ref_id', $ref_id);
        $this->db->where('escalations.type', $type);
        $this->db->order_by('escalations.created', 'desc');
        $q = $this->db->get($this->table);
        return $q->row();
    }
}
