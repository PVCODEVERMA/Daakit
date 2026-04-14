<?php

class Pickups_model extends MY_model
{

    public function __construct()
    {
        parent::__construct();
        $this->table = 'pickups';
        $this->pickup_data = 'pickup_data';
    }

    function insert($save = array())
    {
        if (empty($save))
            return false;

        $save['created'] = time();

        $this->db->insert($this->table, $save);
        return $this->db->insert_id();
    }

    function markPickedUP($shipment_id = false, $user_id = false)
    {
        if (!$shipment_id)
            return false;

        $this->db->select('id,pickup_id');
        $this->db->where('pickup_done', '0');
        if ($user_id) {
            $this->db->where('user_id', $user_id);
        }
        // $this->db->where("FIND_IN_SET('{$shipment_id}', shipment_ids)");
        // $q = $this->db->get($this->table);  

        $this->db->where('shipment_id', $shipment_id);
        $q = $this->db->get($this->pickup_data); 

        $ids = $q->result();
        
        if (empty($ids))
            return true;

        $mark_ids = array();
        $pickup_ids = array();
        foreach ($ids as $id) {
            $mark_ids[] = $id->id;
            $pickup_ids[] = $id->pickup_id;
        }
      
        if (empty($mark_ids) && empty($pickup_ids))
            return false;

        $this->db->where_in('id', $pickup_ids);
        $this->db->set('pickup_done', '1');
        $this->db->update($this->table);

        $this->db->where_in('id', $mark_ids);
        $this->db->set('pickup_done', '1');
        $this->db->update($this->pickup_data);

        return true;
    }

    function getUserPickups($user_id = false, $limit = 50, $offset = 0, $filter = array())
    {   
        if (!$user_id)
            return false;

        $this->db->simple_query('SET SESSION group_concat_max_len=55555555');
        $this->db->select(
            'pickups.id as id,'
                . 'pickup_number,'
                . 'pickups.created as pickup_created,'
                . 'GROUP_CONCAT(`tbl_pickup_data`.`shipment_id`) as `shipment_ids`,'
                . '`tbl_pickup_data`.pickup_done,'
                . 'pickups.escalated_status,'
                . 'pickups.escalation_time,'
                . 'courier.name as courier_name,'
                . 'warehouse.name as warehouse_name,'
                . 'courier.order_type as order_type,'
        );

        if (!empty($filter['start_date'])) {
            $this->db->where("pickups.created >= '" . $filter['start_date'] . "'");
        }

        if (!empty($filter['end_date'])) {
            $this->db->where("pickups.created <= '" . $filter['end_date'] . "'");
        }

        if (!empty($filter['pickup_number'])) {
            $this->db->where_in('pickups.pickup_number', $filter['pickup_number']);
        }

        if (!empty($filter['pickup_id'])) {
            $this->db->where_in('pickups.id', $filter['pickup_id']);
        }

        if (!empty($filter['courier_ids'])) {
            $this->db->where_in('pickups.courier_id', $filter['courier_ids']);
        }

        if (!empty($filter['courier_id'])) {
            $this->db->where('pickups.courier_id', $filter['courier_id']);
        }

        if (!empty($filter['order_type'])) {
            $this->db->where('courier.order_type', $filter['order_type']);
        }

        if (!empty($filter['warehouse_id'])) {
            $this->db->where('pickups.warehouse_id', $filter['warehouse_id']);
        }

        if (!empty($filter['pickup_done'])) {
            if ($filter['pickup_done'] == 'yes') {
                $this->db->where('pickups.pickup_done', '1');
            } else {
                $this->db->where('pickups.pickup_done', '0');
            }
        }

        $this->db->limit($limit);
        $this->db->offset($offset);

        $this->db->where('pickups.user_id', $user_id);
        $this->db->group_by('tbl_pickup_data.pickup_id'); 

        if (isset($filter['orderbyid']) && ($filter['orderbyid'] == 'asc' || $filter['orderbyid'] == 'desc'))
            $this->db->order_by('pickups.id', strtolower($filter['orderbyid']));
        else
            $this->db->order_by('pickups.created', 'desc');

        $this->db->join('courier', 'courier.id = pickups.courier_id', 'LEFT');
        $this->db->join('warehouse', 'warehouse.id = pickups.warehouse_id', 'LEFT');
        $this->db->join('pickup_data', 'tbl_pickup_data.pickup_id = pickups.id', 'LEFT');

        $q = $this->db->get($this->table);

        return $q->result();
    }

    function countUserPickups($user_id = false, $filter = array())
    {
        if (!$user_id)
            return false;

        $this->db->select('count(*) as total');

        if (!empty($filter['pickup_number'])) {
            $this->db->where_in('pickups.pickup_number', $filter['pickup_number']);
        }
        if (!empty($filter['courier_id'])) {
            $this->db->where('pickups.courier_id', $filter['courier_id']);
        }

        if (!empty($filter['courier_ids'])) {
            $this->db->where_in('pickups.courier_id', $filter['courier_ids']);
        }

        if (!empty($filter['pickup_done'])) {
            if ($filter['pickup_done'] == 'yes') {
                $this->db->where('pickups.pickup_done', '1');
            } else {
                $this->db->where('pickups.pickup_done', '0');
            }
        }

        if (!empty($filter['order_type'])) {
            $this->db->where('courier.order_type', $filter['order_type']);
        }

        if (!empty($filter['warehouse_id'])) {
            $this->db->where('pickups.warehouse_id', $filter['warehouse_id']);
        }

        if (!empty($filter['start_date'])) {
            $this->db->where("pickups.created >= '" . $filter['start_date'] . "'");
        }

        if (!empty($filter['pickup_id'])) {
            $this->db->where_in('pickups.id', $filter['pickup_id']);
        }

        if (!empty($filter['end_date'])) {
            $this->db->where("pickups.created <= '" . $filter['end_date'] . "'");
        }

        $this->db->where('pickups.user_id', $user_id);
        $this->db->order_by('pickups.created', 'desc');

        $this->db->join('courier', 'courier.id = pickups.courier_id', 'LEFT');

        $q = $this->db->get($this->table);
        return $q->row()->total;
    }

    function getManifestData($shipment_id = false)
    {
    }

    function update_esc_status($user_id = false, $pickup_id = false)
    {
        if (empty($user_id) && empty($pickup_id))
            return false;

        $this->db->where_in('id', $pickup_id);
        $this->db->where('user_id', $user_id);
        $this->db->set('escalated_status', '1');
        $this->db->set('escalation_time', time());
        $this->db->update($this->table);
        return true;
    }

    function insert_pickup_data($save = array())
    {
        if (empty($save))
            return false;

        $this->db->insert($this->pickup_data, $save);
        return $this->db->insert_id();
    }

    function getShipmentidsByPickdata($id = false){
        if(!$id)
            return false;

        $this->db->simple_query('SET SESSION group_concat_max_len=55555555');
        $this->db->select('pickups.id as id,'
            . 'pickups.user_id as user_id,'
            . 'pickups.pickup_number,'
            . 'pickups.created,'
            . 'pickups.courier_id,'
            . 'pickups.warehouse_id,'
            . 'pickups.created as pickup_created,'
            . 'GROUP_CONCAT(`tbl_pickup_data`.`shipment_id`) as `shipment_ids`,'
            . '`tbl_pickup_data`.pickup_done,'
            . 'pickups.escalated_status,'
            . 'pickups.escalation_time,'
            . 'warehouse.name as warehouse_name,'
        );

        $this->db->where('pickups.id', $id);
        $this->db->group_by('tbl_pickup_data.pickup_id'); 
        $this->db->join('pickup_data', 'tbl_pickup_data.pickup_id = pickups.id', 'LEFT');
        $this->db->join('warehouse', 'warehouse.id = pickups.warehouse_id', 'LEFT');

        $q = $this->db->get('pickups');
        return $q->row();
    }

    function matchManifestData($filter = array()) {
        if (empty($filter))
            return false;

        $this->db->select('id');
        $this->db->where('user_id', $filter['user_id']);
        $this->db->where('courier_id', $filter['courier_id']);
        $this->db->where('pickup_number', $filter['pickup_number']);
        $this->db->where('warehouse_id', $filter['warehouse_id']);
        $q = $this->db->get($this->table);
        return $q->row();
    }

    function matchPickupData($filter = array()) {
        if (empty($filter))
            return false;

        $this->db->select('id');
        $this->db->where('user_id', $filter['user_id']);
        $this->db->where('pickup_id', $filter['pickup_id']);
        $this->db->where('shipment_id', $filter['shipment_id']);
        $q = $this->db->get($this->pickup_data);
        return $q->row();
    }

    function exportByUserID($user_id = false, $limit = 50, $offset = 0, $filter = array())
    {   
        if (!$user_id)
            return false;

        $subQuery = $this->db
            ->select('GROUP_CONCAT(tbl_pickup_data.shipment_id)')
                ->from('pickup_data')
                ->where('pickup_data.pickup_id = pickups.id')
                ->get_compiled_select();
        
        $this->db->select('pickups.id as id,pickup_number,(' . $subQuery . ') as shipment_ids,
        courier.name as courier_name,warehouse.name as warehouse_name'
        );
     
        if (!empty($filter['start_date'])) {
            $this->db->where("pickups.created >= '" . $filter['start_date'] . "'");
        }

        if (!empty($filter['end_date'])) {
            $this->db->where("pickups.created <= '" . $filter['end_date'] . "'");
        }

        if (!empty($filter['pickup_number'])) {
            $this->db->where_in('pickups.pickup_number', $filter['pickup_number']);
        }

        if (!empty($filter['pickup_id'])) {
            $this->db->where_in('pickups.id', $filter['pickup_id']);
        }

        if (!empty($filter['courier_ids'])) {
            $this->db->where_in('pickups.courier_id', $filter['courier_ids']);
        }

        if (!empty($filter['courier_id'])) {
            $this->db->where('pickups.courier_id', $filter['courier_id']);
        }

        if (!empty($filter['order_type'])) {
            $this->db->where('courier.order_type', $filter['order_type']);
        }

        if (!empty($filter['warehouse_id'])) {
            $this->db->where('pickups.warehouse_id', $filter['warehouse_id']);
        }

        if (!empty($filter['pickup_done'])) {
            $this->db->where('pickups.pickup_done', $filter['pickup_done'] == 'yes' ? '1' : '0');
        }

        $this->db->limit($limit);
        $this->db->offset($offset);

        $this->db->where('pickups.user_id', $user_id);
   
        if (isset($filter['orderbyid']) && ($filter['orderbyid'] == 'asc' || $filter['orderbyid'] == 'desc'))
            $this->db->order_by('pickups.id', strtolower($filter['orderbyid']));
        else
            $this->db->order_by('pickups.created', 'desc');
        

        $this->db->join('courier', 'courier.id = pickups.courier_id', 'LEFT');
        $this->db->join('warehouse', 'warehouse.id = pickups.warehouse_id', 'LEFT');
    
        $this->db->from($this->table);
        return $this->db->get_compiled_select();
    }
}