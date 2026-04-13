<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Weight_reco extends Admin_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->load->library('admin/weight_reco_lib');
        $this->load->library('admin/escalation_lib');
        $this->userHasAccess('weight');
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

    function index()
    {
        $this->v();
    }

    function v($page_type = 'weight_upload', $page = 1)
    {
        $inner_content = '';
        switch ($page_type) {
            case 'weight_upload':
                $inner_content = $this->weightUpload();
                break;
            case 'manage':
                $this->userHasAccess('manage_weight');
                $inner_content = $this->manage_weight();
                break;
            default:
                $inner_content = $this->weightUpload();
        }

        $this->data['inner_content'] = $inner_content;

        $this->data['page_type'] = $page_type;
        $this->layout('weight_reco/view');
    }


    function exportPendingWeight()
    {
        $filter = $this->input->get('filter');
        $apply_filters = array();

        $apply_filters['start_date'] = strtotime("first day of last month midnight");

        $apply_filters['end_date'] = strtotime("last day of last month 23:59:59");


        if (!empty($filter['start_date'])) {
            $apply_filters['start_date'] = strtotime(trim($filter['start_date']) . ' 00:00:00');
        } else {
            $filter['start_date'] = date('Y-m-d', $apply_filters['start_date']);
        }

        if (!empty($filter['end_date'])) {
            $apply_filters['end_date']  = strtotime(trim($filter['end_date']) . ' 23:59:59');
        } else {
            $filter['end_date'] = date('Y-m-d', $apply_filters['end_date']);
        }

        if (!empty($filter['courier_id'])) {
            $apply_filters['courier_id'] = $filter['courier_id'];
        }

        $query  = $this->weight_reco_lib->exportPendingWeight($apply_filters);

        $this->load->library('export_db');

        $export = new Export_db('slave');
        $export->query($query);

        $filename = 'Pending_weight' . time() . '.csv';
        header("Content-Description: File Transfer");
        header("Content-Disposition: attachment; filename=$filename");
        header("Content-Type: application/csv; ");
        $file = fopen('php://output', 'w');
        $header = array(
            "Shipment Date",
            "AWB Number",
            "Courier Name",
            "Courier Api Weight",
            "Courier Weight Image"
        );
        fputcsv($file, $header);
        $this->load->library('tracking_lib');
        while ($record = $export->next()) {
            $revise_weight_img = '';

            if(strtolower($record->courier_display_name) == 'bluedart') {
                $revise_weight_img = $this->tracking_lib->get_custom_tracking_metadata_row($record->awb_number, 're_image');
                $revise_weight_img = !empty($revise_weight_img->ref_value) ? $revise_weight_img->ref_value : '';
            }
            $row = array(
                date('Y-m-d', $record->created),
                $record->awb_number,
                $record->courier_name,
                $record->weight,
                $revise_weight_img,
                
            );
            fputcsv($file, $row);
        }
        fclose($file);
        exit;
    }


    private function weightUpload()
    {

        if (!$this->weight_reco_lib->weightFileUpload()) {
            $this->data['error'] = $this->weight_reco_lib->get_error();
        } else {
            $this->session->set_flashdata('success', 'File uploaded successfully');
            redirect('admin/weight_reco/v/weight_upload', true);
        }

        return $this->load->view('admin/weight_reco/pages/weight_upload', $this->data, true);
    }

    function manage_weight($page = 1)
    {

        $per_page = $this->input->get('perPage');

        $filter = $this->input->get('filter');
        $apply_filters = array();


        $this->load->library('courier_lib');
        $couriers = $this->courier_lib->listAllCouriers();

        $this->data['couriers'] = $couriers;


        if (empty($per_page) || !is_numeric($per_page))
            $limit = 50;
        else
            $limit = $per_page;


        $apply_filters['start_date'] = strtotime("-30 days midnight");
        $apply_filters['end_date'] = strtotime("tomorrow midnight") - 1;

        if (!empty($filter['start_date'])) {
            $apply_filters['start_date'] = strtotime(trim($filter['start_date']) . ' 00:00:00');
        } else {
            $filter['start_date'] = date('Y-m-d', $apply_filters['start_date']);
        }
        if (!empty($filter['end_date'])) {
            $apply_filters['end_date'] = strtotime(trim($filter['end_date']) . ' 23:59:59');
        } else {
            $filter['end_date'] = date('Y-m-d', $apply_filters['end_date']);
        }

        if (!empty($filter['awb_no'])) {
            $apply_filters['awb_no'] = array_map('trim', explode(',', $filter['awb_no']));
        }

        if (!empty($filter['seller_id'])) {
            $apply_filters['seller_id'] = $filter['seller_id'];
        }

        if (!empty($filter['manager_id_in'])) {

            $apply_filters['manager_id_in'] = $filter['manager_id_in'];
        }
        if (!empty($filter['courier_id'])) {
            $apply_filters['courier_id'] = $filter['courier_id'];
        }

        if (!empty($filter['status'])) {
            $apply_filters['status'] = $filter['status'];
        }

        $total_row = $this->weight_reco_lib->countRecords($apply_filters);
        $config = array(
            'base_url' => base_url('admin/weight_reco/v/manage'),
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

        $seller_details = '';
        $this->load->library('admin/user_lib');
        $seller_details = $this->user_lib->getUserListFilter($filter['seller_id']);
        $this->data['users'] = $seller_details;

        $admin_users = $this->user_lib->getAdminUsers();
        $this->data['admin_users'] = $admin_users;




        $records = $this->weight_reco_lib->getRecords($limit, $offset, $apply_filters);


        $this->data['records'] = $records;
        $this->data['filter'] = $filter;

        return $this->load->view('admin/weight_reco/pages/manage_weight', $this->data, true);
    }

    function exportCSV()
    {
        $filter = $this->input->get('filter');
        $apply_filters = array();

        $apply_filters['start_date'] = strtotime("-30 days midnight");
        $apply_filters['end_date'] = strtotime("tomorrow midnight") - 1;

        if (!empty($filter['start_date'])) {
            $apply_filters['start_date'] = strtotime(trim($filter['start_date']) . ' 00:00:00');
        } else {
            $filter['start_date'] = date('Y-m-d', $apply_filters['start_date']);
        }
        if (!empty($filter['end_date'])) {
            $apply_filters['end_date'] = strtotime(trim($filter['end_date']) . ' 23:59:59');
        } else {
            $filter['end_date'] = date('Y-m-d', $apply_filters['end_date']);
        }

        if (!empty($filter['awb_no'])) {
            $apply_filters['awb_no'] = array_map('trim', explode(',', $filter['awb_no']));
        }

        if (!empty($filter['seller_id'])) {
            $apply_filters['seller_id'] = $filter['seller_id'];
        }
        if (!empty($filter['manager_id_in'])) {

            $apply_filters['manager_id_in'] = $filter['manager_id_in'];
        }

        if (!empty($filter['courier_id'])) {
            $apply_filters['courier_id'] = $filter['courier_id'];
        }

        if (!empty($filter['status'])) {
            $apply_filters['status'] = $filter['status'];
        }

        $this->data['filter'] = $filter;
        $query  = $this->weight_reco_lib->exportRecords(1000000, 0, $apply_filters);

        $this->load->library('export_db');

        $export = new Export_db();
        $export->query($query);

        $filename = 'Weight_Disputes' . time() . '.csv';
        header("Content-Description: File Transfer");
        header("Content-Disposition: attachment; filename=$filename");
        header("Content-Type: application/csv; ");
        $file = fopen('php://output', 'w');
        $header = array(
            "Upload Date",
            "Applied Date",
            "Escalation Date",
            "Shipment Booking Date",
            "Courier",
            "Parent Courier",
            "AWB Number",
            "Seller ID",
            "Seller Company",
            "Account Manager",
            "Seller Dead Weight",
            "Seller Vol. Weight",
            "Seller Package Length",
            "Seller Package Breadth",
            "Seller Package Height",
            "Seller Booking Slab",

            "Courier Billed Weight",
            "Courier Vol. Weight",
            "Courier Length",
            "Courier Breadth",
            "Courier Height",
            "Applied Weight Slab",

            "Final Settled Weight",
            "Forward Extra Weight Charges",
            "RTO Extra Weight Charges",

            "Weight Applied",
            "Charged to Wallet",
            "Product Name",
            "Product SKU",
            "Product Quantity",
            "Product Price",
            "Order Amount",
            "COD/Prepaid",
            "Status",
            "PID",
            "Freeze Applied Weight",
            "Freeze Applied dimension",
        );

        $show_image_permission = 0; 
        $admin_permissions = isset($this->data['user_details']->permissions)?$this->data['user_details']->permissions:"";
        if(!empty($admin_permissions)){
            if(in_array("manage_weight_export_image",$admin_permissions)){
                    $header[] =  "Revise Weight Image";
                    $show_image_permission=1;
            }
               
        }
        fputcsv($file, $header);

        $this->load->library('tracking_lib');
        $this->load->library('admin/user_lib');

        while ($record = $export->next()) {
           
            $freeze_dimenstion = $freeze_weight = $product_id = "";
            if (!empty($record->weight_applied_id)) {

                    $user_id      = !empty($record->user_id) ? $record->user_id : '';
                    $product_name = !empty($record->product_name) ? $record->product_name : '';
                    $product_sku  = !empty($record->product_sku) ? $record->product_sku : '';
                    $product_qty  = !empty($record->product_qty) ? $record->product_qty : '';

                    if(!empty($product_name)){
                        $product_name = explode(",",$product_name);
                        if(count($product_name)==1){
                          $product_name = isset($product_name[0])?$product_name[0]:"";
                        }
                        $product_sku = explode(",",$product_sku);
                        if(count($product_sku)==1){
                          $product_sku = isset($product_sku[0])?$product_sku[0]:"";
                        }
                        

                    }
                    

                    $code = $user_id." ".$product_sku." ".$product_name." ".$product_qty ;
                    $con_code =  iconv('utf-8','ASCII//IGNORE//TRANSLIT',$code);
                    if(!empty($code)){
                        $code  = url_title($con_code, 'underscore', TRUE);
                    }
                        $queryd = $this->user_lib->get_data_code($code);
                        if(!empty($queryd)){
                            $product_id =  isset($queryd)?!empty($queryd->id)?$queryd->id:"":"";
                            $freeze_dimenstion = $record->weight_applied_length . 'x' . $record->weight_applied_breadth . 'x' . $record->weight_applied_height;
                            $freeze_weight = $record->weight_applied_weight;

                        }
                        
                    
            }
            
            $revise_weight_img = '';

            if(strtolower($record->courier_display_name) == 'bluedart') {
                $revise_weight_img = $this->tracking_lib->get_custom_tracking_metadata_row($record->awb_number, 're_image');
                $revise_weight_img = !empty($revise_weight_img->ref_value) ? $revise_weight_img->ref_value : '';
            }
          
            $row = array(
                date('Y-m-d', $record->upload_date),
                date('Y-m-d', $record->apply_weight_date),
                (!empty($record->escalation_creation_date)) ? date('Y-m-d', $record->escalation_creation_date) : '',
                (!empty($record->shipment_created)) ? date('Y-m-d', $record->shipment_created) : '',
                $record->courier_name,
                $record->courier_display_name,
                $record->awb_number,
                $record->user_id,
                ucwords($record->company_name),
                ucwords($record->account_manager_fname . ' ' . $record->account_manager_lname),
                (!empty($record->seller_dead_weight)) ? $record->seller_dead_weight : '0',
                (!empty($record->seller_volumetric_weight)) ? $record->seller_volumetric_weight : '0',
                (!empty($record->seller_package_length)) ? $record->seller_package_length : '0',
                (!empty($record->seller_package_breadth)) ? $record->seller_package_breadth : '0',
                (!empty($record->seller_package_height)) ? $record->seller_package_height : '0',
                $record->seller_booking_weight,

                $record->courier_billed_weight,
                $record->courier_vol_weight,
                (!empty($record->courier_length)) ? $record->courier_length : '0',
                (!empty($record->courier_breadth)) ? $record->courier_breadth : '0',
                (!empty($record->courier_height)) ? $record->courier_height : '0',

                $record->weight_new_slab,
                ucwords($record->dispute_closure_favour),
                round($record->weight_difference_charges, 2),
                ($record->ship_status == 'rto') ? round($record->weight_difference_charges, 2) : '0',

                ($record->weight_applied == '1') ? 'Yes' : 'No',
                ($record->applied_to_wallet) ? 'Yes' : 'No',
                ucwords($record->product_name),
                $record->product_sku,
                $record->product_qty,
                $record->product_price,
                $record->order_amount,
                $record->order_payment_type,
                strtoupper($record->seller_action_status),
                $product_id,
                $freeze_weight,
                $freeze_dimenstion
                );
                
            if($show_image_permission){
             $row[] = $revise_weight_img;
            }
            fputcsv($file, $row);
        }
        fclose($file);
        exit;
    }


    function exportFreezeCSV()
    {
        $filter = $this->input->get('filter');
        $this->load->library('catalog_lib');
        $query = $this->catalog_lib->exportfrezerecord(100000, 0, $filter);
        $this->load->library('export_db');
        $export = new Export_db();
        $export->query($query);
        $filename = 'Weight_Freeze_' . time() . '.csv';
        header("Content-Description: File Transfer");
        header("Content-Disposition: attachment; filename=$filename");
        header("Content-Type: application/csv; ");
        $file = fopen('php://output', 'w');
        $header = array(
            "PID",
            "Seller Id",
            "Seller Name",
            "Escalation Id",
            "Escalation Date",
            "Escalation closed date",
            "Seller Company",
            "Product Name",
            "Product Quantity",
            "Product SKU",
            "Length",
            "Breadth",
            "Height",
            "Weight",
            "Weight Freeze Status",
            "Remarks",
            "Images Url (comma separated)"
            
           
         );
        fputcsv($file, $header);

        while ($record = $export->next()) {
            $seller_name = $record->fname." ".$record->lname;
            $status = "";
            if($record->weight_locked=='1'){
            $status = 'Requested';
            }else if($record->weight_locked=='2'){
             $status = 'Approved';   
         }else if($record->weight_locked=='3'){
             $status = 'Rejected';   
            }
            $row = array(
                $record->id,
                $record->user_id,
                $seller_name,
                $record->esc_id,
                !empty($record->esc_created)?date('Y-m-d',($record->esc_created)):"",
                !empty($record->close_date)?date('Y-m-d',($record->close_date)):"",
                $record->company_name,
                $record->product_name,
                $record->product_qty,
                $record->product_sku,
                $record->length,
                $record->breadth,
                $record->height,
                $record->weight,
                $status,
                isset($record->remarks)?strip_tags($record->remarks):"",
                isset($record->attachments)?$record->attachments:""
               
              
                );
            fputcsv($file, $row);
        }
        fclose($file);
        exit;
    }



    function bulk_action_import()
    {

        if (!$this->weight_reco_lib->bulkActionimport($this->user->user_id)) {
            $this->session->set_flashdata('error', $this->weight_reco_lib->get_error());
        } else {
            $this->session->set_flashdata('success', 'File uploaded successfully');
        }

        redirect('admin/weight_reco/v/manage', true);
    }

    function updateescalation($id,$remarks,$image_url){
                    $esc_id = $this->escalation_lib->getEscalationByRefIDType($id, 'weight_freeze', false);
                    $status = "closed";
                    $remark = $remarks;
                    
            
                $update = array(
                    'remarks' => $remark,
                    'action_by' => 'delta',
                    'status' => $status,
                    'attachments' => $image_url,
                    'action_user_id' => $this->user->user_id,);
               // pr($update,1);exit;

                $this->escalation_lib->submit_action($esc_id->id, $update);
    }
}
