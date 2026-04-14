<?php

class Operation_model extends MY_model
{

    public function __construct()
    {
        parent::__construct();
    }

    function NDRByMessageGrouped($user_id = false, $filter = array())
    {
        if (!$user_id)
            return false;

        //first get all active ndr and then fetch latest remarks

        $this->db->select(
            'max(ndr_action.id) as max_id'
        );

        $this->db->where_not_in('order_shipping.ship_status', array('delivered', 'rto', 'lost'));
        $this->db->where('order_shipping.ship_status !=', 'cancelled');

        $this->db->where('ndr.user_id', $user_id);

        $this->db->join('order_shipping', 'order_shipping.id = ndr.shipment_id');
        $this->db->join('ndr_action', 'ndr_action.ndr_id = ndr.id');

        $this->db->group_by('ndr_action.ndr_id');
        $this->db->where('ndr_action.source', 'courier');

        $this->db->from('ndr');


        $where_clause = $this->db->get_compiled_select();


        $this->db->select(" count(*) as total_count, ndr_action.remarks as ndr_remarks, group_concat(ndr_action.ndr_id) as ndr_ids ");
        $this->db->where(" ndr_action.id in ($where_clause)", NULL, FALSE);
        $this->db->group_by('ndr_action.remarks');

        $q = $this->db->get('ndr_action');
        return $q->result();
    }

    function stuckedShipments($user_id = false)
    {
        if (!$user_id)
            return false;

        $this->db->select('courier_id, count(*) as total');

        $this->db->where('user_id', $user_id);
        $this->db->where('status_updated_at <', strtotime('-3 days midnight'));
        $this->db->where('status_updated_at !=', '');


        $this->db->where_in('ship_status', $this->config->item('shipment_in_transit_status'));

        $this->db->group_by('courier_id');

        $this->db->order_by('total', 'desc');

        $q = $this->db->get('order_shipping');


        //echo $this->db->last_query();
        // exit;

        return $q->result();
    }
}
