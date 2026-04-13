<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Setting extends User_controller
{

    public function __construct()
    {
        parent::__construct();
        $this->load->library('Setting_lib');
    }

    function main()
    {
        $this->layout('setting/front');
    }

    public function index()
    {
        $userid = $this->user->user_id;
        $this->load->library('form_validation');
        $newcell = $this->input->post('phone');
        $currentcell = $this->setting_lib->checkphone($userid);
        if($newcell == $currentcell->phone)
        {
            $config = array(
                array(
                    'field' => 'fname',
                    'label' => 'First Name',
                    'rules' => 'trim|required|min_length[2]|max_length[20]|alpha_numeric_spaces'
                ),
                array(
                    'field' => 'lname',
                    'label' => 'Last Name',
                    'rules' => 'trim|min_length[2]|max_length[20]|alpha_numeric_spaces'
                ),
                array(
                    'field' => 'phone',
                    'label' => 'Contact Number',
                    'rules' => 'trim|required|exact_length[10]|numeric'
                ),
            );
            if (!empty($this->input->post('currentpassword')) || !empty($this->input->post('checkpassword'))) {
                $config[] = array(
                    'field' => 'currentpassword',
                    'label' => 'New Password',
                    'rules' => 'trim|min_length[6]|max_length[50]'
                );
                $config[] = array(
                    'field' => 'checkpassword',
                    'label' => 'Confirm Password',
                    'rules' => 'trim|min_length[6]|max_length[50]|matches[currentpassword]'
                );
            }
            $this->form_validation->set_rules($config);
            if ($this->form_validation->run()) {
                $save_data = array(
                    'fname' => $this->input->post('fname'),
                    'lname' => $this->input->post('lname'),
                    'phone' => $this->input->post('phone'),
                );
                if (!empty($this->input->post('currentpassword'))) {
                    $save_data['password'] = SHA1($this->input->post('currentpassword'));
                }
                if (!$this->setting_lib->update_user($userid, $save_data)) {
                    $this->data['error'] = $this->setting_lib->get_error();
                } else {
                    $this->session->set_flashdata('success', 'Account Details Updated Successfully');
                    redirect(base_url('setting'));
                }
            } else {
                $this->data['error'] = validation_errors();
            }
        }
        else
        {
            $config = array(
                array(
                    'field' => 'fname',
                    'label' => 'First Name',
                    'rules' => 'trim|required|min_length[2]|max_length[20]|alpha_numeric_spaces'
                ),
                array(
                    'field' => 'lname',
                    'label' => 'Last Name',
                    'rules' => 'trim|required|min_length[2]|max_length[20]|alpha_numeric_spaces'
                ),
                array(
                    'field' => 'phone',
                    'label' => 'Contact Number',
                    'rules' => 'trim|required|exact_length[10]|numeric|callback_isPhoneExist'
                ),
            );
            if (!empty($this->input->post('currentpassword')) || !empty($this->input->post('checkpassword'))) {
                $config[] = array(
                    'field' => 'currentpassword',
                    'label' => 'New Password',
                    'rules' => 'trim|min_length[6]|max_length[50]'
                );
                $config[] = array(
                    'field' => 'checkpassword',
                    'label' => 'Confirm Password',
                    'rules' => 'trim|min_length[6]|max_length[50]|matches[currentpassword]'
                );
            }
            $this->form_validation->set_rules($config);
            if ($this->form_validation->run()) {
                $save_data = array(
                    'fname' => $this->input->post('fname'),
                    'lname' => $this->input->post('lname'),
                    'phone' => $this->input->post('phone'),
                );
                if (!empty($this->input->post('currentpassword'))) {
                    $save_data['password'] = SHA1($this->input->post('currentpassword'));
                }
                if (!$this->setting_lib->update_user($userid, $save_data)) {
                    $this->data['error'] = $this->setting_lib->get_error();
                } else {
                    $this->session->set_flashdata('success', 'Account Details Updated Successfully');
                    redirect(base_url('setting'));
                }
            } else {
                $this->data['error'] = validation_errors();
            }

        }

        
        $setting = $this->setting_lib->getsettingByUserID($this->user->user_id);
        $this->data['setting'] = $setting;
        $this->layout('setting/index');
    }

    public function isPhoneExist($newcell){
		
		$this->load->library('form_validation');
        $is_exist = $this->setting_lib->isPhoneExist($newcell);
        if ($is_exist) {
            $this->form_validation->set_message('isPhoneExist','Contact Number is already exist.');
            return false;
        } else {
            return true;
        }
	}

    function label()
    {
        $this->userHasAccess('shipments');
        $this->load->library('user_lib');
        $user = $this->user_lib->getByID($this->user->user_id);

        //get user autoship filters
        $this->load->library('form_validation');
        $config = array(
            array(
                'field' => 'label_format',
                'label' => 'Label Format',
                'rules' => 'trim|required|in_list[thermal,standard]'
            ),
        );
        $this->form_validation->set_rules($config);
        if ($this->form_validation->run()) {
            $save = array(
                'label_format' => $this->input->post('label_format'),
            );
            $this->user_lib->update($this->user->user_id, $save);
            $this->data['success'] = 'Label Settings Saved';
        } else {
            $this->data['error'] = validation_errors();
        }

        $this->data['label_format'] = $user->label_format;

        $this->layout('setting/label');
    }
    
    function master_label()
    {
        $this->load->library('node_apis/user_node_lib');
        $this->userHasAccess('shipments');

        $master_settings = $this->setting_node_lib->master_label_setting_find($this->user->user_id);

        $logo =  "";
        if (!empty($_FILES['custom_logo_url']['name'])) {

            $filePath = $_FILES['custom_logo_url']['tmp_name'];
            $type = $_FILES['custom_logo_url']['type'];
            $fileName = $_FILES['custom_logo_url']['name'];

            $data = array('dir_name' => 'label_logos', 'document' => curl_file_create($filePath, $type, $fileName));
            $logo = $this->user_node_lib->user_upload($this->user->user_id, $data);

            if ($logo->status == 1)
                $logo = !empty($logo->data) ? $logo->data : '';
        }

        $this->load->library('form_validation');
        $config = array(
            array(
                'field' => 'support_email',
                'label' => 'Support Email',
                'rules' => 'trim|valid_email'
            ),
            array(
                'field' => 'support_mobile',
                'label' => 'Support Mobile Number',
                'rules' => 'trim|min_length[8]|max_length[15]|numeric'
            ),
            array(
                'field' => 'sku_char_limit',
                'label' => 'SKU Character Limit',
                'rules' => 'trim|numeric|max_length[8]|greater_than[0]'
            ),
            array(
                'field' => 'item_name_char_limit',
                'label' => 'Item Name Character Limit',
                'rules' => 'trim|numeric|max_length[8]|greater_than[0]'
            ),
            array(
                'field' => 'no_of_line_items',
                'label' => 'Number of line items',
                'rules' => 'trim|numeric|max_length[8]|greater_than[0]'
            ),
        );
        $this->form_validation->set_rules($config);
        if ($this->form_validation->run()) {
            $save = array(
                'custom_logo_url'      => (empty($logo)) ? $master_settings->custom_logo_url : $logo,
                'show_logo'            => ($this->input->post('show_logo') == '1') ? '1' : '0',
                'show_channel_logo'    => ($this->input->post('show_channel_logo') == '1') ? '1' : '0',
                'show_custom_logo'     => (!empty($logo) || !empty($master_settings->custom_logo_url)) ? '1' : '0',
                'show_support_email'   => ($this->input->post('show_support_email') == '1') ? '1' : '0',
                'show_support_mobile'  => ($this->input->post('show_support_email') == '1') ? '1' : '0',
                'support_email'        => $this->input->post('support_email'),
                'support_mobile'       => $this->input->post('support_mobile'),
                'warehouse_support_email_mobile' => ($this->input->post('warehouse_support_email_mobile') == '1') ? '1' : '0',
                'hide_customer_mobile'  => ($this->input->post('hide_customer_mobile') == '1') ? '1' : '0',
                'hide_warehouse_address' => ($this->input->post('hide_warehouse_address') == '1') ? '1' : '0',
                'hide_warehouse_mobile' => ($this->input->post('hide_warehouse_mobile') == '1') ? '1' : '0',
                'hide_rto_address'      => ($this->input->post('hide_rto_address') == '1') ? '1' : '0',
                'hide_rto_mobile'       => ($this->input->post('hide_rto_mobile') == '1') ? '1' : '0',
                'hide_gst_no'           => ($this->input->post('hide_gst_no') == '1') ? '1' : '0',
                'hide_contact_name'     => ($this->input->post('hide_contact_name') == '1') ? '1' : '0',
                'hide_rto_contact_name' => ($this->input->post('hide_rto_contact_name') == '1') ? '1' : '0',
                'hide_discount'         => ($this->input->post('hide_discount') == '1') ? '1' : '0',
                'hide_sku'              => ($this->input->post('hide_sku') == '1') ? '1' : '0',
                'hide_item_name'        => ($this->input->post('hide_item_name') == '1') ? '1' : '0',
                'hide_qty'              => ($this->input->post('hide_qty') == '1') ? '1' : '0',
                'hide_item_amount'      => ($this->input->post('hide_item_amount') == '1') ? '1' : '0',
                'hide_order_amount'     => ($this->input->post('hide_order_amount') == '1') ? '1' : '0',
                'order_amount_cod'      => ($this->input->post('order_amount_cod') == '1') ? '1' : '0',
                'order_amount_prepaid'  => ($this->input->post('order_amount_prepaid') == '1') ? '1' : '0',
                'sku_char_limit'        => !empty($this->input->post('sku_char_limit')) ? $this->input->post('sku_char_limit') : '0',
                'item_name_char_limit'  => !empty($this->input->post('item_name_char_limit')) ? $this->input->post('item_name_char_limit') : '0',
                'no_of_line_items'      => !empty($this->input->post('no_of_line_items')) ? $this->input->post('no_of_line_items') : '0',
            );
            
            $this->setting_node_lib->master_label_setting_createupdate($this->user->user_id, $save);
            $this->data['success'] = 'Label Settings Saved';

            $master_settings = $this->setting_node_lib->master_label_setting_find($this->user->user_id);
        } else {
            $this->data['error'] = validation_errors();
        }

        $this->data['master_setting'] = $master_settings;

        return $this->load->view('setting/master_label', $this->data, true);
    }
    function pincodes()
    {
        $this->load->library('form_validation');
        $config = array(
            array(
                'field' => 'pickup_pincode',
                'label' => 'Pickup Pincode',
                'rules' => 'trim|required|numeric|exact_length[6]'
            ),
        );
        $this->form_validation->set_rules($config);

        if ($this->form_validation->run()) {
            $pickup = $this->input->post('pickup_pincode');
            $this->load->library('pincode_lib');
            $this->load->library('pricing_lib');
            $query = $this->pincode_lib->serviceablePincodesList();
            $this->load->library('export_db');

            $export = new Export_db();
            $export->query($query);
            $available = array();
            while ($pin = $export->next()) {

                if (!array_key_exists($pin->pincode, $available))
                    $available[$pin->pincode] = $pin;
                else {
                    if ($pin->cod == 'Y')
                        $available[$pin->pincode]->cod = 'Y';
                    if ($pin->prepaid == 'Y')
                        $available[$pin->pincode]->prepaid = 'Y';

                    if ($pin->pickup == 'Y')
                        $available[$pin->pincode]->pickup = 'Y';
                }
            }

            $filename = 'Pincodes.csv';
            header("Content-Description: File Transfer");
            header("Content-Disposition: attachment; filename=$filename");
            header("Content-Type: application/csv; ");
            $file = fopen('php://output', 'w');
            $header = array("Pincode", "City", "State", "COD Delivery", "Prepaid Delivery", "Pickup", "Zone");
            fputcsv($file, $header);
            foreach ($available as $pincode) {
                $pricing = new Pricing_lib();
                $pricing->setOrigin($pickup);
                $pricing->setDestination($pincode->pincode);
                $pincode->zone = $pricing->calculateZone();
                $row = array(
                    $pincode->pincode,
                    ucwords($pincode->city),
                    ucwords($pincode->state_code),
                    $pincode->cod,
                    $pincode->prepaid,
                    $pincode->pickup,
                    ucwords($pincode->zone),
                );
                fputcsv($file, $row);
            }
            fclose($file);
            exit;
        } else {
            $this->data['error'] = validation_errors();
        }
        $this->layout('setting/pincodes');
    }
}
