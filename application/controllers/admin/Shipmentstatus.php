<?php

defined('BASEPATH') or exit('No direct script access allowed');

use App\Lib\Logs\Shipment as Log;

class Shipmentstatus extends Admin_controller
{

    public function __construct()
    {
        parent::__construct();
        $this->load->library('shipping_lib');
        $this->userHasAccess('shipments');
        $this->load->library('status_lib');
    }

    function all()
    {
        $this->load->library('form_validation');
        $config = array(
            array(
                'field' => 'shipmentstatus',
                'label' => 'Shipment Status',
                'rules' => 'required'
            ),
            array(
                'field' => 'awbnumbers',
                'label' => 'Awb Number',
                'rules' => 'trim|required'
            ),
        );
        $this->form_validation->set_rules($config);
        if ($this->form_validation->run()) {
            $shipmentstatus = $this->input->post('shipmentstatus');
            $awbnumbers = array_map('trim', explode(',', $this->input->post('awbnumbers')));

            $AwbList = $this->shipping_lib->getByAWBsMultiple($awbnumbers);

            foreach ($AwbList as $Awbl) {
                $log = new Log();
                $log->update($this->user->user_id, $Awbl->id, "Status changed to : " . $shipmentstatus);
                do_action('shipping.status', $Awbl->id, array('ship_status' => $shipmentstatus, 'event_time' => time()));
                $shipment_array   = array() ; 
                $shipment_array['shipment_id'] = $Awbl->id;
                $shipment_array['ship_status'] = $shipmentstatus ;
                $shipment_array['event_time']  = strtotime(date('Y-m-d H:i:s')) ;
                $status_lib = new Status_lib($shipment_array);
                $status_lib->updateStatus();
                   
            }
            $this->data['success'] = 'AWB Shipment Status Update Successfully';
        } else {
            $this->data['error'] = validation_errors();
        }
        $this->layout('shipment_status/index');
    }

    private function validate_upload_data($data)
    {
        $this->form_validation->set_data($data);

        $this->form_validation->set_message('required', '%s is required');
        $this->form_validation->set_message('alpha_numeric_spaces', 'Only Characters, Numbers & Spaces are  allowed in %s');

        $config = array(
            array(
                'field' => 'AWB Number',
                'label' => 'AWB Number',
                'rules' => 'trim|required|alpha_numeric',
            ),
            array(
                'field' => 'Status',
                'label' => 'Status',
                'rules' => 'trim|required',
            ),

        );

        $this->form_validation->set_rules($config);

        if ($this->form_validation->run()) {
            return true;
        } else {
            $this->data['error'] = validation_errors();
            return false;
        }
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


    function change_shipment_status()
    {
      
        $this->load->library('form_validation');
        $config = array(
            array(
                'field' => 'importFile',
                'label' => 'Import File',
                'rules' => 'callback_file_check'
            ),
        );
        $this->form_validation->set_rules($config);
        $message = array();
        if ($this->form_validation->run()) {

            if (is_uploaded_file($_FILES['importFile']['tmp_name'])) {
                // Load CSV reader library
                $this->load->library('csvreader');
                // Parse data from CSV file
                $csvData = $this->csvreader->parse_csv($_FILES['importFile']['tmp_name']);

                if (empty($csvData)) {
                    $message['error'] = $this->data['error'] = 'Blank CSV File';
                }
                if (count($csvData) > 5000) {
                    $message['error'] = $this->data['error'] = 'Only 5000 AWBs are allowed';
                    return false;
                }

                foreach ($csvData as $row_key => $row) {

                    if (!$this->validate_upload_data($row)) {
                        $message['error'] =  $this->data['error'] = 'Row no. ' . ($row_key + 1) . $this->data['error'];
                    }
                }

                $status_array = array("booked", "pending pickup", "in transit", "out for delivery", "delivered", "lost", "damaged", "rto in transit", "rto delivered", "rto lost", "rto damaged", "cancelled", "exception");

                $csv = new Csv_lib();

                $csv->add_row(array('AWB Number', 'Status', 'Message'));
                foreach ($csvData as $row2) {

                    $awb_number = $row2['AWB Number'];
                    $status = strtolower($row2['Status']);
                    $shipmentstatus = strtoupper($row2['Status']);
                    $csv_row = array(
                        $row2['AWB Number'],
                        $row2['Status']
                    );
                    $shipment = $this->shipping_lib->getByAWB($awb_number);

                    if (empty($shipment) || empty($shipment->order_id)) {
                        $csv_row[] = 'AWB Number Not Found';
                        $csv->add_row($csv_row);
                        continue;
                    }


                    if (!in_array(strtolower($status), $status_array)) {
                        $csv_row[] = 'Wrong Status ' . strtoupper($status);
                        $csv->add_row($csv_row);
                        continue;
                    }

                    do_action('shipping.status', $shipment->id, array('ship_status' => $shipmentstatus, 'event_time' => time()));

                    $csv_row[] = 'Success';
                    $csv->add_row($csv_row);
                }
                // 

                //


            }

            if (!empty($message['error'])) {
                $this->session->set_flashdata('error', $message);
            } else {
                $message = $this->data['success'] = 'File uploaded successfully';
                $this->session->set_flashdata('success', $message);
                $csv->export_csv();
            }

            $this->layout('shipment_status/change_shipment_status');
        } else {


            $message = $this->data['error'] = validation_errors();
            $this->session->set_flashdata('error', $message);

            $this->layout('shipment_status/change_shipment_status');
        }
    }

    function download_shipment_status()
    {
        $this->load->library('form_validation');
        $config = array(
            array(
                'field' => 'importFile',
                'label' => 'Import File',
                'rules' => 'callback_file_check'
            ),
        );
        $this->form_validation->set_rules($config);
        $message = array();
        if ($this->form_validation->run()) {

            if (is_uploaded_file($_FILES['importFile']['tmp_name'])) {
                // Load CSV reader library
                $this->load->library('csvreader');
                // Parse data from CSV file
                $csvData = $this->csvreader->parse_csv($_FILES['importFile']['tmp_name']);

                if (empty($csvData)) {
                    $message['error'] = $this->data['error'] = 'Blank CSV File';
                }
                if (count($csvData) > 5000) {
                    $message['error'] = $this->data['error'] = 'Only 5000 AWBs are allowed';
                    return false;
                }

                foreach ($csvData as $row_key => $row) {

                    if (!$this->validate_download_data($row)) {
                        $message['error'] =  $this->data['error'] = 'Row no. ' . ($row_key + 1) . $this->data['error'];
                    }
                }

                $status_array = array("booked", "pending pickup", "in transit", "out for delivery", "delivered", "lost", "damaged", "rto in transit", "rto delivered", "rto lost", "rto damaged", "cancelled", "exception");

                $csv = new Csv_lib();

                $csv->add_row(array('AWB Number', 'Status from Courier Partner', 'Status from deltagloabal Panel'));
                foreach ($csvData as $row2) {

                    $awb_number = $row2['AWB Number'];
                    $status = strtolower($row2['Status from Courier Partner']);
                    $shipmentstatus = strtoupper($row2['Status from Courier Partner']);
                    $csv_row = array(
                        $row2['AWB Number'],
                        $row2['Status from Courier Partner']
                    );
                    $shipment = $this->shipping_lib->getByAWB($awb_number);

                    if (empty($shipment) || empty($shipment->order_id)) {
                        $csv_row[] = 'AWB Number Not Found';
                        $csv->add_row($csv_row);
                        continue;
                    }


                    if (!in_array(strtolower($status), $status_array)) {
                        $csv_row[] = 'Wrong Status ' . strtoupper($status);
                        $csv->add_row($csv_row);
                        continue;
                    }
                    
                    if ($shipment->ship_status == 'new') {
                        $ship_status = 'Processing';
                    } else if ($shipment->ship_status == 'booked') {
                        $ship_status = 'Booked';
                    } elseif ($shipment->ship_status == 'pending pickup') {
                        $ship_status = 'Waiting for Pickup';
                    } elseif (in_array($shipment->ship_status, array('lost', 'damaged'))) {
                        $ship_status = $shipment->ship_status;
                    } elseif ($shipment->ship_status == 'rto') {
                        $ship_status = !empty($shipment->rto_status) ? 'RTO '.ucwords($shipment->rto_status) : 'RTO';
                    } elseif ($shipment->ship_status == 'cancelled') {
                        $ship_status = 'Cancelled';
                    } elseif ($shipment->ship_status == 'exception') {
                        $ship_status = !empty($shipment->ship_message) ? $shipment->ship_message : 'Exception';
                    } else {
                        $ship_status = $shipment->ship_status;
                    }
                    $csv_row[] = ucwords($ship_status);
                    $csv->add_row($csv_row);
                }
            }

            if (!empty($message['error'])) {
                $this->session->set_flashdata('error', $message);
            } else {
                $message = $this->data['success'] = 'File uploaded successfully';
                $this->session->set_flashdata('success', $message);
                $csv->export_csv();
            }

            $this->layout('shipment_status/download_shipment_status');
        } else {


            $message = $this->data['error'] = validation_errors();
            $this->session->set_flashdata('error', $message);

            $this->layout('shipment_status/download_shipment_status');
        }
    }

    private function validate_download_data($data)
    {
        $this->form_validation->set_data($data);

        $this->form_validation->set_message('required', '%s is required');
        $this->form_validation->set_message('alpha_numeric_spaces', 'Only Characters, Numbers & Spaces are  allowed in %s');

        $config = array(
            array(
                'field' => 'AWB Number',
                'label' => 'AWB Number',
                'rules' => 'trim|required|alpha_numeric',
            ),
            array(
                'field' => 'Status from Courier Partner',
                'label' => 'Status from Courier Partner',
                'rules' => 'trim|required',
            ),

        );

        $this->form_validation->set_rules($config);

        if ($this->form_validation->run()) {
            return true;
        } else {
            $this->data['error'] = validation_errors();
            return false;
        }
    }

}
