<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Wallet_lib extends MY_lib
{

    var $user_id = false;
    var $amount = false;
    var $type = 'debit';
    var $notes = false;
    var $ref_id = '0';
    var $txn_for = false;
    var $txn_ref = false;

    public function __construct($config = array())
    {
        parent::__construct();
        if (!empty($config['user_id']))
            $this->user_id = $config['user_id'];

        $this->CI->load->model('wallet_model');
    }

    public function __call($method, $arguments)
    {
        if (!method_exists($this->CI->wallet_model, $method)) {
            throw new Exception('Undefined method wallet_model::' . $method . '() called');
        }

        return call_user_func_array([$this->CI->wallet_model, $method], $arguments);
    }

    function setUserID($user_id = false)
    {
        $this->user_id = $user_id;
    }

    function setAmount($amount = false)
    {
        $this->amount = $amount;
    }

    function setTransactionType($type = false)
    {
        $this->type = $type;
    }

    function setNotes($note = false)
    {
        $this->notes = $note;
    }

    function setTxnFor($for = false)
    {
        $this->txn_for = $for;
    }

    function setRefID($id = false)
    {
        $this->ref_id = $id;
    }

    function setTxnRef($ref = false)
    {
        $this->txn_ref = $ref;
    }

    function creditDebitWallet()
    {
        if (!$this->user_id)
            return false;

        $amount = round($this->amount, 2);

        if ($amount <= 0)
            return false;

        $this->CI->load->library('user_lib');

        if (!in_array($this->type, array('credit', 'debit')))
            return false;

        $this->CI->user_lib->credit_debit_wallet($this->user_id, $amount, $this->type);

        $user = $this->CI->user_lib->getByID($this->user_id);

        $after_wallet = $user->wallet_balance;

        //save history to db

        $history = array(
            'user_id' => $this->user_id,
            'amount' => round($amount, 2),
            'balance_after' => round($after_wallet, 2),
            'type' => $this->type,
            'notes' => $this->notes,
            'ref_id' => trim($this->ref_id),
            'txn_for' => trim($this->txn_for),
            'txn_ref' => trim($this->txn_ref),
        );
        //pr($history);

        $history_id = $this->insert_history($history);
        do_action('wallet.txn', $history_id);
        switch (trim($this->txn_for)) {
            case 'recharge':
            case 'neft':
            case 'cod':
            case 'promotion':
                do_action('user.wallet_recharged', $this->user_id);
                break;
            default:
        }
        return $history_id;
    }

    function getWalletBalance($user_id = false)
    {
        if (!$this->user_id && !$user_id)
            return false;

        if ($user_id)
            $this->user_id = $user_id;

        $this->CI->load->library('user_lib');
        if (!$user = $this->CI->user_lib->getByID($this->user_id))
            return false;

        return $user->wallet_balance;
    }

    function checkUserCanShip($user_id = false)
    {
        $this->CI->load->library('user_lib');
        //get user wallet balance
        if (!$user = $this->CI->user_lib->getByID($user_id))
            return false;

        if (($user->wallet_balance - $user->wallet_limit) < 100)
            return false; // atleast 100 rs is required to ship an order

        return true;
    }
}
