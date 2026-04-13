<?php

class Products_model extends MY_model
{

    public function __construct()
    {
        parent::__construct();
        $this->table = 'orders';
        $this->products_table = 'order_products';
        $this->product_details_table = 'product_details';
        $this->product_billing_table = 'product_billing_details';
    }

    function insertProduct($save = array())
    {
        if (empty($save))
            return false;
        $save['created'] = time();
        $save['modified'] = time();
        $this->db->insert($this->product_details_table, $save);
        return $this->db->insert_id();
    }

    function insertProductBilling($save = array())
    {
        if (empty($save))
            return false;
        $save['created'] = time();
        $save['modified'] = time();
        $this->db->insert($this->product_billing_table, $save);
        return $this->db->insert_id();
    }


    function insertWeightAppliedDetails($save = array())
    {
        if (empty($save))
            return false;
        $save['created'] = time();
        $this->db->insert('tbl_weight_applied_details', $save);
        return $this->db->insert_id();
    }

    function update($product_id = false, $save = array())
    {

        if (!$product_id || empty($save))
            return false;
        $save['modified'] = time();
        $this->db->where('id', $product_id);
        $this->db->set($save);
        $this->db->update($this->product_details_table);
        return true;
    }

    function fetchByProductUserID($user_id = false, $limit = 1, $offset = 0, $filter = array())
    {
        $whereQuery = $offsetq = '';
        if (!empty($filter['search_query'])) {
            $query = $filter['search_query'];
            $whereQuery = " and (order_products.product_sku like '{$query}%' or tbl_product_details.product_sku like '{$query}%'  or order_products.product_name like '{$query}')";
        }
        if (!empty($offset)) {
            $offsetq = " OFFSET $offset";
        }
        $query = "SELECT `orders`.`user_id`, `orders`.`channel_id`, `orders`.`fulfillment_status`, `order_products`.`id` as `productid`, `order_products`.`product_name` as `products`, `order_products`.`product_sku`, `order_products`.`product_weight`, `order_products`.`product_price`, `tbl_product_details`.`id` as `prod_id`, `tbl_product_details`.`product_sku` as `prod_sku`, `tbl_product_details`.`length` as `prod_length`, `tbl_product_details`.`breadth` as `prod_breadth`, `tbl_product_details`.`height` as `prod_height`, `tbl_product_details`.`weight` as `prod_weight`, `tbl_product_details`.`igst` as `prod_igst`, `tbl_product_details`.`weight_locked` as `prod_weight_locked`, `tbl_product_details`.`hsn_code` as `prod_hsn_code` 
        FROM `orders` LEFT JOIN `order_products` ON `order_products`.`order_id` = `orders`.`id` 
        LEFT JOIN `tbl_product_details` ON (case when (order_products.product_sku='') then (tbl_product_details.product_name = order_products.product_name ) else
        (tbl_product_details.product_name = order_products.product_name && tbl_product_details.product_sku = order_products.product_sku )
        end) 
        WHERE `orders`.`user_id` =" . $user_id . $whereQuery . "
        GROUP BY `order_products`.`product_sku`, `order_products`.`product_name` 
        ORDER BY `order_date` DESC 
        LIMIT " . $limit . $offsetq;
        $query = $this->db->query($query);

        return $query->result();
    }


    function getProductDetailsbyOrder($order_id){
        $query = "SELECT `tbl_weight_applied_details`.`user_id`,`tbl_weight_applied_details`.`shipment_id`,`tbl_weight_applied_details`.`length`,`tbl_weight_applied_details`.`breadth`,`tbl_weight_applied_details`.`height`,`tbl_weight_applied_details`.`weight` FROM `tbl_order_shipping` INNER JOIN tbl_weight_applied_details ON tbl_weight_applied_details.shipment_id = (select id from tbl_order_shipping where order_id = ".$order_id." order by id desc limit 1) order by tbl_weight_applied_details.id desc limit 1"  ;
         $query = $this->db->query($query);

        return $query->row();
    }


    function fetchByProductDetailsUserID($user_id = false, $limit = 1, $offset = 0, $filter = array())
    {
        $whereQuery = $offsetq = '';
        if (!empty($filter['search_query'])) {
            $query = $filter['search_query'];
            $whereQuery = " and (tbl_product_details.product_sku like '{$query}%'  or tbl_product_details.product_name like '{$query}')";
        }
        if (!empty($filter['status'])) {
           $whereQuery .= " and weight_locked = '".$filter['status']."' ";
        }
        if (!empty($filter['pid'])) {
           $whereQuery .= " and tbl_product_details.id IN (".implode(',',$filter['pid']).") ";
        }

        if (!empty($offset)) {
            $offsetq = " OFFSET $offset";
        }
        $query = "SELECT escalations.id as escalation_id , tbl_product_details.`product_qty`, tbl_product_details.`id`,tbl_product_details.`user_id`,tbl_product_details.`product_name`,tbl_product_details.`product_sku`,tbl_product_details.`length`,tbl_product_details.`breadth`,tbl_product_details.`height`,tbl_product_details.`weight`,tbl_product_details.`igst`,tbl_product_details.`weight_locked`,tbl_product_details.`hsn_code`,tbl_product_details.`created`,tbl_product_details.`modified` , tbl_product_details.`is_weight`  FROM `tbl_product_details`
        LEFT JOIN escalations on tbl_product_details.id = escalations.ref_id and escalations.user_id = ". $user_id ."
        WHERE `product_details_code` !='' and `tbl_product_details`.`user_id` =" . $user_id . $whereQuery . "
        ORDER BY `modified` DESC 
        LIMIT " . $limit . $offsetq;
        //echo $query;exit;
        $query = $this->db->query($query); 
        return $query->result();
    }

    function fetchByProductDetailsBillingUserID($user_id = false, $limit = 1, $offset = 0, $filter = array()){
        $whereQuery = $offsetq = '';
        if (!empty($filter['search_query'])) {
            $query = $filter['search_query'];
            $whereQuery = " and (tbl_product_billing_details.product_sku like '{$query}%'  or tbl_product_billing_details.product_name like '{$query}')";
        }
        if (!empty($filter['pid'])) {
           $whereQuery .= " and tbl_product_billing_details.id IN (".implode(',',$filter['pid']).") ";
        }

        if (!empty($offset)) {
            $offsetq = " OFFSET $offset";
        }
        $query = "SELECT tbl_product_billing_details.`id`,tbl_product_billing_details.`user_id`,tbl_product_billing_details.`product_name`,tbl_product_billing_details.`product_sku` , tbl_product_billing_details.`igst`,tbl_product_billing_details.`hsn_code`,tbl_product_billing_details.`created`,tbl_product_billing_details.`modified`  FROM `tbl_product_billing_details`
        WHERE `product_details_code` !='' and `tbl_product_billing_details`.`user_id` =" . $user_id . $whereQuery . "
        ORDER BY `modified` DESC 
        LIMIT " . $limit . $offsetq;
        $query = $this->db->query($query); 
        return $query->result();
    }


     function countProductDetailsId($user_id = false, $filter = array())
    {
        
        $whereQuery = "";
        if (!empty($filter['search_query'])) {
            $query = $filter['search_query'];
            $whereQuery = " and ( tbl_product_details.product_sku like '{$query}%'  or tbl_product_details.product_name like '{$query}')";
        }
        
        if (!empty($filter['status'])) {
           $whereQuery .= " and weight_locked = '".$filter['status']."' ";
        }
        if (!empty($filter['pid'])) {
           $whereQuery .= " and id IN (".implode(',',$filter['pid']).") ";
        }
       
       $query = "SELECT count(*) as total
        FROM `tbl_product_details` WHERE product_details_code !='' and  `tbl_product_details`.`user_id` = ".$user_id . $whereQuery . "
        ";
        $query = $this->db->query($query);
       //echo $this->db->last_query();exit;
        return $query->row();
    }


    function getBYPid($id){
        $this->db->select("`tbl_product_details`.`id`, `tbl_product_details`.`user_id`, `tbl_product_details`.`product_name`, `tbl_product_details`.`product_sku`, `tbl_product_details`.`length`, `tbl_product_details`.`breadth`, `tbl_product_details`.`height`, `tbl_product_details`.`weight`, `tbl_product_details`.`igst`, `tbl_product_details`.`weight_locked`, `tbl_product_details`.`is_weight`");
        $this->db->where("id",$id);
        $q = $this->db->get('product_details');
        return $q->row();

    }


      function countProductBillingDetailsId($user_id = false, $filter = array())
    {
        
        $whereQuery = "";
        if (!empty($filter['search_query'])) {
            $query = $filter['search_query'];
            $whereQuery = " and ( tbl_product_billing_details.product_sku like '{$query}%'  or tbl_product_billing_details.product_name like '{$query}')";
        }
        
        
        if (!empty($filter['pid'])) {
           $whereQuery .= " and id IN (".implode(',',$filter['pid']).") ";
        }
       
       $query = "SELECT count(*) as total
        FROM `tbl_product_billing_details` WHERE product_details_code !='' and  `tbl_product_billing_details`.`user_id` = ".$user_id . $whereQuery . "
        ";
        $query = $this->db->query($query);
       //echo $this->db->last_query();exit;
        return $query->row();
    }

    function exportskuecord($user_id){

        //getting product from produc details tables
        $product_array = array();
        $query = "SELECT `tbl_product_details`.`id`, `tbl_product_details`.`user_id`, `tbl_product_details`.`product_name`, `tbl_product_details`.`product_sku`, `tbl_product_details`.`length`, `tbl_product_details`.`breadth`, `tbl_product_details`.`height`, `tbl_product_details`.`weight`, `tbl_product_details`.`igst`, `tbl_product_details`.`weight_locked`, `tbl_product_details`.`is_weight`, `tbl_product_details`.`hsn_code` FROM `tbl_product_details` WHERE `tbl_product_details`.`weight_locked` in ('0','3')  and tbl_product_details.user_id = ".$user_id." GROUP BY `tbl_product_details`.`product_name`, `tbl_product_details`.`product_sku`, `tbl_product_details`.`user_id`";

         $query = $this->db->query($query);
         $result_1 = $query->result();

        
        //getting product from order and order product

        $query_2 = 'SELECT `order_products`.`product_name`,`order_products`.`product_sku`  FROM `orders` LEFT JOIN `order_products` ON `order_products`.`order_id` = `orders`.`id`  WHERE `orders`.`user_id` =  '.$user_id.' GROUP BY `order_products`.`product_name`,`order_products`.`product_sku`';
        $query_2  = $this->db->query($query_2);
        $result_2 = $query_2->result();    

        $product_array = array("product_details_data"=>$result_1,"order_product_data"=>$result_2);
        
        return $product_array;
    }

    function exportproductDetails($user_id,$filter){

        //getting product from produc details tables
        $whereQuery = "";
        if (!empty($filter['search_query'])) {
            $query = $filter['search_query'];
            $whereQuery = " and ( tbl_product_details.product_sku like '{$query}%'  or tbl_product_details.product_name like '{$query}')";
        }
        
        if (!empty($filter['pid'])) {
           $whereQuery .= " and id IN (".implode(',',$filter['pid']).") ";
        }

        $product_array = array();
        $query = "SELECT `tbl_product_details`.`id` as pid, `tbl_product_details`.`user_id`, `tbl_product_details`.`product_name`, `tbl_product_details`.`product_sku`,`tbl_product_details`.`product_qty` ,  `tbl_product_details`.`length`, `tbl_product_details`.`breadth`, `tbl_product_details`.`height`, `tbl_product_details`.`weight`, `tbl_product_details`.`igst`, `tbl_product_details`.`weight_locked`, `tbl_product_details`.`is_weight`, `tbl_product_details`.`hsn_code` FROM `tbl_product_details` WHERE  tbl_product_details.user_id = ".$user_id. $whereQuery . " order by `tbl_product_details`.`modified` desc ";

        
         $query = $this->db->query($query);
         
         $result_1 = $query->result();
         return $result_1;
    }

    function exportproductBillingDetails($user_id,$filter){

        $whereQuery = "";
        if (!empty($filter['search_query'])) {
            $query = $filter['search_query'];
            $whereQuery = " and ( tbl_product_billing_details.product_sku like '{$query}%'  or tbl_product_billing_details.product_name like '{$query}')";
        }
        
        if (!empty($filter['pid'])) {
           $whereQuery .= " and id IN (".implode(',',$filter['pid']).") ";
        }

        $product_array = array();
        $query = "SELECT `tbl_product_billing_details`.`id` as pid, `tbl_product_billing_details`.`user_id`, `tbl_product_billing_details`.`product_name`, `tbl_product_billing_details`.`product_sku`, `tbl_product_billing_details`.`igst`, `tbl_product_billing_details`.`hsn_code` FROM `tbl_product_billing_details` WHERE tbl_product_billing_details.user_id = ".$user_id. $whereQuery;

        $query = $this->db->query($query);
         
         $result_1 = $query->result();
         return $result_1;
    }

    function countByProductUserID($user_id = false, $filter = array())
    {
        $this->db->select('count(*) as total');
        $whereQuery = "";
        if (!empty($filter['search_query'])) {
            $query = $filter['search_query'];
            $whereQuery = " and (order_products.product_sku like '{$query}%' or tbl_product_details.product_sku like '{$query}%'  or order_products.product_name like '{$query}')";
        }
        if (!empty($offset)) {
            $offsetq = " OFFSET $offset";
        }
        $query = "SELECT count(*) as total
        FROM `orders` LEFT JOIN `order_products` ON `order_products`.`order_id` = `orders`.`id` 
        LEFT JOIN `tbl_product_details` ON (case when (order_products.product_sku='') then (tbl_product_details.product_name = order_products.product_name ) else
        (tbl_product_details.product_name = order_products.product_name && tbl_product_details.product_sku = order_products.product_sku )
        end) 
        WHERE `orders`.`user_id` =" . $user_id . $whereQuery . "
        GROUP BY `order_products`.`product_sku`, `order_products`.`product_name` 
        ORDER BY `order_date` DESC";
        $query = $this->db->query($query);
        return $query->num_rows();
    }

    function getProducts($product_id = false, $user_id = false)
    {
        if (!$product_id)
            return false;
        $this->db->where('user_id', $user_id);
        $this->db->where('id', $product_id);
        $q = $this->db->get($this->product_details_table);
        return $q->row();
    }

     function get_product_details(){
        $query = "SELECT  tbl_product_details.`product_qty`, tbl_product_details.`id`,tbl_product_details.`user_id`,tbl_product_details.`product_name`,tbl_product_details.`product_sku`,tbl_product_details.`length`,tbl_product_details.`breadth`,tbl_product_details.`height`,tbl_product_details.`weight`,tbl_product_details.`igst`,tbl_product_details.`weight_locked`,tbl_product_details.`hsn_code`,tbl_product_details.`created`,tbl_product_details.`modified` , tbl_product_details.`is_weight`  FROM `tbl_product_details`  where tbl_product_details.`igst` > 0 and tbl_product_details.`hsn_code` != '' ";
        $query = $this->db->query($query);
        return $query->result();

     }

    function getProductByOrderId($order_id){
        $sql = "SELECT `product_id`,`product_name`,`product_qty`,`product_sku`,`product_price`  FROM `tbl_order_products` WHERE `order_id` = ".$order_id;
        $query = $this->db->query($sql);
        return $query->result();
    }

    function productInsert($save = false)
    {
        if (!$save)
            return false;

        $checkId =  $this->getProducts($save['product_id'], $save['user_id']);
        unset($save['product_id']);
        if (!empty($checkId)) {
            $ret =$this->update($checkId->id, $save);
        } else {
            $ret = $this->insertProduct($save);
        }
        return $ret;
    }

    function ProductDetailsInsert($save = false)
    {
        if (!$save)
            return false;

        if (empty($save))
            return false;
        $save['created'] = time();
        $save['modified'] = time();
        $this->db->insert($this->product_details_table, $save);
        return $this->db->insert_id();
    }

    function getProductId($user_id,$product_name)
    {
        $query = "SELECT `tbl_product_details`.`id` as `prod_id`
        FROM `orders` LEFT JOIN `order_products` ON `order_products`.`order_id` = `orders`.`id` 
        LEFT JOIN `tbl_product_details` ON (case when (order_products.product_sku='') then (tbl_product_details.product_name = order_products.product_name ) else
        (tbl_product_details.product_name = order_products.product_name && tbl_product_details.product_sku = order_products.product_sku )
        end) 
        WHERE `orders`.`user_id` =" . $user_id." and `order_products`.`product_name`="."'". $product_name."'"."
        GROUP BY `order_products`.`product_sku`, `order_products`.`product_name` 
        ORDER BY `order_date` DESC";
        $query = $this->db->query($query);
        return $query->result();

    }


     function getProductDetailsId($user_id,$product_name,$product_sku)
    {
        $product_sku = str_replace("'", "\'",  $product_sku);
        $product_name = str_replace("'", "\'",  $product_name);

        $query = "SELECT `tbl_product_details`.`id` as `prod_id` , `weight_locked`
        FROM `tbl_product_details`  
        WHERE `user_id` = " . $user_id. " and `product_name` LIKE '%".$product_name."%' and  `product_sku` LIKE '%".$product_sku."%'";
        $query = $this->db->query($query);
        return $query->row();

    }

    function getProductDetailsById($user_id,$id){
        
      
        $this->db->select('id as prod_id , weight_locked');
        $this->db->where('user_id', $user_id);
        $this->db->where('id', $id);
        $q = $this->db->get($this->product_details_table);
        return $q->row();
    }

    function getProductBillingDetailsById($user_id,$id){
        
        $this->db->select('id as prod_id');
        $this->db->where('user_id', $user_id);
        $this->db->where('id', $id);
        $q = $this->db->get('product_billing_details');
        return $q->row();
    }


    function getProductDetailsByCode($user_id,$code,$is_weight=false){
        
        $query = "SELECT `tbl_product_details`.`id` as `prod_id` , `weight_locked` ,tbl_product_details.id,tbl_product_details.user_id,tbl_product_details.product_name,tbl_product_details.product_sku,tbl_product_details.length,tbl_product_details.breadth,tbl_product_details.height,tbl_product_details.weight,tbl_product_details.igst,tbl_product_details.weight_locked,tbl_product_details.is_weight,tbl_product_details.hsn_code
        FROM `tbl_product_details`  
        WHERE `user_id` = " . $user_id. " and CONVERT(product_details_code USING utf8) = '" . $code. "'";
        if($is_weight){
         $query .=  "and is_weight = 1 ";   
        }
        $query = $this->db->query($query);
        return $query->row();

    }

    function getProductBillingDetailsByCode($user_id,$code){
        
        $query = "SELECT `id` as `prod_id` , `igst`,`hsn_code`
        FROM `tbl_product_billing_details`  
        WHERE `user_id` = " . $user_id. " and  CONVERT(product_details_code USING utf8) = '" . $code. "'";
        $query = $this->db->query($query);
        return $query->row();

    }
    
    function productDetailsUpdate($save,$id){

          if (!$save)
          return false;
           $save['modified'] = time();
          $this->db->where('id', $id);
         $result =  $this->db->update('product_details', $save);
         
         return $result;
    }

    function productBillingDetailsUpdate($save,$id){

          if (!$save)
          return false;
           $save['modified'] = time();
          $this->db->where('id', $id);
         $result =  $this->db->update('product_billing_details', $save);
         return $result;
    }

    function getUserProductDetails($user_id,$filter = array()){
        
       $this->db->select('order_products.id as product_id,order_products.product_name,tbl_order_shipping.ship_status,'
       ."sum(case when (tbl_order_shipping.ship_status = 'delivered' ) then 1 else 0 end) as order_delivery,"
       ."sum(case when (tbl_order_shipping.ship_status = 'pending pickup') then 1 else 0 end) as pending_pickup,"
       ."sum(case when (tbl_order_shipping.ship_status ='booked')then 1 else 0 end) as booked,"
       ."sum(case when (tbl_order_shipping.ship_status = 'in transit' or tbl_order_shipping.ship_status = 'out for delivery' or tbl_order_shipping.ship_status = 'exception') then 1 else 0 end) as in_transit,"
       ."sum(case when (tbl_order_shipping.ship_status = 'rto') then 1 else 0 end) as rto,"
       ."sum(case when (tbl_order_shipping.ship_status != 'cancelled' AND tbl_order_shipping.ship_status !='new') then 1 else 0 end) as product_qty,"
    );
    if (!empty($filter['start_date'])) {
        $this->db->where("tbl_order_shipping.created >= '" . $filter['start_date'] . "'");
    }
    if (!empty($filter['end_date'])) {
        $this->db->where("tbl_order_shipping.created <= '" . $filter['end_date'] . "'");
    }
    if(!empty($filter['product_name'])) {
        $this->db->like('order_products.product_name', $filter['product_name']);
    }
       $this->db->join('tbl_order_shipping','tbl_order_shipping.order_id=order_products.order_id'); 
       $this->db->join('orders','orders.id=order_products.order_id');
       $this->db->where('tbl_order_shipping.user_id',$user_id);
       $this->db->where('tbl_order_shipping.shipment_type =','ecom'); 
       $this->db->group_by('order_products.product_name'); 
       $this->db->order_by('product_qty','DESC'); 
       
       $query = $this->db->get('order_products');
    //    pr($this->db->last_query(),1); 
       return $query->result(); 
        
    }
}
