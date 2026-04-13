<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Pickups extends User_controller
{

    public function __construct()
    {
        parent::__construct();
        $this->load->library('pickups_lib');
        $this->userHasAccess('shipments');
    }

    function index($page = 1)
    {
        $per_page = $this->input->post('perPage');
        if (empty($per_page) || !is_numeric($per_page))
            $limit = 10;
        else
            $limit = $per_page;
        $this->load->library('courier_lib');
        $couriers = $this->courier_lib->list_couriers(true);
        $this->data['couriers'] = $couriers;
        $filter = $this->input->post('filter');
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

        if (!empty($filter['pickup_number'])) {
            $apply_filters['pickup_number'] = array_map('trim', explode(',', $filter['pickup_number']));
        }

        if (!empty($filter['pickup_id'])) {
            $apply_filters['pickup_id'] = array_map('trim', explode(',', $filter['pickup_id']));
        }

        if (!empty($filter['courier_id'])) {
            $apply_filters['courier_id'] = $filter['courier_id'];
        }
        if (!empty($filter['pickup_done'])) {
            $apply_filters['pickup_done'] = $filter['pickup_done'];
        }

        if (!empty($filter['warehouse_id'])) {
            $apply_filters['warehouse_id'] = $filter['warehouse_id'];
        }

        if (!empty($filter['order_type'])) {
            $apply_filters['order_type'] = $filter['order_type'];
        }

        $total_row = $this->pickups_lib->countUserPickups($this->user->account_id, $apply_filters);
        $config = array(
            'base_url' => base_url('pickups/index'),
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

        $manifests = $this->pickups_lib->getUserPickups($this->user->account_id, $limit, $offset, $apply_filters);
       
        $manifest_data = array();

        foreach($manifests as $manifest){

            $this->load->library('escalation_lib');
         
            $esc_id =  $this->escalation_lib->getEscalationId($manifest->id, 'pickup');
            if(!empty($esc_id)){
               $manifest->esc_id = $esc_id->esc_id;
            }else{
                $manifest->esc_id = '';
            }
            $manifest_data[] = $manifest;
        }
        
        $this->data['manifests'] = $manifest_data;

        $this->load->library('warehouse_lib');
        $warehouses = $this->warehouse_lib->getUserAllWarehouse($this->user->account_id, true);

        $this->data['warehouses'] = $warehouses;

        $this->data['filter'] = $filter;
        $this->layout('pickups/index');
    }

    function download($id = false)
    {
        if (!$id)
            return false;

        if (!$file_name = $this->pickups_lib->download_manifest(array($id), $this->user->account_id)) {
            $this->session->set_flashdata('error', 'Unable to download manifest');
            redirect(base_url('pickups'));
        }

    
        redirect($file_name);
    }

    function escalate()
    {
        $this->load->library('s3');
        $this->load->library('escalation_lib');

        $this->load->library('form_validation');
        $config = array(
            array(
                'field' => 'pickup_id',
                'label' => 'Pickup ID',
                'rules' => 'trim|required'
            ),
            array(
                'field' => 'remarks',
                'label' => 'Remarks',
                'rules' => 'trim|min_length[3]|max_length[200]'
            ),
        );


        $this->form_validation->set_rules($config);
        if ($this->form_validation->run()) {

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
                        $file_issue = true;
                        $this->data['error'] =  "Unable to upload file";
                    }

                    /*$_FILES['file']['name'] = $_FILES['importFile']['name'][$i];
                    $_FILES['file']['type'] = $_FILES['importFile']['type'][$i];
                    $_FILES['file']['tmp_name'] = $_FILES['importFile']['tmp_name'][$i];
                    $_FILES['file']['error'] = $_FILES['importFile']['error'][$i];
                    $_FILES['file']['size'] = $_FILES['importFile']['size'][$i];

                    // File upload configuration
                    // Load and initialize upload library

                    $this->upload->initialize($config1);

                    // Upload file to server
                    if ($this->upload->do_upload('file')) {
                        // Uploaded file data
                        $fileData = $this->upload->data();
                        $uploadData[] = $fileData['file_name'];
                    } else {
                        $this->data['json'] = array('error' => strip_tags($this->upload->display_errors()));
                        $this->layout(false, 'json');
                        return;
                    }*/
                }
            }




            $pickup_ids = $this->input->post('pickup_id');
            $remarks = $this->input->post('remarks');

            $pickup_ids = explode(',', $pickup_ids);
            foreach ($pickup_ids as $pickup_id) {
                //submit pickup escalation
                $update = array(
                    'type' => 'pickup',
                    'ref_id' => $pickup_id,
                    'remarks' => $remarks,
                    'action_by' => 'seller',
                    'attachments' => implode(',', $uploadData)
                );
                if ($this->escalation_lib->create_escalation($this->user->account_id, $update)) {
                    $this->pickups_lib->update_esc_status($this->user->account_id, $pickup_id);
                    $this->data['json'] = array('success' => 'done');
                } else {
                    $this->data['json'] = array('error' => $this->escalation_lib->get_error());
                }
            }
        } else {
            $this->data['json'] = array('error' => strip_tags(validation_errors()));
        }
        $this->layout(false, 'json');
    }

    function exportCSV()
    {
        ini_set('memory_limit', '512M');
        $this->load->library('orders_lib');

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

        if (!empty($filter['pickup_number'])) {
            $apply_filters['pickup_number'] = array_map('trim', explode(',', $filter['pickup_number']));
        }

        if (!empty($filter['pickup_id'])) {
            $apply_filters['pickup_id'] = array_map('trim', explode(',', $filter['pickup_id']));
        }

        if (!empty($filter['courier_id'])) {
            $apply_filters['courier_id'] = $filter['courier_id'];
        }
        if (!empty($filter['pickup_done'])) {
            $apply_filters['pickup_done'] = $filter['pickup_done'];
        }

        if (!empty($filter['warehouse_id'])) {
            $apply_filters['warehouse_id'] = $filter['warehouse_id'];
        }

        if (!empty($filter['order_type'])) {
            $apply_filters['order_type'] = $filter['order_type'];
        }
        $this->data['filter'] = $filter;

        $query = $this->pickups_lib->exportByUserID($this->user->account_id, 150000000, 0, $apply_filters);
        $this->load->library('export_db');
        $export = new Export_db('slave');
        $export->query('SET SESSION group_concat_max_len=55555555');
        $export->query($query);

        $filename = 'pickups_' . time() . '.csv';
        header("Content-Description: File Transfer");
        header("Content-Disposition: attachment; filename=$filename");
        header("Content-Type: application/csv; ");
        $file = fopen('php://output', 'w');
        $header = array(
            "Manifest ID", "Courier Name", "AWB Number", "Customer Details", "Order Details", "Order Value", "Collectable Amount",
            "Product Details", "Weight (Kg.)", "Payment Mode", "Warehouse Name", "Pickup Reference number"
        );
        fputcsv($file, $header);
        $this->load->library('shipping_lib');
        while ($order = $export->next()) {
            $shipment_ids = explode(',', $order->shipment_ids);
            foreach ($shipment_ids as $shipment_id) {
                $shipment_data = $this->shipping_lib->getExportShipmentByID($shipment_id);
               
                if ($shipment_data->ship_status != 'cancelled') {
                    $row = array(
                        $order->id,
                        $order->courier_name,
                        $shipment_data->awb_number,
                        ucwords($shipment_data->shipping_fname . ' ' . $shipment_data->shipping_lname),
                        $shipment_data->order_no,
                        $shipment_data->order_amount,
                        !empty($shipment_data->collectable_amount) ? $shipment_data->collectable_amount : "N/A",
                     //   implode(',', $productNames),
                        $shipment_data->products,
                        !empty($shipment_data->package_weight) ? round($shipment_data->package_weight / 1000, 2) : '0.5',
                        $shipment_data->order_payment_type,
                        $order->warehouse_name,
                        $order->pickup_number
                    );
                    fputcsv($file, $row);
               }
            }
        }
        

        fclose($file);
        exit;
    }
}
