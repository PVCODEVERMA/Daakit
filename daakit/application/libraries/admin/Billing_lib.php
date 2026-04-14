<?php

defined('BASEPATH') or exit('No direct script access allowed');

use App\Lib\Logs\User as Log;

class Billing_lib extends MY_lib
{

    public function __construct()
    {
        parent::__construct();
    }

    function adjustWalletForm($user_id = false, $admin_id = false)
    {

        if (!$user_id)
            return false;

        $this->CI->load->library('form_validation');
        $config = array(
            array(
                'field' => 'txn_for',
                'label' => 'Adjustment For',
                'rules' => 'trim|required|in_list[cod,recharge,neft,others,shipment,lost,damaged,promotion,wallet_to_wallet_transfer,tds_refund,customer_refund]'
            ),
            array(
                'field' => 'txn_type',
                'label' => 'Adjustment Type',
                'rules' => 'trim|required|in_list[credit,debit]'
            ),
            array(
                'field' => 'ref_id',
                'label' => 'Reference No',
                'rules' => 'trim|numeric'
            ),
            array(
                'field' => 'amount',
                'label' => 'Amount',
                'rules' => 'trim|required|greater_than[0]'
            ),
            array(
                'field' => 'notes',
                'label' => 'Notes',
                'rules' => 'trim|required'
            ),
        );

        $this->CI->form_validation->set_rules($config);

        if (!$this->CI->form_validation->run()) {
            $this->error = validation_errors();
            return false;
        }

        //pr($this->CI->input->post());
        //die;
        $this->CI->load->library('wallet_lib');
        $wallet = new Wallet_lib();

        $wallet->setUserID($user_id);
        $wallet->setAmount($this->CI->input->post('amount'));
        $wallet->setTransactionType($this->CI->input->post('txn_type'));
        $wallet->setNotes($this->CI->input->post('notes'));
        $wallet->setTxnFor($this->CI->input->post('txn_for'));

        if ($this->CI->input->post('txn_for') == 'shipment')
            $wallet->setTxnRef($this->CI->input->post('txn_ref'));


        $wallet->setRefID($this->CI->input->post('ref_id'));

        $ref_id = $wallet->creditDebitWallet();

        $data = array(
            'wallet_history_id' => $ref_id,
            'user_id' => $admin_id
        );

        $this->CI->load->library('admin/wallet_adjustment_lib');

        $this->CI->load->wallet_adjustment_lib->create($data);

        // $log = new Log();
        // $log->update($admin_id, $user_id, 'Wallet balance ' . $this->CI->input->post('txn_type') . 'ed with Rs.' . $this->CI->input->post('amount'));

        return true;
    }

    private function validate_weight_file_data($data)
    {
        $this->CI->form_validation->set_data($data);

        $this->CI->form_validation->set_message('required', '%s is required');
        $this->CI->form_validation->set_message('alpha_numeric_spaces', 'Only Characters, Numbers & Spaces are  allowed in %s');

        $config = array(
            array(
                'field' => 'AWB Number',
                'label' => 'AWB Number',
                'rules' => 'trim|required|alpha_numeric|min_length[4]|max_length[20]',
            ),
            array(
                'field' => 'Weight',
                'label' => 'Weight',
                'rules' => 'trim|required|integer',
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

    private function validate_courier_billing_file_data($data)
    {
        $this->CI->form_validation->set_data($data);

        $this->CI->form_validation->set_message('required', '%s is required');
        $this->CI->form_validation->set_message('alpha_numeric_spaces', 'Only Characters, Numbers & Spaces are  allowed in %s');

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

        $this->CI->form_validation->set_rules($config);

        if ($this->CI->form_validation->run()) {
            return true;
        } else {
            $this->error = validation_errors();
            return false;
        }
    }

    function importCourierBilling()
    {
        $this->CI->load->library('form_validation');
        $config = array(
            array(
                'field' => 'importFile',
                'label' => 'Import File',
                'rules' => 'callback_file_check'
            ),
        );

        $this->CI->form_validation->set_rules($config);

        if (!$this->CI->form_validation->run()) {
            $this->error = validation_errors();
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


            $update = array();
            foreach ($csvData as $row_key => $row) {
                if (!$this->validate_courier_billing_file_data($row)) {
                    $this->error = 'Row no. ' . ($row_key + 1) . $this->error;
                    return false;
                }
            }

            $apply_filters = array(
                'shipment_ids' => array_column($csvData, 'AWB Number'),
            );

            $this->CI->load->library('admin/shipping_lib');


            $existing = $this->CI->shipping_lib->getShipmentsforWeightRec($apply_filters);

            if (empty($existing)) {
                $this->error = 'No records found';
                return false;
            }


            $db_records = array();
            foreach ($existing as $exist) {
                $db_records[$exist->awb_number] = $exist;
            }

            //check if amount is same

            $extra_records = array();
            foreach ($csvData as $csv_key => $csv_row) {
                if (!array_key_exists($csv_row['AWB Number'], $db_records))
                    $extra_records[] = $csv_row['AWB Number'];
                elseif ($db_records[$csv_row['AWB Number']]->courier_billed == '0')
                    $update[] = array(
                        'awb_number' => trim($csv_row['AWB Number']),
                        'courier_billed' => trim($csv_row['Amount'])
                    );
            }
            if (!empty($extra_records)) {
                $this->error = 'Upload Failed. Plase note doen these AWB No Does not exists<br/> ' . implode('<br/>', $extra_records);
                return false;
            }



            $this->CI->shipping_lib->updateBilledAmountbyAWB($update);


            return true;
        }
    }

    function importWeightFile()
    {
        $this->CI->load->library('form_validation');
        $config = array(
            array(
                'field' => 'importFile',
                'label' => 'Import File',
                'rules' => 'callback_file_check'
            ),
        );

        $this->CI->form_validation->set_rules($config);

        if (!$this->CI->form_validation->run()) {
            $this->error = validation_errors();
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

            foreach ($csvData as $row_key => $row) {
                if (!$this->validate_weight_file_data($row)) {
                    $this->error = 'Row no. ' . ($row_key + 1) . $this->error;
                    return false;
                }
            }


            $apply_filters = array(
                'shipment_ids' => array_column($csvData, 'AWB Number'),
            );

            $this->CI->load->library('admin/shipping_lib');


            $existing = $this->CI->shipping_lib->getShipmentsforWeightRec($apply_filters);

            if (empty($existing)) {
                $this->error = 'No records found';
                return false;
            }


            $db_records = array();
            foreach ($existing as $exist) {
                $db_records[$exist->awb_number] = $exist;
            }

            //check if amount is same
            foreach ($csvData as $csv_key => $csv_row) {
                if (array_key_exists($csv_row['AWB Number'], $db_records))
                    $db_records[$csv_row['AWB Number']]->uploaded_weight = $csv_row['Weight'];
            }


            return $db_records;
        }
    }

    function freightReversalUpload()
    {
        //pr($_POST);
        $this->CI->load->library('form_validation');
        $config = array(
            array(
                'field' => 'importFile',
                'label' => 'Import File',
                'rules' => 'callback_file_check'
            ),
            array(
                'field' => 'type',
                'label' => 'Select Type',
                'rules' => 'trim|required|in_list[all,forward,rto]'
            ),
        );

        $this->CI->form_validation->set_rules($config);

        if (!$this->CI->form_validation->run()) {
            $this->error = validation_errors();
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

            foreach ($csvData as $row_key => $row) {
                if (!$this->validate_freight_reversal_file_data($row)) {
                    $this->error = 'Row no. ' . ($row_key + 1) . $this->error;
                    return false;
                }
            }

            $this->CI->load->library('wallet_lib');
            $this->CI->load->library('shipping_lib');

            $filename = 'Fright_reverse_' . time() . '.csv';
            header("Content-Description: File Transfer");
            header("Content-Disposition: attachment; filename=$filename");
            header("Content-Type: application/csv; ");
            $file = fopen('php://output', 'w');
            $header = array(
                "Awb No",
                "Status",

            );
            fputcsv($file, $header);

            // $row=array();
            foreach ($csvData as $row2) {
                $awb_number = $row2['AWB Number'];

                $shipment = $this->CI->shipping_lib->getByAWB($awb_number);
                //pr($shipment); die;



                // || $shipment->all_freight_reversed == '1'  // "Failure : Invalid AWB No / Freight already Reversed",
                $update = array();

                // || $shipment->all_freight_reversed == '1'  // "Failure : Invalid AWB No / Freight already Reversed",

                if (empty($shipment)) {
                    $row11 = array(
                        $awb_number,
                        "Failure : Invalid AWB No.",
                    );

                    fputcsv($file, $row11);
                } else {
                    //continue;

                    $update['total_fees'] = '0';

                    // if($_POST['type']=='rto' &&  ($shipment->payment_type == 'COD' || $shipment->payment_type == 'prepaid'))
                    // {

                    //     $row1 = array(
                    //         $awb_number,
                    //         "Failure :This AWB No. is for COD or Prepaid",
                    //     );

                    //     fputcsv($file, $row1);

                    // }
                    // else if($_POST['type']=='forward' &&  $shipment->payment_type == 'reverse'){
                    //     $row2 = array(
                    //         $awb_number,
                    //         "Failure : This AWB No. is for Reverse",
                    //     );
                    //    fputcsv($file, $row2);
                    // }




                    if ($_POST['type'] == 'forward') {

                        if ($shipment->forward_fee_refund == '1') {
                            $row11 = array(
                                $awb_number,
                                "Failure : Freight charges already Reversed",
                            );

                            fputcsv($file, $row11);
                        } else {


                            //&&  ($shipment->payment_type == 'COD' || $shipment->payment_type == 'prepaid')
                            $row3 = array(
                                $shipment->awb_number,
                                "Success",
                            );


                            if (($shipment->courier_fees) > 0 && ($shipment->fees_refunded) == '0') {
                                $wallet = new Wallet_lib();
                                $wallet->setUserID($shipment->user_id);
                                $wallet->setAmount($shipment->courier_fees);
                                $wallet->setTransactionType('credit');
                                $wallet->setNotes('Freight Charges Reversal');
                                $wallet->setTxnFor('shipment');
                                $wallet->setRefID($shipment->id);
                                $wallet->setTxnRef('freight');
                                $wallet->creditDebitWallet();
                                $update['courier_fees'] = '0';
                            }

                            if (($shipment->cod_fees) > 0 && ($shipment->fees_refunded) == '0' && ($shipment->cod_reverse_amount) <= 0) {
                                $wallet = new Wallet_lib();
                                $wallet->setUserID($shipment->user_id);
                                $wallet->setAmount($shipment->cod_fees);
                                $wallet->setTransactionType('credit');
                                $wallet->setNotes('COD Charges Reversal');
                                $wallet->setTxnFor('shipment');
                                $wallet->setRefID($shipment->id);
                                $wallet->setTxnRef('cod');
                                $wallet->creditDebitWallet();
                                $update['cod_fees'] = '0';
                            }
                            
                            if ($shipment->extra_weight_charges > 0) {
                                $wallet = new Wallet_lib();
                                $wallet->setUserID($shipment->user_id);
                                $wallet->setAmount($shipment->extra_weight_charges);
                                $wallet->setTransactionType('credit');
                                $wallet->setNotes('Extra Weight Charges Reversal');
                                $wallet->setTxnFor('shipment');
                                $wallet->setRefID($shipment->id);
                                $wallet->setTxnRef('extra_weight');
                                $wallet->creditDebitWallet();
                                $update['extra_weight_charges'] = '0';
                            }


                            fputcsv($file, $row3);
                            $update['forward_fee_refund'] = '1';
                            // $update['all_freight_reversed'] = '1';
                            $this->CI->shipping_lib->update($shipment->id, $update);
                        }
                    } else if ($_POST['type'] == 'rto') {  //&&  $shipment->payment_type == 'reverse'
                        if (($shipment->rto_charges) <= 0 && ($shipment->rto_extra_weight_charges) <= 0 && ($shipment->rto_fee_refund) != 1) {
                            $row11 = array(
                                $awb_number,
                                "Failure :  RTO charges not found",
                            );

                            fputcsv($file, $row11);
                        } else if ($shipment->rto_fee_refund == '1') {
                            $row12 = array(
                                $awb_number,
                                "Failure : RTO charges already Reversed",
                            );

                            fputcsv($file, $row12);
                        } else {

                            $row4 = array(
                                $shipment->awb_number,
                                "Success"
                            );

                            if ($shipment->rto_charges > 0) {
                                $wallet = new Wallet_lib();
                                $wallet->setUserID($shipment->user_id);
                                $wallet->setAmount($shipment->rto_charges);
                                $wallet->setTransactionType('credit');
                                $wallet->setNotes('RTO Freight Charges Reversal');
                                $wallet->setTxnFor('shipment');
                                $wallet->setRefID($shipment->id);
                                $wallet->setTxnRef('rto_freight');
                                $wallet->creditDebitWallet();
                                $update['rto_charges'] = '0';
                            }

                            if ($shipment->rto_extra_weight_charges > 0) {
                                $wallet = new Wallet_lib();
                                $wallet->setUserID($shipment->user_id);
                                $wallet->setAmount($shipment->rto_extra_weight_charges);
                                $wallet->setTransactionType('credit');
                                $wallet->setNotes('RTO Extra Weight Charges Reversal');
                                $wallet->setTxnFor('shipment');
                                $wallet->setRefID($shipment->id);
                                $wallet->setTxnRef('rto_extra_weight');
                                $wallet->creditDebitWallet();
                                $update['rto_extra_weight_charges'] = '0';
                            }

                            // if ($shipment->cod_reverse_amount > 0) {
                            //     $wallet = new Wallet_lib();
                            //     $wallet->setUserID($shipment->user_id);
                            //     $wallet->setAmount($shipment->cod_reverse_amount);
                            //     $wallet->setTransactionType('debit');
                            //     $wallet->setNotes('COD Charges');
                            //     $wallet->setTxnFor('shipment');
                            //     $wallet->setRefID($shipment->id);
                            //     $wallet->setTxnRef('cod');
                            //     $wallet->creditDebitWallet();
                            //     $update['cod_reverse_amount'] = '0';
                            // }
                            fputcsv($file, $row4);
                            $update['rto_fee_refund'] = '1';
                            // $update['all_freight_reversed'] = '1';
                            $this->CI->shipping_lib->update($shipment->id, $update);
                        }
                    } else if ($_POST['type'] == 'all') {
                        
                        if (($shipment->rto_fee_refund) == '1' && ($shipment->all_freight_reversed) == '1' && ($shipment->forward_fee_refund) == '1') {
                            $row12 = array(
                                $awb_number,
                                "Failure : All charges already Reversed",
                            );

                            fputcsv($file, $row12);
                        } else {
                            $row5 = array(
                                $awb_number,
                                "Success",
                            );

                            if (($shipment->courier_fees) > 0 && ($shipment->fees_refunded) == '0') {
                                $wallet = new Wallet_lib();
                                $wallet->setUserID($shipment->user_id);
                                $wallet->setAmount($shipment->courier_fees);
                                $wallet->setTransactionType('credit');
                                $wallet->setNotes('Freight Charges Reversal');
                                $wallet->setTxnFor('shipment');
                                $wallet->setRefID($shipment->id);
                                $wallet->setTxnRef('freight');
                                $wallet->creditDebitWallet();
                                $update['courier_fees'] = '0';
                            }
                            
                            // if ($shipment->cod_reverse_amount > 0) {
                            //     $wallet = new Wallet_lib();
                            //     $wallet->setUserID($shipment->user_id);
                            //     $wallet->setAmount($shipment->cod_reverse_amount);
                            //     $wallet->setTransactionType('debit');
                            //     $wallet->setNotes('RTO-COD Amount Reversal');
                            //     $wallet->setTxnFor('shipment');
                            //     $wallet->setRefID($shipment->id);
                            //     $wallet->setTxnRef('cod');
                            //     $wallet->creditDebitWallet();
                            //     $update['cod_reverse_amount'] = '0';
                            // }

                            if (($shipment->cod_fees) > 0 && ($shipment->fees_refunded) == '0' && ($shipment->cod_reverse_amount) <= 0) {
                                $wallet = new Wallet_lib();
                                $wallet->setUserID($shipment->user_id);
                                $wallet->setAmount($shipment->cod_fees);
                                $wallet->setTransactionType('credit');
                                $wallet->setNotes('COD Charges Reversal');
                                $wallet->setTxnFor('shipment');
                                $wallet->setRefID($shipment->id);
                                $wallet->setTxnRef('cod');
                                $wallet->creditDebitWallet();
                                $update['cod_fees'] = '0';
                            }

                            if ($shipment->rto_charges > 0) {
                                $wallet = new Wallet_lib();
                                $wallet->setUserID($shipment->user_id);
                                $wallet->setAmount($shipment->rto_charges);
                                $wallet->setTransactionType('credit');
                                $wallet->setNotes('RTO Freight Charges Reversal');
                                $wallet->setTxnFor('shipment');
                                $wallet->setRefID($shipment->id);
                                $wallet->setTxnRef('rto_freight');
                                $wallet->creditDebitWallet();
                                $update['rto_charges'] = '0';
                            }

                            if ($shipment->extra_weight_charges > 0) {
                                $wallet = new Wallet_lib();
                                $wallet->setUserID($shipment->user_id);
                                $wallet->setAmount($shipment->extra_weight_charges);
                                $wallet->setTransactionType('credit');
                                $wallet->setNotes('Extra Weight Charges Reversal');
                                $wallet->setTxnFor('shipment');
                                $wallet->setRefID($shipment->id);
                                $wallet->setTxnRef('extra_weight');
                                $wallet->creditDebitWallet();
                                $update['extra_weight_charges'] = '0';
                            }

                            if ($shipment->rto_extra_weight_charges > 0) {
                                $wallet = new Wallet_lib();
                                $wallet->setUserID($shipment->user_id);
                                $wallet->setAmount($shipment->rto_extra_weight_charges);
                                $wallet->setTransactionType('credit');
                                $wallet->setNotes('RTO Extra Weight Charges Reversal');
                                $wallet->setTxnFor('shipment');
                                $wallet->setRefID($shipment->id);
                                $wallet->setTxnRef('rto_extra_weight');
                                $wallet->creditDebitWallet();
                                $update['rto_extra_weight_charges'] = '0';
                            }
                            fputcsv($file, $row5);
                            $update['all_freight_reversed'] = '1';
                            $update['forward_fee_refund'] = '1';
                            $update['rto_fee_refund'] = '1';
                            $this->CI->shipping_lib->update($shipment->id, $update);
                        }
                    }
                }
            }

            fclose($file);

            return true;
        }
    }

    private function validate_freight_reversal_file_data($data)
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
