<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Remittance extends Admin_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->load->library('admin/remittance_lib');
        //$this->userHasAccess('remittance_new');
        //$this->userHasAccess('remittance');
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

    private function validate_upload_data($data)
    {
        $this->form_validation->set_data($data);

        $this->form_validation->set_message('required', '%s is required');
        $this->form_validation->set_message('alpha_numeric', 'Only Characters & Numbers are allowed in %s');
        $this->form_validation->set_message('alpha_numeric_spaces', 'Only Characters, Numbers & Spaces are  allowed in %s');

        $config = array(
            array(
                'field' => 'AWB Number',
                'label' => 'AWB Number',
                'rules' => 'trim|required|alpha_numeric|min_length[4]|max_length[20]',
            ),
            array(
                'field' => 'Amount',
                'label' => 'Amount',
                'rules' => 'trim|required|numeric',
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

    function receipt($id = false, $report = 'awb')
    {
        if (!$id || !is_numeric($id)) {
            $this->session->set_flashdata('error', 'Invalid Data');
            redirect('admin/remittance/reports/receipt_history', true);
        }

        //receipt details

        $receipt = $this->remittance_lib->getReceiptByID($id);

        if (empty($receipt)) {
            $this->session->set_flashdata('error', 'Invalid Data');
            redirect('admin/remittance/reports/receipt_history', true);
        }

        $this->data['receipt'] = $receipt;

        $inner_content = '';
        switch ($report) {
            case 'awb':
                $inner_content = $this->receiptAwbReport($id);
                break;
            case 'seller':
                $inner_content = $this->receiptSellerReport($id);
                break;
            default:
                $inner_content = $this->receiptAwbReport($id);
        }

        $this->data['inner_content'] = $inner_content;

        $this->data['report_type'] = $report;
        $this->layout('remittance/receipt');
    }

    private function receiptAwbReport($receipt_id = false)
    {

        //get records by receipt id

        $this->load->library('admin/shipping_lib');
        $records = $this->shipping_lib->getByReceiptID($receipt_id);

        $this->data['records'] = $records;
        return $this->load->view('admin/remittance/receipt_awb_report', $this->data, true);
    }

    private function receiptSellerReport($receipt_id = false)
    {

        //get records by receipt id

        $this->load->library('admin/shipping_lib');
        $records = $this->shipping_lib->sellerRecordsbyreceiptID($receipt_id);
        $this->data['records'] = $records;
        return $this->load->view('admin/remittance/receipt_seller_report', $this->data, true);
    }

    function reports($report = 'receipt_upload', $page = 1)
    {

        $inner_content = '';
        switch ($report) {
            case 'seller':
                $inner_content = $this->reportSellerWise();
                break;

            case 'remittance_analysis':
                $inner_content = $this->reportRemittanceAnalysis();
                break;

            case 'courier_dues':
                $inner_content = $this->reportCourierWiseDues();
                break;
            case 'receipt_history':
                $inner_content = $this->reportReceiptHistory($page);
                break;
            case 'receipt_upload':
                $inner_content = $this->reportReceiptUpload();
                break;
            case 'remittance':
                $inner_content = $this->reportRemittance($page);
                break;
            default:
        }
        $this->load->library('admin/shipping_lib');
        $records = $this->shipping_lib->getCourierExpectedAmount();
        $this->data['remittance_data'] = $records;
        $this->data['inner_content'] = $inner_content;

        $this->data['report_type'] = $report;
        $this->layout('remittance/reports');
    }

    function newreports($report = 'ops_verify', $page = 1)
    {

        $inner_content = '';
        switch ($report) {
            case 'ops_verify':
                $inner_content = $this->reportOpsVerify();
                break;
            case 'export_aws_record':
                $inner_content = $this->reportAwsOpsVerify();
                break;
            case 'import_seller_remittance':
            $inner_content = $this->import_sellerremittance();
                break;
            case 'import_remittance':
                $inner_content = $this->import_remittance();
                break;
            case 'export_unverified_aws_record':
                $inner_content = $this->export_unverified_aws_records();
                break;
            case 'export_seller_payable':
                $inner_content = $this->report_sellerpayablewise();
                break;
            case 'import_seller_remittance':
                $inner_content = $this->import_sellerremittance();
                break;
            default:
        }
        $this->data['inner_content'] = $inner_content;
        $this->data['report_type'] = $report;
        //pr($this->data,1);
        $this->layout('remittance/new_reports');
    }    
    function reportMissingBank()
    {
        $this->load->library('admin/shipping_lib');
        $records = $this->remittance_lib->remittanceMissingBankDetails();

        $this->data['records'] = $records;

        return $this->load->view('admin/remittance/reports/missing_bank', $this->data, true);
    }

    function reportRemittanceAnalysis()
    {
        ini_set('max_execution_time', 12000);
        $this->load->library('admin/shipping_lib');
        $records = $this->shipping_lib->sellerRemittanceAnalysis();

        $this->data['records'] = $records;

        return $this->load->view('admin/remittance/reports/remittance_analysis', $this->data, true);
    }

    private function reportReceiptUpload()
    {

        if (!$this->remittance_lib->uploadReceipt()) {
            $this->data['error'] = $this->remittance_lib->get_error();
        } else {
            $this->session->set_flashdata('success', 'Receipt uploaded successfully');
            redirect('admin/remittance/reports/receipt_history', true);
        }

        $this->load->library('courier_lib');
        $couriers = $this->courier_lib->listAllCouriers();
        $this->data['couriers'] = $couriers;
        return $this->load->view('admin/remittance/reports/receipt_upload', $this->data, true);
    }

    private function reportReceiptHistory($page = 1)
    {
        $filter = $this->input->get('filter');
        $apply_filters = array();

        if (!empty($filter['utr_no'])) {
            $apply_filters['utr_no'] = array_map('trim', explode(',', $filter['utr_no']));
        }
        if (!empty($filter['courier_id'])) {
            $apply_filters['courier_id'] = $filter['courier_id'];
        }

        $limit = 50;

        $total_row = $this->remittance_lib->countCodUploadHistory($apply_filters);
        $config = array(
            'base_url' => base_url('admin/remittance/reports/receipt_history'),
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

        $history = $this->remittance_lib->codUploadHistory($limit, $offset, $apply_filters);
        $this->data['history'] = $history;

        $this->load->library('courier_lib');
        $couriers = $this->courier_lib->listAllCouriers();
        $this->data['couriers'] = $couriers;

        return $this->load->view('admin/remittance/reports/receipt_history', $this->data, true);
    }

    function exportReceiptHistory()
    {
        $filter = $this->input->get('filter');
        $apply_filters = array();

        if (!empty($filter['utr_no'])) {
            $apply_filters['utr_no'] = array_map('trim', explode(',', $filter['utr_no']));
        }

        if (!empty($filter['courier_id'])) {
            $apply_filters['courier_id'] = $filter['courier_id'];
        }




        $records = $this->remittance_lib->codUploadHistory(15000, 0, $apply_filters);

        $filename = 'ReceiptHistory.csv';
        header("Content-Description: File Transfer");
        header("Content-Disposition: attachment; filename=$filename");
        header("Content-Type: application/csv; ");
        $file = fopen('php://output', 'w');
        $header = array("Date", "Receipt ID", "Carrier", "Amount", "Payment Date", "UTR No");
        fputcsv($file, $header);
        foreach ($records as $rec) {
            $row = array(
                date('Y-m-d', $rec->created),
                $rec->id,
                ucwords($rec->courier_name),
                $rec->amount,
                date('Y-m-d', $rec->payment_date),
                $rec->utr_number
            );
            fputcsv($file, $row);
        }
        fclose($file);
        exit;
    }

    function exportCSVRemittance()
    {

        $filter = $this->input->get('filter');
        $apply_filters = array();

        $filter = $this->input->get('filter');
        $apply_filters = array();

        $apply_filters['start_date'] = strtotime("-7 days midnight");
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

        if (!empty($filter['remittance_id'])) {
            $apply_filters['remittance_id'] = array_map('trim', explode(',', $filter['remittance_id']));
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

        if (!empty($filter['created_by'])) {
            $apply_filters['created_by'] = $filter['created_by'];
        }

        if (!empty($filter['created_by_user'])) {
            $apply_filters['created_by_user'] = $filter['created_by_user'];
        }

        if (!empty($filter['date_type'])) {
            $apply_filters['date_type_field'] = trim($filter['date_type']);
        } else {
            $apply_filters['date_type_field'] = 'created';
        }


        $remittances = $this->remittance_lib->remittanceHistory(15000, 0, $apply_filters);



        $filename = 'remittance_' . time() . '.csv';
        header("Content-Description: File Transfer");
        header("Content-Disposition: attachment; filename=$filename");
        header("Content-Type: application/csv; ");
        $file = fopen('php://output', 'w');
        $header = array(
            "Remittance ID", "Remittance Date", "Created By", "Seller ID", "Company", "Invoice Mode", "Wallet Balance", "COD Amount", "Freight Deductions", "Remittance Paid", "Convenience Fee", "Paid", "Payment Date", "Early Cod Charges", "UTR Number", "Client_Code", "Product_Code", "Payment_Type", "Payment_Ref_No.", "Payment_Date", "Instrument Date", "Bank Name", "Dr_Ac_No", "Amount", "Bank_Code_Indicator", "Beneficiary_Code", "Beneficiary_Name", "Beneficiary_Bank", "Beneficiary_Branch / IFSC Code", "Beneficiary_Acc_No", "Location", "Print_Location", "Instrument_Number", "Ben_Add1", "Ben_Add2", "Ben_Add3", "Ben_Add4", "Beneficiary_Email", "Beneficiary_Mobile", "Debit_Narration", "Credit_Narration", "Payment Details 1", "Payment Details 2", "Payment Details 3", "Payment Details 4", "Enrichment_1", "Enrichment_", "Enrichment_3", "Enrichment_4", "Enrichment_5", "Enrichment_6", "Enrichment_7", "Enrichment_8", "Enrichment_9", "Enrichment_10", "Enrichment_11", "Enrichment_12", "Enrichment_13", "Enrichment_14", "Enrichment_15", "Enrichment_16", "Enrichment_17", "Enrichment_18", "Enrichment_19", "Enrichment_20"
        );
        fputcsv($file, $header);
        foreach ($remittances as $remittance) {
            $row = array(
                $remittance->id,
                date('Y-m-d', $remittance->created),
                ($remittance->seller_created == '1') ? 'Seller' : 'delta (' . $remittance->createdby_fname . ' ' . $remittance->createdby_lname . ')',
                $remittance->user_id,
                ucwords(empty($remittance->company_name) ? $remittance->user_company : $remittance->user_fname . ' ' . $remittance->user_lname),
                ($remittance->is_postpaid) ? 'Postpaid' : 'Prepaid',
                round($remittance->wallet_balance, 2),
                round($remittance->amount, 2),
                ($remittance->freight_deductions > 0) ? round($remittance->freight_deductions, 2) : 0,
                ($remittance->remittance_amount > 0) ? round($remittance->remittance_amount, 2) : '0',
                ($remittance->convenience_fee > 0) ? round($remittance->convenience_fee, 2) : '0',
                ($remittance->paid == '1') ? 'Yes' : 'No',
                !empty($remittance->payment_date) ? date('Y-m-d', $remittance->payment_date) : '',
                !empty($remittance->early_cod_charges) ? $remittance->early_cod_charges : '0',
                $remittance->utr_number,
                'delta',
                'VENPAY',
                '',
                '',
                '',
                '',
                'KOTAK BANK-8413190981 (COD)',
                '8413190981',
                round($remittance->amount, 2),
                'M',
                '',
                ucwords($remittance->account_name),
                '',
                (strtoupper(substr($remittance->ifsc_code, 0, 4)) == 'KKBK') ? 'KKBK0000958' : strtoupper($remittance->ifsc_code),
                "'" . $remittance->account_number,
                '',
                '',
                '',
                '',
                '',
                '',
                '',
                '',
                '',
                "{$remittance->id} - {$remittance->account_name}",
                '',
                '',
                '',
                '',
                '',
                '',
                '',
                '',
                '',
                '',
                '',
                '',
                '',
                '',
                '',
                '',
                '',
                '',
                '',
                '',
                '',
                '',
                '',
                '',
                '',
            );
            fputcsv($file, $row);
        }
        fclose($file);
        exit;
    }
    function exportAxisCSVRemittance()
    {

        $filter = $this->input->get('filter');
        $apply_filters = array();

        $filter = $this->input->get('filter');
        $apply_filters = array();

        $apply_filters['start_date'] = strtotime("-7 days midnight");
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

        if (!empty($filter['remittance_id'])) {
            $apply_filters['remittance_id'] = array_map('trim', explode(',', $filter['remittance_id']));
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

        if (!empty($filter['created_by'])) {
            $apply_filters['created_by'] = $filter['created_by'];
        }

        if (!empty($filter['created_by_user'])) {
            $apply_filters['created_by_user'] = $filter['created_by_user'];
        }

        if (!empty($filter['date_type'])) {
            $apply_filters['date_type_field'] = trim($filter['date_type']);
        } else {
            $apply_filters['date_type_field'] = 'created';
        }

        $remittances = $this->remittance_lib->remittanceHistory(15000, 0, $apply_filters);
        $filename = 'remittance_axis_' . time() . '.csv';
        header("Content-Description: File Transfer");
        header("Content-Disposition: attachment; filename=$filename");
        header("Content-Type: application/csv; ");
        $file = fopen('php://output', 'w');
        $header = array(
            "Remittance ID", "Remittance Date", "Created By", "Seller ID", "Company", "Invoice Mode", "Wallet Balance", "COD Amount", "Freight Deductions", "Remittance Paid", "Convenience Fee", "Paid", "Payment Date", "Early Cod Charges", "UTR Number", "Payment Method identifier R-RTGS N-NEFT I-AXIS2AXIS ", "Amount", "Value Date DD-MM-YYYY", "Beneficiary Name", "Bene Account Number  ( Use ' for account starting with 0 or when excel converts in Exponential )", "Email ID of beneficiary", "Email Body", "Debit Account Number", "CRN (Unique Number)", "Receiver IFSC", "Receiver A/c type always 11", "Remarks", "Phone No"
        );
        fputcsv($file, $header);
        foreach ($remittances as $remittance) {
            $unique_no = $remittance->id;
            if (strtoupper(substr($remittance->ifsc_code, 0, 4)) == 'UTIB') {
                $payments_method = 'I';
            } else if ((strtoupper(substr($remittance->ifsc_code, 1, 4)) != 'UTIB' or   (!empty($remittance->ifsc_code))) and $remittance->amount >= 200000) {
                $payments_method = 'R';
            } else if ((strtoupper(substr($remittance->ifsc_code, 1, 4)) != 'UTIB' or   (!empty($remittance->ifsc_code))) and $remittance->amount <= 200000) {
                $payments_method = 'N';
            }
            //$crn_unique_no="nim". str_pad($unique_no, 8, '0', STR_PAD_LEFT);
            $crn_unique_no = "nim" . date("YmdHis") . $unique_no;
            $row = array(
                $remittance->id,
                date('Y-m-d', $remittance->created),
                ($remittance->seller_created == '1') ? 'Seller' : 'delta (' . $remittance->createdby_fname . ' ' . $remittance->createdby_lname . ')',
                $remittance->user_id,
                ucwords(empty($remittance->company_name) ? $remittance->user_company : $remittance->user_fname . ' ' . $remittance->user_lname),
                ($remittance->is_postpaid) ? 'Postpaid' : 'Prepaid',
                round($remittance->wallet_balance, 2),
                round($remittance->amount, 2),
                ($remittance->freight_deductions > 0) ? round($remittance->freight_deductions, 2) : 0,
                ($remittance->remittance_amount > 0) ? round($remittance->remittance_amount, 2) : '0',
                ($remittance->convenience_fee > 0) ? round($remittance->convenience_fee, 2) : '0',
                ($remittance->paid == '1') ? 'Yes' : 'No',
                !empty($remittance->payment_date) ? date('Y-m-d', $remittance->payment_date) : '',
                !empty($remittance->early_cod_charges) ? $remittance->early_cod_charges : '0',
                $remittance->utr_number,
                $payments_method,
                round($remittance->amount, 2),
                " " . date('d-m-Y'),
                $remittance->account_name,
                !empty($remittance->account_number) ?  "'" . $remittance->account_number : '',
                "",
                "",
                "'922020004239025",
                $crn_unique_no,
                strtoupper($remittance->ifsc_code),
                "11",
                $remittance->id . " " . ucwords($remittance->user_fname) . " " . ucwords($remittance->user_lname),
                "7303883456",
            );
            fputcsv($file, $row);
        }
        fclose($file);
        exit;
    }

    private function validate_utr_upload_data($data)
    {
        $this->form_validation->set_data($data);

        $this->form_validation->set_message('required', '%s is required');
        $this->form_validation->set_message('alpha_numeric_spaces', 'Only Characters, Numbers & Spaces are  allowed in %s');

        $config = array(
            array(
                'field' => 'Remittance ID',
                'label' => 'Remittance ID',
                'rules' => 'trim|required|numeric',
            ),
            array(
                'field' => 'UTR Number',
                'label' => 'UTR Number',
                'rules' => 'trim|required',
            ),
            array(
                'field' => 'Freight Deductions',
                'label' => 'Freight Deductions',
                'rules' => 'trim|required|numeric|greater_than_equal_to[0]',
            ),
            array(
                'field' => 'Remittance Paid',
                'label' => 'Remittance Paid',
                'rules' => 'trim|required|numeric|greater_than_equal_to[0]',
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

    private function validate_remittance_upload_data($data)
    {
        $this->form_validation->set_data($data);

        $this->form_validation->set_message('required', '%s is required');
        $this->form_validation->set_message('alpha_numeric_spaces', 'Only Characters, Numbers & Spaces are  allowed in %s');

        $config = array(
            array(
                'field' => 'awb number',
                'label' => 'Awb Number',
                'rules' => 'trim|required',
            ),
            array(
                'field' => 'cod amount',
                'label' => 'COD Amount',
                'rules' => 'trim|required|numeric',
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
    
    private function validate_ops_upload_data($data)
    {
        $this->form_validation->set_data($data);

        $this->form_validation->set_message('required', '%s is required');
        $this->form_validation->set_message('alpha_numeric_spaces', 'Only Characters, Numbers & Spaces are  allowed in %s');

        $config = array(
            array(
                'field' => 'awb number',
                'label' => 'Awb Number',
                'rules' => 'trim|required',
            ),
            array(
                'field' => 'ops status',
                'label' => 'OPS Status',
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

    function importUTR()
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
        if ($this->form_validation->run()) {
            if (is_uploaded_file($_FILES['importFile']['tmp_name'])) {
                // Load CSV reader library
                $this->load->library('csvreader');
                // Parse data from CSV file
                $csvData = $this->csvreader->parse_csv($_FILES['importFile']['tmp_name']);
                if (empty($csvData)) {
                    $this->session->set_flashdata('error', 'Blank CSV File');
                    redirect('admin/remittance/reports/remittance', true);
                }
                foreach ($csvData as $row_key => $row) {
                    if (!$this->validate_utr_upload_data($row)) {
                        $this->session->set_flashdata('error', 'Row no. ' . ($row_key + 1) . $this->data['error']);
                        redirect('admin/remittance/reports/remittance', true);
                    }
                }

                //fetch and check if remittance ids are paid or not
                $apply_filters = array(
                    'remittance_id' => array_column($csvData, 'Remittance ID'),
                );

                $existing = $this->remittance_lib->remittanceHistory(15000, 0, $apply_filters);

                if (empty($existing)) {
                    $this->session->set_flashdata('error', 'No records found');
                    redirect('admin/remittance/reports/remittance', true);
                }


                $db_records = array();
                foreach ($existing as $exist) {
                    $db_records[$exist->id] = $exist;
                    if ($exist->paid == '1') {
                        $this->data['error'] = 'Already paid for remittance ID #' . $exist->id;
                        $this->session->set_flashdata('error', $this->data['error']);
                        redirect('admin/remittance/reports/remittance', true);
                    }
                }

                //check if amount is same
                foreach ($csvData as $csv_key => $csv_row) {
                    if (empty($db_records[$csv_row['Remittance ID']])) {
                        $this->session->set_flashdata('error', 'Remittance ID #' . $csv_row['Remitance ID'] . ' Does Not Exists.');
                        redirect('admin/remittance/reports/remittance', true);
                    }
                    $total_amount = round($csv_row['Freight Deductions'], 2) + round($csv_row['Remittance Paid'], 2) + round($csv_row['Convenience Fee'], 2);
                    $ceilVal = ceil($total_amount);
                    $floorVal = floor($total_amount);
                    $total_rimt_amount = (int)$db_records[$csv_row['Remittance ID']]->amount;
                    //echo $csv_row['Remittance ID']."----".$total_amount."-----".$total_rimt_amount."<br>";
                    if ($ceilVal != $total_rimt_amount && $floorVal != $total_rimt_amount) {
                        $this->session->set_flashdata('error', 'Remittance ID #' . $csv_row['Remittance ID'] . ' amount mismatch.');
                        redirect('admin/remittance/reports/remittance', true);
                    }
                }


                //update UTR for each record
                foreach ($csvData as $csv_key => $csv_row) {
                    $this->remittance_lib->updateRemittanceUTR($csv_row['Remittance ID'], $csv_row['UTR Number'], $csv_row['Remittance Paid'], $csv_row['Freight Deductions'], $csv_row['Convenience Fee']);
                }

                $this->session->set_flashdata('success', 'Records has been updated');
                redirect('admin/remittance/reports/remittance', true);
            }
        } else {
            $this->data['error'] = validation_errors();
            $this->session->set_flashdata('error', $this->data['error']);
            redirect('admin/remittance/reports/remittance', true);
        }
    }

    private function reportRemittance($page = 1)
    {
        $limit = 50;
        $filter = $this->input->get('filter');
        $apply_filters = array();
        $apply_filters['start_date'] = '1731149773';//strtotime("-6 days midnight");
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

        if (!empty($filter['remittance_id'])) {
            $apply_filters['remittance_id'] = array_map('trim', explode(',', $filter['remittance_id']));
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

        if (!empty($filter['created_by'])) {
            $apply_filters['created_by'] = $filter['created_by'];
        }

        if (!empty($filter['created_by_user'])) {
            $apply_filters['created_by_user'] = $filter['created_by_user'];
        }

        if (!empty($filter['date_type'])) {
            $apply_filters['date_type_field'] = trim($filter['date_type']);
        } else {
            $apply_filters['date_type_field'] = 'created';
        }

        $total_row = $this->remittance_lib->countRemittanceHistory($apply_filters);

        $config = array(
            'base_url' => base_url('admin/remittance/reports/remittance'),
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


        $remittances = $this->remittance_lib->remittanceHistory($limit, $offset, $apply_filters);
        $seller_details = '';
        $this->load->library('admin/user_lib');
        $seller_details = $this->user_lib->getUserListFilter('');
        $this->data['users'] = $seller_details;

        $admin_users = $this->user_lib->getAdminUsers();
        foreach ($admin_users as $users) {
            $arrUsers[$users->id] = $users->fname . ' ' . $users->lname;
        }
        $this->data['admin_users'] = $arrUsers;

        $this->data['filter'] = $filter;

        $this->data['remittances'] = $remittances;
        return $this->load->view('admin/remittance/reports/remittance', $this->data, true);
    }

    private function reportSellerWise()
    {
       // ini_set('max_execution_time', 600);

        $filter = $this->input->post('filter');

        $apply_filters = array();
        if (!empty($filter['seller_id'][0]) && isset($filter['seller_id'][0])) {
            $apply_filters['seller_id'] = $filter['seller_id'];
        }

        if (!empty($filter['seller_ids'])) {
            $apply_filters['seller_ids'] = array_map('trim', explode(',', $filter['seller_ids']));
        }

        if (!empty($filter['ignore_seller_id'])) {
            $apply_filters['ignore_seller_id'] = array_map('trim', explode(',', $filter['ignore_seller_id']));
        }

        if (!empty($filter['remittance_cycles'])) {
            $apply_filters['remittance_cycles'] = array_map('trim', explode(',', $filter['remittance_cycles']));
        }


        $remittance_new = $this->input->post('createdremittance');

        if (!empty($remittance_new) && $remittance_new == '1') {
            $this->load->library('form_validation');
            $config = array(
                array(
                    'field' => 'user_ids[]',
                    'label' => 'Checkbox',
                    'rules' => 'trim|required',
                    'errors' => array(
                        'required' => 'Please select the records',
                    ),
                ),
            );

            $this->form_validation->set_rules($config);
            if ($this->form_validation->run()) {
                $user_ids = $this->input->post('user_ids');
                if (!empty($user_ids)) {
                    foreach ($user_ids as $user_id) {
                        $this->remittance_lib->createRemittanceByUserID($user_id, $this->user->user_id);
                    }

                    $this->session->set_flashdata('success', 'Remittance Created');
                    redirect('admin/remittance/reports/remittance', true);
                } else {
                    $this->data['error'] = 'Please select the records';
                }
            } else {
                $this->data['error'] = validation_errors();
            }
        }

        $this->load->library('admin/shipping_lib');

        $seller_details = '';
        $this->load->library('admin/user_lib');
        $seller_details = $this->user_lib->getUserListFilter('');
        $remmitance_cycle = $this->remittance_lib->getdisctinctremmitance();

        if (!empty($filter) && $filter['allseller'] == '1') {

            $records = $this->shipping_lib->sellerPendingTotals($apply_filters);
            $this->data['records'] = $records;
        }
        $this->data['users'] = $seller_details;
        $this->data['remmitance_cycle'] = $remmitance_cycle;
        $this->data['filter'] = $filter;
        return $this->load->view('admin/remittance/reports/seller_wise', $this->data, true);
    }

    private function report_sellerpayablewise()  // new function created for export seller payable on date 01-12-2022
    {
       // ini_set('max_execution_time', 600);
        $filter = $this->input->post('filter');
        $apply_filters = array();
        if (!empty($filter['seller_id'])) {
            $apply_filters['seller_id'] = $filter['seller_id'];
        }
        if (!empty(trim($filter['seller_ids']))) {
            $apply_filters['seller_ids'] = array_map('trim', explode(',', $filter['seller_ids']));
        }
        if (!empty(trim($filter['ignore_seller_id']))) {
            $apply_filters['ignore_seller_id'] = array_map('trim', explode(',', $filter['ignore_seller_id']));
        }
        if (!empty($filter['remittance_cycles'])) {
            $apply_filters['remittance_cycles'] = array_map('trim', explode(',', $filter['remittance_cycles']));
        }
        $apply_filters['operation_verify'] = '1';
        $this->load->library('admin/shipping_lib');
        $remmitance_cycle = $this->remittance_lib->getdisctinctremmitance();
        if (!empty($filter) && $filter['allseller'] == '1') {
            $query = $this->shipping_lib->sellerPendingRemittanceAwbwise(150000000, 0, $apply_filters);
            //pr($query,1);
            $this->load->library('export_db');
            $export = new Export_db('slave');
            $export->query($query);
            $filename = 'Bulk_remittance_' . time() . '.csv';
            header("Content-Description: File Transfer");
            header("Content-Disposition: attachment; filename=$filename");
            header("Content-Type: application/csv; ");
            $file = fopen('php://output', 'w');
            $header = array(
                "User ID",
                "User Name",
                "Company name",
                "AWB Number",
                "Courier",
                "Payment Type",
                "Shipment Status",
                "Remittance Cycle",
                "Amount",
                "Shipment Date",
                "Delivered Date"
            );
            fputcsv($file, $header);
            while ($order = $export->next()) {
                $Remittance_Received_status = ($order->receiptId == 0) ? "No" : "Yes";
                $delivered_date = ($order->delivered_time) ? date('d/m/Y', $order->delivered_time) : '';
                $shipment_date = ($order->pickup_time) ? date('d/m/Y', $order->pickup_time) : '';
                $row = array(
                    $order->user_id,
                    ucfirst($order->user_name),
                    $order->user_company,
                    $order->awb_number,
                    $order->courier_name,
                    $order->payment_type,
                    ucfirst($order->ship_status),
                    "T+" . $order->remittance_cycle,
                    $order->order_total_amount,
                    $shipment_date,
                    $delivered_date
                );
                //pr($row,1);
                fputcsv($file, $row);
            }
            fclose($file);
            exit;
        }
        $this->data['users'] = $seller_details;
        $this->data['remmitance_cycle'] = $remmitance_cycle;
        $this->data['filter'] = $filter;
        return $this->load->view('admin/remittance/reports/seller_payable_export', $this->data, true);
    }
    private function reportOpsVerify()
    {
        $this->load->library('courier_lib');
        $couriers = $this->courier_lib->listAllCouriers();
        $this->data['couriers'] = $couriers;
        $this->load->library('admin/shipping_lib');
        $records = $this->shipping_lib->getCourierWiseAwbCount();
        $unverifiedRecords = $this->shipping_lib->getCourierWiseUnverifiedAwbCount();
        $this->data['aws_records'] = $records;
        $this->data['aws_unverified_records'] = $unverifiedRecords;
        return $this->load->view('admin/remittance/reports/ops_verify', $this->data, true);
    }
    function reportAwsOpsVerify()
    {

        $filter = $this->input->get('filter');

        $apply_filters = array();
        if (!empty($filter['courier_id'])) {
            $apply_filters['courier_id'] = $filter['courier_id'];
        }
        $this->load->library('admin/shipping_lib');

        $records = $this->shipping_lib->getCourierWiseAwbRecords($apply_filters);
        $filename = str_replace(" ","_",strtolower($records[0]->courier_name)).'_ops_awb_list.csv';
        header("Content-Description: File Transfer");
        header("Content-Disposition: attachment; filename=$filename");
        header("Content-Type: application/csv; ");
        $file = fopen('php://output', 'w');
        $header = array("Seller Id", "Company Name", "Courier", "Shipment Id", "Awb Number", "Ship Status", "Amount", "Created Date", "Delivered Date");
        fputcsv($file, $header);
        foreach ($records as $rec) {
            if($rec->ops_verify=='1' || $rec->ops_verify=='2')
                continue;

            $row = array(
                $rec->seller_id,
                $rec->company_name,
                $rec->courier_name,
                $rec->shipping_id,
                $rec->awb_number,
                ucwords (strtolower($rec->ship_status)),
                round($rec->total_amount, 2),    
                ($rec->shipping_created) ? date('d-m-Y',$rec->shipping_created) : '',
                ($rec->delivered_time) ? date('d-m-Y',$rec->delivered_time) : ''
            );
            fputcsv($file, $row);
        }
        fclose($file);
        exit;
    }

    function export_unverified_aws_records()
    {
        $filter = $this->input->get('filter');

        $apply_filters = array();
        if (!empty($filter['courier_id'])) {
            $apply_filters['courier_id'] = $filter['courier_id'];
        }
        $apply_filters['ops_verify']='2';
        $this->load->library('admin/shipping_lib');

        $records = $this->shipping_lib->exportUnverifiedaAwsRecords($apply_filters);
        //$filename = str_replace(" ","_",strtolower($records[0]->courier_name)).'_ops_univerfied_awb.csv';
        $filename = 'ops_univerfied_awb.csv';
        header("Content-Description: File Transfer");
        header("Content-Disposition: attachment; filename=$filename");
        header("Content-Type: application/csv; ");
        $file = fopen('php://output', 'w');
        $header = array("Seller Id", "Company Name", "Courier", "Shipment Id", "Awb Number", "Ship Status", "Amount", "Created Date", "Delivered Date","Ops Status");
        fputcsv($file, $header);
        foreach ($records as $rec) {
            $row = array(
                $rec->seller_id,
                $rec->company_name,
                $rec->courier_name,
                $rec->shipping_id,
                $rec->awb_number,
                ucwords (strtolower($rec->ship_status)),
                round($rec->total_amount, 2),    
                ($rec->shipping_created) ? date('d-m-Y',$rec->shipping_created) : '',
                ($rec->delivered_time) ? date('d-m-Y',$rec->delivered_time) : '',
                'Unverified'
            );
            fputcsv($file, $row);
        }
        fclose($file);
        exit;
    }
    
    function import_sellerremittance() 
    {
        return $this->load->view('admin/remittance/reports/seller_remittance_import', $this->data, true);
    }
    function import_remittance() 
    {
        $this->load->library('form_validation');
        $config = array(
            array(
                'field' => 'importFile',
                'label' => 'Import File',
                'rules' => 'callback_file_check'
            ),
        );
        //pr($_FILES,1);
        $this->form_validation->set_rules($config);
        if ($this->form_validation->run()) {
            if (is_uploaded_file($_FILES['importFile']['tmp_name'])) {
                // Load CSV reader library
                $this->load->library('csvreader');
                // Parse data from CSV file
                $csvData = $this->csvreader->parse_csv($_FILES['importFile']['tmp_name']);
                if (empty($csvData)) {
                    $this->session->set_flashdata('error', 'Blank CSV File');
                    redirect('admin/remittance/newreports/import_seller_remittance', true);
                }
                $unpaidawb_nos=[];
                $paidawb_nos=[];
                foreach ($csvData as $row_key => $row) {
                    $row = array_change_key_case($row,CASE_LOWER);
                    if (!$this->validate_remittance_upload_data($row)) {
                        $this->session->set_flashdata('error', 'Row no. ' . ($row_key) . $this->data['error']);
                        redirect('admin/remittance/newreports/import_seller_remittance', true);
                    }
                    if($row['cod amount']<0)
                    {
                        $unpaidawb_nos[$row['awb number']][]=$row['cod amount'];  
                    }
                    else{
                        $paidawb_nos[$row['awb number']][]=$row['cod amount'];  
                    }
                }
                $returnCsvArr=$this->remittance_lib->createRemittanceByAwbNumber($paidawb_nos,$unpaidawb_nos, $this->user->user_id);
                //check if amount is same
                $filename = 'Bulk_upload_remittance_' . time() . '.csv';
                header("Content-Description: File Transfer");
                header("Content-Disposition: attachment; filename=$filename");
                header("Content-Type: application/csv; ");
                $file = fopen('php://output', 'w');
                $header = ['Awb Number','COD Amount','Remittance Id','Remark'];
                fputcsv($file, $header);
                if(!empty($returnCsvArr))
                {
                    foreach($returnCsvArr as $csvArr)
                    {
                        fputcsv($file,$csvArr);
                    }
                }
                fclose($file);
                exit;            
            }
        } else {
            $this->data['error'] = validation_errors();
            $this->session->set_flashdata('error', $this->data['error']);
            redirect('admin/remittance/newreports/import_seller_remittance', true);
        }
    }

    function import_ops_awb() 
    {
        $this->load->library('form_validation');
        $config = array(
            array(
                'field' => 'importFile',
                'label' => 'Import File',
                'rules' => 'callback_file_check'
            ),
        );
        //pr($_FILES,1);
        $this->form_validation->set_rules($config);
        if ($this->form_validation->run()) {
            if (is_uploaded_file($_FILES['importFile']['tmp_name'])) {
                // Load CSV reader library
                $this->load->library('csvreader');
                // Parse data from CSV file
                $csvData = $this->csvreader->parse_csv($_FILES['importFile']['tmp_name']);
                if (empty($csvData)) {
                    $this->session->set_flashdata('error', 'Blank CSV File');
                    redirect('admin/remittance/newreports/ops_verify', true);
                }
                //pr($csvData,1);
                foreach ($csvData as $row_key => $row) {
                    $row = array_change_key_case($row,CASE_LOWER);
                    if (!$this->validate_ops_upload_data($row)) {
                        $this->session->set_flashdata('error', 'Row no. ' . ($row_key) . $this->data['error']);
                        redirect('admin/remittance/newreports/ops_verify', true);
                    }
                }
                //pr($unpaidawb_nos,1);
                $filename = 'upload_ops_aws_' . time() . '.csv';
                header("Content-Description: File Transfer");
                header("Content-Disposition: attachment; filename=$filename");
                header("Content-Type: application/csv; ");
                $file = fopen('php://output', 'w');
                $header = ['Awb Number','OPS Status','Remark'];
                fputcsv($file, $header);
                foreach ($csvData as $csv_key => $csv_row) {
                    $csv_row = array_change_key_case($csv_row,CASE_LOWER);
                    $return_msg=$this->remittance_lib->verifyOpsByAwbNumber($csv_row['awb number'],$csv_row['ops status'], $this->user->user_id);
                    $returnCsvArr=[$csv_row['awb number'],$csv_row['ops status'],$return_msg];
                    fputcsv($file,$returnCsvArr);
                }
                fclose($file);
                exit();
            }
        } else {
            $this->data['error'] = validation_errors();
            $this->session->set_flashdata('error', $this->data['error']);
            redirect('admin/remittance/newreports/ops_verify', true);
        }
    }
    function exportReportSellerPayable()
    {

        $filter = $this->input->get('filter');

        $apply_filters = array();
        if (!empty($filter['seller_id'])) {
            $apply_filters['seller_id'] = $filter['seller_id'];
        }

        if (!empty($filter['seller_ids'])) {
            $apply_filters['seller_ids'] = array_map('trim', explode(',', $filter['seller_ids']));
        }

        if (!empty($filter['ignore_seller_id'])) {
            $apply_filters['ignore_seller_id'] = array_map('trim', explode(',', $filter['ignore_seller_id']));
        }

        if (!empty($filter['remittance_cycles'])) {
            $apply_filters['remittance_cycles'] = array_map('trim', explode(',', $filter['remittance_cycles']));
        }

        // echo "<pre>"; 
        // print_r($filter); 
        // echo "</pre>"; 
        // die ; 
        $this->load->library('admin/shipping_lib');

        $records = $this->shipping_lib->sellerPendingTotals($apply_filters);


        $filename = 'SellerPayable.csv';
        header("Content-Description: File Transfer");
        header("Content-Disposition: attachment; filename=$filename");
        header("Content-Type: application/csv; ");
        $file = fopen('php://output', 'w');
        $header = array("Seller Id", "Company Name", "Wallet Balance", "Remittance on Hold", "Remittance Cycle", "Remittance Amount", "received From Courier", "Expected", "Eearly Paid", "Expected Due");
        fputcsv($file, $header);
        foreach ($records as $rec) {
            $row = array(
                $rec->user_id,
                $rec->user_company,
                round($rec->wallet_balance, 2),
                round($rec->remittance_on_hold_amount, 2),
                'T-' . $rec->remittance_cycle,
                round($rec->remittance_cycle_total, 2),
                round($rec->receipt_uploaded, 2),
                round($rec->seller_expected, 2),
                round($rec->early_paid, 2),
                round($rec->seller_expected - $rec->early_paid, 2)
            );
            fputcsv($file, $row);
        }
        fclose($file);
        exit;
    }

    private function reportCourierWiseDues()
    {
        ini_set('max_execution_time', 600);
        $this->load->library('admin/shipping_lib');
        $records = $this->shipping_lib->courierDues();
        $this->data['records'] = $records;

        return $this->load->view('admin/remittance/reports/courier_wise_dues', $this->data, true);
    }

    function exportReportCourierWiseDues()
    {
        $this->load->library('admin/shipping_lib');
        $records = $this->shipping_lib->courierDues();

        $filename = 'SellerExpectedDues.csv';
        header("Content-Description: File Transfer");
        header("Content-Disposition: attachment; filename=$filename");
        header("Content-Type: application/csv; ");
        $file = fopen('php://output', 'w');
        $header = array("Courier", "Amount");
        fputcsv($file, $header);
        foreach ($records as $rec) {
            $row = array(
                $rec->courier_name,
                $rec->due_total,
            );
            fputcsv($file, $row);
        }
        fclose($file);
        exit;
    }

    function exportRemittanceAWB($remittance_id = false)
    {
        if (!$remittance_id) {
            $this->session->set_flashdata('error', 'Details Missing');
            redirect(base_url('admin/remittance/reports/remittance'));
        }

        $remittance = $this->remittance_lib->getByID($remittance_id);
        if (empty($remittance)) {
            $this->session->set_flashdata('error', 'Invalid Access');
            redirect(base_url('admin/remittance/reports/remittance'));
        }

        $shipments = $this->remittance_lib->getShippingDetails($remittance_id);


        $filename = 'Remittance_' . $remittance_id . '.csv';
        header("Content-Description: File Transfer");
        header("Content-Disposition: attachment; filename=$filename");
        header("Content-Type: application/csv; ");
        $file = fopen('php://output', 'w');
        $header = array("Shipment ID", "Shipment Date", "Order ID", "Carrier Name", "AWB", "Amount", "Delivery Date");
        fputcsv($file, $header);
        foreach ($shipments as $shipment) {
            $row = array(
                $shipment->shipping_id,
                (!empty($shipment->shipping_created)) ? date('d-M-Y', $shipment->shipping_created) : '',
                $shipment->order_id,
                $shipment->courier_name,
                $shipment->awb_number,
                $shipment->order_amount,
                (!empty($shipment->delivered_time)) ? date('d-M-Y', $shipment->delivered_time) : ''
            );
            fputcsv($file, $row);
        }
        fclose($file);
        exit;
    }

    function exportExpectedDues($type = 'seller', $id = false)
    {
        if (!$type || !$id) {
            $this->session->set_flashdata('error', 'Details Missing');
            redirect(base_url('admin/remittance/reports/dues'));
        }

        $this->load->library('admin/shipping_lib');
        $filter = array();
        if ($type == 'seller') {
            $filter['seller_id'] = $id;
        } elseif ($type == 'courier') {
            $filter['courier_id'] = $id;
        }

        $query = $this->shipping_lib->expectedDues($filter);

        $this->load->library('export_db');

        $export = new Export_db('slave');
        $export->query($query);


        $filename = 'ExpectedDues_' . $id . '.csv';
        header("Content-Description: File Transfer");
        header("Content-Disposition: attachment; filename=$filename");
        header("Content-Type: application/csv; ");
        $file = fopen('php://output', 'w');
        $header = array("Shipment ID", "Shipment Date", "Seller Company", "Courier Name", "AWB Number", "Amount", "Early Paid", "Delivered Date");
        fputcsv($file, $header);
        while ($shipment = $export->next()) {
            $row = array(
                $shipment->id,
                (!empty($shipment->created)) ? date('d-M-Y', $shipment->created) : '',
                $shipment->user_company,
                $shipment->courier_name,
                $shipment->awb_number,
                $shipment->order_total_amount,
                ($shipment->remittance_id > 0) ? 'Yes' : 'No',
                (!empty($shipment->delivered_time)) ? date('d-M-Y', $shipment->delivered_time) : '',
            );
            fputcsv($file, $row);
        }
        fclose($file);
        exit;
    }

    function seller_awb_list($seller_id = false)
    {
        if (empty($seller_id)) {
            $this->data['error'] = 'Invalid Seller ID';
            $this->layout('remittance/awb_list', 'NONE');
            return;
        }
        $this->load->library('admin/shipping_lib');
        $shipments = $this->shipping_lib->payableShipmentsOfUser($seller_id);

        $this->data['shipments'] = $shipments;

        $this->layout('remittance/awb_list', 'NONE');
        return;
    }

    function seller_freight_history($seller_id = false)
    {
        if (empty($seller_id)) {
            $this->data['error'] = 'Invalid Seller ID';
            $this->layout('remittance/seller_freight_history', 'NONE');
            return;
        }
        $this->load->library('admin/wallet_lib');
        $history = $this->wallet_lib->sellerWallerRechargeNAdjustments($seller_id);
        $this->data['history'] = $history;

        $this->layout('remittance/seller_freight_history', 'NONE');
        return;
    }

    function create_partial_remittance()
    {

        $this->load->library('form_validation');
        $config = array(
            array(
                'field' => 'shipping_ids[]',
                'label' => 'Shipments ID',
                'rules' => 'trim|required|numeric'
            ),
        );



        $this->form_validation->set_rules($config);

        if (!$this->form_validation->run()) {
            $this->data['json'] = array('error' => strip_tags(validation_errors()));
            $this->layout(false, 'json');
            return;
        }

        //create remittance for these shipment ids

        $shipping_ids = $this->input->post('shipping_ids');

        if (is_null($shipping_ids) || empty($shipping_ids)) {
            $this->data['json'] = array('error' => 'Unable to create remittance');
            $this->layout(false, 'json');
            return;
        }

        $this->load->library('admin/remittance_lib');

        $wallet_adjust = false;
        if ($this->input->post('walet_adjustment') == 'yes')
            $wallet_adjust = true;

        if (!$this->remittance_lib->createRemittanceByShipmentID($shipping_ids, $wallet_adjust, false, $this->user->user_id)) {
            $this->data['json'] = array('error' => 'Unable to create remittance');
            $this->layout(false, 'json');
            return;
        }

        $this->data['json'] = array('success' => 'Remittance Created');
        $this->layout(false, 'json');
    }

    function sellerExpectedAwbList($seller_id = false)
    {
        if (empty($seller_id)) {
            $this->data['error'] = 'Invalid Seller ID';
            $this->layout('remittance/awb_list', 'NONE');
            return;
        }
        $this->load->library('admin/shipping_lib');
        $shipments = $this->shipping_lib->expectedShipmentsOfUser($seller_id);

        $this->data['shipments'] = $shipments;

        $this->layout('remittance/awb_list', 'NONE');
        return;
    }

    function sellerRemittanceCycleAWBs($seller_id = false)
    {
        if (empty($seller_id)) {
            $this->data['error'] = 'Invalid Seller ID';
            $this->layout('remittance/awb_list', 'NONE');
            return;
        }
        $this->load->library('admin/shipping_lib');
        $shipments = $this->shipping_lib->remittanceCycleAWbs($seller_id);

        $this->data['shipments'] = $shipments;

        $this->layout('remittance/awb_list', 'NONE');
        return;
    }

    function payEarlyCOD()
    {

        $this->load->library('form_validation');
        $config = array(
            array(
                'field' => 'shipping_ids[]',
                'label' => 'Shipments ID',
                'rules' => 'trim|required|numeric'
            ),
        );


        $this->form_validation->set_rules($config);

        if (!$this->form_validation->run()) {
            $this->data['json'] = array('error' => strip_tags(validation_errors()));
            $this->layout(false, 'json');
            return;
        }

        //create remittance for these shipment ids

        $shipping_ids = $this->input->post('shipping_ids');

        $this->load->library('admin/remittance_lib');

        $wallet_adjust = false;
        if ($this->input->post('walet_adjustment') == 'yes')
            $wallet_adjust = true;

        if (is_null($shipping_ids) || empty($shipping_ids)) {
            $this->data['json'] = array('error' => 'Unable to create remittance');
            $this->layout(false, 'json');
            return;
        }

        if (!$this->remittance_lib->createRemittanceByShipmentID($shipping_ids, $wallet_adjust, false, $this->user->user_id)) {
            $this->data['json'] = array('error' => 'Unable to create remittance');
            $this->layout(false, 'json');
            return;
        }

        $this->data['json'] = array('success' => 'Remittance Created');
        $this->layout(false, 'json');
    }

    function payRemittanceCyclePartial()
    {

        $this->load->library('form_validation');
        $config = array(
            array(
                'field' => 'shipping_ids[]',
                'label' => 'Shipments ID',
                'rules' => 'trim|required|numeric'
            ),
        );


        $this->form_validation->set_rules($config);

        if (!$this->form_validation->run()) {
            $this->data['json'] = array('error' => strip_tags(validation_errors()));
            $this->layout(false, 'json');
            return;
        }

        //create remittance for these shipment ids

        $shipping_ids = $this->input->post('shipping_ids');

        $this->load->library('admin/remittance_lib');

        $wallet_adjust = false;
        if ($this->input->post('walet_adjustment') == 'yes')
            $wallet_adjust = true;

        if (is_null($shipping_ids) || empty($shipping_ids)) {
            $this->data['json'] = array('error' => 'Unable to create remittance');
            $this->layout(false, 'json');
            return;
        }

        if (!$this->remittance_lib->createRemittanceByShipmentID($shipping_ids, $wallet_adjust, false, $this->user->user_id)) {
            $this->data['json'] = array('error' => 'Unable to create remittance');
            $this->layout(false, 'json');
            return;
        }

        $this->data['json'] = array('success' => 'Remittance Created');
        $this->layout(false, 'json');
    }

    function bulkExportCSVRemittance()
    {

        $filter = $this->input->get('filter');
        $apply_filters = array();

        $filter = $this->input->get('filter');
        $apply_filters = array();

        $apply_filters['start_date'] = strtotime("-7 days midnight");
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

        if (!empty($filter['remittance_id'])) {
            $apply_filters['remittance_id'] = array_map('trim', explode(',', $filter['remittance_id']));
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

        if (!empty($filter['created_by'])) {
            $apply_filters['created_by'] = $filter['created_by'];
        }

        if (!empty($filter['created_by_user'])) {
            $apply_filters['created_by_user'] = $filter['created_by_user'];
        }

        if (!empty($filter['date_type'])) {
            $apply_filters['date_type_field'] = trim($filter['date_type']);
        } else {
            $apply_filters['date_type_field'] = 'created';
        }

        $query = $this->remittance_lib->bulkremittanceHistory(150000000, 0, $apply_filters);

        $this->load->library('export_db');

        $export = new Export_db('slave');
        $export->query($query);
        $filename = 'remittance_' . time() . '.csv';
        header("Content-Description: File Transfer");
        header("Content-Disposition: attachment; filename=$filename");
        header("Content-Type: application/csv; ");
        $file = fopen('php://output', 'w');
        $header = array("Remittance ID", "Remittance Date", "Shipment ID", "Order Number", "Shipment Date", "Seller ID", "Seller Name", "Seller Company", "Courier Name", "AWB Number", "Delivered Date", "Amount", "Shipment Status", "Remittance Status", "Remitted Date(If paid)", "Remitted by", "Order Type", "Client Mail ID", "Client Contact Number", "UTR No");
        fputcsv($file, $header);
        while ($remittance = $export->next()) {
            $row = array(
                $remittance->id,
                date('Y-m-d', $remittance->created),
                $remittance->shipment_id,
                $remittance->order_id,
                date('Y-m-d', $remittance->shipment_date),
                $remittance->user_id,
                ucwords($remittance->user_fname . ' ' . $remittance->user_lname),
                ucwords(!empty($remittance->user_company) ? $remittance->user_company : ''),
                ucwords($remittance->courier),
                $remittance->awb_number,
                date('Y-m-d', $remittance->delivered_date),
                round($remittance->amount, 2),
                ucwords($remittance->shipment_status),
                ($remittance->paid == '1') ? 'Yes' : 'No',
                !empty($remittance->payment_date) ? date('Y-m-d', $remittance->payment_date) : '',
                ($remittance->seller_created == '1') ? 'Seller' : 'delta',
                $remittance->order_type,
                $remittance->seller_email,
                $remittance->seller_phone,
                $remittance->utr_number,
            );
            fputcsv($file, $row);
        }
        fclose($file);
        exit;
    }

    function report_awbwise()
    {
        return $this->load->view('admin/remittance/reports/awbwise_payable', $this->data, true);
    }

    function export_report_awbwise()
    {

        ini_set('max_execution_time', 600);

        $this->load->library('admin/shipping_lib');

        $apply_filter = $this->input->post();

        $this->load->library('admin/user_lib');

        $query = $this->shipping_lib->sellerPendingRemittanceAwbwise(150000000, 0, $apply_filter);
        $this->load->library('export_db');

        $export = new Export_db('slave');
        $export->query($query);
        $filename = 'Bulk_remittance_' . time() . '.csv';
        header("Content-Description: File Transfer");
        header("Content-Disposition: attachment; filename=$filename");
        header("Content-Type: application/csv; ");
        $file = fopen('php://output', 'w');
        $header = array(
            "User ID",
            "User Name",
            "Company name",
            "AWB Number",
            "Courier",
            "Payment Type",
            "Shipment Status",
            "Remittance Cycle",
            "Amount",
            "Shipment Date",
            "Delivered Date",
            "Remittance Received",
            "Receipt ID",
            "Receipt Amount"
        );

        fputcsv($file, $header);
        while ($order = $export->next()) {
            $Remittance_Received_status = ($order->receiptId == 0) ? "No" : "Yes";
            $delivered_date = date('d/m/Y', $order->delivered_time);
            $shipment_date = date('d/m/Y', $order->pickup_time);
            $row = array(
                $order->user_id,
                ucfirst($order->user_name),
                $order->user_company,
                $order->awb_number,
                $order->courier_name,
                $order->payment_type,
                ucfirst($order->ship_status),
                "T+" . $order->remittance_cycle,
                $order->order_total_amount,
                $shipment_date,
                $delivered_date,
                $Remittance_Received_status,
                $order->receiptId,
                $order->receiptAmount
            );
            fputcsv($file, $row);
        }
        fclose($file);

        return $this->load->view('admin/remittance/reports/awbwise_payable', $this->data, true);
    }


    function exportHDFCCSVRemittance()
    {
        $filter = $this->input->get('filter');
        $apply_filters = array();

        $filter = $this->input->get('filter');
        $apply_filters = array();

        $apply_filters['start_date'] = strtotime("-7 days midnight");
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

        if (!empty($filter['remittance_id'])) {
            $apply_filters['remittance_id'] = array_map('trim', explode(',', $filter['remittance_id']));
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

        if (!empty($filter['created_by'])) {
            $apply_filters['created_by'] = $filter['created_by'];
        }

        if (!empty($filter['created_by_user'])) {
            $apply_filters['created_by_user'] = $filter['created_by_user'];
        }

        if (!empty($filter['date_type'])) {
            $apply_filters['date_type_field'] = trim($filter['date_type']);
        } else {
            $apply_filters['date_type_field'] = 'created';
        }

        $remittances = $this->remittance_lib->remittanceHistory(15000, 0, $apply_filters);

        $filename = 'remittance_hdfc_' . time() . '.csv';
        header("Content-Description: File Transfer");
        header("Content-Disposition: attachment; filename=$filename");
        header("Content-Type: application/csv; ");
        $file = fopen('php://output', 'w');

        $header = array(
            "Remittance ID", "Remittance Date", "Created By", "Seller ID", "Company", "Invoice Mode", "Wallet Balance", "COD Amount", "Freight Deductions", "Remittance Paid", "Convenience Fee", "Paid", "Payment Date", "Early Cod Charges", "UTR Number", "Transaction Type - N", "Blank", "Beneficiary Account Number", "Instrument Amount", "Beneficiary Name", "Blank", "Blank", "Blank", "Blank", "Blank", "Blank", "Blank", "Narration to Vendor", "Narration to Own", "Payment Details ", "Payment Details ", "Payment Details ", "Payment Details ", "Payment Details ", "Payment Details ", "Payment Details ", "Blank", "DATE - DD/MM/YYYY", "Blank", "IFSC Code", "Blank", "Blank", "Beneficiary email id"
        );

        fputcsv($file, $header);
        foreach ($remittances as $remittance) {
            if (strtoupper(substr($remittance->ifsc_code, 0, 4)) == 'HDFC') {
                $transaction_type = 'N';
            } else if ((strtoupper(substr($remittance->ifsc_code, 1, 4)) != 'HDFC' or   (!empty($remittance->ifsc_code))) and  $remittance->amount >= 200000) {
                $transaction_type = 'R';
            } else if ((strtoupper(substr($remittance->ifsc_code, 1, 4)) != 'HDFC' or   (!empty($remittance->ifsc_code))) and  $remittance->amount <= 200000) {
                $transaction_type = 'N';
            }
            $row = array(
                $remittance->id,
                date('Y-m-d', $remittance->created),
                ($remittance->seller_created == '1') ? 'Seller' : 'delta (' . $remittance->createdby_fname . ' ' . $remittance->createdby_lname . ')',
                $remittance->user_id,
                ucwords(empty($remittance->company_name) ? $remittance->user_company : $remittance->user_fname . ' ' . $remittance->user_lname),
                ($remittance->is_postpaid) ? 'Postpaid' : 'Prepaid',
                round($remittance->wallet_balance, 2),
                round($remittance->amount, 2),
                ($remittance->freight_deductions > 0) ? round($remittance->freight_deductions, 2) : 0,
                ($remittance->remittance_amount > 0) ? round($remittance->remittance_amount, 2) : '0',
                ($remittance->convenience_fee > 0) ? round($remittance->convenience_fee, 2) : '0',
                ($remittance->paid == '1') ? 'Yes' : 'No',
                !empty($remittance->payment_date) ? date('Y-m-d', $remittance->payment_date) : '',
                !empty($remittance->early_cod_charges) ? $remittance->early_cod_charges : '0',
                $remittance->utr_number,
                $transaction_type,
                "",
                !empty($remittance->account_number) ?  "'" . $remittance->account_number : '',
                round($remittance->amount, 2),
                !empty($remittance->account_name) ? substr($remittance->account_name, 0, 40) : "",
                "",
                "",
                "",
                "",
                "",
                "",
                "",
                substr($remittance->id, 0, 20),
                substr($remittance->id, 0, 20),
                "",
                "",
                "",
                "",
                "",
                "",
                "",
                "",
                date('d/m/Y'),
                "",
                strtoupper($remittance->ifsc_code),
                "",
                "",
                "jitendrak@deltagloabal.com",
            );

            fputcsv($file, $row);
        }
        fclose($file);
        exit;
    }
}
