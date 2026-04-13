<?php

class Warehouse_model extends MY_model {

    public function __construct() {
        parent::__construct();
        $this->table = 'warehouse';
		$this->warehousehub_table = 'warehouse_hub';
    }
    
	function getUserAllWarehouse($limit = 50, $offset = 0, $filter = array())
	{
       	$this->db->select("warehouse.*,users.fname as user_fname, users.lname as user_lname,users.company_name,users.id as userid,users.phone as sellerPhone,users.email as userEmail");

        if (!empty($filter['start_date'])) {
            $this->db->where("warehouse.created >= '" . $filter['start_date'] . "'");
        }

        if (!empty($filter['end_date'])) {
            $this->db->where("warehouse.created <= '" . $filter['end_date'] . "'");
        }
		
		if (!empty($filter['phone'])){
            $this->db->where_in('warehouse.phone', $filter['phone']);
        }

        if (!empty($filter['seller_id'])){
            $this->db->where('users.id', $filter['seller_id']);
        }
		
		if (!empty($filter['pincode_id'])) {
            $this->db->where_in('warehouse.zip', $filter['pincode_id']);
        }
		
		$this->db->join('users', 'users.id = warehouse.user_id');
		$this->db->limit($limit);
        $this->db->offset($offset);
        $this->db->order_by('id', 'desc');
		$this->db->group_by('warehouse.id');
        $q = $this->db->get($this->table);

        return $q->result();
    }
	
	function countByAllWarehouse($filter = array())
    {
        $this->db->select('count(*) as total');

        if (!empty($filter['start_date'])) {
            $this->db->where("warehouse.created >= '" . $filter['start_date'] . "'");
        }

        if (!empty($filter['end_date'])) {
            $this->db->where("warehouse.created <= '" . $filter['end_date'] . "'");
        }
        
		if (!empty($filter['seller_id'])){
            $this->db->where('users.id', $filter['seller_id']);
        }

		if (!empty($filter['phone'])){
            $this->db->where_in('warehouse.phone', $filter['phone']);
        }

		if (!empty($filter['pincode_id'])) {
            $this->db->where_in('warehouse.zip', $filter['pincode_id']);
        }

		$this->db->join('users', 'users.id = warehouse.user_id');
		
		$q = $this->db->get($this->table);
        return $q->row()->total;
    }
	
	function create($save = array()){
        if (empty($save))
            return false;
        return $this->db->insert($this->warehousehub_table, $save);
    }
	
	function allwarehousehub($expand_pincode)
	{
		$this->db->select("warehouse_hub.*,courier.name as courier_name"
		);
        $this->db->where('warehouse_hub.pincode',$expand_pincode);
        $this->db->order_by('warehouse_hub.id', 'desc');
        $this->db->join('courier', 'courier.id = warehouse_hub.courier_id', 'LEFT');
		$q = $this->db->get($this->warehousehub_table);
        return $q->result();
	}
	
	function getwarehousehub($pincode)
	{
		$this->db->select("warehouse_hub.*,courier.name as courier_name");
        $this->db->where('warehouse_hub.pincode',$pincode);
        $this->db->order_by('warehouse_hub.id', 'desc');
        $this->db->join('courier', 'courier.id = warehouse_hub.courier_id', 'LEFT');
		$q = $this->db->get($this->warehousehub_table);
        return $q->result();
	}
	
	/*function hubrecords($hub_id)
	{
		$this->db->select("warehouse_hub.*,courier.name as courier_name");
        $this->db->where('warehouse_hub.id',$hub_id);
        $this->db->join('courier', 'courier.id = warehouse_hub.courier_id', 'LEFT');
		$q = $this->db->get($this->warehousehub_table);
        return $q->result();
	}*/
	
	function deletehub($id = false)
    {
        if (!$id)
            return false;
        $this->db->where('id', $id);
        $this->db->delete($this->warehousehub_table);
        return true;
    }

    function getUserAllWarehouseCSV($limit = 50, $offset = 0, $filter = array()) {
        $this->db->select("warehouse.*, users.fname as user_fname, users.lname as user_lname, users.company_name, users.id as userid, users.phone as sellerPhone, users.email as userEmail");

        if (!empty($filter['start_date'])) {
            $this->db->where("warehouse.created >= '" . $filter['start_date'] . "'");
        }

        if (!empty($filter['end_date'])) {
            $this->db->where("warehouse.created <= '" . $filter['end_date'] . "'");
        }
        
        if (!empty($filter['phone'])) {
            $this->db->where_in('warehouse.phone', $filter['phone']);
        }

        if (!empty($filter['seller_id'])) {
            $this->db->where('users.id', $filter['seller_id']);
        }

        if (!empty($filter['pincode_id'])) {
            $this->db->where_in('warehouse.zip', $filter['pincode_id']);
        }
        
        $this->db->join('users', 'users.id = warehouse.user_id');
        $this->db->limit($limit);
        $this->db->offset($offset);
        $this->db->order_by('id', 'desc');
        $this->db->group_by('warehouse.id');
        $this->db->from($this->table);

        return $query = $this->db->get_compiled_select();
    }
}