<?php

class Channels_model extends MY_model
{

    public function __construct()
    {
        parent::__construct();
        $this->table = 'user_channels';
    }

    function create($save = array())
    {
        if (empty($save))
            return false;

        $save['created'] = time();
        $save['modified'] = time();

        $this->db->insert($this->table, $save);
        return $this->db->insert_id();
    }

    function addEasyEcomMapping($save)
    {
        if (empty($save))
            return false;

        $save['created'] = time();
        $save['modified'] = time();

        $this->db->insert('easyecom_setting', $save);
        return $this->db->insert_id();
    }

    function update($id = false, $save = array())
    {
        if (empty($save) || empty($id))
            return false;

        $save['modified'] = time();

        $this->db->set($save);
        $this->db->where('id', $id);
        $this->db->update($this->table);
        return $this->db->insert_id();
    }

    function getChannelsByUserID($user_id = false)
    {
        if (!$user_id)
            return false;

        $this->db->where('user_id', $user_id);
        $q = $this->db->get($this->table);
        return $q->result();
    }

    function delete($id = false)
    {
        if (!$id)
            return false;

        $this->db->where('id', $id);
        $this->db->delete($this->table);

        return true;
    }

    function fetchChannels($filter = array())
    {
        if (!empty($filter['channel_in'])) {
            $this->db->where_in('channel', $filter['channel_in']);
        }

        if (!empty($filter['channel_not_in'])) {
            $this->db->where_not_in('channel', $filter['channel_not_in']);
        }

        if (!empty($filter['before_last_fetch'])) {
            $this->db->where('last_order_fetch_at <', $filter['before_last_fetch']);
        }
        if (!empty($filter['after_last_fetch'])) {
            $this->db->where('last_order_fetch_at >', $filter['after_last_fetch']);
        }

        $this->db->order_by('id', 'asc');
        $q = $this->db->get($this->table);
        //echo $this->db->last_query();exit;
        return $q->result();
    }

    function isChannelExists($user_id = false, $channel = false, $host = false)
    {
        if (!$user_id || !$channel || !$host)
            return false;

        $this->db->where('user_id', $user_id);
        $this->db->where('channel', strtolower($channel));
        $this->db->where('api_field_1', $host);


        $q = $this->db->get($this->table);
        return $q->row();
    }

    function getChannelsByChannelName($user_id = false, $channel = false, $abandoned_active = false)
    {
        if (!$user_id)
            return false;

        $this->db->where('user_id', $user_id);
        if ($channel)
            $this->db->where_in('channel', $channel);
        if ($abandoned_active)
            $this->db->where('abandoned_checkouts', '1');

        $q = $this->db->get($this->table);
        return $q->result();
    }

    function getChannelsByChannelId($user_id = false, $channel_id = false)
    {
        if (!$user_id)
            return false;
            
        if (!$channel_id)
            return false;

        $this->db->where('user_id', $user_id);
        if ($channel_id)
            $this->db->where_in('id', $channel_id);

        $q = $this->db->get($this->table);
        return $q->row();
    }

    function updateVinculumSyncStatus($type = 'order')
    {
        $save = array();
        if ($type == 'order')
            $save['is_synced'] = 0;
        if ($type == 'label')
            $save['label_fetched'] = 0;
        if (empty($save))
            return false;
        $this->db->set($save);
        $this->db->where('channel', 'vinculum');
        $this->db->update($this->table);
        return $this->db->insert_id();
    }


    function getVinculumChannel($filter = array())
    {
        if (empty($filter))
            return false;
        if (!empty($filter['is_synced']))
            $this->db->where('is_synced', '0');
        if (!empty($filter['channel_type']))
            $this->db->where('api_field_3', strtolower($filter['channel_type']));
        if (!empty($filter['label_fetched']))
            $this->db->where('label_fetched', '0');
        $this->db->where('channel', 'vinculum');
        $this->db->order_by('id', 'desc');
        // $this->db->limit(1);
        $q = $this->db->get($this->table);
        return $q->result();
    }

    function getUserChannelList($user_id){

        $this->db->select("channel_name,id");
        $this->db->where('user_id',$user_id); 
        $this->db->group_by('channel_name'); 
   $q = $this->db->get('user_channels');
        //  pr($this->db->last_query(),1);
        return $q->result(); 
    }

    function getUserChannelDetail($user_id,$filter=array()){

         $this->db->select("
           (case when (IFNULL(orders.channel_id, 'custom') = 'custom' ) then 'custom' else user_channels.channel_name end) as channel_name,"
         . "sum( case when (order_shipping.ship_status = 'booked') then 1 else 0 end) as unshipped,"
         . "sum( case when (order_shipping.ship_status = 'pending pickup') then 1 else 0 end) as pending,"
         . "sum( case when (order_shipping.ship_status = 'in transit' OR order_shipping.ship_status = 'out for delivery' OR order_shipping.ship_status = 'exception' OR order_shipping.ship_status='lost' OR order_shipping.ship_status ='damaged') then 1 else 0 end) as in_transit,"
         . "sum( case when (order_shipping.ship_status = 'delivered') then 1 else 0 end) as delivered,"
         . "sum( case when (order_shipping.ship_status = 'rto') then 1 else 0 end) as rto,"
         . "count(DISTINCT order_shipping.id) as total_shipment" 
        //   . "sum(case when (order_shipping.ship_status != 'cancelled' AND order_shipping.ship_status !='new') then 1 else 0 end) as total_shipment," 
        );
        if(!empty($filter['start_date'])) 
            $this->db->where("order_shipping.created >= '" . $filter['start_date'] . "'");
        
        if(!empty($filter['end_date'])) 
            $this->db->where("order_shipping.created <= '" . $filter['end_date'] . "'");
        
        if (!empty($filter['channel_name'])) 
        {
            if($filter['channel_name'] == 'custom'){
                $this->db->where(" (orders.channel_id is NULL or orders.channel_id not in (select id from user_channels where user_id = '{$user_id}')) ");
            }
            else{
                $this->db->where('orders.channel_id', $filter['channel_name']);
            }
        }
        // $this->db->join('order_products','order_products.order_id = orders.id','LEFT'); 
        $this->db->join('user_channels','user_channels.id = orders.channel_id','LEFT'); 
        $this->db->join('order_shipping','order_shipping.order_id = orders.id','LEFT');
        $this->db->where('orders.order_type','ecom');
        $this->db->where('order_shipping.ship_status !=', 'cancelled'); 
        $this->db->where('order_shipping.ship_status !=','new'); 
        $this->db->where('orders.user_id',$user_id);
        $this->db->group_by('user_channels.channel_name'); 
        $query = $this->db->get('orders');

        $shipping_details=$query->result();
       
        $this->db->select("count(DISTINCT orders.id) as total_order ,IFNULL(user_channels.channel_name,'custom') channel_name");
        if(!empty($filter['start_date'])) 
        $this->db->where("orders.created >= '" . $filter['start_date'] . "'");
    
        if(!empty($filter['end_date'])) 
        $this->db->where("orders.created <= '" . $filter['end_date'] . "'");

        $this->db->where('orders.order_type','ecom'); 
        $this->db->where('orders.fulfillment_status !=','cancelled'); 
        $this->db->where('orders.user_id',$user_id); 
        $this->db->group_by('orders.channel_id');
        $this->db->order_by('total_order','DESC'); 
        $this->db->join('user_channels','user_channels.id = orders.channel_id','LEFT'); 
        $q = $this->db->get('orders'); 
        $order_details=$q->result();
        //pr($this->db->last_query(),1); 
        $total_order_detail=[];
        if(count($order_details)>0)
        {
            foreach($order_details as $order)
            {
                $total_order_detail[$order->channel_name]=$order->total_order;
            }
        }       
        return array($shipping_details,$total_order_detail);
    
}

    function getChannelsByChnlID($user_id = false)
    {
        if (!$user_id)
            return false;

        $this->db->where('id', $user_id);
        $q = $this->db->get($this->table);
        return $q->result();
    }

   
}

