<?php

class Bank_model extends MY_model
{

    public function __construct()
    {
        parent::__construct();
        $this->table = 'bank_verification_logs';
    }

    function insert($save = array())
    {
        if (empty($save))
            return false;

        $save['created'] = time();

        $this->db->insert($this->table, $save);
        return ($this->db->affected_rows() != 1) ? false : true;
    }
    function update($id, $otp = array())
    {
        if (empty($otp))
            return false;

        $save = array(
            'otp' => $otp['otp'],
            'otp_expire' => $otp['expired'],
            'created' => time()
        );
        $this->db->where('id', $id);
        $this->db->update($this->table, $save);
        return ($this->db->affected_rows() != 1) ? false : true;
    }

    function findBankLog($user_id, $bankDetails)
    {
        if (empty($user_id) || empty($bankDetails)) {
            return false;
        }

        $this->db->select('id');
        $this->db->where('user_id', $user_id);
        $this->db->where('account_number', $bankDetails['account_number']);
        $this->db->where('ifsc', $bankDetails['ifsc']);
        $this->db->order_by('id', 'desc');
        $row = $this->db->get($this->table)->row(0);

        if (!empty($row))
            return $row->id;
        else
            return false;
    }
    function matchOTP($user_id, $bankDetails)
    {
        if (empty($user_id) || empty($bankDetails)) {
            return false;
        }

        $this->db->select('*');
        $this->db->where('user_id', $user_id);
        $this->db->where('account_number', $bankDetails['account_number']);
        $this->db->where('ifsc', $bankDetails['ifsc']);
        $this->db->where('otp', $bankDetails['otp']);
        $this->db->where('amount', $bankDetails['fund_amount']*100);
        $this->db->where('otp_expire >', time());
        $this->db->order_by('id', 'desc');
        $row = $this->db->get($this->table)->row(0);

        if (!empty($row))
            return $row;
        else
            return false;
    }

    function verificationLimit($user_id){

        $limit  = 3;

        $this->db->select('id');
        $this->db->where('user_id', $user_id);
        $this->db->where('created > ', time() - (60*60*24));
        $this->db->where('created <= ', time());
        $count = $this->db->get($this->table)->num_rows();

        if($count >= $limit){
            return true;
        }else{
            return false;
        }

    }
}
