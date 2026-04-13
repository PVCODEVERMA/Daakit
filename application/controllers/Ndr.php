<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Ndr extends User_controller
{

    public function __construct()
    {
        parent::__construct();
        $this->load->library('ndr_lib');
        $this->userHasAccess('ndr');
    }

    function index($page = 1)
    {
        $per_page = $this->input->get('perPage');
        if (empty($per_page) || !is_numeric($per_page))
            $limit = 50;
        else
            $limit = $per_page;

        $this->load->library('courier_lib');
        $couriers = $this->courier_lib->userAvailableCouriers($this->user->account_id);

        $this->data['couriers'] = $couriers;

        $filter = $this->input->post('filter');

        $apply_filters = array();

        $apply_filters['start_date'] = strtotime("-30 days midnight");
        $apply_filters['end_date'] = strtotime("tomorrow midnight") - 1;

        if (!empty($filter['start_date']))
            $apply_filters['start_date'] = strtotime(trim($filter['start_date']) . ' 00:00:00');
        else
            $filter['start_date'] = date('Y-m-d', $apply_filters['start_date']);


        if (!empty($filter['end_date']))
            $apply_filters['end_date'] = strtotime(trim($filter['end_date']) . ' 23:59:59');
        else
            $filter['end_date'] = date('Y-m-d', $apply_filters['end_date']);

        if (!empty($filter['pay_method'])) {
            $apply_filters['pay_method'] = $filter['pay_method'];
        }

        if (!empty($filter['courier_id'])) {
            $apply_filters['courier_id'] = $filter['courier_id'];
        }

        if (!empty($filter['tags'])) {
            $apply_filters['tags'] = strtolower($filter['tags']);
        }

        if (!empty($filter['status'])) {
            $apply_filters['status'] = $filter['status'];
        }

        if (!empty($filter['attempts'])) {
            $apply_filters['attempts'] = $filter['attempts'];
        }

        if (!empty($filter['order_ids'])) {
            $apply_filters['order_ids'] = array_map('trim', explode(',', $filter['order_ids']));
        }

        if (!empty($filter['ndr_id'])) {
            $apply_filters['ndr_id'] = array_map('trim', explode(',', $filter['ndr_id']));
        }

        if (!empty($filter['awb_no'])) {
            $apply_filters['awb_no'] = array_map('trim', explode(',', $filter['awb_no']));
        }

        if (!empty($filter['channel_id'])) {
            $apply_filters['channel_id'] = $filter['channel_id'];
        }

        $total_row = $this->ndr_lib->countByUserID($this->user->account_id, $apply_filters);
        $config = array(
            'base_url' => base_url('ndr/index'),
            'total_rows' => $total_row,
            'per_page' => $limit,
            'num_links' => '3',
            'use_page_numbers' => true,
            'reuse_query_string' => true,
            'cur_tag_open' => '<li class="paginate_button page-item active"><a class="page-link" href="' . current_url() . '">',
            'cur_tag_close' => '</a></li>',
            'num_tag_open' => '<li class="paginate_button page-item ">',
            'num_tag_close' => '</li>',
            'attributes' => array(
                'aria-controls' => 'example-multi',
                'tabindex' => '0',
                'class' => 'page-link',
            ),
            'next_link' => 'Next',
            'prev_link' => 'Prev',
        );

        $this->load->library("pagination");
        $this->pagination->initialize($config);
        $this->data["pagination"] = $this->pagination->create_links();
        $offset = $limit * ($page - 1);
        $this->data['total_records'] = $total_row;
        $this->data['limit'] = $limit;
        $this->data['offset'] = $offset;
        $ndrs = $this->ndr_lib->getByUserID($this->user->account_id, $limit, $offset, $apply_filters);

        $status_orders = array();
        $status_order_count = $this->ndr_lib->countByUserIDStatusGrouped($this->user->account_id, $apply_filters);

        $this->data['count_by_status'] = $status_order_count;
        //pr($status_order_count, true);
        $this->data['ndrs'] = $ndrs;
        $this->data['filter'] = $filter;

        $this->load->library('channels_lib');
        $channels = $this->channels_lib->getChannelsByUserID($this->user->account_id);
        $this->data['channels'] = $channels;

        //check ivr is enabled
        $ivr_enabled = apply_filters('ivr.ndr_enabled', false, $this->user->account_id);

        $this->data['ivr_enabled'] = $ivr_enabled;

        $this->layout('ndr/index');
       // $this->layout('ndr/index-old');
    }

    function exportCSV()
    {
        $filter = $this->input->get('filter');
        $apply_filters = array();

        if (!empty($filter['start_date'])) {
            $apply_filters['start_date'] = strtotime(trim($filter['start_date']) . ' 00:00:00');
        }
        if (!empty($filter['end_date'])) {
            $apply_filters['end_date'] = strtotime(trim($filter['end_date']) . ' 23:59:59');
        }

        if (!empty($filter['pay_method'])) {
            $apply_filters['pay_method'] = $filter['pay_method'];
        }

        if (!empty($filter['courier_id'])) {
            $apply_filters['courier_id'] = $filter['courier_id'];
        }

        if (!empty($filter['tags'])) {
            $apply_filters['tags'] = strtolower($filter['tags']);
        }

        if (!empty($filter['status'])) {
            $apply_filters['status'] = $filter['status'];
        }

        if (!empty($filter['attempts'])) {
            $apply_filters['attempts'] = $filter['attempts'];
        }

        if (!empty($filter['order_ids'])) {
            $apply_filters['order_ids'] = array_map('trim', explode(',', $filter['order_ids']));
        }

        if (!empty($filter['ndr_id'])) {
            $apply_filters['export_all'] = 'yes';
            $apply_filters['ndr_id'] = array_map('trim', explode(',', $filter['ndr_id']));
        }

        if (!empty($filter['awb_no'])) {
            $apply_filters['awb_no'] = array_map('trim', explode(',', $filter['awb_no']));
        }

        if (!empty($filter['channel_id'])) {
            $apply_filters['channel_id'] = $filter['channel_id'];
        }


        $this->data['filter'] = $filter;
        $ndrlist = $this->ndr_lib->getByUserID($this->user->account_id, 10000, 0, $apply_filters);
        $filename = 'Exception_' . time() . '.csv';
        header("Content-Description: File Transfer");
        header("Content-Disposition: attachment; filename=$filename");
        header("Content-Type: application/csv; ");
        $file = fopen('php://output', 'w');
        $header = array("ID", "NDR Date", "Pickup Date", "Order Number", "Product Name", "Payment Type", "Order Amount", "Carrier", "AWB Number", "Order Status", "Customer Name", "Customer Phone", "Shipping Address", "City", "State", "Pincode", "Last Action By", "Attempts", "Details", "Seller Action", "Change Name", "Change Address 1", "Change Address 2", "Change Phone", "Seller Remarks");
        fputcsv($file, $header);
        foreach ($ndrlist as $ndr) {
            $row = array(
                $ndr->id,
                date('Y-m-d', $ndr->created),
                date('Y-m-d', $ndr->pickup_time),
                $ndr->order_number,
                $ndr->products,
                $ndr->order_payment_type,
                $ndr->order_amount,
                $ndr->courier_name,
                $ndr->awb_number,
                ucwords($ndr->ship_status . (($ndr->ship_status == 'rto' && !empty($ndr->rto_status)) ? ' ' . $ndr->rto_status : '')),
                $ndr->shipping_fname . ' ' . $ndr->shipping_lname,
                $ndr->shipping_phone,
                $ndr->shipping_address . ' ' . $ndr->shipping_address_2,
                $ndr->shipping_city,
                $ndr->shipping_state,
                $ndr->shipping_zip,
                $ndr->ndr_source,
                $ndr->ndr_attempt,
                $ndr->ndr_remarks,
                '',
                '',
                '',
                '',
                '',
                '',
                ''
            );
            fputcsv($file, $row);
        }
        fclose($file);
        exit;
    }

    function import()
    {
        $this->load->library('form_validation');
        $config = array(
            array(
                'field' => 'importFile',
                'label' => 'Import File',
                'rules' => 'callback_file_check'
            ),
        );

        $this->form_validation->set_rules($config);
        if (!$this->form_validation->run()) {
            $this->data['error'] = validation_errors();
            $this->session->set_flashdata('error', $this->data['error']);
            redirect('ndr/index', true);
        }


        if (is_uploaded_file($_FILES['importFile']['tmp_name'])) {
            // Load CSV reader library
            $this->load->library('csvreader');
            // Parse data from CSV file
            $csvData = $this->csvreader->parse_csv($_FILES['importFile']['tmp_name']);
            if (empty($csvData)) {
                $this->session->set_flashdata('error', 'Blank CSV File');
                redirect('ndr/index', true);
            }
            foreach ($csvData as $row_key => $row) {
                if (!$this->validate_upload_data($row)) {
                    $this->session->set_flashdata('error', 'Row no. ' . ($row_key + 1) . $this->data['error']);
                    redirect('ndr/index', true);
                }
            }

            foreach ($csvData as $row_key_2 => $row2) {

                //submit ndr
                $update = array(
                    'ndr_id' => $row2['ID'],
                    'action' => $row2['Seller Action'],
                    'remarks' => !empty($row2['Seller Remarks']) ? $row2['Seller Remarks'] : '',
                    'source' => 'seller',
                    're_attempt_date' => (strtolower($row2['Seller Action']) == 're-attempt') ? strtotime('+1 day 23:59:59') : '',
                    'customer_details_name' => !empty($row2['Change Name']) ? $row2['Change Name'] : '',
                    'customer_details_address_1' => !empty($row2['Change Address 1']) ? $row2['Change Address 1'] : '',
                    'customer_details_address_2' => !empty($row2['Change Address 2']) ? $row2['Change Address 2'] : '',
                    'customer_contact_phone' => !empty($row2['Change Phone']) ? $row2['Change Phone'] : '',
                );
                if (!$this->ndr_lib->AddNDRAction($update, $this->user->account_id)) {
                    redirect('ndr/index', true);
                }
            }
            $this->session->set_flashdata('success', 'NDR Submitted Successful');
            redirect('ndr/index', true);
        }
    }

    private function validate_upload_data($data)
    {
        if (!empty($data['Seller Action']))
            $data['Seller Action'] = strtolower($data['Seller Action']);

        $this->form_validation->set_data($data);

        $this->form_validation->set_message('required', '%s is required');
        $this->form_validation->set_message('alpha_numeric_spaces', 'Only Characters, Numbers & Spaces are  allowed in %s');

        $config = array(
            array(
                'field' => 'ID',
                'label' => 'ID',
                'rules' => 'trim|numeric|greater_than[0]',
            ),
            array(
                'field' => 'Seller Action',
                'label' => 'Seller Action',
                'rules' => 'trim|required|in_list[re-attempt,change address,change phone,rto]',
            ),
            array(
                'field' => 'Seller Remarks',
                'label' => 'Seller Remarks',
                'rules' => 'trim|max_length[200]',
            ),

        );
        switch (strtolower($data['Seller Action'])) {
            case 'change address':
                $ndr_rules = array(
                    array(
                        'field' => 'Change Name',
                        'label' => 'Change Name',
                        'rules' => 'trim|required|max_length[50]'
                    ),
                    array(
                        'field' => 'Change Address 1',
                        'label' => 'Change Address 1',
                        'rules' => 'trim|required|min_length[10]|max_length[200]'
                    ),
                    array(
                        'field' => 'Change Address 2',
                        'label' => 'Change Address 2',
                        'rules' => 'trim|max_length[200]'
                    ),
                );
                $config = array_merge($config, $ndr_rules);
                break;
            case 'change phone':
                $ndr_rules = array(
                    array(
                        'field' => 'Change Phone',
                        'label' => 'Change Phone',
                        'rules' => 'trim|required|exact_length[10]|numeric'
                    )
                );
                $config = array_merge($config, $ndr_rules);
                break;
            default:
        }


        $this->form_validation->set_rules($config);

        if ($this->form_validation->run()) {
            $this->form_validation->reset_validation();
            return true;
        } else {
            $this->data['error'] = validation_errors();
            $this->form_validation->reset_validation();
            return false;
        }
    }


    // function action()
    // {
	// 	$this->load->library('form_validation');
	// 	$this->load->library('callcenter_lib');
		
    //     $config = array(
    //         array(
    //             'field' => 'ndr_id',
    //             'label' => 'NDR ID',
    //             'rules' => 'trim|required'
    //         ),
    //         array(
    //             'field' => 'action',
    //             'label' => 'Action',
    //             'rules' => 'trim|required'
    //         ),
    //         array(
    //             'field' => 'remarks',
    //             'label' => 'Remarks',
    //             'rules' => 'trim|max_length[200]'
    //         ),
    //     );

    //     switch (strtolower($this->input->post('action'))) {
    //         case 're-attempt':
    //             $ndr_rules = array(
    //                 array(
    //                     'field' => 're_attempt_date',
    //                     'label' => 'Re-Attempt Date',
    //                     'rules' => 'trim|required|integer'
    //                 ),
    //             );
    //             $config = array_merge($config, $ndr_rules);
    //             break;
    //         case 'change address':
    //             $ndr_rules = array(
    //                 array(
    //                     'field' => 'customer_details_name',
    //                     'label' => 'Customer Name',
    //                     'rules' => 'trim|required|max_length[50]'
    //                 ),
    //                 array(
    //                     'field' => 'customer_details_address_1',
    //                     'label' => 'Customer Address 1',
    //                     'rules' => 'trim|required|min_length[10]|max_length[200]'
    //                 ),
    //                 array(
    //                     'field' => 'customer_details_address_2',
    //                     'label' => 'Customer Address 2',
    //                     'rules' => 'trim|max_length[200]'
    //                 ),
    //             );
    //             $config = array_merge($config, $ndr_rules);
    //             break;
    //         case 'change phone':
    //             $ndr_rules = array(
    //                 array(
    //                     'field' => 'customer_contact_phone',
    //                     'label' => 'Phone Number',
    //                     'rules' => 'trim|required|exact_length[10]|numeric'
    //                 )
    //             );
    //             $config = array_merge($config, $ndr_rules);
    //             break;
    //         default:
    //     }

    //     $this->form_validation->set_rules($config);
    //     if ($this->form_validation->run()) {
    //         $ndr_ids = $this->input->post('ndr_id');
    //         $action = $this->input->post('action');
    //         $remarks = $this->input->post('remarks');
    //         $re_attempt_date = $this->input->post('re_attempt_date');
    //         $customer_details_name = $this->input->post('customer_details_name');
    //         $customer_details_address_1 = $this->input->post('customer_details_address_1');
    //         $customer_details_address_2 = $this->input->post('customer_details_address_2');
    //         $customer_contact_phone = $this->input->post('customer_contact_phone');

    //         $ndr_ids = explode(',', $ndr_ids);
		 
    //         foreach ($ndr_ids as $ndr_id) {
    //             //submit ndr
    //             $update = array(
    //                 'ndr_id' => $ndr_id,
    //                 'action' => $action,
    //                 'remarks' => $remarks,
    //                 'source' => 'seller',
    //                 're_attempt_date' => $re_attempt_date,
    //                 'customer_details_name' => $customer_details_name,
    //                 'customer_details_address_1' => $customer_details_address_1,
    //                 'customer_details_address_2' => $customer_details_address_2,
    //                 'customer_contact_phone' => $customer_contact_phone,
    //             );
			  
	// 			$this->callcenter_lib->Add_NDR_Action($update) ;

    //             // if($new_ndr_actionid)
	// 			// {
	// 			// 	if( $update['source'] ==  'seller' ) {
						 
	// 			// 		$res = $this->callcenter_lib->find_leadby_ndrid( $update['ndr_id'] ); 	
 					
	// 			// 		if($res && $res[0]->lead_id != '')
	// 			// 		{
	// 			// 			// close the lead 
	// 			// 			$result = $this->callcenter_lib->update_ndr_lead_status( $update['ndr_id'] , $res[0]->lead_id ) ;	
							
	// 			// 			if($result->success)
	// 			// 			$this->callcenter_lib->update_ndrlead_status_by_action( $result->status , $res[0]->id ) ;
	// 			// 		}  
	// 			// 	} 	
	// 			// }  	
    //         } // ended foreach 
	// 			$this->session->set_flashdata('success', 'NDR Submitted Successfully.');
	// 			$this->data['json'] = array('success' => 'done');		
    //     } else {
    //         $this->data['json'] = array('error' => strip_tags(validation_errors()));
    //     }
		
    //     $this->layout(false, 'json');
    // }

    
    function action()
{
    $this->load->library('form_validation');
    $this->load->library('callcenter_lib');
    $this->load->library('ndr_lib');

    $config = array(
        array('field' => 'ndr_id', 'label' => 'NDR ID', 'rules' => 'trim|required'),
        array('field' => 'action', 'label' => 'Action', 'rules' => 'trim|required'),
        array('field' => 'remarks', 'label' => 'Remarks', 'rules' => 'trim|max_length[200]')
    );

    switch (strtolower($this->input->post('action'))) {
        case 're-attempt':
            $config[] = array('field' => 're_attempt_date', 'label' => 'Re-Attempt Date', 'rules' => 'trim|required|integer');
            break;
        case 'change address':
            $config = array_merge($config, array(
                array('field' => 'customer_details_name', 'label' => 'Customer Name', 'rules' => 'trim|required|max_length[50]'),
                array('field' => 'customer_details_address_1', 'label' => 'Customer Address 1', 'rules' => 'trim|required|min_length[10]|max_length[200]'),
                array('field' => 'customer_details_address_2', 'label' => 'Customer Address 2', 'rules' => 'trim|max_length[200]')
            ));
            break;
        case 'change phone':
            $config[] = array('field' => 'customer_contact_phone', 'label' => 'Phone Number', 'rules' => 'trim|required|exact_length[10]|numeric');
            break;
    }

    $this->form_validation->set_rules($config);

    if ($this->form_validation->run()) {
        $ndr_ids = explode(',', $this->input->post('ndr_id'));
        $action = $this->input->post('action');
        $remarks = $this->input->post('remarks');
        $re_attempt_date = $this->input->post('re_attempt_date');
        $customer_details_name = $this->input->post('customer_details_name');
        $customer_details_address_1 = $this->input->post('customer_details_address_1');
        $customer_details_address_2 = $this->input->post('customer_details_address_2');
        $customer_contact_phone = $this->input->post('customer_contact_phone');

        $error_messages = array();
        $success_count = 0;

        foreach ($ndr_ids as $ndr_id) {
            // Prepare data for Add_NDR_Action
            $update = array(
                'ndr_id' => $ndr_id,
                'action' => $action,
                'remarks' => $remarks,
                'source' => 'seller',
                're_attempt_date' => $re_attempt_date,
                'customer_details_name' => $customer_details_name,
                'customer_details_address_1' => $customer_details_address_1,
                'customer_details_address_2' => $customer_details_address_2,
                'customer_contact_phone' => $customer_contact_phone,
            );

            // Save NDR action and get action ID
            $action_id = $this->callcenter_lib->Add_NDR_Action($update);

            if (!$action_id || !is_numeric($action_id)) {
                $error_messages[] = "NDR ID {$ndr_id}: Failed to add NDR action.";
                continue; // Skip further steps for this NDR
            }

            // Prepare update for NDR record
            $save = array(
                'last_action' => $action,
                'last_action_by' => 'seller',
                'latest_remarks' => $remarks,
                'last_event' => time(),
                'last_action_id' => $action_id
            );

            // Update NDR record in ndr_lib
            $update_result = $this->ndr_lib->update($ndr_id, $save);

            if (!$update_result) {
                $error_messages[] = "NDR ID {$ndr_id}: Failed to update NDR record.";
                continue; // Skip pushing to courier
            }

            // Push NDR to courier
            $push_result = $this->ndr_lib->pushNdrActionToCourier($ndr_id);

            if (!$push_result) {
                $error_message = !empty($this->ndr_lib->error) ? $this->ndr_lib->error : 'Unknown error while pushing NDR.';
                $error_messages[] = "NDR ID {$ndr_id}: {$error_message}";
            } else {
                $success_count++;
            }
        }

        // Set flash messages
        if (!empty($error_messages)) {
            $this->session->set_flashdata('error', implode('<br>', $error_messages));
        }
        if ($success_count > 0) {
            $this->session->set_flashdata('success', "{$success_count} NDR(s) submitted and pushed successfully.");
        }

        $this->data['json'] = array('success' => 'done');
    } else {
        $this->data['json'] = array('error' => strip_tags(validation_errors()));
    }

    $this->layout(false, 'json');
}

    function action_history()
    {
        $this->load->library('form_validation');
        $config = array(
            array(
                'field' => 'ndr_id',
                'label' => 'NDR ID',
                'rules' => 'trim|required|min_length[1]|max_length[20]|numeric'
            ),
        );
        $this->form_validation->set_rules($config);
        if ($this->form_validation->run()) {
            $ndr_id = $this->input->post('ndr_id');
            $this->data['ndr_id'] = $ndr_id;
            //fetch NDR fdetails
            $ndr = $this->ndr_lib->getByID($ndr_id);
            if ($ndr && $ndr->user_id == $this->user->account_id) {
                $history = $this->ndr_lib->ndrActionHistory($ndr_id);
                $this->data['history'] = $history;
                $this->data['json'] = array('success' => $this->load->view('ndr/ndr_action_history', $this->data, true));
            } else {
                $this->data['json'] = array('error' => 'Invalid request');
            }
        } else {
            $this->data['json'] = array('error' => strip_tags(validation_errors()));
        }
        $this->layout(false, 'json');
    }

    function file_check()
    {
        $allowed_mime_types = array('text/x-comma-separated-values', 'text/comma-separated-values', 'application/octet-stream', 'application/vnd.ms-excel', 'application/x-csv', 'text/x-csv', 'text/csv', 'application/csv', 'application/excel', 'application/vnd.msexcel', 'text/plain');
        if (isset($_FILES['importFile']['name']) && $_FILES['importFile']['name'] != "") {
            $mime = get_mime_by_extension($_FILES['importFile']['name']);
            $fileAr = explode('.', $_FILES['importFile']['name']);
            $ext = end($fileAr);
            if (($ext == 'csv') && in_array($mime, $allowed_mime_types)) {
                return true;
            } else {
                $this->form_validation->set_message('file_check', 'Please select only CSV file to upload.');
                return false;
            }
        } else {
            $this->form_validation->set_message('file_check', 'Please select a CSV file to upload.');
            return false;
        }
    }
}
