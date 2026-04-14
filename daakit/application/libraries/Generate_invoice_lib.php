<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Generate_invoice_lib extends MY_lib
{

    var $rows = array();
    var $month;
    var $start_date;
    var $end_date;
    var $user_id;
    var $gst_type = 'inside';
    var $company;
    var $state_code = '06';
    var $place_of_supply = 'haryana';

    var $state_codes = array(
        '01' => 'jammu & kashmir',
        '02' => 'himachal pradesh',
        '03' => 'punjab',
        '04' => 'chandigarh',
        '05' => 'uttarakhand',
        '06' => 'haryana',
        '07' => 'delhi',
        '08' => 'rajasthan',
        '09' => 'uttar pradesh',
        '10' => 'bihar',
        '11' => 'sikkim',
        '12' => 'arunachal pradesh',
        '13' => 'nagaland',
        '14' => 'manipur',
        '15' => 'mizoram',
        '16' => 'tripura',
        '17' => 'meghalaya',
        '18' => 'assam',
        '19' => 'west bengal',
        '20' => 'jharkhand',
        '21' => 'odisha',
        '22' => 'chhattisgarh',
        '23' => 'madhya Pradesh',
        '24' => 'gujarat',
        '25' => 'daman & diu',
        '26' => 'dadra & nagar haveli & daman & diu ',
        '27' => 'maharashtra',
        '29' => 'karnataka',
        '30' => 'goa',
        '31' => 'lakshdweep',
        '32' => 'kerala',
        '33' => 'tamil nadu',
        '34' => 'puducherry',
        '35' => 'andaman & nicobar islands',
        '36' => 'telangana',
        '37' => 'andhra pradesh',
        '38' => 'ladakh',
        '96' => 'foreign country',
        '97' => 'other territory'
    );

    public function __construct($config = array())
    {
        parent::__construct();
        $this->CI->load->model('invoice_model');

        if (empty($config['user_id']))
            return false;

        $this->user_id = $config['user_id'];

        if (!empty($config['month'])) {
            $this->month = $config['month'];
            $this->start_date = strtotime("first day of {$config['month']} midnight");
            $this->end_date = strtotime("last day of {$config['month']} 23:59:59");
        } else {
            $this->month = date('M Y', strtotime("last month"));
            $this->start_date = strtotime("first day of last month midnight");
            $this->end_date = strtotime("last day of last month 23:59:59");
        }

        $this->init();

        $this->CI->load->library('shipping_lib');
    }

    function init()
    {
        $this->CI->load->library('setting_lib');
        $company_details = $this->CI->setting_lib->getsettingByUserID($this->user_id);
        $this->company = $company_details;

        $this->CI->load->library('user_lib');
        $user = $this->CI->user_lib->getByID($this->user_id);
        $this->user_details = $user;


        if (!empty($company_details)) {
            if (!empty($company_details->cmp_gstno)) {
                $gst_state = substr($company_details->cmp_gstno, 0, 2);
                if ($gst_state != '06')
                    $this->gst_type = 'outside';
                $this->state_code = $gst_state;
                $this->place_of_supply = array_key_exists($this->state_code, $this->state_codes) ? $this->state_codes[$this->state_code] : '';
            } elseif (!empty($this->company->cmp_state) && strtolower($this->company->cmp_state) != 'haryana') {
                $this->gst_type = 'outside';
                $gst_state = array_search(strtolower($this->company->cmp_state), $this->state_codes);
                if ($gst_state) {
                    $this->state_code = $gst_state;
                    $this->place_of_supply = $this->company->cmp_state;
                }
            }
        }
    }

    public function __call($method, $arguments)
    {
        if (!method_exists($this->CI->invoice_model, $method)) {
            throw new Exception('Undefined method invoice_model::' . $method . '() called');
        }

        return call_user_func_array([$this->CI->invoice_model, $method], $arguments);
    }

    function generateInvoiceForUser()
    {

        $user_id = $this->user_id;
        if (!$user_id)
            return false;

        $filter = array(
            'start_date' => $this->start_date,
            'end_date' => $this->end_date,
        );

        //check if user invoice exists for this month

        $exists = $this->checkUserInvoiceExists($this->user_id, $this->month);
        if (!empty($exists))
            return false;

        $this->addForwardtoCSV($user_id);
        $this->addRTOtoInvoice($user_id);

        if (empty($this->rows))
            return false;

        $filename = date('d-M-y') . '_' . time() . rand(1111, 9999) . '.csv';

        //generate csv file

        $file = fopen('assets/invoice/' . $filename, 'w');
        $header = array("Bill Type", "Carrier", "AWB Number", "Shipment Status", "Seller Company Name", "Shipment ID", "Order ID", "Payment Type", "Pincode", "City", "Charged Weight", "Freight Charges", "COD Charges", "IGST", "SGST", "CGST", "Grand Total");
        fputcsv($file, $header);

        foreach ($this->rows as $row) {
            fputcsv($file, $row);
        }

        fclose($file);

        //save the invoice details to db
        $invoice_total = array_sum(array_column($this->rows, 'total'));

        $invoice_total_without_gst = round($invoice_total / 1.18, 2);
        $igst = ($this->gst_type == 'outside') ? round((($invoice_total_without_gst * 18) / 100), 2) : '0';
        $sgst = ($this->gst_type == 'inside') ? round((($invoice_total_without_gst * 9) / 100), 2) : '0';
        $cgst = ($this->gst_type == 'inside') ? round((($invoice_total_without_gst * 9) / 100), 2) : '0';
        $total = round($invoice_total_without_gst + $sgst + $sgst + $igst, 2);

        $save = array(
            'user_id' => $this->user_id,
            'pre_gst' => $invoice_total_without_gst,
            'igst' => $igst,
            'sgst' => $sgst,
            'cgst' => $cgst,
            'total_amount' => $total,
            'month' => date('M Y', $this->start_date),
            'csv_file' => $filename
        );

        $invoice_id = $this->insert($save);

        $this->createInvoicePDF($invoice_id, true);

        do_action('invoice.new', $invoice_id);

        return $invoice_id;
    }

    function createInvoicePDF($invoice_id, $save_to_db = false)
    {

        if (!$invoice_id)
            return false;

        $invoice = $this->getByID($invoice_id);

        $mpdf = new \Mpdf\Mpdf([
            'mode' => 'utf-8',
            'margin_left' => 10,
            'margin_top' => 10,
            'margin_right' => 10,
            'margin_bottom' => 10,
            'tempDir' => './temp',
        ]);

        $invoice_data = array(
            'invoice' => $invoice,
            'company' => $this->company,
            'user' => $this->user_details,
            'state_code' => $this->state_code,
            'place_of_supply' => $this->place_of_supply,
        );

        $pdf_content = $this->CI->load->view('invoice/invoice', $invoice_data, true);

        $mpdf->WriteHTML($pdf_content);

        $file_name = date('d-M-y') . '_' . time() . rand(1111, 9999) . '.pdf';
        //$mpdf->Output();
        $mpdf->Output('assets/invoice/' . $file_name, 'F');

        if ($save_to_db) {
            $update = array(
                'pdf_file' => $file_name,
            );
            $this->update($invoice_id, $update);
        }

        return $file_name;
    }

    private function addForwardtoCSV()
    {
        $user_id = $this->user_id;


        $filter = array(
            'start_date' => $this->start_date,
            'end_date' => $this->end_date,
            'ship_status_not_in' => array(
                'new', 'cancelled'
            )

        );
        //fetchall invoiceable forward shipments for this user
        $shipments = $this->CI->shipping_lib->getByUserID($user_id, 50000, 0, $filter);

        if (empty($shipments))
            return false;

        foreach ($shipments as $shipment) {
            $shipment->package_weight = (!empty($shipment->package_weight)) ? $shipment->package_weight : '500';
            $s = array(
                'bill_type' => 'Forward',
                'awb_number' => $shipment->awb_number,
                'courier_fees' => $shipment->courier_fees + $shipment->extra_weight_charges,
                'cod_fees' => $shipment->cod_fees,
                'carrier' => $shipment->courier_name,
                'shipment_id' => $shipment->shipping_id,
                'order_id' => $shipment->order_id,
                'weight' => ($shipment->charged_weight > $shipment->package_weight) ? $shipment->charged_weight : $shipment->package_weight,
                'pincode' => $shipment->shipping_zip,
                'city' => $shipment->shipping_city,
                'payment_type' => $shipment->order_payment_type,
                'shipment_status' => $shipment->ship_status,
            );
            $this->put_csv_row($s);
            if ($shipment->ship_status == 'rto' && $shipment->rto_date < $this->end_date && $shipment->rto_charges > 0) { // rto in same month
                $s['bill_type'] = 'RTO';
                $s['courier_fees'] = $shipment->rto_charges + $shipment->rto_extra_weight_charges;
                $s['cod_fees'] = '-' . $shipment->cod_reverse_amount;
                $this->put_csv_row($s);
            }
        }
    }

    private function addRTOtoInvoice()
    {

        $user_id = $this->user_id;


        if (!$user_id)
            return false;

        $filter = array(
            'start_date' => $this->start_date,
            'end_date' => $this->end_date,
        );
        //fetch rto shipments in this month
        $rto_shipments = $this->CI->shipping_lib->fetchRTOShipmentsforInvoice($user_id, $filter);

        if (!empty($rto_shipments)) {
            foreach ($rto_shipments as $r_s) {
                $r_s->package_weight = (!empty($r_s->package_weight)) ? $r_s->package_weight : '500';
                $s = array(
                    'bill_type' => 'RTO',
                    'awb_number' => $r_s->awb_number,
                    'courier_fees' => $r_s->rto_charges + $r_s->rto_extra_weight_charges,
                    'cod_fees' => '-' . $r_s->cod_fees,
                    'carrier' => $r_s->courier_name,
                    'shipment_id' => $r_s->shipping_id,
                    'order_id' => $r_s->order_id,
                    'weight' => ($r_s->charged_weight > $r_s->package_weight) ? $r_s->charged_weight : $r_s->package_weight,
                    'pincode' => $r_s->shipping_zip,
                    'city' => $r_s->shipping_city,
                    'payment_type' => $r_s->order_payment_type,
                    'shipment_status' => $r_s->ship_status,
                );
                $this->put_csv_row($s);
            }
        }
    }



    function put_csv_row($save = array())
    {
        if (empty($save))
            return false;

        $courier_fees = !empty($save['courier_fees']) ? round($save['courier_fees'] / 1.18, 2) : '0';
        $cod_fees = !empty($save['cod_fees']) ? round($save['cod_fees'] / 1.18, 2) : '0';
        $fees_total = $courier_fees + $cod_fees;
        $igst = ($this->gst_type == 'outside') ? round((($fees_total * 18) / 100), 2) : '0';
        $sgst = ($this->gst_type == 'inside') ? round((($fees_total * 9) / 100), 2) : '0';
        $cgst = ($this->gst_type == 'inside') ? round((($fees_total * 9) / 100), 2) : '0';
        $total = round($fees_total + $sgst + $sgst + $igst, 2);
        $r = array(
            'bill_type' => !empty($save['bill_type']) ? $save['bill_type'] : '',
            'carrier' => !empty($save['carrier']) ? $save['carrier'] : '',
            'shipment_status' => !empty($save['shipment_status']) ? ucwords($save['shipment_status']) : '',
            'awb_number' => !empty($save['awb_number']) ? $save['awb_number'] : '',
            'company_name' => $this->company->company_name,
            'shipment_id' => !empty($save['shipment_id']) ? $save['shipment_id'] : '',
            'order_id' => !empty($save['order_id']) ? $save['order_id'] : '',
            'payment_type' => !empty($save['payment_type']) ? $save['payment_type'] : '',
            'pincode' => !empty($save['pincode']) ? $save['pincode'] : '',
            'city' => !empty($save['city']) ? $save['city'] : '',
            'weight' => !empty($save['weight']) ? round($save['weight'] / 1000, 1) : '',
            'courier_fees' => $courier_fees,
            'cod_fees' => $cod_fees,
            'igst' => $igst,
            'sgst' => $sgst,
            'cgst' => $cgst,
            'total' => $total,
        );

        $this->rows[] = $r;
    }
}
