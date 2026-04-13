<?php

defined('BASEPATH') or exit('No direct script access allowed');

use App\Lib\Logs\User as Log;

class Billing extends Admin_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->userHasAccess('billing');
        $this->load->helper('download');
        $this->np_product_det = (object)[
            'shipment' => (object)array('name' => 'Shipping charges', 'hsn_code' => '996812'),
            'international' => (object)array('name' => 'Shipping charges', 'hsn_code' => '996812'),
            'insurance' => (object)array('name' => 'Insurance charges', 'hsn_code' => '997135'),
            'addon' => (object)array('name' => 'Other Support Services', 'hsn_code' => '998599')
        ];
    }

    function index()
    {
        self::v();
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

    function v($page = 'recharge_logs', $page_no = 1)
    {
        $inner_content = '';
        switch ($page) {

            case 'recharge_logs':
                $inner_content = $this->recharge_logs($page_no);
                break;
            case 'shipping_charges':
                $inner_content = $this->shipping_charges($page_no);
                break;
            case 'weight_reconciliation':
                $inner_content = $this->weight_reconciliation($page_no);
                break;
            case 'invoice':
                $inner_content = $this->invoice($page_no);
                break;
            case 'credit_notes':
                $inner_content = $this->credit_notes($page_no);
                break;
            case 'upload_awb_billing':
                $inner_content = $this->uploadAwbBilling();
                break;
            case 'wallet_adjustments':
                if ($page_no == '1')
                    $page_no = false;
                $inner_content = $this->adjust_wallet($page_no);
                break;
            case 'price_calculator':
                $inner_content = $this->price_calculator();
                break;
            case 'consolidated_wallet':
                $inner_content = $this->consolidated_wallet();
                break;
            case 'freight_reversal':
                $inner_content = $this->freight_reversal();
                break;
            case 'neft_recharge':
                $inner_content = $this->neft_recharge($page_no);
                break;
            case 'wallet_to_bank':
                $inner_content = $this->wallet_to_bank($page_no);
                break;
            case 'withdraw_wallet':
                $inner_content = $this->wallet_to_bank($page, $page_no);
                break;
            default:
        }


        // pr($inner_content); die;

        $this->data['inner_content'] = $inner_content;
        $this->data['view_page'] = $page;
        $this->layout('billing/view');
    }


    function wallet_to_bank($page = 'withdraw_wallet', $page_no = 1)
    {
        $inner_content = '';
        $page = 'withdraw_wallet'; //die;
        switch ($page) {

            case 'withdraw_wallet':

                $inner_content = $this->withdraw_wallet($page_no);
                break;

            default:
        }
        $this->data['inner_content'] = $inner_content;
        $this->data['view_page'] = $page;
        //  $this->layout('credit_notes/view');
        return $this->load->view('admin/credit_notes/view', $this->data, true);
    }


    private function withdraw_wallet($page = 1)
    {


        $this->load->library('admin/bank_lib');


        $this->load->library('admin/credit_notes_lib');
        if (!$this->credit_notes_lib->walletToBankUpload()) {
            if (!empty($this->credit_notes_lib->get_error()))
                $this->data['error'] = $this->credit_notes_lib->get_error();
        } else {
            $this->data['success'] = 'File uploaded successfully';
        }


        $limit = 50;
        $filter = $this->input->get('filter');
        $apply_filters = array();
        $apply_filters['start_date'] = strtotime("-6 days midnight");
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

        if (!empty($filter['withdraw_id'])) {
            $apply_filters['withdraw_id'] = array_map('trim', explode(',', $filter['withdraw_id']));
        }

        if (!empty($filter['utr_no'])) {
            $apply_filters['utr_no'] = array_map('trim', explode(',', $filter['utr_no']));
        }

        if (!empty($filter['seller_id'])) {
            $apply_filters['seller_id'] = $filter['seller_id'];
        }

        if (!empty($filter['paid_status'])) {
            $apply_filters['paid_status'] = $filter['paid_status'];
        }

        $total_row = $this->bank_lib->countHistory($apply_filters);

        $config = array(
            'base_url' => base_url('admin/billing/v/withdraw_wallet'),
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


        $payments = $this->bank_lib->getHistory($limit, $offset, $apply_filters);

        $this->load->library('admin/user_lib');
        $seller_details = $this->user_lib->getUserList();
        $this->data['users'] = $seller_details;


        $this->data['filter'] = $filter;

        $this->data['payments'] = $payments;


        return $this->load->view('admin/credit_notes/pages/withdraw_wallet', $this->data, true);
    }

    private function freight_reversal()
    {
        $this->load->library('admin/billing_lib');
        if (!$this->billing_lib->freightReversalUpload()) {
            $this->data['error'] = $this->billing_lib->get_error();
        } else {
            $this->session->set_flashdata('success', 'File uploaded successfully');
            redirect('admin/billing/v/freight_reversal', true);
        }

        return $this->load->view('admin/billing/pages/freight_reversal', $this->data, true);
    }



    function consolidated_wallet()
    {
        // $filter = $this->input->post('filter');
        $this->load->library('admin/user_lib');
        $seller_details = $this->user_lib->getUserList();

        $this->load->library('admin/wallet_lib');


        $filter = array(
            'start_date' => date('Y-m-d', strtotime('first day of this month')),
            'end_date' => date('Y-m-d', strtotime("tomorrow midnight") - 1),
        );
        $this->load->library('form_validation');
        $config = array(
            array(
                'field' => 'start_date',
                'label' => 'Date',
                'rules' => 'trim|required'
            ),
            array(
                'field' => 'end_date',
                'label' => 'Date',
                'rules' => 'trim|required'
            ),
            array(
                'field' => 'seller_id',
                'label' => 'Seller ID',
                'rules' => 'trim|numeric'
            ),
            array(
                'field' => 'report_type',
                'label' => 'Report Type',
                'rules' => 'trim|required'
            ),

        );

        $this->form_validation->set_rules($config);
        if ($this->form_validation->run()) {
            switch ($this->input->post('report_type')) {
                case 'consolidated':
                    $this->downloadConsolidatedReport();
                    break;
                case 'detailed':
                    $this->downloadDetailedWalletReport();
                    break;
            }
        } else {
            $this->data['error'] = validation_errors();
        }


        $this->data['users'] = $seller_details;
        $this->data['filter'] = $filter;


        return $this->load->view('admin/billing/pages/consolidated_wallet', $this->data, true);
    }

    private function downloadDetailedWalletReport()
    {
        ini_set('memory_limit', -1);
        ini_set('max_execution_time', '2400');

        $apply_filter = array(
            'start_date' => strtotime($this->input->post('start_date') . ' 00:00:00'),
            'end_date' => strtotime($this->input->post('end_date') . ' 23:59:59'),
            'seller_id' => $this->input->post('seller_id'),
            'awb_no' => $this->input->post('awb_no'),
            'txn_type' => $this->input->post('txn_type'),
            'order_type' => $this->input->post('order_type'),
            'txn_ref_type' => $this->input->post('txn_ref_type')
        );

        $query = $this->wallet_lib->exportDetailedWalletReport($apply_filter);

        $this->load->library('export_db');

        $export = new Export_db('slave');
        $export->query($query);
        $filename = 'detailed_wallet_' . time() . '.csv';
        header("Content-Description: File Transfer");
        header("Content-Disposition: attachment; filename=$filename");
        header("Content-Type: application/csv; ");
        $file = fopen('php://output', 'w');
        $header = array("Date", "Seller ID", "Seller Name", "Seller Company", "Order Type", "Amount", "Balance After", "Type", "Notes", "Shipping Id", "Awb Number", "TXN Type", "TXN Sub Type");
        fputcsv($file, $header);

        while ($record = $export->next()) {

            $row = array(
                date('Y-m-d H:i:s', $record->created),
                $record->user_id,
                ucwords($record->fname . ' ' . $record->lname),
                ucwords($record->company_name),
                ucwords($record->order_type),
                round($record->amount, 2),
                round($record->balance_after, 2),
                $record->type,
                $record->notes,
                $record->shipment_id,
                $record->awb_number,
                ucwords($record->txn_for),
                ucwords($record->txn_ref),
            );
            fputcsv($file, $row);
        }
        fclose($file);
        exit;
    }

    private function testwalletconsolidatedetailed()
    {
        ini_set('memory_limit', -1);
        ini_set('max_execution_time', '2400');

        $db = new \App\Model\Wallet();

        $db->setConnection('slave');

        $apply_filters = array(
            'start_date' => strtotime($this->input->post('start_date') . ' 00:00:00'),
            'end_date' => strtotime($this->input->post('end_date') . ' 23:59:59'),
            'seller_id' => $this->input->post('seller_id'),
            'awb_no' => $this->input->post('awb_no'),
            'txn_type' => $this->input->post('txn_type'),
            'order_type' => $this->input->post('order_type'),
            'txn_ref_type' => $this->input->post('txn_ref_type')
        );

        if (!empty($apply_filters['awb_no'])) {
            $db = $db->whereIn('awb_number',  array_map('trim', explode(',', $apply_filters['awb_no'])));
        }

        if (!empty($apply_filters['start_date'])) {

            $db = $db->where('created', '>=', $apply_filters['start_date']);
        }

        if (!empty($apply_filters['end_date'])) {
            $db = $db->where('created', '<=', $apply_filters['end_date']);
        }

        if (!empty($apply_filters['seller_id'])) {
            $db = $db->where('user_id', $apply_filters['seller_id']);
        }

        if (!empty($apply_filters['txn_type'])) {

            $db = $db->whereIn('txn_for', $apply_filters['txn_type']);
        }

        if (!empty($apply_filters['order_type'])) {
            $db = $db->whereHas('shipments', function ($q) use ($apply_filters) {
                $q->where('order_type', $apply_filters['order_type']);
            });
        }

        if (!empty($apply_filters['txn_ref_type'])) {

            $db = $db->whereIn('txn_ref', $apply_filters['txn_ref_type']);
        }

        $db = $db->where('txn_for', 'shipment');
        // $data = $db->with(['users'])->orderBy('created', 'desc')->get()->toArray();
        //  pr($data);die;
        $filename = 'detailed_wallet_' . time() . '.csv';
        header("Content-Description: File Transfer");
        header("Content-Disposition: attachment; filename=$filename");
        header("Content-Type: application/csv; ");
        $file = fopen('php://output', 'w');
        $header = array("Date", "Seller ID", "Seller Name", "Seller Company", "Order Type", "Amount", "Balance After", "Type", "Notes", "Shipping Id", "Awb Number", "TXN Type", "TXN Sub Type");
        fputcsv($file, $header);

        $db->with(['shipments', 'users'])->orderBy('created', 'desc')->chunk(4000, function ($records) use ($file) {
            foreach ($records as $record) {
                if ($record->shipments->order_type == 'ecom') {
                    $otype = "Ecom";
                } else if ($record->shipments->order_type == 'international') {
                    $otype = "International";
                } else if ($record->shipments->order_type == 'cargo') {
                    $otype = "Cargo";
                } else {
                    $otype = '';
                }
                $row = array(
                    date('Y-m-d H:i:s', $record->created),
                    $record->users->id,
                    ucwords($record->users->fname . ' ' . $record->users->lname),
                    ucwords($record->users->company_name),
                    $otype,
                    round($record->amount, 2),
                    round($record->balance_after, 2),
                    $record->type,
                    $record->notes,
                    $record->shipments->id,
                    $record->shipments->awb_number,
                    ucwords($record->txn_for),
                    ucwords($record->txn_ref),
                );

                fputcsv($file, $row);
            }
        });
        fclose($file);
        exit;
    }

    private function downloadConsolidatedReport()
    {

        $apply_filter = array(
            'start_date' => strtotime($this->input->post('start_date') . ' 00:00:00'),
            'end_date' => strtotime($this->input->post('end_date') . ' 23:59:59'),
            'seller_id' => $this->input->post('seller_id'),
            'seller_id' => $this->input->post('seller_id'),
            'awb_no' =>  $this->input->post('awb_no'),
            'txn_type' => $this->input->post('txn_type'),
            'txn_ref_type' => $this->input->post('txn_ref_type')

        );

        $query = $this->wallet_lib->consolidated_wallet($apply_filter);

        $this->load->library('export_db');

        $export = new Export_db('slave');
        $export->query($query);
        $filename = 'consolidated_wallet_' . time() . '.csv';
        header("Content-Description: File Transfer");
        header("Content-Disposition: attachment; filename=$filename");
        header("Content-Type: application/csv; ");
        $file = fopen('php://output', 'w');
        $header = array("Seller ID", "Seller Name", "Seller Company", "Total Credits", "Total Debits", "Wallet Balance");
        fputcsv($file, $header);
        while ($record = $export->next()) {

            $row = array(
                $record->user_id,
                ucwords($record->user_fname . ' ' . $record->user_lname),
                ucwords($record->company_name),
                round($record->credit_amount),
                round($record->debit_amount),
                round($record->wallet_balance),
            );
            fputcsv($file, $row);
        }
        fclose($file);
        exit;
    }

    function uploadAwbBilling()
    {
        $this->load->library('admin/billing_lib');

        if (!$this->billing_lib->importCourierBilling()) {
            $this->data['error'] = $this->billing_lib->get_error();
        } else {
            $this->data['success'] = 'Records has been updated';
        }

        return $this->load->view('admin/billing/pages/upload_weight_upload', $this->data, true);
    }

    function adjust_wallet($user_id = false)
    {
        if(!empty($this->input->post('seller_id'))){
            $user_id =$this->input->post('seller_id');
        }
        if ($user_id) {
            return $this->adjust_wallet_for_user($user_id);
        } else {
            $seller_details = $this->user_lib->getUserListFilter('');
            $this->data['users']=$seller_details;
            return $this->load->view('admin/billing/pages/adjust_wallet', $this->data, true);
        }
    }

    function adjust_wallet_for_user($user_id = false)
    {
        $this->load->library('admin/billing_lib');

        $this->load->library('admin/user_lib');
        $user = $this->user_lib->getByID($user_id);
        if (empty($user) || $user->parent_id != '0') {
            $this->session->set_flashdata('error', 'Wallet Adjustment not available for this user');
            redirect(base_url('admin/billing/v/wallet_adjustments'), true);
        }

        $this->data['user'] = $user;
        if (!$this->billing_lib->adjustWalletForm($user_id, $this->user->user_id)) {
            $this->data['error'] = $this->billing_lib->get_error();
        } else {
            $this->session->set_flashdata('success', 'Wallet Adjustment Done');
            redirect(current_url(), true);
        }
        $this->data['users']=$seller_details;
        return $this->load->view('admin/billing/pages/adjust_wallet_user', $this->data, true);
    }

    

    function invoice($page = 1)
    {

        $this->load->library('admin/invoice_lib');
        $limit = 50;
        $filter = $this->input->post('filter');
        $apply_filters = array();

        if ($this->input->post('generate') == 'invoice') {
            //list all users for invoice 
            $this->load->library('admin/user_lib');
            $sellers = $this->user_lib->getUserListForInvoice();
            foreach ($sellers as $seller) {
                if ($seller->is_admin == '0')
                    do_action('invoice.generate', $seller->id, date('M Y', strtotime('last month')));
            }
        }

        if (!empty($filter['seller_id'])) {
            $apply_filters['seller_id'] = $filter['seller_id'];
        }

        if (!empty($filter['month'])) {
            $apply_filters['month'] = $filter['month'];
        }
        if (!empty($filter['invoice_type'])) {
            $apply_filters['invoice_type'] = $filter['invoice_type'];
        }
        if (!empty($filter['service_type'])) {
            $apply_filters['service_type'] = $filter['service_type'];
        }
        if (!empty($filter['invoice_no'])) {
            $apply_filters['invoice_no'] = array_map('trim', explode(',', $filter['invoice_no']));
        }
        $total_row = $this->invoice_lib->countInvoice($apply_filters);
        $config = array(
            'base_url' => base_url('admin/billing/v/invoice'),
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
        $invoices = $this->invoice_lib->fetchInvocie($limit, $offset, $apply_filters);
        $seller_details = '';
        $this->load->library('admin/user_lib');
        //if (!empty($filter['seller_id']))
       $seller_details = $this->user_lib->getUserListFilter('');

        $this->data['users'] = $seller_details;
        $this->data['invoices'] = $invoices;
        $this->data['filter'] = $filter;

        $invoices_months = $this->invoice_lib->fetchInvoiceMonthrouped();
        $this->data['months'] = $invoices_months;

        return $this->load->view('admin/billing/pages/invoices', $this->data, true);
    }


    function exportInvoice()
    {
        $this->load->library('admin/invoice_lib');
        $limit = 50;
        $filter = $this->input->get('filter');
        $apply_filters = array();


        if (!empty($filter['seller_id'])) {
            $apply_filters['seller_id'] = $filter['seller_id'];
        }

        if (!empty($filter['month'])) {
            $apply_filters['month'] = $filter['month'];
        }

        if (!empty($filter['invoice_type'])) {
            $apply_filters['invoice_type'] = $filter['invoice_type'];
        }
        if (!empty($filter['service_type'])) {
            $apply_filters['service_type'] = $filter['service_type'];
        }

        $invoices = $this->invoice_lib->fetchInvocie(15000, 0, $apply_filters);

        $filename = 'Invoices.csv';
        header("Content-Description: File Transfer");
        header("Content-Disposition: attachment; filename=$filename");
        header("Content-Type: application/csv; ");
        $file = fopen('php://output', 'w');
        $header = array("Invoice No", "Invoice Date", "Seller Id", "Seller First Name", "Seller Last Name", "Seller Company", "Seller Email", "Seller Address", "GST No", "Invoice Type", "Service Type", "State", "Charges", "IGST", "CGST", "SGST", "Invoice Amount", "Month", "Description", "HSN CODE", "GST Percentage", "CSV", "PDF");
        fputcsv($file, $header);
        foreach ($invoices as $invoice) {
            $company_name = empty($invoice->legal_name) ? $invoice->company_name : $invoice->legal_name;
            $city = empty($invoice->legal_city) ? $invoice->cmp_city : $invoice->legal_city;
            $state = empty($invoice->legal_state) ? $invoice->cmp_state : $invoice->legal_state;
            $address = empty($invoice->legal_address) ? $invoice->cmp_address : $invoice->legal_address;
            $gstno = empty($invoice->legal_gstno) ? $invoice->gstno : $invoice->legal_gstno;
            $row = array(
                ($invoice->invoice_no != '') ? $invoice->invoice_no : 'NPPL/' . $invoice->id,
                date('d-M-Y', $invoice->created),
                $invoice->sellerid,
                ucwords($invoice->firstname),
                ucwords($invoice->lastname),
                ucwords($company_name),
                $invoice->user_email,
                ucwords($address . ' ' . $city),
                strtoupper($gstno),
                strtoupper($invoice->invoice_type),
                ucwords($invoice->service_type),
                ucwords($state),
                round($invoice->pre_gst, 2),
                round($invoice->igst, 2),
                round($invoice->cgst, 2),
                round($invoice->sgst, 2),
                round($invoice->total_amount, 2),
                strtoupper($invoice->month),
                $this->np_product_det->{$invoice->service_type}->name ?? 'Shipping Charges',
                $this->np_product_det->{$invoice->service_type}->hsn_code ?? '996812',
                '18',
                strpos($invoice->csv_file, "amazonaws.com") ? $invoice->csv_file : (base_url("assets/invoice/") . $invoice->csv_file),
                strpos($invoice->pdf_file, "amazonaws.com") ? $invoice->pdf_file : (base_url("assets/invoice/") . $invoice->pdf_file)
            );
            fputcsv($file, $row);
        }
        fclose($file);
        exit;
    }


    function exportCreditNotes()
    {
        $this->load->library('admin/invoice_lib');
        $limit = 50;
        $filter = $this->input->get('filter');
        $apply_filters = array();


        if (!empty($filter['seller_id'])) {
            $apply_filters['seller_id'] = $filter['seller_id'];
        }

        if (!empty($filter['month'])) {
            $apply_filters['month'] = $filter['month'];
        }

        if (!empty($filter['invoice_type'])) {
            $apply_filters['invoice_type'] = $filter['invoice_type'];
        }
        if (!empty($filter['service_type'])) {
            $apply_filters['service_type'] = $filter['service_type'];
        }

        $invoices = $this->invoice_lib->fetchCreditNotes(15000, 0, $apply_filters);

        $filename = 'CreditNotes.csv';
        header("Content-Description: File Transfer");
        header("Content-Disposition: attachment; filename=$filename");
        header("Content-Type: application/csv; ");
        $file = fopen('php://output', 'w');
        $header = array("Credit Note No", "CN Date", "Seller Id", "Seller First Name", "Seller Last Name", "Seller Company", "Seller Email", "Seller Address", "GST No", "Invoice Type", "Service Type", "State", "Charges", "IGST", "CGST", "SGST", "CN Amount", "Month", "Description", "HSN CODE", "GST Percentage", "CSV", "PDF");
        fputcsv($file, $header);
        foreach ($invoices as $invoice) {
            $company_name = empty($invoice->legal_name) ? $invoice->company_name : $invoice->legal_name;
            $city = empty($invoice->legal_city) ? $invoice->cmp_city : $invoice->legal_city;
            $state = empty($invoice->legal_state) ? $invoice->cmp_state : $invoice->legal_state;
            $address = empty($invoice->legal_address) ? $invoice->cmp_address : $invoice->legal_address;
            $gstno = empty($invoice->legal_gstno) ? $invoice->gstno : $invoice->legal_gstno;

            $row = array(
                ($invoice->invoice_no != '') ? $invoice->invoice_no : 'NPPL/CN/' . sprintf('%03d', $invoice->id),
                date('d-M-Y', $invoice->created),
                $invoice->sellerid,
                ucwords($invoice->firstname),
                ucwords($invoice->lastname),
                ucwords($company_name),
                $invoice->user_email,
                ucwords($address . ' ' . $city),
                strtoupper($gstno),
                strtoupper($invoice->invoice_type),
                ucwords($invoice->service_type),
                ucwords($state),
                round($invoice->pre_gst, 2),
                round($invoice->igst, 2),
                round($invoice->cgst, 2),
                round($invoice->sgst, 2),
                round($invoice->total_amount, 2),
                strtoupper($invoice->month),
                $this->np_product_det->{$invoice->service_type}->name ?? 'Shipping Charges',
                $this->np_product_det->{$invoice->service_type}->hsn_code ?? '996812',
                '18',
                strpos($invoice->csv_file, "amazonaws.com") ? $invoice->csv_file : (base_url("assets/invoice/") . $invoice->csv_file),
                strpos($invoice->pdf_file, "amazonaws.com") ? $invoice->pdf_file : (base_url("assets/invoice/") . $invoice->pdf_file)
            );
            fputcsv($file, $row);
        }
        fclose($file);
        exit;
    }


    function credit_notes($page = 1)
    {

        $this->load->library('admin/invoice_lib');
        $limit = 50;
        $filter = $this->input->post('filter');
        $apply_filters = array();


        if (!empty($filter['seller_id'])) {
            $apply_filters['seller_id'] = $filter['seller_id'];
        }

        if (!empty($filter['month'])) {
            $apply_filters['month'] = $filter['month'];
        }

        if (!empty($filter['invoice_type'])) {
            $apply_filters['invoice_type'] = $filter['invoice_type'];
        }
        if (!empty($filter['service_type'])) {
            $apply_filters['service_type'] = $filter['service_type'];
        }
        if (!empty($filter['invoice_no'])) {
            $apply_filters['invoice_no'] = array_map('trim', explode(',', $filter['invoice_no']));
        }
        $total_row = $this->invoice_lib->countCreditNotes($apply_filters);
        $config = array(
            'base_url' => base_url('admin/billing/v/credit_notes'),
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
        $invoices = $this->invoice_lib->fetchCreditNotes($limit, $offset, $apply_filters);
        $seller_details = '';
        $this->load->library('admin/user_lib');
        //if (!empty($filter['seller_id']))
        $seller_details = $this->user_lib->getUserListFilter('');

        $this->data['users'] = $seller_details;
        $this->data['invoices'] = $invoices;
        $this->data['filter'] = $filter;

        $invoices_months = $this->invoice_lib->fetchCreditNotesMonthrouped();
        $this->data['months'] = $invoices_months;

        return $this->load->view('admin/billing/pages/credit_notes', $this->data, true);
    }


    function weight_reconciliation()
    {
        $this->load->library('admin/billing_lib');
        if (!$shipments = $this->billing_lib->importWeightFile()) {
            $this->data['error'] = $this->billing_lib->get_error();
        }

        if (!empty($shipments)) {
            $this->data['shipments'] = $shipments;
        }
        return $this->load->view('admin/billing/pages/weight_reconciliation', $this->data, true);
    }

    function apply_weight()
    {

        $this->load->library('form_validation');
        $config = array(
            array(
                'field' => 'shipping_ids[]',
                'label' => 'Shipments ID',
                'rules' => 'trim|required|numeric'
            ),
            array(
                'field' => 'uploaded_weight[]',
                'label' => 'Uploaded Weight',
                'rules' => 'trim|required|numeric'
            ),
        );



        $this->form_validation->set_rules($config);

        if (!$this->form_validation->run()) {
            $this->session->set_flashdata('error', validation_errors());
            redirect('admin/billing/v/weight_reconciliation', true);
        }

        $shipping_ids = $this->input->post('shipping_ids');
        $weights = $this->input->post('uploaded_weight');

        $this->load->library('admin/shipping_lib');
        foreach ($shipping_ids as $shipping_id) {
            $this->shipping_lib->applyAWBWeight($shipping_id, $weights[$shipping_id]);
        }

        $this->session->set_flashdata('success', 'Records has been updated');
        redirect('admin/billing/v/weight_reconciliation', true);
    }

    function recharge_logs($page = 1)
    {
        $this->load->library('admin/wallet_lib');
        $limit = 50;
        $filter = $this->input->post('filter');
        if ($filter == null) {
            $filter['txn_for'] = "recharge";
        } else {
            $filter = $this->input->post('filter');
        }
        $apply_filters = array();
        if (!empty($filter['txn_for'])) {
            switch ($filter['txn_for']) {
                case 'whatsapp':
                    $apply_filters['txn_for'] = 'whatsapp';
                    break;        
                case 'email':
                    $apply_filters['txn_for'] = 'email';
                    break;        
                case 'sms':
                    $apply_filters['txn_for'] = 'sms';
                    break; 
                case 'ivr':
                    $apply_filters['txn_for'] = 'ivr';
                    break; 
                case 'all_communication':
                    $apply_filters['txn_for'] = 'all_communication';
                    break;       
                default:
                    $apply_filters['txn_for'] = $filter['txn_for'];
            }
        }
        if (!empty($filter['shipment_id'])) {
            $apply_filters['shipment_id'] = $filter['shipment_id'];
        }

        if (!empty($filter['shipment_id'])) {
            $apply_filters['shipment_id'] = $filter['shipment_id'];
        }

        $apply_filters['start_date'] = strtotime("today midnight");
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

        if (!empty($filter['seller_id'])) {
            $apply_filters['seller_id'] = $filter['seller_id'];
        }
        $total_row = $this->wallet_lib->countByUserID($apply_filters);
        $config = array(
            'base_url' => base_url('admin/billing/v/recharge_logs'),
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
        $history = $this->wallet_lib->fetchByUserID($limit, $offset, $apply_filters);

        $seller_details = '';
        $this->load->library('admin/user_lib');
        //if (!empty($filter['seller_id']))
        $seller_details = $this->user_lib->getUserListFilter('');

        $this->data['users'] = $seller_details;
        $this->data['history'] = $history;
        $this->data['filter'] = $filter;
        return $this->load->view('admin/billing/pages/recharge_logs', $this->data, true);
    }

    function communications_recharge_logs_export()
    {
        $this->load->library('wallet_lib');

        $filter = $this->input->get('filter');

        $apply_filters = array();

        $apply_filters['start_date'] = strtotime("-30 days midnight");
        $apply_filters['end_date'] = strtotime("tomorrow midnight") - 1;

        if (!empty($filter['seller_id'])) {
            $apply_filters['seller_id'] = $filter['seller_id'];
        }

        if (!empty($filter['txn_for'])) {
            switch ($filter['txn_for']) {
                case 'shipment_refund':
                    $apply_filters['txn_for'] = 'shipment';
                    $apply_filters['txn_ref'] = 'refund';
                    break;
                case 'ivr_number':
                    $apply_filters['txn_for'] = 'addon';
                    $apply_filters['txn_ref'] = 'ivr_number';
                    break;
                case 'ivr_call':
                    $apply_filters['txn_for'] = 'addon';
                    $apply_filters['txn_ref'] = 'ivr_call';
                    break;     
                default:
                    $apply_filters['txn_for'] = $filter['txn_for'];
            }
        }

        if (!empty($filter['shipment_id'])) {
            $apply_filters['shipment_id'] = $filter['shipment_id'];
        }

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

        /* $history = $this->wallet_lib->fetchByUserIDCommunication($this->user->account_id, 20000, 0, $apply_filters);

        $filename = 'seller_recharge_logs_' . time() . rand(1111, 9999) . '.csv';
        header("Content-Description: File Transfer");
        header("Content-Disposition: attachment; filename=$filename");
        header("Content-Type: application/csv; ");
        $file = fopen('php://output', 'w');
        $header = array("DATE", "TXN TYPE", "REF NO#", "TRANSACTION ID", "CREDIT(₹)", "DEBIT(₹)", "CLOSING BALANCE(₹)", "DESCRIPTION");
        fputcsv($file, $header);
        foreach ($history as $his) {
            switch ($his->txn_for) {
                case 'whatsapp':
                    $txn_type = 'Whatsapp';
                    break;
                case 'sms':
                    $txn_type = 'SMS';
                    break;
                case 'email':
                    $txn_type = 'Email';
                    break;
                case 'ivr':
                    $txn_type = 'IVR';
                    break;
                case 'all_communication':
                    $txn_type = 'Communication';
                    break;
                default:
                    $txn_type = '-';
            };

            $row = array(
                (!empty($his->created)) ? date('M d, Y', $his->created) : '',
                $txn_type,
                $his->ref_id,
                '#' . $his->id,
                ($his->type == 'credit') ? round($his->amount, 2) : '-',
                ($his->type == 'debit') ? round($his->amount, 2) : '-',
                round($his->balance_after, 2),
                ucwords($his->notes),
            );
            fputcsv($file, $row);
        }
        fclose($file);
        exit; */

        $history = $this->wallet_lib->fetchByUserIDCommunication($filter['seller_id'], 20000, 0, $apply_filters);

        $filename = 'seller_recharge_logs_' . time() . rand(1111, 9999) . '.csv';

        header("Content-Description: File Transfer");
        header("Content-Disposition: attachment; filename=$filename");
        header("Content-Type: application/csv; ");

        $file = fopen('php://output', 'w');

        // Define header row for CSV
        $header = array(
            "ORDER NUMBER",
            "AWB NUMBER",
            "RESPONSE (IF ANY)",
            "SENT AT",
            "DELIVERED AT",
            "READ AT",
            "DELIVERY STATUS",
            "REMARKS",
            "AGING SECONDS",
            "REFERENCE ID",
            "TXN FOR",
            "TXN REF",
            "PACK TYPE",
            "TYPE",
            "NOTES",
            "BALANCE BEFORE",
            "AMOUNT",
            "BALANCE AFTER"
        );
        fputcsv($file, $header);

        // Loop through results
        foreach ($history as $his) {
            
            $row = array(
                $his->order_number,
                $his->awb_number,
                $his->response,
                $his->sent_at,
                $his->delivered_at,
                $his->read_at,
                $his->delivery_status,
                $his->remarks,
                $his->aging_seconds,
                $his->ref_id,
                $his->txn_for,
                $his->txn_ref,
                $his->pack_type,
                $his->type,
                $his->notes,
                round($his->balance_before, 2),
                round($his->amount, 2),
                round($his->balance_after, 2)
            );
            fputcsv($file, $row);
        }

        fclose($file);
        exit;

    }

    function recharge_logsexportCSV()
    {
        $this->load->library('admin/wallet_lib');
        $filter = $this->input->get('filter');
        $apply_filters = array();

        if (!empty($filter['txn_for'])) {
            $apply_filters['txn_for'] = $filter['txn_for'];
        }

        if (!empty($filter['shipment_id'])) {
            $apply_filters['shipment_id'] = $filter['shipment_id'];
        }

        $apply_filters['start_date'] = strtotime("-6 days midnight");
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

        if (!empty($filter['seller_id'])) {
            $apply_filters['seller_id'] = $filter['seller_id'];
        }

        $this->data['filter'] = $filter;
        $history = $this->wallet_lib->fetchByUserID(15000, 0, $apply_filters);

        $filename = 'recharge_logs_sheet_' . time() . '.csv';
        header("Content-Description: File Transfer");
        header("Content-Disposition: attachment; filename=$filename");
        header("Content-Type: application/csv; ");
        $file = fopen('php://output', 'w');
        $header = array("Date", "Seller ID", "Company Name", "Seller Name", "Transation ID", "TXN Type", "Credit", "Debit", "Description");
        fputcsv($file, $header);


        foreach ($history as $his) {
            $txn_for = '';
            switch ($his->txn_for) {
                case 'shipment':
                    $txn_for =  'Shipping';
                    break;
                case 'credits':
                    $txn_for =  'Credit Note';
                    break;
                case 'cod':
                    $txn_for =  'COD Adjustments';
                    break;
                case 'neft':
                    $txn_for =  'Recharge - NEFT';
                    break;
                case 'recharge':
                    $txn_for =  (!empty(trim($his->gateway)) ? "Recharge - " . ucfirst($his->gateway) : 'Recharge - Razorpay');
                    break;
                case 'promotion':
                    $txn_for =  'Promotion';
                    break;

                case 'lost':
                    $txn_for =   'lost';
                    break;
                case 'damaged':
                    $txn_for =   'damaged';
                    break;
                case 'promotion':
                    $txn_for =   'promotion';
                    break;
                case 'wallet_to_wallet_transfer':
                    $txn_for =   'Wallet to wallet transfer';
                    break;
                case 'tds_refund':
                    $txn_for =   'Tds refund';
                    break;
                case 'customer_refund':
                    $txn_for =   'Customer refund';
                    break;


                default:
                    $txn_for =  '-';
            };
            $row = array(
                date('Y-m-d', $his->created),
                $his->userid,
                $his->company_name,
                ucwords($his->fname . ' ' . $his->lname),
                $his->id,
                ucwords($txn_for),
                $his->type == 'credit' ? round($his->amount, 2) : '0.00',
                $his->type == 'debit' ? round($his->amount, 2) : '0.00',
                $his->notes,
            );
            fputcsv($file, $row);
        }
        fclose($file);
        exit;
    }

    function shipping_charges($page = 1)
    {
        $this->load->library('admin/shipping_lib');
        $limit = 50;
        $filter = $this->input->post('filter');
        $apply_filters = array();
        if (!empty($filter['awb_no'])) {
            $apply_filters['awb_no'] = array_map('trim', explode(',', $filter['awb_no']));
        }

        $apply_filters['start_date'] = strtotime("today midnight");
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


        if (!empty($filter['seller_id'])) {
            $apply_filters['seller_id'] = $filter['seller_id'];
        }
        $total_row = '0';
        $total_rows = $this->shipping_lib->countByUserIDNew($apply_filters);
        if (!empty($total_rows)) {
            $total_row = $total_rows[0]->totl;
        }
        /// die;
        $config = array(
            'base_url' => base_url('admin/billing/v/shipping_charges'),
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
        $history = $this->shipping_lib->getByUserIDNew($limit, $offset, $apply_filters);
        $seller_details = '';
        $this->load->library('admin/user_lib');
        //if (!empty($filter['seller_id']))
        $seller_details = $this->user_lib->getUserListFilter('');

        $this->data['users'] = $seller_details;
        $this->data['history'] = $history;
        $this->data['filter'] = $filter;

        //echo "hello"; die;
        return $this->load->view('admin/billing/pages/shipping_charges', $this->data, true);
    }

    function shippingChargesExport()
    {
        $this->load->library('admin/shipping_lib');

        $filter = $this->input->get('filter');
        $apply_filters = array();
        if (!empty($filter['awb_no'])) {
            $apply_filters['awb_no'] = array_map('trim', explode(',', $filter['awb_no']));
        }

        $apply_filters['start_date'] = strtotime("today midnight");
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

        if (!empty($filter['seller_id'])) {
            $apply_filters['seller_id'] = $filter['seller_id'];
        }

        ini_set('memory_limit', -1);
        ini_set('max_execution_time', '2080');

        //$query = $this->shipping_lib->exportShipments(1500000, 0, $apply_filters);//exportShipmentsNew
        $query = $this->shipping_lib->exportShipmentsNew(1500000, 0, $apply_filters); //exportShipmentsNew
        $this->load->library('export_db');
        //echo "new =====>".$query; die;
        $export = new Export_db('slave');
        $export->query($query);

        $filename = 'shipping_charges' . time() . '.csv';

        header("Content-Description: File Transfer");
        header("Content-Disposition: attachment; filename=$filename");
        header("Content-Type: application/csv; ");
        $file = fopen('php://output', 'w');
        $header = array("Shipment Created", "Company Name", "Courier", "AWB Number", "Status", "Freight Charges", "COD Charges", "Shipment Insurance Charges", "ENTERED WGT(KG)", "APPLIED WGT(KG)", "EXTRA WGT CHARGES", "RTO Charges", "COD Charge Reversed", "RTO Extra Wgt Charges", "TOTAL CHARGES", "Courier Billed", "P/L");
        fputcsv($file, $header);

        while ($his = $export->next()) {
            $total = (($his->courier_fees > 0) ? round($his->courier_fees, 2) : '0') + (($his->insurance_price > 0) ? round($his->insurance_price, 2) : '0') + (($his->cod_fees > 0) ? round($his->cod_fees, 2) : '0') + (($his->extra_weight_charges > 0) ? round($his->extra_weight_charges, 2) : '0') + (($his->rto_charges > 0) ? round($his->rto_charges, 2) : '0') + (($his->rto_extra_weight_charges > 0) ? round($his->rto_extra_weight_charges, 2) : '0') - (($his->cod_reverse_amount > 0) ? round($his->cod_reverse_amount, 2) : '0');
            $row = array(
                date('Y-m-d', $his->shipping_created),
                ucwords($his->company_name),
                ucwords($his->courier_name),
                $his->awb_number,
                strtoupper($his->ship_status),
                round($his->courier_fees, 2),
                round($his->cod_fees, 2),
                round($his->insurance_price, 2),
                !empty($his->package_weight) ? round($his->package_weight / 1000, 3) : '0.5',
                ($his->charged_weight > $his->package_weight) ? round($his->charged_weight / 1000, 3) : '0',
                ($his->extra_weight_charges > 0) ? round($his->extra_weight_charges, 2) : '0',
                ($his->rto_charges > 0) ? round($his->rto_charges, 2) : '0',
                ($his->cod_reverse_amount > 0) ? '-' . round($his->cod_reverse_amount, 2) : '0',
                ($his->rto_extra_weight_charges > 0) ? round($his->rto_extra_weight_charges, 2) : '0',
                round($total, 2),
                round($his->courier_billed, 2),
                ($his->courier_billed > 0) ? round($total - $his->courier_billed, 2) : '0'
            );
            fputcsv($file, $row);
        }
        fclose($file);
        exit;
    }

    function price_calculator()
    {
        $this->load->library('pricing_lib');
        $this->load->library('courier_lib');
        $couriers = $this->courier_lib->userAvailableCouriers();
        $this->load->library('admin/user_lib');
        $users = $this->user_lib->getUserList();
        $this->data['couriers'] = $couriers;
        $this->data['users'] = $users;
        $this->load->library('admin/plans_lib');
        $this->data['plan'] = $this->plans_lib->getAllPlans();
        return $this->load->view('admin/billing/pages/price_calculator', $this->data, true);
    }

    function calculate_pricing()
    {
        $this->load->library('form_validation');
        $config = array(
            array(
                'field' => 'seller_id',
                'label' => 'Seller Name',
                'rules' => 'required'
            ),
            array(
                'field' => 'origin',
                'label' => 'Origin Pincode',
                'rules' => 'trim|required|exact_length[6]|numeric'
            ),
            array(
                'field' => 'destination',
                'label' => 'Destination Pincode',
                'rules' => 'trim|required|exact_length[6]|numeric'
            ),
            array(
                'field' => 'weight',
                'label' => 'Weight',
                'rules' => 'trim|required|numeric|greater_than[0]'
            ),
            array(
                'field' => 'cod',
                'label' => 'COD',
                'rules' => 'trim|required|in_list[yes,no]'
            ),
        );
        if ($this->input->post('cod') == 'yes') {
            $config[] = array(
                'field' => 'cod_amount',
                'label' => 'COD Amount',
                'rules' => 'trim|required|numeric'
            );
        }
        $this->form_validation->set_rules($config);
        if ($this->form_validation->run()) {

            $this->load->library('user_lib');
            $user = $this->user_lib->getByID($this->input->post('seller_id'));
            $origin = $this->input->post('origin');
            $destination = $this->input->post('destination');
            $weight = $this->input->post('weight') * 1000;
            $length = $this->input->post('length');
            $height = $this->input->post('height');
            $breadth = $this->input->post('breadth');

            $cod = $this->input->post('cod');
            if ($cod == 'yes') {
                $order_type = 'cod';
            } else {
                $order_type = 'prepaid';
            }
            $cod_amount = $this->input->post('cod_amount');
            $this->load->library('pricing_lib');
            $this->load->library('courier_lib');
            $user_couriers = $this->courier_lib->userAvailableCouriers($this->input->post('seller_id'));
            //check pin code serviceblity
            $this->load->library('pincode_lib');
            $couriers = $this->pincode_lib->getPincodeService($destination, $order_type);
            $return = array();
            if (!empty($couriers)) { //get courier price
                foreach ($couriers as $key => $courier) {
                    if (!array_key_exists($courier->id, $user_couriers)) {
                        unset($couriers[$key]);
                    } else {
                        $pricing = new Pricing_lib();
                        $pricing->setPlan($user->pricing_plan);
                        $pricing->setCourier($courier->id);
                        $pricing->setOrigin($origin);
                        $pricing->setDestination($destination);
                        $pricing->setType($order_type);
                        $pricing->setAmount($cod_amount);
                        $pricing->setWeight($weight);
                        $pricing->setLength($length);
                        $pricing->setBreadth($breadth);
                        $pricing->setHeight($height);
                        $price = $pricing->calculateCost();
                        $courier->courier_charges = round($price['courier_charges'] / 1.18);
                        $courier->cod_charges = round($price['cod_charges'] / 1.18);
                        $courier->total_price = round($price['total'] / 1.18);
                        $return[] = $courier;
                    }
                }
                $this->data['json'] = array('success' => $return);
            } else {
                $this->data['json'] = array('error' => 'Delivery pincode is not serviceable');
            }
        } else {
            $this->data['json'] = array('error' => strip_tags(validation_errors()));
        }
        $this->layout(false, 'json');
    }

    function neft_recharge($page = 1)
    {
        $this->load->library('payment_lib');
        $limit = 50;
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


        if (!empty($filter['seller_id'])) {
            $apply_filters['seller_id'] = $filter['seller_id'];
        }

        if (!empty($filter['utr_no'])) {
            $apply_filters['utr_no'] = $filter['utr_no'];
        }

        $total_row = $this->payment_lib->countByNeftPayment($apply_filters);
        $config = array(
            'base_url' => base_url('admin/billing/v/neft_recharge'),
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
        $neftpament = $this->payment_lib->getByNeftPayment($limit, $offset, $apply_filters);
        $seller_details = '';
        $this->load->library('admin/user_lib');
        if (!empty($filter['seller_id']))
            $seller_details = $this->user_lib->getUserListFilter($filter['seller_id']);

        $this->data['users'] = $seller_details;
        $this->data['neftpament'] = $neftpament;
        $this->data['filter'] = $filter;
        return $this->load->view('admin/billing/pages/neft_recharge', $this->data, true);
    }

    function neftPaymentExport()
    {
        $this->load->library('payment_lib');

        $filter = $this->input->get('filter');
        $apply_filters = array();

        $apply_filters['start_date'] = strtotime("today midnight");
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

        if (!empty($filter['seller_id'])) {
            $apply_filters['seller_id'] = $filter['seller_id'];
        }

        if (!empty($filter['utr_no'])) {
            $apply_filters['utr_no'] = $filter['utr_no'];
        }

        $query = $this->payment_lib->getByNeftPaymentExport($apply_filters);

        $this->load->library('export_db');
        $export = new Export_db('slave');
        $export->query($query);

        $filename = 'neft_recharge' . time() . '.csv';

        header("Content-Description: File Transfer");
        header("Content-Disposition: attachment; filename=$filename");
        header("Content-Type: application/csv; ");
        $file = fopen('php://output', 'w');
        $header = array("Recharge Date", "Seller ID", "Company Name", "Amount", "Utr Number");
        fputcsv($file, $header);

        while ($neft = $export->next()) {

            $row = array(
                date('Y-m-d H:i:s', $neft->created),
                $neft->userid,
                ucwords($neft->company_name . ' (' . $neft->user_fname . ' ' . $neft->user_lname . ')'),
                $neft->amount,
                $neft->utr_number
            );
            fputcsv($file, $row);
        }
        fclose($file);
        exit;
    }
    function exportInvoiceData()
    {
        $this->load->library('admin/invoice_lib');
        $filter = $this->input->get('filter');
        $apply_filters = array();


        if (!empty($filter['seller_id'])) {
            $apply_filters['seller_id'] = $filter['seller_id'];
        }

        if (!empty($filter['month'])) {
            $apply_filters['month'] = $filter['month'];
        }

        if (!empty($filter['invoice_type'])) {
            $apply_filters['invoice_type'] = $filter['invoice_type'];
        }
        if (!empty($filter['service_type'])) {
            $apply_filters['service_type'] = $filter['service_type'];
        }
        $query = $this->invoice_lib->exportInvoiceDataReport($apply_filters);

        $this->load->library('export_db');

        $export = new Export_db('slave');
        $export->query($query);
        $filename = 'export_invoice_data_' . time() . '.csv';
        header("Content-Description: File Transfer");
        header("Content-Disposition: attachment; filename=$filename");
        header("Content-Type: application/csv; ");
        $file = fopen('php://output', 'w');
        $header = array("Seller ID", "Company Name", "GST No", "Seller Name", "Invoice Month", "Invoice Number", "Bill Type", "Carrier", "AWB Number", "Shipment Status", "Seller Company Name", "Shipment ID", "Order ID", "Payment Type", "Pincode", "City", "Charged Weight", "Freight Charges", "COD Charges", "IGST", "SGST", "CGST", "Grand Total");
        fputcsv($file, $header);
        while ($record = $export->next()) {

            $row = array(
                $record->sellerid,
                ucwords($record->company_name),
                strtoupper($record->gstno),
                ucwords($record->firstname . ' ' . $record->lastname),
                $record->month,
                ($record->invoice_no != '') ? $record->invoice_no : 'NPPL/' . $record->invoice_id,
                $record->bill_type,
                ucwords($record->courier),
                $record->awb_number,
                ucwords($record->shipment_status),
                $record->seller_company_name,
                $record->shipment_id,
                $record->order_id,
                $record->payment_type,
                $record->pincode,
                $record->city,
                round($record->charged_weight, 2),
                round($record->freight_charges, 2),
                round($record->cod_charges, 2),
                round($record->igst, 2),
                round($record->sgst, 2),
                round($record->cgst, 2),
                round($record->total, 2),
            );
            fputcsv($file, $row);
        }
        fclose($file);
        exit;
    }

    function exportCreditNoteData()
    {
        $this->load->library('admin/invoice_lib');
        $filter = $this->input->get('filter');
        $apply_filters = array();


        if (!empty($filter['seller_id'])) {
            $apply_filters['seller_id'] = $filter['seller_id'];
        }

        if (!empty($filter['month'])) {
            $apply_filters['month'] = $filter['month'];
        }
        if (!empty($filter['invoice_type'])) {
            $apply_filters['invoice_type'] = $filter['invoice_type'];
        }
        if (!empty($filter['service_type'])) {
            $apply_filters['service_type'] = $filter['service_type'];
        }
        $query = $this->invoice_lib->exportCreditNoteDataReport($apply_filters);



        $this->load->library('export_db');

        $export = new Export_db('slave');
        $export->query($query);
        $filename = 'export_credit_data_' . time() . '.csv';
        header("Content-Description: File Transfer");
        header("Content-Disposition: attachment; filename=$filename");
        header("Content-Type: application/csv; ");
        $file = fopen('php://output', 'w');
        $header = array("Seller ID", "Company Name", "GST No", "Seller Name", "Credit Note Month", "Credit Note Number", "Bill Type", "Carrier", "AWB Number", "Shipment Status", "Seller Company Name", "Shipment ID", "Order ID", "Payment Type", "Pincode", "City", "Charged Weight", "Freight Charges", "COD Charges", "IGST", "SGST", "CGST", "Grand Total");
        fputcsv($file, $header);
        while ($record = $export->next()) {
            $row = array(
                $record->sellerid,
                ucwords($record->company_name),
                strtoupper($record->gstno),
                ucwords($record->firstname . ' ' . $record->lastname),
                $record->month,
                ($record->invoice_no != '') ? $record->invoice_no : 'NPPL/CN/' . sprintf('%03d', $record->invoice_id),
                $record->bill_type,
                ucwords($record->courier),
                $record->awb_number,
                ucwords($record->shipment_status),
                $record->seller_company_name,
                $record->shipment_id,
                $record->order_id,
                $record->payment_type,
                $record->pincode,
                $record->city,
                round($record->charged_weight, 2),
                round($record->freight_charges, 2),
                round($record->cod_charges, 2),
                round($record->igst, 2),
                round($record->sgst, 2),
                round($record->cgst, 2),
                round($record->total, 2),
            );
            fputcsv($file, $row);
        }
        fclose($file);
        exit;
    }


    public function bulk_action_import()
    {
        $this->load->library('wallet_lib');
        $wallet = new Wallet_lib();

        if ((isset($_FILES['importFile']['tmp_name'])) && (empty($_FILES['importFile']['tmp_name']))) {
            $this->session->set_flashdata('error', 'please Upload file');
            redirect('admin/billing/v/wallet_adjustments', true);
        }
        $ext = pathinfo($_FILES['importFile']['name'], PATHINFO_EXTENSION);

        if ($ext != "csv") {
            $this->session->set_flashdata('error', 'please Upload only csv file');
            redirect('admin/billing/v/wallet_adjustments', true);
        }
        if (is_uploaded_file($_FILES['importFile']['tmp_name'])) {
            $this->load->library('csvreader');
            $csvData = $this->csvreader->parse_csv($_FILES['importFile']['tmp_name']);
            if (empty($csvData)) {
                $this->session->set_flashdata('error', 'Blank CSV File');
                redirect('admin/billing/v/wallet_adjustments', true);
            }

            $import_message_array = array();
            $this->load->library('form_validation');
            $error = "";
            $user_error = "";
            $this->data['error'] = '';


            foreach ($csvData as $row_key => $row) {
                $str1 = array_change_key_case($row);

                if (count($str1) > 7) {

                    $this->session->set_flashdata('error', 'You can add only selected header field');
                    redirect('admin/billing/v/wallet_adjustments', true);
                }



                if (!array_key_exists("seller id", $str1)) {
                    $this->session->set_flashdata('error', 'Seller Id header is missing.');
                    redirect('admin/billing/v/wallet_adjustments', true);
                }

                if (!array_key_exists("adjustment for", $str1)) {
                    $this->session->set_flashdata('error', 'Adjustment For header is missing.');
                    redirect('admin/billing/v/wallet_adjustments', true);
                }

                if (!array_key_exists("txn ref", $str1)) {
                    $this->session->set_flashdata('error', 'Txn Ref For header is missing.');
                    redirect('admin/billing/v/wallet_adjustments', true);
                }

                if (!array_key_exists("adjustment type", $str1)) {
                    $this->session->set_flashdata('error', 'Adjustment Type header is missing.');
                    redirect('admin/billing/v/wallet_adjustments', true);
                }

                if (!array_key_exists("ref no", $str1)) {
                    $this->session->set_flashdata('error', 'Ref No header is missing.');
                    redirect('admin/billing/v/wallet_adjustments', true);
                }
                if (!array_key_exists("amount", $str1)) {
                    $this->session->set_flashdata('error', 'Amount header is missing.');
                    redirect('admin/billing/v/wallet_adjustments', true);
                }

                if (!array_key_exists("remarks", $str1)) {
                    $this->session->set_flashdata('error', 'Remarks  header is missing.');
                    redirect('admin/billing/v/wallet_adjustments', true);
                }

                if (array_key_exists("status", $str1)) {
                    $this->session->set_flashdata('error', 'Invalid status header.');
                    redirect('admin/billing/v/wallet_adjustments', true);
                }
            }
            // pr($str1);

            $filename = 'bulk_import_wallet_adjustment' . time() . '.csv';
            header("Content-Description: File Transfer");
            header("Content-Disposition: attachment; filename=$filename");
            header("Content-Type: application/csv; ");
            $file = fopen('php://output', 'w');
            $header = array("Seller Id", "Adjustment For", "Txn Ref", "Adjustment Type", "Ref No", "Amount", "Remarks", "Status");
            fputcsv($file, $header);


            $this->load->library('admin/billing_lib');
            $this->load->library('admin/user_lib');
            foreach ($csvData as $row_key1 => $row) {

                $str =  array_change_key_case($row);

                $user = $this->user_lib->getByID($str['seller id']);
                $str1 = array(
                    'sellerid' => $str['seller id'],
                    'adjustmentfor' => strtolower($str['adjustment for']), //product_sku
                    'txnref' => strtolower($str['txn ref']),
                    'adjustmenttype' => strtolower($str['adjustment type']),
                    'refno' => $str['ref no'],
                    'amount' => $str['amount'],
                    'remarks' => $str['remarks'],
                );

                if (empty($user) || $user->parent_id != '0') {

                    $user_error = "Wallet Adjustment not available for this user";
                    if (empty($str['seller id'])) {
                        $user_error = "Seller id is missing";
                    }
                    $row = array(
                        $str['seller id'],
                        $str['adjustment for'],
                        $str['txn ref'],
                        $str['adjustment type'],
                        $str['ref no'],
                        $str['amount'],
                        $str['remarks'],
                        "Failed -  " . $user_error,
                    );

                    fputcsv($file, $row);  //exit;   
                } else {

                    $this->data['user'] = $user;
                    if (!$this->validate_upload_data($str1)) {
                        $row = array(
                            $str['seller id'],
                            $str['adjustment for'],
                            $str['txn ref'],
                            $str['adjustment type'],
                            $str['ref no'],
                            $str['amount'],
                            $str['remarks'],
                            "Failed - " . strip_tags($this->data['error']),

                        );
                        fputcsv($file, $row);
                    } else {
                        $this->session->set_flashdata('success', 'Wallet Adjustment Done');
                        $wallet->setUserID($str['seller id']);
                        $wallet->setAmount($str['amount']);
                        $wallet->setTransactionType(strtolower($str['adjustment type']));
                        $wallet->setNotes($str['remarks']);
                        $wallet->setTxnFor(strtolower($str['adjustment for']));

                        if (strtolower($str['adjustment for']) == 'shipment')
                            $wallet->setTxnRef($str['txn ref']);

                        $wallet->setRefID($str['ref no']);
                        $ref_id =  $wallet->creditDebitWallet();

                        $data = array(
                            'wallet_history_id' => $ref_id,
                            'user_id' => $this->user->user_id
                        );

                        $this->load->library('admin/wallet_adjustment_lib');

                        $this->load->wallet_adjustment_lib->create($data);

                        $log = new Log();
                        $log->update($this->user->user_id, $str['seller id'], 'Wallet balance ' . $str['adjustment type'] . 'ed with Rs.' . $str['amount']);

                        $row = array(
                            $str['seller id'],
                            $str['adjustment for'],
                            $str['txn ref'],
                            $str['adjustment type'],
                            $str['ref no'],
                            $str['amount'],
                            $str['remarks'],
                            "Success",
                        );
                        fputcsv($file, $row);
                    }
                }
            }

            fclose($file);
        }
    }

    private function validate_upload_data($data)
    {
        $this->form_validation->set_data($data);

        $config = array(
            array(
                'field' => 'sellerid',
                'label' => 'Seller Id',
                'rules' => 'required'
            ),
            array(
                'field' => 'adjustmentfor',
                'label' => 'Adjustment For',
                'rules' => 'trim|required|in_list[cod,recharge,neft,others,shipment,lost,damaged,promotion,wallet_to_wallet_transfer,tds_refund,customer_refund]'
            ),
            array(
                'field' => 'adjustmenttype',
                'label' => 'Adjustment Type',
                'rules' => 'trim|required|in_list[credit,debit]'
            ),
            array(
                'field' => 'refno',
                'label' => 'Reference No',
                'rules' => 'trim|numeric'
            ),
            array(
                'field' => 'amount',
                'label' => 'Amount',
                'rules' => 'trim|required|greater_than[0]'
            ),
            array(
                'field' => 'remarks',
                'label' => 'Remarks',
                'rules' => 'trim|required'
            ),
        );

        if (strtolower($data['adjustmentfor']) == 'shipment') {
            $array =  array(
                'field' => 'txnref',
                'label' => 'Txn Ref',
                'rules' => 'trim|required|in_list[freight,cod,rto_freight,extra_weight,rto_extra_weight]',
            );

            array_push($config, $array);
        }

        $this->form_validation->set_rules($config);

        if ($this->form_validation->run()) {
            $this->form_validation->reset_validation();
            return true;
        } else {
            $this->data['error'] = validation_errors();
            $this->form_validation->reset_validation();
            return false;
        }
    }
}
