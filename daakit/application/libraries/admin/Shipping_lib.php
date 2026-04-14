<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Shipping_lib extends MY_lib
{

    public function __construct()
    {
        parent::__construct();

        $this->CI->load->model('admin/shipping_model', 'shp_model');
    }

    public function __call($method, $arguments)
    {
        if (!method_exists($this->CI->shp_model, $method)) {
            throw new Exception('Undefined method shipping_model::' . $method . '() called');
        }

        return call_user_func_array([$this->CI->shp_model, $method], $arguments);
    }

    function applyAWBWeight($id = false, $weight = false)
    {
        if (!$id || !$weight)
            return false;

        $shipment = $this->getByID($id);

        if (!$shipment)
            return false;

        if ($shipment->extra_weight_charges > '0')
            return false;

        $this->CI->load->library('admin/orders_lib');
        $order = $this->CI->orders_lib->getByID($shipment->order_id);

        if (!$order)
            return false;

        $this->CI->load->library('courier_lib');
        $courier = $this->CI->courier_lib->getByID($shipment->courier_id);

        if (!$courier)
            return false;

        $this->CI->load->library('warehouse_lib');
        $warehouse = $this->CI->warehouse_lib->getByID($shipment->warehouse_id);

        if (!$warehouse)
            return false;

        $this->CI->load->library('admin/user_lib');
        $user = $this->CI->user_lib->getByID($order->user_id);

        $this->CI->load->library('pricing_lib');
        $pricing = new Pricing_lib();

        $pricing->setPlan($user->pricing_plan);
        $pricing->setCourier($shipment->courier_id);
        if (strtolower($order->order_payment_type) == 'reverse') {
            $pricing->setOrigin($order->shipping_zip);
            $pricing->setDestination($warehouse->zip);
            $pricing->setType('reverse');
        } else {
            $pricing->setOrigin($warehouse->zip);
            $pricing->setDestination($order->shipping_zip);
        }
        $pricing->setWeight($weight);
        $pricing->setLength($order->package_length);
        $pricing->setHeight($order->package_height);
        $pricing->setBreadth($order->package_breadth);
        $shipping_cost = $pricing->calculateCost();

        if (empty($shipping_cost))
            return false;

        $chargeable_amount = $shipping_cost['courier_charges'];
        $charged_amount = $shipment->courier_fees;

        $this->CI->load->library('wallet_lib');

        if ($chargeable_amount > $charged_amount) {
            $extra_charge = round($chargeable_amount - $charged_amount, 2);
            $wallet = new Wallet_lib();
            $wallet->setUserID($user->id);
            $wallet->setAmount($extra_charge);
            $wallet->setTransactionType('debit');
            $wallet->setNotes('Weight Reconciliation Charges');
            $wallet->setTxnFor('shipment');
            $wallet->setRefID($shipment->id);
            $wallet->setTxnRef('extra_weight');
            $wallet->creditDebitWallet();

            $save = array(
                'charged_weight' => $weight,
                'extra_weight_charges' => $extra_charge,
            );

            //apply rto weight charges
            if ($shipment->ship_status == 'rto') {
                $wallet = new Wallet_lib();
                $wallet->setUserID($user->id);
                $wallet->setAmount($extra_charge);
                $wallet->setTransactionType('debit');
                $wallet->setNotes('RTO Weight Reconciliation Charges');
                $wallet->setTxnFor('shipment');
                $wallet->setRefID($shipment->id);
                $wallet->setTxnRef('rto_extra_weight');
                $wallet->creditDebitWallet();
                $save['rto_extra_weight_charges'] = $extra_charge;
            }
            $this->update($shipment->id, $save);
        }
        return true;
    }

    function cancelShipment($shipment_id = false)
    {
        if (!$shipment_id)
            return false;
        $shipment = $this->getByID($shipment_id);
        if (empty($shipment)) {
            $this->error = 'Shipment not available';
            return false;
        }

        if (!in_array(strtolower($shipment->ship_status), array('new'))) {
            $this->error = 'Unable to cancel';
            return false;
        }

        $update = array(
            'ship_status' => 'cancelled'
        );
        $this->update($shipment_id, $update);
        return true;
    }

    function changePaymentTypeUpload()
    {
        if (empty($_FILES['importFile']['tmp_name'])) {
            $this->error = 'Blank CSV File';
            return false;
        }

        $allowed_mime_types = array('text/x-comma-separated-values', 'text/comma-separated-values', 'application/octet-stream', 'application/vnd.ms-excel', 'application/x-csv', 'text/x-csv', 'text/csv', 'application/csv', 'application/excel', 'application/vnd.msexcel', 'text/plain');

        $mime = get_mime_by_extension($_FILES['importFile']['name']);
        $fileAr = explode('.', $_FILES['importFile']['name']);
        $ext = end($fileAr);
        if (($ext != 'csv') || !in_array($mime, $allowed_mime_types)) {
            $this->error = 'Invalid File Format';
            return false;
        }

        if (is_uploaded_file($_FILES['importFile']['tmp_name'])) {
            // Load CSV reader library
            $this->CI->load->library('csvreader');
            // Parse data from CSV file
            $csvData = $this->CI->csvreader->parse_csv($_FILES['importFile']['tmp_name']);
            if (empty($csvData)) {
                $this->error = 'Blank CSV File';
                return false;
            }

            if (count($csvData) > 1000) {
                $this->error = 'Only 1000 AWBs are allowed';
                return false;
            }

            foreach ($csvData as $row_key => $row) {

              
                if (!array_key_exists("AWB Number", $row)) {
                    $this->error = 'AWB Number header is missing.';
                    return false;
                }

                if (!array_key_exists("Payment Type", $row)) {
                    $this->error =  'Payment Type header is missing.';
                    return false;
                }

                if (!$this->validate_changePaymentType($row)) {
                    $this->error = 'Row No. (' . ($row_key + 1) . '): ' . strip_tags($this->error);
                    return false;
                }
            }

            $csv = new Csv_lib();

            $csv->add_row(array('AWB Number', 'Payment Type', 'Message'));

            $this->CI->load->library('admin/orders_lib');
            $this->CI->load->library('warehouse_lib');
            $this->CI->load->library('admin/user_lib');

            foreach ($csvData as $row2) {
                $awb_number = $row2['AWB Number'];
                $payment_type = strtolower($row2['Payment Type']);
                $csv_row = array(
                    $row2['AWB Number'],
                    $row2['Payment Type']
                );
                $shipment = $this->getByAWB($awb_number);
                // pr($shipment);
                $update = array();
                $save = array();
                if (empty($shipment) || empty($shipment->order_id)) {
                    $csv_row[] = 'AWB Number Not Found';
                    $csv->add_row($csv_row);
                    continue;
                }

                $awb_order = $this->CI->orders_lib->getByID($shipment->order_id);

                if (empty($awb_order)) {
                    $csv_row[] = 'AWB Number Not Found';
                    $csv->add_row($csv_row);
                    continue;
                }

                if ($shipment->ship_status == 'cancelled') {
                    $csv_row[] = 'Not able to change the payment mode';
                    $csv->add_row($csv_row);
                    continue;
                }

                if (strtolower($awb_order->order_payment_type) == strtolower($payment_type)) {
                    $csv_row[] = 'Payment type is already ' . strtoupper($payment_type);
                    $csv->add_row($csv_row);
                    continue;
                }

                $update['order_payment_type'] = $payment_type;

                $this->CI->orders_lib->update($awb_order->id, $update);

                $shipment_update["payment_type"] = $payment_type;

                $this->update($shipment->id, $shipment_update);

                if (($shipment->cod_fees) <= 0 && (strtolower($payment_type) == 'cod')) {

                    $order = $this->CI->orders_lib->getByID($shipment->order_id);

                    if (!$order)
                        return false;

                    $warehouse = $this->CI->warehouse_lib->getByID($shipment->warehouse_id);

                    if (!$warehouse)
                        return false;

                    $user = $this->CI->user_lib->getByID($order->user_id);

                    $this->CI->load->library('pricing_lib');
                    $pricing = new Pricing_lib();

                    $pricing->setPlan($user->pricing_plan);
                    $pricing->setCourier($shipment->courier_id);
                    $pricing->setOrigin($order->shipping_zip);
                    $pricing->setDestination($warehouse->zip);
                    $pricing->setType('cod');
                    $pricing->setWeight($shipment->calculated_weight);
                    $pricing->setLength($order->package_length);
                    $pricing->setHeight($order->package_height);
                    $pricing->setBreadth($order->package_breadth);
                    $shipping_cost = $pricing->calculateCost();

                    if (empty($shipping_cost))
                        return false;

                    $shipment_array['cod_fees'] = $shipping_cost['cod_charges'];
                    $shipment_array['total_fees'] = $shipping_cost['cod_charges'] + $shipment->total_fees;

                    $this->CI->load->library('wallet_lib');

                    $wallet = new Wallet_lib(array('user_id' => $shipment->user_id));
                    $wallet->setAmount($shipping_cost['cod_charges']);
                    $wallet->setTransactionType('debit');
                    $wallet->setNotes('COD Charges');
                    $wallet->setRefID($shipment->id);
                    $wallet->setTxnFor('shipment');
                    $wallet->setTxnRef('cod');
                    $wallet->creditDebitWallet();

                    $this->update($shipment->id, $shipment_array);
                }

                $csv_row[] = 'Success';
                $csv->add_row($csv_row);
            }

            $csv->export_csv();
            return true;
        }
    }

    private function validate_changePaymentType($data)
    {
        $this->CI->form_validation->set_data($data);

        $this->CI->form_validation->set_message('required', '%s is required');
        $this->CI->form_validation->set_message('alpha_numeric_spaces', 'Only Characters, Numbers & Spaces are  allowed in %s');
        $this->CI->form_validation->set_message('alpha_dash', 'Only Characters, Numbers & Dash are  allowed in %s');

        $config = array(
            array(
                'field' => 'AWB Number',
                'label' => 'AWB Number',
                'rules' => 'trim|required|alpha_numeric|min_length[4]|max_length[20]',
            ),
            array(
                'field' => 'Payment Type',
                'label' => 'Payment Type',
                'rules' => 'trim|required|in_list[cod,prepaid,COD,PREPAID,Cod,Prepaid]',
            ),
        );

        $this->CI->form_validation->set_rules($config);

        if ($this->CI->form_validation->run()) {
            return true;
        } else {
            $this->error = validation_errors();
            return false;
        }
    }

    function edShipments()
    {
        if (empty($_FILES['importFile']['tmp_name'])) {
            $this->error = 'Blank CSV File';
            return false;
        }

        $allowed_mime_types = array('text/x-comma-separated-values', 'text/comma-separated-values', 'application/octet-stream', 'application/vnd.ms-excel', 'application/x-csv', 'text/x-csv', 'text/csv', 'application/csv', 'application/excel', 'application/vnd.msexcel', 'text/plain');

        $mime = get_mime_by_extension($_FILES['importFile']['name']);
        $fileAr = explode('.', $_FILES['importFile']['name']);
        $ext = end($fileAr);
        if (($ext != 'csv') || !in_array($mime, $allowed_mime_types)) {
            $this->error = 'Invalid File Format';
            return false;
        }

        if (is_uploaded_file($_FILES['importFile']['tmp_name'])) {
            // Load CSV reader library
            $this->CI->load->library('csvreader');
            // Parse data from CSV file
            $csvData = $this->CI->csvreader->parse_csv($_FILES['importFile']['tmp_name']);
            if (empty($csvData)) {
                $this->error = 'Blank CSV File';
                return false;
            }

            if (count($csvData) > 1000) {
                $this->error = 'Only 1000 AWBs are allowed';
                return false;
            }

            foreach ($csvData as $row_key => $row) {
                if (!array_key_exists("AWB Number", $row)) {
                    $this->error = 'AWB Number header is missing.';
                    return false;
                }

                if (!$this->validateEdShipments($row)) {
                    $this->error = 'Row No. (' . ($row_key + 1) . '): ' . strip_tags($this->error);
                    return false;
                }
            }

            $save = array();
                
            $this->CI->load->library('courier_lib');

            $csv = new Csv_lib();
            $csv->add_row(array('AWB Number', 'Message'));

            foreach ($csvData as $row2) {
                $awb_number = $row2['AWB Number'];
                $csv_row = array($awb_number);

                if (empty($awb_number)) {
                    $csv_row[] = 'Empty';
                    $csv->add_row($csv_row);
                    continue;
                }

                $shipment = $this->getByAWB($awb_number);

                if (empty($shipment) || empty($shipment->order_id) || empty($shipment->courier_id)) {
                    $csv_row[] = 'Not Found';
                    $csv->add_row($csv_row);
                    continue;
                }

                if (in_array($shipment->ship_status, ['cancelled','delivered','rto delivered'])) {
                    $csv_row[] = ucfirst($shipment->ship_status);
                    $csv->add_row($csv_row);
                    continue;
                }

                $courier = $this->CI->courier_lib->getByID($shipment->courier_id);

                if (empty($courier)) {
                    $csv_row[] = 'Not Found';
                    $csv->add_row($csv_row);
                    continue;
                }

                if (strtolower($courier->display_name) != 'bluedart') {
                    $csv_row[] = 'Invalid';
                    $csv->add_row($csv_row);
                    continue;
                }

                $ed_data = array();
                $ed_data['awb_number'] = $awb_number;
                $ed_data['courier_id'] = $shipment->courier_id;
                $ed_data['created'] = time();
                $ed_data['modified'] = time();

                // $save[] = $ed_data;

                $csv_row[] = 'Success';
                $csv->add_row($csv_row);

                $id = $this->insertEdShipment($ed_data);

                if($id)
                    do_action('ed_shipments.new', $id);
            }

            /*if(!empty($save)) {
                $this->bulkInsertEdShipment($save);
            }*/

            $csv->export_csv();
            return true;
        }
    }

    private function validateEdShipments($data)
    {
        $this->CI->form_validation->set_data($data);

        $this->CI->form_validation->set_message('required', '%s is required');
        $this->CI->form_validation->set_message('alpha_numeric_spaces', 'Only Characters, Numbers & Spaces are  allowed in %s');
        $this->CI->form_validation->set_message('alpha_dash', 'Only Characters, Numbers & Dash are  allowed in %s');

        $config = array(
            array(
                'field' => 'AWB Number',
                'label' => 'AWB Number',
                'rules' => 'trim|required|alpha_numeric|min_length[4]|max_length[20]',
            )
        );

        $this->CI->form_validation->set_rules($config);

        if ($this->CI->form_validation->run()) {
            return true;
        } else {
            $this->error = validation_errors();
            return false;
        }
    }
}