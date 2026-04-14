<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Weight extends User_controller
{

    public function __construct()
    {
        parent::__construct();
        $this->load->library('weight_lib');
        $this->userHasAccess('weight');
    }

    function index()
    {
        $this->all();
    }

    function all($page = 1)
    {
        $per_page = $this->input->get('perPage');
        if (empty($per_page) || !is_numeric($per_page))
            $limit = 50;
        else
            $limit = $per_page;

        $disbute_time = $this->weight_lib->get_dispute_time_limit($this->user->account_id);
        if(!empty($disbute_time)) {
            $weight_dispute_time_limit=$disbute_time->time_limt * 60 * 60 * 24 ;
        } else {
            $weight_dispute_time_limit=$this->config->item('weight_dispute_time_limit');
        }

        $this->data['dispute_time_limit'] = $weight_dispute_time_limit;

        $this->load->library('courier_lib');
        $couriers = $this->courier_lib->showingToUsers();

        $this->data['couriers'] = $couriers;

        $filter = $this->input->get('filter');
        $apply_filters = array();

        $apply_filters['start_date'] = strtotime("-30 days midnight");
        $apply_filters['end_date'] = strtotime("tomorrow midnight") - 1;

        if (!empty($filter['start_date']))
            $apply_filters['start_date'] = strtotime(trim($filter['start_date']) . ' 00:00:00');
        else
            $filter['start_date'] = date('Y-m-d', $apply_filters['start_date']);


        if (!empty($filter['end_date']))
            $apply_filters['end_date'] = strtotime(trim($filter['end_date']) . ' 23:59:59');
        else
            $filter['end_date'] = date('Y-m-d', $apply_filters['end_date']);

        if (!empty($filter['awb_no'])) {
            $apply_filters['awb_no'] = array_map('trim', explode(',', $filter['awb_no']));
        }

        if (!empty($filter['product_name'])) {
            $apply_filters['product_name'] = $filter['product_name'];
        }

        if (!empty($filter['courier_id'])) {
            $apply_filters['courier_id'] = $filter['courier_id'];
        }

        if (!empty($filter['status'])) {
            $apply_filters['status'] = $filter['status'];
        }

        $total_row = $this->weight_lib->countByUserID($this->user->account_id, $apply_filters);
   
        $config = array(
            'base_url' => base_url('weight/all'),
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
        $records = $this->weight_lib->getByUserID($this->user->account_id, $limit, $offset, $apply_filters);

        $this->load->library('user_lib');
        $user = $this->user_lib->getByID($this->user->user_id);

        if (empty($user))
            return false;

        $this->load->library('plans_lib');
        $plans = $this->plans_lib->getPlanByName($user->pricing_plan);

        if (empty($plans))
            return false;

        $plan_type = $plans->plan_type;

        $this->data['records'] = $records;

        $this->data['filter'] = $filter;
        
        $this->data['plan_type'] = $plan_type;

        $this->layout('weight/index');
    }

    function exportCSV()
    {
        $filter = $this->input->get('filter');
        $apply_filters = array();

        $apply_filters['start_date'] = strtotime("-30 days midnight");
        $apply_filters['end_date'] = strtotime("tomorrow midnight") - 1;

        if (!empty($filter['start_date']))
            $apply_filters['start_date'] = strtotime(trim($filter['start_date']) . ' 00:00:00');
        else
            $filter['start_date'] = date('Y-m-d', $apply_filters['start_date']);


        if (!empty($filter['end_date']))
            $apply_filters['end_date'] = strtotime(trim($filter['end_date']) . ' 23:59:59');
        else
            $filter['end_date'] = date('Y-m-d', $apply_filters['end_date']);

        if (!empty($filter['awb_no'])) {
            $apply_filters['awb_no'] = array_map('trim', explode(',', $filter['awb_no']));
        }

        if (!empty($filter['product_name'])) {
            $apply_filters['product_name'] = $filter['product_name'];
        }

        if (!empty($filter['courier_id'])) {
            $apply_filters['courier_id'] = $filter['courier_id'];
        }

        if (!empty($filter['status'])) {
            $apply_filters['status'] = $filter['status'];
        }

        $this->data['filter'] = $filter;
        $query  = $this->weight_lib->exportByUserID($this->user->account_id, 100000, 0, $apply_filters);

        $this->load->library('export_db');

        $export = new Export_db();
        $export->query($query);


        $this->load->library('user_lib');
        $user = $this->user_lib->getByID($this->user->user_id);

        if (empty($user))
            return false;

        $this->load->library('plans_lib');
        $plans = $this->plans_lib->getPlanByName($user->pricing_plan);

        if (empty($plans))
            return false;

        $plan_type = $plans->plan_type;

        $filename = 'Weight_Disputes' . time() . '.csv';
        header("Content-Description: File Transfer");
        header("Content-Disposition: attachment; filename=$filename");
        header("Content-Type: application/csv; ");
        $file = fopen('php://output', 'w');
        $header = array(
            "Applied Date",
            "Courier",
            "AWB Number",
            "Order Id",
            "Dead Weight",
            "Package Length",
            "Package Breadth",
            "Package Height",
            "Volumetric Weight",
            "Booking Slab",
            "Applied Weight Slab",
            "Forward Extra Weight Charges",
            "RTO Extra Weight Charges",
            "Charged to Wallet",
            "Product Name",
            "Product SKU",
            "Product Quantity",
            "Status"
        );
        fputcsv($file, $header);

        while ($record = $export->next()) {

            $len=(!empty($record->seller_package_length)) ? $record->seller_package_length : '0'; 
            $bre=(!empty($record->seller_package_breadth)) ? $record->seller_package_breadth : '0';
            $hei=(!empty($record->seller_package_height)) ? $record->seller_package_height : '0'; 
            $sum = $len * $bre * $hei;

            $totalsum = $sum / 5000;
            $weight = ($totalsum * 1000);
            $product_sku='';
        //    if(!empty($record->product_sku) && substr($record->product_sku,0,1)!=',') 
        //    { $product_sku= rtrim(ucwords(mb_strimwidth($record->product_sku, 0, 30, "...")), ','); } 
            $row = array(
                date('Y-m-d', $record->apply_weight_date),
                $record->courier_name,
                $record->awb_number,
                $record->order_id,
                (!empty($record->seller_dead_weight)) ? $record->seller_dead_weight : '0',
                (!empty($record->seller_package_length)) ? $record->seller_package_length : '0',
                (!empty($record->seller_package_breadth)) ? $record->seller_package_breadth : '0',
                (!empty($record->seller_package_height)) ? $record->seller_package_height : '0',
                round($weight),
                $record->seller_booking_weight,
                $record->weight_new_slab,
                round($record->weight_difference_charges, 2),
                ($record->ship_status == 'rto' && $plan_type != 'per_dispatch') ? round($record->weight_difference_charges, 2) : '0',
                ($record->applied_to_wallet) ? 'Yes' : 'No',
                ucwords($record->product_name),
                ucwords($record->product_sku),
                $record->product_qty,
                strtoupper($record->seller_action_status)
            );
            fputcsv($file, $row);
        }
          fclose($file);
        exit;
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

    function accept_weight()
    {
        $this->load->library('form_validation');
        $config = array(
            array(
                'field' => 'ids[]',
                'label' => 'Record IDs',
                'rules' => 'trim|required'
            ),
        );


        $this->form_validation->set_rules($config);

        if (!$this->form_validation->run()) {
            $this->data['json'] = array('error' => strip_tags(validation_errors()));
            $this->layout(false, 'json');
            return;
        }

        $ids = $this->input->post('ids');


        if (empty($ids)) {
            $this->data['json'] = array('error' => 'Please select shipments.');
            $this->layout(false, 'json');
            return;
        }


        foreach ($ids as $id) {
            $this->weight_lib->accept_weight($id, $this->user->account_id);
        }


        $this->data['json'] = array('success' => 'Applied successfully');
        $this->layout(false, 'json');
        return;
    }

    function raise_dispute()
    {
        $this->load->library('escalation_lib');
        $this->load->library('shipping_lib');
        $this->load->library('s3');

        $this->load->library('form_validation');
        $config = array(
            array(
                'field' => 'shipment_id',
                'label' => 'Shipment ID',
                'rules' => 'trim|required'
            ),
            array(
                'field' => 'remarks',
                'label' => 'Remarks',
                'rules' => 'trim|min_length[3]'
            ),
        );

        $this->form_validation->set_rules($config);
        if (!$this->form_validation->run()) {
            $this->data['json'] = array('error' => strip_tags(validation_errors()));
            $this->layout(false, 'json');
            return;
        }

        $config1 = array();
        $config1['upload_path'] = 'assets/escalations/';
        $config1['allowed_types'] = '*';
        $config1['max_size'] = 5000;
        $config1['encrypt_name'] = TRUE;

        $this->load->library('upload', $config1);

        $filesCount =  (!empty($_FILES['importFile'])) ? count($_FILES['importFile']['name']) : 0;

        $uploadData = array();
        $upload_folder = "escalations";

        for ($i = 0; $i < $filesCount; $i++) {
            if (!empty($_FILES['importFile']['name'][$i])) {
                $extension = explode(".", $_FILES['importFile']['name'][$i]);
                $new_name = time() . rand(100, 999) . '.' . end($extension);

                $config['file_name'] = $new_name;

                $fileTempName = $_FILES['importFile']['tmp_name'][$i];
                $image_name = $new_name;

                $file_name = $this->s3->amazonS3Upload($image_name, $fileTempName, $upload_folder);

                if ($file_name) {
                    $uploadData[] = $file_name;
                } else {
                    $this->data['json'] = array('error' => "Unable to upload file");
                    $this->layout(false, 'json');
                    return;
                }
            }
        }

        $ids = $this->input->post('shipment_id');
        $remarks = $this->input->post('remarks');
        $shipment_ids = explode(',', $ids);
        foreach ($shipment_ids as $shipment_id) {
            $this->weight_lib->raise_dispute($shipment_id, $remarks, $uploadData, $this->user->account_id);
        }
        $this->data['json'] = array('success' => 'done');

        $this->layout(false, 'json');
    }
}
