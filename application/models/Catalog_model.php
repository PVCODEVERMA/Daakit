<?php

class Catalog_model extends MY_model
{

    public function __construct()
    {
        parent::__construct();
        $this->table = 'catalog_products';
        $this->table_maps = 'catalog_products_maps';
       
    }

    function insert($data = array())
    {
        if (empty($data))
            return false;

        $data['created'] = time();
        $data['modified'] = time();
        $this->db->insert($this->table, $data);
        return $this->db->insert_id();
    }

    function updateMasterSku($catalog_id = false, $skuArr = array())
    {
        if (!$catalog_id || empty($skuArr))
            return false;

        $this->db->where('id', $catalog_id);
        $this->db->set($skuArr);
        $this->db->update($this->table);
        return true;
    }

    function update($product_id = false, $data = array())
    {

        if (!$product_id || empty($data))
            return false;

        $data['modified'] = time();

        $this->db->where('id', $product_id);
        $this->db->set($data);
        $this->db->update($this->table);
        return true;
    }

    function getProductByChannelId($product_id = false, $channel_id = false)
    {
        if (!$product_id || !$channel_id)
            return false;

        $this->db->where('product_id', $product_id);
        $this->db->where('channel_id', $channel_id);
        $this->db->limit(1);
        $q = $this->db->get($this->table);
        return $q->row();
    }

    function searchByUserIDProductName($user_id = false, $query  = false)
    {
        if (!$user_id || !$query)
            return false;

        $this->db->where('user_id', $user_id);
        $this->db->like('product_name', $query, 'after');
        $this->db->limit(5);
        $q = $this->db->get($this->table);
        return $q->result();
    }

    function countByCatalogUserID($user_id = false, $filter = array())
    {

        if (!$user_id)
        return false;

        $this->db->select('count(DISTINCT catalog_products.id) as total');
        if (!empty($filter['search_query'])) {
            $query = $filter['search_query'];
            $this->db->where("(catalog_products.product_sku like '%{$query}%' or catalog_products.product_name like '%{$query}%' or catalog_products_maps.product_sku like '%{$query}%')");
        }
        if (!empty($filter['status'])) {
            $this->db->where('catalog_products_maps.weight_locked', $filter['status']);
         }
        $this->db->where('catalog_products.user_id', $user_id);
        $this->db->join('catalog_products_maps', 'catalog_products.id = catalog_products_maps.catalog_id', 'LEFT');
        $this->db->where('catalog_products.product_sku !=','');
       // $this->db->group_by('catalog_products.product_sku'); 

      
        $q = $this->db->get($this->table);
        return $q->row()->total;
    }
    function fetchByProductUserID($user_id = false, $limit = 1, $offset = 0, $filter = array())
    {
        $this->db->select("catalog_products.*,catalog_products_maps.product_sku as map_sku ,catalog_products_maps.length,catalog_products_maps.breadth,catalog_products_maps.height,catalog_products_maps.weight as map_weight,catalog_products_maps.igst,catalog_products_maps.hsn_code,catalog_products_maps.weight_locked,catalog_products_maps.is_weight,escalations.id as escalations_id");

        if (!empty($filter['search_query'])) {
            $query = $filter['search_query'];
            $this->db->where("(catalog_products.product_sku like '%{$query}%' or catalog_products.product_name like '%{$query}%' or catalog_products_maps.product_sku like '%{$query}%')");
        }
       if (!empty($filter['status'])) {
            $this->db->where('catalog_products_maps.weight_locked', $filter['status']);
         }
        $this->db->where('catalog_products.user_id', $user_id);
        
        $this->db->join('catalog_products_maps', 'catalog_products.id = catalog_products_maps.catalog_id', 'LEFT');
        $this->db->join('escalations', 'escalations.ref_id = catalog_products_maps.id AND `escalations`.`type` = "weight_freeze"', 'LEFT' );
        $this->db->where('catalog_products.product_sku !=','');
        $this->db->group_by('catalog_products.product_sku','catalog_products.product_name'); 
        $q = $this->db->get($this->table);
        return $q->result();
    }

    function getProducts($catalog_id = false)
    {
        if (!$catalog_id)
            return false;
        $this->db->where('catalog_id', $catalog_id);
        $q = $this->db->get($this->table_maps);
        return $q->row();
    }

     function getProductsdetails($prod_id = false)
    {
        if (!$prod_id)
            return false;
        $this->db->where('id', $prod_id);
        $q = $this->db->get('product_details');
        return $q->row();
    }
   
    function insertProduct_maps($save = array())
    {
        if (empty($save))
            return false;
        $save['created'] = time();
        $this->db->insert($this->table_maps, $save);
        return $this->db->insert_id();
    }
    


    function update_producut_map($product_id = false, $data = array())
    {

        if (!$product_id || empty($data))
            return false;

        $data['modified'] = time();

        $this->db->where('id', $product_id);
        $this->db->set($data);
        $this->db->update($this->table_maps);
        return true;
    }

      function update_product_details($product_id = false, $data = array())
    {

        if (!$product_id || empty($data))
            return false;

        $data['modified'] = time();

        $this->db->where('id', $product_id);
        $this->db->set($data);
        $this->db->update('product_details');
        return true;
    }

    function countByCatalog($filter = array())
    {

        $this->db->select('count(DISTINCT catalog_products.id) as total');
        if (!empty($filter['search_query'])) {
            $query = $filter['search_query'];
            $this->db->where("(catalog_products.product_sku like '%{$query}%' or catalog_products.product_name like '%{$query}%' or catalog_products_maps.product_sku like '%{$query}%')");
        }
        if(!empty($filter['seller_id'])){
            $this->db->where('catalog_products.user_id', $filter['seller_id']);
        }
       
        $this->db->join('catalog_products_maps', 'catalog_products.id = catalog_products_maps.catalog_id');
        $q = $this->db->get($this->table);

        return $q->row()->total;
    }

     function countByProduct_details($filter = array())
    {
        //pr($_GET['filter']);exit;
        $this->db->select('count(DISTINCT product_details.id) as total');
        if (!empty($filter['search_query'])) {
            $query = $filter['search_query'];
            $this->db->where("(product_details.product_sku like '%{$query}%' or product_details.product_name like '%{$query}%' or product_details.product_sku like '%{$query}%')");
        }

        if (!empty($filter['start_date'])) {
            $this->db->where("product_details.created >= '" . $filter['start_date'] . "'");
        }

        if (!empty($filter['end_date'])) {
            $this->db->where("product_details.created <= '" . $filter['end_date'] . "'");
        }
        if (!empty($filter['pid'])) {
           $this->db->where_in('product_details.id', $filter['pid']);
        }  
        if(!empty($filter['seller_id'])){
            $this->db->where('product_details.user_id', $filter['seller_id']);
        }
        if(!empty($filter['status'])){
            $this->db->where('product_details.weight_locked', $filter['status']);
        }
        
        $this->db->where('product_details.weight_locked  !=', '0');
        $q = $this->db->get('product_details');
        //$this->db->last_query(); echo $q->row()->total ;exit;
        return $q->row()->total;
    }

    function fetchByCatalog($limit = 50, $offset = 0, $filter = array())
    {
        $this->db->select("catalog_products.*,catalog_products_maps.product_sku as map_sku ,catalog_products_maps.length,catalog_products_maps.breadth,catalog_products_maps.height,catalog_products_maps.weight as map_weight,catalog_products_maps.weight_locked,catalog_products_maps.igst,catalog_products_maps.hsn_code,users.fname,users.lname,users.company_name,escalations.id as escalations_id");

        if (!empty($filter['search_query'])) {
            $query = $filter['search_query'];
            $this->db->where("(catalog_products.product_sku like '%{$query}%' or catalog_products.product_name like '%{$query}%' or catalog_products_maps.product_sku like '%{$query}%')");
        }

       if (!empty($filter['start_date'])) {
            $this->db->where("catalog_products_maps.created >= '" . $filter['start_date'] . "'");
        }

        if (!empty($filter['end_date'])) {
            $this->db->where("catalog_products_maps.created <= '" . $filter['end_date'] . "'");
        }  
        if(!empty($filter['seller_id'])){
            $this->db->where('catalog_products.user_id', $filter['seller_id']);
        }
        if(!empty($filter['status'])){
            $this->db->where('catalog_products_maps.weight_locked', $filter['status']);
        }
        $this->db->limit($limit);
        $this->db->offset($offset);

        $this->db->where('catalog_products_maps.weight_locked  !=', '0');
        $this->db->join('catalog_products_maps', 'catalog_products.id = catalog_products_maps.catalog_id');
        $this->db->join('escalations', 'catalog_products_maps.id = escalations.ref_id','LEFT');
        $this->db->join('users', 'catalog_products.user_id = users.id');
        $this->db->group_by('catalog_products.product_name,catalog_products.product_sku,catalog_products.user_id'); 

         
        $q = $this->db->get($this->table);   
       /* echo $this->db->last_query();
         exit();      */
        return $q->result();
    }

    function fetchByProductDetails($limit = 50, $offset = 0, $filter = array())
    {
        $this->db->select("escalations.esc_closed_date AS close_date,
,escalations.id as esc_id , escalations.created as esc_date ,product_details.product_qty,product_details.id,product_details.user_id,product_details.product_name,product_details.product_sku,product_details.length,product_details.breadth,product_details.height,product_details.weight,product_details.igst,product_details.weight_locked,product_details.is_weight,product_details.hsn_code,users.fname,users.lname,users.company_name,escalations.id as escalations_id");

        if (!empty($filter['search_query'])) {
            $query = $filter['search_query'];
            $this->db->where("(product_details.product_sku like '%{$query}%' or product_details.product_name like '%{$query}%' or product_details.product_sku like '%{$query}%')");
        }

       if (!empty($filter['start_date'])) {
            $this->db->where("product_details.created >= '" . $filter['start_date'] . "'");
        }
        if (!empty($filter['pid'])) {
           $this->db->where_in('product_details.id', $filter['pid']);
        }

        if (!empty($filter['end_date'])) {
            $this->db->where("product_details.created <= '" . $filter['end_date'] . "'");
        }  
        if(!empty($filter['seller_id'])){
            $this->db->where('product_details.user_id', $filter['seller_id']);
        }
        if(!empty($filter['status'])){
            $this->db->where('product_details.weight_locked', $filter['status']);
        }
        $this->db->limit($limit);
        $this->db->offset($offset);

        $this->db->where('product_details.weight_locked  !=', '0');
        $this->db->join('escalations', 'product_details.id = escalations.ref_id and product_details.user_id = escalations.user_id','LEFT');
        //$this->db->join('escalations', 'product_details.user_id = escalations.user_id','LEFT');
        $this->db->join('users', 'product_details.user_id = users.id');
        $this->db->order_by('product_details.modified', 'desc');
        $this->db->group_by('product_details.product_name,product_details.product_sku,product_details.user_id'); 
        $q = $this->db->get('product_details');  
        // echo $this->db->last_query();exit;
        // pr($q->result());exit;
      
        return $q->result();
    }

    function exportfrezerecord($limit = 50, $offset = 0, $filter = array()){
        $apply_filters['start_date'] = strtotime("-30 days midnight");
        $apply_filters['end_date'] = time();
         $this->db->select(" escalations.id as esc_id , escalations.esc_closed_date AS close_date , escalations.created AS esc_created, `p1`.`remarks`,
    `p1`.`attachments`,  product_details.product_qty,product_details.id,product_details.user_id,product_details.product_name,product_details.product_sku,product_details.length,product_details.breadth,product_details.height,product_details.weight,product_details.igst,product_details.weight_locked,product_details.is_weight,product_details.hsn_code,users.fname,users.lname,users.company_name ,users.phone,users.email");

        if (!empty($filter['search_query'])) {
            $query = $filter['search_query'];
            $this->db->where("(product_details.product_sku like '%{$query}%' or product_details.product_name like '%{$query}%' or product_details.product_sku like '%{$query}%')");
        }

        if (!empty($filter['start_date'])) {
           $apply_filters['start_date'] = strtotime(trim($filter['start_date']) . ' 00:00:00');        
        }
        $this->db->where("product_details.created >= '" . ($apply_filters['start_date']) . "'");

        if (!empty($filter['end_date'])) {
            $apply_filters['end_date'] = strtotime(trim($filter['end_date']) . ' 23:59:59');
        }  
        $this->db->where("product_details.created <= '" . ($apply_filters['end_date']) . "'");

        if(!empty($filter['seller_id'])){
            $this->db->where('product_details.user_id', $filter['seller_id']);
        }
        if(!empty($filter['status'])){
            $this->db->where('product_details.weight_locked', $filter['status']);
        }
        $this->db->limit($limit);
        $this->db->offset($offset);

        $this->db->where('product_details.weight_locked  !=', '0');
        $this->db->where('p2.id IS NULL');

        $this->db->join('users', 'product_details.user_id = users.id');
        $this->db->group_by('product_details.product_name,product_details.product_sku,product_details.user_id'); 
        $this->db->join('escalations', "product_details.id = escalations.ref_id and `type` LIKE 'weight_freeze'",'LEFT');
        $this->db->join('escalation_action p1', 'escalations.id = p1.escalation_id');
        $this->db->join('escalation_action p2', "escalations.id = p2.escalation_id AND ( ( p1.created < p2.created ) OR(
                p1.created = p2.created AND p1.id < p2.id
            ) ) ",'LEFT OUTER',false);
        $this->db->order_by("product_details.modified", "desc");

        $this->db->from('product_details');
        //echo $this->db->last_query();exit;
        return $query = $this->db->get_compiled_select();


    }
}
