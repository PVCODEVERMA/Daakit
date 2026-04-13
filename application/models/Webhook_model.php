<?php

class Webhook_model extends MY_model
{

    public function __construct()
    {
        parent::__construct();
        $this->table = 'webhooks';
        $this->event_table = 'webhook_events';
    }

    function getShipmentLastEvent($shipment_id = false, $webhook_id = false)
    {
        if (!$shipment_id || !$webhook_id)
            return false;

        $this->db->where('webhook_id', $webhook_id);
        $this->db->where('shipment_id', $shipment_id);

        $this->db->limit(1);

        $query = $this->db->get($this->event_table);
        if ($query->num_rows() == 1)
            return $query->row();

        return FALSE;
    }

    function createEvent($save = array())
    {
        if (empty($save))
            return false;

        $this->db->insert($this->event_table, $save);
        return $this->db->insert_id();
    }

    function updateEvent($id = false, $save = array())
    {
        if (!$id || empty($save))
            return false;

        $this->db->where('id', $id);
        $this->db->set($save);
        $this->db->update($this->event_table);
        return true;
    }

    function create($save = array())
    {
        if (empty($save))
            return false;

        $this->db->insert($this->table, $save);
        return $this->db->insert_id();
    }

    function update($id = false, $save = array())
    {
        if (!$id || empty($save))
            return false;

        $this->db->where('id', $id);
        $this->db->set($save);
        $this->db->update($this->table);
        return true;
    }

    function getByUserID($user_id = false)
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

    function getUserAllWebhooks($user_id = false)
    {
        if (!$user_id)
            return false;

        $this->db->where('user_id', $user_id);

        $this->db->order_by('id', 'desc');

        $q = $this->db->get($this->table);
        return $q->result();
    }

    function getUserActiveWebhooks($user_id = false)
    {
        if (!$user_id)
            return false;

        $this->db->where('user_id', $user_id);
        $this->db->where('status', '1');

        $this->db->order_by('id', 'asc');

        $q = $this->db->get($this->table);
        return $q->result();
    }

    function getUserAPIWebhooks($url = false, $user_id = false, $id = false)
     {
         if (empty($url))
             return false;

             $this->db->where('user_id', $user_id);
             $this->db->where('url', $url); 

             if(!empty($id)){
                $this->db->where('id !=', $id);
             }
             
             $this->db->order_by('id', 'asc');
             $q = $this->db->get($this->table);
             return $q->row();
     }

     function getUserAPIWebhooksID($id = false)
     {
         if (empty($id))
             return false;

             $this->db->where('id', $id);
             $this->db->order_by('id', 'asc');
             $q = $this->db->get($this->table);
             return $q->row();
     }
    
}
