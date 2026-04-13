<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Easebuzz
{
    protected $CI;
    protected $easebuzz_key = false;
    protected $easebuzz_access_key = false;

    function __construct()
    {
        $this->CI = &get_instance();
        $this->easebuzz_key = $this->CI->config->item('easebuzz_key');
        $this->easebuzz_access_key = $this->CI->config->item('easebuzz_access_key');;
    }

    function validateWebhookData($data = false,)
    {
        if (!$data)
            return false;

        $payment=json_decode($data);
        $hash_seq=hash('sha512',"$this->easebuzz_access_key|$payment->status|||||||||||$payment->email|$payment->firstname|$payment->productinfo|$payment->amount|$payment->txnid|$this->easebuzz_key");
        $verified = hash_equals($payment->hash, $hash_seq);

        if (!$verified)
            return false;

        if (empty($payment->easepayid) || empty($payment->txnid))
            return false;

        $return = array(
            'gateway_id' => $payment->easepayid,
            'payment_id' => $payment->txnid,
            'amount' => round($payment->amount, 2)
        );
        return $return;
    }
}
