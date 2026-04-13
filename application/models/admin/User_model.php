<?php

class User_model extends MY_model
{
    public function __construct()
    {
        parent::__construct();
        $this->table = 'users';
        $this->channel_table = 'user_channels';
        $this->company_table = 'company_details';
        $this->dulicate_accounts = 'user_duplicate_accounts';
        $this->wallet_history_table = 'wallet_history';
        $this->user_notes_table = 'user_notes';
        $this->legal_entity = 'legal_entity';
        $this->slave = $this->load->database('slave', TRUE);
    }

    function getUserList($account_manager_id = false)
    {
        $this->db->select("users.id,users.fname as user_fname, users.lname as user_lname, users.company_name");
        $this->db->where('parent_id', '0');
        $this->db->where('verified', '1');
        $this->db->order_by("users.fname", "asc");
        if ($account_manager_id)
            $this->db->where('account_manager_id', $account_manager_id);

        $q = $this->db->get($this->table);
        return $q->result();
    }

    function allgetUserList($account_manager_id = false)
    {
        $this->db->select("users.id,users.fname as user_fname, users.lname as user_lname, users.company_name");
        $this->db->where('parent_id', '0');
        //$this->db->where('verified', '1');
        $this->db->order_by("users.fname", "asc");
        $q = $this->db->get($this->table);
        //echo "".$this->db->last_query();
        return $q->result();
    }

    function allgetExportShipmentUserList()
    {
        $this->slave = $this->load->database('slave', TRUE);
        $this->slave->select("users.id,users.fname as user_fname, users.lname as user_lname, users.company_name, users.account_manager_id, users.sale_manager_id, users.international_sale_manager_id, users.b2b_sale_manager_id, users.training_manager_id");
        $q = $this->slave->get($this->table);
        return $q->result();
    }

    function getUserListForInvoice()
    {
        $this->slave = $this->load->database('slave', TRUE);

        $this->slave->select("users.id, users.fname as user_fname, users.lname as user_lname, users.company_name, is_admin");
        $this->slave->where('parent_id', '0');

        $this->slave->order_by("users.id", "asc");

        $q = $this->slave->get($this->table);
        return $q->result();
    }

    function getUserprocessList($account_manager_id = false)
    {
        $this->db->select("users.id,users.fname as user_fname, users.lname as user_lname,users.company_name");
        $this->db->where('parent_id', '0');
        $this->db->where('verified', '0');
        $this->db->order_by("users.fname", "asc");

        if ($account_manager_id)
            $this->db->where('account_manager_id', $account_manager_id);

        $q = $this->db->get($this->table);
        return $q->result();
    }

    function getUserjunkList($account_manager_id = false)
    {
        $this->db->select("users.id,users.fname as user_fname, users.lname as user_lname,users.company_name");
        $this->db->where('parent_id', '0');
        $this->db->where('verified', '2');
        $this->db->order_by("users.fname", "asc");
        if ($account_manager_id)
            $this->db->where('account_manager_id', $account_manager_id);

        $q = $this->db->get($this->table);
        return $q->result();
    }

    function fetchByUserID($limit = 50, $offset = 0, $filter = array())
    {
        $login_user_id = $this->session->userdata('user_info')->user_id;
        $start_date = strtotime(date('Y-m-d') . ' 00:00:00');

        $this->db->select("users.*,warehouse.name,warehouse.address_1,warehouse.address_2,warehouse.city,warehouse.state,
    warehouse.gst_number,warehouse.phone as pickupcell,warehouse.zip,user_channels.channel, group_concat(user_channels.channel_name),user_channels.api_field_1, users.account_manager_id as manager_id, admin_users.fname as manager_fname, admin_users.lname as manager_lname, users.sale_manager_id as sale_id, admin_sales_users.fname as sale_fname, admin_sales_users.lname as sale_lname,interntl_sales_users.fname as int_sale_fname,interntl_sales_users.lname as int_sale_lname");

        if (!empty($filter['start_date'])) {
            $this->db->where("users.created >= '" . $filter['start_date'] . "'");
        }

        if (!empty($filter['end_date'])) {
            $this->db->where("users.created <= '" . $filter['end_date'] . "'");
        }

        if (!empty($filter['id'])) {
            $this->db->where_in('users.id', array_map('intval', $filter['id']));
        }

        if (!empty($filter['email'])) {
            $this->db->where('users.email', $filter['email']);
        }

        if (!empty($filter['phone'])) {
            $this->db->where('users.phone', $filter['phone']);
        }

        if (!empty($filter['manager_id'])) {
            $this->db->where_in('users.account_manager_id', array_map('intval', $filter['manager_id']));
        }

        if (!empty($filter['seller_ID'])) {
            $this->db->where_in('users.id', $filter['seller_ID']);
        }

        if (!empty($filter['account_manager_id'])) {
            $this->db->where('users.account_manager_id', $filter['account_manager_id']);
        }

        if (!empty($filter['sale_manager_id'])) {
            $this->db->where_in('users.sale_manager_id', array_map('intval', $filter['sale_manager_id']));
        }

        if (!empty($filter['international_sale_manager_id'])) {
            $this->db->where_in('users.international_sale_manager_id', array_map('intval', $filter['international_sale_manager_id']));
        }

        if (!empty($filter['pricing_plan'])) {
            $this->db->where('users.pricing_plan', $filter['pricing_plan']);
        }

        if (!empty($filter['international_pricing_plan'])) {
            $this->db->where('users.international_pricing_plan', $filter['international_pricing_plan']);
        }

        if (!empty($filter['cargo_pricing_plan'])) {
            $this->db->where('users.cargo_pricing_plan', $filter['cargo_pricing_plan']);
        }

        if (!empty($filter['support_category'])) {
            $this->db->where_in('users.support_category', $filter['support_category']);
        }

        if (!empty($filter['seller_cluster'])) {
            $this->db->where_in('users.seller_territory', $filter['seller_cluster']);
        }

        if (!empty($filter['lead_source'])) {
            $this->db->where('users.lead_source', $filter['lead_source']);
        }

        if (!empty($filter['service_type'])) {
            if ($filter['service_type'] == 'domestic')
                $this->db->where('users.service_type', '0');
            else if ($filter['service_type'] == 'international')
                $this->db->where('users.service_type', '1');
        }

        if (!empty($filter['tags'])) {
            $this->db->where(" (find_in_set('{$filter['tags']}', users.applied_tags))");
        }

        if (!empty($filter['kyc_done'])) {
            if ($filter['kyc_done'] == 'yes')
                $this->db->where('company_details.companytype !=', '');
            else if ($filter['kyc_done'] == 'e_verified')
                $this->db->where('users.e_verified', '1');
            else
                $this->db->where('company_details.companytype', '');
        }

        if (!empty($filter['recharge_status'])) {
            if ($filter['recharge_status'] == 'yes')
                $this->db->where('users.wallet_balance >', '0');
            else
                $this->db->where('users.wallet_balance', '0');
        }
        if (!empty($filter['status']) && $filter['status'] == 'process') {
            $this->db->where('users.parent_id', '0');
            $this->db->where('users.verified', '0');
        }
        if (!empty($filter['status']) &&  $filter['status'] == 'seller') {
            $this->db->where('users.parent_id !=', '0');
        }
        if (!empty($filter['status']) && $filter['status'] == 'junk') {
            $this->db->where('users.parent_id', '0');
            $this->db->where('users.verified', '2');
        }
        if (!empty($filter['status']) && $filter['status'] == 'active') {
            $this->db->where('users.parent_id', '0');
            $this->db->where('users.verified', '1');
        }
        if (!empty($filter['user_type']) && $filter['user_type'] == 'international') {
            $this->db->join('international_kyc_details', 'international_kyc_details.user_id = users.id', 'INNER');
        }


        $this->db->limit($limit);
        $this->db->offset($offset);
        $this->db->order_by('created', 'desc');
        $this->db->group_by('users.id');
        $this->db->join('warehouse', 'warehouse.user_id = users.id', 'left');
        $this->db->join('user_channels', 'user_channels.user_id = users.id', 'left');
        $this->db->join('company_details', 'company_details.user_id = users.id', 'LEFT');
        //$this->db->join('enable_contact', "enable_contact.view_user_id = users.id AND enable_contact.user_id= $login_user_id AND enable_contact.view_type='user_login' AND  enable_contact.created >= '" . $start_date . "'", 'LEFT');
        $this->db->join("( SELECT    id,fname, lname FROM users where is_admin = '1'  ) admin_users", 'admin_users.id = users.account_manager_id', 'left');
        $this->db->join("( SELECT    id,fname, lname FROM users where is_admin = '1'  ) interntl_sales_users", 'interntl_sales_users.id = users.international_sale_manager_id', 'left');
        $this->db->join("(
        SELECT    id,fname, lname 
            FROM tbl_users
        where is_admin = '1'
    ) admin_sales_users", 'admin_sales_users.id = users.sale_manager_id', 'left');

        $q = $this->db->get($this->table);
        return $q->result();
    }

    function countByUserID($filter = array())
    {
        // pr($filter);exit;
        $this->db->select('count(*) as total');

        if (!empty($filter['start_date'])) {
            $this->db->where("users.created >= '" . $filter['start_date'] . "'");
        }

        if (!empty($filter['end_date'])) {
            $this->db->where("users.created <= '" . $filter['end_date'] . "'");
        }

        if (!empty($filter['id'])) {
            $this->db->where_in('users.id', $filter['id']);
        }

        if (!empty($filter['email'])) {
            $this->db->where('users.email', $filter['email']);
        }

        if (!empty($filter['phone'])) {
            $this->db->where('users.phone', $filter['phone']);
        }

        if (!empty($filter['manager_id'])) {
            $this->db->where_in('users.account_manager_id', array_map('intval', $filter['manager_id']));
        }

        if (!empty($filter['seller_ID'])) {
            $this->db->where_in('users.id', $filter['seller_ID']);
        }

      
        if (!empty($filter['sale_manager_id'])) {
            $this->db->where_in('users.sale_manager_id', array_map('intval', $filter['sale_manager_id']));
        }
        if (!empty($filter['b2b_sale_manager_id'])) {
            $this->db->where_in('users.b2b_sale_manager_id', array_map('intval', $filter['b2b_sale_manager_id']));
        }

        if (!empty($filter['training_manager_id'])) {
            $this->db->where_in('users.training_manager_id', array_map('intval', $filter['training_manager_id']));
        }


        if (!empty($filter['international_sale_manager_id'])) {
            $this->db->where_in('users.international_sale_manager_id', array_map('intval', $filter['international_sale_manager_id']));
        }

        if (!empty($filter['pricing_plan'])) {
            $this->db->where('users.pricing_plan', $filter['pricing_plan']);
        }

        if (!empty($filter['international_pricing_plan'])) {
            $this->db->where('users.international_pricing_plan', $filter['international_pricing_plan']);
        }

        if (!empty($filter['cargo_pricing_plan'])) {
            $this->db->where('users.cargo_pricing_plan', $filter['cargo_pricing_plan']);
        }

        if (!empty($filter['support_category'])) {
            $this->db->where_in('users.support_category', $filter['support_category']);
        }
        if (!empty($filter['seller_cluster'])) {
            $this->db->where_in('users.seller_territory', $filter['seller_cluster']);
        }

        if (!empty($filter['lead_source'])) {
            $this->db->where('users.lead_source', $filter['lead_source']);
        }
        if (!empty($filter['service_type'])) {
            if ($filter['service_type'] == 'domestic')
                $this->db->where('users.service_type', '0');
            else if ($filter['service_type'] == 'international')
                $this->db->where('users.service_type', '1');
        }

        if (!empty($filter['tags'])) {
            $this->db->where(" (FIND_IN_SET('{$filter['tags']}', users.applied_tags))");
        }

        if (!empty($filter['kyc_done'])) {
            if ($filter['kyc_done'] == 'yes')
                $this->db->where('company_details.companytype !=', '');
            else if ($filter['kyc_done'] == 'e_verified')
                $this->db->where('users.e_verified', '1');
            else
                $this->db->where('company_details.companytype', '');
        }

        if (!empty($filter['recharge_status'])) {
            if ($filter['recharge_status'] == 'yes')
                $this->db->where('users.wallet_balance >', '0');
            else
                $this->db->where('users.wallet_balance', '0');
        }
        if (!empty($filter['status']) && $filter['status'] == 'process') {
            $this->db->where('users.parent_id', '0');
            $this->db->where('users.verified', '0');
        }
        if (!empty($filter['status']) &&  $filter['status'] == 'seller') {
            $this->db->where('users.parent_id !=', '0');
        }
        if (!empty($filter['status']) && $filter['status'] == 'junk') {
            $this->db->where('users.parent_id', '0');
            $this->db->where('users.verified', '2');
        }
        if (!empty($filter['status']) && $filter['status'] == 'active') {
            $this->db->where('users.parent_id', '0');
            $this->db->where('users.verified', '1');
        }

        $where_condition = "";
        if (!empty($filter['account_manager_id'])) {
            $where_condition .= "users.account_manager_id = " . $filter['account_manager_id'];
        }

        if (!empty($filter['account_sale_manager_id'])) {
            $where_condition .= " or users.sale_manager_id = " . $filter['account_sale_manager_id'];
        }

        if (!empty($filter['account_training_manager_id'])) {
            $where_condition .= " or users.training_manager_id = " . $filter['account_training_manager_id'];
        }

        if (!empty($where_condition)) {
            $where = '(' . $where_condition . ')';
            $this->db->where($where);
        }
        $this->db->join('company_details', 'company_details.user_id = users.id', 'LEFT');
        $q = $this->db->get($this->table);
        //echo "---->"; pr($this->db->last_query());die;
        return $q->row()->total;
    }

    function fetchByUserIDprocess($limit = 50, $offset = 0, $filter = array())
    {
        $this->db->select("users.*, users.account_manager_id as manager_id, admin_users.fname as manager_fname, admin_users.lname as manager_lname, users.sale_manager_id as sale_id, admin_sales_users.fname as sale_fname, admin_sales_users.lname as sale_lname,interntl_sales_users.fname as int_sale_fname,interntl_sales_users.lname as int_sale_lname");

        if (!empty($filter['start_date'])) {
            $this->db->where("users.created >= '" . $filter['start_date'] . "'");
        }

        if (!empty($filter['end_date'])) {
            $this->db->where("users.created <= '" . $filter['end_date'] . "'");
        }

        if (!empty($filter['id'])) {
            $this->db->where_in('users.id', array_map('intval', $filter['id']));
        }

        if (!empty($filter['email'])) {
            $this->db->where('users.email', $filter['email']);
        }

        if (!empty($filter['kyc_done'])) {
            if ($filter['kyc_done'] == 'yes')
                $this->db->where('company_details.companytype !=', '');
            else
                $this->db->where('company_details.companytype', '');
        }

        if (!empty($filter['recharge_status'])) {
            if ($filter['recharge_status'] == 'yes')
                $this->db->where('wallet_balance >', '0');
            else
                $this->db->where('wallet_balance', '0');
        }

        if (!empty($filter['phone'])) {
            $this->db->where('users.phone', $filter['phone']);
        }

        if (!empty($filter['lead_source'])) {
            $this->db->where('users.lead_source', $filter['lead_source']);
        }

        if (!empty($filter['service_type'])) {
            if ($filter['service_type'] == 'domestic')
                $this->db->where('users.service_type', '0');
            else if ($filter['service_type'] == 'international')
                $this->db->where('users.service_type', '1');
        }

        if (!empty($filter['seller_ID'])) {
            $this->db->where_in('users.id', $filter['seller_ID']);
        }

        if (!empty($filter['account_manager_id'])) {
            $this->db->where('users.account_manager_id', $filter['account_manager_id']);
        }

        if (!empty($filter['manager_id'])) {
            $this->db->where_in('users.account_manager_id', array_map('intval', $filter['manager_id']));
        }

        if (!empty($filter['sale_manager_id'])) {
            $this->db->where_in('users.sale_manager_id', array_map('intval', $filter['sale_manager_id']));
        }
        if (!empty($filter['international_sale_manager_id'])) {
            $this->db->where_in('users.international_sale_manager_id', array_map('intval', $filter['international_sale_manager_id']));
        }

        $this->db->where('users.verified', '0');
        $this->db->where('users.parent_id', '0');
        $this->db->limit($limit);
        $this->db->offset($offset);
        $this->db->order_by('created', 'desc');
        $this->db->join('company_details', 'company_details.user_id = users.id', 'LEFT');
        $this->db->join("(
        SELECT    id,fname, lname 
            FROM tbl_users
        where is_admin = '1'
    ) admin_users", 'admin_users.id = users.account_manager_id', 'left');
        $this->db->join("(
        SELECT    id,fname, lname 
            FROM tbl_users
        where is_admin = '1'
    ) admin_sales_users", 'admin_sales_users.id = users.sale_manager_id', 'left');
        $this->db->join("( SELECT    id,fname, lname FROM users where is_admin = '1'  ) interntl_sales_users", 'interntl_sales_users.id = users.international_sale_manager_id', 'left');
        $q = $this->db->get($this->table);

        return $q->result();
    }

    function countByUserIDprocess($filter = array())
    {
        $this->db->select('count(*) as total');

        if (!empty($filter['start_date'])) {
            $this->db->where("users.created >= '" . $filter['start_date'] . "'");
        }

        if (!empty($filter['end_date'])) {
            $this->db->where("users.created <= '" . $filter['end_date'] . "'");
        }

        if (!empty($filter['id'])) {
            $this->db->where_in('users.id', array_map('intval', $filter['id']));
        }

        if (!empty($filter['email'])) {
            $this->db->where('users.email', $filter['email']);
        }

        if (!empty($filter['phone'])) {
            $this->db->where('users.phone', $filter['phone']);
        }

        if (!empty($filter['lead_source'])) {
            $this->db->where('users.lead_source', $filter['lead_source']);
        }

        if (!empty($filter['service_type'])) {
            if ($filter['service_type'] == 'domestic')
                $this->db->where('users.service_type', '0');
            else if ($filter['service_type'] == 'international')
                $this->db->where('users.service_type', '1');
        }


        if (!empty($filter['kyc_done'])) {
            if ($filter['kyc_done'] == 'yes')
                $this->db->where('company_details.companytype !=', '');
            else
                $this->db->where('company_details.companytype', '');
        }

        if (!empty($filter['recharge_status'])) {
            if ($filter['recharge_status'] == 'yes')
                $this->db->where('wallet_balance >', '0');
            else
                $this->db->where('wallet_balance', '0');
        }

        if (!empty($filter['account_manager_id'])) {
            $this->db->where('users.account_manager_id', $filter['account_manager_id']);
        }

        if (!empty($filter['seller_ID'])) {
            $this->db->where_in('users.id', $filter['seller_ID']);
        }

        if (!empty($filter['manager_id'])) {
            $this->db->where_in('users.account_manager_id', array_map('intval', $filter['manager_id']));
        }

        if (!empty($filter['sale_manager_id'])) {
            $this->db->where_in('users.sale_manager_id', array_map('intval', $filter['sale_manager_id']));
        }

        if (!empty($filter['international_sale_manager_id'])) {
            $this->db->where_in('users.international_sale_manager_id', array_map('intval', $filter['international_sale_manager_id']));
        }

        $this->db->where('users.verified', '0');
        $this->db->where('users.parent_id', '0');
        $this->db->join('company_details', 'company_details.user_id = users.id', 'LEFT');
        $q = $this->db->get($this->table);
        return $q->row()->total;
    }

    function fetchByUserIDemployee($limit = 50, $offset = 0, $filter = array())
    {
        $this->db->select("users.*");

        if (!empty($filter['start_date'])) {
            $this->db->where("users.created >= '" . $filter['start_date'] . "'");
        }
        if (!empty($filter['end_date'])) {
            $this->db->where("users.created <= '" . $filter['end_date'] . "'");
        }
        if (!empty($filter['email'])) {
            $this->db->where('users.email', $filter['email']);
        }

        if (!empty($filter['phone'])) {
            $this->db->where('users.phone', $filter['phone']);
        }

        if (!empty($filter['seller_ID'])) {
            $this->db->where_in('users.id', $filter['seller_ID']);
        }

        if (!empty($filter['account_manager_id'])) {
            $this->db->where('users.account_manager_id', $filter['account_manager_id']);
        }

        $this->db->limit($limit);
        $this->db->offset($offset);
        $this->db->where('users.parent_id !=', '0');
        $this->db->order_by('created', 'desc');
        $this->db->group_by('users.parent_id');
        $this->db->group_by('users.company_name');
        $q = $this->db->get($this->table);
        return $q->result();
    }

    function countByUserIDemployee($filter = array())
    {
        $this->db->select('count(DISTINCT users.parent_id) as total');

        if (!empty($filter['start_date'])) {
            $this->db->where("users.created >= '" . $filter['start_date'] . "'");
        }
        if (!empty($filter['end_date'])) {
            $this->db->where("users.created <= '" . $filter['end_date'] . "'");
        }
        if (!empty($filter['email'])) {
            $this->db->where('users.email', $filter['email']);
        }

        if (!empty($filter['phone'])) {
            $this->db->where('users.phone', $filter['phone']);
        }

        if (!empty($filter['seller_ID'])) {
            $this->db->where_in('users.parent_id', $filter['seller_ID']);
        }

        if (!empty($filter['account_manager_id'])) {
            $this->db->where('users.account_manager_id', $filter['account_manager_id']);
        }

        $this->db->where('users.parent_id !=', '0');
        $this->db->order_by('created', 'desc');
        $q = $this->db->get($this->table);
        return $q->row()->total;
    }

    function getUserWalletBalanceList()
    {
        $this->db->select('id, fname, lname, company_name, wallet_balance');

        $this->db->order_by('wallet_balance', 'desc');
        $q = $this->db->get($this->table);
        return $q->result();
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

    function updateuserverify($exploreids, $updateverify)
    {
        foreach ($exploreids as $id) {
            $this->db->set($updateverify);
            $this->db->where('id', $id);
            $this->db->update($this->table);
        }
    }

    function updateVerifyDate($exploreids = array())
    {
        if (empty($exploreids))
            return false;

        foreach ($exploreids as $id) {
            $this->db->set('verified_date', time());
            $this->db->where('verified_date', '0');
            $this->db->where('id', $id);
            $this->db->update($this->table);
        }
    }

    function updateuserjunk($exploreids, $updatejunk)
    {
        foreach ($exploreids as $id) {
            $this->db->set($updatejunk);
            $this->db->where('id', $id);
            $this->db->update($this->table);
        }
    }

    function sellerremovemanager($exploreids, $removemanager)
    {
        foreach ($exploreids as $id) {
            $this->db->set($removemanager);
            $this->db->where('id', $id);
            $this->db->update($this->table);
        }
    }

    function updateuserprocess($exploreids, $updateprocess)
    {
        foreach ($exploreids as $id) {
            $this->db->set($updateprocess);
            $this->db->where('id', $id);
            $this->db->update($this->table);
        }
    }

    function deleteuser($exploreids)
    {
        $count = 0;
        foreach ($exploreids as $id) {
            $this->db->where('id', $id);
            $this->db->delete($this->table);
            $this->db->where('user_id', $id);
            $this->db->delete('warehouse');
            $count = $count + 1;
        }
        echo '<div class="alert alert-success" style="font-weight:bold">' . $count . ' Users Deleted successfully</div>';
        $count = 0;
    }

    function singleuserview($id)
    {
        $this->db->select("users.*,users.id as sellerid,company_details.*,referral_rate.*,users.account_manager_id as manager_id, admin_users.fname as manager_fname, admin_users.lname as manager_lname, users.sale_manager_id as sale_id, admin_sales_users.fname as sale_fname, admin_sales_users.lname as sale_lname,traning_manager.fname as training_fname , traning_manager.lname as training_lname,account_master_id");
        $this->db->where('users.id', $id);
        //$this->db->join('warehouse', 'warehouse.user_id = users.id', 'left');
        $this->db->join('company_details', 'company_details.user_id = users.id', 'left');
        $this->db->join('referral_rate', 'referral_rate.user_id = users.id', 'left');
        $this->db->join("(
        SELECT    id,fname, lname 
            FROM    tbl_users
        where is_admin = '1'
    ) admin_users", 'admin_users.id = users.account_manager_id', 'left');
        $this->db->join("(
        SELECT    id,fname, lname 
            FROM      tbl_users
        where is_admin = '1'
    ) admin_sales_users", 'admin_sales_users.id = users.sale_manager_id', 'left');
        $this->db->join("( SELECT id,fname, lname FROM tbl_users where is_admin = '1'  ) traning_manager", 'traning_manager.id = users.training_manager_id', 'left');
        $q = $this->db->get($this->table);
        return $q->result();
    }

    function total_remittance($id)
    {
        $this->db->select("remittance.*");
        $this->db->where('remittance.user_id', $id);
        $this->db->order_by('created', 'desc');
        $q = $this->db->get('remittance');
        return $q->result();
    }

    function userchannelview($id)
    {
        $this->db->select("user_channels.*");
        $this->db->where('user_channels.user_id', $id);
        $q = $this->db->get($this->channel_table);
        return $q->result();
    }

    function sellerwarehouse($id)
    {
        $this->db->select("warehouse.*");
        $this->db->where('warehouse.user_id', $id);
        $this->db->limit(100);
        $q = $this->db->get("warehouse");
        return $q->result();
    }

    function selleremployeeview($id)
    {
        $this->db->select("users.*");
        $this->db->where('users.parent_id', $id);
        $q = $this->db->get($this->table);
        return $q->result();
    }

    function sellercompanyview($id)
    {
        $this->db->select("company_details.*");
        $this->db->where('company_details.user_id', $id);
        $q = $this->db->get($this->company_table);
        return $q->result();
    }

    function sellerbankview($id)
    {
        $this->db->select("company_details.*");
        $this->db->where('company_details.user_id', $id);
        $q = $this->db->get($this->company_table);
        return $q->result();
    }

    function sellerrechargelogview($id)
    {
        $this->db->select('wallet_history.*');
        $this->db->where('wallet_history.user_id', $id);
        $this->db->where('wallet_history.txn_for !=', 'shipment');
        $this->db->limit('20');
        $this->db->order_by('wallet_history.created', 'desc');
        $q = $this->db->get($this->wallet_history_table);
        return $q->result();
    }

    function change_pricing_plan($id, $change_data)
    {
        $this->db->set($change_data);
        $this->db->where('id', $id);
        return $this->db->update($this->table);
    }

    function add_wallet_balance($id, $data)
    {
        $this->db->set($data);
        $this->db->where('id', $id);
        return $this->db->update($this->table);
    }

    function fetchByUserIDjunk($limit = 50, $offset = 0, $filter = array())
    {
        $this->db->select("users.*,warehouse.name,warehouse.address_1,warehouse.address_2,warehouse.city,warehouse.state,warehouse.gst_number,warehouse.phone as pickupcell,warehouse.zip,user_channels.channel,user_channels.channel_name,user_channels.	api_field_1");

        if (!empty($filter['start_date'])) {
            $this->db->where("users.created >= '" . $filter['start_date'] . "'");
        }

        if (!empty($filter['end_date'])) {
            $this->db->where("users.created <= '" . $filter['end_date'] . "'");
        }

        if (!empty($filter['id'])) {
            $this->db->where('users.id', $filter['id']);
        }

        if (!empty($filter['email'])) {
            $this->db->where('users.email', $filter['email']);
        }

        if (!empty($filter['phone'])) {
            $this->db->where('users.phone', $filter['phone']);
        }

        if (!empty($filter['seller_ID'])) {
            $this->db->where_in('users.id', $filter['seller_ID']);
        }

        if (!empty($filter['account_manager_id'])) {
            $this->db->where('users.account_manager_id', $filter['account_manager_id']);
        }

        $this->db->where('users.verified', '2');
        $this->db->where('users.parent_id', '0');
        $this->db->limit($limit);
        $this->db->offset($offset);
        $this->db->order_by('created', 'desc');
        $this->db->group_by('users.id');
        $this->db->join('warehouse', 'warehouse.user_id = users.id', 'left');
        $this->db->join('user_channels', 'user_channels.user_id = users.id', 'left');
        $q = $this->db->get($this->table);
        return $q->result();
    }

    function countByUserIDjunk($filter = array())
    {
        $this->db->select('count(*) as total');

        if (!empty($filter['start_date'])) {
            $this->db->where("users.created >= '" . $filter['start_date'] . "'");
        }

        if (!empty($filter['end_date'])) {
            $this->db->where("users.created <= '" . $filter['end_date'] . "'");
        }

        if (!empty($filter['id'])) {
            $this->db->where('users.id', $filter['id']);
        }

        if (!empty($filter['email'])) {
            $this->db->where('users.email', $filter['email']);
        }

        if (!empty($filter['phone'])) {
            $this->db->where('users.phone', $filter['phone']);
        }

        $where_condition = "";
        if (!empty($filter['account_manager_id'])) {
            $where_condition .= "users.account_manager_id = " . $filter['account_manager_id'];
        }

        if (!empty($filter['sale_manager_id_1'])) {
            $where_condition .= " or users.sale_manager_id = " . $filter['sale_manager_id_1'];
        }

        if (!empty($filter['international_sale_manager_id_1'])) {
            $where_condition .= " or users.international_sale_manager_id = " . $filter['international_sale_manager_id_1'];
        }

        if (!empty($where_condition)) {
            $where = '(' . $where_condition . ')';
            $this->db->where($where);
        }

        $this->db->where('users.verified', '2');
        $this->db->where('users.parent_id', '0');
        $this->db->join('warehouse', 'warehouse.user_id = users.id', 'left');
        $this->db->join('user_channels', 'user_channels.user_id = users.id', 'left');
        $q = $this->db->get($this->table);
        return $q->row()->total;
    }

    function referral_name($referral_id)
    {
        $this->db->select("users.fname as referral_fname, users.lname as referral_lname,users.company_name as referral_company_name");
        $this->db->where('users.id', $referral_id);
        $q = $this->db->get($this->table);
        return $q->result();
    }

    function change_user_referral($id, $change_user_data)
    {
        $this->db->where('id', $id);
        $this->db->update($this->table, $change_user_data);
        return true;
    }

    function add_user_referral($add_refferal_price)
    {
        $this->db->insert('referral_rate', $add_refferal_price);
        return $this->db->insert_id();
    }

    function update_user_referral_price($id, $update_refferal_price)
    {
        $this->db->where('user_id', $id);
        $this->db->update('referral_rate', $update_refferal_price);
        return true;
    }

    function disable_user_referral($id, $change_user_data)
    {
        $this->db->where('id', $id);
        $this->db->update($this->table, $change_user_data);
        return true;
    }

    function enable_user_referral($id, $change_user_data)
    {
        $this->db->where('id', $id);
        $this->db->update($this->table, $change_user_data);
        return true;
    }

    function update_seller_details($id, $edit_seller_info)
    {
        $this->db->where('id', $id);
        $this->db->update('users', $edit_seller_info);
        return true;
    }

    function update_seller_personal_details($id, $edit_personal_data)
    {
        $this->db->where('user_id', $id);
        $this->db->update($this->company_table, $edit_personal_data);
        return true;
    }

    function checksellerrecord($userid = false)
    {
        if (!$userid)
            return false;
        $this->db->select("users.*");
        $this->db->where('users.id', $userid);
        $q = $this->db->get('users');
        return $q->row();
    }

    function checkcmprecord($userid = false)
    {
        if (!$userid)
            return false;
        $this->db->select("company_details.*");
        $this->db->where('company_details.user_id', $userid);
        $q = $this->db->get('company_details');
        return $q->row();
    }

    function insert_personal($insert_data)
    {
        return $this->db->insert('company_details', $insert_data);
    }

    function update_sellercmpbank($id, $updateaccountdata)
    {
        if (empty($id))
            return false;
        $this->db->set($updateaccountdata);
        $this->db->where('user_id', $id);
        return $this->db->update('company_details');
    }

    function insert_cmpbankdetails($insertaccountdata)
    {
        return $this->db->insert("company_details", $insertaccountdata);
    }

    function insert_kycdetails($kycdata)
    {
        return $this->db->insert("company_details", $kycdata);
    }

    function update_kycdetails($id, $kycdata)
    {
        if (empty($id))
            return false;
        $this->db->set($kycdata);
        $this->db->where('user_id', $id);
        return $this->db->update('company_details');
    }

    function change_freeze_plan($id, $freeze_save)
    {
        $this->db->where('id', $id);
        $this->db->update($this->table, $freeze_save);
        return true;
    }

    function allUsers()
    {
        return $this->db->get_where('users', array('parent_id' => '0'))->result();
    }

    function getAdminUsers()
    {
        return $this->db->order_by('fname', 'asc')->get_where('users', array('is_admin' => '1'))->result();
    }

    function assignManager($user_ids = false, $manager_id = false)
    {
        if (empty($user_ids) || !$manager_id)
            return false;

        $this->db->where_in('id', $user_ids);
        $this->db->set('account_manager_id', $manager_id);

        $this->db->update($this->table);

        return true;
    }

    function assignSales($user_ids = false, $sale_id = false)
    {
        if (empty($user_ids) || !$sale_id)
            return false;
        $this->db->where_in('id', $user_ids);
        $this->db->set('sale_manager_id', $sale_id);
        $this->db->update($this->table);
        return true;
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
            $this->db->set('remittance_on_hold_amount', "GREATEST(round(remittance_on_hold_amount-{$amount},2),0)", FALSE);

        $this->db->update($this->table);
        return true;
    }

    function fetchByUserIDAllExports($filter = array())
    {
        $this->db->select("`tbl_users`.`id`, `tbl_users`.`fname`, `tbl_users`.`lname`, `tbl_users`.`company_name`, `tbl_users`.`email`, `tbl_users`.`phone`, `tbl_users`.`created`, `tbl_users`.`remittance_cycle`, `tbl_users`.`remittance_on_hold_amount`, `tbl_users`.`leadsquared_id`, `tbl_users`.`verified`, `tbl_users`.`e_verified`,`tbl_users`.`wallet_limit`, `tbl_users`.`wallet_balance`, `tbl_users`.`pricing_plan`, `tbl_users`.`verified_date`, `tbl_users`.`lead_source`, `tbl_users`.`parent_id`, `tbl_users`.`projected_shipments`, `tbl_users`.`ndr_action_type`, `tbl_users`.`support_category`, `tbl_users`.`freeze_remittance`, `tbl_company_details`.`cmp_url`, `tbl_company_details`.`cmp_email`, `tbl_company_details`.`cmp_phone`, `tbl_company_details`.`cmp_pan`, `tbl_company_details`.`cmp_gstno`, `tbl_company_details`.`cmp_address`, `tbl_company_details`.`cmp_city`, `tbl_company_details`.`cmp_state`, `tbl_company_details`.`cmp_pincode`, `tbl_company_details`.`cmp_accntholder`, `tbl_company_details`.`cmp_accno`, `tbl_company_details`.`cmp_acctype`, `tbl_company_details`.`cmp_bankname`, `tbl_company_details`.`cmp_bankbranch`, `tbl_company_details`.`cmp_accifsc`, `tbl_company_details`.`companytype`, `tbl_company_details`.`document_type`, `tbl_company_details`.`kycdoc_id`, `tbl_company_details`.`kycdoc_name`, `tbl_company_details`.`cmppanno`, `tbl_company_details`.`cmppanname`, `tbl_company_details`.`agreement_status`, `tbl_users`.`support_category`");

        if (!empty($filter['start_date'])) {
            $this->db->where("users.created >= '" . $filter['start_date'] . "'");
        }

        if (!empty($filter['end_date'])) {
            $this->db->where("users.created <= '" . $filter['end_date'] . "'");
        }

        if (!empty($filter['id'])) {
            $this->db->where_in('users.id', array_map('intval', $filter['id']));
        }

        if (!empty($filter['email'])) {
            $this->db->where('users.email', $filter['email']);
        }

        if (!empty($filter['phone'])) {
            $this->db->where('users.phone', $filter['phone']);
        }

        if (!empty($filter['manager_id'])) {
            $this->db->where_in('users.account_manager_id', array_map('intval', $filter['manager_id']));
        }

        if (!empty($filter['seller_ID'])) {
            $this->db->where_in('users.id', $filter['seller_ID']);
        }

        if (!empty($filter['seller_cluster'])) {
            $this->db->where_in('users.seller_territory', $filter['seller_cluster']);
        }

        $where_condition = "";
        if (!empty($filter['account_manager_id'])) {
            $where_condition .= "users.account_manager_id = " . $filter['account_manager_id'];
        }

        if (!empty($filter['sale_manager_id_1'])) {
            $where_condition .= " or users.sale_manager_id = " . $filter['sale_manager_id_1'];
        }
        
        if (!empty($filter['training_manager_id'])) {
            $this->db->where_in('users.training_manager_id', array_map('intval', $filter['training_manager_id']));
        }

        if (!empty($filter['training_manager_id'])) {
            $where_condition .= " or users.training_manager_id = " . $filter['training_manager_id'];
        }

        if (!empty($where_condition)) {
            $where = '(' . $where_condition . ')';
            $this->db->where($where);
        }

        if (!empty($filter['sale_manager_id'])) {
            $this->db->where_in('users.sale_manager_id', array_map('intval', $filter['sale_manager_id']));
        }

        if (!empty($filter['pricing_plan'])) {
            $this->db->where('users.pricing_plan', $filter['pricing_plan']);
        }

        if (!empty($filter['support_category'])) {
            $this->db->where_in('users.support_category', $filter['support_category']);
        }

        if (!empty($filter['lead_source'])) {
            $this->db->where('users.lead_source', $filter['lead_source']);
        }

        if (!empty($filter['service_type'])) {
            if ($filter['service_type'] == 'domestic')
                $this->db->where('users.service_type', '0');
        }

        if (!empty($filter['tags'])) {
            $this->db->where(" (find_in_set('{$filter['tags']}', users.applied_tags))");
        }

        if (!empty($filter['kyc_done'])) {
            if ($filter['kyc_done'] == 'yes')
                $this->db->where('company_details.companytype !=', '');
            else if ($filter['kyc_done'] == 'e_verified')
                $this->db->where('users.e_verified', '1');
            else
                $this->db->where('company_details.companytype', '');
        }

        if (!empty($filter['recharge_status'])) {
            if ($filter['recharge_status'] == 'yes')
                $this->db->where('users.wallet_balance >', '0');
            else
                $this->db->where('users.wallet_balance', '0');
        }
        if (!empty($filter['status']) && $filter['status'] == 'process') {
            $this->db->where('users.parent_id', '0');
            $this->db->where('users.verified', '0');
        }
        if (!empty($filter['status']) &&  $filter['status'] == 'seller') {
            $this->db->where('users.parent_id !=', '0');
        }
        if (!empty($filter['status']) && $filter['status'] == 'junk') {
            $this->db->where('users.parent_id', '0');
            $this->db->where('users.verified', '2');
        }
        if (!empty($filter['status']) && $filter['status'] == 'active') {
            $this->db->where('users.parent_id', '0');
            $this->db->where('users.verified', '1');
        }

        if (!empty($filter['status']) && $filter['status'] == 'parent_sellers') {
            $this->db->where('users.parent_id', '0');
        }


        $this->db->order_by('created', 'desc');
        $this->db->group_by('users.id');
        $this->db->join('company_details', 'company_details.user_id = users.id', 'left');
        $this->db->join("(
        SELECT    id,fname, lname 
            FROM      tbl_users
        where is_admin = '1'
    ) admin_users", 'admin_users.id = users.account_manager_id', 'left');
        $this->db->join("(
        SELECT    id,fname, lname 
            FROM  tbl_users
        where is_admin = '1'
    ) admin_sales_users", 'admin_sales_users.id = users.sale_manager_id', 'left');
        $this->db->join("( SELECT id,fname, lname FROM tbl_users where is_admin = '1'  ) traning_manager", 'traning_manager.id = users.training_manager_id', 'left');
        $this->db->from($this->table);
        
        return $query = $this->db->get_compiled_select();
    }

    function getUsersNotes($id)
    {
        $limit = 1;
        $this->db->select("user_notes.*,users.fname, users.lname");
        $this->db->where('user_notes.user_id', $id);
        $this->db->limit($limit);
        $this->db->order_by('user_notes.created', 'desc');
        $this->db->join('users', 'user_notes.by_user_id = users.id', 'inner');
        $q = $this->db->get($this->user_notes_table);
        return $q->row();
    }

    function fetchByUserIDAllrecords($limit = 50, $offset = 0, $filter = array())
    {
        $this->db->select("users.*,warehouse.name,warehouse.address_1,warehouse.address_2,warehouse.city,warehouse.state,
    warehouse.gst_number,warehouse.phone as pickupcell,warehouse.zip,user_channels.channel, group_concat(user_channels.channel_name),user_channels.api_field_1, company_details.*");

        if (!empty($filter['start_date'])) {
            $this->db->where("users.created >= '" . $filter['start_date'] . "'");
        }

        if (!empty($filter['end_date'])) {
            $this->db->where("users.created <= '" . $filter['end_date'] . "'");
        }

        if (!empty($filter['id'])) {
            $this->db->where('users.id', $filter['id']);
        }

        if (!empty($filter['email'])) {
            $this->db->where('users.email', $filter['email']);
        }

        if (!empty($filter['phone'])) {
            $this->db->where('users.phone', $filter['phone']);
        }

        if (!empty($filter['account_manager_id'])) {
            $this->db->where('users.account_manager_id', $filter['account_manager_id']);
        }

        if (!empty($filter['seller_ID'])) {
            $this->db->where_in('users.id', $filter['seller_ID']);
        }

        $this->db->where('users.verified', '1');
        $this->db->limit($limit);
        $this->db->offset($offset);
        $this->db->order_by('created', 'desc');
        $this->db->group_by('users.id');
        $this->db->join('warehouse', 'warehouse.user_id = users.id', 'left');
        $this->db->join('user_channels', 'user_channels.user_id = users.id', 'left');
        $this->db->join('company_details', 'company_details.user_id = users.id', 'left');
        $q = $this->db->get($this->table);
        return $q->result();
    }

    function isEmailExist($selleremail)
    {
        $this->db->select('id');
        $this->db->where('email', $selleremail);
        $query = $this->db->get('users');
        if ($query->num_rows() > 0) {
            return true;
        } else {
            return false;
        }
    }
    function getSellerList($query = false, $status = false)
    {
        $this->db->select("users.id,users.fname as user_fname, users.lname as user_lname,users.email,users.company_name");
        $this->db->where('parent_id', '0');

        if (!empty($status)) {
            $this->db->where('verified', '1');
        }

        $this->db->group_start();
        $this->db->where("CONCAT_WS(' ',fname,lname) LIKE '" . $query . "%'", NULL, FALSE);
        $this->db->or_like('users.company_name', $query);
        $this->db->or_like('users.id', $query);
        $this->db->group_end();
        $this->db->group_by('users.id');
        $this->db->order_by("users.fname", "asc");
        $q = $this->db->get($this->table);
        return $q->result();
    }

    function getAllsUserList()
    {
        $this->db->select("users.id,users.fname as user_fname, users.lname as user_lname,users.email,users.company_name");
        $this->db->where('verified', '1');
        $this->db->order_by("users.fname", "asc");
        $q = $this->db->get($this->table);
        return $q->result();
    }

    function getUserListFilter($user_id = false)
    {
        $this->db->select("users.id,users.fname as user_fname, users.lname as user_lname,users.company_name");
        $this->db->where('parent_id', '0');
        if (!empty($user_id)) {
            $this->db->where_in('users.id', $user_id);
        }
        $this->db->group_by('users.id');
        $this->db->order_by("users.fname", "asc");
        $q = $this->db->get($this->table);
        return $q->result();
    }

    function insert_notesdetails($insertaccountdata)
    {
        return $this->db->insert($this->user_notes_table, $insertaccountdata);
    }

    function getNotes($id)
    {
        $this->db->select("user_notes.*,users.fname, users.lname");
        $this->db->where('user_notes.user_id', $id);
        $this->db->order_by('user_notes.created', 'desc');
        $this->db->join('users', 'user_notes.by_user_id = users.id', 'inner');
        $q = $this->db->get($this->user_notes_table);
        return $q->result();
    }

    function insertreferrallink($id, $refid)
    {
        $this->db->where('id', $id);
        $this->db->set('referral_id', $refid);
        $this->db->update($this->table);
        return true;
    }

    function sellerplansheet()
    {
        $this->db->select("pricing_plans.id, pricing_plans.plan_name");
        $q = $this->db->get('pricing_plans');
        return $q->result();
    }

    function getseller()
    {
        $this->db->select("users.pricing_plan");
        $q = $this->db->get($this->table);
        return $q->row();
    }

    function countByDuplicateUser($filter = array())
    {
        $this->db->select("count(distinct(t.duplicate_ids)) as total");
        $this->db->join("(SELECT user_id, 'pan card' as duplicate_type, cmp_pan as duplicate_number, GROUP_CONCAT(user_id order by user_id asc) as duplicate_ids FROM `company_details` where cmp_pan != '' group by cmp_pan having count(*) >1
    UNION
    SELECT user_id, 'gst number' as duplicate_type, cmp_gstno as duplicate_number, GROUP_CONCAT(user_id order by user_id asc) as duplicate_ids FROM `company_details` where cmp_gstno != '' group by cmp_gstno having count(*) >1
    UNION 
    SELECT user_id, 'company phone' as duplicate_type, cmp_phone as duplicate_number, GROUP_CONCAT(user_id order by user_id asc) as duplicate_ids FROM `company_details` where cmp_phone != '' group by cmp_phone having count(*) >1
    UNION 
    SELECT user_id, 'company email' as duplicate_type, cmp_email as duplicate_number, GROUP_CONCAT(user_id order by user_id asc) as duplicate_ids FROM `company_details` where cmp_email != '' group by cmp_email having count(*) >1
    UNION 
    SELECT user_id, 'company Account' as duplicate_type, cmp_accno as duplicate_number, GROUP_CONCAT(user_id order by user_id asc) as duplicate_ids FROM `company_details` where cmp_accno != '' group by cmp_accno having count(*) >1
    ) t", "t.user_id = company_details.user_id", "INNER");
        $this->db->join('users', 'users.id = company_details.user_id', 'inner');
        $this->db->where('users.parent_id', '0');
        $q = $this->db->get($this->company_table);
        return $q->row()->total;
    }

    function fetchByDuplicateUser($limit = 50, $offset = 0, $filter = array())
    {
        $this->db->select("company_details.user_id, GROUP_CONCAT(t.duplicate_type) as duplicate_type, t.duplicate_number, t.duplicate_ids,users.company_name as  company_name,CONCAT_WS(' ',users.fname,users.lname) as name, t.total");
        $this->db->join("(SELECT user_id, 'pan card' as duplicate_type, cmp_pan as duplicate_number, GROUP_CONCAT(user_id order by user_id asc) as duplicate_ids, count(*) as total FROM `company_details` where cmp_pan != '' group by cmp_pan having count(*) >1
    UNION
    SELECT user_id, 'gst number' as duplicate_type, cmp_gstno as duplicate_number, GROUP_CONCAT(user_id order by user_id asc) as duplicate_ids , count(*) as total FROM `company_details` where cmp_gstno != '' group by cmp_gstno having count(*) >1
    UNION 
    SELECT user_id, 'company phone' as duplicate_type, cmp_phone as duplicate_number, GROUP_CONCAT(user_id order by user_id asc) as duplicate_ids , count(*) as total FROM `company_details` where cmp_phone != '' group by cmp_phone having count(*) >1
    UNION 
    SELECT user_id, 'company email' as duplicate_type, cmp_email as duplicate_number, GROUP_CONCAT(user_id order by user_id asc) as duplicate_ids , count(*) as total FROM `company_details` where cmp_email != '' group by cmp_email having count(*) >1
    UNION 
    SELECT user_id, 'company Bank Account' as duplicate_type, cmp_accno as duplicate_number, GROUP_CONCAT(user_id order by user_id asc) as duplicate_ids , count(*) as total FROM `company_details` where cmp_accno != '' group by cmp_accno having count(*) >1
    ) t", "t.user_id = company_details.user_id", "INNER");
        $this->db->join('users', 'users.id = company_details.user_id', 'inner');
        $this->db->where('users.parent_id', '0');
        $this->db->limit($limit);
        $this->db->offset($offset);
        $this->db->group_by('t.duplicate_ids');
        $this->db->order_by("company_details.user_id,t.total", "DESC");
        $q = $this->db->get($this->company_table);
        return $q->result();
    }

    function getalllead()
    {
        $this->db->select("users.lead_source");
        $this->db->where('users.lead_source !=', '');
        $this->db->group_by('users.lead_source');
        $q = $this->db->get('users');
        return $q->result();
    }

    function fetchByUserWalletExports($filter = array())
    {
        $this->db->select("users.id,users.fname,users.lname,users.company_name,users.email,users.phone,users.created,users.remittance_cycle,users.leadsquared_id,users.verified,users.wallet_limit,users.wallet_balance,users.pricing_plan,users.verified_date,users.lead_source,users.parent_id,users.projected_shipments,users.ndr_action_type,users.support_category,company_details.cmp_url,company_details.cmp_email,company_details.cmp_phone,company_details.cmp_pan,company_details.cmp_gstno,company_details.cmp_address,company_details.cmp_city,company_details.cmp_state,company_details.cmp_pincode,company_details.cmp_accntholder,company_details.cmp_accno,company_details.cmp_acctype,company_details.cmp_bankname,company_details.cmp_bankbranch,company_details.cmp_accifsc,company_details.companytype,company_details.document_type,company_details.kycdoc_id,company_details.kycdoc_name,company_details.cmppanno,company_details.cmppanname,company_details.agreement_status,users.support_category,admin_users.fname as manager_fname,admin_users.lname as manager_lname,admin_sales_users.fname as sale_fname,admin_sales_users.lname as sale_lname,users.is_postpaid");
        if (!empty($filter['status']) && $filter['status'] == 'parent_sellers') {
            $this->db->where('users.parent_id', '0');
        }
        $orderby = 'convert(users.wallet_balance, decimal) desc';
        $this->db->order_by($orderby);
        $this->db->group_by('users.id');
        $this->db->join('company_details', 'company_details.user_id = users.id', 'left');
        $this->db->join("(
        SELECT    id,fname, lname 
            FROM tbl_users
        where is_admin = '1'
    ) admin_users", 'admin_users.id = users.account_manager_id', 'left');
        $this->db->join("(
        SELECT    id,fname, lname 
            FROM tbl_users
        where is_admin = '1'
    ) admin_sales_users", 'admin_sales_users.id = users.sale_manager_id', 'left');
        $this->db->from($this->table);
        return $query = $this->db->get_compiled_select();
    }

    function getUserTags($id)
    {
        $this->db->select("users.applied_tags");
        $this->db->where('users.id', $id);
        $q = $this->db->get($this->table);
        return $q->row();
    }

    function getUserPermissiontype($id)
    {
        $this->db->select("users.id,users.admin_permission_level");
        $this->db->where('users.id', $id);
        $q = $this->db->get($this->table);
        return $q->row();
    }

    function fetchDuplicateUser($sellerid = array(), $cmp_pan = false, $cmp_accno = false, $email = array(), $phone = array(), $gstno = array())
    {
        $this->db->select("users.id,users.fname,users.lname,users.company_name,users.created,users.verified,
        users.wallet_balance,users.wallet_limit,company_details.cmp_gstno,company_details.cmp_email,company_details.cmp_accno,
        company_details.cmp_pan,company_details.cmp_phone,
        admin_users.fname as manager_fname, admin_users.lname as manager_lname,
        users.sale_manager_id as sale_id,
        admin_sales_users.fname as sale_fname, admin_sales_users.lname as sale_lname");

        if (!empty($gstno)) {

            $this->db->select("if(`tbl_company_details`.`cmp_gstno` IN('" . implode("','", $gstno) . "'),1,0) as 'cmp_gstno'");
            $this->db->select("if(`tbl_warehouse`.`gst_number` IN('" . implode("','", $gstno) . "'),1,0) as 'w_gstno'");
            $this->db->or_where_in('company_details.cmp_gstno', $gstno);
            $this->db->or_where_in('warehouse.gst_number', $gstno);
        }
        if (!empty($email)) {
            $this->db->select("if(`tbl_company_details`.`cmp_email` IN('" . implode("','", $email) . "'),1,0) as 'cmp_email'");
            $this->db->select("IF(`tbl_warehouse`.`email` IN ('" . implode("','", array_map([$this->db, 'escape_str'], $email)) . "'), 1, 0) as 'w_email'");
            $this->db->or_where_in('company_details.cmp_email', $email);
            $this->db->or_where_in('warehouse.email', $email);
        }
        if (!empty($phone)) {
            $this->db->select("if(`tbl_company_details`.`cmp_phone` IN('" . implode("','", $phone) . "'),1,0) as 'c_phone'");
            $this->db->select("if(`tbl_warehouse`.`phone` IN('" . implode("','", $phone) . "'),1,0) as 'w_phone'");
            $this->db->or_where_in('company_details.cmp_phone', $phone);
            $this->db->or_where_in('warehouse.phone', $phone);
        }

        if (!empty($cmp_pan)) {
            $this->db->select("if(`tbl_company_details`.`cmp_pan` IN('" . $cmp_pan . "'),1,0) as 'pan_card'");
            $this->db->or_where_in('company_details.cmp_pan', $cmp_pan);
        }
        if (!empty($cmp_accno)) {
            $this->db->select("if(`tbl_company_details`.`cmp_accno` IN('" . $cmp_accno . "'),1,0) as 'cmp_accno'");
            $this->db->or_where_in('company_details.cmp_accno', $cmp_accno);
        }

        $this->db->where('users.parent_id', '0');
        $this->db->where('users.is_admin', '0');
        $this->db->order_by('users.created', 'desc');
        $this->db->group_by('users.id');
        $this->db->join("company_details", "company_details.user_id = users.id and users.id NOT IN('" . implode("','", $sellerid) . "')", "LEFT");
        $this->db->join("warehouse", "warehouse.user_id = users.id and warehouse.user_id NOT IN('" . implode("','", $sellerid) . "')", "LEFT");
        $this->db->join("(
            SELECT    id,fname, lname 
                FROM tbl_users
            where is_admin = '1'
        ) admin_users", 'admin_users.id = users.account_manager_id', 'left');
        $this->db->join("(
            SELECT    id,fname, lname 
                FROM tbl_users
            where is_admin = '1'
        ) admin_sales_users", 'admin_sales_users.id = users.sale_manager_id', 'left');
        $q = $this->db->get($this->table);
        return $q->result();
    }
    function ndr_call_seller_set($sid = '', $actionby = '')
    {
        if ($sid != '') {
            $this->db->select('id,is_active,last_modify_by');
            $this->db->where('user_id', $sid);
            $this->db->limit('1');
            $result = $this->db->get('seller_ndr_calling')->result();

            if ($result) {
                foreach ($result as $res) {
                    $active = 0;

                    if ($res->is_active == 0) {
                        $active = 1;
                    }

                    $update_data = array(
                        'is_active' => $active,
                        'last_modify_by' => $actionby,
                        'modified' => strtotime(gmdate('Y-m-d H:i:s')),
                    );
                    $this->db->where('id', $res->id);
                    $this->db->update('seller_ndr_calling', $update_data);
                    return "udpated";
                }
            } else {
                $insert_data = array(
                    'user_id' => $sid,
                    'is_active' => '1',
                    'last_modify_by' => $actionby,
                    'created' => strtotime(gmdate('Y-m-d H:i:s')),
                    'modified' => strtotime(gmdate('Y-m-d H:i:s'))
                );
                $this->db->insert('seller_ndr_calling', $insert_data);

                return "added";
            }
        }

        return false;
    }

    function get_seller_ndrcall_status($sellerid)
    {
        if ($sellerid != '') {
            $this->db->select('id,is_active,last_modify_by');
            $this->db->where('user_id', $sellerid);
            $this->db->limit('1');
            $result = $this->db->get('seller_ndr_calling')->result();
            return $result;
        }

        return false;
    }

    function getAllUserList()
    {
        $this->db->select("users.id,users.fname as user_fname, users.lname as user_lname, users.company_name");
        $this->db->where('parent_id', '0');
        $this->db->order_by("users.fname", "asc");
        $q = $this->db->get($this->table);
        return $q->result();
    }

    function getUserListRules($user_id = false)
    {
        $this->db->select("users.id,users.fname as user_fname, users.lname as user_lname,users.company_name");
        $this->db->where('parent_id', '0');
        $this->db->where('verified', '1');
        if (!empty($user_id)) {
            $this->db->where_in('users.id', $user_id);
        }
        $this->db->group_by('users.id');
        $this->db->order_by("users.fname", "asc");
        $q = $this->db->get($this->table);
        return $q->result();
    }

    function insert_tat_weight($data)
    {
        return $this->db->insert("weight_dispute_time_limit", $data);
    }

    function get_dispute_time_limit($user_id = false)
    {
        if (empty($user_id))
            return false;
        $this->db->select('*');
        $this->db->where('user_id', $user_id);
        $this->db->from('weight_dispute_time_limit');
        $q = $this->db->get();
        return $q->row();
    }

    function update_tat_weight($id = false, $save = array())
    {
        $this->db->where('id', $id);
        $this->db->update('weight_dispute_time_limit', $save);
        return true;
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
    function is_view_enable($user_id = false, $view_id = false, $type = false)
    {
        if (empty($user_id) || empty($view_id) || empty($type))
            return false;
        $start_date = strtotime(date('Y-m-d') . ' 00:00:00');
        //$end_date = strtotime(date('Y-m-d'). ' 23:59:59');
        $this->db->select('*');
        $this->db->where("created >= '" . $start_date . "'");
        //$this->db->where("created <= '" . $end_date . "'");
        $this->db->where('view_user_id', $view_id);
        $this->db->where('view_type', strtolower($type));
        $this->db->where('user_id', $user_id);
        $this->db->from('enable_contact');
        $q = $this->db->get();
        return $q->row();
    }
    function is_view_enable_all($user_id = false, $view_id = false)
    {
        if (empty($user_id) || empty($view_id))
            return false;
        $start_date = strtotime(date('Y-m-d') . ' 00:00:00');
        //$end_date = strtotime(date('Y-m-d'). ' 23:59:59');
        $this->db->select('*');
        $this->db->where("created >= '" . $start_date . "'");
        //$this->db->where("created <= '" . $end_date . "'");
        $this->db->where('view_user_id', $view_id);
        $this->db->where('user_id', $user_id);
        $this->db->from('enable_contact');
        $q = $this->db->get();
        return $q->result();
    }

    function fetchByUserIDNew($limit = 50, $offset = 0, $filter = array())
    {
        // pr($filter,1);
        $login_user_id = $this->session->userdata('user_info')->user_id;
        $start_date = strtotime(date('Y-m-d') . ' 00:00:00');

        $this->db->select("users.*, users.account_manager_id as manager_id, admin_users.fname as manager_fname, admin_users.lname as manager_lname, users.sale_manager_id as sale_id, admin_sales_users.fname as sale_fname, admin_sales_users.lname as sale_lname,traning_manager.fname as training_fname , traning_manager.lname as training_lname");

        if (!empty($filter['start_date'])) {
            $this->db->where("users.created >= '" . $filter['start_date'] . "'");
        }

        if (!empty($filter['end_date'])) {
            $this->db->where("users.created <= '" . $filter['end_date'] . "'");
        }

        if (!empty($filter['id']) && is_array($filter['id'])) {
            $this->db->where_in('users.id', array_map('intval', $filter['id']));
        }

        if (!empty($filter['email'])) {
            $this->db->where('users.email', $filter['email']);
        }

        if (!empty($filter['phone'])) {
            $this->db->where('users.phone', $filter['phone']);
        }

        if (!empty($filter['manager_id']) && is_array($filter['manager_id'])) {
            $this->db->where_in('users.account_manager_id', array_map('intval', $filter['manager_id']));
        }

        if (!empty($filter['sale_manager_id'])) {
            $this->db->where_in('users.sale_manager_id', array_map('intval', $filter['sale_manager_id']));
        }
        if (!empty($filter['training_manager_id'])) {
            $this->db->where_in('users.training_manager_id', array_map('intval', $filter['training_manager_id']));
        }

        if (!empty($filter['seller_ID'])) {
            $this->db->where_in('users.id', $filter['seller_ID']);
        }
        
        $where_condition = "";
        if (!empty($filter['account_manager_id'])) {
            $where_condition .= "users.account_manager_id = " . $filter['account_manager_id'];
        }

        if (!empty($filter['account_sale_manager_id'])) {
            $where_condition .= " or users.sale_manager_id = " . $filter['account_sale_manager_id'];
        }

        if (!empty($filter['account_training_manager_id'])) {
            $where_condition .= " or users.training_manager_id = " . $filter['account_training_manager_id'];
        }

        if (!empty($where_condition)) {
            $where = '(' . $where_condition . ')';
            $this->db->where($where);
        }
        

        if (!empty($filter['pricing_plan'])) {
            $this->db->where('users.pricing_plan', $filter['pricing_plan']);
        }

        if (!empty($filter['support_category'])) {
            $this->db->where_in('users.support_category', $filter['support_category']);
        }

        if (!empty($filter['seller_cluster'])) {
            $this->db->where_in('users.seller_territory', $filter['seller_cluster']);
        }

        if (!empty($filter['lead_source'])) {
            $this->db->where('users.lead_source', $filter['lead_source']);
        }

        if (!empty($filter['service_type'])) {
            if ($filter['service_type'] == 'domestic')
                $this->db->where('users.service_type', '0');
        }


        if (!empty($filter['tags'])) {
            $this->db->where(" (find_in_set('{$filter['tags']}', users.applied_tags))");
        }

        if (!empty($filter['kyc_done'])) {
            if ($filter['kyc_done'] == 'yes')
                $this->db->where('company_details.companytype !=', '');
            else if ($filter['kyc_done'] == 'e_verified')
                $this->db->where('users.e_verified', '1');
            else
                $this->db->where('company_details.companytype', '');
        }

        if (!empty($filter['recharge_status'])) {
            if ($filter['recharge_status'] == 'yes')
                $this->db->where('users.wallet_balance >', '0');
            else
                $this->db->where('users.wallet_balance', '0');
        }
        if (!empty($filter['status']) && $filter['status'] == 'process') {
            $this->db->where('users.parent_id', '0');
            $this->db->where('users.verified', '0');
        }
        if (!empty($filter['status']) &&  $filter['status'] == 'seller') {
            $this->db->where('users.parent_id !=', '0');
        }
        if (!empty($filter['status']) && $filter['status'] == 'junk') {
            $this->db->where('users.parent_id', '0');
            $this->db->where('users.verified', '2');
        }
        if (!empty($filter['status']) && $filter['status'] == 'active') {
            $this->db->where('users.parent_id', '0');
            $this->db->where('users.verified', '1');
        }

        $this->db->limit($limit);
        $this->db->offset($offset);
        $this->db->order_by('created', 'desc');
        $this->db->group_by('users.id');
        $this->db->join('company_details', 'company_details.user_id = users.id', 'LEFT');
        //$this->db->join('enable_contact', "enable_contact.view_user_id = users.id AND enable_contact.user_id= $login_user_id AND enable_contact.view_type='user_login' AND  enable_contact.created >= '" . $start_date . "'", 'LEFT');
        $this->db->join("( SELECT    id,fname, lname FROM tbl_users where is_admin = '1'  ) admin_users", 'admin_users.id = users.account_manager_id', 'left');
        $this->db->join("(
        SELECT    id,fname, lname 
            FROM  tbl_users
        where is_admin = '1'
    ) admin_sales_users", 'admin_sales_users.id = users.sale_manager_id', 'left');
        $this->db->join("( SELECT id,fname, lname FROM tbl_users where is_admin = '1'  ) traning_manager", 'traning_manager.id = users.training_manager_id', 'left');

        $q = $this->db->get($this->table);
        // echo $this->db->last_query();exit;
        return $q->result();
    }

    function getnewuserslistintwodays()
    {
        $twodayspreviousdate = strtotime("-2 days");
        $twodayspreviousdate =  date('y-m-d 00:00:00', $twodayspreviousdate);

        return  $this->db->select('id')->where("created >= '" . $twodayspreviousdate . "'")->get($this->table)->result();
    }

    function getLegalDetailsByUserId($userid)
    {
        if (!$userid)
            return false;
        $this->db->select("*");
        $this->db->where('user_id', $userid);
        $q = $this->db->get($this->legal_entity);
        return $q->row();
    }


    function insertLegalEntity($accpt_data)
    {
        return $this->db->insert($this->legal_entity, $accpt_data);
    }

    function updateLegalEntity($userid, $accpt_data)
    {
        if (empty($userid) || empty($accpt_data))
            return false;
        $this->db->set($accpt_data);
        $this->db->where('user_id', $userid);
        return $this->db->update($this->legal_entity);
    }


    function batchInsertDuplicateUser($save = array())
    {
        if (empty($save))
            return false;

        $this->db->insert_batch($this->dulicate_accounts, $save);
        return true;
    }

    function get_data_code($code = array())
    {
        if (empty($code))
        return false;
        $this->db->select("id,weight_locked,weight,length,breadth,height");
        $this->db->where('product_details_code', $code);
        $q = $this->db->get('product_details');
        return $q->row();
    }
    public function get_product_details_code($user_id,$product = array()){
        $product =  $this->formatProductDetails($user_id,$product);
        $code = $product['user_id']." ".$product['product_sku']." ".$product['product_name']." ".$product['product_qty'] ;
        if(!empty($code)){
            $code  = url_title($code, 'underscore', TRUE);
            
        }
        return $code;

    }
    public function formatProductDetails($user_id,$product){
        if(empty($product)){
            return false;
        }
        $product_name = isset($product['product_name'])?!empty($product['product_name'])?$product['product_name']:"":"";
        $product_sku = isset($product['product_sku'])?!empty($product['product_sku'])?$product['product_sku']:"":"";
        $product_qty = isset($product['product_qty'])?!empty($product['product_qty'])?$product['product_qty']:"":"";
        $product = array(
                     "user_id"=>$user_id,
                     "product_name"=>$product_name,
                     "product_sku"=>$product_sku,
                     "product_qty"=>$product_qty
                );
        return $product;

    }


    function getOrderProducts($order_id = false)
    {
        if (!$order_id)
            return false;

        $this->db->where('order_id', $order_id);
        $q = $this->db->get('order_products');
        return $q->result();
    }

 
    function getduplicateUserdata($seller_id)
    {
        if (empty($seller_id))
            return false;
        $this->db->select('*');
        $this->db->where('seller_id', $seller_id);
        $q = $this->db->get($this->dulicate_accounts);
        return $q->result();
    }

    function insertaccountmasterid($id, $account_manager_id)
    {
        $this->db->where('id', $id);
        $this->db->set('account_master_id', $account_manager_id);
        $this->db->update($this->table);
        return true;
    }

    function get_master_id($id)
    {
        $this->db->select("id,account_master_id");
        $this->db->where('users.id', $id);
        $q = $this->db->get($this->table);
        //echo "</br>1===>".$this->db->last_query()."</br>";
        return $q->result();
    }

    function getUserIdAndName()
    {
        $this->db->select("id,fname AS name");
        $q = $this->db->get($this->table);
        $user = $q->result();
        return $user;
    }

    function getallparent_id($parent_id)
    {
        $this->db->select("id,fname,lname,company_name,account_master_id");
        $this->db->where('users.id', $parent_id);
        $q = $this->db->get($this->table);
        //echo "1====>".$this->db->last_query(); echo "</br>";
        return $q->result();
    }

    function getallparent_id1($parent_id)
    {
        $this->db->select("id,fname,lname,company_name,account_master_id");
        $this->db->where('users.account_master_id', $parent_id);
        $q = $this->db->get($this->table);
        //echo "2====>".$this->db->last_query(); echo "</br>";
        return $q->result();
    }

    function getdata_user_id($parent_id)
    {
        $this->db->select("id,fname,lname,company_name,account_master_id");
        $this->db->where_in('users.id', $parent_id);
        $q = $this->db->get($this->table);
        return $q->result();
    }

    function update_whatsapp_notification($id = false)
    {
        if (!$id)
            return false;

            $this->db->where('user_id', $id);
            $this->db->delete('whatsapp_notification');
        return true;
    }
}
