<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Dashboard extends User_controller
{

    public function __construct()
    {
        parent::__construct();
        $this->load->library('analytics_lib');
    }

    function index()
    {
        if ($this->user->user_id != $this->user->account_id)
            $this->employee_dashboard();
        else
            $this->parent_dashboard();
    }

    function employee_dashboard()
    {
        $this->load->library('user_lib');
        $employee = $this->user_lib->getByID($this->user->user_id);
        $permissions = array();
        if (!empty($employee->permissions)) {
            $permissions = explode(',', $employee->permissions);
        }

        if(in_array('dashboard', $permissions)) {
            $this->parent_dashboard();
        } else {
            $this->layout('dash/index');
        }
    }

    function parent_dashboard()
    {
        $filter = $this->input->post('filter');
        $apply_filters = array();

        $start_date = strtotime("-30 days midnight");
        $end_date = time();

        if (!empty($filter['start_date'])) {
            $start_date = strtotime(trim($filter['start_date']) . ' 00:00:00');
        } else {
            $filter['start_date'] = date('Y-m-d', $start_date);
        }

        if (!empty($filter['end_date'])) {
            $end_date  = strtotime(trim($filter['end_date']) . ' 23:59:59');
        } else {
            $filter['end_date'] = date('Y-m-d', $end_date);
        }

        $stats = $this->analytics_lib->userShipmentStats($this->user->account_id, $start_date, $end_date);
        $this->data['stats'] = $stats;

        $this->load->library('orders_lib');
        $total_row = $this->orders_lib->countByUserID($this->user->account_id, array('start_date'=>$start_date,'end_date'=>$end_date));
        $this->data['total_orders'] = $total_row;

        $top_destinations = $this->analytics_lib->topDestinations($this->user->account_id, $start_date, $end_date);
        $this->data['top_destinations'] = $top_destinations;

        $courier_status = $this->analytics_lib->courierWiseStatusDistribution($this->user->account_id, $start_date, $end_date);
        
        $courier_stats = [];
        $this->load->library('courier_lib');

        $couriers = $this->courier_lib->showingToUsers();
      
        $this->data['couriers'] = $couriers;
        foreach ($courier_status as $c_array) {
            $courier_id = $c_array->courier_id;
            $courier_vise_data = array_filter($couriers, function($v,$k) use ($courier_id){
                return $courier_id == $v->id;
                },ARRAY_FILTER_USE_BOTH);
            $key = array_keys($courier_vise_data);
            
            if(!empty($key)) {
                $data = $courier_vise_data[$key[0]];
                $courier_name = str_replace(' ', '_', strtolower($data->display_name));
                $c_array->courier_name = $data->name;
                $c_array->display_name = $data->display_name; 
                $courier_stats[$courier_name][] = $c_array; 
            }
        }

        $courier_monthly_status = $this->analytics_lib
            ->courierWiseMonthlyStatusCounts(
                $this->user->account_id,
                $start_date,
                $end_date
            );

        $this->data['courier_monthly_status'] = $courier_monthly_status;
        $this->data['courier_stats'] = $courier_stats;
        $this->data['courier_stats_new'] = $courier_stats;
        
        $productWiseStatus = $this->analytics_lib->productWiseStatusDistribution($this->user->account_id, $start_date, $end_date);
        $this->data['product_wise_status'] = $productWiseStatus;

        $this->load->library('weight_lib');
        $number_of_weight_disputes = $this->weight_lib->countOpenWeighDisputes($this->user->account_id);

        $this->data['total_weight_disputes'] = $number_of_weight_disputes;
       
        //onboarding steps check
        //check if channel is integrated
        $this->data['onboarding_channel'] = $this->analytics_lib->user_channel_integrated($this->user->account_id);
        $this->data['onboarding_warehouse'] = $this->analytics_lib->user_warehouse_integrated($this->user->account_id);
        $user_detail = $this->analytics_lib->user_recharge_check($this->user->account_id);
        if ($user_detail->wallet_balance > '0' || $user_detail->wallet_limit != '0')
            $this->data['onboarding_recharge'] = true;
        else
            $this->data['onboarding_recharge'] = false;

        $kyc_details = $this->analytics_lib->user_kyc_check($this->user->account_id);
        
        if (!empty($kyc_details) && !empty($kyc_details->companytype))
            $this->data['onboarding_kyc'] = true;

        if ($user_detail->verified == '1')
            $this->data['onboarding_kyc'] = true;

        else
            $this->data['onboarding_kyc'] = false;

        $this->data['onboarding_shipment'] = $this->analytics_lib->user_shipment_check($this->user->account_id);
        $this->load->library('ndr_lib');
        $status_order_count = $this->ndr_lib->countByUserIDStatusGrouped($this->user->account_id, ['start_date'=>$start_date, 'end_date'=>$end_date]);
        $this->data['count_by_status'] = $status_order_count;

        $this->load->library('escalation_lib');
        $escalation_count = $this->escalation_lib->getUserActionReqEscalations($this->user->account_id, ['start_date'=>$start_date, 'end_date'=>$end_date]);
        $this->data['escalation_count'] = $escalation_count;
       
        $this->load->library('Profile_lib');
        $data_date = array();
        $data1 = array();
		$profile = $this->profile_lib->getprofileByUserID($this->user->account_id);
		$this->data['profile'] = $profile;
        $current_time = time();

		if(!empty($this->data['profile']->agreement_accept_date)) {
			$date_filter = $this->data['profile']->agreement_accept_date; 
		    $data_date = $this->analytics_lib->getallagreements_dash($date_filter,$this->user->account_id);
		}

        if(!empty($data_date)) {
            foreach($data_date as $d) {
				$data1[]= $this->analytics_lib->getallagreements_accpt($d->id,$this->user->account_id);
			}
        }
            
        $accept_status = '0';
        $data1 = array_filter($data1);

        if((!empty($this->data['profile']->agreement_accept_date) && empty($data1)) || (empty($data1))) {
            $accept_status = '1';
        }

        if(!empty($this->data['profile']->agreement_accept_date) && empty($data_date) &&  empty($data1)) {
            $accept_status = '0';
        }

        if(!empty($this->data['profile']->agreement_accept_date) && !empty($data_date)) {
            $accept_status = '0';
        }

        if((!empty($this->data['profile']->agreement_accept_date) && empty($data1)) && !empty($data_date)) {
            $accept_status = '1';
            $accept_id = @$data_date[0]->id;
        } else {
            $accept_status = '0';
            $accept_id = '0';
        }
      
        if(!empty($this->data['profile']->remind_me_later) && $this->data['profile']->remind_me_later >= $current_time) {
            $accept_status = '0';
        }

        $this->data['agreement_accept'] = $accept_status; 
        $this->data['accept_id'] = $accept_id;

        $this->data['filter'] = $filter;
        $userid = $this->user->account_id;
        $this->load->library('Profile_lib');
        $this->data['legal_entity'] = $this->profile_lib->getLegalDetailsByUserId($userid);
        $this->layout('onboarding/index');
    }

    function markagreement()
    {
        if(empty($_POST['aggrement_id']))
            return false;
       
        $data = $this->analytics_lib->get_agreements($_POST['aggrement_id']);
        if(!empty($data)) {
            $data1[] = $this->analytics_lib->getallagreements_accpt($data[0]->id,$this->user->account_id);
            if($data1 != '0' && isset($data1[0]->seller_id)) {
                $data[0]->status = '1';
                $agreement_url = $data1[0]->agreement_url;
            } else {
                $data[0]->status = '';
                $agreement_url = base_url($data[0]->doc_link);
            }

            echo $data[0]->id."||".$data[0]->section_name."||".$data[0]->version."||".$agreement_url."||".date("d M, Y",strtotime($data[0]->publish_on))."||".$data[0]->change_description."||".$data[0]->status;
        } else {
            echo "error";
        }
    }

    function acpt_agreement()
    {
        if (empty($_POST['update_id']))
            return false;

        $this->load->library('Profile_lib');
        $this->load->library("s3");
        $userid = $this->user->account_id;
        $upload_folder = "company_agreement";

        $profile = $this->profile_lib->getprofileByUserID($userid);

        if(empty($profile->cmp_phone) || empty($profile->fname) || empty($profile->company_name) || empty($profile->cmp_address) || empty($profile->cmp_pan)){
            $this->data['json'] = array('error' => 'Please fill your basic company profile.');
            $this->layout(false, 'json');
        }
        if(empty($profile->companytype)){
            $this->data['json'] = array('error' => 'Please fill your kyc details.');
            $this->layout(false, 'json');
        }
        $replace_key = array('{CREATED_ON}','{COMPANY_NAME}','{COMPANY_GST_NO}','{COMPANY_ADDRESS}',
            '{COMPANY_PAN}','{FIRST_NAME}','{LAST_NAME}','{CURRENT_DATE}','{COMPANY_CIN}','{COMPANY_CITY}','{COMPANY_STATE}','{COMPANY_PINCODE}','{EMAIL_ID}','WEBSITE_URL');
        $replace_value = array(
            date("j F Y",strtotime($profile->created)),ucfirst($profile->company_name),$profile->cmp_address,
            strtoupper($profile->cmp_pan),ucfirst($profile->fname),ucfirst($profile->lname),date("d-m-Y", time()),$profile->cmp_cin,$profile->cmp_city,$profile->cmp_state,$profile->cmp_pincode,$profile->cmp_email,$profile->cmp_url);
        if(!empty($profile->cmp_gstno))
            array_splice($replace_value, 2, 0, "[GSTIN: ".$profile->cmp_gstno." ]");
        else
            array_splice($replace_value, 2, 0, "");

        $mpdf = new \Mpdf\Mpdf([
            'tempDir' => './temp',
            'mode' => 'utf-8',
        ]);
        $this->load->library('analytics_lib');
        $content = $this->analytics_lib->getAgreementContent();
        if(empty($content))
            return false;
        $pdf_content = $this->load->view('profile/agreement_pdf', array('content'=>$content), true);
        $pdf_content = str_replace($replace_key,$replace_value,$pdf_content);

        $mpdf->WriteHTML($pdf_content);
        $file_name = 'agreement_download_' . time() . '.pdf';

        $source_file_path = 'assets/' . $upload_folder."/".$file_name;

        $mpdf->Output($source_file_path, 'F');

        $agreement_file = $this->s3->amazonS3Upload($file_name, $source_file_path, $upload_folder);
        //unlink($source_file_path);
        //pr($replace_key,1);
        
        $cmprecord = $this->profile_lib->checkcmprecord($userid);

        if (!empty($cmprecord) && ($cmprecord->user_id != Null && $cmprecord->user_id != "")) {
            $accpt_data = array(
                "user_id" => $userid,
                "agreement_status" => '1',
                "agreement_url" => $agreement_file,
                "modification_date" => time(),
            );

            if (empty($cmprecord->agreement_accept_date)) {
                $accpt_data['agreement_accept_date'] = time();
            }
            $this->profile_lib->acceptagreementupdate($userid, $accpt_data);
        } else {
            $accpt_data = array(
                "user_id" => $userid,
                "agreement_status" => '1',
                "agreement_url" => $agreement_file,
                "agreement_accept_date" => time(),
                "modification_date" => time(),
            );
            $this->profile_lib->insert_acceptagreement($accpt_data);
        }

        $get_id = $this->analytics_lib->getallagreements_date($_POST['update_id']);
        foreach ($get_id as $d) {
            $data1 = $this->analytics_lib->get_acceptence($d->id, $this->user->account_id);
            if ($data1 == 0) {
                $this->analytics_lib->insert_acceptence($d->id, $agreement_file);
            }
        }

        $this->data['json'] = array('success' => 'You have Successfully Accepted the Agreement');
        $this->layout(false, 'json');
    }

    function remind_me_later()
    {
        $this->load->library('Profile_lib');
        $userid = $this->user->account_id;
		$cmprecord = $this->profile_lib->checkcmprecord($userid);
        $date = Date('m/d/Y', strtotime('+3 days'));
        $new_time = strtotime(trim($date) . ' 00:00:00'); // agreement_accept_date
       
		if (!empty($cmprecord) && ($cmprecord->user_id != Null && $cmprecord->user_id != "")) {
            $accpt_data = array(
                "user_id" => $userid,
                "remind_me_later" => $new_time, 
            );
         
			$this->profile_lib->acceptagreementupdate($userid, $accpt_data);
		} else {
            $accpt_data = array(
                "user_id" => $userid,
                "remind_me_later" => $new_time,
            );

            $this->profile_lib->insert_acceptagreement($accpt_data);
		}

        $cmprecord = $this->profile_lib->checkcmprecord($userid);
       
        $this->data['json'] = array('success' => 'You have Successfully Accepted the Agreement');
        $this->layout(false, 'json');
    }

    function kyc($type = 'individual')
    {
        $valid_types = ['individual', 'sole_proprietor', 'partnership', 'company'];
        if (!in_array($type, $valid_types)) $type = 'individual';
        $this->layout('onboarding/kyc/' . $type . '/index');
    }

    public function onboarding($step = 'index', $type = 'individual')
    {
        $views = [
            'index' => 'onboarding/index',
            'kyc' => 'onboarding/kyc/' . $type . '/index',
            'recharge-wallet' => 'onboarding/recharge-wallet',
            'add-pickup' => 'onboarding/add-pickup',
            'create-order' => 'onboarding/create-order'
        ];

        if (array_key_exists($step, $views)) {
            // Retrieve data to support index.php variables
            $this->data['onboarding_channel'] = $this->analytics_lib->user_channel_integrated($this->user->account_id);
            $this->data['onboarding_warehouse'] = $this->analytics_lib->user_warehouse_integrated($this->user->account_id);
            
            $user_detail = $this->analytics_lib->user_recharge_check($this->user->account_id);
            if ($user_detail->wallet_balance > '0' || $user_detail->wallet_limit != '0')
                $this->data['onboarding_recharge'] = true;
            else
                $this->data['onboarding_recharge'] = false;

            $kyc_details = $this->analytics_lib->user_kyc_check($this->user->account_id);
            if (!empty($kyc_details) && !empty($kyc_details->companytype))
                $this->data['onboarding_kyc'] = true;
            elseif ($user_detail->verified == '1')
                $this->data['onboarding_kyc'] = true;
            else
                $this->data['onboarding_kyc'] = false;

            $this->data['onboarding_shipment'] = $this->analytics_lib->user_shipment_check($this->user->account_id);
            $this->data['user_details'] = $user_detail; // To support fname rendering

            $this->layout($views[$step]); 
        } else {
            show_404();
        }
    }
}