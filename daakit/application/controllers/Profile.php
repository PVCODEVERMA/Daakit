<?php

defined('BASEPATH') or exit('No direct script access allowed');

use App\Lib\Logs\User as Log;

class Profile extends User_controller
{

	private $allowed_image_extension;
	private $allowed_document_extension;

	public function __construct()
	{
		parent::__construct();
		$this->load->library('Profile_lib');
		$this->userHasAccess('settings');
		$this->load->library("s3");
		$this->allowed_image_extension = array('jpg', 'jpeg', 'png');
		$this->allowed_document_extension = array('pdf');
	}

	public function index()
    {
        $userid = $this->user->account_id;
        $this->load->library('form_validation');
        $picture = $gstimage = $signatureimg = "";
        if (!empty($_FILES['picture']['name'])) {
            $picture = $this->uploadFile('picture', 'seller_company_logo', true);
        }

        if (!empty($_FILES['gstimage']['name'])) {
            $gstimage = $this->uploadFile('gstimage', 'seller_company_gstimages');
        }
        // if (!empty($_FILES['signatureimg']['name'])) {
        //     $signatureimg = $this->uploadFile('signatureimg', 'seller_company_signatureimg');
        // }
        $config = array(
            array(
                'field' => 'cmp_email',
                'label' => 'Email Address',
                'rules' => 'trim|required|valid_email'
            ),
            array(
                'field' => 'cmp_phone',
                'label' => 'Contact Number',
                'rules' => 'trim|required|numeric|exact_length[10]'
            ),
            array(
                'field' => 'cmp_pan',
                'label' => 'Pan Number',
                'rules' => 'trim|required|alpha_numeric|exact_length[10]'
            ),
			array(
                'field' => 'cmp_cin',
                'label' => 'Company CIN',
                'rules' => 'trim|alpha_numeric|exact_length[21]'
            ),
            array(
                'field' => 'cmp_address',
                'label' => 'Company Address',
                'rules' => 'trim|required'
            ),
            array(
                'field' => 'cmp_city',
                'label' => 'City',
                'rules' => 'trim|required'
            ),
            array(
                'field' => 'cmp_state',
                'label' => 'State',
                'rules' => 'trim|required'
            ),
            array(
                'field' => 'cmp_pincode',
                'label' => 'Pincode',
                'rules' => 'trim|required'
            )
        );
        $this->form_validation->set_rules($config);
        if ($this->form_validation->run()) {
            $cmpdata = array(
                'user_id'           => $userid,
                'cmp_url'           => $this->input->post('cmp_url'),
                'cmp_email'         => $this->input->post('cmp_email'),
                'cmp_phone'         => $this->input->post('cmp_phone'),
                'cmp_pan'           => $this->input->post('cmp_pan'),
                'cmp_cin'           => $this->input->post('cmp_cin'),
                'cmp_gstno'         => $this->input->post('cmp_gstno'),
                'cmp_address'       => $this->input->post('cmp_address'),
                'cmp_city'          => $this->input->post('cmp_city'),
                'cmp_state'         => $this->input->post('cmp_state'),
                'cmp_pincode'       => $this->input->post('cmp_pincode'),
                'creation_date'     => time(),
                'modification_date' => time(),
            );

            if (!empty($picture)) {
                $cmpdata['cmp_logo'] = $picture;
            }
            if (!empty($gstimage)) {
                $cmpdata['cmp_gstimg'] = $gstimage;
            }
            // if (!empty($signatureimg)) {
            //     $cmpdata['cmp_signatureimg'] = $signatureimg;
            // }

            $cmprecord = $this->profile_lib->getCompanyByUserID($userid);
            if ($cmprecord->user_id != Null && $cmprecord->user_id != "") {

                $this->profile_lib->update_companydetails($userid, $cmpdata);


                $log = new Log();
                $old_details = array(
                    'cmp_url'           => $cmprecord->cmp_phone,
                    'cmp_email'         => $cmprecord->cmp_email,
                    'cmp_phone'  => $cmprecord->cmp_phone,
					'cmp_cin'           =>  $cmprecord->cmp_cin,
                    'cmp_address'   => $cmprecord->cmp_address,
                    'cmp_city'      => $cmprecord->cmp_city,
                    'cmp_state'     => $cmprecord->cmp_state,
                    'cmp_pincode'   => $cmprecord->cmp_pincode,
                    'cmp_pan'       => $cmprecord->cmp_pan,
                    'cmp_gstno'     => $cmprecord->cmp_gstno
                );
                $json_records = array('action' => 'Seller Profile updated', 'old_details' => $old_details, 'new_details' => $cmpdata);
                $log->update($userid, $userid, json_encode($json_records));

                $this->session->set_flashdata('success', 'Updated Company Details Successfully');
                redirect(base_url('profile'));
            } else {
                $this->profile_lib->insert_companydetails($cmpdata);
                $log = new Log();
                $json_records = array('action' => 'Seller Profile Saved', 'old_details' => '', 'new_details' => $cmpdata);
                $log->create($userid, $userid, json_encode($json_records));

                $this->session->set_flashdata('success', 'Saved Company Details Successfully');
                redirect(base_url('profile'));
            }
        } else {
            $this->data['error'] = validation_errors();
        }

        $profile = $this->profile_lib->getprofileByUserID($this->user->account_id);
        $this->data['profile'] = $profile;
        $this->data['state_codes'] = $this->config->item('state_codes');
        $this->layout('profile/index');
    }

	public function legalentity()
	{   
		$userid = $this->user->account_id;
		$cmprecord = $this->profile_lib->getLegalDetailsByUserId($userid);
		$this->load->library('form_validation');
		$config = array(
			array(
				'field' => 'legal_name',
				'label' => 'Legal Company Name',
				'rules' => 'trim|required'
			),
			array(
				'field' => 'legal_address',
				'label' => 'Company Address',
				'rules' => 'trim|required'
			),
			array(
				'field' => 'legal_city',
				'label' => 'City',
				'rules' => 'trim|required'
			),
			array(
				'field' => 'legal_state',
				'label' => 'State',
				'rules' => 'trim|required'
			),
			array(
				'field' => 'legal_pincode',
				'label' => 'Pincode',
				'rules' => 'trim|required'
			),
			// array(
			// 	'field' => 'type',
			// 	'label' => 'Registered GST Entity',
			// 	'rules' => 'trim|required'
			// )
			
		);
		if($this->input->post('type')=='1')
        {
            $config[] =array(
                            'field' => 'legal_gstno',
                            'label' => 'Legal Entity GST No',
                            'rules' => 'trim|alpha_numeric|exact_length[15]|required'
            );
        }
        else
        {   
            $config[] =array(
                'field' => 'legal_gstno',
                'label' => 'Legal Entity GST No',
                'rules' => 'trim|alpha_numeric|exact_length[15]'
            );
        }
		$gstimage = "";
        if (!empty($_FILES['gstimage']['name'])) {
            $gstimage = $this->uploadFile('gstimage', 'seller_company_gstimages');
        }
		$this->form_validation->set_rules($config);
		if ($this->form_validation->run()) {
			$cmpdata = array(
				'user_id' 			=> $userid,
				'legal_name' 		=> $this->input->post('legal_name'),
				'legal_gstno' 		=> $this->input->post('legal_gstno'),
				'legal_address'		=> $this->input->post('legal_address'),
				'legal_city'		=> $this->input->post('legal_city'),
				'legal_state'		=> $this->input->post('legal_state'),
				'legal_pincode'		=> $this->input->post('legal_pincode'),
				'type'              => $this->input->post('type'),
				'created' 	=> time(),
				'modified' => time(),
			);
			$cmpdata1 = array(
                'cmp_gstno'         => $this->input->post('legal_gstno'),
                'creation_date'     => time(),
                'modification_date' => time(),
            );
			$this->profile_lib->update_companydetails($userid, $cmpdata1);
			if (!empty($gstimage)) {
                $cmpdata['cmp_gstimg'] = $gstimage;
            }
			
			if ($cmprecord->user_id != Null && $cmprecord->user_id != "") {
				//$this->profile_lib->updateLegalEntity($userid, $cmpdata);

				// $log = new Log();
				// $old_details = array(
				// 	'legal_name' 		=> $this->input->post('legal_name'),
				// 	'legal_gstno' 		=> $this->input->post('legal_gstno'),
				// );
				// $log = new Log();
				// $old_details = array(
				// 	'cmp_url' 			=> $cmprecord->cmp_phone,
				// 	'cmp_email' 		=> $cmprecord->cmp_email,
				// 	'cmp_phone'  => $cmprecord->cmp_phone,
				// 	'cmp_address'   => $cmprecord->cmp_address,
				// 	'cmp_city'      => $cmprecord->cmp_city,
				// 	'cmp_state'     => $cmprecord->cmp_state,
				// 	'cmp_pincode'   => $cmprecord->cmp_pincode,
				// 	'cmp_pan'       => $cmprecord->cmp_pan,
				// 	'cmp_gstno'     => $cmprecord->cmp_gstno
				// );
				//  $json_records = array('action' => 'Seller Profile updated', 'old_details' => $old_details, 'new_details' => $cmpdata);
				 //$log->update($userid, $userid, json_encode($json_records));

				$this->session->set_flashdata('success', 'Updated Legal Entity Details Successfully');
				redirect(base_url('profile/legalentity'));
			} else {
				$this->profile_lib->insertLegalEntity($cmpdata);
				// $log = new Log();
				// $json_records = array('action' => 'Seller Legal Entity Saved', 'old_details' => '', 'new_details' => $cmpdata);
				// $log->create($userid, $userid, json_encode($json_records));

				$this->session->set_flashdata('success', 'Saved Legal Entity Details Successfully');
				redirect(base_url('profile/legalentity'));
			}
		} else {
			$this->data['error'] = validation_errors();
		}

		// $profile = $this->profile_lib->getprofileByUserID($this->user->account_id);
		// $profile->legal_gstno =  empty($profile->legal_gstno) ? $profile->cmp_gstno : $profile->legal_gstno;
		// $profile->legal_name = empty($profile->legal_name) ? $profile->company_name : $profile->legal_name;
		$this->data['profile'] = $cmprecord;
		$this->data['state_codes'] = $this->config->item('state_codes');
		$this->layout('profile/legalentity');
	}

	public function cmpaccountdetails()
	{
		$userid = $this->user->account_id;
		$this->load->library('form_validation');
		$chequeimage = "";
		if (!empty($_FILES['chequeimage']['name'])) {
			$chequeimage = $this->uploadFile('chequeimage', 'seller_company_Cheque');
		} else {
			$this->form_validation->set_rules('chequeimage', 'Cancelled Cheque Image', 'required');
		}
		$this->form_validation->set_message('alpha_numeric_spaces', 'Only Characters, Numbers & Spaces are allowed in %s');
		$config = array(
			array(
				'field' => 'cmp_accntholder',
				'label' => 'Account holder Name',
				'rules' => 'trim|required'
			),
			array(
				'field' => 'cmp_accno',
				'label' => 'Account Number',
				'rules' => 'trim|required|alpha_numeric'
			),
			array(
				'field' => 'bankacctype',
				'label' => 'Account Type',
				'rules' => 'required'
			),
			array(
				'field' => 'cmp_bankname',
				'label' => 'Bank Name',
				'rules' => 'trim|required'
			),
			array(
				'field' => 'cmp_bankbranch',
				'label' => 'Bank Branch',
				'rules' => 'trim|required'
			),
			array(
				'field' => 'cmp_accifsc',
				'label' => 'Bank IFSC Code',
				'rules' => 'trim|required'
			),
		);
		$this->form_validation->set_rules($config);
		if ($this->form_validation->run()) {
			$cmpaccountdata = array(
				'user_id' => $userid,
				'cmp_accntholder' => $this->input->post('cmp_accntholder'),
				'cmp_accno' => $this->input->post('cmp_accno'),
				'cmp_acctype' => $this->input->post('bankacctype'),
				'cmp_bankname' => $this->input->post('cmp_bankname'),
				'cmp_bankbranch' => $this->input->post('cmp_bankbranch'),
				'cmp_accifsc' => $this->input->post('cmp_accifsc'),
				'cmp_chequeimg' => $chequeimage,
				'creation_date' => time(),
				'modification_date' => time(),
			);
			$cmprecord = $this->profile_lib->getCompanyByUserID($userid);
			if ($cmprecord->user_id != Null && $cmprecord->user_id != "") {
				$this->profile_lib->update_cmpbankdetails($userid, $cmpaccountdata);

				// $log = new Log();
				// $old_details = array(
				// 	'cmp_accntholder' => $cmprecord->cmp_accntholder,
				// 	'cmp_accno' => $cmprecord->cmp_accno,
				// 	'cmp_acctype' => $cmprecord->cmp_acctype,
				// 	'cmp_bankname' => $cmprecord->cmp_bankname,
				// 	'cmp_bankbranch' => $cmprecord->cmp_bankbranch,
				// 	'cmp_accifsc' =>  $cmprecord->cmp_accifsc,
				// 	'cmp_chequeimg' => $cmprecord->cmp_chequeimg
				// );

				// $json_records = array('action' => 'Updated Bank Details', 'old_details' => $old_details, 'new_details' => $cmpaccountdata);
				// $log->update($userid, $userid, json_encode($json_records));

				$this->session->set_flashdata('success', 'Bank details has been successfully updated');
				redirect(base_url('profile/cmpaccountdetails'));
			} else {
				$this->profile_lib->insert_cmpbankdetails($cmpaccountdata);
				// $log = new Log();
				// $json_records = array('action' => 'Saved Bank Details', 'old_details' => '', 'new_details' => $cmpaccountdata);
				// $log->create($userid, $userid, json_encode($json_records));

				$this->session->set_flashdata('success', 'Bank details has been successfully inserted');
				redirect(base_url('profile/cmpaccountdetails'));
			}
		} else {
			$this->data['error'] = validation_errors();
		}

		$profile = $this->profile_lib->getprofileByUserID($this->user->account_id);
		$bankdetails = $this->profile_lib->getbankdetailsByUserID($this->user->account_id);
		$bank_verification_details = $this->profile_lib->getbankverificationdetailsByUserID($this->user->account_id);
		$checkProcessingState = $this->profile_lib->checkProcessingState($this->user->account_id);
        $this->data['profile'] = $profile;
		$this->data['bankdetails'] = $bankdetails;
		$this->data['checkProcessingState'] = $checkProcessingState;
		$this->data['bank_verification_details'] = $bank_verification_details;
		$this->layout('profile/cmpaccount');
	}


	public function request_bank_account(){
		$userid = $this->user->account_id;
		$this->load->library('form_validation');
		$chequeimage = "";
		if (!empty($_FILES['chequeimage']['name'])) {
			$chequeimage = $this->uploadFile('chequeimage', 'seller_company_Cheque');
		} else {
			$this->form_validation->set_rules('chequeimage', 'Cancelled Cheque Image', 'required');
		}
		$this->form_validation->set_message('alpha_numeric_spaces', 'Only Characters, Numbers & Spaces are allowed in %s');
		
		$config = array(
			array(
				'field' => 'cmp_accntholder',
				'label' => 'Account holder Name',
				'rules' => 'trim|required'
			),
			array(
				'field' => 'cmp_accno',
				'label' => 'Account Number',
				'rules' => 'trim|required|alpha_numeric'
			),
			array(
				'field' => 'bankacctype',
				'label' => 'Account Type',
				'rules' => 'required'
			),
			array(
				'field' => 'cmp_bankname',
				'label' => 'Bank Name',
				'rules' => 'trim|required'
			),
			array(
				'field' => 'cmp_bankbranch',
				'label' => 'Bank Branch',
				'rules' => 'trim|required'
			),
			array(
				'field' => 'cmp_accifsc',
				'label' => 'Bank IFSC Code',
				'rules' => 'trim|required'
			),
		);
		$this->form_validation->set_rules($config);
		if ($this->form_validation->run()) {
			$cmpaccountdata = array(
				'user_id' => $userid,
				'cmp_accntholder' => $this->input->post('cmp_accntholder'),
				'cmp_accno' => $this->input->post('cmp_accno'),
				'cmp_acctype' => $this->input->post('bankacctype'),
				'cmp_bankname' => $this->input->post('cmp_bankname'),
				'cmp_bankbranch' => $this->input->post('cmp_bankbranch'),
				'cmp_accifsc' => $this->input->post('cmp_accifsc'),
				'cmp_chequeimg' => $chequeimage,
				'creation_date' => time(),
				'modification_date' => time(),
			);
			$cmprecord = $this->profile_lib->insert_bank_verification($cmpaccountdata);
			$this->data['success'] = "Bank Details Updated Successfully";
			$this->output->set_header('refresh:3;url=cmpaccountdetails');


		} else {
			$this->data['error'] = validation_errors();
		}

		
		$this->layout('profile/add_bank');
	}



	public function kycdetails()
	{
		$documentimage = $pancarddocumentimage = $companydocumentimage = "";
		$companytype = $this->input->post('companytype');
		
		$profile = $this->profile_lib->getprofileByUserID($this->user->account_id);
		$kycdetails = $this->profile_lib->getkycdetailsByUserID($this->user->account_id);
		if ($companytype == "Sole Proprietorship") {
			$userid = $this->user->account_id;
			$this->load->library('form_validation');
			$companydocumentimagereq='trim';
			if (!empty($_FILES['documentimage']['name'])) {
				$companydocumentimagereq = 'trim|callback_file_check';
			}
			$config = array(
				array(
					'field' => 'document_type',
					'label' => 'Document Type',
					'rules' => 'required'
				),
				array(
					'field' => 'kycdoc_id',
					'label' => 'Document ID',
					'rules' => 'trim|required'
				),
				array(
					'field' => 'kycdoc_name',
					'label' => 'Document Name',
					'rules' => 'trim|required'
				),
				array(
					'field' => 'documentimage',
					'label' => 'Document Image',
					'rules' => $companydocumentimagereq
				),
			);
			$this->form_validation->set_rules($config);
			if(!empty($profile->documentimage)){
				$documentimage = $profile->documentimage;
			}
			if (!empty($_FILES['documentimage']['name'])) {
				
				$documentimage = $this->uploadFile('documentimage', 'kyc_document');
				
			} 
			if ($this->form_validation->run()) {
				$kycdata = array(
					'user_id' 			=> $userid,
					'companytype' 		=> $companytype,
					'document_type' 	=> $this->input->post('document_type'),
					'kycdoc_id' 		=> $this->input->post('kycdoc_id'),
					'kycdoc_name' 		=> $this->input->post('kycdoc_name'),
					'documentimage' 	=> $documentimage,
					'creation_date' 	=> time(),
					'modification_date' => time(),
				);
				$cmprecord = $this->profile_lib->getCompanyByUserID($userid);
				
				if ($cmprecord->user_id != Null && $cmprecord->user_id != "") {
					$this->profile_lib->update_kycdetails($userid, $kycdata,$profile->verified);

					$log = new Log();
					$old_details = array(
						'companytype'  => !empty($this->input->post('companytype'))?$this->input->post('companytype'):$cmprecord->companytype,
						'document_type'  =>!empty($this->input->post('document_type'))?$this->input->post('document_type'):$cmprecord->document_type,
						'kycdoc_id'   => !empty($this->input->post('kycdoc_id'))?$this->input->post('kycdoc_id'):$cmprecord->kycdoc_id,
						'kycdoc_name'      => !empty($this->input->post('kycdoc_name'))?$this->input->post('kycdoc_name'):$cmprecord->kycdoc_name,
						'documentimage'     => $documentimage,
						'cmppanno'              => $cmprecord->cmppanno,
						'cmppanname'            => $cmprecord->cmppanname,
						'pancarddocumentimage'  => $cmprecord->pancarddocumentimage
					);


					$json_records = array('action' => 'KYC Details Updated', 'old_details' => $old_details, 'new_details' => $kycdata);
					$log->update($userid, $userid, json_encode($json_records));
					$this->profile_lib->update_kycdetails($userid, $old_details,$profile->verified);
					$this->session->set_flashdata('success', 'KYC Details Updated Successfully');
					redirect(base_url('profile/kycdetails'));
				} else {
					$this->profile_lib->insert_kycdetails($kycdata);
					$log = new Log();
					$json_records = array('action' => 'KYC Details Saved', 'old_details' => '', 'new_details' => $kycdata);
					$log->create($userid, $userid, json_encode($json_records));
					$this->session->set_flashdata('success', 'KYC Details Saved Successfully');
					redirect(base_url('profile/kycdetails'));
				}
			} else {
				$this->data['error'] = validation_errors();
			}
		} else {
			$userid = $this->user->account_id;
			$this->load->library('form_validation');
			$pancarddocumentimagereq=$companydocumentimagereq='trim';
			if (!empty($_FILES['pancarddocumentimage']['name'])) {
				$pancarddocumentimagereq = 'trim|callback_file_checkpancardimage';
			}
			if (!empty($_FILES['companydocumentimage']['name'])) {
				$companydocumentimagereq = 'trim|callback_company_documentimage';
			}
			$config = array(
				array(
					'field' => 'cmppanno',
					'label' => 'Company Pan No',
					'rules' => 'required'
				),
				array(
					'field' => 'cmppanname',
					'label' => 'Company Pan Name',
					'rules' => 'trim|required'
				),
				// array(
				// 	'field' => 'company_document_type',
				// 	'label' => 'Document Type',
				// 	'rules' => 'required'
				// ),
				// array(
				// 	'field' => 'company_kycdoc_id',
				// 	'label' => 'Document ID',
				// 	'rules' => 'trim|required'
				// ),
				// array(
				// 	'field' => 'company_kycdoc_name',
				// 	'label' => 'Document Name',
				// 	'rules' => 'trim|required'
				// ),
				array(
					'field' => 'pancarddocumentimage',
					'label' => 'Pancard Image',
					'rules' => $pancarddocumentimagereq
				),
				// array(
				// 	'field' => 'companydocumentimage',
				// 	'label' => 'Company Document Image',
				// 	'rules' => $companydocumentimagereq
				// ),
			);
			if(!empty($profile->pancarddocumentimage)){
				$pancarddocumentimage = $profile->pancarddocumentimage;
			}
			if(!empty($profile->documentimage)){
				$companydocumentimage = $profile->documentimage;
			}
			if (!empty($_FILES['pancarddocumentimage']['name'])) {
				$pancarddocumentimage = $this->uploadFile('pancarddocumentimage', 'kyc_document_panimage');
			}
			if (!empty($_FILES['companydocumentimage']['name'])) {
				$companydocumentimage = $this->uploadFile('companydocumentimage', 'kyc_document');
			}
			$this->form_validation->set_rules($config);
			if ($this->form_validation->run()) {
				$kycdata = array(
					'user_id' 				=> $userid,
					'companytype' 			=> $companytype,
					'document_type' 		=> $this->input->post('company_document_type'),
					'kycdoc_id' 			=> $this->input->post('company_kycdoc_id'),
					'kycdoc_name' 			=> $this->input->post('company_kycdoc_name'),
					'cmppanno' 				=> $this->input->post('cmppanno'),
					'cmppanname' 			=> $this->input->post('cmppanname'),
					'documentimage' 		=> $companydocumentimage,
					'pancarddocumentimage' 	=> $pancarddocumentimage,
					'creation_date' 		=> time(),
					'modification_date' 	=> time(),
				);

				$cmprecord = $this->profile_lib->getCompanyByUserID($userid);
				
				if ($cmprecord->user_id != Null && $cmprecord->user_id != "") {

					$log = new Log();
					$old_details = array(
						'companytype'  => $cmprecord->companytype,
						'document_type'  => $cmprecord->document_type,
						'kycdoc_id'   => $cmprecord->kycdoc_id,
						'kycdoc_name'      => $cmprecord->kycdoc_name,
						'documentimage'     => !empty($cmprecord->documentimage)?$cmprecord->documentimage:$companydocumentimage,
						'cmppanno'              => $cmprecord->cmppanno,
						'cmppanname'            => $cmprecord->cmppanname,
						'pancarddocumentimage'  => !empty($cmprecord->pancarddocumentimage)?$cmprecord->pancarddocumentimage:$pancarddocumentimage
					);

					$json_records = array('action' => 'KYC Details Updated', 'old_details' => $old_details, 'new_details' => $kycdata);
					$log->update($userid, $userid, json_encode($json_records));

					$this->profile_lib->update_kycdetails($userid, $kycdata,$profile->verified);
					$this->session->set_flashdata('success', 'KYC Details Updated Successfully');
					redirect(base_url('profile/kycdetails'));
				} else {
					$this->profile_lib->insert_kycdetails($kycdata);
					$log = new Log();
					$json_records = array('action' => 'KYC Details Saved', 'old_details' => '', 'new_details' => $kycdata);
					$log->create($userid, $userid, json_encode($json_records));

					$this->session->set_flashdata('success', 'KYC Details Saved Successfully');
					redirect(base_url('profile/kycdetails'));
				}
			} else {
				$this->data['error'] = validation_errors();
			}
		}
		
		$this->data['profile'] = $profile;
		$this->data['kycdetails'] = $kycdetails;
		$this->layout('profile/kycdetails');
	}



	public function agreement()
	{
		$this->load->library('analytics_lib');
		$userid = $this->user->account_id;
		$this->load->library('user_lib'); 
		$user_details=$this->user_lib->getByID($userid);
		$data1=array();
		$data_date=$data_limit=array();

		$profile = $this->profile_lib->getprofileByUserID($this->user->account_id);
		$this->data['profile'] = $profile;
			if(!empty($this->data['profile']->agreement_accept_date))
			{
				//echo $this->data['profile']->agreement_accept_date;
				$date_filter = $this->data['profile']->agreement_accept_date; 
			    $data_date = $this->analytics_lib->getallagreements($date_filter,$userid);
			}

			if(!empty($this->data['profile']->agreement_accept_date))
			{
				
			    $data_limit = $this->analytics_lib->getallagreements_bef(@$this->data['profile']->agreement_accept_date,$userid);
			}
			else
			{
				$data_limit = $this->analytics_lib->getallagreements_limit(@$this->data['profile']->agreement_accept_date);
			}

			// if(!empty($data_limit))
			// {
			// $data_limit[0]->version='';
			// $data_limit[0]->change_description=''; 
			// }

			$data =array_merge($data_date,$data_limit);
			
			$data = array_map("unserialize", array_unique(array_map("serialize", $data)));
			
			
			$this->data['agreements']=$data;

				foreach($data as $d)
				{
					$data1[]= $this->analytics_lib->getallagreements_accpt($d->id,$this->user->account_id);
				}
				
				
				$this->data['agreements1']=$data1;
				$this->layout('profile/agreement_layout');

	}

	// public function agreementsubmit()
	// {
	// 	$userid = $this->user->account_id;
	// 	$upload_folder = "company_agreement";

	// 	$mpdf = new \Mpdf\Mpdf([
	// 		'tempDir' => './temp',
	// 		'mode' => 'utf-8',
	// 	]);
	// 	$profile = $this->profile_lib->getprofileByUserID($userid);
	// 	$pdf_content = $this->load->view('profile/agreement_pdf', array('profile' => $profile), true);
	// 	$mpdf->WriteHTML($pdf_content);
	// 	$file_name = date('YmdHis') . 'Agreementdownload' . rand(10, 99) . '.pdf';

	// 	$source_file_path = 'assets/tmp/' . $file_name;

	// 	$mpdf->Output($source_file_path, 'F');

	// 	$agreement_file = $this->s3->amazonS3Upload($file_name, $source_file_path, $upload_folder);
	// 	unlink($source_file_path);

	// 	$accpt_data = array(
	// 		"user_id" 				=> $userid,
	// 		"agreement_status" => '1',
	// 		"agreement_url" => $agreement_file,
	// 	);
	// 	$cmprecord = $this->profile_lib->checkcmprecord($userid);
	// 	if ($cmprecord->user_id != Null && $cmprecord->user_id != "") {
	// 		$this->profile_lib->acceptagreementupdate($userid, $accpt_data);
	// 		$this->session->set_flashdata('success', 'Agreement File Accepted Successfully Please Download Your Agreement');
	// 		redirect(base_url('profile/agreement'));
	// 	} else {
	// 		$this->profile_lib->insert_acceptagreement($accpt_data);
	// 		$this->session->set_flashdata('success', 'Agreement File Accepted Successfully Please Download Your Agreement');
	// 		redirect(base_url('profile/agreement'));
	// 	}
	// }

	public function uploadagreement()
	{
		$uploadagreement = '';
		$userid = $this->user->account_id;
		if (!empty($_FILES['uploadagreement']['name'])) {
			$uploadagreement = $this->uploadFile('uploadagreement', 'seller_upload_agreement');
		} else {
			$this->form_validation->set_rules('uploadagreement', 'Upload Agreement', 'required');
		}
		$accpt_data = array(
			"user_id" 				=> $userid,
			"seller_upload_agree" => $uploadagreement,
		);
		$cmprecord = $this->profile_lib->checkcmprecord($userid);
		if ($cmprecord->user_id != Null && $cmprecord->user_id != "") {
			$this->profile_lib->updateuploadagreement($userid, $accpt_data);
			redirect(base_url('profile'));
		} else {
			$this->profile_lib->insert_agreementupload($accpt_data);
			redirect(base_url('profile'));
		}
	}

	private function uploadFile($variable_name = null, $folder_name = null, $image_only = false)
	{
		if ($variable_name == null || $folder_name == null) {
			return '';
		}
		$returnval = '';
		$extension = strtolower(pathinfo($_FILES[$variable_name]['name'], PATHINFO_EXTENSION));

		$new_name = time() . rand(1111, 9999) . '.' . ($extension);

		if (($image_only && in_array($extension, $this->allowed_image_extension)) || (!$image_only && (in_array($extension, $this->allowed_image_extension) || in_array($extension, $this->allowed_document_extension)))) {
			$config['file_name'] = $new_name;

			$fileTempName = $_FILES[$variable_name]['tmp_name'];
			$image_name = $new_name;

			$file_name = $this->s3->amazonS3Upload($image_name, $fileTempName, $folder_name);
			if ($file_name) {
				$returnval = $file_name;
			}
		}
		return $returnval;
	}
	function changePassword()
	{
		redirect(base_url('users/login'));
	}


	public function verify_gst()
	{
		$gst =$this->input->post('gst_no');
		if(empty($gst) || strlen($gst) != 15)
		{
			$this->data['json'] =array('error' => 'Invalid Gst');
			$this->layout(false, 'json');
			return;
		}
		$this->load->library('Gst_verification_lib');
		$data = $this->gst_verification_lib->getGSTDetails($gst);
		$this->data['json'] = (empty($data)) ? array('error' => 'Invalid Gst') : array('success' =>'true', 'data'=>$data);
		$this->layout(false, 'json');
		return;
	}

	public function file_check($str){
        $allowed_mime_type_arr = array('application/pdf','image/gif','image/jpeg','image/pjpeg','image/png','image/x-png');
        $mime = get_mime_by_extension($_FILES['documentimage']['name']);
        if(isset($_FILES['documentimage']['name']) && $_FILES['documentimage']['name']!=""){
            if(in_array($mime, $allowed_mime_type_arr)){
                return true;
            }else{
                $this->form_validation->set_message('file_check', 'Please select only PDF/GIF/JPG/PNG file  for Document  Image.');
                return false;
            }
        }
		else
		{
			$this->form_validation->set_message('file_check', 'Please Select Document  Image.');
               
			return false;
		}
		
    }

	public function file_checkpancardimage($str){
        $allowed_mime_type_arr = array('application/pdf','image/gif','image/jpeg','image/pjpeg','image/png','image/x-png');
        $mime = get_mime_by_extension($_FILES['pancarddocumentimage']['name']);
        if(isset($_FILES['pancarddocumentimage']['name']) && $_FILES['pancarddocumentimage']['name']!=""){
            if(in_array($mime, $allowed_mime_type_arr)){
                return true;
            }else{
                $this->form_validation->set_message('file_checkpancardimage', 'Please select only PDF/GIF/JPG/PNG file  for Pancard  Image.');
                return false;
            }
        }
        else
		{
			$this->form_validation->set_message('file_checkpancardimage', 'Please Select Pan Card  Image.');
                
			return false;
		}
	
    }

	public function company_documentimage($str){
        $allowed_mime_type_arr = array('application/pdf','image/gif','image/jpeg','image/pjpeg','image/png','image/x-png');
        $mime = get_mime_by_extension($_FILES['companydocumentimage']['name']);
        if(isset($_FILES['companydocumentimage']['name']) && $_FILES['companydocumentimage']['name']!=""){
            if(in_array($mime, $allowed_mime_type_arr)){
                return true;
            }else{
                $this->form_validation->set_message('company_documentimage', 'Please select only PDF/GIF/JPG/PNG file for Company Document Image.');
                return false;
            }
        }
		else
		{
			$this->form_validation->set_message('company_documentimage', 'Please Select Company Documentimage Image.');
                return false;
			
		}
		
    }
}
