<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Payment_lib extends MY_lib
{
    private static $payment_id_for_app = '';
    public function __construct()
    {
        parent::__construct();
        $this->CI->load->model('payment_model');
    }

    public function __call($method, $arguments)
    {
        if (!method_exists($this->CI->payment_model, $method)) {
            throw new Exception('Undefined method payment_model::' . $method . '() called');
        }

        return call_user_func_array([$this->CI->payment_model, $method], $arguments);
    }

    function createUserPayment($user_id = false, $amount = '0',$payment_mode='')
    {
        if (!$user_id)
            return false;
        $save = array(
            'user_id' => $user_id,
            'amount' => $amount,
            'paid' => '0',
            'select_mode'=>$payment_mode,
        );

        return $this->insert($save);
    }

    function createUserPaymenthdfc($user_id = false, $amount = '0',$payment_mode='')
    {
        if (!$user_id)
            return false;
           
        $save = array(
            'user_id' => $user_id,
            'amount' => $amount,
            'paid' => '0',
            'select_mode'=>$payment_mode,
        );

        return $this->insert($save);
    }

    function webhookCapturedPayment($gateway = false, $data = false, $headers = false)
    {
        $payment_data = array();
        switch ($gateway) {
            case 'easebuzz':
                $this->CI->load->library('easebuzz');
                $payment_data = $this->CI->easebuzz->validateWebhookData($data);
                break;
            default:
                $this->error = 'Invalid Gateway';
                return false;
        }
        if (empty($payment_data['payment_id']) || empty($payment_data['gateway_id']))
            return false;

        return $this->markAsPaymentreceived($payment_data['payment_id'], $payment_data['gateway_id'], $payment_data['amount'], $gateway);
    }

    
    function webhookCapturedhdfcPayment($gateway = false, $data = false, $headers = false)
    {
               $payment_data = array();
     
                $this->CI->load->library('razorpay');
                $payment_data = $this->CI->razorpay->validatehdfcWebhookData($data, $headers);
              
        if (empty($payment_data['payment_id']) || empty($payment_data['gateway_id']))
            return false;

           // return $this->markAsPaymentreceived_hdfc($payment_data['payment_id'], $payment_data['gateway_id'], $payment_data['amount'], 'hdfc_razorpay');
            return $this->markAsPaymentreceived($payment_data['payment_id'], $payment_data['gateway_id'], $payment_data['amount'], 'hdfc_razorpay');
     
    }

    function set_payment_id($payment_id)
    {
        self::$payment_id_for_app = $payment_id;
    }
    function getPaymentId()
    {
        return self::$payment_id_for_app;
    }
    function paymentResponse($gateway = false)
    {
        if (!$gateway) {
            $this->error = 'Invalid Gateway';
            return false;
        }

        $payment_data = array();
        switch (strtolower($gateway)) {
            case 'razorpay':
                $payment_data = $this->razorpay_response();
                break;
            case 'razorpay_app':
                $payment_data = $this->razorpay_response_api();
                break;
            case 'paytm':
                $payment_data = $this->paytm_response_api();
                break;
            default:
                $this->error = 'Invalid Gateway';
                return false;
        }

        if (empty($payment_data))
            return false;

        $payment_id = $payment_data['payment_id'];
        $amount = $payment_data['amount'];
        $gateway_id = $payment_data['gateway_id'];

        if (empty($payment_id) || empty($amount) || !$gateway_id) {
            $this->error = 'Incomplete payment info';
            return false;
        }

        if ($gateway == 'razorpay_app') {
            $gateway = 'razorpay';
        }

        return $this->markAsPaymentreceived($payment_id, $gateway_id, $amount, $gateway);
    }

    function markAsPaymentreceived($payment_id = false, $gateway_id = false, $amount = false, $gateway = false)
    {
       
        if (!$payment_id || !$gateway_id || !$amount)
            return false;
        $payment = $this->getbyID($payment_id);
        if (empty($payment)) {
            $this->error = 'No payment found';
            return false;
        }
      
        if ($payment->paid == '1') {
            $this->error = 'Payment already processed';
            return false;
        }

         if (round($payment->amount, 2) != round($amount, 2)) {  
            $this->error = 'Payment amount mismatch';
            return false;
        }

        //mark as paid
        $this->markAsPaid($payment_id, $gateway_id, $gateway);

        //do_action('payment.success', $payment_id);

        do_action('user.wallet_recharged', $payment->user_id);

        $amount = $payment->amount;

        $this->CI->load->library('admin/coupon_lib');
      
        //all oky..
        // not save the payment and credit the wallet
        $this->CI->load->library('wallet_lib');

        $wallet = new Wallet_lib(array('user_id' => $payment->user_id));
        $wallet->setAmount($amount);  
        $wallet->setTransactionType('credit');
        $wallet->setNotes('Credit Applied for ' . ucwords($gateway) . ' Recharge. Payment Ref#' . $payment_id);
        $wallet->setTxnFor('recharge');
        $wallet->setTxnRef(strtolower($gateway));
        $wallet->setRefID($payment_id);
        $wallet->creditDebitWallet();

        $getcoupon = $this->CI->coupon_lib->getCouponApplied($payment->user_id, $payment_id);  //check coupon apply or not 
        if ($getcoupon) {
            $coupon = $this->CI->coupon_lib->getByID($getcoupon->coupon_id);
            $save_udpate = array(
                'coupon_used' => $coupon->coupon_used + 1
            );
            $amount = round($getcoupon->discount_amount, 2); // add discount in the wallet table
            $this->CI->coupon_lib->apply_coupon_update($getcoupon->id, array('coupon_status' => 1)); // finaly update coupons_applied
            $this->CI->coupon_lib->update($coupon->id, $save_udpate);  // udpate coupon table for coupon_used  
          if($amount){
              
            $wallet = new Wallet_lib(array('user_id' => $payment->user_id));
            $wallet->setAmount($amount);  
            $wallet->setTransactionType('credit');
            $wallet->setNotes('Cashback against coupon '.strtoupper($coupon->coupon_code).'.  Payment Ref#' . $payment_id);
            $wallet->setTxnFor('promotion');
            //$wallet->setTxnRef(strtolower('promotion'));
            $wallet->setRefID($payment_id);
            $wallet->creditDebitWallet();

          }
        }
        return true;
    }

    private function razorpay_response()
    {
        $this->CI->load->library('razorpay');
        $gateway_id = $this->CI->input->post('razorpay_payment_id');
        $payment_data = $this->CI->razorpay->validateCapturePayment($gateway_id);

        if (!$payment_data) {
            $this->error = 'Unauthorized Payment';
            return false;
        }

        return $payment_data;
    }
    private function razorpay_response_api()
    {
        $this->CI->load->library('razorpay');
        $gateway_id = $this->getPaymentId();
        if ($gateway_id == false) {
            $this->error = 'Something went wrong!! Please try again';
            return false;
        }
        $payment_data = $this->CI->razorpay->validateCapturePayment($gateway_id);

        if (!$payment_data) {
            $this->error = 'Unauthorized Payment';
            return false;
        }

        return $payment_data;
    }

    function updateUserPayment($paymentid = false, $amount = '0')
    {
        if (empty($amount) || empty($paymentid))
            return false;

        $save = array(
            'amount' => $amount
        );
        return $this->update($paymentid, $save);
    }


    
}
