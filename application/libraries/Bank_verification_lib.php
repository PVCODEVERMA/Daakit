<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Bank_verification_lib extends MY_lib
{
    public function __construct()
    {
        parent::__construct();
        $this->CI->load->model('profile_model');
        $this->CI->load->model('otp_model');
    }

    public function __call($method, $arguments)
    {
        if (!method_exists($this->CI->profile_model, $method)) {
            throw new Exception('Undefined method profile_model::' . $method . '() called');
        }
        return call_user_func_array([$this->CI->profile_model, $method], $arguments);
    }

    function isValidateBankAccount($account_holder, $account_number = null, $ifsc = null, $company_details = null)
    {
        if ($account_number == null) {
            return false;
        }

        $this->CI->load->library('razorpay');
        $return_val = $this->CI->razorpay->add_contact($account_holder, $account_number, $ifsc, $company_details);
        return $return_val;
    }

    function saveBankAccountNo($user_id = null, $cmpdata = array())
    {
        if ($user_id == null || empty($cmpdata)) {
            return false;
        }
        $this->update_companydetails($user_id, $cmpdata);
        return true;
    }
    function generate_otp()
    {
        $otp_val = rand(111111, 999999);

        $save_data["otp"] = $otp_val;
        $save_data["expired"] = time() + 600;
        return $save_data;
    }

    function send_sms($phone = null, $otp = null)
    {
        if ($phone == null || $otp == null) {
            return false;
        }

        $this->CI->load->library('sms');
        $sms = new sms();
        $this->data["otp"] = $otp;
        $isOTPsent =  $sms->send_sms($phone, 'otp', (object) $this->data);
        return $isOTPsent;
    }

    function saveBankVerification($userId, $bankRecord, $otp, $requested_employee)
    {
        $records = array(
            'user_id' => $userId,
            'otp' => $otp['otp'],
            'otp_expire' => $otp['expired'],
            'payout_id' => $bankRecord->payout_id,
            'fund_account_id' => $bankRecord->fund_account_id,
            'amount' => $bankRecord->amount,

            'utr_number' => $bankRecord->utr_number != null ? $bankRecord->utr_number : '',
            'status' => $bankRecord->status,
            "fees" =>  $bankRecord->fees,
            "tax" =>  $bankRecord->tax,

            "account_holder" => $bankRecord->account_holder,
            "bank_name" => $bankRecord->bank_name,
            "ifsc" => $bankRecord->ifsc,
            "account_number" => $bankRecord->account_number,
            'employee_id' => $requested_employee
        );

        $this->CI->load->model('bank_model');
        return $this->CI->bank_model->insert($records);
    }

    function matchOtp($user_id = null, $otp = null)
    {
        if (empty($user_id) || empty($otp)) {
            return false;
        }
        return ($this->CI->otp_model->getOtp($user_id, $otp));
    }
}
