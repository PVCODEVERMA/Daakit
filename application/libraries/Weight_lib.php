<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Weight_lib extends MY_lib
{
    protected $weight_record;
    protected $shipment;

    public function __construct()
    {
        parent::__construct();

        $this->CI->load->model('weight_model');
    }

    public function __call($method, $arguments)
    {
        if (!method_exists($this->CI->weight_model, $method)) {
            throw new Exception('Undefined method weight_model::' . $method . '() called');
        }

        return call_user_func_array([$this->CI->weight_model, $method], $arguments);
    }





    function accept_weight($id = false, $user_id = false, $auto_accept = false)
    {
        if (!$id) {
            $this->error = 'Invalid ID';
            return false;
        }

        $weight_record = $this->getByID($id);


        if (empty($weight_record)) {
            $this->error = 'No record found';
            return false;
        }



        if ($user_id && $weight_record->user_id != $user_id) {
            $this->error = 'Invalid request';
            return false;
        }


        if ($weight_record->seller_action_status != 'open' || $weight_record->weight_applied != '1' || $weight_record->weight_difference_charges <= 0) {
            $this->error = 'Unable to process';
            return false;
        }

        $this->CI->load->library('user_lib');
        $this->CI->load->library('shipping_lib');
        $shipment = $this->CI->shipping_lib->getByID($weight_record->shipment_id);

        if (empty($shipment)) {
            $this->error = 'No record found';
            return false;
        }
        $save = array(
            'seller_action_status' => 'accepted',
        );

        if ($auto_accept)
            $save = array(
                'seller_action_status' => 'auto accepted',
            );

        if (!$weight_record->applied_to_wallet) {
            //apply charges to seller wallet
            $save['applied_to_wallet'] =  '1';
            $save['applied_to_wallet_date'] = time();
            if (!$this->apply_weight_charges($shipment, $weight_record->weight_difference_charges)) {
                return false;
            }

            $unhold_amount = $weight_record->weight_difference_charges;
            if (strtolower($shipment->ship_status) == 'rto') {
                $unhold_amount = round($unhold_amount * 2, 2);
            }

            $this->CI->user_lib->hold_release_remittance($weight_record->user_id, $unhold_amount, 'release');
        }
        $this->update($weight_record->id, $save);

        $save_shipment = array(
            'charged_weight' => $weight_record->weight_new_slab,
        );

        $this->CI->shipping_lib->update($shipment->id, $save_shipment);


        return true;
    }

    private function apply_weight_charges(object $shipment = NULL, $amount = 0)
    {
        if (empty($shipment->id) || empty($amount))
            return false;

        if ($shipment->extra_weight_charges > 0) {
            $this->error = 'Charges already applied';
            return false;
        }

        $this->CI->load->library('shipping_lib');
        $this->CI->load->library('wallet_lib');

        //get user details
        $this->CI->load->library('user_lib');
        $user = $this->CI->user_lib->getByID($shipment->user_id);

        if (empty($user))
            return false;

        $this->CI->load->library('plans_lib');
        $plan = $this->CI->plans_lib->getPlanByName($user->pricing_plan);

        if (empty($plan))
            return false;

        $plan_type = $plan->plan_type;

        //charge balance from customer wallet
        $wallet = new Wallet_lib();
        $wallet->setUserID($shipment->user_id);
        $wallet->setAmount($amount);
        $wallet->setTransactionType('debit');
        $wallet->setNotes('Extra Weight Charges Applied');
        $wallet->setTxnFor('shipment');
        $wallet->setRefID($shipment->id);
        $wallet->setTxnRef('extra_weight');

        if (!$wallet->creditDebitWallet())
            return false;

        $save = array(
            'extra_weight_charges' => $amount
        );

        if (($plan_type != 'per_dispatch') && (strtolower($shipment->ship_status) == 'rto') && ($shipment->rto_extra_weight_charges <= 0)) {
            $wallet = new Wallet_lib();
            $wallet->setUserID($shipment->user_id);
            $wallet->setAmount($amount);
            $wallet->setTransactionType('debit');
            $wallet->setNotes('RTO Extra Weight Charges Applied');
            $wallet->setTxnFor('shipment');
            $wallet->setRefID($shipment->id);
            $wallet->setTxnRef('rto_extra_weight');

            if (!$wallet->creditDebitWallet())
                return false;

            $save['rto_extra_weight_charges'] = $amount;
        }

        $this->CI->shipping_lib->update($shipment->id, $save);

        return true;
    }

    function raise_dispute($id = false, $remarks = false, $attachments = array(), $user_id = false)
    {
        if (!$id) {
            $this->error = 'Invalid ID';
            return false;
        }

        $weight_record = $this->getByID($id);



        if (empty($weight_record)) {
            $this->error = 'No record found';
            return false;
        }



        if ($user_id && $weight_record->user_id != $user_id) {
            $this->error = 'Invalid request';
            return false;
        }


        if ($weight_record->seller_action_status != 'open' || $weight_record->weight_applied != '1' || $weight_record->weight_difference_charges <= 0) {
            $this->error = 'Unable to process';
            return false;
        }

        $weight_time = $this->get_dispute_time_limit($user_id);
        

        $time_for_dispute =  $this->CI->config->item('weight_dispute_time_limit');

        if (!empty($weight_time)) {
            $time_for_dispute = $weight_time->time_limt * 24 * 60 * 60;
        }

        if ($weight_record->apply_weight_date < (time() - $time_for_dispute)) {
            $this->error = 'Unable to process';
            return false;
        }



        $this->CI->load->library('escalation_lib');

        //submit shipment escalation
        $update = array(
            'type' => 'weight',
            'ref_id' => $weight_record->shipment_id,
            'remarks' => $remarks,
            'action_by' => 'seller',
            'attachments' => (!empty($attachments)) ?  implode(',', $attachments) : ''
        );

        if (!$esc_id = $this->CI->escalation_lib->create_escalation($weight_record->user_id, $update)) {
            $this->error = 'Unable to create ticket';
            return false;
        }

        $save = array(
            'dispute_id' => $esc_id,
            'seller_action_status' => 'dispute',
        );

        $this->update($weight_record->id, $save);
        return true;
    }

    function autoChargeWeight()
    {
        $ids = $this->getRecordsForAutoAcceptWeight();
        if (empty($ids))
            return false;

        foreach ($ids as $id) {
            $this->accept_weight($id->id, false, true);
        }

        return true;
    }
}
