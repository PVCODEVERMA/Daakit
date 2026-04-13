<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Generate_invoice extends MY_lib
{

    var $rows = array();
    var $month;
    var $start_date;
    var $end_date;
    var $user_id;
    var $gst_type = 'inside';
    var $company;
    var $csv_file_name;
    var $state_code = '06';
    var $place_of_supply = 'haryana';
    var $service_type;
    var $product_details = array();

      
    var $shipment = array("Bill Type", "Carrier", "AWB Number", "Shipment Status", "Seller Company Name", "Shipment ID", "Order ID", "Payment Type", "Pincode", "City", "Charged Weight", "Foward Freight","RTO Freight","Extra Wgt Charges","RTO Extra Wgt Charges", "COD Charges","Sub Total", "IGST", "SGST", "CGST", "Grand Total","Order Amount","Warehouse City","Warehouse State","Warehouse Pin Code","SKU(1)", "Product(1)", "Quantity(1)","SKU(2)", "Product(2)", "Quantity(2)","SKU(3)", "Product(3)", "Quantity(3)","SKU(4)", "Product(4)", "Quantity(4)","SKU(5)", "Product(5)", "Quantity(5)","SKU(6)", "Product(6)", "Quantity(6)","SKU(7)", "Product(7)", "Quantity(7)","SKU(8)", "Product(8)", "Quantity(8)","SKU(9)", "Product(9)", "Quantity(9)","SKU(10)", "Product(10)", "Quantity(10)");
    var $insurance = array("AWB Number","Carrier", "Shipment Status","Shipment ID", "Order ID", "Insurance Charges", "IGST", "SGST", "CGST", "Grand Total");
    var $addon = array("Date","Service Type","Service Charges", "IGST", "SGST", "CGST", "Grand Total","AWB Number", "Note");
    var $invoice_type = 'b2b';
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
        $this->CI->load->library('orders_lib');
        $this->CI->load->model('Products_model');
        


        if (empty($config['user_id']))
            return false;

        if (empty($config['type']))   
            return false;
        $this->service_type = strtolower($config['type']);
        $this->product_details = $this->getProductName();

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
        $this->financial_year       = $this->CI->config->item('financial_year');
        $this->inv_prefix           = $this->CI->config->item('inv_prefix');
        $this->inv_prefix_credit    = $this->CI->config->item('inv_prefix_credit');
        $this->init();
    }

    function init()
    {
        $this->CI->load->library('setting_lib');
        $this->CI->load->library('profile_lib');
        
        $company_details = $this->CI->setting_lib->getsettingByUserID($this->user_id);

		$legalRecord = $this->CI->profile_lib->getLegalDetailsByUserId($this->user_id);
        if(!empty($legalRecord))
        {
            $company_details->cmp_gstno = $legalRecord->legal_gstno;
            $company_details->company_name =$legalRecord->legal_name;

            $company_details->cmp_address =$legalRecord->legal_address;

            $company_details->cmp_city =  $legalRecord->legal_city;

            $company_details->cmp_pincode =$legalRecord->legal_pincode;

            $company_details->cmp_state = $legalRecord->legal_state;
        }
        $company_details->cmp_address = substr($company_details->cmp_address, 0, 99);

        $this->company = $company_details;

        $this->CI->load->library('user_lib');
        $user = $this->CI->user_lib->getByID($this->user_id);
        $this->user_details = $user;

        $this->invoice_type = 'b2c';
        if (!empty($company_details)) {
            if (!empty($company_details->cmp_gstno)) {
                $gst_state = substr($company_details->cmp_gstno, 0, 2);
                $this->invoice_type = 'b2c';
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
       
        $exists = $this->checkUserInvoiceExists($this->user_id, $this->month, $this->service_type);
      
        if (!empty($exists))
            return false;
        
        

        $exists = $this->checkUserCreditNoteExists($this->user_id, $this->month, $this->service_type);
        
        if (!empty($exists))
            return false; 

           
        $this->addForwardtoCSV($user_id);
        
        if (empty($this->rows))
            return false;

    
           
        $this->generateCSV();
        //save the invoice details to db
        $invoice_total = array_sum(array_column($this->rows, 'total'));
        if ($invoice_total == 0)
            return false;

        if ($invoice_total < 0)
            return $this->generateCreditNote();
        else
            return $this->generateGSTInvoice();
    }

    private function generateCSV()
    {       
        $this->CI->load->library('s3');
        $file_name = date('d-M-y') . '_' . time() . rand(1111, 9999) . '.csv';

        //generate csv file
        $directory = 'assets/invoice/';
        $upload_folder = "invoice";

        $file = fopen($directory . $file_name, 'w');
        $header = $this->{$this->service_type};
        fputcsv($file, $header);
        if($this->service_type != 'addon')
        {
            foreach ($this->rows as $r) {
            $dynamic_values = array();
            for ($i = 1; $i <= 10; $i++) 
            {
                $dynamic_values['SKU'.$i] = !empty($r['SKU'.$i]) ? $r['SKU'.$i] : '';
                $dynamic_values['Product'.$i] = !empty($r['Product'.$i]) ? $r['Product'.$i] : '';
                $dynamic_values['Qty'.$i] = !empty($r['Qty'.$i]) ? $r['Qty'.$i] : '';
            } 
                $csv_row =[];
                switch ($this->service_type) {
                    case 'shipment':
                        $csv_row = [
                            'bill_type' => $r['bill_type'],
                            'carrier' => $r['carrier'],
                            'awb_number' => $r['awb_number'],
                            'shipment_status' => $r['shipment_status'],
                            'company_name' => $r['company_name'],
                            'shipment_id' => $r['shipment_id'],
                            'order_id' => $r['order_id'],
                            'payment_type' => $r['payment_type'],
                            'pincode' => $r['pincode'],
                            'city' => $r['city'],
                            'weight' => $r['weight'],
                            'freight_charges'=>round($r['freight_charges'], 2),
                            'rto_freight_charges'=>round($r['rto_freight_charges'], 2),
                            'extra_weight_charges'=>round($r['extra_weight_charges'], 2),
                            'rto_extra_weight_charges'=>round($r['rto_extra_weight_charges'], 2),
                            'cod_fees'=> round($r['cod_fees'], 2),
                            'sub_total'=>round(($r['freight_charges'] + $r['rto_freight_charges'] + $r['extra_weight_charges'] + $r['rto_extra_weight_charges'] + $r['cod_fees']), 2),
                            'igst'=>round($r['igst'], 2),
                            'sgst'=>round($r['sgst'], 2),
                            'cgst'=>round($r['cgst'], 2),
                            'total'=>round($r['total'], 2),
                            'Order Amount'=> $r['OrderAmount'],
                            'Warehouse City'=> $r['WarehouseCity'],
                            'Warehouse State'=> $r['WarehouseState'],
                            'Warehouse Pin Code'=>$r['WarehousePinCode'],

                        ];

                       for ($i = 1; $i <= 10; $i++) {
                            $csv_row['SKU'.$i] = !empty($dynamic_values['SKU'.$i]) ? $dynamic_values['SKU'.$i] : '';
                            $csv_row['Product'.$i] = !empty($dynamic_values['Product'.$i]) ? $dynamic_values['Product'.$i] : '';
                            $csv_row['Qty'.$i] = !empty($dynamic_values['Qty'.$i]) ? $dynamic_values['Qty'.$i] : '';
                        } 

                        break;
                    default:
                        return false;
                }
                fputcsv($file, $csv_row);
            }
        }   
        fclose($file);
        $this->csv_file_name =$this->CI->s3->amazonS3Upload($file_name, $directory . $file_name, $upload_folder);
    }

    function saveInvoiceData($type = 'invoice', $id = false)
    {
        if (!$id)
            return false;

        $save = array();

        foreach ($this->rows as $row) {
            $freight_charges = $row['courier_fees'] ?? $row['service_charges'] ?? 0;
            $cod_fees = $row['cod_fees'] ?? 0;
            $save[] = array(
                'type' => $type,
                'ref_id' => $id,
                'charged_weight' => $row['weight'] ?? 0,
                'freight_charges' => round($freight_charges, 2),
                'cod_charges' => round($cod_fees, 2),
                'insurance_charges'=>$row['insurance_charges'] ?? 0,
                'igst' => round($row['igst'],2),
                'sgst' => round($row['sgst'],2),
                'cgst' => round($row['cgst'],2),
                'total' => round($row['total'],2),
                'courier' => $row['carrier'] ?? '',
                'awb_number' => $row['awb_number'] ?? '',
                'shipment_status' => $row['shipment_status'] ?? '',
                'seller_company_name' => $row['company_name'] ?? '',
                'shipment_id' => $row['shipment_id'] ?? '',
                'order_id' => $row['order_id'] ?? '',
                'payment_type' => $row['payment_type'] ?? '',
                'pincode' => $row['pincode'] ?? '',
                'city' => $row['city'] ?? '',
                'bill_type' => $row['bill_type'] ?? '',
                'notes' => $row['notes'] ?? '',
            );
        }

        $this->batchInsertInvoiceData($save);
        return true;
    }

    private function  generateCreditNote()
    {

        $total = abs(array_sum(array_column($this->rows, 'total')));
        $igst = abs(array_sum(array_column($this->rows, 'igst')));
        $sgst = abs(array_sum(array_column($this->rows, 'sgst')));
        $cgst = abs(array_sum(array_column($this->rows, 'cgst')));
        
        $pre_gst = round($total / 1.18, 2);
        
        $last_inv = (int)$this->getLastCnInvoiceNo();
        $new_inv_no = $last_inv + 1;
        $save = array(
            'user_id' => $this->user_id,
            'pre_gst' => $pre_gst,
            'igst' => round($igst, 2),
            'sgst' => round($sgst, 2),
            'cgst' => round($cgst, 2),
            'total_amount' => round($total, 2),
            'month' => date('M Y', $this->start_date),
            'csv_file' => $this->csv_file_name,
            'inv_no'    => $new_inv_no,
            'invoice_no'=>$this->inv_prefix_credit.$this->financial_year.'/'.sprintf('%03d', $new_inv_no),
            'invoice_type'=>$this->invoice_type,
            'service_type'=>$this->service_type,
            'gstno' => $this->company->cmp_gstno ?? ''
        );

        $invoice_cn_id = $this->insert_credit_note($save);
        $this->createInvoiceCNPDF($invoice_cn_id, true);
        $this->saveInvoiceData('credit', $invoice_cn_id);
        return $invoice_cn_id;
    }

    private function generateGSTInvoice()
    {
        $total = abs(array_sum(array_column($this->rows, 'total')));
        $igst = abs(array_sum(array_column($this->rows, 'igst')));
        $sgst = abs(array_sum(array_column($this->rows, 'sgst')));
        $cgst =abs(array_sum(array_column($this->rows, 'cgst')));        
        $pre_gst = round($total / 1.18, 2);
        $last_inv = (int)$this->getLastInvoiceNo();
        $new_inv_no = $last_inv + 1;
        $save = array(
            'user_id' => $this->user_id,
            'pre_gst' => $pre_gst,
            'igst' => round($igst, 2),
            'sgst' => round($sgst, 2),
            'cgst' => round($cgst, 2),
            'total_amount' => round($total, 2),
            'month' => date('M Y', $this->start_date),
            'csv_file' => $this->csv_file_name,
            'inv_no'=> $new_inv_no,
            'invoice_no'=>$this->inv_prefix.$this->financial_year.'/'.sprintf('%03d', $new_inv_no),
            'service_type'=>$this->service_type,
            'invoice_type'=>$this->invoice_type,
            'gstno' => $this->company->cmp_gstno ?? ''
        );

        $invoice_id = $this->insert($save);
        $this->createInvoicePDF($invoice_id, true);
        $this->saveInvoiceData('invoice', $invoice_id);
        return $invoice_id;
    }
    private function addForwardtoCSV()
    {
        $user_id = $this->user_id;

        $filter = array(
            'start_date' => $this->start_date,
            'end_date' => $this->end_date,
            'type' => $this->service_type
        );
        $query = $this->getInvoiceTXNs($user_id, $filter);
        $this->CI->load->library('export_db');
        $export = new Export_db();
        $export->query($query);

        while ($shipment = $export->next()) {
            $shipment->package_weight = (!empty($shipment->package_weight)) ? $shipment->package_weight : '500';
            $weight = empty($shipment->charged_weight) ? $shipment->calculated_weight ?? 0 : $shipment->charged_weight ?? 0;
            $shipment->order_payment_type = !empty($shipment->order_payment_type) ? $shipment->order_payment_type :'';
            $s = array(
                'bill_type' => (strtolower($shipment->order_payment_type) == 'reverse') ? 'Reverse' : 'Forward',
                'awb_number' => $shipment->awb_number ?? '',
                'order_amount' =>$shipment->order_amount ?? '',
                'Warehouse_state' =>$shipment->state ?? '',
                'Warehouse_city' =>$shipment->city ?? '',
                'Warehouse_zip' =>$shipment->zip ?? '',
                'carrier' => $shipment->courier_name ?? '',
                'shipment_id' => $shipment->shipment_id ?? '',
                'order_id' => $shipment->order_no ?? '',
                'weight' => $weight,
                'pincode' => $shipment->shipping_zip ?? '',
                'city' => $shipment->shipping_city ?? '',
                'payment_type' => $shipment->order_payment_type,
                'shipment_status' => $shipment->ship_status ?? '',
                'freight_charges' => $shipment->freight_charges ?? 0,
                'rto_freight_charges' => $shipment->rto_freight_charges ?? 0,
                'extra_weight_charges' => $shipment->extra_weight_charges ?? 0,
                'rto_extra_weight_charges' => $shipment->rto_extra_weight_charges ?? 0,
                'cod_charges' => $shipment->cod_charges ?? 0,
                'insurance_charges'=>$shipment->insurance_charges ?? 0,
                'exotel_charges'=> $shipment->exotel_charges ?? 0,
                'exotel_recurring_charges'=> $shipment->exotel_recurring_charges ?? 0,
                'whatsapp_charges'=> $shipment->whatsapp_charges ?? 0,
                'created' => $shipment->created  ?? 0,
                'txn_ref' =>$shipment->txn_ref ?? '',
                'notes' => $shipment->notes ?? ''
                
            );
            if(!empty($shipment->order_db_id))
            {
                $products = $this->CI->orders_lib->getOrderProducts($shipment->order_db_id);
                if (!empty($products))
                {
                    $i=1;
                    foreach ($products as $prod) {
                        $s['sku_'.$i] = !empty($prod->product_sku) ? $prod->product_sku : '';
                        $s['product_'.$i] =  !empty($prod->product_name) ? $prod->product_name : '';
                        $s['qty_'.$i] =  !empty($prod->product_qty) ? $prod->product_qty : '';
                        $i++;
                    }
                } 
            }
            $this->put_csv_row($s);
        }
    }


    private  function createInvoicePDF($invoice_id, $save_to_db = false)
    {
        $this->CI->load->library('s3');
        if (!$invoice_id)
            return false;

        $invoice = $this->getByID($invoice_id);

        $invoice_data = array(
            'invoice' => $invoice,
            'company' => $this->company,
            'user' => $this->user_details,
            'state_code' => $this->state_code,
            'place_of_supply' => $this->place_of_supply,
            'invoice_prefix'=>$this->inv_prefix.$this->financial_year.'/',
            'inv_type'=>'INV',
            'product_name'=>$this->product_details,
            'total_shipment'=>count($this->rows),
        );
        $mpdf = new \Mpdf\Mpdf([
            'mode' => 'utf-8',
            'margin_left' => 10,
            'margin_top' => 10,
            'margin_right' => 10,
            'margin_bottom' => 10,
            'tempDir' => './temp',
        ]);    
        $pdf_content = $this->CI->load->view('invoice/invoice', $invoice_data, true);

        $mpdf->WriteHTML($pdf_content);

        $file_name = date('d-M-y') . '_' . time() . rand(1111, 9999) . '.pdf';
        $upload_folder = "invoice";
        $directory = 'assets/invoice/';

        $mpdf->Output($directory . $file_name, 'F');
        $pdf_url = $this->CI->s3->amazonS3Upload($file_name, $directory . $file_name, $upload_folder);
        // unlink($directory . $file_name);
        
        if ($save_to_db) {
            $update = array(
                'pdf_file' => $pdf_url
            );
            $this->update($invoice_id, $update);
        }

        return $pdf_url;
    }

    private  function createInvoiceCNPDF($invoice_id, $save_to_db = false)
    {
        $this->CI->load->library('s3');
        if (!$invoice_id)
            return false;

        $invoice = $this->getInvoiceCNByID($invoice_id);
        $invoice_data = array(
            'invoice' => $invoice,
            'company' => $this->company,
            'user' => $this->user_details,
            'state_code' => $this->state_code,
            'place_of_supply' => $this->place_of_supply,
            'invoice_prefix'=>$this->inv_prefix_credit.$this->financial_year.'/',
            'inv_type'=>'CRN',
            'product_name'=>$this->product_details,
            'total_shipment'=>count($this->rows)
        );

        $mpdf = new \Mpdf\Mpdf([
            'mode' => 'utf-8',
            'margin_left' => 10,
            'margin_top' => 10,
            'margin_right' => 10,
            'margin_bottom' => 10,
            'tempDir' => './temp',
        ]);
        $pdf_content = $this->CI->load->view('invoice/credit_note', $invoice_data, true);

        $mpdf->WriteHTML($pdf_content);

        $file_name = date('d-M-y') . '_' . time() . rand(1111, 9999) . '.pdf';
        
        $upload_folder = "invoice";
        $directory = 'assets/invoice/';
        
        //$mpdf->Output();
        $mpdf->Output($directory . $file_name, 'F');

        $pdf_url = $this->CI->s3->amazonS3Upload($file_name, $directory . $file_name, $upload_folder);
        
        //unlink($directory . $file_name);
        
        if ($save_to_db) {
            $update = array(
                'pdf_file' => $pdf_url,
            );
            $this->update_credit_note($invoice_id, $update);
        }
        
        return $pdf_url;
    }


    function put_csv_row($save = array())
    {
       
        if (empty($save))
            return false;
        
        switch ($this->service_type) {
            case 'shipment':
                $freight_charges = !empty($save['freight_charges']) ? $save['freight_charges'] / 1.18 : '0';
                $cod_charges = !empty($save['cod_charges']) ? $save['cod_charges'] / 1.18: '0';
                $rto_freight_charges = !empty($save['rto_freight_charges']) ? $save['rto_freight_charges'] / 1.18 : '0';
                $extra_weight_charges = !empty($save['extra_weight_charges']) ? $save['extra_weight_charges'] / 1.18 : '0';
                $rto_extra_weight_charges = !empty($save['rto_extra_weight_charges']) ? $save['rto_extra_weight_charges'] / 1.18: '0';
                $fees_total = $freight_charges + $cod_charges + $rto_freight_charges + $extra_weight_charges + $rto_extra_weight_charges;
                $courier_fees = $freight_charges  + $rto_freight_charges + $extra_weight_charges + $rto_extra_weight_charges;
                $igst = ($this->gst_type == 'outside') ? (($fees_total * 18) / 100) : '0';
                $sgst = ($this->gst_type == 'inside') ? (($fees_total * 9) / 100) : '0';
                $cgst = ($this->gst_type == 'inside') ? (($fees_total * 9) / 100) : '0';
                $total = $fees_total + $cgst + $sgst + $igst;

                $r = array(
                    'bill_type' => !empty($save['bill_type']) ? $save['bill_type'] : '',
                    'carrier' => !empty($save['carrier']) ? $save['carrier'] : '',
                    'awb_number' => !empty($save['awb_number']) ? $save['awb_number'] : '',
                    'shipment_status' => !empty($save['shipment_status']) ? ucwords($save['shipment_status']) : '',
                    'company_name' => $this->company->company_name,
                    'shipment_id' => !empty($save['shipment_id']) ? $save['shipment_id'] : '',
                    'order_id' => !empty($save['order_id']) ? $save['order_id'] : '',
                    'payment_type' => !empty($save['payment_type']) ? strtoupper($save['payment_type']) : '',
                    'pincode' => !empty($save['pincode']) ? $save['pincode'] : '',
                    'city' => !empty($save['city']) ? $save['city'] : '',
                    'weight' => !empty($save['weight']) ? round($save['weight'] / 1000, 1) : '',
                    'courier_fees'=> $courier_fees,
                    'freight_charges'=>$freight_charges,
                    'rto_freight_charges'=>$rto_freight_charges,
                    'extra_weight_charges'=>$extra_weight_charges,
                    'rto_extra_weight_charges'=>$rto_extra_weight_charges,
                    'cod_fees'=> $cod_charges,
                    'igst'=>$igst,
                    'sgst'=>$sgst,
                    'cgst'=>$cgst,
                    'total'=>$total,
                    'OrderAmount'=>$save['order_amount'],
                    'WarehouseCity'=>$save['Warehouse_city'],  
                    'WarehouseState'=>$save['Warehouse_state'],
                    'WarehousePinCode'=>$save['Warehouse_zip']
                    
                    
                );

                for ($i = 0; $i < 10; $i++) {
                    $sku_key = 'SKU' . ($i+1);
                    $product_key = 'Product' . ($i+1);
                    $qty_key = 'Qty' . ($i+1);
                    
                    $sku_value = !empty($save['sku_' . ($i+1)]) ? $save['sku_' . ($i+1)] : '';
                    $product_value = !empty($save['product_' . ($i+1)]) ? $save['product_' . ($i+1)] : '';
                    $qty_value = !empty($save['qty_' . ($i+1)]) ? $save['qty_' . ($i+1)] : '';
                    
                    $r[$sku_key] = $sku_value;
                    $r[$product_key] = $product_value;
                    $r[$qty_key] = $qty_value;
                }
               
                break;
            default:
                return false;

        }
        $this->rows[] = $r;
    }

    function getProductName()
    {
        
        switch ($this->service_type) {
            case 'shipment':
            case 'international':
                $product = array('name'=> 'Shipping charges (HSN Code - 996812)', 'hsn_code'=>'996812');
                break;
            case 'insurance':
                $product = array('name'=> 'Insurance charges (HSN Code - 997135)', 'hsn_code'=>'997135');
                break;
            case 'addon':
                $product = array('name'=> 'Other Support Services (HSN Code - 998599)', 'hsn_code'=>'998599');
                break;
            // // case 'exotel_recurring':
            // //     $product_name = 'Exotel charges (HSN Code - 998429)';
            // //     break;
            // // case 'whatsapp':
            // //     $product_name = 'Whatsapp charges (HSN Code - 998429)';
            //     break;
            default:
                return false;
        }
        return $product;
    }   
}