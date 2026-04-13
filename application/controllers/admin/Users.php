<?php
defined('BASEPATH') or exit('No direct script access allowed');

use App\Lib\Logs\User as Log;

class Users extends Admin_controller
{
    private $allowed_image_extension;
    private $allowed_document_extension;

    public function __construct()
    {
        parent::__construct();
        $this->load->library('admin/user_lib');
        $this->userHasAccess('users');
        $this->load->helper('download');
        $this->load->library("s3");
        $this->allowed_image_extension = array('jpg', 'jpeg', 'png');
        $this->allowed_document_extension = array('pdf');
         $this->load->model('Courier_model');
    }

    function all($page = 1)
    {
        $per_page = $this->input->get('perPage');
        if (empty($per_page) || !is_numeric($per_page))
            $limit = 50;
        else
            $limit = $per_page;

        $filter = $this->input->post('filter');

        $apply_filters = array();
        $status = !empty($filter['status']) ? $filter['status'] : '';

        $apply_filters['start_date'] = strtotime("today midnight -30 days");
        $apply_filters['end_date'] = strtotime("tomorrow midnight") - 1;
        
        if (!empty($filter['start_date'])) {
            $apply_filters['start_date'] = (trim($filter['start_date']) . ' 00:00:00');
        }else {
            $filter['start_date'] = date('Y-m-d', $apply_filters['start_date']);
            $apply_filters['start_date'] = date('Y-m-d', $apply_filters['start_date']);
        }

        if (!empty($filter['end_date'])) {
            $apply_filters['end_date'] = (trim($filter['end_date']) . ' 23:59:59');
        }else {
            $filter['end_date'] = date('Y-m-d', $apply_filters['end_date']);
            $apply_filters['end_date']= date('Y-m-d', $apply_filters['end_date']);
        }

        if (!empty($filter['id'])) {
            $apply_filters['id'] = $filter['id'];
        }

        if (!empty($filter['email'])) {
            $apply_filters['email'] = $filter['email'];
        }

        if (!empty($filter['user_type'])) {
            $apply_filters['user_type'] = $filter['user_type'];
        }

        if (!empty($filter['seller_ID'])) {
            $apply_filters['seller_ID'] = array_map('trim', explode(',', $filter['seller_ID']));
        }

        if (!empty($filter['phone'])) {
            $apply_filters['phone'] = $filter['phone'];
        }

        if (!empty($filter['manager_id'])) {
            $apply_filters['manager_id'] = $filter['manager_id'];
        }

        if (!empty($filter['sale_manager_id'])) {
            $apply_filters['sale_manager_id'] = $filter['sale_manager_id'];
        }

        if (!empty($filter['b2b_sale_manager_id'])) {
            $apply_filters['b2b_sale_manager_id'] = $filter['b2b_sale_manager_id'];
        }
        if (!empty($filter['training_manager_id'])) {
            $apply_filters['training_manager_id'] = $filter['training_manager_id'];
        }


        if ((!isset($apply_filters['email'])) && (!isset($apply_filters['phone']))) {
            if ($this->restricted_permissions) {
                $apply_filters['account_manager_id'] = $this->user->user_id;
            }
            if ($this->restricted_permissions) {
                $apply_filters['account_sale_manager_id'] = $this->user->user_id;
            }
            if ($this->restricted_permissions) {
                $apply_filters['account_b2b_sale_manager_id'] = $this->user->user_id;
            }
            if ($this->restricted_permissions) {
                $apply_filters['account_international_sale_manager_id'] = $this->user->user_id;
            }

             if ($this->restricted_permissions) {
                $apply_filters['account_training_manager_id'] = $this->user->user_id;
            } 
        }





        $account_level = 0;
        if ($this->restricted_permissions) {
            $account_level = 1;
        }

        if (!empty($filter['pricing_plan'])) {
            $apply_filters['pricing_plan'] = $filter['pricing_plan'];
        }

        if (!empty($filter['support_category'])) {
            $apply_filters['support_category'] = $filter['support_category'];
        }

        if (!empty($filter['seller_cluster'])) {
            $apply_filters['seller_cluster'] = $filter['seller_cluster'];
        }

        if (!empty($filter['status'])) {
            $apply_filters['status'] = $filter['status'];
        }

        if (!empty($filter['kyc_done'])) {
            $apply_filters['kyc_done'] = $filter['kyc_done'];
        }

        if (!empty($filter['recharge_status'])) {
            $apply_filters['recharge_status'] = $filter['recharge_status'];
        }

        if (!empty($filter['lead_source'])) {
            $apply_filters['lead_source'] = $filter['lead_source'];
        }

        if (!empty($filter['service_type'])) {
            $apply_filters['service_type'] = $filter['service_type'];
        }

        if (!empty($filter['tags'])) {
            $apply_filters['tags'] = strtolower($filter['tags']);
        }

        if (!empty($filter['international_sale_manager_id'])) {
            $apply_filters['international_sale_manager_id'] = $filter['international_sale_manager_id'];
        }


        $total_row = $this->user_lib->countByUserID($apply_filters);

        $config = array(
            'base_url' => base_url('admin/users/all'),
            'total_rows' => $total_row,
            'per_page' => $limit,
            'num_links' => '3',
            'use_page_numbers' => true,
            'reuse_query_string' => true,
            'cur_tag_open' => '<li class="paginate_button page-item active"><a class="page-link" href="' . current_url() . '">',
            'cur_tag_close' => '</a></li>',
            'num_tag_open' => '<li class="paginate_button page-item ">',
            'num_tag_close' => '</li>',
            'attributes' => array(
                'aria-controls' => 'example-multi',
                'tabindex' => '0',
                'class' => 'page-link',
            ),
            'next_link' => 'Next',
            'prev_link' => 'Prev',
        );
        $this->load->library("pagination");
        $this->pagination->initialize($config);
        $this->data["pagination"] = $this->pagination->create_links();
        $offset = $limit * ($page - 1);
        $this->data['total_records'] = $total_row;
        $this->data['limit'] = $limit;
        $this->data['offset'] = $offset;
        $this->data['account_level'] = $account_level;
        $userlist = $this->user_lib->fetchByUserIDNew($limit, $offset, $apply_filters);
       
        $adminuserid= $this->session->userdata['user_info']->user_id;
        if (!empty($adminuserid)) 
        {
        $userDatapermission=$this->user_lib->getUserPermissiontype($adminuserid);
        }
         
        


        if (!empty($userlist)) {
            foreach ($userlist as $ul) {
                $ul->login_token = $this->user_lib->getLoginToken($ul->id);
            }
        }
        $this->load->library('admin/user_lib');
        $seller_details = '';
        if (!empty($filter['id']))
            $seller_details = $this->user_lib->getUserListFilter($filter['id']);

        $leadsource = $this->user_lib->getalllead();

        $this->data['users'] = $seller_details;
        $this->data['userlist'] = $userlist;

        $this->data['filter'] = $filter;
        $sellerplan = $this->user_lib->sellerplansheet();
        $admin_users = $this->user_lib->getAdminUsers();
        $this->data['userDatapermission'] = $userDatapermission;
        $this->data['lead_source'] = $leadsource;
        $this->data['admin_users'] = $admin_users;
        $this->data['plan'] = $sellerplan;
        $this->layout('user/index');
    }

    function exportCSV()
    {
        $this->userHasAccess('users_export');

        $filter = $this->input->get('filter');

        $apply_filters = array();
        $status = !empty($filter['status']) ? $filter['status'] : '';
        if (!empty($filter['start_date'])) {
            $apply_filters['start_date'] = (trim($filter['start_date']) . ' 00:00:00');
        }
        if (!empty($filter['end_date'])) {
            $apply_filters['end_date'] = (trim($filter['end_date']) . ' 23:59:59');
        }

        if (!empty($filter['id'])) {
            $apply_filters['id'] = $filter['id'];
        }
        if (!empty($filter['email'])) {
            $apply_filters['email'] = $filter['email'];
        }

        if (!empty($filter['seller_ID'])) {
            $apply_filters['seller_ID'] = array_map('trim', explode(',', $filter['seller_ID']));
        }

        if (!empty($filter['phone'])) {
            $apply_filters['phone'] = $filter['phone'];
        }

        if (!empty($filter['manager_id'])) {
            $apply_filters['manager_id'] = $filter['manager_id'];
        }

        if (!empty($filter['sale_manager_id'])) {
            $apply_filters['sale_manager_id'] = $filter['sale_manager_id'];
        }
        if (!empty($filter['training_manager_id'])) {
            $apply_filters['training_manager_id'] = $filter['training_manager_id'];
        }

        if ($this->restricted_permissions) {
            $apply_filters['account_manager_id'] = $this->user->user_id;
        }

        if ($this->restricted_permissions) {
            $apply_filters['sale_manager_id_1'] = $this->user->user_id;
        }

        if ($this->restricted_permissions) {
            $apply_filters['training_manager_id'] = $this->user->user_id;
        }

        if (!empty($filter['pricing_plan'])) {
            $apply_filters['pricing_plan'] = $filter['pricing_plan'];
        }

        if ($this->restricted_permissions) {
            $apply_filters['account_manager_id'] = $this->user->user_id;
        }

        if (!empty($filter['support_category'])) {
            $apply_filters['support_category'] = $filter['support_category'];
        }

        if (!empty($filter['seller_cluster'])) {
            $apply_filters['seller_cluster'] = $filter['seller_cluster'];
        }
        if (!empty($filter['status'])) {
            $apply_filters['status'] = $filter['status'];
        }
        if (!empty($filter['kyc_done'])) {
            $apply_filters['kyc_done'] = $filter['kyc_done'];
        }
        if (!empty($filter['recharge_status'])) {
            $apply_filters['recharge_status'] = $filter['recharge_status'];
        }
        if (!empty($filter['lead_source'])) {
            $apply_filters['lead_source'] = $filter['lead_source'];
        }

        if (!empty($filter['service_type'])) {
            $apply_filters['service_type'] = $filter['service_type'];
        }
        if (!empty($filter['tags'])) {
            $apply_filters['tags'] = strtolower($filter['tags']);
        }
        

        $this->data['filter'] = $filter;
        $userlistQuery = $this->user_lib->fetchByUserIDAllExports($apply_filters);
        //pr($userlistQuery,1);
        $this->load->library('export_db');
        $export = new Export_db('slave');
        $export->query($userlistQuery);
        $filename = 'SellerActiveList_' . time() . '.csv';
        header("Content-Description: File Transfer");
        header("Content-Disposition: attachment; filename=$filename");
        header("Content-Type: application/csv; ");
        $file = fopen('php://output', 'w');
        $header = array(
            "User Id",
            "Sign up Date",
            "Verified Date",
            "Verification type",
            "Company",
            "Remittance Freezed",
            "Seller Name",
            "Email",
            "Mobile",
            // "LeadSquared Id",
            "Seller Region",
            "Seller Territory",
            "Company Type",
            "Company GST",
            "Company Pan Number",
            "Company Address",
            "Company City",
            "Company State",
            "Company Pincode",
            "Company Account Name",
            "Company Account Number",
            "IFSC Code",
            "Account Type",
            "Projected Shipment Volume",
            "Wallet Balance",
            "Wallet Limit",
            "Current Plan",
            "Remittance Cycle",
            "Remittance ON Hold",
            "Register Date",
            "NDR Action Type",
            // "Account Manager Name",
            // "Domestic Sales Person Name",
            // "Training Manager Name",
            // "Lead Source",
            "Support Category",
            // "Sign Up Type",
            // "Service Type(Domestic)",
            // "Status",
            // "Service Type (International)",
            // "Status",
            // "Users Tag",
            // "Wallet Adjustment Cycle",
            // "Settled to wallet(%)",
            // "Settled to bank(%)",
            // "Notes Date",
            // "Notes Category",
            // "Notes Remarks",
            // "Notes By",
        );
        fputcsv($file, $header);
        while ($user = $export->next()) {
            $user_id = $user->id;
            $uid = (int)$user_id;
            $note = $this->user_lib->getUsersNotes($uid);
            $note_array = array();
            if (!empty($note)) {
                $note_date = (!empty($note->created)) ? $note->created : '';
                $note_array[] = date('j-M-Y', (int)$note_date);
                $note_array[] = (!empty($note->category_issue)) ? $note->category_issue : '';
                $note_array[] = (!empty($note->remarks)) ? $note->remarks : '';
                $note_array[] = $note->fname . ' ' . $note->lname;
            }
            switch ($user->verified) {
                case '0':
                    $domesticStatus = "Process";
                    break;
                case '1':
                    $domesticStatus = "Active";
                    break;
                case '2':
                    $domesticStatus = "Junk Seller";
                    break;
                default:
                    $domesticStatus = "Process";
                    break;
            }
            if (!empty($user->int_kyc_status)) {
                $internationalStatus = "Active";
            } else {
                $internationalStatus = "Process";
            }
            if ($user->service_type == '0') {
                $signUpType =  "Domestic";
            } elseif ($user->service_type == '1') {
                $signUpType = "International";
            } else {
                $signUpType =  "Domestic";
            }
            if ($user->verified_date == '0') {
                $varifiy_date = "";
            } else {
                $varifiy_date = date("j-M-Y", $user->verified_date);
            }
            if ($user->e_verified == '0' && ($user->verified == '1' || $user->verified == '2')) {
                $varifiy = "Manually";
            } else if ($user->e_verified == '1' && ($user->verified == '1' || $user->verified == '2')) {
                $varifiy = "E-Verified";
            } else {
                $varifiy = "";
            }
            if ($user->freeze_remittance == '0') {
                $freeze_remittance = 'No';
            } else {
                $freeze_remittance = 'Yes';
            }
            $wallet_adjustment_cycle = "";
            if ((isset($user->wallet_adjustment_cycle)) && ($user->wallet_adjustment_cycle == '1')) {
                $wallet_adjustment_cycle = "Daily";
            }
            if ((isset($user->wallet_adjustment_cycle)) && ($user->wallet_adjustment_cycle == '2')) {
                $wallet_adjustment_cycle = "Twice a week";
            }
            if ((isset($user->wallet_adjustment_cycle)) && ($user->wallet_adjustment_cycle == '3')) {
                $wallet_adjustment_cycle = "Thrice a week";
            }
            $settle_wallet = "";
            $settle_bank = "";
            if ((isset($user->remitence_term)) && (!empty($user->remitence_term))) {
                $remitence_term = unserialize($user->remitence_term);
                $settle_wallet = isset($remitence_term['settle_wallet']) ? $remitence_term['settle_wallet'] : "";
                $settle_bank  = isset($remitence_term['settle_bank']) ? $remitence_term['settle_bank'] : "";
            }
            $row = array(
                $user->id,
                date("j-M-Y", strtotime($user->created)),
                $varifiy_date,
                $varifiy,
                ucwords($user->company_name),
                ucwords($freeze_remittance),
                ucwords($user->fname . ' ' . $user->lname),
                $user->email,
                $user->phone,
                // $user->leadsquared_id,
                ucwords($user->seller_region),
                ucwords($user->seller_territory),
                ucwords($user->companytype),
                strtoupper($user->cmp_gstno),
                strtoupper($user->cmp_pan),
                $user->cmp_address,
                ucwords($user->cmp_city),
                ucwords($user->cmp_state),
                $user->cmp_pincode,
                ucwords($user->cmp_accntholder),
                $user->cmp_accno,
                strtoupper($user->cmp_accifsc),
                ucwords($user->cmp_acctype),
                $user->projected_shipments,
                $user->wallet_balance,
                $user->wallet_limit,
                ucwords($user->pricing_plan),
                'T+' . $user->remittance_cycle,
                $user->remittance_on_hold_amount,
                date("j-M-Y", strtotime($user->created)),
                ucwords($user->ndr_action_type),
                // ucwords($user->manager_fname . ' ' . $user->manager_lname),
                // ucwords($user->sale_fname . ' ' . $user->sale_lname),
                // ucwords($user->training_fname . ' ' . $user->training_lname),
                // ucwords($user->lead_source),
                ucwords($user->support_category),
                // $signUpType,
                // "Domestic",
                // $domesticStatus,
                // ucwords($user->applied_tags),
                // $wallet_adjustment_cycle,
                // $settle_wallet,
                // $settle_bank
            );
            $row = array_merge($row, $note_array);
            fputcsv($file, $row);
        }
        fclose($file);
        exit;
    }

    public function processseller($page = 1)
    {
        $per_page = $this->input->get('perPage');
        if (empty($per_page) || !is_numeric($per_page))
            $limit = 50;
        else
            $limit = $per_page;
        $filter = $this->input->get('filter');
        $apply_filters = array();

        if (!empty($filter['start_date'])) {
            $apply_filters['start_date'] = (trim($filter['start_date']) . ' 00:00:00');
        }
        if (!empty($filter['end_date'])) {
            $apply_filters['end_date'] = (trim($filter['end_date']) . ' 23:59:59');
        }

        if (!empty($filter['id'])) {
            $apply_filters['id'] = $filter['id'];
        }
        if (!empty($filter['email'])) {
            $apply_filters['email'] = $filter['email'];
        }
        if (!empty($filter['kyc_done'])) {
            $apply_filters['kyc_done'] = $filter['kyc_done'];
        }
        if (!empty($filter['recharge_status'])) {
            $apply_filters['recharge_status'] = $filter['recharge_status'];
        }

        if (!empty($filter['seller_ID'])) {
            $apply_filters['seller_ID'] = array_map('trim', explode(',', $filter['seller_ID']));
        }

        if (!empty($filter['phone'])) {
            $apply_filters['phone'] = $filter['phone'];
        }

        if (!empty($filter['lead_source'])) {
            $apply_filters['lead_source'] = $filter['lead_source'];
        }

        if (!empty($filter['service_type'])) {
            $apply_filters['service_type'] = $filter['service_type'];
        }

        if ($this->restricted_permissions) {
            $apply_filters['account_manager_id'] = $this->user->user_id;
        }

        if (!empty($filter['manager_id'])) {
            $apply_filters['manager_id'] = $filter['manager_id'];
        }

        if (!empty($filter['sale_manager_id'])) {
            $apply_filters['sale_manager_id'] = $filter['sale_manager_id'];
        }

        if (!empty($filter['international_sale_manager_id'])) {
            $apply_filters['international_sale_manager_id'] = $filter['international_sale_manager_id'];
        }


        $total_row = $this->user_lib->countByUserIDprocess($apply_filters);
        $config = array(
            'base_url' => base_url('admin/users/processseller'),
            'total_rows' => $total_row,
            'per_page' => $limit,
            'num_links' => '3',
            'use_page_numbers' => true,
            'reuse_query_string' => true,
            'cur_tag_open' => '<li class="paginate_button page-item active"><a class="page-link" href="' . current_url() . '">',
            'cur_tag_close' => '</a></li>',
            'num_tag_open' => '<li class="paginate_button page-item ">',
            'num_tag_close' => '</li>',
            'attributes' => array(
                'aria-controls' => 'example-multi',
                'tabindex' => '0',
                'class' => 'page-link',
            ),
            'next_link' => 'Next',
            'prev_link' => 'Prev',
        );
        $this->load->library("pagination");
        $this->pagination->initialize($config);
        $this->data["pagination"] = $this->pagination->create_links();
        $offset = $limit * ($page - 1);
        $this->data['total_records'] = $total_row;
        $this->data['limit'] = $limit;
        $this->data['offset'] = $offset;
        $this->data['filter'] = $filter;
        $userlist = $this->user_lib->fetchByUserIDprocess($limit, $offset, $apply_filters);
        if (!empty($userlist)) {
            foreach ($userlist as $ul) {
                $ul->login_token = $this->user_lib->getLoginToken($ul->id);
            }
        }
        $this->load->library('admin/user_lib');
        $seller_details = $this->user_lib->getUserprocessList($this->restricted_permissions);
        $this->data['users'] = $seller_details;
        $this->data['userlist'] = $userlist;
        $admin_users = $this->user_lib->getAdminUsers();
        $this->data['admin_users'] = $admin_users;
        $this->layout('user/process');
    }

    function processexportCSV()
    {
        $filter = $this->input->get('filter');
        $apply_filters = array();

        if (!empty($filter['start_date'])) {
            $apply_filters['start_date'] = (trim($filter['start_date']) . ' 00:00:00');
        }
        if (!empty($filter['end_date'])) {
            $apply_filters['end_date'] = (trim($filter['end_date']) . ' 23:59:59');
        }

        if (!empty($filter['id'])) {
            $apply_filters['id'] = $filter['id'];
        }
        if (!empty($filter['email'])) {
            $apply_filters['email'] = $filter['email'];
        }

        if (!empty($filter['kyc_done'])) {
            $apply_filters['kyc_done'] = $filter['kyc_done'];
        }
        if (!empty($filter['recharge_status'])) {
            $apply_filters['recharge_status'] = $filter['recharge_status'];
        }

        if (!empty($filter['phone'])) {
            $apply_filters['phone'] = $filter['phone'];
        }

        if (!empty($filter['lead_source'])) {
            $apply_filters['lead_source'] = $filter['lead_source'];
        }

        if (!empty($filter['service_type'])) {
            $apply_filters['service_type'] = $filter['service_type'];
        }

        if ($this->restricted_permissions) {
            $apply_filters['account_manager_id'] = $this->user->user_id;
        }

        $this->data['filter'] = $filter;
        $userlist = $this->user_lib->fetchByUserIDprocess(10000, 0, $apply_filters);
        $filename = 'SellerProcessList_' . time() . '.csv';
        header("Content-Description: File Transfer");
        header("Content-Disposition: attachment; filename=$filename");
        header("Content-Type: application/csv; ");
        $file = fopen('php://output', 'w');
        $header = array("Name", "Plan", "Company", "Email", "Company Contact", "LeadSquared Id", "Warehouse", "Warehouse Address", "Warehouse Phone", "Company GST", "Date", "Verified Status");
        fputcsv($file, $header);
        foreach ($userlist as $user) {
            $row = array(
                $user->fname . ' ' . $user->lname,
                $user->pricing_plan,
                $user->company_name,
                $user->email,
                $user->phone,
                $user->name,
                $user->address_1 . ' ' . $user->address_2 . ' ' . $user->city . ' ' . $user->state . ' ' . $user->zip,
                $user->pickupcell,
                $user->gst_number,
                date("j-M-Y", strtotime($user->created)),
                ($user->verified == '1') ? 'Yes' : 'No',
            );
            fputcsv($file, $row);
        }
        fclose($file);
        exit;
    }

    public function verifyusers()
    {
        $userids = $this->input->post('ids');
        $exploreids = explode(",", $userids);
        $updateverify = array(
            'verified' => '1',
        );

        foreach ($exploreids as $user_id) {
            $log = new Log();
            $log->update($this->user->user_id, $user_id, 'Account Verified');
        }
        $this->user_lib->updateuserverify($exploreids, $updateverify);
        $this->user_lib->updateVerifyDate($exploreids);
    }

    public function updatestatus()
    {
        $userids = $this->input->post('ids');
        $stauts = $this->input->post('stauts');
        $exploreids = explode(",", $userids);
        $save = array(
            'verified' => $stauts,
        );

        if ($stauts == 1) {
            $this->user_lib->updateVerifyDate($exploreids);
        }
        foreach ($exploreids as $id) {
            $this->user_lib->update($id, $save);

            $log = new Log();
            $log->update($this->user->user_id, $id, 'Verification status changed to : ' . $stauts);
            if ($stauts == 1) {
                $this->user_lib->create_lead($id);
            }
        }
        $this->data['json'] = array('success' => 'Success');
        $this->layout(false, 'json');
    }

    public function removemanager()
    {
        $userids = $this->input->post('ids');
        $exploreids = explode(",", $userids);
        $save = array(
            'account_manager_id' => '',
        );
        foreach ($exploreids as $id) {
            $this->user_lib->update($id, $save);
            $log = new Log();
            $log->update($this->user->user_id, $id, 'Removed account manager');
        }
    }

    public function processusers()
    {
        $userids = $this->input->post('ids');
        $exploreids = explode(",", $userids);
        $updateprocess = array(
            'verified' => '0',
        );
        $this->user_lib->updateuserprocess($exploreids, $updateprocess);
    }

    public function deleteusers()
    {
        $userids = $this->input->post('ids');
        $exploreids = explode(",", $userids);
        $this->user_lib->deleteuser($exploreids);
    }

    public function viewuser($id = false)
    {
        if (!$id)
            redirect('users/all', true);

        $filter = $this->input->get();

        $this->load->library('form_validation');
        $config = array(
            array(
                'field' => 'planname',
                'label' => 'Plan',
                'rules' => 'required'
            ),
        );
        $this->form_validation->set_rules($config);
        if ($this->form_validation->run()) {
            $change_data = array(
                'pricing_plan' => $this->input->post('planname'),
            );

            $this->user_lib->change_pricing_plan($id, $change_data);
            $this->data['success'] = "Plan Updated Successfully";
        } else {
            $this->data['error'] = validation_errors();
        }
        $singleuserdetail = $this->user_lib->singleuserview($id);

        if ($this->restricted_permissions && (!in_array($this->restricted_permissions, array($singleuserdetail[0]->account_manager_id, $singleuserdetail[0]->international_sale_manager_id, $singleuserdetail[0]->sale_manager_id)))) {
            if (!empty($filter['email']) || !empty($filter['phone'])) {
                $this->session->set_flashdata('error', 'No permission');
                redirect('admin/users/all');
            }
        }



        $referral_id = $singleuserdetail[0]->referral_id;
        $referral_name = $this->user_lib->referral_name($referral_id);
        $userwarehosuedetail = $this->user_lib->sellerwarehouse($id);
        $userchanneldetail = $this->user_lib->userchannelview($id);
        $selleremployeedetail = $this->user_lib->selleremployeeview($id);
        $sellercompanydetail = $this->user_lib->sellercompanyview($id);
        $sellerbankdetail = $this->user_lib->sellerbankview($id);
        $sellerrechargelogdetail = $this->user_lib->sellerrechargelogview($id);
        $total_remittance = $this->user_lib->total_remittance($id);
        $seller_details = $this->user_lib->getUserList($this->restricted_permissions);
        $all_seller_details = $this->user_lib->allgetUserList();
        $sellerplan = $this->user_lib->sellerplansheet();
        $seller_ndr_call = $this->user_lib->get_seller_ndrcall_status($id);
        $legalEntity = $this->user_lib->getLegalDetailsByUserId($id);

        if (!empty($singleuserdetail[0]->account_master_id)) {
            $parent_id = $this->getparent_id($singleuserdetail[0]->account_master_id);
            $this->data['parent_id'] = $parent_id;
            $master_data = $this->getparent_data1($parent_id);

            $personArray = $this->array_flatten($master_data);
            $alldata = $this->my_array_unique($personArray);
            $sassdsd = $this->user_lib->getdata_user_id($alldata);
            $this->data['master_accounts'] = $sassdsd;
        } else {

            $parent_id = $this->getparent_id($id);

            $this->data['parent_id'] = '';
            // echo "new".$parent_id; //die;
            $master_data = $this->getparent_data1($parent_id);
            //echo "new parent--->";pr($master_data);

            $personArray = $this->array_flatten($master_data);
            $alldata = $this->my_array_unique($personArray);
            $sassdsd = $this->user_lib->getdata_user_id($alldata);
            //   pr($sassdsd); die;
            $this->data['master_accounts'] = $sassdsd;
        }
        //die;
        $this->load->library('userlogs_lib');
        $log_history_details = $this->userlogs_lib->getUserLogHistoryByUserId($id);

        $this->data['legalEntity'] = $legalEntity;
        $this->data['users'] = $seller_details;
        $this->data['allusers'] = $all_seller_details;
        $this->data['singleuserdetail'] = $singleuserdetail;
        $this->data['referral_name'] = $referral_name;
        $this->data['userwarehosuedetail'] = $userwarehosuedetail;
        $this->data['channels'] = $userchanneldetail;
        $this->data['employees'] = $selleremployeedetail;
        $this->data['companydetails'] = $sellercompanydetail;
        $this->data['bankdetails'] = $sellerbankdetail;
        $this->data['rechargelogdetails'] = $sellerrechargelogdetail;
        $this->data['total_remittance'] = $total_remittance;
        $this->data['trello_card_url'] = '';
        $this->data['notes'] = $this->user_lib->getNotes($id);
        $this->data['plan'] = $sellerplan;
        $this->data['userIVR'] = (!empty($userIVR->api_field_6)) ? ($userIVR->api_field_6) : '';
        $this->data['tags'] = $this->user_lib->getUserTags($id);
        $this->data['duplicate_account'] = $this->user_lib->getDuplicateUser($singleuserdetail, $userwarehosuedetail);
        $this->data['get_dispute_time_limit'] = $this->user_lib->get_dispute_time_limit($id);
        $is_enable = $this->user_lib->is_view_enable_all($this->user->user_id, $id);
        $this->data['is_enable'] = !empty($is_enable) ? array_column(empty($is_enable) ? [] : $is_enable, 'view_type') : [''];
        $this->data['seller_ndrcall'] = $seller_ndr_call;
        $this->data['state_codes'] = $this->config->item('state_codes');
        $this->data['log_history_details'] = $log_history_details;
        $this->layout('user/view');
    }

    function my_array_unique($array, $keep_key_assoc = false)
    {
        $duplicate_keys = array();
        $tmp = array();
        if (!empty($array)) {
            foreach ($array as $key => $val) {
                // convert objects to arrays, in_array() does not support objects
                if (is_object($val))
                    $val = (array)$val;

                if (!in_array($val, $tmp))
                    $tmp[] = $val;
                else
                    $duplicate_keys[] = $key;
            }

            foreach ($duplicate_keys as $key)
                unset($array[$key]);

            return $keep_key_assoc ? $array : array_values($array);
        }
    }

    function array_flatten($array)
    {
        if (!is_array($array)) {
            return FALSE;
        }
        $result = array();
        foreach ($array as $key => $value) {
            if (is_array($value)) {
                $result = array_merge($result, $this->array_flatten($value));
            } else {
                $result[$key] = $value;
            }
        }
        return $result;
    }

    function referral_ids($id = false)
    {
        $this->load->library('form_validation');
        $refid = $this->input->post('referralid');
        $config = array(
            array(
                'field' => 'referralid',
                'label' => 'Referral Id',
                'rules' => 'required'
            ),
        );
        $this->form_validation->set_rules($config);
        if ($this->form_validation->run()) {
            $this->user_lib->insertreferrallink($id, $refid);

            $log = new Log();
            $log->update($this->user->user_id, $id, "Changed referral to: " . $refid);

            $this->session->set_flashdata('success', 'Refferral Id Added Successfully');
            redirect(base_url('admin/users/viewuser/' . $id));
        } else {
            $this->data['error'] = validation_errors();
            redirect(base_url('admin/users/viewuser/' . $id));
        }
    }

    // public function sellerplan()
    // {
    //     $this->load->library('form_validation');
    //     $id = $this->input->post('seller_id');
    //     $config = array(
    //         array(
    //             'field' => 'planname',
    //             'label' => 'Plan',
    //             'rules' => 'required'
    //         ),
    //         array(
    //             'field' => 'order_type',
    //             'label' => 'Order Type',
    //             'rules' => 'required'
    //         )
    //     );

    //     $this->form_validation->set_rules($config);
    //     if ($this->form_validation->run()) {
    //         $order_type = '';

    //         if ($this->input->post('order_type') == 'international') {
    //             $order_type = $this->input->post('order_type') . ' ';
    //             $change_data = array(
    //                 'is_international_franchise' => !empty($this->input->post('is_international_franchise')) ? '1' : '0',
    //                 'international_pricing_plan' => $this->input->post('planname')
    //             );
    //         } else if ($this->input->post('order_type') == 'cargo') {
    //             $order_type = $this->input->post('order_type') . ' ';
    //             $change_data = array(
    //                 'is_franchise' => !empty($this->input->post('is_franchise')) ? 'yes' : 'no',
    //                 'cargo_pricing_plan' => $this->input->post('planname')
    //             );
    //         } else {
    //             $change_data = array(
    //                 'pricing_plan' => $this->input->post('planname')
    //             );
    //         }

    //         $this->user_lib->change_pricing_plan($id, $change_data);
    //         $this->data['json'] = array('success' => ucfirst($order_type) . 'Plan Updated Successfully');

    //         $log = new Log();
    //         $log->update($this->user->user_id, $id, "Changed " . $order_type . "plan to " . $this->input->post('planname'));

    //         $this->layout(false, 'json');
    //         return;
    //     } else {
    //         $this->data['json'] = array('error' => strip_tags(validation_errors()));
    //         $this->layout(false, 'json');
    //         return;
    //     }

    //     $singleuserdetail = $this->user_lib->singleuserview($id);
    //     if ($this->restricted_permissions && $singleuserdetail[0]->account_manager_id != $this->restricted_permissions) {
    //         $this->session->set_flashdata('error', 'No permission');
    //         redirect('admin/users/all');
    //     }
    // }

    public function sellerplan()
{
    $this->load->library('form_validation');
    $id = $this->input->post('seller_id');

    $this->form_validation->set_rules([
        [
            'field' => 'planname',
            'label' => 'Plan',
            'rules' => 'required'
        ],
        [
            'field' => 'order_type',
            'label' => 'Order Type',
            'rules' => 'required'
        ]
    ]);

    if ($this->form_validation->run()) {
        $order_type = $this->input->post('order_type');
        $planname = $this->input->post('planname');

        if ($order_type === 'international') {
            $change_data = [
                'is_international_franchise' => $this->input->post('is_international_franchise') ? '1' : '0',
                'international_pricing_plan' => $planname
            ];
        } elseif ($order_type === 'cargo') {
            $change_data = [
                'is_franchise' => $this->input->post('is_franchise') ? 'yes' : 'no',
                'cargo_pricing_plan' => $planname
            ];
        } else {
            $change_data = [
                'pricing_plan' => $planname
            ];
        }

        // Update pricing plan
        $this->user_lib->change_pricing_plan($id, $change_data);

        // Log activity
        $log = new Log();
        $log->update($this->user->user_id, $id, "Changed {$order_type} plan to {$planname}");

        // Flash success message and redirect
        $this->session->set_flashdata('success', ucfirst($order_type) . ' plan updated successfully.');
        redirect('admin/users/viewuser/' . $id);
    } else {
        // Flash error message and redirect back
        $this->session->set_flashdata('error', strip_tags(validation_errors()));
        redirect('admin/users/viewuser/' . $id);
    }
}


    public function Wallet_limit($id = false)
    {
        if (!$id)
            redirect('users/all', true);
        $invoicemode    = $this->input->post('invoicemode');
        if (!empty($invoicemode)) {
            $this->load->library('form_validation');
            $config = array(
                array(
                    'field' => 'wallet_limit',
                    'label' => 'Wallet Limit',
                    'rules' => 'required|numeric'
                ),
                array(
                    'field' => 'is_postpaid',
                    'label' => 'Invoice Mode',
                    'rules' => 'required'
                ),
            );
            $this->form_validation->set_rules($config);
            if ($this->form_validation->run()) {
                $data = array(
                    'wallet_limit' => $this->input->post('wallet_limit'),
                    'is_postpaid' => $this->input->post('is_postpaid'),
                );
                $this->user_lib->add_wallet_balance($id, $data);
                $log = new Log();
                $log->update($this->user->user_id, $id, "Wallet limit: " . $this->input->post('wallet_limit'));


                $this->data['success'] = "Wallet Limit Updated Successfully";
            } else {
                $this->data['error'] = validation_errors();
            }
        } else {
            $this->load->library('form_validation');
            $config = array(
                array(
                    'field' => 'wallet_limit',
                    'label' => 'Wallet Limit',
                    'rules' => 'required|numeric'
                ),
            );
            $this->form_validation->set_rules($config);
            if ($this->form_validation->run()) {
                $data = array(
                    'wallet_limit' => $this->input->post('wallet_limit'),
                );
                $this->user_lib->add_wallet_balance($id, $data);
                $log = new Log();
                $log->update($this->user->user_id, $id, "Wallet limit: " . $this->input->post('wallet_limit'));


                $this->data['success'] = "Wallet Limit Updated Successfully";
            } else {
                $this->data['error'] = validation_errors();
            }
        }

        $singleuserdetail = $this->user_lib->singleuserview($id);
        $referral_id = $singleuserdetail[0]->referral_id;
        $referral_name = $this->user_lib->referral_name($referral_id);
        $userwarehosuedetail = $this->user_lib->sellerwarehouse($id);
        $userchanneldetail = $this->user_lib->userchannelview($id);
        $selleremployeedetail = $this->user_lib->selleremployeeview($id);
        $sellercompanydetail = $this->user_lib->sellercompanyview($id);
        $sellerbankdetail = $this->user_lib->sellerbankview($id);
        $sellerrechargelogdetail = $this->user_lib->sellerrechargelogview($id);
        $total_remittance = $this->user_lib->total_remittance($id);
        $this->data['singleuserdetail'] = $singleuserdetail;
        $this->data['referral_name'] = $referral_name;
        $this->data['userwarehosuedetail'] = $userwarehosuedetail;
        $this->data['channels'] = $userchanneldetail;
        $this->data['employees'] = $selleremployeedetail;
        $this->data['companydetails'] = $sellercompanydetail;
        $this->data['bankdetails'] = $sellerbankdetail;
        $this->data['rechargelogdetails'] = $sellerrechargelogdetail;
        $this->data['total_remittance'] = $total_remittance;
        $this->data['trello_card_url'] ='';
        redirect('admin/users/viewuser/'.$id);
        //$this->layout('user/view');
    }

    public function selleremployee($page = 1)
    {
        $per_page = $this->input->get('perPage');
        if (empty($per_page) || !is_numeric($per_page))
            $limit = 50;
        else
            $limit = $per_page;
        $filter = $this->input->get('filter');
        $apply_filters = array();

        if (!empty($filter['start_date'])) {
            $apply_filters['start_date'] = (trim($filter['start_date']) . ' 00:00:00');
        }
        if (!empty($filter['end_date'])) {
            $apply_filters['end_date'] = (trim($filter['end_date']) . ' 23:59:59');
        }
        if (!empty($filter['email'])) {
            $apply_filters['email'] = $filter['email'];
        }

        if (!empty($filter['id'])) {
            $apply_filters['id'] = $filter['id'];
        }

        if (!empty($filter['phone'])) {
            $apply_filters['phone'] = $filter['phone'];
        }

        if (!empty($filter['seller_ID'])) {
            $apply_filters['seller_ID'] = array_map('trim', explode(',', $filter['seller_ID']));
        }


        if ($this->restricted_permissions) {
            $apply_filters['account_manager_id'] = $this->user->user_id;
        }

        $total_row = $this->user_lib->countByUserIDemployee($apply_filters);
        $config = array(
            'base_url' => base_url('admin/users/processseller'),
            'total_rows' => $total_row,
            'per_page' => $limit,
            'num_links' => '3',
            'use_page_numbers' => true,
            'reuse_query_string' => true,
            'cur_tag_open' => '<li class="paginate_button page-item active"><a class="page-link" href="' . current_url() . '">',
            'cur_tag_close' => '</a></li>',
            'num_tag_open' => '<li class="paginate_button page-item ">',
            'num_tag_close' => '</li>',
            'attributes' => array(
                'aria-controls' => 'example-multi',
                'tabindex' => '0',
                'class' => 'page-link',
            ),
            'next_link' => 'Next',
            'prev_link' => 'Prev',
        );
        $this->load->library("pagination");
        $this->pagination->initialize($config);
        $this->data["pagination"] = $this->pagination->create_links();
        $offset = $limit * ($page - 1);
        $this->data['total_records'] = $total_row;
        $this->data['limit'] = $limit;
        $this->data['offset'] = $offset;
        $this->data['filter'] = $filter;
        $employeelist = $this->user_lib->fetchByUserIDemployee($limit, $offset, $apply_filters);
        if (!empty($employeelist)) {
            foreach ($employeelist as $ul) {
                $ul->login_token = $this->user_lib->getLoginToken($ul->id);
            }
        }
        $this->load->library('admin/user_lib');
        $seller_details = $this->user_lib->getUserprocessList($this->restricted_permissions);
        $this->data['users'] = $seller_details;
        $this->data['employeelist'] = $employeelist;
        $this->layout('user/employees');
    }

    function selleremployeeCSV()
    {
        $filter = $this->input->get('filter');
        $apply_filters = array();
        if (!empty($filter['start_date'])) {
            $apply_filters['start_date'] = (trim($filter['start_date']) . ' 00:00:00');
        }
        if (!empty($filter['end_date'])) {
            $apply_filters['end_date'] = (trim($filter['end_date']) . ' 23:59:59');
        }
        if (!empty($filter['email'])) {
            $apply_filters['email'] = $filter['email'];
        }
        if (!empty($filter['phone'])) {
            $apply_filters['phone'] = $filter['phone'];
        }

        if ($this->restricted_permissions) {
            $apply_filters['account_manager_id'] = $this->user->user_id;
        }

        $this->data['filter'] = $filter;
        $userlist = $this->user_lib->fetchByUserIDemployee(10000, 0, $apply_filters);
        $filename = 'EmployeeList_' . time() . '.csv';
        header("Content-Description: File Transfer");
        header("Content-Disposition: attachment; filename=$filename");
        header("Content-Type: application/csv; ");
        $file = fopen('php://output', 'w');
        $header = array("Date", "Employee Name", "Email", "Permisssion");
        fputcsv($file, $header);
        foreach ($userlist as $user) {
            $row = array(
                date("j-M-Y", strtotime($user->created)),
                $user->fname,
                $user->email,
                $user->permissions,
            );
            fputcsv($file, $row);
        }
        fclose($file);
        exit;
    }

    public function junkseller($page = 1)
    {
        $per_page = $this->input->get('perPage');
        if (empty($per_page) || !is_numeric($per_page))
            $limit = 50;
        else
            $limit = $per_page;
        $filter = $this->input->get('filter');
        $apply_filters = array();

        if (!empty($filter['start_date'])) {
            $apply_filters['start_date'] = (trim($filter['start_date']) . ' 00:00:00');
        }
        if (!empty($filter['end_date'])) {
            $apply_filters['end_date'] = (trim($filter['end_date']) . ' 23:59:59');
        }
        if (!empty($filter['email'])) {
            $apply_filters['email'] = $filter['email'];
        }

        if (!empty($filter['id'])) {
            $apply_filters['id'] = $filter['id'];
        }

        if (!empty($filter['phone'])) {
            $apply_filters['phone'] = $filter['phone'];
        }

        if (!empty($filter['seller_ID'])) {
            $apply_filters['seller_ID'] = array_map('trim', explode(',', $filter['seller_ID']));
        }

        if ($this->restricted_permissions) {
            $apply_filters['account_manager_id'] = $this->user->user_id;
        }

        $total_row = $this->user_lib->countByUserIDjunk($apply_filters);
        $config = array(
            'base_url' => base_url('admin/users/processseller'),
            'total_rows' => $total_row,
            'per_page' => $limit,
            'num_links' => '3',
            'use_page_numbers' => true,
            'reuse_query_string' => true,
            'cur_tag_open' => '<li class="paginate_button page-item active"><a class="page-link" href="' . current_url() . '">',
            'cur_tag_close' => '</a></li>',
            'num_tag_open' => '<li class="paginate_button page-item ">',
            'num_tag_close' => '</li>',
            'attributes' => array(
                'aria-controls' => 'example-multi',
                'tabindex' => '0',
                'class' => 'page-link',
            ),
            'next_link' => 'Next',
            'prev_link' => 'Prev',
        );
        $this->load->library("pagination");
        $this->pagination->initialize($config);
        $this->data["pagination"] = $this->pagination->create_links();
        $offset = $limit * ($page - 1);
        $this->data['total_records'] = $total_row;
        $this->data['limit'] = $limit;
        $this->data['offset'] = $offset;
        $this->data['filter'] = $filter;
        $userlist = $this->user_lib->fetchByUserIDjunk($limit, $offset, $apply_filters);
        if (!empty($userlist)) {
            foreach ($userlist as $ul) {
                $ul->login_token = $this->user_lib->getLoginToken($ul->id);
            }
        }
        $this->load->library('admin/user_lib');
        $seller_details = $this->user_lib->getUserjunkList($this->restricted_permissions);
        $this->data['users'] = $seller_details;
        $this->data['userlist'] = $userlist;
        $this->layout('user/junkseller');
    }

    function junksellerCSV()
    {
        $filter = $this->input->get('filter');
        $apply_filters = array();
        if (!empty($filter['start_date'])) {
            $apply_filters['start_date'] = (trim($filter['start_date']) . ' 00:00:00');
        }
        if (!empty($filter['end_date'])) {
            $apply_filters['end_date'] = (trim($filter['end_date']) . ' 23:59:59');
        }
        if (!empty($filter['email'])) {
            $apply_filters['email'] = $filter['email'];
        }
        if (!empty($filter['phone'])) {
            $apply_filters['phone'] = $filter['phone'];
        }

        if ($this->restricted_permissions) {
            $apply_filters['account_manager_id'] = $this->user->user_id;
        }


        $this->data['filter'] = $filter;
        $userlist = $this->user_lib->fetchByUserIDemployee(10000, 0, $apply_filters);
        $filename = 'EmployeeList_' . time() . '.csv';
        header("Content-Description: File Transfer");
        header("Content-Disposition: attachment; filename=$filename");
        header("Content-Type: application/csv; ");
        $file = fopen('php://output', 'w');
        $header = array("Date", "Employee Name", "Email", "Permisssion");
        fputcsv($file, $header);
        foreach ($userlist as $user) {
            $row = array(
                date("j-M-Y", strtotime($user->created)),
                $user->fname,
                $user->email,
                $user->permissions,
            );
            fputcsv($file, $row);
        }
        fclose($file);
        exit;
    }

    function referralform($id = false)
    {
        $this->load->library('form_validation');
        $config = array(
            array(
                'field' => 'codprice',
                'label' => 'COD Price',
                'rules' => 'required|numeric'
            ),
            array(
                'field' => 'prepaidprice',
                'label' => 'Prepaid Price',
                'rules' => 'required|numeric'
            ),
        );
        $this->form_validation->set_rules($config);
        if ($this->form_validation->run()) {
            $change_user_data = array(
                'can_refer' => '1',
            );
            $this->user_lib->change_user_referral($id, $change_user_data);
            $add_refferal_price = array(
                'user_id' => $id,
                'cod' => $this->input->post('codprice'),
                'prepaid' => $this->input->post('prepaidprice'),
            );
            $this->user_lib->add_user_referral($add_refferal_price);

            $log = new Log();
            $log->update($this->user->user_id, $id, "Enabled Referral with Price:  COD: {$add_refferal_price['cod']}, Prepaid: {$add_refferal_price['prepaid']}");

            $this->session->set_flashdata('success', 'Refferral Generated Successfully');
            redirect(base_url('admin/users/viewuser/' . $id));
        } else {
            $this->data['error'] = validation_errors();
            redirect(base_url('admin/users/viewuser/' . $id));
        }
    }

    function referralupdateform($id = false)
    {
        $this->load->library('form_validation');
        $config = array(
            array(
                'field' => 'codupdateprice',
                'label' => 'COD Price',
                'rules' => 'required|numeric'
            ),
            array(
                'field' => 'prepaidupdateprice',
                'label' => 'Prepaid Price',
                'rules' => 'required|numeric'
            ),
        );
        $this->form_validation->set_rules($config);
        if ($this->form_validation->run()) {
            $update_refferal_price = array(
                'user_id' => $id,
                'cod' => $this->input->post('codupdateprice'),
                'prepaid' => $this->input->post('prepaidupdateprice'),
            );
            $this->user_lib->update_user_referral_price($id, $update_refferal_price);

            $log = new Log();
            $log->update($this->user->user_id, $id, "Referral Price:  COD: {$update_refferal_price['cod']}, Prepaid: {$update_refferal_price['prepaid']}");

            $this->session->set_flashdata('success', 'Refferral Price Update Successfully');
            redirect(base_url('admin/users/viewuser/' . $id));
        } else {
            $this->data['error'] = validation_errors();
            redirect(base_url('admin/users/viewuser/' . $id));
        }
    }


    function disablesellerreferrial($id = false)
    {
        $change_user_data = array(
            'can_refer' => '0'
        );
        $this->user_lib->disable_user_referral($id, $change_user_data);

        $log = new Log();
        $log->update($this->user->user_id, $id, "Disabled Referral");

        $this->session->set_flashdata('success', 'Seller Refferral Disable Successfully');
        redirect(base_url('admin/users/viewuser/' . $id));
    }
    function enablesellerreferrial($id = false)
    {
        $change_user_data = array(
            'can_refer' => '1'
        );
        $this->user_lib->enable_user_referral($id, $change_user_data);

        $log = new Log();
        $log->update($this->user->user_id, $id, "Enabled Referral");

        $this->session->set_flashdata('success', 'Seller Refferral Enable Successfully');
        redirect(base_url('admin/users/viewuser/' . $id));
    }

    function sellerpersonaldetailsedit()
    {
        $this->load->library('form_validation');
        $id = $this->input->post('seller_id');
        $newselleremail = $this->input->post('useremail');
        $sellerrecord  = $this->user_lib->checksellerrecord($id);
        $oldemail = $sellerrecord->email;
        if ($oldemail == $newselleremail) {
            $config = array(
                array(
                    'field' => 'firstsellername',
                    'label' => 'First Seller Name',
                    'rules' => 'trim|required'
                ),
                array(
                    'field' => 'lastsellername',
                    'label' => 'Last Seller Name',
                    'rules' => 'trim'
                ),
                array(
                    'field' => 'editcmpname',
                    'label' => 'Company Name',
                    'rules' => 'trim|required'
                ),
                array(
                    'field' => 'useremail',
                    'label' => 'Email',
                    'rules' => 'trim|required',
                ),
                array(
                    'field' => 'editphone',
                    'label' => 'Phone',
                    'rules' => 'trim|required|exact_length[10]|integer|greater_than[0]'
                ),
                array(
                    'field' => 'editcmpphone',
                    'label' => 'Company Phone',
                    'rules' => 'trim|exact_length[10]|integer|greater_than[0]'
                ),
                array(
                    'field' => 'editaddress',
                    'label' => 'Address',
                    'rules' => 'trim'
                ),
                array(
                    'field' => 'editcity',
                    'label' => 'City',
                    'rules' => 'trim'
                ),
                array(
                    'field' => 'editstate',
                    'label' => 'State',
                    'rules' => 'trim'
                ),
                array(
                    'field' => 'editpincode',
                    'label' => 'Pin Code',
                    'rules' => 'trim|numeric'
                ),
                array(
                    'field' => 'editpanno',
                    'label' => 'Pan Number',
                    'rules' => 'trim|exact_length[10]'
                ),
                array(
                    'field' => 'editgstno',
                    'label' => 'GST Number',
                    'rules' => 'trim|exact_length[15]'
                ),
            );
            $this->form_validation->set_rules($config);
            if (!$this->form_validation->run()) {
                $this->data['json'] = array('error' => strip_tags(validation_errors()));
                $this->layout(false, 'json');
                return;
            }
            $cmprecord  = $this->user_lib->checkcmprecord($id);
            if ($cmprecord->user_id != Null) {
                $edit_seller_info = array(
                    'fname'         => $this->input->post('firstsellername'),
                    'lname'         => $this->input->post('lastsellername'),
                    'company_name'  => $this->input->post('editcmpname'),
                    'email'         => $this->input->post('useremail'),
                    'phone'         => $this->input->post('editphone'),
                );
                $edit_personal_data = array(
                    'cmp_email'     => $this->input->post('editcmpemail'),
                    'cmp_phone'     => $this->input->post('editcmpphone'),
                    'cmp_address'   => $this->input->post('editaddress'),
                    'cmp_city'      => $this->input->post('editcity'),
                    'cmp_state'     => $this->input->post('editstate'),
                    'cmp_pincode'   => $this->input->post('editpincode'),
                    'cmp_pan'       => $this->input->post('editpanno'),
                    'cmp_gstno'     => $this->input->post('editgstno'),
                );
                $this->user_lib->update_seller_details($id, $edit_seller_info);
                $this->user_lib->update_seller_personal_details($id, $edit_personal_data);

                $log = new Log();
                $old_details = array(
                    'fname' => $sellerrecord->fname,
                    'lname' => $sellerrecord->lname,
                    'company_name'  => $sellerrecord->company_name,
                    'email'      => $sellerrecord->email,
                    'phone'      => $sellerrecord->phone,
                    'cmp_email'  => $cmprecord->cmp_email,
                    'cmp_phone'  => $cmprecord->cmp_phone,
                    'cmp_address'   => $cmprecord->cmp_address,
                    'cmp_city'      => $cmprecord->cmp_city,
                    'cmp_state'     => $cmprecord->cmp_state,
                    'cmp_pincode'   => $cmprecord->cmp_pincode,
                    'cmp_pan'       => $cmprecord->cmp_pan,
                    'cmp_gstno'     => $cmprecord->cmp_gstno
                );

                $new_details = array_merge($edit_seller_info, $edit_personal_data);
                $json_records = array(
                    'action' => 'Seller Profile updated',
                    'old_details' => $old_details, 'new_details' => $new_details
                );
                $log->update($this->user->user_id, $id, json_encode($json_records));

                $this->data['json'] = array('success' => 'Seller Details Updated Successfully');
                $this->layout(false, 'json');
                return;
            } else {
                $edit_seller_info = array(
                    'fname'         => $this->input->post('firstsellername'),
                    'lname'         => $this->input->post('lastsellername'),
                    'company_name'  => $this->input->post('editcmpname'),
                    'phone'         => $this->input->post('editphone'),
                );

                $insert_data = array(
                    'user_id' => $id,
                    'cmp_email'     => $this->input->post('editcmpemail'),
                    'cmp_phone'     => $this->input->post('editphone'),
                    'cmp_address'   => $this->input->post('editaddress'),
                    'cmp_city'      => $this->input->post('editcity'),
                    'cmp_state'     => $this->input->post('editstate'),
                    'cmp_pincode'   => $this->input->post('editpincode'),
                    'cmp_pan'       => $this->input->post('editpanno'),
                    'cmp_gstno'     => $this->input->post('editgstno'),
                );
                $this->user_lib->update_seller_details($id, $edit_seller_info);
                $this->user_lib->insert_personal($insert_data);

                $log = new Log();

                $new_details = array_merge($edit_seller_info, $insert_data);
                $json_records = array(
                    'action' => 'Seller Details Insert Successfully',
                    'old_details' => '', 'new_details' => $new_details
                );

                $log->update($this->user->user_id, $id, json_encode($json_records));

                $this->data['json'] = array('success' => 'Seller Details Insert Successfully');
                $this->layout(false, 'json');
                return;
            }
        } else {
            $config = array(
                array(
                    'field' => 'firstsellername',
                    'label' => 'First Seller Name',
                    'rules' => 'trim|required'
                ),
                array(
                    'field' => 'lastsellername',
                    'label' => 'Last Seller Name',
                    'rules' => 'trim'
                ),
                array(
                    'field' => 'editcmpname',
                    'label' => 'Company Name',
                    'rules' => 'trim|required'
                ),
                array(
                    'field' => 'useremail',
                    'label' => 'Email',
                    'rules' => 'trim|required|callback_isEmailExist',
                ),
                array(
                    'field' => 'editphone',
                    'label' => 'Phone',
                    'rules' => 'trim|required|exact_length[10]|integer|greater_than[0]'
                ),
                array(
                    'field' => 'editcmpphone',
                    'label' => 'Company Phone',
                    'rules' => 'trim|exact_length[10]|integer|greater_than[0]'
                ),
                array(
                    'field' => 'editaddress',
                    'label' => 'Address',
                    'rules' => 'trim'
                ),
                array(
                    'field' => 'editcity',
                    'label' => 'City',
                    'rules' => 'trim'
                ),
                array(
                    'field' => 'editstate',
                    'label' => 'State',
                    'rules' => 'trim'
                ),
                array(
                    'field' => 'editpincode',
                    'label' => 'Pin Code',
                    'rules' => 'trim|numeric'
                ),
                array(
                    'field' => 'editpanno',
                    'label' => 'Pan Number',
                    'rules' => 'trim|exact_length[10]'
                ),
                array(
                    'field' => 'editgstno',
                    'label' => 'GST Number',
                    'rules' => 'trim|exact_length[15]'
                ),
            );
            $this->form_validation->set_rules($config);
            if (!$this->form_validation->run()) {
                $this->data['json'] = array('error' => strip_tags(validation_errors()));
                $this->layout(false, 'json');
                return;
            }
            $cmprecord  = $this->user_lib->checkcmprecord($id);
            if ($cmprecord->user_id != Null) {
                $edit_seller_info = array(
                    'fname'         => $this->input->post('firstsellername'),
                    'lname'         => $this->input->post('lastsellername'),
                    'company_name'  => $this->input->post('editcmpname'),
                    'email'         => $this->input->post('useremail'),
                    'phone'         => $this->input->post('editphone'),
                );
                $edit_personal_data = array(
                    'cmp_email'     => $this->input->post('editcmpemail'),
                    'cmp_phone'     => $this->input->post('editcmpphone'),
                    'cmp_address'   => $this->input->post('editaddress'),
                    'cmp_city'      => $this->input->post('editcity'),
                    'cmp_state'     => $this->input->post('editstate'),
                    'cmp_pincode'   => $this->input->post('editpincode'),
                    'cmp_pan'       => $this->input->post('editpanno'),
                    'cmp_gstno'     => $this->input->post('editgstno'),
                );
                $this->user_lib->update_seller_details($id, $edit_seller_info);
                $this->user_lib->update_seller_personal_details($id, $edit_personal_data);

                $log = new Log();

                $old_details = array(
                    'fname' => $sellerrecord->fname,
                    'lname' => $sellerrecord->lname,
                    'company_name'  => $sellerrecord->company_name,
                    'email'      => $sellerrecord->email,
                    'phone'      => $sellerrecord->phone,
                    'cmp_email'  => $cmprecord->cmp_email,
                    'cmp_phone'  => $cmprecord->cmp_phone,
                    'cmp_address'   => $cmprecord->cmp_address,
                    'cmp_city'      => $cmprecord->cmp_city,
                    'cmp_state'     => $cmprecord->cmp_state,
                    'cmp_pincode'   => $cmprecord->cmp_pincode,
                    'cmp_pan'       => $cmprecord->cmp_pan,
                    'cmp_gstno'     => $cmprecord->cmp_gstno
                );

                $new_details = array_merge($edit_seller_info, $edit_personal_data);
                $json_records = array(
                    'action' => 'Seller Details Updated Successfully',
                    'old_details' => $old_details, 'new_details' => $new_details
                );
                $log->update($this->user->user_id, $id, json_encode($json_records));

                $this->data['json'] = array('success' => 'Seller Details Updated Successfully');
                $this->layout(false, 'json');
                return;
            } else {
                $edit_seller_info = array(
                    'fname'         => $this->input->post('firstsellername'),
                    'lname'         => $this->input->post('lastsellername'),
                    'company_name'  => $this->input->post('editcmpname'),
                    'phone'         => $this->input->post('editphone'),
                );

                $insert_data = array(
                    'user_id' => $id,
                    'cmp_email'     => $this->input->post('editcmpemail'),
                    'cmp_phone'     => $this->input->post('editphone'),
                    'cmp_address'   => $this->input->post('editaddress'),
                    'cmp_city'      => $this->input->post('editcity'),
                    'cmp_state'     => $this->input->post('editstate'),
                    'cmp_pincode'   => $this->input->post('editpincode'),
                    'cmp_pan'       => $this->input->post('editpanno'),
                    'cmp_gstno'     => $this->input->post('editgstno'),
                );
                $this->user_lib->update_seller_details($id, $edit_seller_info);
                $this->user_lib->insert_personal($insert_data);

                $log = new Log();

                $new_details = array_merge($edit_seller_info, $insert_data);
                $json_records = array(
                    'action' => 'Seller Details Inserted',
                    'old_details' => '', 'new_details' => $new_details
                );
                $log->update($this->user->user_id, $id, json_encode($json_records));

                $this->data['json'] = array('success' => 'Seller Details Insert Successfully');
                $this->layout(false, 'json');
                return;
            }
        }
    }

    public function isEmailExist($newselleremail)
    {
        $this->load->library('form_validation');
        $is_exist = $this->user_lib->isEmailExist($newselleremail);
        if ($is_exist) {
            $this->form_validation->set_message(
                'isEmailExist',
                'Email address is already exist.'
            );
            return false;
        } else {
            return true;
        }
    }

    function upload_cheque_img()
    {
        $chequeimage = $this->uploadFile('chequeimage', 'seller_company_Cheque');
        if (empty($chequeimage)) {
            $this->error = "Unable to upload, please try again";
            $this->data['json'] = array('error' => strip_tags($this->error));
            $this->layout(false, 'json');
            return;
        }
        $this->data['json'] = array('success' => $chequeimage);
        $this->layout(false, 'json');
        return;
    }

    public function  bankdetailsedit()
    {
        $id = $this->input->post('seller_id');
        $this->form_validation->set_message('alpha_numeric_spaces', 'Only Characters, Numbers & Spaces are allowed in %s');
        if (!empty($cmprecord = $this->user_lib->checkcmprecord($id))) {
            $config = array(
                array(
                    'field' => 'cmp_accntholder',
                    'label' => 'Account holder Name',
                    'rules' => 'trim|required'
                ),
                array(
                    'field' => 'cmp_accno',
                    'label' => 'Account Number',
                    'rules' => 'trim|required|alpha_numeric'
                ),
                array(
                    'field' => 'bankacctype',
                    'label' => 'Account Type',
                    'rules' => 'required'
                ),
                array(
                    'field' => 'cmp_bankname',
                    'label' => 'Bank Name',
                    'rules' => 'trim|required'
                ),
                array(
                    'field' => 'cmp_bankbranch',
                    'label' => 'Bank Branch',
                    'rules' => 'trim|required'
                ),
                array(
                    'field' => 'cmp_accifsc',
                    'label' => 'Bank IFSC Code',
                    'rules' => 'trim|required'
                ),
                // array(
                //     'field' => 'uploadedimage',
                //     'label' => 'Cancel Cheque',
                //     'rules' => 'required'
                // ),
            );
            // chequeimage  chequeimage uploadedimage
            $this->form_validation->set_rules($config);
            if ($this->form_validation->run()) {
                $updateaccountdata = array(
                    'cmp_accntholder' => $this->input->post('cmp_accntholder'),
                    'cmp_accno' => $this->input->post('cmp_accno'),
                    'cmp_acctype' => $this->input->post('bankacctype'),
                    'cmp_bankname' => $this->input->post('cmp_bankname'),
                    'cmp_bankbranch' => $this->input->post('cmp_bankbranch'),
                    'cmp_accifsc' => $this->input->post('cmp_accifsc'),
                    //'cmp_chequeimg' => $this->input->post('uploadedimage'),
                    'creation_date' => time(),
                    'modification_date' => time(),
                    'user_id' => $id,
                );

                if (!empty($this->input->post('uploadedimage'))) {
                    $updateaccountdata['cmp_chequeimg'] = $this->input->post('uploadedimage');
                }
                // $this->user_lib->update_sellercmpbank($id, $updateaccountdata);
                // $this->data['json'] = array('success' => 'Updated Bank Details Successfully');
                $this->load->library('Profile_lib');
                $checkProcessingState = $this->profile_lib->checkProcessingState($id);
                if (empty($checkProcessingState)) {
                    $this->profile_lib->insert_bank_verification($updateaccountdata);
                    $this->data['json'] = array('success' => 'Updated Bank Details Successfully');
                } else {
                    $this->data['json'] = array('error' => 'Request is already in processing');
                }

                $log = new Log();
                $old_details = array(
                    'cmp_accntholder' => $cmprecord->cmp_accntholder,
                    'cmp_accno' => $cmprecord->cmp_accno,
                    'cmp_acctype' => $cmprecord->cmp_acctype,
                    'cmp_bankname' => $cmprecord->cmp_acctype,
                    'cmp_bankbranch' => $cmprecord->cmp_bankbranch,
                    'cmp_accifsc' => $cmprecord->cmp_accifsc,
                    'cmp_chequeimg' => $cmprecord->cmp_chequeimg
                );
                $json_records = array(
                    'action' => 'Updated Bank Details',
                    'old_details' => $old_details, 'new_details' => $updateaccountdata
                );
                $log->update($this->user->user_id, $id, json_encode($json_records));

                $this->layout(false, 'json');
                return;
            } else {
                $this->data['json'] = array('error' => strip_tags(validation_errors()));
                $this->layout(false, 'json');
                return;
            }
        } else {
            $this->load->library('form_validation');
            $config = array(
                array(
                    'field' => 'cmp_accntholder',
                    'label' => 'Account holder Name',
                    'rules' => 'trim|required|alpha_numeric_spaces'
                ),
                array(
                    'field' => 'cmp_accno',
                    'label' => 'Account Number',
                    'rules' => 'trim|required|alpha_numeric'
                ),
                array(
                    'field' => 'bankacctype',
                    'label' => 'Account Type',
                    'rules' => 'required'
                ),
                array(
                    'field' => 'cmp_bankname',
                    'label' => 'Bank Name',
                    'rules' => 'trim|required'
                ),
                array(
                    'field' => 'cmp_bankbranch',
                    'label' => 'Bank Branch',
                    'rules' => 'trim|required'
                ),
                array(
                    'field' => 'cmp_accifsc',
                    'label' => 'Bank IFSC Code',
                    'rules' => 'trim|required'
                ),
            );
            $this->form_validation->set_rules($config);
            if ($this->form_validation->run()) {
                $insertaccountdata = array(
                    'user_id' => $id,
                    'cmp_accntholder' => $this->input->post('cmp_accntholder'),
                    'cmp_accno' => $this->input->post('cmp_accno'),
                    'cmp_acctype' => $this->input->post('bankacctype'),
                    'cmp_bankname' => $this->input->post('cmp_bankname'),
                    'cmp_bankbranch' => $this->input->post('cmp_bankbranch'),
                    'cmp_accifsc' => $this->input->post('cmp_accifsc'),
                    // 'cmp_chequeimg' => $this->input->post('uploadedimage'),
                    'creation_date' => time(),
                    'modification_date' => time(),
                );

                if (!empty($this->input->post('uploadedimage'))) {
                    $insertaccountdata['cmp_chequeimg'] = $this->input->post('uploadedimage');
                }

                $this->load->library('Profile_lib');
                $checkProcessingState = $this->profile_lib->checkProcessingState($id);
                if (empty($checkProcessingState)) {
                    $this->profile_lib->insert_bank_verification($insertaccountdata);
                    $this->data['json'] = array('success' => 'Updated Bank Details Successfully');
                } else {
                    $this->data['json'] = array('error' => 'Request is already in processing');
                }
                $this->user_lib->insert_cmpbankdetails($insertaccountdata);
                $this->data['json'] = array('success' => 'Updated Bank Details Successfully');

                $log = new Log();
                $json_records = array(
                    'action' => 'Inserted Bank Details',
                    'old_details' => '', 'new_details' => $insertaccountdata
                );
                $log->create($this->user->user_id, $id, json_encode($json_records));

                $this->layout(false, 'json');
                return;
            } else {
                $this->data['json'] = array('error' => strip_tags(validation_errors()));
                $this->layout(false, 'json');
                return;
            }
        }
    }

    public function upload_soledoc_img()
    {
        $soleimage = $this->uploadFile('soleimage', 'kyc_document');
        if (empty($soleimage)) {
            $this->error = "Unable to upload, please try again";;
            $this->data['json'] = array('error' => strip_tags($this->error));
            $this->layout(false, 'json');
            return;
        }
        $this->data['json'] = array('success' => $soleimage);
        $this->layout(false, 'json');
        return;
    }

    public function upload_doc1()
    {
        $documentinfo = $this->uploadFile('documentinfo1', 'kyc_document_panimage');
        if (empty($documentinfo)) {
            $this->error = "Unable to upload, please try again";
            $this->data['json'] = array('error' => strip_tags($this->error));
            $this->layout(false, 'json');
            return;
        }
        $this->data['json'] = array('success' => $documentinfo);
        $this->layout(false, 'json');
        return;
    }

    public function upload_doc2()
    {
        $documentinfo = $this->uploadFile('documentinfo2', 'kyc_document');
        if (empty($documentinfo)) {
            $this->error = "Unable to upload, please try again";
            $this->data['json'] = array('error' => strip_tags($this->error));
            $this->layout(false, 'json');
            return;
        }
        $this->data['json'] = array('success' => $documentinfo);
        $this->layout(false, 'json');
        return;
    }

    function editsellerkycdetails()
    {
        $id = $this->input->post('seller_id');
        $companytype = $this->input->post('companytype');
        if ($companytype == "Sole Proprietorship") {
            $this->load->library('form_validation');
            $config = array(
                array(
                    'field' => 'document_type',
                    'label' => 'Document Type',
                    'rules' => 'required'
                ),
                array(
                    'field' => 'kycdoc_id',
                    'label' => 'Document Number',
                    'rules' => 'trim|required'
                ),
                array(
                    'field' => 'kycdoc_name',
                    'label' => 'Document Name',
                    'rules' => 'trim|required'
                ),
                array(
                    'field' => 'uploadedsoleimage',
                    'label' => 'Upload Picture',
                    'rules' => 'trim|required'
                ),
            );
            $this->form_validation->set_rules($config);
            if ($this->form_validation->run()) {
                $kycdata = array(
                    'user_id'           => $id,
                    'companytype'       => $companytype,
                    'document_type'     => $this->input->post('document_type'),
                    'kycdoc_id'         => $this->input->post('kycdoc_id'),
                    'kycdoc_name'       => $this->input->post('kycdoc_name'),
                    'documentimage'     => $this->input->post('uploadedsoleimage'),
                    'creation_date'     => time(),
                    'modification_date' => time(),
                );
                $cmprecord = $this->user_lib->checkcmprecord($id);

                $log = new Log();

                if (!empty($cmprecord->user_id)) {
                    $this->user_lib->update_kycdetails($id, $kycdata);

                    $old_details = array(
                        'companytype'  => $cmprecord->companytype,
                        'document_type'  => $cmprecord->document_type,
                        'kycdoc_id'   => $cmprecord->kycdoc_id,
                        'kycdoc_name'      => $cmprecord->kycdoc_name,
                        'documentimage'     => $cmprecord->documentimage
                    );

                    $json_records = array(
                        'action' => 'Sole Kyc Details Updated Successfully',
                        'old_details' => $old_details, 'new_details' => $kycdata
                    );
                    $log->update($this->user->user_id, $id, json_encode($json_records));

                    $this->data['json'] = array('success' => 'Sole Kyc Details Updated Successfully');
                    $this->layout(false, 'json');
                    return;
                } else {
                    $this->user_lib->insert_kycdetails($kycdata);

                    $old_details = array();
                    $json_records = array(
                        'action' => 'Sole Kyc Details Saved Successfully',
                        'old_details' => $old_details, 'new_details' => $kycdata
                    );
                    $log->update($this->user->user_id, $id, json_encode($json_records));

                    $this->data['json'] = array('success' => 'Sole Kyc Details Saved Successfully');
                    $this->layout(false, 'json');
                    return;
                }

                $log = new Log();
            } else {
                $this->data['json'] = array('error' => strip_tags(validation_errors()));
                $this->layout(false, 'json');
                return;
            }
        } else {
            $this->load->library('form_validation');
            $config = array(
                array(
                    'field' => 'cmppanno',
                    'label' => 'Company Pan No',
                    'rules' => 'trim|required'
                ),
                array(
                    'field' => 'cmppanname',
                    'label' => 'Company Pan Name',
                    'rules' => 'trim|required'
                ),
                array(
                    'field' => 'company_document_type',
                    'label' => 'Document Type',
                    'rules' => 'required'
                ),
                array(
                    'field' => 'company_kycdoc_id',
                    'label' => 'Document ID',
                    'rules' => 'trim|required'
                ),
                array(
                    'field' => 'company_kycdoc_name',
                    'label' => 'Document Name',
                    'rules' => 'trim|required'
                ),
                array(
                    'field' => 'documentfile1',
                    'label' => 'First Document',
                    'rules' => 'trim|required'
                ),
                array(
                    'field' => 'documentfile2',
                    'label' => 'Second Document',
                    'rules' => 'trim|required'
                ),
            );
            $this->form_validation->set_rules($config);
            if ($this->form_validation->run()) {
                $kycdata = array(
                    'user_id'               => $id,
                    'companytype'           => $companytype,
                    'document_type'         => $this->input->post('company_document_type'),
                    'kycdoc_id'             => $this->input->post('company_kycdoc_id'),
                    'kycdoc_name'           => $this->input->post('company_kycdoc_name'),
                    'cmppanno'              => $this->input->post('cmppanno'),
                    'cmppanname'            => $this->input->post('cmppanname'),
                    'pancarddocumentimage'  => $this->input->post('documentfile1'),
                    'documentimage'         => $this->input->post('documentfile2'),
                    'creation_date'         => time(),
                    'modification_date'     => time(),
                );
                $cmprecord = $this->user_lib->checkcmprecord($id);

                $log = new Log();
                // $log->update($this->user->user_id, $id, "KYC Details Updated");

                if ($cmprecord->user_id != Null) {
                    $this->user_lib->update_kycdetails($cmprecord->user_id, $kycdata);

                    $old_details = array(
                        'companytype'  => $cmprecord->companytype,
                        'document_type'  => $cmprecord->document_type,
                        'kycdoc_id'   => $cmprecord->kycdoc_id,
                        'kycdoc_name'      => $cmprecord->kycdoc_name,
                        'documentimage'     => $cmprecord->documentimage,
                        'cmppanno'              => $cmprecord->cmppanno,
                        'cmppanname'            => $cmprecord->cmppanname,
                        'pancarddocumentimage'  => $cmprecord->pancarddocumentimage
                    );
                    $json_records = array(
                        'action' => 'KYC Details Updated',
                        'old_details' => $old_details, 'new_details' => $kycdata
                    );
                    $log->update($this->user->user_id, $id, json_encode($json_records));

                    $this->data['json'] = array('success' => 'KYC Details Updated Successfully');
                    $this->layout(false, 'json');
                    return;
                } else {
                    $this->user_lib->insert_kycdetails($kycdata);
                    $this->data['json'] = array('success' => 'KYC Details Saved Successfully');

                    $old_details = array();
                    $json_records = array(
                        'action' => 'KYC Details Saved Successfully',
                        'old_details' => $old_details, 'new_details' => $kycdata
                    );
                    $log->create($this->user->user_id, $id, json_encode($json_records));

                    $this->layout(false, 'json');
                    return;
                }
            } else {
                $this->data['json'] = array('error' => strip_tags(validation_errors()));
                $this->layout(false, 'json');
                return;
            }
        }
    }

    function freeze_remittance($id = false)
    {
        $this->load->library('form_validation');

        $logs = "";


        if (!empty($_POST['eary_cod_charges']) || $_POST['eary_cod_charges'] == 0) {
            $freeze_save['early_cod_charges'] = $_POST['eary_cod_charges'];
            $this->user_lib->change_freeze_plan($id, $freeze_save);
            $log = new Log();
            $log->update($this->user->user_id, $id, $logs);
        }


        if ((isset($_POST['wallet_adjustment_cycle'])) || (isset($_POST['remitence_term']))) {
            $freeze_save = array();
            $wallet_adjustment_cycle = $this->input->post('wallet_adjustment_cycle');
            if (!empty($wallet_adjustment_cycle)) {
                $freeze_save['wallet_adjustment_cycle'] = $wallet_adjustment_cycle;
                $logs .= "Wallet Adjustment Cycle: {$freeze_save['wallet_adjustment_cycle']}";
            }


            if (isset($_POST['remitence_term'])) {
                $settle_wallet = !empty($_POST['remitence_term'][0]) ? $_POST['remitence_term'][0] : "";
                $settle_bank = !empty($_POST['remitence_term'][1]) ? $_POST['remitence_term'][1] : "";

                $remitence = array("settle_wallet" => $settle_wallet, "settle_bank" => $settle_bank);
                $freeze_save['remitence_term'] = serialize($remitence);
                $logs .= " Remitence Term: {$freeze_save['remitence_term']}";
            }
            if (!empty($freeze_save)) {
                $this->user_lib->change_freeze_plan($id, $freeze_save);
                $log = new Log();
                $log->update($this->user->user_id, $id, $logs);
            }
        }
        if ((isset($_POST['freeze_remittance'])) || (isset($_POST['remittance_cycle']))) {
            $config = array(
                array(
                    'field' => 'freeze_remittance',
                    'label' => 'Freeze Remittance',
                    'rules' => 'trim|in_list[0,1]'
                ),
                array(
                    'field' => 'remittance_cycle',
                    'label' => 'Remittance Cycle',
                    'rules' => 'required'
                ),
            );
            $this->form_validation->set_rules($config);
            if ($this->form_validation->run()) {
                $freeze_save = array(
                    'freeze_remittance' => ($this->input->post('freeze_remittance') == '1') ? '1' : '0',
                    'remittance_cycle' => $this->input->post('remittance_cycle'),
                );

                $this->user_lib->change_freeze_plan($id, $freeze_save);

                $log = new Log();
                $log->update($this->user->user_id, $id, "Remittance Cycle: {$freeze_save['remittance_cycle']}, Freeze : {$freeze_save['freeze_remittance']} ");

                $this->data['success'] = "Freeze Remittance Successfully Changed";
                redirect(base_url('admin/users/viewuser/' . $id));
            } else {
                $this->data['error'] = validation_errors();
                redirect(base_url('admin/users/viewuser/' . $id));
            }
        } else {
            $this->data['error'] = validation_errors();
            redirect(base_url('admin/users/viewuser/' . $id));
        }
    }

    function assign_manager()
    {
        $this->load->library('form_validation');
        $config = array(
            array(
                'field' => 'manager_id',
                'label' => 'Account Manager',
                'rules' => 'trim|required|numeric'
            ),
            array(
                'field' => 'user_ids',
                'label' => 'Users',
                'rules' => 'trim|required'
            ),
        );
        $this->form_validation->set_rules($config);
        if (!$this->form_validation->run()) {
            $this->data['json'] = array('error' => strip_tags(validation_errors()));
            $this->layout(false, 'json');
            return;
        }
        $userids = $this->input->post('user_ids');
        $manager_id = $this->input->post('manager_id');
        if (!empty($manager_id) && !empty($userids))
            $exploreids = explode(",", $userids);
        $save = array(
            'account_manager_id' => $manager_id,
        );
        foreach ($exploreids as $id) {
            $this->user_lib->update($id, $save);

            $log = new Log();
            $log->update($this->user->user_id, $id, 'Changed account manager');
        }
        $this->data['json'] = array('success' => 'Success');
        $this->layout(false, 'json');
    }

    function assign_sales()
    {
        $this->load->library('form_validation');
        $config = array(
            array(
                'field' => 'sale_id',
                'label' => 'Sales Person',
                'rules' => 'trim|required|numeric'
            ),
            array(
                'field' => 'user_ids',
                'label' => 'Users',
                'rules' => 'trim|required'
            ),
        );
        $this->form_validation->set_rules($config);
        if (!$this->form_validation->run()) {
            $this->data['json'] = array('error' => strip_tags(validation_errors()));
            $this->layout(false, 'json');
            return;
        }
        $sale_id = $this->input->post('sale_id');
        $user_ids = $this->input->post('user_ids');
        if (!empty($sale_id) && !empty($user_ids))
            $exploreids = explode(",", $user_ids);
        $save = array(
            'sale_manager_id' => $sale_id,
        );
        foreach ($exploreids as $id) {
            $user = $this->user_lib->getByID($id);
            if ($user->sale_manager_id > 0 && !in_array('change_sales_manager', $this->permissions))
                continue;

            $this->user_lib->update($id, $save);
            $log = new Log();
            $log->update($this->user->user_id, $id, 'Changed sales manager');
        }

        $this->data['json'] = array('success' => 'Success');
        $this->layout(false, 'json');
    }

    function lead_source_form()
    {
        $this->load->library('form_validation');
        $config = array(
            array(
                'field' => 'lead_source',
                'label' => 'Lead Source',
                'rules' => 'trim|required'
            ),
            array(
                'field' => 'user_ids',
                'label' => 'Users',
                'rules' => 'trim|required'
            ),
        );
        $this->form_validation->set_rules($config);
        if (!$this->form_validation->run()) {
            $this->data['json'] = array('error' => strip_tags(validation_errors()));
            $this->layout(false, 'json');
            return;
        }
        $lead_source = $this->input->post('lead_source');
        $user_ids = $this->input->post('user_ids');
        if (!empty($lead_source) && !empty($user_ids))
            $exploreids = explode(",", $user_ids);
        $save = array(
            'lead_source' => $lead_source,
        );
        foreach ($exploreids as $id) {
            $this->user_lib->update($id, $save);

            $log = new Log();
            $log->update($this->user->user_id, $id, "Lead Source Updated");
        }

        $this->data['json'] = array('success' => 'Success');
        $this->layout(false, 'json');
    }

    public function pushlead()
    {
        $userids = $this->input->post('ids');
        $exploreids = explode(",", $userids);

        foreach ($exploreids as $id) {
            do_action('users.signup', $id);
            $log = new Log();
            $log->update($this->user->user_id, $id, "Lead Signup");
        }
    }
    public function settinguser()
    {
        $this->load->library('form_validation');
        $id = $this->input->post('seller_id');
        $this->form_validation->set_error_delimiters('', '');
        $this->form_validation->set_rules('projected_shipments', 'Projected shipments', 'numeric');
        if ($this->form_validation->run()) {
            $change_data = array();
            $change_data['projected_shipments'] = $this->input->post('projected_shipments');
            $change_data['ndr_action_type'] = $this->input->post('ndr_action_type');

            $log = new Log();
            $log->update($this->user->user_id, $id, "Projected Shipments to {$change_data['projected_shipments']}, NDR Action Type: {$change_data['ndr_action_type']} ");

            $this->user_lib->update($id, $change_data);
            $this->data['json'] = array('success' => 'User settings successfully update');
            $this->layout(false, 'json');
            return;
        } else {
            $this->data['json'] = array('error' => strip_tags(validation_errors()));
            $this->layout(false, 'json');
            return;
        }
    }
    public function supportcategory()
    {
        $id = $this->input->post('seller_id');
        $change_data = array();
        $change_data['support_category']      = $this->input->post('support_category');
        $change_data['seller_premium_status'] = $this->input->post('seller_premium');
        $seller_cluster = $this->input->post('seller_cluster');
        if (!empty($seller_cluster)) {
            $seller_clusters = $this->config->item('seller_clusters');
            foreach ($seller_clusters as $sc_key => $sc) {
                if (array_key_exists($seller_cluster, $sc)) {
                    $change_data['seller_region'] = $sc_key;
                    $change_data['seller_territory'] = $seller_cluster;
                }
            }
        }
        $seller_premium = (empty($change_data['seller_premium_status'])) ? 0 : $change_data['seller_premium_status'];

        $this->user_lib->update($id, $change_data);

        $log = new Log();
        $log->update($this->user->user_id, $id, "Support Category: {$change_data['support_category']} Seller Premium Status:{$seller_premium}");

        if (empty($change_data['seller_premium_status'])) {

            $msg = "Support Services successfully update";
        } else {
            $msg = "Premium Seller successfully update";
        }
        $this->data['json'] = array('status' => 1, 'success' => $msg);
        $this->layout(false, 'json');
        return true;
    }


    public function notesdetailsedit()
    {
        $this->load->library('form_validation');
        $config = array(
            array(
                'field' => 'issue',
                'label' => 'Issue',
                'rules' => 'trim|required'
            ),
            array(
                'field' => 'remarks',
                'label' => 'Remarks',
                'rules' => 'trim|required'
            ),
        );
        $this->form_validation->set_rules($config);
        if ($this->form_validation->run()) {
            $insertnote = array(
                'user_id' => $this->input->post('seller_id'),
                'category_issue' => $this->input->post('issue'),
                'remarks' => htmlentities($this->input->post('remarks')),
                'by_user_id' => $this->input->post('user_id'),
                'created' => time(),
            );
            $this->user_lib->insert_notesdetails($insertnote);

            $log = new Log();
            $log->create($this->user->user_id, $this->input->post('seller_id'), "Notes Details Inserted");

            $this->data['json'] = array('success' => 'Record has been successfully saved');
            $this->layout(false, 'json');
            return;
        } else {
            $this->data['json'] = array('error' => strip_tags(validation_errors()));
            $this->layout(false, 'json');
            return;
        }
    }
    function duplicate($page = 1)
    {
        $per_page = $this->input->get('perPage');
        if (empty($per_page) || !is_numeric($per_page))
            $limit = 50;
        else
            $limit = $per_page;
        $filter = $this->input->get('filter');
        $apply_filters = array();
        $total_row = $this->user_lib->countByDuplicateUser($apply_filters);
        $config = array(
            'base_url' => base_url('admin/users/duplicate'),
            'total_rows' => $total_row,
            'per_page' => $limit,
            'num_links' => '3',
            'use_page_numbers' => true,
            'reuse_query_string' => true,
            'cur_tag_open' => '<li class="paginate_button page-item active"><a class="page-link" href="' . current_url() . '">',
            'cur_tag_close' => '</a></li>',
            'num_tag_open' => '<li class="paginate_button page-item ">',
            'num_tag_close' => '</li>',
            'attributes' => array(
                'aria-controls' => 'example-multi',
                'tabindex' => '0',
                'class' => 'page-link',
            ),
            'next_link' => 'Next',
            'prev_link' => 'Prev',
        );
        $this->load->library("pagination");
        $this->pagination->initialize($config);
        $this->data["pagination"] = $this->pagination->create_links();
        $offset = $limit * ($page - 1);
        $this->data['total_records'] = $total_row;
        $this->data['limit'] = $limit;
        $this->data['offset'] = $offset;
        $userlist = $this->user_lib->fetchByDuplicateUser($limit, $offset, $apply_filters);

        $this->data['userlist'] = $userlist;
        $this->layout('user/duplicate');
    }

    private function uploadFile($variable_name = null, $folder_name = null, $image_only = false)
    {
        if ($variable_name == null || $folder_name == null) {
            return '';
        }
        $returnval = '';
        $extension = strtolower(pathinfo($_FILES[$variable_name]['name'], PATHINFO_EXTENSION));

        $new_name = time() . rand(1111, 9999) . '.' . ($extension);

        if (($image_only && in_array($extension, $this->allowed_image_extension)) || (!$image_only && (in_array($extension, $this->allowed_image_extension) || in_array($extension, $this->allowed_document_extension)))) {
            $config['file_name'] = $new_name;

            $fileTempName = $_FILES[$variable_name]['tmp_name'];
            $image_name = $new_name;

            $file_name = $this->s3->amazonS3Upload($image_name, $fileTempName, $folder_name);
            if ($file_name) {
                $returnval = $file_name;
            }
        }
        return $returnval;
    }

    function update_ivr_charge_status($user_id = null)
    {
        if (empty($user_id)) {
            $this->session->set_flashdata('error', 'IVR charge could not updated');
            redirect(base_url('admin/users/viewuser/' . $user_id));
        }
        $val = 0;
        if (!empty($this->input->post('chargeable')) && $this->input->post('chargeable') == 'on') {
            $val = 1;
        }
        $allotted_free_minutes = ($val == 1) ? $this->config->item('free_unit') : 0;
        $this->load->library('user_lib');

        $this->user_lib->update($user_id, array('ivr_call_chargeable' => $val, 'allotted_free_minutes' => $allotted_free_minutes));
        $this->session->set_flashdata('success', 'IVR Call Charge Updated Successfully');
        redirect(base_url('admin/users/viewuser/' . $user_id));
    }
    function update_whatsapp_status($user_id = null)
    {
        if (empty($user_id)) {
            $this->session->set_flashdata('error', 'Whatsapp notification could not updated');
            redirect(base_url('admin/users/viewuser/' . $user_id));
        }
        $val = 0;
        if (!empty($this->input->post('whatsapp_status')) && $this->input->post('whatsapp_status') == 'on') {
            $val = 1;
        }
        //$allotted_free_minutes = ($val == 1) ? $this->config->item('free_unit') : 0;
        $this->load->library('user_lib');

        $this->user_lib->update($user_id, array('whatsapp_enable' => $val, 'wp_enable_date' => time()));
        if($val=='0')
        {
            $this->user_lib->update_whatsapp_notification($user_id);
        }

        $this->session->set_flashdata('success', 'Whatsapp Notification Updated Successfully');
        redirect(base_url('admin/users/viewuser/' . $user_id));
    }

    function loguserId()
    {
        $user_id = $this->input->post('user_id');

        $log = new Log();
        $log->update($this->user->user_id, $user_id, 'User Logged In');
    }
    function ndrcalledit()
    {

        $seller_id = $this->input->post('seller_id');
        $admin_id = $this->user->user_id;
        $this->load->library('user_lib');
        $res = $this->user_lib->ndr_call_seller_set($seller_id, $admin_id);

        if ($res == 'udpated')
            $this->data['json'] = array('success' => 'Record has been successfully updated');
        else if ($res == 'added')
            $this->data['json'] = array('success' => 'Record has been successfully saved');
        else
            $this->data['json'] = array('error' => 'Error while processing data');

        $this->layout(false, 'json');
        return;
    }

    function save_tat_weight($id = false)
    {

        $data_exit = $this->user_lib->get_dispute_time_limit($id);

        if (!empty($data_exit)) {
            $update_data = array(
                'time_limt' => $_POST['tat_time_limt'],
                'updated' => time(),
            );
            $this->user_lib->update_tat_weight($data_exit->id, $update_data);
            $this->session->set_flashdata('success', 'Added Succesfully');
            redirect(base_url('admin/users/viewuser/' . $id));
        } else {

            $insert_data = array(
                'user_id' => $id,
                'time_limt' => $_POST['tat_time_limt'],
                'created' => time(),
                'updated' => time(),
            );
            $this->user_lib->insert_tat_weight($insert_data);
            $this->session->set_flashdata('success', 'Added Succesfully');
            redirect(base_url('admin/users/viewuser/' . $id));
        }
    }

    function int_assign_sales()
    {
        $this->load->library('form_validation');
        $config = array(
            array(
                'field' => 'sale_id',
                'label' => 'Sales Person',
                'rules' => 'trim|required|numeric'
            ),
            array(
                'field' => 'user_ids',
                'label' => 'Users',
                'rules' => 'trim|required'
            ),
        );
        $this->form_validation->set_rules($config);
        if (!$this->form_validation->run()) {
            $this->data['json'] = array('error' => strip_tags(validation_errors()));
            $this->layout(false, 'json');
            return;
        }
        $sale_id = $this->input->post('sale_id');
        $user_ids = $this->input->post('user_ids');



        if (!empty($sale_id) && !empty($user_ids))
            $exploreids = explode(",", $user_ids);
        $save = array(
            'international_sale_manager_id' => $sale_id,
        );

        $sucess = '';
        foreach ($exploreids as $id) {
            $user = $this->user_lib->getByID($id);
            if ($user->international_sale_manager_id > 0 && !in_array('change_int_sales_manager', $this->permissions)) {
                $sucess = 'no';
            } else {

                $this->user_lib->update($id, $save);
                $log = new Log();
                $log->update($this->user->user_id, $id, 'Changed International sales manager');
            }
        }

        if ($sucess == 'no') {
            $this->data['json'] = array('error' => "You don't have Edit permission");
        } else {
            $this->data['json'] = array('success' => 'Success');
        }

        $this->layout(false, 'json');
    }

    function b2b_assign_sales()
    {
        $this->load->library('form_validation');
        $config = array(
            array(
                'field' => 'sale_id',
                'label' => 'Sales Person',
                'rules' => 'trim|required|numeric'
            ),
            array(
                'field' => 'user_ids',
                'label' => 'Users',
                'rules' => 'trim|required'
            ),
        );
        $this->form_validation->set_rules($config);
        if (!$this->form_validation->run()) {
            $this->data['json'] = array('error' => strip_tags(validation_errors()));
            $this->layout(false, 'json');
            return;
        }
        $sale_id = $this->input->post('sale_id');
        $user_ids = $this->input->post('user_ids');



        if (!empty($sale_id) && !empty($user_ids))
            $exploreids = explode(",", $user_ids);
        $save = array(
            'b2b_sale_manager_id' => $sale_id,
        );

        $sucess = '';
        foreach ($exploreids as $id) {
            $user = $this->user_lib->getByID($id);
            if ($user->b2b_sale_manager_id > 0 && !in_array('change_b2b_sales_manager', $this->permissions)) {
                $sucess = 'no';
            } else {

                $this->user_lib->update($id, $save);
                $log = new Log();
                $log->update($this->user->user_id, $id, 'Changed B2B sales manager');
            }
        }

        if ($sucess == 'no') {
            $this->data['json'] = array('error' => "You don't have Edit permission");
        } else {
            $this->data['json'] = array('success' => 'Success');
        }

        $this->layout(false, 'json');
    }

    function set_training_manager()
    {
        $this->load->library('form_validation');
        $config = array(
            array(
                'field' => 'sale_id',
                'label' => 'Sales Manager',
                'rules' => 'trim|required|numeric'
            ),
            array(
                'field' => 'user_ids',
                'label' => 'Users',
                'rules' => 'trim|required'
            ),
        );
        $this->form_validation->set_rules($config);
        if (!$this->form_validation->run()) {
            $this->data['json'] = array('error' => strip_tags(validation_errors()));
            $this->layout(false, 'json');
            return;
        }
        $sale_id = $this->input->post('sale_id');
        $user_ids = $this->input->post('user_ids');



        if (!empty($sale_id) && !empty($user_ids))
            $exploreids = explode(",", $user_ids);
        $save = array(
            'training_manager_id' => $sale_id,
        );

        $sucess = '';
        foreach ($exploreids as $id) {
            $user = $this->user_lib->getByID($id);
            if ($user->training_manager_id > 0 && !in_array('change_training_manager', $this->permissions)) {
                $sucess = 'no';
            } else {

                $this->user_lib->update($id, $save);
                $log = new Log();
                $log->update($this->user->user_id, $id, 'Changed training manager');
            }
        }

        if ($sucess == 'no') {
            $this->data['json'] = array('error' => "You don't have Edit permission");
        } else {
            $this->data['json'] = array('success' => 'Success');
        }

        $this->layout(false, 'json');
    }

    public function enable_user_view($view_contact_id, $type)
    {
        $this->user_lib->enable_contact($this->user->user_id, $view_contact_id, $type);
        $url = ($type == 'user_login') ? $_SERVER['HTTP_REFERER'] : base_url("admin/users/viewuser/$view_contact_id");
        redirect($url);
    }
    function legalentityupdate()
    {
        $userid = $this->input->post('seller_id');
        $cmprecord = $this->user_lib->getLegalDetailsByUserId($userid);
        $this->load->library('form_validation');

        $config = array(
            array(
                'field' => 'legal_name',
                'label' => 'Company Name',
                'rules' => 'trim|required'
            ),

            array(
                'field' => 'legal_address',
                'label' => 'Company Address',
                'rules' => 'trim|required'
            ),
            array(
                'field' => 'legal_city',
                'label' => 'City',
                'rules' => 'trim|required'
            ),
            array(
                'field' => 'legal_state',
                'label' => 'State',
                'rules' => 'trim|required'
            ),
            array(
                'field' => 'legal_pincode',
                'label' => 'Pincode',
                'rules' => 'trim|required'
            ),
            array(
                'field' => 'type',
                'label' => 'Registered GST Entity?',
                'rules' => 'trim|required'
            )

        );
        if ($this->input->post('type') == '1') {
            $config[] = array(
                'field' => 'legal_gstno',
                'label' => 'Legal Entity GST No',
                'rules' => 'trim|alpha_numeric|exact_length[15]|required'
            );
        } else {
            $config[] = array(
                'field' => 'legal_gstno',
                'label' => 'Legal Entity GST No',
                'rules' => 'trim|alpha_numeric|exact_length[15]'
            );
        }
        $this->form_validation->set_rules($config);
        if ($this->form_validation->run()) {
            $cmpdata = array(
                'user_id'             => $userid,
                'legal_name'         => $this->input->post('legal_name'),
                'legal_gstno'         => $this->input->post('legal_gstno'),
                'legal_address'        => $this->input->post('legal_address'),
                'legal_city'        => $this->input->post('legal_city'),
                'legal_state'        => $this->input->post('legal_state'),
                'legal_pincode'        => $this->input->post('legal_pincode'),
                'type'              => $this->input->post('type'),
                'created'     => time(),
                'modified' => time(),
            );


            if (!empty($cmprecord)) {

                $this->user_lib->updateLegalEntity($userid, $cmpdata);

                // $log = new Log();
                // $old_details = array(
                // 	'legal_name' 		=> $this->input->post('legal_name'),
                // 	'legal_gstno' 		=> $this->input->post('legal_gstno'),
                // );
                // $log = new Log();
                // $old_details = array(
                // 	'cmp_url' 			=> $cmprecord->cmp_phone,
                // 	'cmp_email' 		=> $cmprecord->cmp_email,
                // 	'cmp_phone'  => $cmprecord->cmp_phone,
                // 	'cmp_address'   => $cmprecord->cmp_address,
                // 	'cmp_city'      => $cmprecord->cmp_city,
                // 	'cmp_state'     => $cmprecord->cmp_state,
                // 	'cmp_pincode'   => $cmprecord->cmp_pincode,
                // 	'cmp_pan'       => $cmprecord->cmp_pan,
                // 	'cmp_gstno'     => $cmprecord->cmp_gstno
                // );
                //  $json_records = array('action' => 'Seller Profile updated', 'old_details' => $old_details, 'new_details' => $cmpdata);
                //$log->update($userid, $userid, json_encode($json_records));

                $this->data['json'] = array('success' => 'Updated Legal Entity Details Successfully');
            } else {
                $this->user_lib->insertLegalEntity($cmpdata);
                // $log = new Log();
                // $json_records = array('action' => 'Seller Legal Entity Saved', 'old_details' => '', 'new_details' => $cmpdata);
                // $log->create($userid, $userid, json_encode($json_records));

                $this->data['json'] = array('success' => 'Saved Legal Entity Details Successfully');
            }
        } else {
            $this->data['json'] = array('error' => strip_tags(validation_errors()));
        }
        $this->layout(false, 'json');
        return;
    }


    function account_master_id($id = false)
    {
        $this->load->library('form_validation');
        $account_masterid = $this->input->post('account_master');
        $config = array(
            array(
                'field' => 'account_master',
                'label' => 'Account Master Id',
                'rules' => 'required'
            ),
        );
        $this->form_validation->set_rules($config);
        if ($this->form_validation->run()) {

            // echo "=---->".$account_masterid;
            $parent_id = $this->getparent_id($account_masterid);

            //echo "New Parent id= ".$parent_id; die;
            $this->user_lib->insertaccountmasterid($id, $parent_id);

            $log = new Log();
            $log->update($this->user->user_id, $id, " Change Account Master to: " . $parent_id);

            $this->session->set_flashdata('success', 'Account Master Id Added Successfully');
            redirect(base_url('admin/users/viewuser/' . $id));
        } else {
            $this->data['error'] = validation_errors();
            redirect(base_url('admin/users/viewuser/' . $id));
        }
    }

    //function to get the master account Id
    function getparent_id($master_id)
    {
        $data =  $this->user_lib->get_master_id($master_id);
        if (!empty($data) && !empty($data[0]->account_master_id)) {
            if ($master_id == $data[0]->account_master_id) {
                return $master_id;
            } else {
                return $this->getparent_id($data[0]->account_master_id);
            }
        } else {
            return $data[0]->id;
        }
    }

    function getparent_data($master_id)
    {
        $resdata = array();
        $data =  $this->user_lib->getallparent_id($master_id);

        $resdata[] = $data;
        if (!empty($data) && !empty($data[0]->account_master_id)) {
            if ($master_id == $data[0]->account_master_id) { //echo "1</br>";
                //$resdata[]=$data;
            } else {
                $resdata[] = $this->getparent_data($data[0]->account_master_id);
            }
        }

        return $resdata;
    }

    // function getparent_data1_org($master_id)
    // {  
    //     $array1 = array();
    //     $array2 = array();

    //     $data=  $this->user_lib->getallparent_id1($master_id);
    //     $array1[]=$data;

    //      foreach($data as $dt)
    //      {
    //         $resdata =$this->user_lib->getallparent_id($dt->account_master_id);
    //         $array2[] = $resdata;
    //      }

    //      return array_merge($array1,$array2);
    // }


    function getparent_data1($master_id)
    {
        $array1 = array();

        $array1[] = $master_id;

        $array2 = array();

        $data =  $this->user_lib->getallparent_id1($master_id);
        foreach ($data as $dt) {

            $array1[] = $dt->id;
            if($dt->id!= $master_id){
                $dat = $this->getparent_data1($dt->id);
            }

            if (!empty($dat)) {
                foreach ($dat as $a) {
                    $array2[] = $a;
                }
            }
        }

        return array_merge($array1, $array2);
    }

     public function get_active_couriers()
    {
        $couriers = $this->Courier_model->get_active_couriers(); // status = 1
        echo json_encode($couriers);
    }


public function update_disabled_couriers()
{
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);

    $this->load->model('Courier_model');

    $user_id = $this->input->post('user_id');
    $enabled = $this->input->post('enabled_couriers') ?? [];

    $all_active = $this->Courier_model->get_active_courier_ids();
    $disabled = array_diff($all_active, $enabled);

    $this->Courier_model->update_user_disabled_couriers($user_id, $disabled);

    echo json_encode(['status' => 'success', 'message' => 'Updated successfully']);
}





public function get_user_disabled_couriers($user_id)
{
    $this->load->model('Courier_model');

    $disabled = $this->Courier_model->get_user_disabled_couriers($user_id);

    echo json_encode($disabled);
}

}
