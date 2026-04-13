<?php

class Orders_model extends MY_model
{

    public function __construct()
    {
        parent::__construct();
        $this->table = 'orders';
        $this->products_table = 'order_products';

        $this->slave = $this->load->database('slave', TRUE);
    }

    function insertOrder($save = array())
    {
        if (empty($save))
            return false;

        $save['created'] = time();
        $save['modified'] = time();

        $this->db->insert($this->table, $save);
        return $this->db->insert_id();
    }

    function insertProduct($save = array())
    {
        if (empty($save))
            return false;
        $this->db->insert($this->products_table, $save);
        return $this->db->insert_id();
    }

    function fetchByUserID($limit = 50, $offset = 0, $filter = array())
    {
        $this->slave->select("orders.*, group_concat(tbl_order_products.product_name) as products, users.fname as user_fname, users.lname as user_lname, users.company_name, users.id as userid");

        if (!empty($filter['order_type'])) {
            $this->slave->where('order_type', $filter['order_type']);
        }

        if (!empty($filter['channel_id'])) {
            $this->slave->where('channel_id', $filter['channel_id']);
        }

        if (!empty($filter['order_ids'])) {
            $this->slave->where_in('orders.order_no', $filter['order_ids']);
        }

        if (!empty($filter['seller_id'])) {
            $this->slave->where('users.id', $filter['seller_id']);
        }

        if (!empty($filter['search_query'])) {
            $query = $filter['search_query'];
            $this->slave->where(" (orders.customer_name like '%{$query}%' or orders.shipping_phone like '%{$query}%' or orders.shipping_address like '%{$query}%' or orders.shipping_address_2 like '%{$query}%' or orders.shipping_city  like '%{$query}%' or orders.shipping_state like '%{$query}%' or tbl_order_products.product_name like '%{$query}%' ) ");
        }

        if (!empty($filter['pay_method'])) {
            $this->slave->where('order_payment_type', $filter['pay_method']);
        }

        if (!empty($filter['start_date'])) {
            $this->slave->where("order_date >= '" . $filter['start_date'] . "'");
        }

        if (!empty($filter['end_date'])) {
            $this->slave->where("order_date <= '" . $filter['end_date'] . "'");
        }

        if (!empty($filter['fulfillment'])) {
            $this->slave->where('fulfillment_status', $filter['fulfillment']);
        }

        if (!empty($filter['account_manager_id'])) {
            $this->slave->where('users.account_manager_id', $filter['account_manager_id']);
        }

        $this->slave->limit($limit);
        $this->slave->offset($offset);
        $this->slave->order_by('order_date', 'desc');
        $this->slave->group_by('tbl_order_products.order_id');
        $this->slave->join('order_products', 'tbl_order_products.order_id = orders.id', 'inner');
        $this->slave->join('users', 'users.id = orders.user_id');
        $q = $this->slave->get($this->table);
        return $q->result();
    }

    function countByUserID($filter = array())
    {
        $this->slave->select('count(DISTINCT tbl_order_products.order_id) as total');

        if (!empty($filter['order_type'])) {
            $this->slave->where('order_type', $filter['order_type']);
        }

        if (!empty($filter['channel_id'])) {
            $this->slave->where('channel_id', $filter['channel_id']);
        }

        if (!empty($filter['order_ids'])) {
            $this->slave->where_in('orders.order_no', $filter['order_ids']);
        }

        if (!empty($filter['seller_id'])) {
            $this->slave->where('users.id', $filter['seller_id']);
        }

        if (!empty($filter['search_query'])) {
            $query = $filter['search_query'];
            $this->slave->where(" (orders.customer_name like '%{$query}%'  or orders.shipping_phone like '%{$query}%' or orders.shipping_address like '%{$query}%' or orders.shipping_address_2 like '%{$query}%' or orders.shipping_city  like '%{$query}%' or orders.shipping_state like '%{$query}%' or tbl_order_products.product_name like '%{$query}%'  ) ");
        }

        if (!empty($filter['product_name'])) {
            $this->slave->like('tbl_order_products.product_name', $filter['product_name']);
        }
        if (!empty($filter['pay_method'])) {
            $this->slave->where('order_payment_type', $filter['pay_method']);
        }
        if (!empty($filter['start_date'])) {
            $this->slave->where("order_date >= '" . $filter['start_date'] . "'");
        }
        if (!empty($filter['end_date'])) {
            $this->slave->where("order_date <= '" . $filter['end_date'] . "'");
        }
        if (!empty($filter['fulfillment'])) {
            $this->slave->where('fulfillment_status', $filter['fulfillment']);
        }

        if (!empty($filter['account_manager_id'])) {
            $this->slave->where('users.account_manager_id', $filter['account_manager_id']);
        }

        $this->slave->join('order_products', 'tbl_order_products.order_id = orders.id', 'LEFT');
        $this->slave->join('users', 'users.id = orders.user_id');
        $q = $this->slave->get($this->table);
        return $q->row()->total;
    }


    function countByUserIDStatusGrouped($filter = array())
    {
        $this->slave->select("orders.fulfillment_Status as fulfillment_status, count(DISTINCT tbl_order_products.order_id) as total_count");

        if (!empty($filter['order_type'])) {
            $this->slave->where('order_type', $filter['order_type']);
        }

        if (!empty($filter['channel_id'])) {
            $this->slave->where('channel_id', $filter['channel_id']);
        }

        if (!empty($filter['order_ids'])) {
            $this->slave->where_in('orders.order_no', $filter['order_ids']);
        }

        if (!empty($filter['product_name'])) {
            $this->slave->like('tbl_order_products.product_name', $filter['product_name']);
        }

        if (!empty($filter['pay_method'])) {
            $this->slave->where('order_payment_type', $filter['pay_method']);
        }

        if (!empty($filter['start_date'])) {
            $this->slave->where("order_date >= '" . $filter['start_date'] . "'");
        }

        if (!empty($filter['end_date'])) {
            $this->slave->where("order_date <= '" . $filter['end_date'] . "'");
        }

        if (!empty($filter['seller_id'])) {
            $this->slave->like('orders.user_id', $filter['seller_id']);
        }

        if (!empty($filter['search_query'])) {
            $query = $filter['search_query'];
            $this->slave->where(" (orders.customer_name like '%{$query}%' or orders.shipping_phone like '%{$query}%' or orders.shipping_address like '%{$query}%' or orders.shipping_address_2 like '%{$query}%' or orders.shipping_city  like '%{$query}%' or orders.shipping_state like '%{$query}%' or tbl_order_products.product_name like '%{$query}%'  ) ");
        }

        if (!empty($filter['account_manager_id'])) {
            $this->slave->where('users.account_manager_id', $filter['account_manager_id']);
        }

        $this->slave->group_by('orders.fulfillment_status');
        $this->slave->join('order_products', 'tbl_order_products.order_id = orders.id', 'LEFT');
        $this->slave->join('users', 'users.id = orders.user_id');
        $q = $this->slave->get($this->table);
        return $q->result();
    }

    function updateFulfillmentStatus($order_id = false, $status = false)
    {
        if (!$order_id || !$status)
            return false;

        $this->db->where('id', $order_id);
        $this->db->set('fulfillment_status', $status);
        $this->db->update($this->table);
        return true;
    }

    function update($order_id = false, $save = array())
    {
        if (!$order_id || empty($save))
            return false;

        $save['modified'] = time();

        $this->db->where('id', $order_id);
        $this->db->set($save);
        $this->db->update($this->table);
        return true;
    }

    function getOrderProductsGrouped($order_id = false)
    {
        if (!$order_id)
            return false;

        $this->db->select('group_concat(product_name) as product_name, count(*) as total');
        $this->db->where('order_id', $order_id);
        $this->db->limit(1);
        $this->db->group_by('order_id');
        $q = $this->db->get($this->products_table);
        return $q->row();
    }

    function getOrderProducts($order_id = false)
    {
        if (!$order_id)
            return false;

        $this->db->where('order_id', $order_id);
        $q = $this->db->get($this->products_table);
        return $q->result();
    }

    function getsellerbyid($user_id)
    {
        $this->db->select('users.id,users.fname,users.lname,users.company_name');
        $this->db->where('id', $user_id);
        $q = $this->db->get('users');
        return $q->row();
    }
	
	
	public function markshipmentnew( $shipid = false ) {
		
		 
		$update = array(
            'fulfillment_status' => 'new'
        );
        
		$this->db->where('id', $shipid);
        $this->db->set($update);
		$this->db->update($this->table) ;
	 
		// $shipmentstatus = array(
		// 	'ship_status' => 'cancelled' 
		// ) ;
			
		// $this->db->where('order_id', $shipid);
		// $this->db->set($shipmentstatus);
		// $this->db->update('order_shipping'); 
		// return true ;	
		 
	}
// 	
}