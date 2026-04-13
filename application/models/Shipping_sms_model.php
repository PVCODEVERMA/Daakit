<?php

class Shipping_sms_model extends MY_model
{
    public function __construct()
    {
        parent::__construct();
        $this->table = 'send_status_sms';
    }

    function updateStatusSMS($id = false, $update = false) {
        if (!$id || !$update)
            return false;


        $this->db->where('shipment_id', $id);
        $q = $this->db->get($this->table);
        if($q->row()) {
            $update['modified'] = time();

            $this->db->where('shipment_id', $id);
            $this->db->set($update);
            $this->db->update($this->table);
        } else {
            $update['shipment_id'] = $id;
            $update['created'] = time();
            $update['modified'] = time();

            $this->db->insert($this->table, $update);
        }

        return true;
    }

    function getShipmentSMSByID($shipment_id = false)
    {
        if (!$shipment_id)
            return false;

        $this->db->select('send_status_sms.*');
        $this->db->where('shipment_id', $shipment_id);
        $this->db->limit(1);
        $q = $this->db->get($this->table);

        return $q->row();
    }
}