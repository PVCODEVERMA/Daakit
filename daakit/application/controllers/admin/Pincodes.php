<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Pincodes extends Admin_controller
{

    public function __construct()
    {
        parent::__construct();
        $this->load->library('admin/pincode_lib');
        $this->userHasAccess('pincodes');
        
        $this->order_type = 'ecom';
    }

    function index()
    {
        self::v();
    }

    function v($page = 'list', $page_no = 1)
    {
        $inner_content = '';
        switch ($page) {

            case 'list':
                $inner_content = $this->pincode_list($page_no);
                break;
            case 'listexport':
                $inner_content = $this->pincode_list_export();
                break;
            case 'upload':
                $inner_content = $this->upload($page_no);
                break;

            default:
        }
        $this->data['inner_content'] = $inner_content;
        $this->data['view_page'] = $page;
        $this->layout('pincode/index');
    }

    function pincode_list($page = 1)
    {
        $per_page = $this->input->post('perPage');
        if (empty($per_page) || !is_numeric($per_page))
            $limit = 50;
        else
            $limit = $per_page;
        $this->load->library('courier_lib');
        $couriers = $this->courier_lib->listAllCouriers($this->order_type);
        $this->data['couriers'] = $couriers;
        $filter = $this->input->post('filter');
        $apply_filters = array();


        if (!empty($filter['pincode'])) {
            $apply_filters['pincode'] = $filter['pincode'];
        }


        if (!empty($filter['courier_id'])) {
            $apply_filters['courier_id'] = $filter['courier_id'];
        }


        $total_row = $this->pincode_lib->countPincodes($apply_filters);
        $config = array(
            'base_url' => base_url('admin/pincodes/v/list'),
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
        $pincodes = $this->pincode_lib->getPincodesList($limit, $offset, $apply_filters);



        $this->data['couriers'] = $couriers;
        $this->data['pincodes'] = $pincodes;
        $this->data['filter'] = $filter;

        return $this->load->view('admin/pincode/pages/pincodes', $this->data, true);
    }

    function pincode_list_export()
    {

        $filter = $this->input->get('filter');
        $apply_filters = array();
        if (!empty($filter['pincode'])) {
            $apply_filters['pincode'] = $filter['pincode'];
        }

        if (!empty($filter['courier_id'])) {
            $apply_filters['courier_id'] = $filter['courier_id'];
        }

        $query = $this->pincode_lib->getPincodesList(500000, 0, $apply_filters, true);

        $this->load->library('export_db');

        $export = new Export_db();
        $export->query($query);

        $filename = 'pincodes.csv';
        header("Content-Description: File Transfer");
        header("Content-Disposition: attachment; filename=$filename");
        header("Content-Type: application/csv; ");
        $file = fopen('php://output', 'w');
        $header = array("Pincode", "Courier ID" , "Courier", "City", "State", "COD Delivery", "Prepaid Delivery", "Pickup", "Reverse Pickup");
        fputcsv($file, $header);
        while ($pincode = $export->next()) {
          $courier_alias=  isset(($pincode->courier_alias)) ? " (".ucfirst($pincode->courier_alias).")" : "";
            $row = array(
                $pincode->pincode,
                $pincode->courier_id,
                ucwords($pincode->courier_name).$courier_alias,
                ucwords($pincode->city),
                ucwords($pincode->state_code),
                ucwords($pincode->cod),
                ucwords($pincode->prepaid),
                ucwords($pincode->pickup),
                ucwords($pincode->is_reverse_pickup)
            );
            fputcsv($file, $row);
        }
        fclose($file);
        exit;
    }

    function edit_pincode()
    {
        $this->load->library('form_validation');
        $config = array(
            array(
                'field' => 'pincode_id',
                'label' => 'Pincode ID',
                'rules' => 'trim|required|numeric',
            ),
        );

        $this->form_validation->set_rules($config);
        if (!$this->form_validation->run()) {
            $this->data['json'] = array('error' => strip_tags(validation_errors()));
            $this->layout(false, 'json');
        }

        $pincode = $this->pincode_lib->getByID($this->input->post('pincode_id'));
        if (!$pincode) {
            $this->data['json'] = array('error' => 'Pincode Not Available');
            $this->layout(false, 'json');
        }

        $this->data['pincode'] = $pincode;

        $this->layout('pincode/edit_pincode', 'NONE');
    }


    function save_pincode()
    {
        $this->load->library('form_validation');
        $config = array(
            array(
                'field' => 'pincode_id',
                'label' => 'Pincode ID',
                'rules' => 'trim|required|numeric',
            ),
            array(
                'field' => 'cod',
                'label' => 'COD',
                'rules' => 'trim|required|in_list[Y,N]',
            ),
            array(
                'field' => 'prepaid',
                'label' => 'Prepaid',
                'rules' => 'trim|required|in_list[Y,N]',
            ),
            array(
                'field' => 'pickup',
                'label' => 'Pickup',
                'rules' => 'trim|required|in_list[Y,N]',
            ),
            array(
                'field' => 'is_reverse_pickup',
                'label' => 'Reverse Pickup',
                'rules' => 'trim|required|in_list[Y,N]',
            ),
        );

        $this->form_validation->set_rules($config);
        if (!$this->form_validation->run()) {
            $this->data['json'] = array('error' => strip_tags(validation_errors()));
            $this->layout(false, 'json');
        }

        $save = array(
            'cod' => $this->input->post('cod'),
            'prepaid' => $this->input->post('prepaid'),
            'pickup' => $this->input->post('pickup'),
            'is_reverse_pickup' => $this->input->post('is_reverse_pickup'),
        );

        $this->pincode_lib->update($this->input->post('pincode_id'), $save);

        $this->data['json'] = array('success' => 'pincode updated');
        $this->layout(false, 'json');
    }

    function upload()
    {

        if (!$this->pincode_lib->uploadPincodes()) {
            $this->data['error'] = $this->pincode_lib->get_error();
        } else {
            $this->session->set_flashdata('success', 'Pincode uploaded successfully');
            redirect('admin/pincodes/v/upload', true);
        }

        $this->load->library('courier_lib');
        $couriers = $this->courier_lib->listAllCouriers($this->order_type);
        $this->data['couriers'] = $couriers;
        redirect('admin/pincodes', true);
    }

    function file_check()
    {
        $allowed_mime_types = array('text/x-comma-separated-values', 'text/comma-separated-values', 'application/octet-stream', 'application/vnd.ms-excel', 'application/x-csv', 'text/x-csv', 'text/csv', 'application/csv', 'application/excel', 'application/vnd.msexcel', 'text/plain');
        if (isset($_FILES['importFile']['name']) && $_FILES['importFile']['name'] != "") {
            $mime = get_mime_by_extension($_FILES['importFile']['name']);
            $fileAr = explode('.', $_FILES['importFile']['name']);
            $ext = end($fileAr);
            if (($ext == 'csv') && in_array($mime, $allowed_mime_types)) {
                return true;
            } else {
                $this->form_validation->set_message('file_check', 'Please select only CSV file to upload.');
                return false;
            }
        } else {
            $this->form_validation->set_message('file_check', 'Please select a CSV file to upload.');
            return false;
        }
    }
}
