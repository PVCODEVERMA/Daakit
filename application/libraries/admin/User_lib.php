<?php

use Firebase\JWT\JWT;

defined('BASEPATH') or exit('No direct script access allowed');

class User_lib extends MY_lib
{

    public function __construct()
    {
        parent::__construct();
        $this->CI->load->model('admin/user_model');
    }

    public function __call($method, $arguments)
    {
        if (!method_exists($this->CI->user_model, $method)) {
            throw new Exception('Undefined method user_model::' . $method . '() called');
        }
        return call_user_func_array([$this->CI->user_model, $method], $arguments);
    }

    function getTrelloURL($user_id = false)
    {
        if (!$user_id)
            return false;

        $user = $this->getByID($user_id);

        if (empty($user))
            return false;

        if (!empty($user->trello_card_url))
            return $user->trello_card_url;

        //generate the trello card

        $this->CI->load->library('trello');
        $description = "ID:{$user->id} \xA Name: {$user->fname} {$user->lname} \xA Mobile: {$user->phone}";
        $card = $this->CI->trello->create_card($user->company_name, $description);
        if (!$card)
            return false;

        $this->update($user_id, array('trello_card_id' => $card['id'], 'trello_card_url' => $card['url']));

        return $card['url'];
    }

    function getLoginToken($user_id = false)
    {
        if (!$user_id)
            return false;

        $this->CI->load->library('jwt_lib');
        $jwt = new JWT_lib();
        $jwt_data = array(
            'user_id' => $user_id,
        );
        return $token = $jwt->encode($jwt_data);
    }

    function getDuplicateUser($singleuserdetail = array(), $userwarehosuedetail = array())
    {

        if (!$singleuserdetail)
            return false;

        $singleuserdetail = $singleuserdetail[0];
        $sellerid = array('1');
        $sellerid[] = $singleuserdetail->sellerid;
        $gstno = array();
        $phone = array();
        $email = array();
        $cmp_pan = $singleuserdetail->cmp_pan;
        $cmp_accno = $singleuserdetail->cmp_accno;

        if (!empty($singleuserdetail->cmp_gstno)) {
            $gstno[] = $singleuserdetail->cmp_gstno;
        }

        if (!empty($singleuserdetail->cmp_phone)) {
            $phone[] = $singleuserdetail->cmp_phone;
        }

        if (!empty($singleuserdetail->email)) {
            $email[] = $singleuserdetail->email;
        }

        if (!empty($userwarehosuedetail)) {
            foreach ($userwarehosuedetail as $value) {
                if (!empty($value->email))
                    $email[] = $value->email;

                if (!empty($value->phone))
                    $phone[] = $value->phone;

                if (!empty($value->gst_number))
                    $gstno[] = $value->gst_number;
            }
        }

        $rs = $this->fetchDuplicateUser($sellerid, $cmp_pan, $cmp_accno, array_unique($email), array_unique($phone), array_unique($gstno));

        if (!empty($rs)) {
           // $existingrecords = $this->getduplicateUserdata($sellerid[1]);

            $existing_records = array();
            // if (!empty($existingrecords)) {
            //     foreach ($existingrecords as $ext) {
            //         $existing_records[$ext->duplicate_user_id] = $ext;
            //     }
            // }

            $insert = array();
   
            foreach ($rs as $response) {
                $duplicate = '';
                if (!empty($response->cmp_gstno) && $response->cmp_gstno == '1') {
                    $duplicate .= 'Company GST Number,';
                }if (!empty($response->w_gstno) && $response->w_gstno == '1') {
                    $duplicate .= 'Warehouse GST Number,';
                }
                if (!empty($response->cmp_email) && $response->cmp_email == '1') {
                    $duplicate .= 'Company Email Id,';
                }
                if (!empty($response->w_email) && $response->w_email == '1') {
                    $duplicate .= 'Warehouse Email ID,';
                }
                if (!empty($response->c_phone) && $response->c_phone == '1') {
                    $duplicate .= 'Company Phone Number,';
                }
                if (!empty($response->w_phone) && $response->w_phone == '1') {
                    $duplicate .= 'Warehouse Phone Number,';
                }
                if (!empty($response->pan_card) && $response->pan_card == '1') {
                    $duplicate .= 'Pan card,';
                }
                if (!empty($response->cmp_accno) && $response->cmp_accno == '1') {
                    $duplicate .= 'Account Number,';
                }
                if (!array_key_exists($response->id, $existing_records)) {
                    $insert[] = array(
                        'seller_id' => $sellerid[1],
                        'duplicate_user_id' => $response->id,
                        'duplicate_user_name' => $response->fname . ' ' . $response->lname,
                        'duplicate_by' => rtrim($duplicate,','),
                        'created'      => time(),
                    );
                }
            }
                //$this->batchInsertDuplicateUser($insert);
        }

        return $rs;
    }

    function create_lead($user_id = false)
    {
        return true;
        
        if (!$user_id)
            return false;

        $user = $this->getByID($user_id);

        if (empty($user))
            return false;


        // delta to Sales CRM from API
        $this->CI->load->library('delta_sales_crm');

        $lead = new delta_sales_crm();
        $lead->setSellerID($user->id);
        $lead->setFName($user->fname);
        $lead->setLName($user->lname);
        $lead->setEmail($user->email);
        $lead->setPhone($user->phone);
        $lead->setVerified($user->verified);
        $lead->setCompany($user->company_name);
        $lead->setSource(($user->lead_source) ? $user->lead_source : 'organic');
        $lead->setStatus(($user->status == '1') ? 'Active' : 'New');
        $lead->setShippingVolume($user->shipping_volume);
        $lead->setShippingPartner($user->shipping_partner);
        $lead->setShippingType($user->industry_type);

        $lead->createLead();
        return true;
    }
}
