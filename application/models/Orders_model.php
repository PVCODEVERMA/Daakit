<?php

class Orders_model extends MY_model
{

    public function __construct()
    {
        parent::__construct();
        $this->table = 'orders';
        $this->products_table = 'tbl_order_products';
        $this->products_details_table = 'tbl_order_products_details';
        $this->order_invoice_table = 'order_invoice';
        $this->tag_table = 'order_tags';
        $this->order_reverse_qc_table = 'order_reverse_qc';
        $this->cargo_invoice = 'cargo_order_invoice';
        $this->orders_details_international = 'orders_details_international';
        $this->orders_multi_bagging_international = 'multi_bagging_orders';
    }

    function insertOrder($save = array())
    {
        if (empty($save))
            return false;

        $save['created'] = time();
        $save['modified'] = time();
        $this->db->insert($this->table, $save);
        $return_id=$this->db->insert_id();
        return $return_id;
    }

    function insertProduct($save = array())
    {
        if (empty($save))
            return false;

        $this->db->insert($this->products_table, $save);
        return $this->db->insert_id();
    }


    function deleteOrderProduct($id = false)
    {
        if (!$id)
            return false;

        $this->db->where('order_id', $id);
        $this->db->delete($this->products_table);

        return true;
    }

    function getByChannelOrderID($channel = false, $api_order_id = false)
    {
        if (!$channel || !$api_order_id)
            return false;

        $this->db->where('channel_id', $channel);
        $this->db->where('api_order_id', (string) $api_order_id);
        $this->db->limit(1);
        $q = $this->db->get($this->table);
        return $q->row();
    }

    function getLastOrder($channel = false)
    {
        if (!$channel)
            return false;

        $this->db->where('channel_id', $channel);
        $this->db->limit(1);
        $this->db->order_by('order_date', 'desc');
        $q = $this->db->get($this->table);
        return $q->row();
    }

    function fetchByUserID($user_id = false, $limit = 50, $offset = 0, $filter = array())
    {
        if (!$user_id)
            return false;

        $this->db->select("orders.*, group_concat(tbl_order_products.product_name) as products, group_concat(tbl_order_products.product_sku)  as products_sku, user_channels.channel_name as channel_name");


        if (!empty($filter['channel_id'])) {
            if ($filter['channel_id'] == 'custom')
                $this->db->where(" (orders.channel_id is NULL or orders.channel_id not in (select id from user_channels where user_id = '{$user_id}')) ");
            else
                $this->db->where('user_channels.id', $filter['channel_id']);
        }

        if (!empty($filter['order_type'])) {
            $this->db->where_in('orders.order_type', $filter['order_type']);
        }

        if (!empty($filter['order_ids'])) {
            $this->db->where_in('tbl_orders.order_no', $filter['order_ids']);
        }

        if (!empty($filter['tags'])) {
            $filter['tags'] = trim(str_replace("'", "\'", $filter['tags']));
            $this->db->where(" (find_in_set('{$filter['tags']}', tbl_orders.applied_tags))");
        }

        if (!empty($filter['search_query'])) {
            $query = trim(str_replace("'", "\'", $filter['search_query']));
            $this->db->where(" (CONCAT(orders.shipping_fname, ' ', orders.shipping_lname) like '%{$query}%' or orders.shipping_phone like '%{$query}%' or orders.shipping_address like '%{$query}%' or orders.shipping_address_2 like '%{$query}%' or orders.shipping_city  like '%{$query}%' or orders.shipping_state like '%{$query}%' or tbl_order_products.product_name like '%{$query}%' or orders.order_tags like '%{$query}%' ) ");
        }

        if (!empty($filter['pay_method'])) {
            $this->db->where('order_payment_type', $filter['pay_method']);
        }

        if (!empty($filter['start_date'])) {
            $this->db->where("order_date >= '" . $filter['start_date'] . "'");
        }

        if (!empty($filter['end_date'])) {
            $this->db->where("order_date <= '" . $filter['end_date'] . "'");
        }

        if (!empty($filter['fulfillment'])) {
            $this->db->where('orders.fulfillment_status', $filter['fulfillment']);
        }

        if (!empty($filter['product_name'])) {
            $this->db->like('tbl_order_products.product_name', $filter['product_name']);
        }

        if (!empty($filter['user_filters_query'])) {
            $this->db->where($filter['user_filters_query']);
        }
        if (!empty($filter['ivr_status'])) {
            $this->db->where('orders.ivr_calling_status', $filter['ivr_status']);
        }
        if (!empty($filter['engage_status'])) {
            $this->db->where('orders.whatsapp_status', $filter['engage_status']);
        }

        $this->db->limit($limit);
        $this->db->offset($offset);
        $this->db->where('orders.user_id', $user_id);

        $this->db->order_by('order_date', 'desc');

        $this->db->group_by('tbl_order_products.order_id');

        $this->db->join('tbl_order_products', 'tbl_order_products.order_id = orders.id', 'inner');
        $this->db->join('user_channels', 'user_channels.id = orders.channel_id', 'LEFT');

        $q = $this->db->get($this->table);
        //echo $this->db->last_query();exit;

        return $q->result();
    }

    function countByUserID($user_id = false, $filter = array())
    {
        if (!$user_id)
            return false;

        $this->db->select('count(DISTINCT tbl_order_products.order_id) as total');

        if (!empty($filter['channel_id'])) {
            if ($filter['channel_id'] == 'custom')
                $this->db->where(" (orders.channel_id is NULL or orders.channel_id not in (select id from user_channels where user_id = '{$user_id}')) ");
            else
                $this->db->where('user_channels.id', $filter['channel_id']);
        }

        if (!empty($filter['order_type'])) {
            //$this->db->where_in('orders.order_type', $filter['order_type']);
        }

        if (!empty($filter['order_ids'])) {
            $this->db->where_in('tbl_orders.order_no', $filter['order_ids']);
        }

        if (!empty($filter['tags'])) {
            $filter['tags'] = trim(str_replace("'", "\'", $filter['tags']));
            //$this->db->where(" (find_in_set('{$filter['tags']}', applied_tags))");
        }

        if (!empty($filter['search_query'])) {
            $query = trim(str_replace("'", "\'", $filter['search_query']));
            $this->db->where(" (CONCAT(orders.shipping_fname, ' ', orders.shipping_lname) like '%{$query}%' or orders.shipping_phone like '%{$query}%' or orders.shipping_address like '%{$query}%' or orders.shipping_address_2 like '%{$query}%' or orders.shipping_city  like '%{$query}%' or orders.shipping_state like '%{$query}%' or tbl_order_products.product_name like '%{$query}%' or orders.order_tags like '%{$query}%' )  ");
        }

        if (!empty($filter['pay_method'])) {
            $this->db->where('order_payment_type', $filter['pay_method']);
        }

        if (!empty($filter['start_date'])) {
            $this->db->where("order_date >= '" . $filter['start_date'] . "'");
        }

        if (!empty($filter['end_date'])) {
            $this->db->where("order_date <= '" . $filter['end_date'] . "'");
        }

        if (!empty($filter['fulfillment'])) {
            $this->db->where('fulfillment_status', $filter['fulfillment']);
        }

        if (!empty($filter['product_name'])) {
            $this->db->like('tbl_order_products.product_name', $filter['product_name']);
        }

        if (!empty($filter['user_filters_query'])) {
            $this->db->where($filter['user_filters_query']);
        }

        if (!empty($filter['ivr_status'])) {
            $this->db->where('orders.ivr_calling_status', $filter['ivr_status']);
        }
        if (!empty($filter['engage_status'])) {
            $this->db->where('orders.whatsapp_status', $filter['engage_status']);
        }

        $this->db->where('orders.user_id', $user_id);

        //$this->db->group_by('tbl_order_products.order_id');

        $this->db->join('tbl_order_products', 'tbl_order_products.order_id = orders.id', 'LEFT');
        $this->db->join('user_channels', 'user_channels.id = orders.channel_id', 'LEFT');

        $q = $this->db->get($this->table);

        return $q->row()->total;
    }

    function countByUserIDStatusGrouped($user_id = false, $filter = array())
    {
        if (!$user_id)
            return false;

        $this->db->select("orders.fulfillment_Status as fulfillment_status, count(DISTINCT tbl_order_products.order_id) as total_count ");

        if (!empty($filter['channel_id'])) {
            if ($filter['channel_id'] == 'custom')
                $this->db->where(" (orders.channel_id is NULL or orders.channel_id not in (select id from user_channels where user_id = '{$user_id}')) ");
            else
                $this->db->where('user_channels.id', $filter['channel_id']);
        }

        if (!empty($filter['order_type'])) {
            $this->db->where_in('orders.order_type', $filter['order_type']);
        }

        if (!empty($filter['tags'])) {
            $filter['tags'] = trim(str_replace("'", "\'", $filter['tags']));

            $this->db->where(" (find_in_set('{$filter['tags']}', tbl_orders.applied_tags))");
        }

        if (!empty($filter['order_ids'])) {
            $this->db->where_in('tbl_orders.order_no', $filter['order_ids']);
        }

        if (!empty($filter['search_query'])) {
            $query = trim(str_replace("'", "\'", $filter['search_query']));
            $this->db->where(" (orders.customer_name like '%{$query}%' or orders.shipping_phone like '%{$query}%' or orders.shipping_address like '%{$query}%' or orders.shipping_address_2 like '%{$query}%' or orders.shipping_city  like '%{$query}%' or orders.shipping_state like '%{$query}%' or tbl_order_products.product_name like '%{$query}%' or orders.order_tags like '%{$query}%'  ) ");
        }

        if (!empty($filter['pay_method'])) {
            $this->db->where('order_payment_type', $filter['pay_method']);
        }

        if (!empty($filter['channel_id'])) {
            if ($filter['channel_id'] == 'custom')
                $this->db->where(" (orders.channel_id is NULL or orders.channel_id not in (select id from user_channels where user_id = '{$user_id}')) ");
            else
                $this->db->where('user_channels.id', $filter['channel_id']);
        }

        if (!empty($filter['start_date'])) {
            $this->db->where("order_date >= '" . $filter['start_date'] . "'");
        }

        if (!empty($filter['end_date'])) {
            $this->db->where("order_date <= '" . $filter['end_date'] . "'");
        }

        if (!empty($filter['product_name'])) {
            $this->db->like('tbl_order_products.product_name', $filter['product_name']);
        }


        $this->db->where('orders.user_id', $user_id);

        $this->db->group_by('orders.fulfillment_status');
        $this->db->join('tbl_order_products', 'tbl_order_products.order_id = orders.id', 'LEFT');
        $this->db->join('user_channels', 'user_channels.id = orders.channel_id', 'LEFT');
        $q = $this->db->get($this->table);
        return $q->result();
    }


    function exportByUserID($user_id = false, $limit = 50, $offset = 0, $filter = array())
    {
        if (!$user_id)
            return false;

        $this->db->select("orders.*, tbl_order_products.product_name, tbl_order_products.product_qty, tbl_order_products.product_price, tbl_order_products.product_sku,tbl_order_products.product_id");

        if (!empty($filter['channel_id'])) {
            if ($filter['channel_id'] == 'custom')
                $this->db->where(" (orders.channel_id is NULL or orders.channel_id not in (select id from user_channels where user_id = '{$user_id}')) ");
            else
                $this->db->where('user_channels.id', $filter['channel_id']);
        }

        if (!empty($filter['order_type'])) {
            $this->db->where_in('orders.order_type', $filter['order_type']);
        }

        if (!empty($filter['order_ids'])) {
            $this->db->where_in('tbl_orders.order_no', $filter['order_ids']);
        }

        if (!empty($filter['tags'])) {
            $this->db->where(" (find_in_set('{$filter['tags']}', applied_tags))");
        }

        if (!empty($filter['search_query'])) {
            $query = trim(str_replace("'", "\'", $filter['search_query']));
            $this->db->where(" (CONCAT(orders.shipping_fname, ' ', orders.shipping_lname) like '%{$query}%' or orders.shipping_phone like '%{$query}%' or orders.shipping_address like '%{$query}%' or orders.shipping_address_2 like '%{$query}%' or orders.shipping_city  like '%{$query}%' or orders.shipping_state like '%{$query}%' or tbl_order_products.product_name like '%{$query}%' or orders.order_tags like '%{$query}%' ) ");
        }

        if (!empty($filter['pay_method'])) {
            $this->db->where('order_payment_type', $filter['pay_method']);
        }

        if (!empty($filter['start_date'])) {
            $this->db->where("order_date >= '" . $filter['start_date'] . "'");
        }

        if (!empty($filter['end_date'])) {
            $this->db->where("order_date <= '" . $filter['end_date'] . "'");
        }

        if (!empty($filter['fulfillment'])) {
            $this->db->where('orders.fulfillment_status', $filter['fulfillment']);
        }

        if (!empty($filter['product_name'])) {
            $this->db->like('tbl_order_products.product_name', $filter['product_name']);
        }

        if (!empty($filter['user_filters_query'])) {
            $this->db->where($filter['user_filters_query']);
        }
        if (!empty($filter['ivr_status'])) {
            $this->db->where('orders.ivr_calling_status', $filter['ivr_status']);
        }
        if (!empty($filter['engage_status'])) {
            $this->db->where('orders.whatsapp_status', $filter['engage_status']);
        }


        $this->db->limit($limit);
        $this->db->offset($offset);
        $this->db->where('orders.user_id', $user_id);

        $this->db->order_by('order_date', 'desc');
        $this->db->group_by('orders.id');

        $this->db->join('tbl_order_products', 'tbl_order_products.order_id = orders.id', 'inner');
        $this->db->join('user_channels', 'user_channels.id = orders.channel_id', 'LEFT');

        $this->db->from($this->table);
        return $query = $this->db->get_compiled_select();
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

        $this->db->select('group_concat(product_name) as product_name, group_concat(product_sku) as product_sku, count(*) as total');
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

    function getBulkOrderProducts($order_ids = false)
    {
        if (empty($order_ids))
            return false;

        $this->db->where_in('order_id', $order_ids);
        $q = $this->db->get($this->products_table);
        return $q->result();
    }

    function fetchAPIOrders($filter = array())
    {

        $this->db->select("orders.*");

        if (!empty($filter['channel_id'])) {
            $this->db->where('orders.channel_id', $filter['channel_id']);
        }

        if (!empty($filter['order_type'])) {
            $this->db->where_in('orders.order_type', $filter['order_type']);
        }

        if (!empty($filter['order_ids'])) {
            $this->db->where_in('tbl_orders.order_no', $filter['order_ids']);
        }

        if (!empty($filter['id'])) {
            $this->db->where('orders.id', $filter['id']);
        }

        if (!empty($filter['pay_method'])) {
            $this->db->where('order_payment_type', $filter['pay_method']);
        }

        if (!empty($filter['start_date'])) {
            $this->db->where("order_date >= '" . $filter['start_date'] . "'");
        }

        if (!empty($filter['end_date'])) {
            $this->db->where("order_date <= '" . $filter['end_date'] . "'");
        }

        if (!empty($filter['fulfillment'])) {
            $this->db->where('orders.fulfillment_status', $filter['fulfillment']);
        }

        if (!empty($filter['limit'])) {
            $this->db->limit($filter['limit']);
        }

        if (!empty($filter['offset'])) {
            $this->db->offset($filter['offset']);
        }

        if (!empty($filter['user_id'])) {
            $this->db->where('orders.user_id', $filter['user_id']);
        }

        if (!empty($filter['order_by'])) {
            $this->db->order_by($filter['order_by'], (!empty($filter['order_dir']) ? $filter['order_dir'] : 'DESC'));
        }


        $q = $this->db->get($this->table);
        return $q->result();
    }


    function getAllTags($user_id = false, $order_tag = '')
    {
        if (!$user_id)
            return false;

        $this->db->select('tag');

        $this->db->where_in('user_id', $user_id);

        if (!empty($order_tag)) {
            $this->db->where_in('tag', $order_tag);
        }
        $this->db->limit(5);
         $this->db->order_by('id', 'desc');
       return  $this->db->get($this->tag_table)->result();
    }

    function fetchAllTags($user_id = false, $order_tag =false)
    {
        if (!$user_id)
            return false;

        $this->db->select('tag');
        $this->db->where_in('user_id', $user_id);
        $this->db->where_in('tag', $order_tag);
        $this->db->limit(1);
        $this->db->order_by('id', 'desc');
        return  $this->db->get($this->tag_table)->result();
    }

    function getByUserOrderID($user_id = false, $order_id = false)
    {
        if (!$user_id || !$order_id)
            return false;

        $this->db->where('user_id', $user_id);
        $this->db->where('order_id', $order_id);

        $this->db->limit(1);
        $this->db->order_by('id', 'desc');
        return  $this->db->get($this->table)->row();
    }

    function getOrderProductsWithGst($order_id = false, $user_id = false)
    {
        if (!$order_id)
            return false;

        $this->db->select("tbl_order_products.*,product_details.length,product_details.breadth,product_details.height,product_details.weight,product_details.igst,product_details.hsn_code,product_details.igst");
        $this->db->where('tbl_order_products.order_id', $order_id);
        $this->db->join("product_details", "product_details.product_sku = tbl_order_products.product_sku and product_details.user_id = $user_id", "LEFT");
        $this->db->group_by('tbl_order_products.product_sku');
        $q = $this->db->get($this->products_table);
        return $q->result();
    }

    function getOrderInvoiceDate($user_id = false, $shippingId = false)
    {
        $this->db->select("invoice_number, created");
        $this->db->where('user_id', $user_id);
        $this->db->where('shipping_id', $shippingId);
        $this->db->limit(1);
        return $this->db->get($this->order_invoice_table)->row();
    }

    function createOrderInvoiceDate($user_id = false, $shippingId = false)
    {
        if (!$user_id && !$shippingId)
            return false;

        $getorderDate = $this->getOrderInvoiceDate($user_id, $shippingId);

        if (empty($getorderDate)) {
            $inv_no = time();
            $save['user_id'] = $user_id;
            $save['shipping_id'] = $shippingId;
            $save['invoice_number'] = "INV-" . $inv_no;
            $save['created'] = time();
            $this->db->insert($this->order_invoice_table, $save);
            $getorderDate = $this->getOrderInvoiceDate($user_id, $shippingId);
        }
        return $getorderDate;
    }

    function getOrderProductsWith($product_name = false, $user_id = false, $field = false)
    {
        if (!$user_id && !$product_name && !$field)
            return false;
        $this->db->select("product_details.length,product_details.breadth,product_details.height,product_details.weight,product_details.igst,product_details.hsn_code,product_details.igst");
        $this->db->where('product_details.' . $field . '', $product_name);
        $this->db->where('product_details.user_id', $user_id);
        $this->db->limit(1);
        $q = $this->db->get('product_details');
        return $q->row();
    }


    function getOrderProductsWithCode($product_name = false, $user_id = false, $field = false)
    {
        if (!$user_id && !$code)
            return false;
        $this->db->select("product_details.length,product_details.breadth,product_details.height,product_details.weight,product_details.igst,product_details.hsn_code,product_details.igst");
        $this->db->where('product_details.code', $code);
        $this->db->where('product_details.user_id', $user_id);
        $this->db->limit(1);
        $q = $this->db->get('product_details');
        return $q->row();
    }

    function getByUserDuplicateOrderID($user_id = false, $order_id = false)
    {
        if (!$user_id || !$order_id)
            return false;

        $this->db->where('user_id', $user_id);
        $this->db->where('order_id', $order_id);
        $this->db->where('channel_id is NULL');

        $this->db->limit(1);
        $this->db->order_by('id', 'desc');

        return $this->db->get($this->table)->row();
    }

    function insertOrderTag($save = array())
    {
        if (empty($save))
            return false;

        $this->db->insert($this->tag_table, $save);
        return $this->db->insert_id();
    }

    function insertInvoice($save = array())
    {
        if (empty($save))
            return false;

        $save['created'] = time();
        $save['modified'] = time();
        $this->db->insert($this->cargo_invoice, $save);
        return $this->db->insert_id();
    }

    function deleteOrderInvoice($id = false)
    {
        if (!$id)
            return false;

        $this->db->where('order_id', $id);
        $this->db->delete($this->cargo_invoice);

        return true;
    }

    function getOrderInvoice($order_id = false)
    {
        if (!$order_id)
            return false;

        $this->db->where('order_id', $order_id);
        $q = $this->db->get($this->cargo_invoice);
        return $q->result();
    }

    function getCargoOrderProducts($order_id = false)
    {
        if (!$order_id)
            return false;

        $this->db->select("tbl_order_products_details.*, tbl_order_products.*, order_mps.awb_number as parent_awb_number, order_mps.mps_number as awb_number");

        $this->db->where('tbl_order_products.order_id', $order_id);

        $this->db->join('tbl_order_products_details', 'tbl_order_products_details.order_product_id = tbl_order_products.id', 'left');
        $this->db->join('order_mps', 'order_mps.order_product_id = tbl_order_products_details.order_product_id AND order_mps.ship_status != "cancelled"', 'left');

        // $this->db->group_by('tbl_order_products.id');

        $q = $this->db->get($this->products_table);
        return $q->result();
    }

    function getOrderProductsById($order_id = false, $id = false)
    {
        if (!$order_id)
            return false;

        $this->db->select("tbl_order_products.*, tbl_order_products_details.*");

        if ($id != '') {
            $this->db->where('id', $id);
        }
        $this->db->where('tbl_order_products.order_id', $order_id);
        $this->db->join('tbl_order_products_details', 'tbl_order_products_details.order_product_id = tbl_order_products.id');
        $q = $this->db->get($this->products_table);
        return $q->result();
    }

    function getHubName($pincode = false)
    {
        if (!$pincode)
            return false;

        $this->db->where('pincode', $pincode);
        $this->db->limit(1);
        $q = $this->db->get('cargo_pincodes');
        return $q->row();
    }

    function insertProductDetails($save = array())
    {
        if (empty($save))
            return false;
        $this->db->insert($this->products_details_table, $save);
        return $this->db->insert_id();
    }

    function deleteOrderProductDetails($id = false)
    {
        if (!$id)
            return false;

        $this->db->where('order_id', $id);
        $this->db->delete($this->products_details_table);

        return true;
    }

    function getOrdercategories($id = false)
    {
        $this->db->select("orders_categories.id, orders_categories.categories_name");
        $this->db->where('orders_categories.status', '1');
        if ($id != '') {
            $this->db->where('id', $id);
        }
        $q = $this->db->get('orders_categories');
        return $q->result();
    }

    function insertreverseProduct($save_product = false)
    {
        $this->db->insert($this->products_table, $save_product);
        return true;
    }

    function updatereverseorder($order_id = false, $save = array())
    {
        if (!$order_id || empty($save))
            return false;

        $save['modified'] = time();

        $this->db->where('id', $order_id);
        $this->db->set($save);
        $this->db->update($this->table);
        return true;
    }

    function updatereverseproduct($order_id = false, $save = array())
    {
        if (!$order_id || empty($save))
            return false;

        $this->db->where('order_id', $order_id);
        $this->db->set($save);
        $this->db->update($this->products_table);
        return true;
    }

    function insertReverseQCProduct($save = array())
    {
        if (empty($save))
            return false;

        $this->db->insert($this->order_reverse_qc_table, $save);
        return $this->db->insert_id();
    }

    function deleteReverseQCOrderProduct($id = false)
    {
        if (!$id)
            return false;

        $this->db->where('order_id', $id);
        $this->db->delete($this->order_reverse_qc_table);

        return true;
    }

    function getReverseQCOrderProducts($order_id = false)
    {
        if (!$order_id)
            return false;

        $this->db->where('order_id', $order_id);
        //$q = $this->db->get($this->order_reverse_qc_table);
        return true;//$q->result();
    }

    function insertOrderDetails($save = array())
    {
        if (empty($save))
            return false;

        $save['created'] = time();
        $save['modified'] = time();

        $this->db->insert($this->orders_details_international, $save);
        return $this->db->insert_id();
    }

    function deleteOrderDetails($id = false)
    {
        if (!$id)
            return false;

        $this->db->where('order_id', $id);
        $this->db->delete($this->orders_details_international);

        return true;
    }

    function getOrderDetails($order_id = false)
    {
        if (!$order_id)
            return false;

        $this->db->select("csb_type,shipping_remark,ioss_remark,currency");
        $this->db->where('order_id', $order_id);
        $q = $this->db->get($this->orders_details_international);
        return $q->row(0);
    }

    function insertMultiBagging($save = array())
    {
        if (empty($save))
            return false;

        $this->db->insert($this->orders_multi_bagging_international, $save);
        return $this->db->insert_id();
    }

    function checkShopifyOrderExist($api_order_id, $channel_id)
    {
        if (!$api_order_id)
            return false;
        
        $this->db->select("id");
        $this->db->where('api_order_id', $api_order_id);
        $this->db->where('channel_id', $channel_id);
        $this->db->limit(1);
        $q = $this->db->get($this->table);
        return $q->row();
    }

    function getEnable_custom_order($user_id = false)
    {
        if (!$user_id)
            return false;

        $this->db->select("id,custom_order_confirm");
        $this->db->where('user_id', $user_id);
        $q = $this->db->get('tbl_whatsapp_notification');
        return $q->result();
    }

    function getPickupDataComma()
    {
        $this->db->where('created > 1659292200');
        $this->db->where('created <= 1667200000');
        $this->db->where('is_process = 0');
        $this->db->where('shipment_ids IS NOT NULL');
        $this->db->limit(10000);
        $this->db->order_by('id', 'desc');
        $q = $this->db->get('pickups');
        return $q->result();
    }

    function insertPickupData($data = array()){
        if (!$data)
            return false;

        $this->db->insert_batch('pickup_data', $data);
        return true;
    }

    function checkOrderRequest($user_id, $order_unique_id, $status = false)
    {
        $this->db->where('user_id', $user_id);
        $this->db->where('channel_id IS NULL');
        $this->db->where('api_order_id', (string)$order_unique_id);
        $this->db->limit(1);
        $this->db->order_by('id', 'desc');
        $q = $this->db->get($this->table);
        return $q->row();
    }

    public function insertProductsBatch($products)
{
    return $this->db->insert_batch('tbl_order_products', $products);
}
}