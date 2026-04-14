<?php

class Profile_model extends MY_model {

    public function __construct() {
        parent::__construct();
        $this->table = 'users';
        $this->legal_entity = 'legal_entity';
    }

    function getprofileByUserID($user_id = false) {
        if (!$user_id)
            return false;
        $this->db->select("users.*,
		company_details.*");
        $this->db->where('users.id', $user_id);
        $this->db->join('company_details', 'company_details.user_id = users.id', 'left');
        $q = $this->db->get($this->table);
        return $q->row();
    }
	
	function getbankdetailsByUserID($user_id = false) {
        if (!$user_id)
            return false;
        $this->db->select("users.*,
		company_details.*");
        $this->db->where('users.id', $user_id);
        $this->db->join('company_details', 'company_details.user_id = users.id', 'left');
        $q = $this->db->get($this->table);
        return $q->result();
    }

    function getbankverificationdetailsByUserID($user_id = false) {
        if (!$user_id)
            return false;
        $this->db->select("reject_reason,status,user_id,cmp_accntholder,cmp_accno,cmp_acctype,cmp_bankname,cmp_bankbranch,cmp_accifsc,cmp_chequeimg,status,creation_date,modification_date");
        $this->db->where('user_id', $user_id);
        $this->db->order_by("id", "desc");
        $q = $this->db->get('bank_verifications');
        return $q->result();
    }

    function checkProcessingState($user_id = false) {
        if (!$user_id)
            return false;
        $this->db->select("user_id,cmp_accntholder,cmp_accno,cmp_acctype,cmp_bankname,cmp_bankbranch,cmp_accifsc,cmp_chequeimg,status,creation_date,modification_date");
        $this->db->where('user_id', $user_id);
        $this->db->where('status', "0");
        $this->db->limit(1);
        $q = $this->db->get('bank_verifications');
        return $q->result();
    }

	
	function getkycdetailsByUserID($user_id = false) {
        if (!$user_id)
            return false;
        $this->db->select("users.*,
		company_details.*");
        $this->db->where('users.id', $user_id);
        $this->db->join('company_details', 'company_details.user_id = users.id', 'left');
        $q = $this->db->get($this->table);
        return $q->result();
    }

    function checkcmprecord($userid = false) {
        if (!$userid)
            return false;
        $this->db->select("company_details.user_id,company_details.agreement_accept_date");
        $this->db->where('company_details.user_id', $userid);
        $q = $this->db->get('company_details');
        return $q->row();
    }

    function getCompanyByUserID($userid = false) {
        if (!$userid)
            return false;
        $this->db->where('company_details.user_id', $userid);
        $q = $this->db->get('company_details');
        return $q->row();
    }

    function getBankVerificationByUserID($userid = false) {
        if (!$userid)
            return false;
        $this->db->where('bank_verifications.user_id', $userid);
        $q = $this->db->get('bank_verifications');
        return $q->row();
    }

    function insert_companydetails($cmpdata) {
        return $this->db->insert("company_details", $cmpdata);
    }

    function update_companydetails($userid, $cmpdata) {
        if (empty($userid))
            return false;
        $this->db->set($cmpdata);
        $this->db->where('user_id', $userid);
        return $this->db->update('company_details');
    }
	
	
	function insert_cmpbankdetails($cmpaccountdata) {
        return $this->db->insert("company_details", $cmpaccountdata);
    }

    function insert_bank_verification($cmpaccountdata) {
        return $this->db->insert("bank_verifications", $cmpaccountdata);
    }

    function update_cmpbankdetails($userid, $cmpaccountdata) {
        if (empty($userid))
            return false;
        $this->db->set($cmpaccountdata);
        $this->db->where('user_id', $userid);
        return $this->db->update('company_details');
    }
	
	function insert_kycdetails($kycdata)
	{
        return $this->db->insert("company_details", $kycdata);
    }

    function update_kycdetails($userid, $kycdata,$verified=false) {
        if (empty($userid))
            return false;

            //if($verified==1)
            //{
                $this->db->set($kycdata);
                $this->db->where('user_id', $userid);
                return $this->db->update('company_details');
            //}
       
            return false;
    }
	
	
	function insert_acceptagreement($accpt_data)
	{
        return $this->db->insert("company_details", $accpt_data);
    }
	
	function acceptagreementupdate($userid, $accpt_data)
	{
		if (empty($userid))
            return false;
        $this->db->set($accpt_data);
        $this->db->where('user_id', $userid);
        return $this->db->update('company_details');
	}
	
	
	function insert_agreementupload($accpt_data)
	{
        return $this->db->insert("company_details", $accpt_data);
    }
	
	function updateuploadagreement($userid, $accpt_data)
	{
		if (empty($userid))
            return false;
        $this->db->set($accpt_data);
        $this->db->where('user_id', $userid);
        return $this->db->update('company_details');
	}

    function getLegalDetailsByUserId($userid)
    {
        if (!$userid)
        return false;
        $this->db->select("*");
        $this->db->where('user_id', $userid);
        $q = $this->db->get($this->legal_entity);
        return $q->row();
    }


    function insertLegalEntity($accpt_data)
	{
        return $this->db->insert($this->legal_entity, $accpt_data);
    }
	
	function updateLegalEntity($userid, $accpt_data)
	{
		if (empty($userid) || empty($accpt_data))
            return false;
        $this->db->set($accpt_data);
        $this->db->where('user_id', $userid);
        return $this->db->update($this->legal_entity);
	}

    function get_invoice_setting($userid = false) {
        if (!$userid)
            return false;
        $this->db->select("*");
        $this->db->where('invoice_setting.seller_id', $userid);
        $q = $this->db->get('invoice_setting');
        return $q->row();
    }

    function get_channel_data($chanelid = false) {
        if (!$chanelid)
            return false;
            $this->db->select("id,brand_logo");
            $this->db->where('user_channels.id', $chanelid);
            $q = $this->db->get('user_channels');
            return $q->row();
    }

    function insert_invoice_setting($data)
	{
        return $this->db->insert("invoice_setting", $data);
    }

    function update_invoice_setting($userid, $inv_data)
	{
		if (empty($userid) || empty($inv_data))
            return false;
        $this->db->set($inv_data);
        $this->db->where('seller_id', $userid);
        return $this->db->update('invoice_setting');
	}

}

?>