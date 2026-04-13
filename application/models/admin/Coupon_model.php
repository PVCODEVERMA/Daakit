<?php

class Coupon_model extends MY_model
{

    public function __construct()
    {
        parent::__construct();
        $this->table = 'coupons';
        $this->table_coupon_apply = 'coupons_applied';
        $this->table_coupons_specific_seller = 'coupons_specific_seller';

        $this->slave = $this->load->database('slave', TRUE);
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

    function specific_create($save = array())
    {
        if (empty($save))
            return false;

        $save['created'] = time();
        //$save['modified'] = time();
        $this->db->insert($this->table_coupons_specific_seller, $save);
        return $this->db->insert_id();
    }

    function apply_coupon_create($save = array())
    {
        if (empty($save))
            return false;

        $save['created'] = time();
        $this->db->insert($this->table_coupon_apply, $save);
        return $this->db->insert_id();
    }
    function apply_coupon_update($id = false, $save = array())
    {
        if (empty($save) || empty($id))
            return false;

        $save['modified'] = time();
        $this->db->set($save);
        $this->db->where('id', $id);
        $this->db->update($this->table_coupon_apply);
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

    function countByCouponID($filter = array())
    {
        $this->db->select('count(*) as total');


        if (!empty($filter['coupon_code'])) {
            $this->db->where('coupon_code', $filter['coupon_code']);
        }

        if (!empty($filter['coupon_type'])) {
            $this->db->where('coupon_type', $filter['coupon_type']);
        }
        if (!empty($filter['status'])) {
            $status = !empty($filter['status'] == 'active') ? '1' : '0';
            $this->db->where('status', $status);
        }
        $q = $this->db->get($this->table);

        return $q->row()->total;
    }

    function fetchByCouponID($limit = 50, $offset = 0, $filter = array())
    {

        if (!empty($filter['coupon_code'])) {
            $this->db->where('coupon_code', $filter['coupon_code']);
        }

        if (!empty($filter['coupon_type'])) {
            $this->db->where('coupon_type', $filter['coupon_type']);
        }
        if (!empty($filter['status'])) {
            $this->db->where('status', $filter['status']);
        }

        $this->db->limit($limit);
        $this->db->offset($offset);
        $this->db->order_by('created', 'desc');

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
    function isCouponCodeExists($coupon = false, $id = false)
    {
        if (!$coupon)
            return false;

        $this->db->where('coupon_code', strtolower($coupon));
        if ($id) {
            $this->db->where('id !=', $id);
        }
        $q = $this->db->get($this->table);
        return $q->row();
    }

    function isCouponCodeAvilable($coupon = false)
    {
        if (!$coupon)
            return false;

        $this->db->where('coupon_code', strtolower($coupon));
        $this->db->where('status', '1');
        $q = $this->db->get($this->table);
        return $q->row();
    }

    function isCouponCodeApplied($coupon_id = false, $user_id = false)
    {
        if (!$coupon_id && !$user_id)
            return false;

        $this->db->select('count(*) as total');
        $this->db->where('seller_id', $user_id);
        $this->db->where('coupon_id', $coupon_id);
        $this->db->where('coupon_status', 1);
        $q = $this->db->get($this->table_coupon_apply);
        return $q->row()->total;
    }

    function getCouponApplied($user_id = false, $payment_id)
    {
        if (!$payment_id && !$user_id)
            return false;

        $this->db->where('seller_id', $user_id);
        $this->db->where('payment_id', $payment_id);
        $q = $this->db->get($this->table_coupon_apply);
        return $q->row();
    }

    function getCouponAppliedID($user_id = false, $coupon_id)
    {
        if (!$coupon_id && !$user_id)
            return false;

        $this->db->where('seller_id', $user_id);
        $this->db->where('coupon_id', $coupon_id);
        $this->db->where('coupon_status', '0');
        $q = $this->db->get($this->table_coupon_apply);
        return $q->row();
    }

    function specific_delete($id = false)
    {
        if (!$id)
            return false;
        $this->db->where_in('coupon_id', $id);
        $this->db->delete($this->table_coupons_specific_seller);
        return true;
    }
    function check_specific_seller($coupon_id = false, $user_id = false)
    {
        if (!$coupon_id && !$user_id)
            return false;

        $this->db->select('*');
        $this->db->where('seller_id', $user_id);
        $this->db->where('coupon_id', $coupon_id);
        $q = $this->db->get($this->table_coupons_specific_seller);
        return $q->row();
    }

    function specific_seller($coupon_id = false)
    {
        if (!$coupon_id)
            return false;

        $this->db->select('seller_id');
        $this->db->where('coupon_id', $coupon_id);
        $q = $this->db->get($this->table_coupons_specific_seller);
        return $q->result();
    }

    function getapplyCoupons($coupon_id)
    {
        if (!$coupon_id)
            return false;

        $this->slave->select("users.id,users.fname, users.lname, coupons_applied.created as applied_date, coupons_applied.recharge_amount, coupons_applied.discount_amount, coupons_applied.payment_coupon_type as coupon_type,coupons_applied.coupon_status");
        $this->slave->join('coupons', 'coupons.id = coupons_applied.coupon_id');
        $this->slave->join('users', 'users.id = coupons_applied.seller_id');
        $this->slave->where('coupons_applied.coupon_id', $coupon_id);
        $this->slave->from($this->table_coupon_apply);
        return $query = $this->slave->get_compiled_select();
    }
}
