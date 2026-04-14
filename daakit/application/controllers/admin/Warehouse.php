<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Warehouse extends User_controller
{

    public function __construct()
    {
        parent::__construct();
        $this->load->library('warehouse_lib');
        $this->userHasAccess('settings');
    }

    public function index($page = 1)
    {
        $per_page = $this->input->get('perPage');
        if (empty($per_page) || !is_numeric($per_page))
            $limit = 50;
        else
            $limit = $per_page;

        $total_row = $this->warehouse_lib->countByUserID($this->user->account_id);

        $config = array(
            'base_url' => base_url('warehouse/index'),
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

        $warehouses = $this->warehouse_lib->getUserWarehouseData($this->user->account_id, $limit, $offset);

        $this->data['warehouses'] = $warehouses;
        $this->layout('warehouse/index');
    }

    function add($id = false)
    {
        $selected_state = $this->input->post('state');
        $warehouse = array();
        $order_exists = false;

        if ($id) {
            $warehouse = $this->warehouse_lib->getByID($id);

            if (empty($warehouse) || $warehouse->user_id != $this->user->account_id) {
                $this->session->set_flashdata('error', 'No Records Found');
                redirect('warehouse', true);
            }

            //check if warehouse is having existing orders
            $this->load->library('shipping_lib');
            $order_exists = $this->shipping_lib->checkIfWarehouseShipmentExists($this->user->account_id, $id);
        }
        $this->load->library('form_validation');
        $config = array(
            array(
                'field' => 'hide_label_address',
                'label' => 'Hide Warehouse Address',
                'rules' => 'trim|in_list[0,1]'
            ),
            array(
                'field' => 'hide_label_pickup_mobile',
                'label' => 'Hide Warehouse Mobile',
                'rules' => 'trim|in_list[0,1]'
            ),

            array(
                'field' => 'hide_label_products',
                'label' => 'Hide Products',
                'rules' => 'trim|in_list[0,1]'
            ),
            array(
                'field' => 'support_email',
                'label' => 'Support Email',
                'rules' => 'trim|valid_email'
            ),
            array(
                'field' => 'support_phone',
                'label' => 'Support Phone',
                'rules' => 'trim|min_length[8]|max_length[15]|numeric'
            ),
        );
        if (!$order_exists) {
            $rules_new = array(
                array(
                    'field' => 'name',
                    'label' => 'Warehouse Name',
                    'rules' => 'trim|required|min_length[2]|max_length[40]|alpha_numeric_spaces'
                ),
                array(
                    'field' => 'contact_name',
                    'label' => 'Contact Name',
                    'rules' => 'trim|required|min_length[2]|max_length[20]'
                ),
                array(
                    'field' => 'phone',
                    'label' => 'Contact No.',
                    'rules' => 'trim|required|exact_length[10]|numeric'
                ),
                array(
                    'field' => 'address_1',
                    'label' => 'Address 1',
                    'rules' => 'trim|required|min_length[3]|max_length[200]'
                ),
                array(
                    'field' => 'address_2',
                    'label' => 'Address 2',
                    'rules' => 'trim|min_length[3]|max_length[200]'
                ),
                array(
                    'field' => 'city',
                    'label' => 'City',
                    'rules' => 'trim|required|min_length[2]|max_length[200]'
                ),
                array(
                    'field' => 'state',
                    'label' => 'State',
                    'rules' => 'trim|required|min_length[2]|max_length[200]'
                ),
                array(
                    'field' => 'zip',
                    'label' => 'Pin Code',
                    'rules' => 'trim|required|exact_length[6]|numeric'
                ),
                array(
                    'field' => 'gst_number',
                    'label' => 'GST Number',
                    'rules' => 'trim|exact_length[15]|alpha_numeric'
                ),
            );

            $config = array_merge($config, $rules_new);
        }

        $this->form_validation->set_rules($config);
        if ($this->form_validation->run()) {
            $save = array(
                'user_id' => $this->user->account_id,
                'hide_label_address' => ($this->input->post('hide_label_address') == '1') ? '1' : '0',
                'hide_label_pickup_mobile' => ($this->input->post('hide_label_pickup_mobile') == '1') ? '1' : '0',
                'hide_label_products' => ($this->input->post('hide_label_products') == '1') ? '1' : '0',
                'support_email' => $this->input->post('support_email'),
                'support_phone' => $this->input->post('support_phone'),
                'latitude' => $this->input->post('latitude'),
                'longitude' => $this->input->post('longitude'),
                'hyperlocal_status' => ($this->input->post('hyperlocal_check') == '1') ? '1' : '0',
                'hyperlocal_address' => htmlentities($this->input->post('hyperlocal_address'))
            );

            if (!$order_exists) {
                $warehouse_c = md5(url_title(trim(strtolower($this->input->post('name'))) . "" . trim(strtolower($this->input->post('address_1'))) . "" . trim(strtolower($this->input->post('city'))) . "" . trim(strtolower($this->input->post('state'))) . "" . trim($this->input->post('zip')) . "" . trim($this->input->post('phone'))));
                $save_update = array(
                    'name' => trim($this->input->post('name')),
                    'contact_name' => trim($this->input->post('contact_name')),
                    'phone' => trim($this->input->post('phone')),
                    'email' => trim($this->input->post('email')??''),
                    'address_1' => trim($this->input->post('address_1')??''),
                    'address_2' => trim($this->input->post('address_2')),
                    'city' => trim($this->input->post('city')),
                    'state' => $this->input->post('state'),
                    'country' => 'India',
                    'zip' => trim($this->input->post('zip')),
                    'gst_number' => trim($this->input->post('gst_number')),
                    'warehouse_all_details' => $warehouse_c,
                );
                $save = array_merge($save, $save_update);
            }

            if (!empty($warehouse)) {
                $this->warehouse_lib->update($warehouse->id, $save);
                $this->session->set_flashdata('success', 'Warehouse Updated');

                $this->warehouse_lib->createUpdateWarehouseWithCourier($warehouse->id, true);
                redirect('warehouse', true);
            } else {
                $user_warehouse = $this->warehouse_lib->getUserAllWarehouse($this->user->account_id, true);

                if (empty($user_warehouse)) {
                    $save['is_default'] = '1';
                }

                $save['active'] = '1';
                $new_id = $this->warehouse_lib->create($save);

              
                $this->warehouse_lib->createUpdateWarehouseWithCourier($new_id);
                $this->session->set_flashdata('success', 'Warehouse Created');
                redirect('warehouse', true);
            }
        } else {
            $this->data['value'] = $selected_state;
            $this->data['error'] = validation_errors();
        }

        $this->data['warehouse'] = $warehouse;
        $this->data['order_exists'] = $order_exists;
        $this->data['state_codes'] = $this->config->item('state_codes');
        $this->layout('warehouse/add');
    }

    function toggle_status($warehouse_id = false)
    {
        if (!$warehouse_id)
            return false;

        $change_status = $this->warehouse_lib->toogleStatus($warehouse_id, $this->user->account_id);

        if (!$change_status) {
            $this->session->set_flashdata('error', $this->warehouse_lib->get_error());
        } else {
            $this->session->set_flashdata('success', 'Warehouse Status Changed');
        }

        redirect('warehouse', true);
    }

    function toggle_default($warehouse_id = false)
    {
        if (!$warehouse_id)
            return false;

        $toggle_default = $this->warehouse_lib->makeDefault($warehouse_id, $this->user->account_id);

        if (!$toggle_default) {
            $this->session->set_flashdata('error', $this->warehouse_lib->get_error());
        } else {
            $this->session->set_flashdata('success', 'Records Updated');
        }

        redirect('warehouse', true);
    }

    public function export()
    {
        $warehouses = $this->warehouse_lib->getUserWarehouseData($this->user->account_id, 100000, 0);
        $filename = 'warehouse_list_' . date('dmyhis') . '.csv';
        header("Content-Description: File Transfer");
        header("Content-Disposition: attachment; filename=$filename");
        header("Content-Type: application/csv; ");
        $file = fopen('php://output', 'w');
        $header = array(
            "Warehouse Name",
            "Contact Person Name",
            "Contact Person Number",
            "Address1",
            "Address2",
            "GST Number",
            "Pincode",
            "City",
            "State",
            "Support Email",
            "Support Phone",
            "Status",
        );
        fputcsv($file, $header);

        foreach ($warehouses as $key => $record) {
            $row = array(
                $record->name,
                $record->contact_name,
                $record->phone,
                $record->address_1,
                $record->address_2,
                $record->gst_number,
                $record->zip,
                $record->city,
                $record->state,
                $record->support_email,
                $record->support_phone,
                ($record->active == 1 ? 'active' : 'in-active'),
            );
            fputcsv($file, $row);
        }
        fclose($file);
        exit;
    }
}
