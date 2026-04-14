<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Ndr extends Admin_controller
{

    public function __construct()
    {
        parent::__construct();
        $this->load->library('admin/ndr_lib');
        $this->userHasAccess('ndr');
    }

    function index($page = 1)
    {
        $per_page = $this->input->post('perPage');
        if (empty($per_page) || !is_numeric($per_page))
            $limit = 50;
        else
            $limit = $per_page;

        $this->load->library('courier_lib');
        $couriers = $this->courier_lib->showingToUsers();
        $this->data['couriers'] = $couriers;

        $filter = $this->input->post('filter');
        $apply_filters = array();

        $apply_filters['start_date'] = strtotime("-7 days midnight");
        $apply_filters['end_date'] = strtotime("tomorrow midnight") - 1;

        if (!empty($filter['start_date'])) {
            $apply_filters['start_date'] = strtotime(trim($filter['start_date']) . ' 00:00:00');
        } else {
            $filter['start_date'] = date('Y-m-d', $apply_filters['start_date']);
        }
        if (!empty($filter['end_date'])) {
            $apply_filters['end_date'] = strtotime(trim($filter['end_date']) . ' 23:59:59');
        } else {
            $filter['end_date'] = date('Y-m-d', $apply_filters['end_date']);
        }

        if (!empty($filter['pay_method'])) {
            $apply_filters['pay_method'] = $filter['pay_method'];
        }

        if (!empty($filter['courier_id'])) {
            $apply_filters['courier_id'] = $filter['courier_id'];
        }

        if (!empty($filter['seller_id'])) {
            $apply_filters['seller_id'] = $filter['seller_id'];
        }

        if (!empty($filter['manager_id'])) {
            $apply_filters['manager_id'] = $filter['manager_id'];
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

        if (!empty($filter['awb_no'])) {
            $apply_filters['awb_no'] = array_map('trim', explode(',', $filter['awb_no']));
        }
        if (!empty($filter['ndr_type'])) {
            $apply_filters['ndr_type'] = $filter['ndr_type'];
        }

        if (!empty($filter['ndr_ids'])) {
            $apply_filters['ndr_id'] = array_map('trim', explode(',', $filter['ndr_ids']));
        }

        $total_row = $this->ndr_lib->countByUserID($apply_filters);
        $config = array(
            'base_url' => base_url('admin/ndr/index'),
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
        $ndrs = $this->ndr_lib->getByUserID($limit, $offset, $apply_filters);


        $seller_details = '';
        $this->load->library('admin/user_lib');
        
        $seller_details = $this->user_lib->getUserListFilter($filter['seller_id']);

        $this->data['users'] = $seller_details;
        $status_orders = array();
        $status_order_count = $this->ndr_lib->countByUserIDStatusGrouped($apply_filters);
        $this->data['count_by_status'] = $status_order_count;
        $this->data['ndrs'] = $ndrs;
        $this->data['filter'] = $filter;
        $admin_users = $this->user_lib->getAdminUsers();
        $this->data['admin_users'] = $admin_users;
        $this->layout('ndr/index');
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
            $ndr = $this->ndr_lib->getByID($ndr_id);
            if (!empty($ndr)) {
                $history = $this->ndr_lib->ndrActionHistory($ndr_id);
                $this->data['history'] = $history;
                $this->data['json'] = array('success' => $this->load->view('admin/ndr/ndr_action_history', $this->data, true));
            } else {
                $this->data['json'] = array('error' => 'Invalid request');
            }
        } else {
            $this->data['json'] = array('error' => strip_tags(validation_errors()));
        }
        $this->layout(false, 'json');
    }


    function exportCSV()
    {
        $filter = $this->input->get('filter');
        $apply_filters = array();
        $apply_filters['start_date'] = strtotime("-7 days midnight");
        $apply_filters['end_date'] = strtotime("tomorrow midnight") - 1;
        if (!empty($filter['start_date'])) {
            $apply_filters['start_date'] = strtotime(trim($filter['start_date']) . ' 00:00:00');
        } else {
            $filter['start_date'] = date('Y-m-d', $apply_filters['start_date']);
        }

        if (!empty($filter['end_date'])) {
            $apply_filters['end_date'] = strtotime(trim($filter['end_date']) . ' 23:59:59');
        } else {
            $filter['end_date'] = date('Y-m-d', $apply_filters['end_date']);
        }

        if (!empty($filter['pay_method'])) {
            $apply_filters['pay_method'] = $filter['pay_method'];
        }
        if (!empty($filter['courier_id'])) {
            $apply_filters['courier_id'] = $filter['courier_id'];
        }
        if (!empty($filter['seller_id'])) {
            $apply_filters['seller_id'] = $filter['seller_id'];
        }
        if (!empty($filter['manager_id'])) {
            $apply_filters['manager_id'] = $filter['manager_id'];
        }
        if (!empty($filter['status'])) {
            $apply_filters['status'] = $filter['status'];
        }
        if (!empty($filter['order_ids'])) {
            $apply_filters['order_ids'] = array_map('trim', explode(',', $filter['order_ids']));
        }
        if (!empty($filter['awb_no'])) {
            $apply_filters['awb_no'] = array_map('trim', explode(',', $filter['awb_no']));
        }
        if (!empty($filter['ndr_type'])) {
            $apply_filters['ndr_type'] = $filter['ndr_type'];
        }
        if (!empty($filter['ndr_ids'])) {
            $apply_filters['ndr_id'] = array_map('trim', explode(',', $filter['ndr_ids']));
        }

        $this->data['filter'] = $filter;
        $ndrlist = $this->ndr_lib->getNdrCsvdetails($apply_filters);

        $this->load->library('export_db');

        $export = new Export_db('slave');

        $export->query($ndrlist);
        $filename = 'Exceptionsheet_' . time() . '.csv';
        header("Content-Description: File Transfer");
        header("Content-Disposition: attachment; filename=$filename");
        header("Content-Type: application/csv; ");
        $file = fopen('php://output', 'w');
        $header = array(
            "NDR Date", "Order Number", "Seller Name", "Account Manager", "Product Name", "Payment",
            "Payment Type", "Customer Name", "Phone Number", "Address", "City", "State", "Zip Code", "Courier Name", "AWB Number", "Status", "Remarks", "NDR Remark Type", "Last NDR Reson", "Last NDR Date", "Last Seller/Buyer Action Type", "Last Seller/Buyer Action", "Last Action Date", "Last Action By", "Push to Courier", "Push to Courier Response", "Pickup Request Date", "Pickup Date", "Aging From Pickup Date", "Reached At Destination Date", "No. Of Delivery Attempts", "First Delivery Attempt Date", "Last Delivery Attempt Date", "Expected Date of Delivery", "Delivery Date", "RTO Initiated Date", "RTO Delivered Date", "Change Name", "Change Address 1", "Change Address 2", "Change Phone"
        );

        fputcsv($file, $header);
        while ($ndr = $export->next()) {
            $remark_msg = '';
            
            $arrRemarks = $this->addRemarksToCSV($ndr->remarks);

            if (!empty($arrRemarks)) {
                foreach ($arrRemarks as $remarks) {
                    if (strtolower(trim($remarks["customer_details_name"])) == "seller") {
                        $remark_msg .= ($remark_msg != '') ? " | " : '';
                        $remark_msg .= $remarks["remarks"];
                    }
                }
            }

            $row = array(
                date('Y-m-d', $ndr->created),
                $ndr->order_number,
                ucwords($ndr->user_fname . ' ' . $ndr->user_lname),
                ucwords($ndr->manager_fname . ' ' . $ndr->manager_lname),
                $ndr->products,
                $ndr->order_amount,
                $ndr->order_payment_type,
                $ndr->shipping_fname . ' ' . $ndr->shipping_lname,
                $ndr->shipping_phone,
                $ndr->shipping_address,
                $ndr->shipping_city,
                $ndr->shipping_state,
                $ndr->shipping_zip,
                $ndr->courier_name,
                $ndr->awb_number,
                ucwords($ndr->ship_status . (($ndr->ship_status == 'rto' && !empty($ndr->rto_status)) ? ' ' . $ndr->rto_status : '')),
                $remark_msg,
                ucwords($ndr->ndr_action_type),
            );

            if (!empty($ndr->remarks)) {
                $remarksArr = explode("|||||", $ndr->remarks);
                $reqArr = array();
                $remarksArr = array_reverse($remarksArr);
                foreach ($remarksArr as $remark) {
                    $remarkaction = explode("<->", $remark);
                    if (end($remarkaction) == "courier") {

                        $reqArr["courier"] = $remarkaction;
                    }
                    if (end($remarkaction) == "seller" || end($remarkaction) == "buyer") {
                        $reqArr["seller"] = $remarkaction;
                    }
                    if (!empty($reqArr["seller"]) && !empty($reqArr["courier"])) {
                        break;
                    }
                }
                $row[] = (!empty($reqArr["courier"][2])) ? $reqArr["courier"][2] : '';
                $row[] = (!empty($reqArr["courier"][0])) ? date('Y-m-d', $reqArr["courier"][0]) : '';
                $seller_action_type = (!empty($reqArr["seller"][1])) ? $reqArr["seller"][1] : '';
                $row[] = ucwords($seller_action_type);
                $seller_remarks = (!empty($reqArr["seller"][2])) ? $reqArr["seller"][2] : '';
                $seller_action_date = (!empty($reqArr["seller"][0])) ? date('Y-m-d', $reqArr["seller"][0]) : '';
                if ($seller_action_type == 're-attempt') {
                    $seller_remarks =  $seller_action_date . ' ' . $seller_remarks;
                }
                if ($seller_action_type == 'change_address') {
                    $seller_remarks =  $ndr->shipping_address . ' ' . $seller_remarks;
                }
                $row[] = $seller_remarks;
                $row[] = $seller_action_date;
                $row[] = (!empty($reqArr["seller"][6])) ? $reqArr["seller"][6] : '';
                $push_api_status_data = !empty($remarkaction[4]) ? $remarkaction[4] : '0';
                if (trim($push_api_status_data) == '1') {
                    $push_api_status = 'Yes';
                }
                if (trim($push_api_status_data) == '2') {
                    $push_api_status = 'Fail';
                }
                if (trim($push_api_status_data) == '0') {
                    $push_api_status = 'No';
                }
                $row[] = $push_api_status;
                $row[] = (!empty($remarkaction[5])) ?  $remarkaction[5] : '';
                $pickup_request_time = (!empty($ndr->pickupRequest)) ? date('Y-m-d', $ndr->pickupRequest) : '';
                $row[] = $pickup_request_time;
                $row[] = (!empty($ndr->pickupTime)) ? date('Y-m-d', $ndr->pickupTime) : '';

                $aging = '0';
                if (!empty($ndr->pickupTime)) {
                    $datetime1 = new DateTime(date('Y-m-d', $ndr->pickupTime));

                    $datetime2 = new DateTime();

                    $difference = $datetime1->diff($datetime2);

                    $aging = $difference->days;
                }

                $row[] = (!empty($ndr->pickupTime)) ? $aging : '';

                $row[] = (!empty($ndr->reached_at_destination_hub)) ? date('Y-m-d', $ndr->reached_at_destination_hub) : ''; //reached_at_destination_hub
                $row[] = !empty($ndr->delivery_attempt_count) ? $ndr->delivery_attempt_count : (!empty($ndr->total_ofd_attempts) ? $ndr->total_ofd_attempts : '');
                $row[] = (!empty($ndr->ofd_attempt_1_date)) ? date('Y-m-d', $ndr->ofd_attempt_1_date) : '';
                $row[] = (!empty($ndr->last_attempt_date)) ? date('Y-m-d', $ndr->last_attempt_date) : '';
                $row[] = (!empty($ndr->expected_delivery_date)) ? date('Y-m-d', $ndr->expected_delivery_date) : '';
                $row[] = (!empty($ndr->delivered_time)) ? date('Y-m-d', $ndr->delivered_time) : '';
                $row[] = (!empty($ndr->rto_mark_date)) ? date('Y-m-d', $ndr->rto_mark_date) : '';
                $row[] = (!empty($ndr->rto_delivered_date)) ? date('Y-m-d', $ndr->rto_delivered_date) : '';
                $row[] = (!empty($ndr->details_name)) ? trim($ndr->details_name) : '';
                $row[] = (!empty($ndr->details_address_1)) ? trim($ndr->details_address_1) : '';
                $row[] = (!empty($ndr->details_address_2)) ? trim($ndr->details_address_2) : '';
                $row[] = (!empty($ndr->contact_phone)) ? trim($ndr->contact_phone) : '';
            }

            fputcsv($file, $row);
        }
        fclose($file);
        exit;
    }

    private function addRemarksToCSV($grouped_remarks = array())
    {
        $remarks = array();
        if (!empty($grouped_remarks)) {
            $all_remarks =   explode("|||||", $grouped_remarks);
            if (!empty($all_remarks)) {
                foreach ($all_remarks as $single_remark) {
                    $single_remark = explode('<->', $single_remark);
                    $remarks[] = array(
                        'created' => (!empty($single_remark[0])) ? $single_remark[0] : '',
                        'action' => (!empty($single_remark[1])) ? $single_remark[1] : '',
                        'remarks' => (!empty($single_remark[2])) ? $single_remark[2] : '',
                        'attempt' => (!empty($single_remark[3])) ? $single_remark[3] : '',
                        'source' => (!empty($single_remark[4])) ? $single_remark[4] : '',
                        're_attempt_date' => (!empty($single_remark[5])) ? $single_remark[5] : '',
                        'customer_details_name' => (!empty($single_remark[6])) ? $single_remark[6] : '',
                        'customer_details_address_1' => (!empty($single_remark[7])) ? $single_remark[7] : '',
                        'customer_details_address_2' => (!empty($single_remark[8])) ? $single_remark[8] : '',
                        'customer_contact_phone' => (!empty($single_remark[9])) ? $single_remark[9] : '',
                        'push_ndr_status' => (!empty($single_remark[10])) ? $single_remark[10] : '',
                        'push_ndr_message' => (!empty($single_remark[11])) ? $single_remark[11] : '',
                    );
                }
                array_multisort(array_column($remarks, 'created'), SORT_DESC, $remarks);
            }
        }
        return $remarks;
    }

        
    function action()
{
    $this->load->library('form_validation');
    //$this->load->library('callcenter_lib');
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
            $action_id = $this->ndr_lib->Add_NDR_Action($update);

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
}
