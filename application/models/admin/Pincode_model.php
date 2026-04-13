<?php

class Pincode_model extends MY_model
{

    public function __construct()
    {
        parent::__construct();
        $this->table = 'pincodes';

        $this->slave = $this->load->database('slave', TRUE);
    }

    function update($id = false, $save = array())
    {
        if (empty($save) || empty($id))
            return false;

        $this->db->set($save);
        $this->db->where('id', $id);
        $this->db->update($this->table);
        return true;
    }

    function deleteCourierPincodes($courier_id  = false)
    {
        if (!$courier_id)
            return false;

        $this->db->where('courier_id', $courier_id);
        $this->db->delete($this->table);
        return true;
    }

    function batchInsert($save = array())
    {
        if (empty($save))
            return false;

        $this->db->insert_batch($this->table, $save);
        return true;
    }
    function batchUpdate($save = array())
    {
        if (empty($save))
            return false;

        $this->db->update_batch($this->table, $save, 'id');
        return true;
    }

    function getCourierPincodes($courier_id = false)
    {
        if (!$courier_id)
            return false;

        $this->db->where('courier_id', $courier_id);
        $q = $this->db->get($this->table);
        return $q->result();
    }

    function getPincodesList($limit = 50, $offset = 0, $filter = array(), $return_query = false)
    {

        $this->slave->select('pincodes.*, courier.name as courier_name,courier.courier_alias as courier_alias');

        if (!empty($filter['pincode'])) {
            $this->slave->where('pincodes.pincode', $filter['pincode']);
        }
        if (!empty($filter['courier_id'])) {
            $this->slave->where('pincodes.courier_id', $filter['courier_id']);
        }


        $this->slave->limit($limit);

        $this->slave->offset($offset);

        $this->slave->order_by('pincodes.id', 'desc');


        $this->slave->join('courier', 'courier.id = pincodes.courier_id', 'LEFT');


        if ($return_query) {
            $this->slave->from($this->table);
            return $query =   $this->slave->get_compiled_select();
        } else {
            $q = $this->slave->get($this->table);
            return $q->result();
        }
    }

    function countPincodes($filter = array())
    {

        $this->slave->select('count(*) as total');


        if (!empty($filter['pincode'])) {
            $this->slave->where('pincodes.pincode', $filter['pincode']);
        }
        if (!empty($filter['courier_id'])) {
            $this->slave->where('pincodes.courier_id', $filter['courier_id']);
        }




        $this->slave->join('courier', 'courier.id = pincodes.courier_id', 'LEFT');


        $q = $this->slave->get($this->table);

        return $q->row()->total;
    }
}
