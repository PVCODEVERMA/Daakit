<?php

class User_model extends MY_model
{

    public function __construct()
    {
        parent::__construct();
        $this->table = 'users';
        $this->tag_table = 'user_tags';
    }

    function create($save = array())
    {
        if (empty($save))
            return false;

        $save['created'] = date("Y-m-d H:i:s");
        $save['modified'] = date("Y-m-d H:i:s");

        $this->db->insert($this->table, $save);
        return $this->db->insert_id();
    }

    function update($id = false, $save = array())
    {
        if (!$id || empty($save))
            return false;

        $save['modified'] = date("Y-m-d H:i:s");

        $this->db->where('id', $id);
        $this->db->update($this->table, $save);
        return true;
    }

    function getByEmail($email = false)
    {
        if (!$email) {
            $this->error = 'Invalid Email';
            return false;
        }
        $this->db->where('email', $email);
        $this->db->limit(1);
        $q = $this->db->get($this->table);
        $user = $q->row();
        return !empty($user) ? $user : FALSE;
    }

    function getByMobile($mobile = false)
    {
        if (!$mobile) {
            $this->error = 'Invalid Mobile';
            return false;
        }
        $this->db->where('phone', $mobile);
        $this->db->limit(1);
        $q = $this->db->get($this->table);
        $user = $q->row();
        return !empty($user) ? $user : FALSE;
    }

    function userByEmailPassword($email = false, $password = false, $isUnicommerce = false)
    {
        if (!$email || !$password)
            return false;

        $this->db->where('email', $email);
        if ($isUnicommerce) {
            $this->db->where('unicommerce_password', $password);
        } else {
            $this->db->where('password', $password);
        }
        $this->db->limit(1);
        $q = $this->db->get($this->table);
        return $q->row();
    }

    function savePassword($id = false, $password = false)
    {
        if (!$id || !$password)
            return false;

        $this->db->where('id', $id);
        $this->db->set('password', $password);
        $this->db->update($this->table);
        return true;
    }

    function getChildUsers($user_id = false)
    {
        if (!$user_id)
            return false;

        $this->db->where('parent_id', $user_id);
        $this->db->order_by('created', 'desc');
        $q = $this->db->get($this->table);
        return $q->result();
    }

    function credit_debit_wallet($user_id = false, $amount = 0, $type = false)
    {
        if (!$user_id || $amount <= 0 || !$type)
            return false;

        $this->db->where('id', $user_id);
        if ($type == 'credit')
            $this->db->set('wallet_balance', "round(wallet_balance+{$amount},2)", FALSE);
        if ($type == 'debit')
            $this->db->set('wallet_balance', "round(wallet_balance-{$amount},2)", FALSE);

        $this->db->update($this->table);
        return true;
    }

    function hold_release_remittance($user_id = false, $amount = 0, $type = false)
    {
        if (!$user_id || $amount <= 0 || !$type)
            return false;

        $this->db->where('id', $user_id);
        if ($type == 'hold')
            $this->db->set('remittance_on_hold_amount', "round(remittance_on_hold_amount+{$amount},2)", FALSE);
        if ($type == 'release')
            $this->db->set('remittance_on_hold_amount', "round(remittance_on_hold_amount-{$amount},2)", FALSE);

        $this->db->update($this->table);
        return true;
    }

    function ivrChargeableUsers()
    {
        $this->db->where('ivr_call_chargeable', '1');
        $this->db->order_by('created', 'desc');
        $q = $this->db->get($this->table);
        return $q->result();
    }

    function getAllTags($user_id = false, $user_tags = '')
    {
        if (!$user_id)
            return false;

        $this->db->select('tag');

        if (!empty($user_tags)) {
            $this->db->where_in('tag', $user_tags);
        }

        return $this->db->get($this->tag_table)->result();
    }

    function insertUserTag($save = array())
    {
        if (empty($save))
            return false;

        $this->db->insert($this->tag_table, $save);
        return $this->db->insert_id();
    }

    function getUserdata($id = false)
    {
        if (!$id)
            return false;

        $this->db->where('id', $id);
        $q = $this->db->get($this->table);
        return $q->result();
    }

    function whatsappChargeableUsers()
    {
        $this->db->where('whatsapp_enable', '1');
        $this->db->order_by('created', 'desc');
        $q = $this->db->get($this->table);
        return $q->result();
    }

    function enable_contact($user_id = false, $view_id = false, $type = false)
    {
        if (empty($user_id) || empty($view_id) || empty($type))
            return false;

        $save = [
            'user_id' => $user_id,
            'view_user_id' => $view_id,
            'view_type' => $type,
            'created' => time(),
            'modified' => time(),
        ];
        $this->db->insert('enable_contact', $save);
        return $this->db->insert_id();
    }

    function checkParentUser($id = false, $user_id = false)
    {
        if (!$user_id)
            return false;

        $this->db->where('parent_id', $user_id);
        $this->db->where('id', $id);
        $this->db->order_by('created', 'desc');
        $q = $this->db->get($this->table);
        $user = $q->row();
        return !empty($user) ? 1 : 0;
    }

    function update_user_otp($user_id, $update)
    {
        if (empty($user_id))
            return false;

        $this->db->where('id', $user_id);
        $this->db->update($this->table, $update);
        return true;
    }

    function get_user_otp($user_id)
    {
        $this->db->where('id', $user_id);
        $this->db->select('user_login_otp,phone,expiry_date,created');
        $q = $this->db->get($this->table);
        $user = $q->row();
        return $user;
    }

    // 🔥 ================== NEW FUNCTIONS FOR GOOGLE LOGIN ==================

    function getUserById($id = false)
    {
        if (!$id)
            return false;

        $this->db->where('id', $id);
        $q = $this->db->get($this->table);

        return $q->row_array();
    }

    function insertUser($data = array())
    {
        if (empty($data))
            return false;

        $data['created'] = date("Y-m-d H:i:s");
        $data['modified'] = date("Y-m-d H:i:s");

        $this->db->insert($this->table, $data);
        return $this->db->insert_id();
    }

    function updateProfile($id = false, $data = array())
    {
        if (!$id || empty($data))
            return false;

        $data['modified'] = date("Y-m-d H:i:s");

        $this->db->where('id', $id);
        $this->db->update($this->table, $data);

        return true;
    }
}