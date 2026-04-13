<?php

class Tracking_model extends MY_model
{
    var $tracking_db;

    public function __construct()
    {
        parent::__construct();
        $this->table = 'awb_tracking';
        $this->shipment_tracking = 'shipment_tracking';
        $this->shipment_tracking_metadata = 'shipment_tracking_metadata';
        $this->custom_tracking_metadata = 'custom_tracking_metadata';

        $this->tracking_db = $this->load->database('awb_tracking', TRUE);
    }

    function deleteByAWB($awb = false)
    {
        if (!$awb)
            return false;

        $this->tracking_db->where('awb_number', $awb);
        $this->tracking_db->delete($this->table);
        return true;
    }

    function deleteByAWBEventTime($awb = false, $event_time = false)
    {
        if (!$awb || !$event_time)
            return false;

        $this->tracking_db->where('awb_number', (string)$awb);
        $this->tracking_db->where('event_time', (string)$event_time);
        $this->tracking_db->delete($this->table);
        return true;
    }

    function batchInsert($save = array())
    {
        if (empty($save))
            return false;

        $this->tracking_db->insert_batch($this->table, $save);
        return true;
    }

    function insert($save = array())
    {
        if (empty($save))
            return false;

        $this->tracking_db->insert($this->table, $save);
        return true;
    }

    function getByAWB($awb = false)
    {
        if (!$awb)
            return false;

        $this->tracking_db->where('awb_number', $awb);
        $this->tracking_db->order_by('event_time', 'desc');
        $this->tracking_db->group_by('event_time');
        $q = $this->tracking_db->get($this->table);
        return $q->result();
    }

    function insertShipmentTracking($save = array())
    {
        if (empty($save))
            return false;

        $this->db->insert($this->shipment_tracking, $save);
        return true;
    }

    function updateShipmentTracking($id = false, $update = false)
    {
        if (!$id || !$update)
            return false;

        $this->db->where('id', $id);
        $this->db->set($update);
        $this->db->update($this->shipment_tracking);
        return true;
    }

    function getTrackingBYShipmentID($shipment_id = false)
    {
        if (!$shipment_id)
            return false;

        $this->db->where('shipment_id', $shipment_id);
        $this->db->limit(1);
        $q = $this->db->get($this->shipment_tracking);
        return $q->row();
    }

    function deleteDuplicateForAWB($awb = false)
    {
        if (!$awb)
            return false;

        $this->tracking_db->query("delete from awb_tracking
            where awb_number = '{$awb}' and id not in(
           SELECT * FROM(
           SELECT max(id) FROM `awb_tracking` where awb_number = '{$awb}' group by event_time
           ) temp)");

        return true;
    }

    function batchInsertNewDB($save = array())
    {
        if (empty($save))
            return false;

        $this->tracking_db->insert_batch($this->table, $save);
        return true;
    }

    function saveEventMetadata($save = array())
    {
        if (empty($save))
            return false;

        $this->tracking_db->where('awb', $save['awb']);
        $this->tracking_db->where('event', $save['event']);
        $this->tracking_db->delete($this->shipment_tracking_metadata);

        $save['created'] = time();
        $save['modified'] = time();

        $this->tracking_db->insert($this->shipment_tracking_metadata, $save);
        return $this->tracking_db->insert_id();
    }

    function get_tracking_metadata($awb_number = false)
    {
        if ($awb_number) {
            $this->tracking_db->where('awb', $awb_number);
        }
        $this->tracking_db->order_by('id', 'asc');
        // $this->tracking_db->where_in('status', ['delivery_attempt_metadata', 'pickup_reattempt', 'out_for_delivery']);
        // $this->tracking_db->limit(1);
        $q = $this->tracking_db->get($this->shipment_tracking_metadata);

        return $q->result();
    }

    function batchInsertCustomMetadata($save = array())
    {
        if (empty($save))
            return false;

        $this->tracking_db->insert_batch($this->custom_tracking_metadata, $save);
        return true;
    }

    function get_custom_tracking_metadata($awb_number = false)
    {
        if (!$awb_number)
            return false;

        $this->tracking_db->where('awb_number', $awb_number);
        $this->tracking_db->order_by('id', 'asc');
        $q = $this->tracking_db->get($this->custom_tracking_metadata);
        return $q->result();
    }

    function deleteCustomMetadata($awb_number = false)
    {
        if (!$awb_number)
            return false;

        $this->tracking_db->where('awb_number', (string) $awb_number);
        $this->tracking_db->delete($this->custom_tracking_metadata);
        return true;
    }

    function get_custom_tracking_metadata_row($awb_number = false, $ref_type = false)
    {
        if (!$awb_number || !$ref_type)
            return false;

        $this->tracking_db->where('ref_type', $ref_type);
        $this->tracking_db->where('awb_number', $awb_number);
        $q = $this->tracking_db->get($this->custom_tracking_metadata);
        return $q->row(0);
    }
}