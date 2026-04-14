<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Coupon_lib extends MY_lib
{

    public function __construct()
    {
        parent::__construct();
        $this->CI->load->model('admin/coupon_model');
    }

    public function __call($method, $arguments)
    {
        if (!method_exists($this->CI->coupon_model, $method)) {
            throw new Exception('Undefined method coupon_model::' . $method . '() called');
        }
        return call_user_func_array([$this->CI->coupon_model, $method], $arguments);
    }

    function isCouponCodeCheck($user_id = false, $amount = false, $coupon_code = false, $gateway='')
    {
        if (!$coupon_code && !$user_id)
            return false;

        $coupon = $this->isCouponCodeAvilable($coupon_code);

        if ($coupon) {

            $ischeck = true;
            $today_date = strtotime(date('m/d/Y H:i:s'));
            $start_date  = strtotime(trim($coupon->start_date) . ' 00:01:01');
            $end_date  = strtotime(trim($coupon->end_date) . ' 23:59:59');
            $allow_gateway = explode(',', $coupon->applied_for);
            if ($gateway!='' && !in_array($gateway,$allow_gateway)) {
                $this->error = "Invalid gateway";
                return false;
            }
            if ($amount < $coupon->minimum_amount_recharge) {
                $this->error = "Minimum Recharge Amount is Rs." . $coupon->minimum_amount_recharge;
                return false;
            }
            if ($today_date < $start_date) {
                $this->error = "Invalid coupon";
                return false;
            }
            if ($today_date > $end_date) {

               $this->error = "Invalid coupon";
                 return false;
            }

            if ($coupon->coupon_limit <= $coupon->coupon_used) {
                $this->error = "Promocode has been expired.";
                return false;
            }
            if ($coupon->user_type == 1) {
                $ischeck = $this->checkFirstRecharge($user_id); //; First recharge
            }
            if ($coupon->user_type == 2) {
                $ischeck = $this->checkSpecificUser($coupon, $user_id); //; Specific user
            }
            if (empty($ischeck)) {
                $this->error = $this->error;
                return false;
            }
            $check_applied = $this->isCouponCodeApplied($coupon->id, $user_id);
            if ($check_applied >= $coupon->user_wise_limit) {
                $this->error = "Promocode has been already applied.";
                return false;
            }

            return $coupon;
        }
        $this->error = "Invalid coupon code.";
        return false;
    }

    function checkFirstRecharge($user_id)
    {
        $this->CI->load->library('payment_lib');
        $recharge = $this->CI->payment_lib->countPaidUser($user_id);
        // pr($recharge);die();
        if ($recharge > 0) {
            $this->error = "Coupon codes are not available for this user.";
            return false;
        }
        return true;
    }
    function checkSpecificUser($coupon, $user_id)
    {
        $check_applied = $this->check_specific_seller($coupon->id, $user_id);
        if (empty($check_applied)) {
            $this->error = "Invalid coupon code.";
            return false;
        }

        return true;
    }

    function couponTrackingAmount($user_id = false, $amount = false, $coupon = array(), $payment_id = false)  //update table wallet
    {
        if (empty($coupon))
            return false;

        $return_discount_ammount = 0;

        switch ($coupon->discount_type) {

            case 'fixed': //;
                $return_discount_ammount = $this->fixed($coupon->discount_amount_upto);
                break;
            default: //discount
                $return_discount_ammount = $this->discount($amount, $coupon->discount_amount, $coupon->discount_amount_upto);

                // $inner_content = $this->rules();
        }

        $save = array(
            'coupon_id' => $coupon->id,
            'seller_id' => $user_id,
            'payment_id' => $payment_id,
            'payment_coupon_type' => $coupon->coupon_type,
            'coupon_user_type' => $coupon->user_type,
            'recharge_amount' => $amount,
            'discount_amount' => $return_discount_ammount
        );

        $check_applied = $this->getCouponAppliedID($user_id, $coupon->id);  // check tabel coupons_applied
        if (!empty($check_applied)) {
            $this->apply_coupon_update($check_applied->id, $save);   // update coupons_applied
        } else {
            $this->apply_coupon_create($save); // create coupons_applied
        }

        return $return_discount_ammount;
    }

    public function fixed($discount_amount_upto)
    {
        $return_discount_ammount = 0;
        if (!empty($discount_amount_upto)) {
            $return_discount_ammount = $discount_amount_upto;
        }
        return $return_discount_ammount;
    }


    public function discount($amount, $discout_percentage, $discount_amount_upto = false)
    {
        $return_discount_ammount = 0;
        if (!empty($discout_percentage)) {
             $return_discount_ammount = round(($discout_percentage / 100) * $amount);
        }
        if (!empty($discount_amount_upto)) {
            if ($return_discount_ammount > $discount_amount_upto) {
                $return_discount_ammount = $discount_amount_upto;
            }
        }
        return $return_discount_ammount;
    }
}
