<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Remittance_lib extends MY_lib
{

    public function __construct()
    {
        parent::__construct();

        $this->CI->load->model('admin/remittance_model');
    }

    public function __call($method, $arguments)
    {
        if (!method_exists($this->CI->remittance_model, $method)) {
            throw new Exception('Undefined method remittance_model::' . $method . '() called');
        }

        return call_user_func_array([$this->CI->remittance_model, $method], $arguments);
    }

    function sendRemittanceEmail($remittance_id = false)
    {
        if (!$remittance_id)
            return false;

        $remittance = $this->getByID($remittance_id);

        if (empty($remittance))
            return false;

        $this->CI->load->library('user_lib');
        $user = $this->CI->user_lib->getByID($remittance->user_id);

        $this->CI->load->library('email_lib');
        $email = new Email_lib();

        $email->to($user->email);

        $email->subject("deltagloabal COD Remittance Dt-" . date('d-m-Y', $remittance->payment_date));
        $email->message($this->CI->load->view('emails/remittance_paid', array('remittance' => $remittance, 'user' => $user), true));
        $email->send();

        return true;
    }

    function createRemittanceByUserID($user_id = false, $created_by = false)
    {
        if (!$user_id)
            return false;
        //get total unpaid amount for this user

        $this->CI->load->library('admin/user_lib');

        $user = $this->CI->user_lib->getByID($user_id);

        $this->CI->load->library('admin/shipping_lib', null, 'admin_shipping_lib');

        $shipments = $this->CI->admin_shipping_lib->remittanceCycleAWbs($user_id);

        if (empty($shipments))
            return false;


        $shipment_list = array_column((array) $shipments, 'id');

        $total_shipment_amount = round(array_sum(array_column((array) $shipments, 'order_total_amount')), 2);

        if ($user->remittance_on_hold_amount > 0) {
            $total_remittance_due = $this->CI->admin_shipping_lib->calculateTotalPayableRemittance($user_id);

            $pending_remittance = round($total_remittance_due - $total_shipment_amount);

            if ($pending_remittance < $user->remittance_on_hold_amount) {
                $total_shipment_amount = 0;
                //create custom remittance for this
                //regenerate shipment list again
                array_multisort(array_column($shipments, 'delivered_time'), SORT_ASC, $shipments);

                //how much remittance can be processed
                $new_remittance_to_process = round($total_remittance_due - $user->remittance_on_hold_amount);

                $new_remittance_shipments = array();
                foreach ($shipments as $shipment) {
                    if ($total_shipment_amount <= $new_remittance_to_process) {
                        $total_shipment_amount += $shipment->order_total_amount;
                        $new_remittance_shipments[] = $shipment;
                    }
                }

                if (empty($new_remittance_shipments) || $new_remittance_to_process <= 100)
                    return false;

                $shipment_list = array_column((array) $new_remittance_shipments, 'id');
                $total_shipment_amount = round(array_sum(array_column((array) $new_remittance_shipments, 'order_total_amount')), 2);
            }
        }


        $save = array(
            'user_id' => $user_id,
            'amount' => $total_shipment_amount,
            'paid' => '0',
            'shipping_ids' => implode(',', $shipment_list),
            'created_by' => $created_by
        );
        $remittance_id = $this->createRemittance($save);

        //update all orders with this menifest id for this order

        $this->CI->admin_shipping_lib->updateRemittanceID($shipment_list, $remittance_id);
        return $remittance_id;
    }

    function createRemittanceByShipmentID($shipment_ids = array(), $is_wallet_adjustment = false, $seller_created = false, $created_by = false)
    {

        if (empty($shipment_ids))
            return false;



        //get shipments for these ids
        $this->CI->load->library('admin/shipping_lib', '', 'admin_shipping_lib');

        $shipments = $this->CI->admin_shipping_lib->remittanceShipmentList($shipment_ids);



        if (empty($shipments))
            return false;

        $pay_array = array();
        foreach ($shipments as $shipment) {
            $pay_array[$shipment->user_id][] = $shipment;
        }

        if (empty($pay_array))
            return false;
        $this->CI->load->library('admin/user_lib');

        foreach ($pay_array as $pa_key => $pa) {
            $shipment_list = array_column((array) $pa, 'id');

            $total_shipment_amount = round(array_sum(array_column((array) $pa, 'order_total_amount')), 2);

            $user = $this->CI->user_lib->getByID($pa_key);

            if ($user->remittance_on_hold_amount > 0) {
                $total_remittance_due = $this->CI->admin_shipping_lib->calculateTotalPayableRemittance($pa_key);

                $pending_remittance = round($total_remittance_due - $total_shipment_amount);

                if ($pending_remittance < $user->remittance_on_hold_amount) {
                    $total_shipment_amount = 0;
                    //create custom remittance for this
                    //regenerate shipment list again
                    array_multisort(array_column($pa, 'delivered_time'), SORT_ASC, $pa);

                    //how much remittance can be processed
                    $new_remittance_to_process = round($total_remittance_due - $user->remittance_on_hold_amount);

                    $new_remittance_shipments = array();
                    foreach ($pa as $ps) {
                        if ($total_shipment_amount <= $new_remittance_to_process) {
                            $total_shipment_amount += $ps->order_total_amount;
                            $new_remittance_shipments[] = $ps;
                        }
                    }

                    if (empty($new_remittance_shipments) || $new_remittance_to_process <= 100)
                        return false;

                    $shipment_list = array_column((array) $new_remittance_shipments, 'id');
                    $total_shipment_amount = round(array_sum(array_column((array) $new_remittance_shipments, 'order_total_amount')), 2);
                }
            }

            $save = array(
                'user_id' => $pa_key,
                'amount' => $total_shipment_amount,
                'paid' => '0',
                'shipping_ids' => implode(',', $shipment_list),
                'seller_created' => ($seller_created) ? '1' : '0',
                'created_by' => $created_by
            );

            $remittance_id = $this->createRemittance($save);

            //update all orders with this menifest id for this order

            $this->CI->admin_shipping_lib->updateRemittanceID($shipment_list, $remittance_id);

            if ($is_wallet_adjustment) {
                $remittance = $this->getByID($remittance_id);
                $this->updateRemittanceUTR($remittance_id, 'Wallet Adjustment', 0, $remittance->amount);
            }
        }
        return true;
    }

    function updateRemittanceUTR($remittance_id = false, $utr_number = false, $remittance_amount = 0, $freight_deductions = 0, $convenience_fee = 0)
    {
        if (!$remittance_id || !$utr_number)
            return false;
        $update = array(
            'utr_number' => $utr_number,
            'paid' => '1',
            'payment_date' => time(),
            'remittance_amount' => $remittance_amount,
            'freight_deductions' => $freight_deductions,
            'convenience_fee' => $convenience_fee
        );
        $remittance = $this->getByID($remittance_id);
        $this->update($remittance_id, $update);
        if ($freight_deductions > 0) {
            $this->CI->load->library('wallet_lib');
            $wallet = new Wallet_lib();
            $wallet->setUserID($remittance->user_id);
            $wallet->setAmount($freight_deductions);
            $wallet->setTransactionType('credit');
            $wallet->setNotes('Frieght deductions from COD remittance');
            $wallet->setTxnFor('cod');
            $wallet->setRefID($remittance_id);
            $wallet->creditDebitWallet();
        }
        do_action('remittance.paid', $remittance_id);
        return true;
    }

    function createRemittanceByAwbNumber($paidAwbNos=[],$unpaidAwbNos=[],$created_by = false)
    {
        if (empty($paidAwbNos))
           return false;
        $paid_awb_nos=array_keys($paidAwbNos);
        //get shipments for these ids
        $this->CI->load->library('admin/shipping_lib', '', 'admin_shipping_lib');
        $this->CI->load->library('admin/user_lib');
        $messageArr=[];
        $shipments = $this->CI->admin_shipping_lib->remittanceShipmentListByAWB($paid_awb_nos);
        //if (empty($shipments))
           //return false;

        $pay_array = array();
        $invalidAwbs = array();
        foreach ($shipments as $shipment) {
            $invalidAwbs[]=$shipment->awb_number;
            if(isset($paidAwbNos[$shipment->awb_number]) && $shipment->order_total_amount < $paidAwbNos[$shipment->awb_number][0])
            {
                $messageArr[]=[$shipment->awb_number,$paidAwbNos[$shipment->awb_number][0],'','Cod amount is exceed the total order amount.'];
            }
            else if ($shipment->remittance_id!='0')
            {
                $messageArr[]=[$shipment->awb_number,$paidAwbNos[$shipment->awb_number][0],'','Already Paid'];
            }
            else if ($shipment->ship_status!='delivered')
            {
                $messageArr[]=[$shipment->awb_number,$paidAwbNos[$shipment->awb_number][0],'','Status of this awb number is not delivered'];
            }
            else if(!array_key_exists($shipment->awb_number,$paidAwbNos))
            {
                $messageArr[]=[$shipment->awb_number,$paidAwbNos[$shipment->awb_number][0],'','Invalid Awb Number'];
            }
            else
            {
                if($shipment->ops_verify=='1')
                    $pay_array[$shipment->user_id][] = $shipment;
                else
                    $messageArr[]=[$shipment->awb_number,$paidAwbNos[$shipment->awb_number][0],'','Unverified Awb'];
            }
        }
        foreach($paidAwbNos as $awbKey=>$val)
        {
            if(!in_array($awbKey,$invalidAwbs)){
                $messageArr[]=[$awbKey,$val[0],'','Invalid Awb Number'];
            }
        }
       // pr($messageArr,1);
        $this->CI->load->library('admin/user_lib');
        if (!empty($pay_array))
        {
            $negative_awb_nos=array_keys($unpaidAwbNos);
            $negativeShipments = $this->CI->admin_shipping_lib->remittanceShipmentListByAWB($negative_awb_nos);
            $neg_pay_array=[];
            if(count($negativeShipments)>0){
                foreach($negativeShipments as $ships){
                    $neg_pay_array[$ships->user_id][$ships->awb_number]= $unpaidAwbNos[$ships->awb_number];
                }
            }
            $used_neg_awbs=[];
            foreach ($pay_array as $pa_key => $pa) {
                $shipment_list = array_column((array) $pa, 'id');
                $awb_list = array_column((array) $pa, 'awb_number');
                //$total_shipment_amount = round(array_sum(array_column((array) $pa, 'order_total_amount')), 2);
                $total_shipment_amount = 0;
                $saveRemtDetails=[];
                $used_awbs=[];
                foreach($awb_list as $awb_no)
                {
                    if(isset($paidAwbNos[$awb_no]))
                    {
                        if(isset($neg_pay_array[$pa_key])) // Checking for negative entries
                        {
                            //sort($neg_pay_array[$pa_key][$awb_no]);
                            $totalRemainAmt=0;
                            $balanceAmt=0;
                            $amount=$paidAwbNos[$awb_no][0];
                            foreach($neg_pay_array[$pa_key] as $awb_key=>$negAmt)
                            {
                                $negAmts=$negAmt[0];
                                $used_neg_awbs[]=$awb_key;
                                if($amount < abs($negAmts)){
                                    $total_shipment_amount+=(int)$amount;
                                    $messageArr[]=array($awb_key,$negAmts,'Amount is exceed total remaining amount.');
                                }
                                else{
                                    $totalRemainAmt=(int)$amount -abs($negAmts);
                                    $total_shipment_amount+=$totalRemainAmt;
                                    $saveRemtDetails[]= array(
                                        'user_id' => $pa_key,
                                        'amount' => $negAmts,
                                        'awb_number' => $awb_key,
                                        'created_by' => $created_by
                                    );
                                }
                            }
                            $saveRemtDetails[]= array(
                                'user_id' => $pa_key,
                                'amount' => $amount,
                                'awb_number' => $awb_no,
                                'created_by' => $created_by
                            );
                        }
                        else
                        {
                            $total_shipment_amount+=$paidAwbNos[$awb_no][0];
                            $saveRemtDetails[]= array(
                                'user_id' => $pa_key,
                                'amount' => $paidAwbNos[$awb_no][0],
                                'awb_number' => $awb_no,
                                'created_by' => $created_by
                            );
                        }
                    }
                }
                $save = array(
                    'user_id' => $pa_key,
                    'amount' => $total_shipment_amount,
                    'paid' => '0',
                    'shipping_ids' => implode(',', $shipment_list),
                    'seller_created' => ($seller_created) ? '1' : '0',
                    'created_by' => $created_by
                );
                //pr($saveRemtDetails,1);
                $remittance_id = $this->createRemittance($save);
                if(count($awb_list)>0)
                {
                    foreach($awb_list as $awb)
                    {
                        if(isset($paidAwbNos[$awb]))
                        {
                            $used_awbs[]=$awb;
                            $messageArr[]=[$awb,$paidAwbNos[$awb][0],$remittance_id,'Success'];
                        }
                    }
                }
                if(count($saveRemtDetails)>0)
                {
                    foreach($saveRemtDetails as $remDetails)
                    {
                        $remDetails['remittance_id']=$remittance_id;
                        $remittance_det_id = $this->createRemittanceDetail($remDetails);
                        if(!in_array($remDetails['awb_number'],$used_awbs))
                        {
                            $messageArr[]=array($remDetails['awb_number'],$remDetails['amount'],$remDetails['remittance_id'],'Success');
                        }
                    }
                }
                //pr($messageArr);
                $this->CI->admin_shipping_lib->updateRemittanceID($shipment_list, $remittance_id);
            }
        }
        if(count($unpaidAwbNos)>0)
        {
            foreach($unpaidAwbNos as $key=>$unpaidAwbs)
            {
                if(!in_array($key,$used_neg_awbs))
                {
                    foreach($unpaidAwbs as $val){
                        $messageArr[]=[$key,$val,'','No data found'];
                    }
                }
            }
        }
        //pr($messageArr,1);
        return $messageArr;
    }

    function verifyOpsByAwbNumber($awb_no= false,$status='', $created_by = false)
    {
        if (empty($awb_no))
            return 'Awb number can not be blank.';

        //get shipments for these ids
        $this->CI->load->library('admin/shipping_lib', '', 'admin_shipping_lib');
        $shipments_detail=$this->CI->admin_shipping_lib->getByAWB($awb_no);
        if (empty($shipments_detail))
            return 'Invalid Awb Number';

        if ($shipments_detail->ship_status!='delivered')
            return 'Status of this awb number is not delivered';

        if ($shipments_detail->remittance_id > '0')
            return 'Remittance already created.';

        if(strtolower($status)=='verified')
            $ops_status='1';
        else if(strtolower($status)=='unverified')
            $ops_status='2';
        else
            $ops_status='0';

        $save = array(
            'shipping_id' =>$shipments_detail->id,
            'courier_id' =>$shipments_detail->courier_id,
            'awb_number' =>$shipments_detail->awb_number,
            'ops_verify' => $ops_status,
            'created_by' => $created_by
        );
        $verifyStatus = $this->createOpsAwbVerify($save);
        if($ops_status=='0')
            return  'Invalid Status';
        else 
            return ($verifyStatus=='0') ? 'Updated successfully' : 'Inserted successfully';
    }

    function getShippingDetails($remittance_id = false)
    {

        if (!$remittance_id)
            return false;

        $remittance = $this->getById($remittance_id);

        if (empty($remittance))
            return false;

        $shipping_ids = explode(',', $remittance->shipping_ids);
        if (empty($shipping_ids))
            return false;

        $this->CI->load->library('admin/shipping_lib', null, 'admin_shipping_lib');
        $shipments = $this->CI->admin_shipping_lib->shipmentDetailsBulkIds(10000, 0, array('shipment_ids' => $shipping_ids));
        return $shipments;
    }

    function uploadReceipt()
    {

        $this->CI->load->library('form_validation');
        $config = array(
            array(
                'field' => 'courier_id',
                'label' => 'Courier',
                'rules' => 'trim|required|numeric',
            ),
            array(
                'field' => 'date',
                'label' => 'Date',
                'rules' => 'trim|required',
            ),
            array(
                'field' => 'amount',
                'label' => 'amount',
                'rules' => 'trim|required|numeric',
            ),
            array(
                'field' => 'utr_number',
                'label' => 'UTR Number',
                'rules' => 'trim|required',
            ),
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


        //upoad config
        $upload_config['upload_path'] = 'assets/cod_receipts/';
        $upload_config['allowed_types'] = 'csv';
        $upload_config['file_name'] = time() . rand(1111, 9999);

        $this->CI->load->library('upload', $upload_config);

        if (!$this->CI->upload->do_upload('importFile')) {
            $this->error = $this->CI->upload->display_errors();
        }

        //upload successfull
        $file_info = $this->CI->upload->data();
        $this->CI->load->library('csvreader');

        $this->CI->load->library('admin/shipping_lib', null, 'admin_shipping_lib');

        $csvData = $this->CI->csvreader->parse_csv($_FILES['importFile']['tmp_name']);

        if (empty($csvData)) {
            $this->error = 'Blank CSV File';
            return false;
        }


        $awb_array = array();
        foreach ($csvData as $row_key => $row) {
            if (array_key_exists($row['AWB Number'], $awb_array)) {
                $this->error = 'Duplicate Records for AWB: ' . $row['AWB Number'];
                return false;
            }
            $awb_array[$row['AWB Number']] = $row['AWB Number'];
            if (!$this->validate_upload_data($row)) {
                $this->error = 'Row no. ' . ($row_key + 1) . $this->data['error'];
                return false;
            }
        }



        //check if awb are already paid
        $uploaded_courier_id = $this->CI->input->post('courier_id');
        //check if already exits
        $utr_exists = $this->CI->remittance_lib->codUploadHistoryByUTR($this->CI->input->post('courier_id'), $this->CI->input->post('utr_number'));
        if (!empty($utr_exists)) {
            $this->error = 'Records already exists for UTR Number: ' . $this->CI->input->post('utr_number');
        }

        $existing = $this->CI->admin_shipping_lib->checkReceiptsBulk(array_values($awb_array), $uploaded_courier_id);

        if (empty($existing)) {
            $this->error = 'No records found';
            return false;
        }


        $db_records = array();
        foreach ($existing as $exist) {
            $db_records[$exist->awb_number] = $exist;
        }


        $save = array(
            'courier_id' => $this->CI->input->post('courier_id'),
            'amount' => $this->CI->input->post('amount'),
            'utr_number' => $this->CI->input->post('utr_number'),
            'file_name' => $file_info['file_name'],
            'payment_date' => strtotime($this->CI->input->post('date')),
        );

        $receipt_id = $this->CI->remittance_lib->saveCODUpload($save);

        //update shipping table for the payment info
        $update = array();
        foreach ($csvData as $row_key => $row) {
            if (array_key_exists($row['AWB Number'], $db_records) && $db_records[$row['AWB Number']]->receipt_id <= 0) {
                $update[] = array(
                    'id' => $db_records[$row['AWB Number']]->id,
                    'receipt_id' => $receipt_id,
                    'receipt_amount' => $row['Amount'],
                );
            }
        }

        $this->CI->admin_shipping_lib->updateReceiptBYAwb($update);
        return true;
    }

    private function validate_upload_data($data)
    {
        $this->CI->form_validation->set_data($data);

        $this->CI->form_validation->set_message('required', '%s is required');
        $this->CI->form_validation->set_message('alpha_numeric', 'Only Characters & Numbers are allowed in %s');
        $this->CI->form_validation->set_message('alpha_numeric_spaces', 'Only Characters, Numbers & Spaces are  allowed in %s');

        $config = array(
            array(
                'field' => 'AWB Number',
                'label' => 'AWB Number',
                'rules' => 'trim|required|regex_match[/^[A-Za-z0-9\-]+$/]|min_length[4]|max_length[30]',
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
}
