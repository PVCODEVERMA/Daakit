<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Billing extends User_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->load->library('payment_lib');
        $this->userHasAccess('billing');
    }

    function index()
    {
        self::version();
    }

    function version($page = 'seller_price_calculator', $page_no = 1)
    {
        $inner_content = '';
        switch ($page) {
            case 'seller_price_calculator':
                $inner_content = $this->seller_price_calculator();
                break;
            case 'seller_cod_remittance':
                $inner_content = $this->seller_cod_remittance();
                break;
            case 'seller_recharge_logs':
                $inner_content = $this->seller_recharge_logs($page_no);
                break;
            case 'seller_shipping_charges':
                $inner_content = $this->seller_shipping_charges($page_no);
                break;
            case 'seller_invoice':
                $inner_content = $this->seller_invoice($page_no);
                break;
            case 'seller_credit_notes':
                $inner_content = $this->seller_credit_notes($page_no);
                break;
            case 'seller_weight_reconciliation':
                $inner_content = $this->seller_weight_reconciliation($page_no);
                break;
            case 'b2b_seller_price_calculator':
                $inner_content = $this->b2b_seller_price_calculator();
                break;
            case 'int_seller_price_calculator':
                $inner_content = $this->int_seller_price_calculator();
                break;
            default:
        }

        $this->data['inner_content'] = $inner_content;

        $this->data['view_page'] = $page;
        $this->layout('billing/view');
    }

    function rechage_wallet()
    {
        return $this->layout('billing/recharge_form');
    }

    function seller_weight_reconciliation($page = 1)
    {
        $this->session->set_flashdata('error', 'You can access weight reconciliation directly from left side menu');
        redirect('weight', true);

        $this->load->library('weight_lib');
        $this->load->library('pricing_lib');

        $this->load->library('courier_lib');
        $couriers = $this->courier_lib->showingToUsers();

        $this->data['couriers'] = $couriers;

        $this->data['dispute_time_limit'] = $this->config->item('weight_dispute_time_limit');
        $limit = 50;

        $filter = $this->input->get('filter');
        $apply_filters = array();

        $apply_filters['start_date'] = strtotime("-30 days midnight");
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

        if (!empty($filter['awb_no'])) {
            $apply_filters['awb_no'] = array_map('trim', explode(',', $filter['awb_no']));
        }

        if (!empty($filter['product_name'])) {
            $apply_filters['product_name'] = $filter['product_name'];
        }

        if (!empty($filter['courier_id'])) {
            $apply_filters['courier_id'] = $filter['courier_id'];
        }


        $total_row = $this->weight_lib->countWeightUploadHistory($this->user->account_id, $apply_filters);
        $config = array(
            'base_url' => base_url('billing/version/seller_weight_reconciliation'),
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
        $history = $this->weight_lib->getWeightUploadHistory($this->user->account_id, $limit, $offset, $apply_filters);

        $this->data['records'] = $history;


        $this->data['filter'] = $filter;

        return $this->load->view('billing/pages/weight_reconciliation', $this->data, true);
    }


    function weight_rec_export()
    {
        $this->load->library('weight_lib');
        $this->load->library('pricing_lib');

        $disbute_time = $this->weight_lib->get_dispute_time_limit($this->user->account_id);
        if(!empty($disbute_time))
        {
            $weight_dispute_time_limit=$disbute_time->time_limt * 60 * 60 * 24 ;
        }
        else
        {
            $weight_dispute_time_limit=$this->config->item('weight_dispute_time_limit');
        }

        $this->data['dispute_time_limit'] = $weight_dispute_time_limit;
        $limit = 50;

        $filter = $this->input->get('filter');
        $apply_filters = array();

        $apply_filters['start_date'] = strtotime("-30 days midnight");
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

        if (!empty($filter['awb_no'])) {
            $apply_filters['awb_no'] = array_map('trim', explode(',', $filter['awb_no']));
        }

        if (!empty($filter['product_name'])) {
            $apply_filters['product_name'] = $filter['product_name'];
        }

        if (!empty($filter['courier_id'])) {
            $apply_filters['courier_id'] = $filter['courier_id'];
        }

        $history = $this->weight_lib->getWeightUploadHistory($this->user->account_id, 10000, 0, $apply_filters);

        $filename = 'Weight_Reconciliation' . time() . rand(1111, 9999) . '.csv';
        header("Content-Description: File Transfer");
        header("Content-Disposition: attachment; filename=$filename");
        header("Content-Type: application/csv; ");
        $file = fopen('php://output', 'w');
        $header = array("Weight Applied Date", "Courier", "AWB Number", "Entered Weight", "Entered LxBxH", "Applied Weight", "Products");
        fputcsv($file, $header);
        foreach ($history as $his) {
            $row = array(
                date('Y-m-d', $his->weight_applied_date),
                ucwords($his->courier_name),
                $his->awb_number,
                ((!empty($his->package_weight)) ? $his->package_weight : '0') . 'g',
                ((!empty($his->package_length)) ? $his->package_length : '10') . 'x' . ((!empty($his->package_breadth)) ? $his->package_breadth : '10') . 'x' . ((!empty($his->package_height)) ? $his->package_height : '10'),
                $his->charged_weight . 'g',
                ucwords($his->product_name),
            );
            fputcsv($file, $row);
        }
        fclose($file);
        exit;
    }


    function seller_invoice($page = 1)
    {

        $this->load->library('invoice_lib');
        $limit = 50;
        $filter = $this->input->get('filter');
        $apply_filters = array();

        $total_row = $this->invoice_lib->countInvoice($this->user->account_id, $apply_filters);
        $config = array(
            'base_url' => base_url('billing/version/invoice'),
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
        $invoices = $this->invoice_lib->fetchInvocie($this->user->account_id, $limit, $offset, $apply_filters);

        $this->data['invoices'] = $invoices;
        $this->data['filter'] = $filter;
        return $this->load->view('billing/pages/invoices', $this->data, true);
    }

    function seller_credit_notes($page = 1)
    {
        $this->load->library('invoice_lib');
        $limit = 50;
        $filter = $this->input->get('filter');
        $apply_filters = array();

        $total_row = $this->invoice_lib->countCreditNotes($this->user->account_id, $apply_filters);
        $config = array(
            'base_url' => base_url('billing/version/seller_credit_notes'),
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
        $invoices = $this->invoice_lib->fetchCreditNotes($this->user->account_id, $limit, $offset, $apply_filters);

        $this->data['invoices'] = $invoices;
        $this->data['filter'] = $filter;
        return $this->load->view('billing/pages/credit_notes', $this->data, true);
    }

    function seller_price_calculator()
    {
        $this->load->library('user_lib');
        $user = $this->user_lib->getByID($this->user->account_id);

        $this->load->library('plans_lib');
        $plan = $this->plans_lib->getPlanByName($user->pricing_plan);

        if($plan->plan_type == 'smart') {
            $custom_plan_details = $this->plans_lib->getSmartPlanById($plan->id, '1');

            $couriers = [];
            foreach ($custom_plan_details as $custom_plan_detail) {
                if($custom_plan_detail->courier_type_weight) {
                    $custom_courier = new stdClass();

                    $courier = explode("_", $custom_plan_detail->courier_type_weight);

                    $custom_courier->id = 0;
                    $custom_courier->courier_type = $courier[0];
                    $custom_courier->name = ucfirst($courier[0]) . ' ' . round($courier[1]/1000, 2) . ' kg';
                    $custom_courier->weight = $courier[1];
                    $custom_courier->additional_weight = $courier[2];

                    $couriers[] = $custom_courier;
                }
            }
        } else {
            $this->load->library('courier_lib');
            $couriers = $this->courier_lib->userAvailableCouriers($this->user->account_id);
        }

        $this->load->library('country_lib');
        $countries = $this->country_lib->getAllCountry();

        $this->data['countries'] = $countries;

        $this->data['plan'] = $plan;
        $this->data['couriers'] = $couriers;
        $this->data['user'] = $user;

        return $this->load->view('billing/pages/price_calculator', $this->data, true);
    }

    function calculate_pricing()
    {
        $this->load->library('form_validation');
        $config = array(
            array(
                'field' => 'origin',
                'label' => 'Origin Pincode',
                'rules' => 'trim|required|exact_length[6]|numeric'
            ),
            array(
                'field' => 'destination',
                'label' => 'Destination Pincode',
                'rules' => 'trim|required|exact_length[6]|numeric'
            ),
            array(
                'field' => 'weight',
                'label' => 'Weight',
                'rules' => 'trim|required|numeric|greater_than[0]'
            ),
            array(
                'field' => 'cod',
                'label' => 'COD',
                'rules' => 'trim|required|in_list[yes,no]'
            ),
        );
        if ($this->input->post('cod') == 'yes') {
            $config[] = array(
                'field' => 'cod_amount',
                'label' => 'COD Amount',
                'rules' => 'trim|required|numeric'
            );
        }
        $this->form_validation->set_rules($config);
        if ($this->form_validation->run()) {

            $this->load->library('user_lib');
            $user = $this->user_lib->getByID($this->user->account_id);

            $origin = $this->input->post('origin');
            $destination = $this->input->post('destination');
            $weight = $this->input->post('weight') * 1000;
            $length = $this->input->post('length');
            $height = $this->input->post('height');
            $breadth = $this->input->post('breadth');

            $cod = $this->input->post('cod');
            if ($cod == 'yes') {
                $order_type = 'cod';
            } else {
                $order_type = 'prepaid';
            }
            $cod_amount = $this->input->post('cod_amount');

            $this->load->library('pricing_lib');

            $this->load->library('courier_lib');
            $user_couriers = $this->courier_lib->userAvailableCouriers($this->user->account_id);

            //check pin code serviceblity
            $this->load->library('pincode_lib');

            $couriers = $this->pincode_lib->getPincodeService($destination, $order_type);

            $return = array();

            if (!empty($couriers)) { //get courier price
                $this->load->library('plans_lib');
                $custom_plan = $this->plans_lib->getCustomPlanByName($user->pricing_plan);

                foreach ($couriers as $key => $courier) {
                    $courier->courier_type_weight = $courier->courier_type . '_' . $courier->weight . '_' . $courier->additional_weight;

                    if (!array_key_exists($courier->id, $user_couriers)) {
                        unset($couriers[$key]);
                        continue;
                    }

                    if (!$this->pincode_lib->checkPickupServiceByCourier($origin, $courier->id)) {
                        unset($couriers[$key]);
                        continue;
                    }

                    if(empty($custom_plan)) {
                        $pricing = new Pricing_lib();
                        $pricing->setPlan($user->pricing_plan);
                        $pricing->setCourier($courier->id);
                        $pricing->setOrigin($origin);
                        $pricing->setDestination($destination);
                        $pricing->setType($order_type);
                        $pricing->setAmount($cod_amount);
                        $pricing->setWeight($weight);
                        $pricing->setLength($length);
                        $pricing->setBreadth($breadth);
                        $pricing->setHeight($height);
                        $price = $pricing->calculateCost();

                        if (empty($price['total'])) {
                            unset($couriers[$key]);
                            continue;
                        }

                        $courier->courier_charges = round(($price['courier_charges'] / 1.18),2);
                        $courier->cod_charges = round(($price['cod_charges'] / 1.18),2);
                        $courier->total_price = round(($price['total'] / 1.18),2);
                        $return[] = $courier;
                    }
                }

                if($custom_plan) {
                    $return = array();

                    $custom_plan_details_arr = [];
                    $custom_plan_details = $this->plans_lib->getSmartPlanById($custom_plan->id, '1');
                    foreach ($custom_plan_details as $custom_plan_detail) {
                        $custom_plan_details_arr[] = $custom_plan_detail->courier_type_weight;
                    }

                    $couriers = array_values(array_unique(array_column($couriers, 'courier_type_weight')));
                    sort($couriers, SORT_NATURAL | SORT_FLAG_CASE);

                    $couriers = array_intersect($couriers, $custom_plan_details_arr);

                    foreach ($couriers as $c_key => $courier) {
                        $custom_courier = new stdClass();
                        $custom_courier->courier = $courier;

                        $courier = explode("_", $courier);

                        $courier_type = $courier[0];
                        $courier_weight = $courier[1];
                        $courier_additional_weight = $courier[2];

                        $pricing = new Pricing_lib();
                        $pricing->setPlan($user->pricing_plan);
                        $pricing->setCourier(0);
                        $pricing->setOrigin($origin);
                        $pricing->setDestination($destination);
                        $pricing->setType($order_type);
                        $pricing->setAmount($cod_amount);
                        $pricing->setWeight($weight);
                        $pricing->setLength($length);
                        $pricing->setBreadth($breadth);
                        $pricing->setHeight($height);
                        $pricing->setCourierType($courier_type);
                        $pricing->setCourierWeight($courier_weight);
                        $pricing->setCourierAdditionalWeight($courier_additional_weight);
                        $pricing->setCourierVolumetricDivisor(5000);

                        $price = $pricing->calculateCost();
                        
                        if (empty($price['total'])) {
                            unset($couriers[$key]);
                            continue;
                        }

                        $custom_courier->id = 0;
                        $custom_courier->courier_type = $courier[0];
                        $custom_courier->name = ucfirst($courier[0]) . ' ' . round($courier[1]/1000, 2) . ' kg';
                        $custom_courier->weight = $courier[1];
                        $custom_courier->additional_weight = $courier[2];
                        $custom_courier->courier_charges = round(($price['courier_charges'] / 1.18),2);
                        $custom_courier->cod_charges = round(($price['cod_charges'] / 1.18),2);
                        $custom_courier->total_price = round(($price['total'] / 1.18),2);
                        $return[] = $custom_courier;
                    }
                }

                $this->data['json'] = array('success' => $return);
            } else {
                $this->data['json'] = array('error' => 'Delivery pincode is not serviceable');
            }
        } else {
            $this->data['json'] = array('error' => strip_tags(validation_errors()));
        }

        $this->layout(false, 'json');
    }

    function create_payment()
    {
        $this->load->library('form_validation');
        $config = array(
            array(
                'field' => 'amount',
                'label' => 'Amount',
                'rules' => 'trim|required|min_length[1]|max_length[10]|numeric'
            ),
        );
        $this->form_validation->set_rules($config);
        if ($this->form_validation->run()) {

            $amount = $this->input->post('amount');
            $coupon_code_apply = $this->input->post('coupon_code_apply');
            $payment_mode = $this->input->post('payment_mode');
            if ($amount >= 200) {
                $this->load->library('payment_lib');
                $payment_id = $this->payment_lib->createUserPayment($this->user->account_id, $amount,$payment_mode);
                if ($coupon_code_apply) {  // coupon check and apply coupons
                    $this->load->library('admin/coupon_lib');
                    $coupon = $this->coupon_lib->isCouponCodeCheck($this->user->account_id, $amount, $coupon_code_apply,$payment_mode);
                    if ($coupon) {
                        $coupon_amount = $this->coupon_lib->couponTrackingAmount($this->user->account_id, $amount, $coupon, $payment_id); //save
                        if ($coupon->coupon_type != 'extra credit') {
                            $amount = ($amount - $coupon_amount);
                            $this->payment_lib->updateUserPayment($payment_id, $amount); //update payment table 
                        }
                    }
                }
                $this->data['json'] = array(
                    'success' => array(
                        'payment_id' => $payment_id,
                        'amount' => $amount,
                        'payment_mode' => $payment_mode,
                    )
                );
            } else {
                $this->data['json'] = array('error' => 'Minimum Recharge Amount is Rs.200');
            }
        } else {
            $this->data['json'] = array('error' => strip_tags(validation_errors()));
        }
        $this->layout(false, 'json');
    }

    function create_payment_hdfc()
    {
        $this->load->library('form_validation');
        $config = array(
            array(
                'field' => 'amount',
                'label' => 'Amount',
                'rules' => 'trim|required|min_length[1]|max_length[10]|numeric'
            ),
        );
        $this->form_validation->set_rules($config);
        if ($this->form_validation->run()) {

            $amount = $this->input->post('amount');
            $coupon_code_apply = $this->input->post('coupon_code_apply');
            $payment_mode ='hdfc_razorpay';

            if ($amount >= 200) {
                $this->load->library('payment_lib');
                $curl = curl_init();
                
                curl_setopt_array($curl, array(
                  CURLOPT_URL => 'https://api.razorpay.com/v1/orders/',
                  CURLOPT_RETURNTRANSFER => true,
                  CURLOPT_ENCODING => '',
                  CURLOPT_MAXREDIRS => 10,
                  CURLOPT_TIMEOUT => 0,
                  CURLOPT_FOLLOWLOCATION => true,
                  CURLOPT_USERPWD => $this->config->item('hdfc_razorpay_key') . ":" . $this->config->item('hdfc_razorpay_secret'),
                  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                  CURLOPT_CUSTOMREQUEST => 'POST', 
                  CURLOPT_POSTFIELDS => array('amount' => $amount*100,'currency' => 'INR','receipt' => time(),'payment_capture' => '1'),
                 
                ));
                
                $response = curl_exec($curl);
                $res=json_decode($response);
                curl_close($curl);

              $payment_id = $this->payment_lib->createUserPaymenthdfc($this->user->account_id, $amount,$payment_mode);
                if ($coupon_code_apply) {  // coupon check and apply coupons
                    $this->load->library('admin/coupon_lib');
                    $coupon = $this->coupon_lib->isCouponCodeCheck($this->user->account_id, $amount, $coupon_code_apply,$payment_mode);
                    if ($coupon) {
                        $coupon_amount = $this->coupon_lib->couponTrackingAmount($this->user->account_id, $amount, $coupon, $payment_id); //save
                        if ($coupon->coupon_type != 'extra credit') {
                            $amount = ($amount - $coupon_amount);
                            $this->payment_lib->updateUserPayment($payment_id, $amount); //update payment table 
                        }
                    }
                }

                $this->load->library('user_lib');
                $user_details = $this->user_lib->getByID($this->user->account_id);

                $this->data['json'] = array(
                    'success' => array(
                        'payment_id' => $payment_id,
                        'amount' => $amount,
                        'payment_mode' => $payment_mode,
                        'order_id'=> $res->id,
                        'callback_url'=> base_url('payment/api_response/').$payment_id,
                        'user_name'=> ucfirst($user_details->fname . ' ' . $user_details->lname),
                        "user_email"=>$user_details->email,
                        "user_contact"=>$user_details->phone,
                        "transaction_id"=>time(),
                        "hdfc_razorpay_key"=> $this->config->item('hdfc_razorpay_key')
                    )
                );
            } else {
                $this->data['json'] = array('error' => 'Minimum Recharge Amount is Rs.200');
            }
        } else {
            $this->data['json'] = array('error' => strip_tags(validation_errors()));
        }
        $this->layout(false, 'json');
    }

    function neft_payment()
    {
        $this->load->library('form_validation');
        $config = array(
            array(
                'field' => 'amount',
                'label' => 'Amount',
                'rules' => 'trim|required|min_length[1]|max_length[10]|numeric'
            ),
            array(
                'field' => 'utr_number',
                'label' => 'UTR Number',
                'rules' => 'trim|required|min_length[1]|max_length[50]'
            ),
        );
        $this->form_validation->set_rules($config);
        if ($this->form_validation->run()) {

            $amount = $this->input->post('amount');
            $utr = $this->input->post('utr_number');

            $save = array(
                'user_id' => $this->user->account_id,
                'amount' => $amount,
                'utr_number' => $utr
            );
            $this->payment_lib->saveNeftPayment($save);

            $this->session->set_flashdata('success', 'Payment details received');
            redirect(base_url('dash'));
        } else {
            $this->data['error'] = validation_errors();
        }

        $this->session->set_flashdata('error', $this->data['error']);
        redirect(base_url('dash'));
    }

    function seller_recharge_logs($page = 1)
    {
        $this->load->library('wallet_lib');

        $limit = 25;

        $filter = $this->input->post('filter');
        // if($this->input->post('perPage'))
        //     $page = $this->input->post('perPage');

        $apply_filters = array();

        $apply_filters['start_date'] = strtotime("-30 days midnight");
        $apply_filters['end_date'] = strtotime("tomorrow midnight") - 1;
        //echo $filter['txn_for'];exit;
        if (!empty($filter['txn_for'])) {
            switch ($filter['txn_for']) {
                case 'shipment_refund':
                    $apply_filters['txn_for'] = 'shipment';
                    $apply_filters['txn_ref'] = 'refund';
                    break;
                case 'ivr_number':
                    $apply_filters['txn_for'] = 'addon';
                    $apply_filters['txn_ref'] = 'ivr_number';
                    break;
                case 'ivr_call':
                    $apply_filters['txn_for'] = 'addon';
                    $apply_filters['txn_ref'] = 'ivr_call';
                    break;  
                case 'whatsapp':
                    $apply_filters['txn_for'] = 'whatsapp';
                    break;        
                case 'email':
                    $apply_filters['txn_for'] = 'email';
                    break;        
                case 'sms':
                    $apply_filters['txn_for'] = 'sms';
                    break;
                case 'all_communication':
                    $apply_filters['txn_for'] = 'all_communication';
                    break;     
                default:
                    $apply_filters['txn_for'] = $filter['txn_for'];
            }
        }

        if (!empty($filter['shipment_id'])) {
            $apply_filters['shipment_id'] = $filter['shipment_id'];
        }

        if (!empty($filter['start_date']))
            $apply_filters['start_date'] = strtotime(trim($filter['start_date']) . ' 00:00:00');
        else
            $filter['start_date'] = date('Y-m-d', $apply_filters['start_date']);


        if (!empty($filter['end_date']))
            $apply_filters['end_date'] = strtotime(trim($filter['end_date']) . ' 23:59:59');
        else
            $filter['end_date'] = date('Y-m-d', $apply_filters['end_date']);

        if (!empty($filter['awb_no'])) {
            $apply_filters['awb_no'] = array_map('trim', explode(',', $filter['awb_no']));
        }

        $total_row = $this->wallet_lib->countByUserID($this->user->account_id, $apply_filters);

        $config = array(
            'base_url' => base_url('billing/version/seller_recharge_logs'),
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
        $history = $this->wallet_lib->fetchByUserID($this->user->account_id, $limit, $offset, $apply_filters);
        $this->data['history'] = $history;

        $this->data['filter'] = $filter;

        $wallet_balance = $this->wallet_lib->getWalletBalance($this->user->account_id);

        $this->data['wallet_balance'] = $wallet_balance;

        return $this->load->view('billing/pages/recharge_logs', $this->data, true);
    }

    function recharge_logs_export()
    {
        $this->load->library('wallet_lib');

        $filter = $this->input->get('filter');

        $apply_filters = array();

        $apply_filters['start_date'] = strtotime("-30 days midnight");
        $apply_filters['end_date'] = strtotime("tomorrow midnight") - 1;


        if (!empty($filter['txn_for'])) {
            switch ($filter['txn_for']) {
                case 'shipment_refund':
                    $apply_filters['txn_for'] = 'shipment';
                    $apply_filters['txn_ref'] = 'refund';
                    break;
                case 'ivr_number':
                    $apply_filters['txn_for'] = 'addon';
                    $apply_filters['txn_ref'] = 'ivr_number';
                    break;
                case 'whatsapp':
                    $apply_filters['txn_for'] = 'whatsapp';
                    break;
                case 'ivr_call':
                    $apply_filters['txn_for'] = 'addon';
                    $apply_filters['txn_ref'] = 'ivr_call';
                    break;     
                default:
                    $apply_filters['txn_for'] = $filter['txn_for'];
            }
        }

        if (!empty($filter['shipment_id'])) {
            $apply_filters['shipment_id'] = $filter['shipment_id'];
        }

        if (!empty($filter['start_date']))
            $apply_filters['start_date'] = strtotime(trim($filter['start_date']) . ' 00:00:00');
        else
            $filter['start_date'] = date('Y-m-d', $apply_filters['start_date']);


        if (!empty($filter['end_date']))
            $apply_filters['end_date'] = strtotime(trim($filter['end_date']) . ' 23:59:59');
        else
            $filter['end_date'] = date('Y-m-d', $apply_filters['end_date']);

        if (!empty($filter['awb_no'])) {
            $apply_filters['awb_no'] = array_map('trim', explode(',', $filter['awb_no']));
        }

        $history = $this->wallet_lib->fetchByUserID($this->user->account_id, 20000, 0, $apply_filters);

        $filename = 'seller_recharge_logs_' . time() . rand(1111, 9999) . '.csv';
        header("Content-Description: File Transfer");
        header("Content-Disposition: attachment; filename=$filename");
        header("Content-Type: application/csv; ");
        $file = fopen('php://output', 'w');
        $header = array("DATE", "TXN TYPE", "REF NO#", "TRANSACTION ID", "CREDIT(₹)", "DEBIT(₹)", "CLOSING BALANCE(₹)", "DESCRIPTION");
        fputcsv($file, $header);
        foreach ($history as $his) {
            switch ($his->txn_for) {
                case 'shipment':
                    $txn_type = 'Shipping';
                    break;
                case 'credits':
                    $txn_type = 'Credit Note';
                    break;
                case 'cod':
                    $txn_type = 'COD Adjustments';
                    break;
                case 'neft':
                    $txn_type = 'Recharge - NEFT';
                    break;
                case 'recharge':
                    $txn_type = 'Recharge - Gateway';
                    break;
                case 'promotion':
                    $txn_type = 'Promotion';
                    break;
                case 'whatsapp':
                    $txn_type = 'Whatsapp';
                    break;
                case 'email':
                    $txn_type = 'Email';
                    break;
                case 'sms':
                    $txn_type = 'SMS';
                    break;
                case 'ivr':
                    $txn_type = 'IVR';
                    break;     
                default:
                    $txn_type = '-';
            };

            $row = array(
                (!empty($his->created)) ? date('M d, Y', $his->created) : '',
                $txn_type,
                ($his->txn_for == 'shipment') ? $his->awb_number : '-',
                '#' . $his->id,
                ($his->type == 'credit') ? round($his->amount, 2) : '-',
                ($his->type == 'debit') ? round($his->amount, 2) : '-',
                round($his->balance_after, 2),
                ucwords($his->notes),
            );
            fputcsv($file, $row);
        }
        fclose($file);
        exit;
    }

    function communications_recharge_logs_export()
    {
        $this->load->library('wallet_lib');

        $filter = $this->input->get('filter');

        $apply_filters = array();

        $apply_filters['start_date'] = strtotime("-30 days midnight");
        $apply_filters['end_date'] = strtotime("tomorrow midnight") - 1;


        if (!empty($filter['txn_for'])) {
            switch ($filter['txn_for']) {
                case 'shipment_refund':
                    $apply_filters['txn_for'] = 'shipment';
                    $apply_filters['txn_ref'] = 'refund';
                    break;
                case 'ivr_number':
                    $apply_filters['txn_for'] = 'addon';
                    $apply_filters['txn_ref'] = 'ivr_number';
                    break;
                case 'ivr_call':
                    $apply_filters['txn_for'] = 'addon';
                    $apply_filters['txn_ref'] = 'ivr_call';
                    break;          
                default:
                    $apply_filters['txn_for'] = $filter['txn_for'];
            }
        }

        if (!empty($filter['shipment_id'])) {
            $apply_filters['shipment_id'] = $filter['shipment_id'];
        }

        if (!empty($filter['start_date']))
            $apply_filters['start_date'] = strtotime(trim($filter['start_date']) . ' 00:00:00');
        else
            $filter['start_date'] = date('Y-m-d', $apply_filters['start_date']);


        if (!empty($filter['end_date']))
            $apply_filters['end_date'] = strtotime(trim($filter['end_date']) . ' 23:59:59');
        else
            $filter['end_date'] = date('Y-m-d', $apply_filters['end_date']);

        if (!empty($filter['awb_no'])) {
            $apply_filters['awb_no'] = array_map('trim', explode(',', $filter['awb_no']));
        }

        // $history = $this->wallet_lib->fetchByUserIDCommunication($this->user->account_id, 20000, 0, $apply_filters);

        // $filename = 'seller_recharge_logs_' . time() . rand(1111, 9999) . '.csv';
        // header("Content-Description: File Transfer");
        // header("Content-Disposition: attachment; filename=$filename");
        // header("Content-Type: application/csv; ");
        // $file = fopen('php://output', 'w');
        // $header = array("DATE", "TXN TYPE", "REF NO#", "TRANSACTION ID", "CREDIT(₹)", "DEBIT(₹)", "CLOSING BALANCE(₹)", "DESCRIPTION");
        // fputcsv($file, $header);
        // foreach ($history as $his) {
        //     switch ($his->txn_for) {
        //         case 'shipment':
        //             $txn_type = 'Shipping';
        //             break;
        //         case 'credits':
        //             $txn_type = 'Credit Note';
        //             break;
        //         case 'cod':
        //             $txn_type = 'COD Adjustments';
        //             break;
        //         case 'neft':
        //             $txn_type = 'Recharge - NEFT';
        //             break;
        //         case 'recharge':
        //             $txn_type = 'Recharge - Gateway';
        //             break;
        //         case 'promotion':
        //             $txn_type = 'Promotion';
        //             break;     
        //         case 'whatsapp':
        //             $txn_type = 'Whatsapp';
        //             break;
        //         case 'sms':
        //             $txn_type = 'SMS';
        //             break;
        //         case 'email':
        //             $txn_type = 'Email';
        //             break;
        //         default:
        //             $txn_type = '-';
        //     };

        //     $row = array(
        //         (!empty($his->created)) ? date('M d, Y', $his->created) : '',
        //         $txn_type,
        //         ($his->txn_for == 'shipment') ? $his->awb_number : '-',
        //         '#' . $his->id,
        //         ($his->type == 'credit') ? round($his->amount, 2) : '-',
        //         ($his->type == 'debit') ? round($his->amount, 2) : '-',
        //         round($his->balance_after, 2),
        //         ucwords($his->notes),
        //     );
        //     fputcsv($file, $row);
        // }
        // fclose($file);
        // exit;

        $history = $this->wallet_lib->fetchByUserIDCommunication($this->user->account_id, 20000, 0, $apply_filters);

        $filename = 'seller_recharge_logs_' . time() . rand(1111, 9999) . '.csv';

        header("Content-Description: File Transfer");
        header("Content-Disposition: attachment; filename=$filename");
        header("Content-Type: application/csv; ");

        $file = fopen('php://output', 'w');

        // Define header row for CSV
        $header = array(
            "ORDER NUMBER",
            "AWB NUMBER",
            "RESPONSE (IF ANY)",
            "SENT AT",
            "DELIVERED AT",
            "READ AT",
            "DELIVERY STATUS",
            "REMARKS",
            "AGING SECONDS",
            "REFERENCE ID",
            "TXN FOR",
            "TXN REF",
            "PACK TYPE",
            "TYPE",
            "NOTES",
            "BALANCE BEFORE",
            "AMOUNT",
            "BALANCE AFTER"
        );
        fputcsv($file, $header);

        // Loop through results
        foreach ($history as $his) {
            $row = array(
                $his->order_number,
                $his->awb_number,
                $his->response,
                $his->sent_at,
                $his->delivered_at,
                $his->read_at,
                $his->delivery_status,
                $his->remarks,
                $his->aging_seconds,
                $his->ref_id,
                $his->txn_for,
                $his->txn_ref,
                $his->pack_type,
                $his->type,
                $his->notes,
                round($his->balance_before, 2),
                round($his->amount, 2),
                round($his->balance_after, 2)
            );
            fputcsv($file, $row);
        }

        fclose($file);
        exit;
    }

    function billing_dues($page = 1)
    {

        $this->load->library('dues_lib');

        $last_txn = $this->dues_lib->userLastTransaction($this->user->account_id);

        $limit = 50;

        $filter = $this->input->get('filter');

        $total_row = $this->dues_lib->countByUserID($this->user->account_id);
        $config = array(
            'base_url' => base_url('billing/version/billing_dues'),
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
        $history = $this->dues_lib->fetchByUserID($this->user->account_id, $limit, $offset);
        $this->data['history'] = $history;

        $this->data['filter'] = $filter;

        $this->data['last_txn'] = $last_txn;

        return $this->load->view('billing/pages/billing_dues', $this->data, true);
    }

    function seller_shipping_charges($page = 1)
    {
        $this->load->library('shipping_lib');

        $limit = 50;

        $filter = $this->input->get('filter');
        $apply_filters = array();



        if (!empty($filter['awb_no'])) {
            $apply_filters['awb_no'] = array_map('trim', explode(',', $filter['awb_no']));
        }

        if (!empty($filter['start_date']))
            $apply_filters['start_date'] = strtotime(trim($filter['start_date']) . ' 00:00:00');



        if (!empty($filter['end_date']))
            $apply_filters['end_date'] = strtotime(trim($filter['end_date']) . ' 23:59:59');


        $total_row = $this->shipping_lib->countByUserID($this->user->account_id, $apply_filters);
        $config = array(
            'base_url' => base_url('billing/version/seller_shipping_charges'),
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
        $history = $this->shipping_lib->getByUserID($this->user->account_id, $limit, $offset, $apply_filters);
        $this->data['history'] = $history;


        $this->data['filter'] = $filter;

        return $this->load->view('billing/pages/shipping_charges', $this->data, true);
    }

    function seller_shipping_charges_export()
    {
        $this->load->library('shipping_lib');

        $filter = $this->input->get('filter');
        $apply_filters = array();


        if (!empty($filter['awb_no'])) {
            $apply_filters['awb_no'] = array_map('trim', explode(',', $filter['awb_no']));
        }

        if (!empty($filter['start_date']))
            $apply_filters['start_date'] = strtotime(trim($filter['start_date']) . ' 00:00:00');



        if (!empty($filter['end_date']))
            $apply_filters['end_date'] = strtotime(trim($filter['end_date']) . ' 23:59:59');


        $this->data['filter'] = $filter;
        $history = $this->shipping_lib->getByUserID($this->user->account_id, 10000, 0, $apply_filters);

        $filename = 'seller_shipping_charges_' . time() . rand(1111, 9999) . '.csv';
        header("Content-Description: File Transfer");
        header("Content-Disposition: attachment; filename=$filename");
        header("Content-Type: application/csv; ");
        $file = fopen('php://output', 'w');
        $header = array("Shipment Date","Products", "Total QTY", "Courier", "AWB", "Status", "Freight Charges", "COD Charges", "ENTERED WGT(KG)", "APPLIED WGT(KG)", "Extra Weight(KG)", "EXTRA FREIGHT CHARGES", "RTO Charges", "COD Charge Reversed", "RTO Extra Wgt Charges", "TOTAL CHARGES");
        fputcsv($file, $header);
        foreach ($history as $his) {
            $his->package_weight = empty($his->package_weight) ? 0 : $his->package_weight; 
            $total = (($his->courier_fees > 0) ? round($his->courier_fees, 2) : '0') + (($his->cod_fees > 0) ? round($his->cod_fees, 2) : '0') + (($his->insurance_price > 0) ? round($his->insurance_price, 2) : '0') + (($his->extra_weight_charges > 0) ? round($his->extra_weight_charges, 2) : '0') + (($his->rto_charges > 0) ? round($his->rto_charges, 2) : '0') + (($his->rto_extra_weight_charges > 0) ? round($his->rto_extra_weight_charges, 2) : '0') - (($his->cod_reverse_amount > 0) ? round($his->cod_reverse_amount, 2) : '0');
            $row = array(
                date('d-m-Y', $his->shipping_created),
                $his->products,
                $his->prod_qty,
                ucwords($his->courier_name),
                $his->awb_number,
                strtoupper($his->ship_status),
                round($his->courier_fees, 2),
                round($his->cod_fees, 2),
                !empty($his->package_weight) ? round($his->package_weight / 1000, 2) : '0.5',
                ($his->charged_weight > $his->package_weight) ? round($his->charged_weight / 1000, 2) : '0',
                (($his->charged_weight - $his->package_weight) > 0) ? round(($his->charged_weight - $his->package_weight) / 1000, 2) : '0',
                ($his->extra_weight_charges > 0) ? round($his->extra_weight_charges, 2) : '0',
                ($his->rto_charges > 0) ? round($his->rto_charges, 2) : '0',
                ($his->cod_reverse_amount > 0) ? '-' . round($his->cod_reverse_amount, 2) : '0',
                ($his->rto_extra_weight_charges > 0) ? round($his->rto_extra_weight_charges, 2) : '0',
                round($total, 2)
            );
            fputcsv($file, $row);
        }
        fclose($file);
        exit;
    }

    function seller_cod_remittance()
    {
        $this->load->library('remittance_lib');
        $remitted_amount = $this->remittance_lib->remittedAmount($this->user->account_id);
        $this->data['remitted_amount'] = $remitted_amount;

        $last_remittance = $this->remittance_lib->lastRemittance($this->user->account_id);
        $this->data['last_remittance'] = $last_remittance;



        $this->load->library('shipping_lib');

        $next_remittance = $this->shipping_lib->nextRemittance($this->user->account_id);
        $this->data['next_remittance'] = $next_remittance;

        $total_remittance_due = $this->shipping_lib->totalRemittanceDue($this->user->account_id);
        $this->data['total_remittance_due'] = $total_remittance_due;

        //$pending_From_courier = $this->shipping_lib->dueFromCourier($this->user->account_id);
        //$this->data['due_from_courier'] = $pending_From_courier;

        $history = $this->remittance_lib->remittanceHistory($this->user->account_id);
        $this->data['history'] = $history;
        return $this->load->view('billing/pages/cod_remittance', $this->data, true);
    }

    function recharge_from_remittance()
    {
        $this->load->library('form_validation');
        $config = array(
            array(
                'field' => 'amount',
                'label' => 'Amount',
                'rules' => 'trim|required|min_length[1]|max_length[10]|numeric'
            ),
        );
        $this->form_validation->set_rules($config);
        if ($this->form_validation->run()) {

            $amount = $this->input->post('amount');
            if ($amount >= 1000) {
                //create wallet recharge for this amount
                $this->load->library('billing_lib');
                if (!$this->billing_lib->rechargeFromRemittance($this->user->account_id, $amount)) {
                    $this->data['json'] = array('error' => $this->billing_lib->get_error());
                } else {
                    $this->data['json'] = array('success' => 'recharge successfull');
                }
            } else {
                $this->data['json'] = array('error' => 'Minimum Recharge Amount is Rs.1000');
            }
        } else {
            $this->data['json'] = array('error' => strip_tags(validation_errors()));
        }
        $this->layout(false, 'json');
    }

    function weight_dispute()
    {
        $this->load->library('escalation_lib');
        $this->load->library('shipping_lib');
        $this->load->library('s3');

        $this->load->library('form_validation');
        $config = array(
            array(
                'field' => 'shipment_id',
                'label' => 'Shipment ID',
                'rules' => 'trim|required'
            ),
            array(
                'field' => 'remarks',
                'label' => 'Remarks',
                'rules' => 'trim|min_length[3]'
            ),
        );

        $this->form_validation->set_rules($config);
        if (!$this->form_validation->run()) {
            $this->data['json'] = array('error' => strip_tags(validation_errors()));
            $this->layout(false, 'json');
            return;
        }

        $config1 = array();
        $config1['upload_path'] = 'assets/escalations/';
        $config1['allowed_types'] = '*';
        $config1['max_size'] = 5000;
        $config1['encrypt_name'] = TRUE;

        $this->load->library('upload', $config1);

        $filesCount =  (!empty($_FILES['importFile'])) ? count($_FILES['importFile']['name']) : 0;

        $uploadData = array();
        $upload_folder = "escalations";

        for ($i = 0; $i < $filesCount; $i++) {
            if (!empty($_FILES['importFile']['name'][$i])) {
                $extension = explode(".", $_FILES['importFile']['name'][$i]);
                $new_name = time() . rand(100, 999) . '.' . end($extension);

                $config['file_name'] = $new_name;

                $fileTempName = $_FILES['importFile']['tmp_name'][$i];
                $image_name = $new_name;

                $file_name = $this->s3->amazonS3Upload($image_name, $fileTempName, $upload_folder);

                if ($file_name) {
                    $uploadData[] = $file_name;
                } else {
                    $this->data['json'] = array('error' => "Unable to upload file");
                    $this->layout(false, 'json');
                    return;
                }

                /*$_FILES['file']['name'] = $_FILES['importFile']['name'][$i];
                $_FILES['file']['type'] = $_FILES['importFile']['type'][$i];
                $_FILES['file']['tmp_name'] = $_FILES['importFile']['tmp_name'][$i];
                $_FILES['file']['error'] = $_FILES['importFile']['error'][$i];
                $_FILES['file']['size'] = $_FILES['importFile']['size'][$i];

                // File upload configuration
                // Load and initialize upload library

                $this->upload->initialize($config1);

                // Upload file to server
                if ($this->upload->do_upload('file')) {
                    // Uploaded file data
                    $fileData = $this->upload->data();
                    $uploadData[] = $fileData['file_name'];
                } else {
                    $this->data['json'] = array('error' => strip_tags($this->upload->display_errors()));
                    $this->layout(false, 'json');
                    return;
                }*/
            }
        }

        $shipment_ids = $this->input->post('shipment_id');
        $remarks = $this->input->post('remarks');
        $this->load->library('weight_lib');
        $disbute_time = $this->weight_lib->get_dispute_time_limit($this->user->account_id);
        if(!empty($disbute_time))
        {
            $weight_dispute_time_limit=$disbute_time->time_limt * 60 * 60 * 24 ;
        }
        else
        {
            $weight_dispute_time_limit=$this->config->item('weight_dispute_time_limit');
        }

        $shipment_ids = explode(',', $shipment_ids);
        foreach ($shipment_ids as $shipment_id) {
            $shipment = $this->shipping_lib->getByID($shipment_id);
            if (empty($shipment) || $shipment->user_id != $this->user->account_id  || $shipment->weight_dispute_raised == '1' || $shipment->weight_dispute_accepted == '1')
                continue;
            if ($shipment->weight_applied_date < (time() - $weight_dispute_time_limit))
                continue;
            //submit shipment escalation
            $update = array(
                'type' => 'weight',
                'ref_id' => $shipment_id,
                'remarks' => $remarks,
                'action_by' => 'seller',
                'attachments' => implode(',', $uploadData)
            );
            if ($this->escalation_lib->create_escalation($shipment->user_id, $update)) {
                $this->data['json'] = array('success' => 'done');
                $update_shipment = array(
                    'weight_dispute_raised' => '1',
                );
                $this->shipping_lib->update($shipment->id, $update_shipment);
            } else {
                $this->data['json'] = array('error' => $this->escalation_lib->get_error());
            }
        }
        $this->data['json'] = array('success' => 'done');

        $this->layout(false, 'json');
    }

    function accept_weight()
    {
        $this->load->library('form_validation');
        $config = array(
            array(
                'field' => 'shipping_ids[]',
                'label' => 'Shipment IDs',
                'rules' => 'trim|required'
            ),
        );


        $this->form_validation->set_rules($config);

        if (!$this->form_validation->run()) {
            $this->data['json'] = array('error' => strip_tags(validation_errors()));
            $this->layout(false, 'json');
            return;
        }

        $shipment_ids = $this->input->post('shipping_ids');

        if (empty($shipment_ids)) {
            $this->data['json'] = array('error' => 'Please select shipments.');
            $this->layout(false, 'json');
            return;
        }

        $this->load->library('admin/apply_weight');
        $this->load->library('admin/shipping_lib');


        foreach ($shipment_ids as $shipment_id) {
            $shipment = $this->shipping_lib->getByID($shipment_id);

            if (empty($shipment) || $shipment->user_id != $this->user->account_id || $shipment->weight_dispute_accepted == '1' || $shipment->weight_dispute_raised == '1')
                continue;

            $apply_weight = new Apply_weight();
            $apply_weight->setShipmentID($shipment_id);
            $apply_weight->setWonBy('courier');
            $done = $apply_weight->applyAndSavePendingWeightCharges();

            if ($done) {
                $save = array(
                    'weight_dispute_accepted' => '1',
                );

                $this->shipping_lib->update($shipment_id, $save);
            }
        }


        $this->data['json'] = array('success' => 'Applied successfully');
        $this->layout(false, 'json');
        return;
    }

    function generateLinkData()
    {
        $payment_id = $this->input->post('payment_id');
        $amount = $this->input->post('amount');
        $coupon_code = $this->input->post('coupon_code_apply');
        if (empty($amount) || $amount <= 0 || empty($payment_id)) {
            $this->data['json'] = array('error' => "Unable to create payment");
        } else {
            $this->load->library('admin/coupon_lib');
            $coupon = $this->coupon_lib->isCouponCodeCheck($this->user->account_id, $amount, $coupon_code);
            $payment_gateway_available=$this->config->item('payment_gateway');
            // if(!empty($coupon->applied_for) && !in_array('easebuzz', explode(',', $coupon->applied_for)))
            // {
            //     $this->data['json'] = array('error' => 'NA', 'payment_gateway'=>$payment_gateway_available,'coupon_gateway_allowed' => $coupon->applied_for ?? '');
            // }
            // else
            // {
                $easebuzz_key=$this->config->item('easebuzz_key');
                $easebuzz_access_key=$this->config->item('easebuzz_access_key');
                $this->load->library('user_lib');
                $user = $this->user_lib->getByID($this->user->account_id);
                $transactionId=time();
                $date=date('d-m-Y h:i:s');
                $surl=base_url('payment');
                $furl=base_url('payment');
                $hash_seq=hash('sha512',"$easebuzz_key|$payment_id|$amount|Wallet recharge by seller|$user->fname|$user->email|||||||||||$easebuzz_access_key");
                $post_parameter="key=$easebuzz_key&txnid=$payment_id&amount=$amount&productinfo=Wallet recharge by seller&firstname=$user->fname&phone=$user->phone&email=$user->email&surl=$surl&furl=$furl&hash=$hash_seq";
                $curl = curl_init();
                curl_setopt_array($curl, array(
                CURLOPT_URL => 'https://pay.easebuzz.in/payment/initiateLink',
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => '',
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 0,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => 'POST',
                CURLOPT_POSTFIELDS => $post_parameter,
                CURLOPT_HTTPHEADER => array(
                    'Accept: application/json',
                    'Content-Type: application/x-www-form-urlencoded',
                    'Cookie: Path=/; Path=/; Path=/'
                ),
                ));
                $response = curl_exec($curl);
                curl_close($curl);
                $response=json_decode($response);
                if (!empty($response->status)) {
                    $this->data['data']=$response->data;
                    $this->data['json'] = array('data' => $this->data['data']);                   
                } else {    
                    $this->data['json'] = array('error' => $pay_data->msg==''?'Unable to create payment':$pay_data->msg, 'payment_gateway'=>$payment_gateway_available,'coupon_gateway_allowed' => $coupon->applied_for ?? '');
                }
            // }
        }
        $this->layout(false, 'json');
        return;
    }

    function exportCallDetailCsv($wallet_id = null)
    {
        $filter = $this->input->get('filter');
        $apply_filters = array();

        if (!empty($wallet_id)) {
            $apply_filters['wallet_history_id'] = $wallet_id;
        }
        
        $this->load->library('wallet_lib');
        $wallet_detail = $this->wallet_lib->getById($wallet_id);

        if (empty($wallet_detail))
            return false;

        if($wallet_detail->user_id != $this->user->account_id)
            return false;

        $call_history = array();

        $filename = 'call_charges_detail' . time() . rand(1111, 9999) . '.csv';
        header("Content-Description: File Transfer");
        header("Content-Disposition: attachment; filename=$filename");
        header("Content-Type: application/csv; ");
        $file = fopen('php://output', 'w');
        $header = array("Date", "Transaction Type", "Transaction ID", "Call from", "Call to", "Total Call Duration (In minutes)", "Total Cost");
        fputcsv($file, $header);

        foreach ($call_history as $call) {
            $row = array(
                date('Y-m-d', $call->created),
                $call->type,
                $call->ref_id,
                $call->from_no,
                $call->to_no,
                $call->call_time,
                ($call->call_cost/100)
            );
            fputcsv($file, $row);
        }
        fclose($file);
        exit;
    }
    
    function check_coupon_code()
    {
        $this->load->library('form_validation');
        $config = array(
            array(
                'field' => 'amount',
                'label' => 'Amount',
                'rules' => 'trim|required|min_length[1]|max_length[10]|numeric'
            ), array(
                'field' => 'coupon_code',
                'label' => 'Coupon code',
                'rules' => 'trim|required'
            ),
        );
        $this->form_validation->set_rules($config);
        if ($this->form_validation->run()) {
            $amount = $this->input->post('amount');
            $coupon_code = $this->input->post('coupon_code');
            $this->load->library('admin/coupon_lib');

            $coupon = $this->coupon_lib->isCouponCodeCheck($this->user->account_id, $amount, $coupon_code);  //check coupon valid or not 
            if (!empty($coupon)) {
                $discount_amount = 0;
                if($coupon->discount_type=='percentage'){
                    $discount_amount = $this->coupon_lib->discount($amount, $coupon->discount_amount,$coupon->discount_amount_upto); 
                }
                if($coupon->discount_type=='fixed'){
                    $discount_amount = $this->coupon_lib->fixed($coupon->discount_amount_upto); 
                }
                if($amount > $discount_amount){
                    $discount_amount =  number_format(($amount - $discount_amount),2);                 
                }
                if($coupon->coupon_type == 'extra credit'){
                    $discount_amount =  number_format(($amount),2);
                }
               
                $this->data['json'] = array('statusCode' => '200','success' => strtoupper($coupon_code) . ' Applied'
                 ,'discount_amount' => $discount_amount,'descriptions' => $coupon->descriptions);
            } else {
                $this->data['json'] = array('statusCode' => '201', 'error' => $this->coupon_lib->get_error());
            }
        } else {
            $this->data['json'] = array('statusCode' => '201', 'error' => strip_tags(validation_errors()));
        }
        $this->layout(false, 'json');
    }
    public function pay_link()
    {   
        $url = $this->session->tempdata('pay_link');
        redirect($url);
    }

    public function getOrderStatus()
    {   
        $this->load->library('payment_lib');
        $data = $this->payment_lib->getbyID($this->input->post('order_id'));
        if (!empty($data) && $data->paid =='1') {
            $this->session->set_flashdata('success', 'Wallet Credited Successfully');
            echo 1;
            exit;
        }        
        echo 0;
    }

    function b2b_seller_price_calculator()
    {
        $this->load->library('user_lib');
        $user = $this->user_lib->getByID($this->user->account_id);
        if(empty($user->is_franchise) || $user->is_franchise != 'yes')
            redirect('billing', true);

        return $this->load->view('billing/pages/b2b_seller_price_calculator', [], true);
    }

    function b2b_calculate_pricing()
    {
        $this->load->library('form_validation');
        $config = array(
            array(
                'field' => 'origin',
                'label' => 'Origin Pincode',
                'rules' => 'trim|required|exact_length[6]|numeric'
            ),
            array(
                'field' => 'destination',
                'label' => 'Destination Pincode',
                'rules' => 'trim|required|exact_length[6]|numeric'
            ),
            array(
                'field' => 'weight',
                'label' => 'Weight',
                'rules' => 'trim|required|numeric|greater_than[0]'
            ),
            array(
                'field' => 'cod_amount',
                'label' => 'Amount',
                'rules' => 'trim|required|numeric'
            ),
        );
        $this->form_validation->set_rules($config);
        if ($this->form_validation->run()) {
            $this->load->library('user_lib');
            $user = $this->user_lib->getByID($this->user->account_id);

            $origin = $this->input->post('origin');
            $destination = $this->input->post('destination');
            $weight = $this->input->post('weight') * 1000;
            $length = $this->input->post('length');
            $height = $this->input->post('height');
            $breadth = $this->input->post('breadth');

            $order_type = 'prepaid';
            // $order_amount = $this->input->post('cod_amount');

            $this->load->library('courier_lib');
            $user_couriers = $this->courier_lib->userAvailableCouriers($this->user->account_id, 'cargo');

            //check pincode serviceblity
            $this->load->library('cargo_pincode_lib');
            $couriers = $this->cargo_pincode_lib->getPincodeService($destination);

            $return = array();

            if (!empty($couriers)) {
                $this->load->library('cargo_pricing_lib');
                foreach ($couriers as $key => $courier) {
                    if (!array_key_exists($courier->id, $user_couriers)) {
                        unset($couriers[$key]);
                        continue;
                    }

                    if (!$this->cargo_pincode_lib->checkPickupServiceByCourier($origin, $courier->id)) {
                        unset($couriers[$key]);
                        continue;
                    }

                    $pricing = new Cargo_pricing_lib();
                    $pricing->setPlan($user->cargo_pricing_plan);
                    $pricing->setCourier($courier->id);
                    $pricing->setOrderId($this->input->post());
                    $pricing->setOrigin($origin);
                    $pricing->setDestination($destination);
                    $pricing->setType($order_type);
                    $pricing->setUserID($this->user->account_id);

                    $price = $pricing->calculateCost();

                    if (empty($price['total'])) {
                        unset($couriers[$key]);
                        continue;
                    }

                    $courier->courier_charges = round($price['courier_charges'] / 1.18);
                    $courier->total_price = round($price['total'] / 1.18);

                    $cargo_charges = '';
                    $price_cargo_charges = json_decode($price['cargo_charges'], 1);

                    if(!empty($price_cargo_charges)) {
                        unset($price_cargo_charges['courier_id']);
                        unset($price_cargo_charges['plan']);
                        unset($price_cargo_charges['landing_rate']);
                        unset($price_cargo_charges['margin_rate']);
                        // unset($price_cargo_charges['calculated_weight']);
                        unset($price_cargo_charges['base_freight']);
                        unset($price_cargo_charges['total_charges_gst']);
                        foreach ($price_cargo_charges as $key => $value) {
                            $key = strtoupper($key);
                            $cargo_charges .= "<strong>$key: </strong> $value<br />";
                        }
                    }

                    $courier->cargo_charges = $cargo_charges;
                    $return[] = $courier;
                }

                $this->data['json'] = array('success' => $return);
            } else {
                $this->data['json'] = array('error' => 'Delivery pincode is not serviceable');
            }
        } else {
            $this->data['json'] = array('error' => strip_tags(validation_errors()));
        }

        $this->layout(false, 'json');
    }

    function exportWhatsappDetailCsv($wallet_id=false, $type =false)
    {
        
        if(empty($wallet_id) || empty($type))
            return false;
        
        $this->load->library('wallet_lib');
        $wallet_detail = $this->wallet_lib->getById($wallet_id);

        if (empty($wallet_detail))
            return false;

        if($wallet_detail->user_id != $this->user->account_id)
            return false;

        $this->load->library('Whatsappengage_charges');
        $this->whatsappengage_charges->generateCSV($wallet_id, urldecode($type));
        exit;
    }


}