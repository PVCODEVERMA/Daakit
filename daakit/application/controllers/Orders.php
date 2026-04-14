<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Orders extends User_controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->library('orders_lib');
        $this->userHasAccess('orders');
        $this->order_type = 'ecom';
    }

    function all($page = 1)
    {
        $per_page = $this->input->post('perPage');
        $page = $this->input->post('page') ?? 1;
        if (empty($per_page) || !is_numeric($per_page))
            $limit = 50;
        else
            $limit = $per_page;

        $filter = $this->input->post('filter');
        $apply_filters = array();
        $apply_filters['order_type'] = $this->order_type;

        $apply_filters['start_date'] = strtotime("-30 days midnight");
        $apply_filters['end_date'] = strtotime("tomorrow midnight") - 1;
        //pr($apply_filters,1);
        if (!empty($filter['order_ids'])) {
            $apply_filters['order_ids'] = array_map('trim', explode(',', $filter['order_ids']));
        }

        if (!empty($filter['tags'])) {
            $apply_filters['tags'] = strtolower($filter['tags']);
        }

        if (!empty($filter['search_query'])) {
            $apply_filters['search_query'] = $filter['search_query'];
        }

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

        if (!empty($filter['channel_id'])) {
            $apply_filters['channel_id'] = $filter['channel_id'];
        }

        if (!empty($filter['fulfillment'])) {
            $apply_filters['fulfillment'] = $filter['fulfillment'];
        }

        if (!empty($filter['product_name'])) {
            $apply_filters['product_name'] = $filter['product_name'];
        }
        if (!empty($filter['ivr_status'])) {
            $apply_filters['ivr_status'] = $filter['ivr_status'];
        }
        if (!empty($filter['engage_status'])) {
            $apply_filters['engage_status'] = $filter['engage_status'];
        }

        if (!empty($filter['segment_id'])) {
            $segment_id = $filter['segment_id'];
            $apply_filters['segment_id'] = $filter['segment_id'];
        } else {
            $apply_filters['segment_id'] = '';
            $segment_id = false;
        }

        $config["base_url"] = base_url('orders/all');

        $apply_filters = apply_filters('order_filters.apply_filter', $apply_filters, $apply_filters['segment_id'], $this->user->account_id);
        
        $current_url=current_url()."/".$page;
        $total_row = $this->orders_lib->countByUserID($this->user->account_id, $apply_filters);
        $config = array(
            'base_url' => base_url('orders/all'),
            'total_rows' => $total_row,
            'per_page' => $limit,
            'num_links' => '3',
            'use_page_numbers' => true,
            'reuse_query_string' => true,
            'cur_page' => $page,
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
        //pr($config,1);
        $this->load->library("pagination");
        $this->pagination->initialize($config);
        $this->data["pagination"] = $this->pagination->create_links();
        $offset = $limit * ($page - 1);

        $this->data['total_records'] = $total_row;
        $this->data['limit'] = $limit;
        $this->data['offset'] = $offset;

        $this->data['filter'] = $filter;
        $orders = $this->orders_lib->fetchByUserID($this->user->account_id, $limit, $offset, $apply_filters);

        $status_orders = array();
        $status_order_count = $this->orders_lib->countByUserIDStatusGrouped($this->user->account_id, $apply_filters);
        if (!empty($status_order_count))
            foreach ($status_order_count as $status_count) {
                $status_orders[strtolower($status_count->fulfillment_status)] = $status_count->total_count;
            }
        $this->load->library('channels_lib');
        $channels = $this->channels_lib->getChannelsByUserID($this->user->account_id);
        $this->data['channels'] = $channels;
        $this->data['orders'] = $orders;
        $this->data['count_by_status'] = $status_orders;

        $this->data['current_segment'] = $segment_id;

        $user_filters = array();
        $user_filters = apply_filters('order_filters.list', $user_filters, $this->user->account_id);
        $this->data['segments'] = $user_filters;

        //check ivr is enabled
        $ivr_enabled = apply_filters('ivr.orders_enabled', false, $this->user->account_id);

        $this->data['ivr_enabled'] = $ivr_enabled;
        $this->layout('orders/index');
    }

    /* View Page by Deep Rana 18-10-2025--- */

    function view($id = false)
    {
        if (!$id)
            redirect('orders/all', true);

        $order = $this->orders_lib->getByID($id);
        if (empty($order) || $order->user_id != $this->user->account_id) {
            $this->session->set_flashdata('error', 'Invalid Request');
            redirect('orders/all', true);
        }

        $warehouse = false;
        $rtowarehouse = false;
        //get shipping details
        $this->load->library('shipping_lib');
        $shipping = $this->shipping_lib->getByOrderID($order->id);

        //get channel name 
        $channel = false;
        $image_url = '';
        $this->load->library('channels_lib');
        $channel_name = $this->channels_lib->getChannelsByChannelId($order->user_id, $order->channel_id);
        if (!empty($channel_name)) {
            switch ($channel_name->channel) {
                case 'shopify':
                    $image_url = 'assets/channel_icons/shopify.jpg';
                    break;
                case 'shopify_oneclick':
                    $image_url = 'assets/channel_icons/shopify.jpg';
                    break;
                case 'woocommerce':
                    $image_url = 'assets/channel_icons/woocommerce.png';
                    break;
                case 'magento2':
                    $image_url = 'assets/channel_icons/magento.png';
                    break;
                case 'magento':
                    $image_url = 'assets/channel_icons/magento.png';
                    break;
                case 'storehippo':
                    $image_url = 'assets/channel_icons/storehippo.png';
                    break;
                case 'kartrocket':
                    $image_url = 'assets/channel_icons/kartrocket.png';
                    break;
                case 'kwikfunnels':
                    $image_url = 'assets/channel_icons/kwikfunnels.png';
                    break;
                case 'vinculum':
                    $image_url = 'assets/channel_icons/Vinculum-logo.png';
                    break;
                case 'amazon':
                    $image_url = 'assets/channel_icons/amazon.jpg';
                    break;
                case 'easyecom':
                    $image_url = 'assets/channel_icons/easyecom-logo.png';
                    break;

                default:
                    $image_url = 'assets/channel_icons/manual.jpg';
            }

            $channel['channel_name'] = $channel_name->channel;
            $channel['channel_icon'] = $image_url;
        }
        $this->data['channel'] = $channel;

        $courier = false;

        if (!empty($shipping)) {
            //get courioer details
            $this->load->library('courier_lib');
            $courier = $this->courier_lib->getByID($shipping->courier_id);

            //get warehouse details
            $this->load->library('warehouse_lib');
            if ($shipping->warehouse_id == '0') {
                $warehouse = $this->warehouse_lib->getUserWarehouse($order->user_id);
            } else {
                $warehouse = $this->warehouse_lib->getByID($shipping->warehouse_id);
                $rtowarehouse = $this->warehouse_lib->getByID($shipping->rto_warehouse_id);
            }
        }
        $user = $this->user_lib->getByID($this->user->user_id);
        $this->data['user'] = $user;

        //get order products
        $products = $this->orders_lib->getOrderProducts($order->id);


        $this->load->library('products_lib');
        if ($order->fulfillment_status == 'booked') {
            $result_data =   $this->products_lib->getProductDetailsbyOrder($order->id);
            if ($result_data) {
                if (((isset($result_data->weight))) && ((isset($result_data->length))) && ((isset($result_data->breadth))) && ((isset($result_data->height)))) {
                    $order->seller_applied_weight =  $result_data->weight;
                    $order->seller_applied_length =  $result_data->length;
                    $order->seller_applied_breadth = $result_data->breadth;
                    $order->seller_applied_height =  $result_data->height;
                    $order->seller_applied_weight_status = "Applied";
                }
            }
        }
        //pr($order);exit;

        $this->data['order'] = $order;
        $this->data['warehouse'] = $warehouse;
        $this->data['rtowarehouse'] = $rtowarehouse;
        $this->data['products'] = $products;
        $this->data['shipping'] = $shipping;
        $this->data['courier'] = $courier;
        //get reverse qc order products
        $qc_products = $this->orders_lib->getReverseQCOrderProducts($order->id);
        if (!empty($qc_products[0]->order_category_id)) {
            $ordercategories = $this->orders_lib->getOrdercategories($qc_products[0]->order_category_id);
            $this->data['ordercategories'] = $ordercategories;
        }

        $this->layout('orders/view');
    }

    /* View Page by Deep Rana 18-10-2025 stop--- */



    /* Edit Values by Deep Rana 18-10-2025--- */

    function orderdetailsedit()
    {
        $orderid = $this->input->post('orderid');
        $firstname = $this->input->post('firstname');
        $lastname = $this->input->post('lastname');
        $firstadd = $this->input->post('firstadd');
        $secondadd = $this->input->post('secondadd');
        $customercity = $this->input->post('customercity');
        $customerstate = $this->input->post('customerstate');
        $customercountry = $this->input->post('customercountry');
        $customerzipcode = $this->input->post('customerzipcode');
        $customercell = $this->input->post('customercell');
        $productnewname = $this->input->post('productnewname');
        $productnewqty = $this->input->post('productnewqty');

        $customereditarray = array(
            'customer_name' => $firstname . ' ' . $lastname,
            'shipping_fname' => $firstname,
            'shipping_lname' => $lastname,
            'shipping_address' => $firstadd,
            'shipping_address_2' => $secondadd,
            'shipping_city' => $customercity,
            'shipping_state' => $customerstate,
            'shipping_zip' => $customerzipcode,
            'shipping_country' => $customercountry,
            'shipping_phone' => $customercell,
        );

        $this->orders_lib->updateorder($orderid, $customereditarray, $productnewname, $productnewqty);
        redirect('orders/all', 'refresh');
    }

    /* Edit Values by Deep Rana 18-10-2025--- */

    function exportCSV()
    {

        $filter = $this->input->get('filter');
        $apply_filters = array();

        $apply_filters['order_type'] = $this->order_type;

        $apply_filters['start_date'] = strtotime("-30 days midnight");
        $apply_filters['end_date'] = strtotime("tomorrow midnight") - 1;

        if (!empty($filter['order_ids'])) {
            $apply_filters['order_ids'] = array_map('trim', explode(',', $filter['order_ids']));
        }

        if (!empty($filter['tags'])) {
            $apply_filters['tags'] = strtolower($filter['tags']);
        }

        if (!empty($filter['product_name'])) {
            $apply_filters['product_name'] = $filter['product_name'];
        }

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

        if (!empty($filter['channel_id'])) {
            $apply_filters['channel_id'] = $filter['channel_id'];
        }

        if (!empty($filter['fulfillment'])) {
            $apply_filters['fulfillment'] = $filter['fulfillment'];
        }

        if (!empty($filter['segment_id'])) {
            $apply_filters['segment_id'] = $filter['segment_id'];
        } else {
            $apply_filters['segment_id'] = '';
        }

        if (!empty($filter['ivr_status'])) {
            $apply_filters['ivr_status'] = $filter['ivr_status'];
        }

        if (!empty($filter['search_query'])) {
            $apply_filters['search_query'] = $filter['search_query'];
        }


        $this->data['filter'] = $filter;

        $apply_filters = apply_filters('order_filters.apply_filter', $apply_filters, $apply_filters['segment_id'], $this->user->account_id);

        $query = $this->orders_lib->exportByUserID($this->user->account_id, 150000000, 0, $apply_filters);
        $this->load->library('export_db');
        $export = new Export_db('slave');
        $export->query($query);


        $filename = 'Order_' . time() . '.csv';
        header("Content-Description: File Transfer");
        header("Content-Disposition: attachment; filename=$filename");
        header("Content-Type: application/csv; ");
        $file = fopen('php://output', 'w');
        $header = array("ID", "Order ID*", "Order Date",  "Order Amount", "Payment Type*", "Tags", "Shopify Order Tags",  "Shipping First Name*", "Shipping Last Name", "Shipping Company Name", "Shipping Email", "QC Status", "IVR Status", "IVR Confirmation Status", "Shipping Address 1*", "Shipping Address 2", "Shipping City*", "Shipping State*", "Shipping Country", "Shipping Pincode*", "Billing First Name", "Billing Last Name", "Billing Company Name", "Billing Address 1", "Billing Address 2", "Billing City", "Billing State", "Billing Country", "Billing Pincode", "Billing GST Number", "Weight(gm)", "Length(cm)", "Height(cm)", "Breadth(cm)", "IVR Status", "Whatsapp Status", "Status", "Shipping Charges", "COD Charges", "Tax Amount", "Discount", "PID(1)", "SKU(1)", "Product(1)*", "Quantity(1)*", "Price(1)*", "PID(2)", "SKU(2)", "Product(2)*", "Quantity(2)*", "Price(2)*",  "PID(3)", "SKU(3)", "Product(3)*", "Quantity(3)*", "Price(3)*", "PID(4)", "SKU(4)", "Product(4)*", "Quantity(4)*", "Price(4)*", "PID(5)", "SKU(5)", "Product(5)*", "Quantity(5)*", "Price(5)*");
        fputcsv($file, $header);
        while ($order = $export->next()) {
            $qc_status = '';
            if (($order->order_payment_type == 'reverse' && $order->qccheck == '1')) {
                $qc_status = 'Yes';
            }

            $row = array(
                $order->id,
                $order->order_no,
                date('Y-m-d', $order->order_date),
                $order->order_amount,
                $order->order_payment_type,
                $order->applied_tags,
                $order->order_tags,
                $order->shipping_fname,
                $order->shipping_lname,
                $order->shipping_company_name,
                $order->shipping_email,
                $qc_status,
                $order->ivr_calling_status,
                $order->ivr_status,
                $order->shipping_address,
                $order->shipping_address_2,
                $order->shipping_city,
                $order->shipping_state,
                $order->shipping_country,
                $order->shipping_zip,
                $order->billing_fname,
                $order->billing_lname,
                $order->billing_company_name,
                $order->billing_address,
                $order->billing_address_2,
                $order->billing_city,
                $order->billing_state,
                $order->billing_country,
                $order->billing_zip,
                $order->billing_gst_number,
                $order->package_weight,
                $order->package_length,
                $order->package_height,
                $order->package_breadth,
                ($order->ivr_calling_status == 'confirm') ? 'Verified' : (($order->ivr_calling_status == 'cancel') ? 'Cancelled' : ''),
                (@$order->whatsapp_status == 'confirm') ? 'Confirm' : ((@$order->whatsapp_status == 'cancel') ? 'Cancelled' : ''),
                ($order->fulfillment_status == 'new') ? 'Not Booked' : $order->fulfillment_status,
                $order->shipping_charges,
                $order->cod_charges,
                $order->tax_amount,
                $order->discount,
            );
            $products = $this->orders_lib->getOrderProducts($order->id);
            if (!empty($products))
                foreach ($products as $prod) {
                    $row[] = !empty($prod->product_id) ? $prod->product_id : '';
                    $row[] = !empty($prod->product_sku) ? $prod->product_sku : '';
                    $row[] =  !empty($prod->product_name) ? $prod->product_name : '';
                    $row[] =  !empty($prod->product_qty) ? $prod->product_qty : '';
                    $row[] =  !empty($prod->product_price) ? $prod->product_price : '';
                }
            fputcsv($file, $row);
        }
        fclose($file);
        exit;
    }

    function get_delivery_info($order_id = false, $warehouse_id = false)
    {
        if (empty($order_id)) {
            $this->data['error'] = 'Invalid Request';
            $this->layout(true, 'json');
        }

        $order = $this->orders_lib->getByID($order_id);
        //pr($order,1);
        if (empty($order) || $order->user_id != $this->user->account_id) {
            $this->data['error'] = 'Invalid Request';
            $this->layout('orders/delivery_info', 'NONE');
        } else {
            //get warehouse details
            $this->load->library('warehouse_lib');

            $cookie_warehouse = isset($_COOKIE['default_warehouse_' . $this->user->account_id]) ? $_COOKIE['default_warehouse_' . $this->user->account_id] : '';

            if ($warehouse_id) {
                setcookie('default_warehouse_' . $this->user->account_id, $warehouse_id, time() + (86400 * 30), '/'); // 86400 = 1 day
            } elseif (!empty($cookie_warehouse)) {
                $warehouse_id = $cookie_warehouse;
            } else {
                $default_warehouse = $this->warehouse_lib->getUserDefaultWarehouse($this->user->account_id);
                if (!empty($default_warehouse)) {
                    $warehouse_id = $default_warehouse->id;
                } else {
                    $this->data['error'] = 'Please set default warehouse in settings';
                    $this->layout('orders/delivery_info', 'NONE');
                    return;
                }
            }

            $warehouse = $this->warehouse_lib->getByID($warehouse_id);
            $this->data['selected_warehouse'] = $warehouse_id;


            //check user wallet balance
            $this->load->library('wallet_lib');

            if (!$this->wallet_lib->checkUserCanShip($order->user_id)) {
                $this->data['error'] = 'No credit available. Please recharge.';
                $this->layout('orders/delivery_info', 'NONE');
                return;
            }
            // first check agreement done
            $this->load->library('analytics_lib');
            $check_agreement = $this->analytics_lib->getallagreements_count($order->user_id);
            // if (empty($check_agreement)) {   //process limit 50 shipment
            //     $this->data['error'] = 'Please complete your company profile';
            //     $this->layout('orders/delivery_info', 'NONE');
            //     return;
            // }
            //get user details
            $this->load->library('user_lib');
            $user = $this->user_lib->getByID($order->user_id);
            if ($user->verified == '0') {   //process limit 50 shipment
                $this->data['error'] = 'KYC Verification Pending!<br>Please connect us : info@daakit.com/+91 9266426868';
                $this->layout('orders/delivery_info', 'NONE');
                return;
            }

            if (empty($warehouse) || empty($warehouse->zip)) {
                $this->data['error'] = 'Warehouse Details Missing';
                $this->layout('orders/delivery_info', 'NONE');
            } else {
                $this->load->library('plans_lib');
                $custom_plan = $this->plans_lib->getCustomPlanByName($user->pricing_plan);

                $this->load->library('pincode_lib');

                $pin_code = $order->shipping_zip;

                $this->load->library('plans_lib');
                $plans = $this->plans_lib->getPlanByName($user->pricing_plan);

                if (empty($plans))
                    return false;

                $plan_type = $plans->plan_type;

                if (strtolower($order->order_payment_type) == 'reverse') {
                    //check for pickup availale
                    $pickups_couriers = $this->pincode_lib->getReversePickupAvailabel($pin_code);

                    if (empty($pickups_couriers)) {
                        setcookie('default_warehouse_' . $this->user->account_id, $warehouse_id, time() - 3600, '/');
                        $this->data['error'] = 'Pickup Not Available';
                        $this->layout('orders/delivery_info', 'NONE');
                        return;
                    }

                    $pickup_courier_list = array();
                    foreach ($pickups_couriers as $pickups_courier) {
                        $pickup_courier_list[$pickups_courier->id] = $pickups_courier;
                    }

                    $this->load->library('courier_lib');
                    $user_couriers = $this->courier_lib->userAvailableCouriers($this->user->account_id, $this->order_type);

                    $couriers = $this->pincode_lib->getReversePincodeService($warehouse->zip);

                    $this->load->library('pricing_lib');

                    if (!empty($couriers)) { //get courier price
                        foreach ($couriers as $c_key => $courier) {
                            $courier->courier_type_weight = $courier->courier_type . '_' . $courier->weight . '_' . $courier->additional_weight;

                            if (!array_key_exists($courier->id, $pickup_courier_list)) {
                                unset($couriers[$c_key]);
                            }
                            if (!array_key_exists($courier->id, $user_couriers)) {
                                unset($couriers[$c_key]);
                            }
                            if ($order->qccheck == '1' && !$courier->reverse_qc_pickup) {
                                unset($couriers[$c_key]);
                            }

                            $pricing = new Pricing_lib();
                            $pricing->setPlan($user->pricing_plan);
                            $pricing->setCourier($courier->id);
                            $pricing->setOrigin($pin_code);
                            $pricing->setDestination($warehouse->zip);
                            $pricing->setType($order->order_payment_type);
                            $pricing->setAmount($order->order_amount);
                            $pricing->setWeight($order->package_weight);
                            $pricing->setLength($order->package_length);
                            $pricing->setBreadth($order->package_breadth);
                            $pricing->setHeight($order->package_height);

                            $shipping_cost = $pricing->calculateCost();
                            if (!empty($shipping_cost)) {
                                $courier->charges = $shipping_cost['total'];
                            }
                        }
                    }

                    array_multisort(array_column($couriers, 'courier_order'), SORT_ASC, $couriers);
                } else {
                    //check for pickup availale
                    $pickups_couriers = $this->pincode_lib->getPickupService($warehouse->zip);

                    if (empty($pickups_couriers)) {
                        setcookie('default_warehouse_' . $this->user->account_id, $warehouse_id, time() - 3600, '/');
                        $this->data['error'] = 'Pickup Not Available';
                        $this->layout('orders/delivery_info', 'NONE');
                        return;
                    }

                    $pickup_courier_list = array();
                    foreach ($pickups_couriers as $pickups_courier) {
                        $pickup_courier_list[$pickups_courier->id] = $pickups_courier;
                    }

                    $this->load->library('courier_lib');
                    $user_couriers = $this->courier_lib->userAvailableCouriers($this->user->account_id, $this->order_type);
                    //check pin code serviceblity
                    $couriers = $this->pincode_lib->getPincodeService($pin_code, $order->order_payment_type);
                    $this->load->library('pricing_lib');
                    if (!empty($couriers)) { //get courier price
                        foreach ($couriers as $c_key => $courier) {
                            $courier->courier_type_weight = $courier->courier_type . '_' . $courier->weight . '_' . $courier->additional_weight;

                            if (!array_key_exists($courier->id, $pickup_courier_list)) {
                                unset($couriers[$c_key]);
                            }
                            if (!array_key_exists($courier->id, $user_couriers))
                                unset($couriers[$c_key]);

                            if (empty($custom_plan)) {
                                $pricing = new Pricing_lib();
                                $pricing->setPlan($user->pricing_plan);
                                $pricing->setCourier($courier->id);
                                $pricing->setOrigin($warehouse->zip);
                                $pricing->setDestination($pin_code);
                                $pricing->setType($order->order_payment_type);
                                $pricing->setAmount($order->order_amount);
                                $pricing->setWeight($order->package_weight);
                                $pricing->setLength($order->package_length);
                                $pricing->setBreadth($order->package_breadth);
                                $pricing->setHeight($order->package_height);

                                $shipping_cost = $pricing->calculateCost();
                                // echo $courier->id."----".$warehouse->zip."----".$pin_code."----".$order->order_amount."-----".$order->order_payment_type;
                                // pr($shipping_cost);
                                if ($plan_type == 'per_dispatch' && empty($shipping_cost['total'])) {
                                    unset($couriers[$c_key]);
                                } else if (!empty($shipping_cost)) {
                                    $courier->charges = $shipping_cost['total'];
                                }
                            }
                        }
                    }

                    array_multisort(array_column($couriers, 'courier_order'), SORT_ASC, $couriers);

                    foreach ($couriers as $c_a_key => $c_a) {
                        // Highlight name
                        if ($c_a->highlight == 'yes') {
                            $couriers[$c_a_key]->name = "<i class='mdi mdi-star'></i> " . $c_a->name;
                        }
                    }

                    $couriers = apply_filters('order_ship.available_couriers', $couriers, $this->user->account_id);

                    if (!in_array($plan_type, array('standard')) && ($couriers[0]->id == 'autoship')) {
                        unset($couriers[0]);
                        $couriers = array_values($couriers);
                    }
                }
                if (empty($couriers)) {
                    setcookie('default_warehouse_' . $this->user->account_id, $warehouse_id, time() - 3600, '/');
                    $this->data['error'] = 'Couriers Not Serviceable';
                    $this->layout('orders/delivery_info', 'NONE');
                    return;
                }

                $warehouses = $this->warehouse_lib->getUserAllWarehouse($this->user->account_id, true);

                $this->data['warehouses'] = $warehouses;
                $this->data['secure_shipment'] = (!empty($secure_shipment->status) && $secure_shipment->status == 1) ? true : false;
                $this->data['order'] = $order;

                if ($custom_plan) {
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
                        $pricing->setOrigin($warehouse->zip);
                        $pricing->setDestination($pin_code);
                        $pricing->setType($order->order_payment_type);
                        $pricing->setAmount($order->order_amount);
                        $pricing->setWeight($order->package_weight);
                        $pricing->setLength($order->package_length);
                        $pricing->setBreadth($order->package_breadth);
                        $pricing->setHeight($order->package_height);
                        $pricing->setCourierType($courier_type);
                        $pricing->setCourierWeight($courier_weight);
                        $pricing->setCourierAdditionalWeight($courier_additional_weight);
                        $pricing->setCourierVolumetricDivisor(5000);

                        $shipping_cost = $pricing->calculateCost();
                        if (!empty($shipping_cost)) {
                            $custom_courier->charges = $shipping_cost['total'];
                        }

                        $couriers[$c_key] = $custom_courier;
                    }

                    $this->data['couriers'] = $couriers;
                    $this->layout('orders/delivery_info_flat', 'NONE');
                } else {
                    $this->data['couriers'] = $couriers;
                    $this->layout('orders/delivery_info', 'NONE');
                }
            }
        }
    }

    function getBulkShipCouriers($warehouse_id = false)
    {
        $this->load->library('courier_lib');
        //get warehouse details
        $this->load->library('warehouse_lib');
        $cookie_warehouse = isset($_COOKIE['default_warehouse_' . $this->user->account_id]) ? $_COOKIE['default_warehouse_' . $this->user->account_id] : '';

        if ($warehouse_id) {
            setcookie('default_warehouse' . $this->user->account_id, $warehouse_id, time() + (86400 * 30), '/'); // 86400 = 1 day
        } elseif (!empty($cookie_warehouse)) {
            $warehouse_id = $cookie_warehouse;
        } else {
            $default_warehouse = $this->warehouse_lib->getUserDefaultWarehouse($this->user->account_id);
            if (!empty($default_warehouse))
                $warehouse_id = $default_warehouse->id;
        }

        $warehouse = $this->warehouse_lib->getByID($warehouse_id);

        $this->data['selected_warehouse'] = $warehouse_id;

        //check user wallet balance
        $this->load->library('wallet_lib');

        if (!$this->wallet_lib->checkUserCanShip($this->user->account_id)) {
            $this->data['error'] = 'Wallet balance is low. Please recharge.';
            $this->layout('orders/bulk_ship_couriers', 'NONE');
            return;
        }

        if (empty($warehouse)) {
            $this->data['error'] = 'Warehouse Details Missing';
        } else {
            //get user details
            $this->load->library('user_lib');
            $user = $this->user_lib->getByID($this->user->account_id);

            $this->load->library('plans_lib');
            $plans = $this->plans_lib->getPlanByName($user->pricing_plan);

            $couriers = $this->courier_lib->userAvailableCouriers($this->user->account_id, $this->order_type);

            array_multisort(array_column($couriers, 'courier_order'), SORT_ASC, $couriers);

            $couriers = apply_filters('order_ship.available_couriers', $couriers, $this->user->account_id);

            $custom_plan = $this->plans_lib->getCustomPlanByName($user->pricing_plan);

            if ($custom_plan) {
                foreach ($couriers as $key => $courier) {
                    if (empty($courier->courier_type)) {
                        unset($couriers[$key]);
                        continue;
                    }

                    $courier->courier_type_weight = $courier->courier_type . '_' . $courier->weight . '_' . $courier->additional_weight;
                }

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

                    $custom_courier->courier_type = $courier[0];
                    $custom_courier->name = ucfirst($courier[0]) . ' ' . round($courier[1] / 1000, 2) . ' kg';
                    $custom_courier->weight = $courier[1];
                    $custom_courier->additional_weight = $courier[2];

                    $couriers[$c_key] = $custom_courier;
                }
            }

            $plan_type = $plans->plan_type;
            if (!in_array($plan_type, array('standard'))) {
                foreach ($couriers as $c_key => $courier) {
                    $couriers[$c_key]->highlight = 'no';

                    $courier_id = ($plan_type == 'smart') ? '0' : $courier->id;

                    if ($plan_type == 'smart') {
                        $plan_details = $this->plans_lib->getPlanDetailsByPlanCourierTypeAndWeight($plans->id, $courier_id, 'fwd', $courier->courier_type, $courier->weight);
                    } else {
                        $plan_details = $this->plans_lib->getPlanDetailsByCourierAndType($plans->id, $courier_id, 'fwd');
                    }

                    if (empty($plan_details) || (empty($plan_details->zone1) && empty($plan_details->zone2) && empty($plan_details->zone3) && empty($plan_details->zone4) && empty($plan_details->zone5))) {
                        unset($couriers[$c_key]);
                    }
                }
            }

            $this->data['couriers'] = $couriers;
        }

        $warehouses = $this->warehouse_lib->getUserAllWarehouse($this->user->account_id, true);

        $this->data['warehouses'] = $warehouses;

        $this->data['couriers'] = $couriers;

        $this->data['secure_shipment'] = (!empty($secure_shipment->status) && $secure_shipment->status == 1) ? true : false;

        if ($custom_plan) {
            $this->layout('orders/bulk_ship_couriers_flat', 'NONE');
        } else {
            $this->layout('orders/bulk_ship_couriers', 'NONE');
        }
    }

    function cancel()
    {
        $this->load->library('form_validation');
        $config = array(
            array(
                'field' => 'order_id',
                'label' => 'Order ID',
                'rules' => 'trim|required|min_length[1]|max_length[20]|numeric'
            ),
        );

        $this->form_validation->set_rules($config);
        if ($this->form_validation->run()) {
            $order_id = $this->input->post('order_id');
            //process order shipment
            if (!$this->orders_lib->cancelOrder($order_id, $this->user->account_id)) {
                $this->data['json'] = array('error' => $this->orders_lib->get_error());
            } else {
                $this->data['json'] = array('success' => 'Cancelled');
            }
        } else {
            $this->data['json'] = array('error' => strip_tags(validation_errors()));
        }
        $this->layout(false, 'json');
    }

    function bulk_cancel()
    {
        $this->load->library('form_validation');
        $config = array(
            array(
                'field' => 'order_ids[]',
                'label' => 'Order ID',
                'rules' => 'trim|required|numeric'
            ),
        );

        $this->form_validation->set_rules($config);
        if ($this->form_validation->run()) {
            $order_ids = $this->input->post('order_ids');
            foreach ($order_ids as $order_id) {
                $this->orders_lib->cancelOrder($order_id, $this->user->account_id);
            }
            $this->data['json'] = array('success' => 'Cancelled');
        } else {
            $this->data['json'] = array('error' => strip_tags(validation_errors()));
        }
        $this->layout(false, 'json');
    }

    function ship()
    {
        $this->load->library('form_validation');
        $config = array(
            array(
                'field' => 'order_id',
                'label' => 'Order ID',
                'rules' => 'trim|required|min_length[1]|max_length[20]|numeric'
            ),
            array(
                'field' => 'courier_id',
                'label' => 'Courier ID',
                'rules' => 'trim|required|min_length[1]|max_length[100]'
            ),
            array(
                'field' => 'warehouse_id',
                'label' => 'Pickup Warehouse',
                'rules' => 'trim|required|integer|min_length[1]|max_length[10]'
            ),
            array(
                'field' => 'rto_warehouse_id',
                'label' => 'RTO Warehouse',
                'rules' => 'trim|required|integer|min_length[1]|max_length[10]'
            )
        );

        $this->form_validation->set_rules($config);
        if ($this->form_validation->run()) {
            $order_id = $this->input->post('order_id');
            $courier_id = $this->input->post('courier_id');
            $warehouse_id = $this->input->post('warehouse_id');
            $rto_warehouse_id = $this->input->post('rto_warehouse_id');
            $essential_order = ($this->input->post('essential_order')) ? $this->input->post('essential_order') : 0;
            $dg_order = ($this->input->post('dg_order')) ? $this->input->post('dg_order') : 0;
            $is_insurance = ($this->input->post('is_insurance')) ? $this->input->post('is_insurance') : 0;
            //process order shipment

            $order = $this->orders_lib->getByID($order_id);

            if (empty($order) || $order->user_id != $this->user->account_id) {
                $this->data['json'] = array('error' => 'Invalid Request');
            } elseif ($order->fulfillment_status != 'new') {
                $this->data['json'] = array('error' => 'Order is already booked');
            } else {
                //process order for shipment
                if (!$this->orders_lib->processOrderShipment($order_id, $courier_id, $this->user->account_id, $warehouse_id, $rto_warehouse_id, $essential_order, $dg_order, $is_insurance)) {
                    $this->data['json'] = array('error' => $this->orders_lib->get_error());
                } else {
                    $this->data['json'] = array('success' => 'booked');
                }
            }
        } else {
            $this->data['json'] = array('error' => strip_tags(validation_errors()));
        }
        $this->layout(false, 'json');
    }

    function bulk_ship()
    {
        $this->load->library('form_validation');
        $config = array(
            array(
                'field' => 'courier_id',
                'label' => 'Courier ID',
                'rules' => 'trim|required|min_length[1]|max_length[100]'
            ),
            array(
                'field' => 'warehouse_id',
                'label' => 'Warehouse',
                'rules' => 'trim|required|integer|min_length[1]|max_length[10]'
            ),
            array(
                'field' => 'rto_warehouse_id',
                'label' => 'RTO Warehouse',
                'rules' => 'trim|required|integer|min_length[1]|max_length[10]'
            )
        );

        $this->form_validation->set_rules($config);
        if ($this->form_validation->run()) {

            //check if user is verified
            $this->load->library('user_lib');

            $order_ids = $this->input->post('order_ids');
            $courier_id = $this->input->post('courier_id');
            $warehouse_id = $this->input->post('warehouse_id');
            $rto_warehouse_id = $this->input->post('rto_warehouse_id');
            $essential_order = ($this->input->post('essential_order')) ? $this->input->post('essential_order') : 0;
            $dg_order = ($this->input->post('dg_order')) ? $this->input->post('dg_order') : 0;
            $is_insurance = ($this->input->post('is_insurance')) ? $this->input->post('is_insurance') : 0;
            if (!empty($order_ids)) {
                foreach ($order_ids as $order_id) {
                    if (!$this->orders_lib->processOrderShipment($order_id, $courier_id, $this->user->account_id, $warehouse_id, $rto_warehouse_id, $essential_order, $dg_order, $is_insurance))
                        $this->session->set_flashdata('error', $this->orders_lib->get_error());
                }
            }
            $this->data['json'] = array('success' => 'booked');
        } else {
            $this->data['json'] = array('error' => strip_tags(validation_errors()));
        }
        $this->layout(false, 'json');
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
        if ($this->form_validation->run()) {
            if (is_uploaded_file($_FILES['importFile']['tmp_name'])) {
                // Load CSV reader library
                $this->load->library('csvreader');
                // Parse data from CSV file
                $csvData = $this->csvreader->parse_csv($_FILES['importFile']['tmp_name']);
                if (empty($csvData)) {
                    $this->session->set_flashdata('error', 'Blank CSV File');
                    redirect('orders/all', true);
                }
                foreach ($csvData as $row_key => $row) {
                    $keys = str_replace('*', '', array_keys($row));
                    $row = array_combine($keys, array_values($row));

                    if (!$this->validate_upload_data($row)) {
                        $this->session->set_flashdata('error', 'Row no. ' . ($row_key + 1) . $this->data['error']);
                        redirect('orders/all', true);
                    }
                }
                $import_message_array = array();
                $enable_not = $this->orders_lib->getEnable_custom_order($this->user->account_id);
                foreach ($csvData as $row_key_2 => $row2) {
                    $keys = str_replace('*', '', array_keys($row2));
                    $row2 = array_combine($keys, array_values($row2));
                    $order_id = false;
                    $payment_type = strtolower($row2['Payment Type']);
                    if ($payment_type == 'cod') {
                        $payment_type = 'COD';
                    } elseif ($payment_type == 'reverse') {
                        $payment_type = 'reverse';
                    } else {
                        $payment_type = 'prepaid';
                    }

                    $products = array();
                    $order_total = 0;

                    for ($i = 1; $i <= 10; $i++) {
                        if (!empty($row2["Product({$i})"])) {
                            $products[] = array(
                                'product_name' => $row2["Product({$i})"],
                                'product_sku' => $row2["SKU({$i})"],
                                'product_qty' => $row2["Quantity({$i})"],
                                'product_price' => $row2["Price({$i})"],
                                'product_id' => isset($row2["PID({$i})"]) ? $row2["PID({$i})"] : '',
                            );
                            $order_total += $row2["Quantity({$i})"] * $row2["Price({$i})"];
                        }
                    }

                    $row2['Shipping Address 1'] = trim(preg_replace('/\s+/', ' ', str_replace(array("\r\n", "\r", "\n"), ' ', trim($row2['Shipping Address 1']))));
                    $row2['Shipping Address 2'] = trim(preg_replace('/\s+/', ' ', str_replace(array("\r\n", "\r", "\n"), ' ', trim($row2['Shipping Address 2']))));
                    $row2['Billing Address 1'] = trim(preg_replace('/\s+/', ' ', str_replace(array("\r\n", "\r", "\n"), ' ', trim($row2['Billing Address 1']))));
                    $row2['Billing Address 2'] = trim(preg_replace('/\s+/', ' ', str_replace(array("\r\n", "\r", "\n"), ' ', trim($row2['Billing Address 2']))));

                    $order_total = $order_total +  (((!empty($row2["Shipping Charges"])) ? $row2["Shipping Charges"] : '0') + ((!empty($row2["COD Charges"])) ? $row2["COD Charges"] : '0') + ((!empty($row2["Tax Amount"])) ? $row2["Tax Amount"] : '0') - ((!empty($row2["Discount"])) ? $row2["Discount"] : '0'));
                    $save_order = array(
                        'user_id' => $this->user->account_id,
                        'order_no' => $row2['Order ID'],
                        'order_date' => time(),
                        'order_amount' => $order_total,
                        'order_payment_type' => $payment_type,
                        'shipping_fname' => $row2['Shipping First Name'],
                        'shipping_lname' => $row2['Shipping Last Name'],
                        'shipping_company_name' => (!empty($row2['Shipping Company Name'])) ? $row2['Shipping Company Name'] : '',
                        'shipping_email' => (!empty($row2['Shipping Email'])) ? $row2['Shipping Email'] : '',
                        'shipping_address' => $row2['Shipping Address 1'],
                        'shipping_address_2' => $row2['Shipping Address 2'],
                        'shipping_phone' => $row2['Shipping Phone Number'],
                        'shipping_city' => $row2['Shipping City'],
                        'shipping_state' => $row2['Shipping State'],
                        'shipping_country' => 'India',
                        'shipping_zip' => $row2['Shipping Pincode'],
                        'billing_fname' => $row2['Billing First Name'],
                        'billing_lname' => $row2['Billing Last Name'],
                        'billing_company_name' => (!empty($row2['Billing Company Name'])) ? $row2['Billing Company Name'] : '',
                        'billing_address' => $row2['Billing Address 1'],
                        'billing_address_2' => $row2['Billing Address 2'],
                        'billing_phone' => $row2['Billing Phone Number'],
                        'billing_city' => $row2['Billing City'],
                        'billing_state' => $row2['Billing State'],
                        'billing_country' => 'India',
                        'billing_zip' => $row2['Billing Pincode'],
                        'billing_gst_number' => (!empty($row2['Billing GST Number'])) ? $row2['Billing GST Number'] : '',
                        'package_weight' => (floor((int)$row2['Weight(gm)'] / 500) >= 1) ? $row2['Weight(gm)'] : 500,
                        'package_length' => $row2['Length(cm)'],
                        'package_height' => $row2['Height(cm)'],
                        'package_breadth' => $row2['Breadth(cm)'],
                        'shipping_charges' => $row2['Shipping Charges'],
                        'cod_charges' => $row2['COD Charges'],
                        'tax_amount' => $row2['Tax Amount'],
                        'discount' => $row2['Discount'],
                        'applied_tags' => !empty($row2['Tags']) ? $row2['Tags'] : ''
                    );
                    if (!empty($row2['ID'])) {
                        $order = $this->orders_lib->getByID($row2['ID']);
                        // if (empty($order) || $order->user_id != $this->user->account_id || $order->fulfillment_status != 'new')
                        // continue;
                        if (empty($order)) {
                            $import_message_array[$row2['ID']] = "Order Id not Found";
                            continue;
                        }
                        if ($order->user_id != $this->user->account_id) {
                            $import_message_array[$row2['ID']] = "Unauthorised User";
                            continue;
                        }
                        if ($order->fulfillment_status != 'new') {
                            $import_message_array[$row2['ID']] = "Order not Editable";
                            continue;
                        }

                        $save_order['order_date'] = $order->order_date;
                        $update_status = $this->orders_lib->update($row2['ID'], $save_order);
                        if ($update_status)
                            $import_message_array[$row2['ID']] = "Success";

                        //delte existing products for this order
                        $this->orders_lib->deleteOrderProduct($row2['ID']);
                        $order_id = $row2['ID'];
                    } else {
                        $checkDuplicate = !empty($this->input->post('check_duplicates')) ? '1' : '0';
                        if ($checkDuplicate == '1') {
                            $cutome_order_id = $save_order['order_id'];
                            //check for duplicate
                            $orderArr = $this->orders_lib->getByUserDuplicateOrderID($this->user->account_id, $cutome_order_id);
                            if (empty($orderArr)) {
                                $order_id = $this->orders_lib->insertOrder($save_order);
                            } else {
                                $import_message_array[$cutome_order_id] = "Duplicate Order Id";
                            }
                        } else {
                            //insert the product and get the order ID

                            $order_id = $this->orders_lib->insertOrder($save_order);
                            
                        }
                    }
                    if (!empty($order_id)) {
                        $import_message_array[$save_order['order_id']] = "Success";
                        
                        foreach ($products as $product) {
                            $product['order_id'] = $order_id;
                            $result_id = $this->orders_lib->insertProduct($product);
                            if ($result_id) {
                                $this->load->library('products_lib');
                                $this->products_lib->CheckUpdateProductDetails($this->user->account_id, $product);
                            }
                        }
                        if(!empty($enable_not) && $enable_not[0]->custom_order_confirm=='1' && strtolower($payment_type) == 'cod' )
                        {
                            //do_action('whatsapp_neworder.message', $order_id); 
                        }
                    }
                }

                /**Start File export */
                if (is_dir('temp') === false) {
                    mkdir('temp', 0700);
                }
                $filename = 'Order_import_' . $this->user->user_id . time() . '.csv';
                $handle = fopen('temp/' . $filename, 'w');
                $header = array('Order ID', 'Message');
                fputcsv($handle, $header);
                $req_row = array();
                foreach ($csvData as $row_data => $row) {
                    $keys = str_replace('*', '', array_keys($row));
                    $row = array_combine($keys, array_values($row));

                    $orderid = !empty($row['ID']) ? $row['ID'] : $row['Order ID'];
                    $req_row['Order ID'] = $orderid;
                    $req_row['Message'] = !empty($import_message_array[$orderid]) ? $import_message_array[$orderid] : '';
                    fputcsv($handle, $req_row);
                }

                fclose($handle);

                $directory = 'temp/';

                $this->load->library('s3');
                $aws_file_name = $this->s3->amazonS3Upload($filename, $directory . $filename, 'order_import_csv');

                unlink($directory . $filename);

                $this->session->set_flashdata('success', 'Order Import is successfully completed</a>');


                /**end File export */
                redirect('orders/all', true);
            }
        } else {
            $this->data['error'] = validation_errors();
            $this->session->set_flashdata('error', $this->data['error']);
            redirect('orders/all', true);
        }
    }

    private function validate_upload_data($data)
    {
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
                'field' => 'Order ID',
                'label' => 'Order ID',
                'rules' => 'trim|required',
            ),
            array(
                'field' => 'Payment Type',
                'label' => 'Payment Type',
                'rules' => 'trim|required|in_list[prepaid,COD,Cod,Prepaid,PREPAID,cod,Reverse,reverse,REVERSE]',
            ),
            array(
                'field' => 'Tags',
                'label' => 'Tags',
                'rules' => 'trim|max_length[200]',
            ),
            array(
                'field' => 'Shipping First Name',
                'label' => 'Shipping First Name',
                'rules' => 'trim|required',
            ),
            array(
                'field' => 'Shipping Last Name',
                'label' => 'Shipping Last Name',
                'rules' => 'trim',
            ),
            array(
                'field' => 'Shipping Company Name',
                'label' => 'Shipping Company Name',
                'rules' => 'trim',
            ),
            array(
                'field' => 'Shipping Address 1',
                'label' => 'Shipping Address 1',
                'rules' => 'trim|required',
            ),
            array(
                'field' => 'Shipping Address 2',
                'label' => 'Shipping Address 2',
                'rules' => 'trim',
            ),
            array(
                'field' => 'Shipping Phone Number',
                'label' => 'Shipping Phone Number',
                'rules' => 'trim|required|numeric|exact_length[10]',
            ),
            array(
                'field' => 'Shipping City',
                'label' => 'Shipping City',
                'rules' => 'trim|required',
            ),
            array(
                'field' => 'Shipping State',
                'label' => 'Shipping State',
                'rules' => 'trim|required',
            ),
            array(
                'field' => 'Shipping Pincode',
                'label' => 'Shipping Pincode',
                'rules' => 'trim|required|exact_length[6]|numeric',
            ),

            array(
                'field' => 'Billing First Name',
                'label' => 'Billing First Name',
                'rules' => 'trim',
            ),
            array(
                'field' => 'Billing Last Name',
                'label' => 'Billing Last Name',
                'rules' => 'trim',
            ),
            array(
                'field' => 'Billing Company Name',
                'label' => 'Billing Company Name',
                'rules' => 'trim',
            ),
            array(
                'field' => 'Billing Address 1',
                'label' => 'Billing Address 1',
                'rules' => 'trim',
            ),
            array(
                'field' => 'Billing Address 2',
                'label' => 'Billing Address 2',
                'rules' => 'trim',
            ),
            array(
                'field' => 'Billing Phone Number',
                'label' => 'Billing Phone Number',
                'rules' => 'trim|exact_length[10]',
            ),
            array(
                'field' => 'Billing City',
                'label' => 'Billing City',
                'rules' => 'trim',
            ),
            array(
                'field' => 'Billing State',
                'label' => 'Billing State',
                'rules' => 'trim',
            ),
            array(
                'field' => 'Billing Pincode',
                'label' => 'Pincode',
                'rules' => 'trim|exact_length[6]|numeric',
            ),
            array(
                'field' => 'Billing GST Number',
                'label' => 'Billing GST Number',
                'rules' => 'trim|exact_length[15]',
            ),
            array(
                'field' => 'Weight(gm)',
                'label' => 'Weight(gm)',
                'rules' => 'trim|numeric',
            ),
            array(
                'field' => 'Length(cm)',
                'label' => 'Length(cm)',
                'rules' => 'trim|numeric',
            ),
            array(
                'field' => 'Height(cm)',
                'label' => 'Height(cm)',
                'rules' => 'trim|numeric',
            ),
            array(
                'field' => 'Breadth(cm)',
                'label' => 'Breadth(cm)',
                'rules' => 'trim|numeric',
            ),

            array(
                'field' => 'Shipping Charges',
                'label' => 'Shipping Charges',
                'rules' => 'trim|numeric',
            ),
            array(
                'field' => 'COD Charges',
                'label' => 'COD Charges',
                'rules' => 'trim|numeric',
            ),
            array(
                'field' => 'Discount',
                'label' => 'Discount',
                'rules' => 'trim|numeric',
            ),
            array(
                'field' => 'Product(1)',
                'label' => 'Product(1)',
                'rules' => 'trim|required',
            ),
            array(
                'field' => 'SKU(1)',
                'label' => 'SKU(1)',
                'rules' => 'trim',
            ),
            array(
                'field' => 'PID(1)',
                'label' => 'PID(1)',
                'rules' => 'trim|numeric',
            ),
            array(
                'field' => 'Price(1)',
                'label' => 'Price(1)',
                'rules' => 'trim|required|numeric',
            ),
            array(
                'field' => 'Quantity(1)',
                'label' => 'Quantity(1)',
                'rules' => 'trim|required|numeric',
            ),
        );
        if (!empty($data["Product(1)"]) || !empty($data["SKU(1)"]) ||  !empty($data["Price(1)"]) || !empty($data["Quantity(1)"])) {
            if (((filter_var($data["Quantity(1)"], FILTER_VALIDATE_INT) === false)) || (!is_numeric($data["Price(1)"]))) {
                $this->data['error'] = ' Invalid product quantity.';
                return false;
            }
        }

        for ($i = 2; $i <= 3; $i++) {

            if (!empty($data["Product({$i})"]) || !empty($data["SKU({$i})"]) || !empty($data["PID({$i})"]) || !empty($data["Price({$i})"]) || !empty($data["Quantity({$i})"])) {

                $config[] =  array(
                    'field' => "Product({$i})",
                    'label' => "Product({$i})",
                    'rules' => 'trim|required',
                );
                $config[] = array(
                    'field' => "SKU({$i})",
                    'label' => "SKU({$i})",
                    'rules' => 'trim',
                );
                $config[] = array(
                    'field' => "PID({$i})",
                    'label' => "PID({$i})",
                    'rules' => 'trim|numeric',
                );
                $config[] = array(
                    'field' => "Price({$i})",
                    'label' => "Price({$i})",
                    'rules' => 'trim|required|numeric',
                );
                $config[] = array(
                    'field' => "Quantity({$i})",
                    'label' => "Quantity({$i})",
                    'rules' => 'trim|required|numeric',
                );

                if (((filter_var($data["Quantity({$i})"], FILTER_VALIDATE_INT) === false)) || (!is_numeric($data["Price({$i})"]))) {

                    $this->data['error'] = ' Invalid product quantity.';
                    return false;
                }
            }
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

    function validate_order_date($date = false)
    {
        if (preg_match("/^[0-9]{4}-(0[1-9]|1[0-2])-(0[1-9]|[1-2][0-9]|3[0-1])$/", $date)) {
            return true;
        } else {
            $this->form_validation->set_message('validate_order_date', 'Order date format should be YYYY-MM-DD.');
            return false;
        }
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

    function create($id = false, $clone = false)
    {
        $this->data['order_type'] = 'new';

        if ($clone == 'clone') {
            $clone = true;
        } else
            $clone = false;

        if ($id) {
            $order = $this->orders_lib->getByID($id);
            $this->load->library('channels_lib');
            if (empty($order) || $order->user_id != $this->user->account_id) {
                $this->session->set_flashdata('error', 'Not Found');
                redirect('orders/all', true);
            }

            if ($order->fulfillment_status != 'new' && !$clone) {
                $this->session->set_flashdata('error', 'Order is not editable');
                redirect('orders/all', true);
            }

            $this->data['order'] = $order;
            //get order products

            $products = $this->orders_lib->getOrderProducts($order->id);
            $this->data['product'] = $products;
            $this->data['order_id'] = $id;
        }

        $this->data['clone'] = $clone;

        if ($id && !$clone)
            $this->data['order_type'] = 'edit';

        $this->createUpdateOrder();
    }

    private function createUpdateOrder($order_payment_type = false)
    {
        $this->load->library('form_validation');

        $this->form_validation->set_message('alpha_numeric_spaces', 'Only Characters, Numbers & Spaces are allowed in %s');

        $config = array(
            array(
                'field' => 'order_id',
                'label' => 'Order ID',
                'rules' => 'trim|required',
            ),
            array(
                'field' => 'payment_method',
                'label' => 'Payment Method',
                'rules' => 'trim|required|in_list[prepaid,COD,Reverse,reverse]',
            ),
            array(
                'field' => 'shipping_name',
                'label' => 'Shipping First Name',
                'rules' => 'trim|required',
            ),
            array(
                'field' => 'shipping_company_name',
                'label' => 'Shipping Company Name',
                'rules' => 'trim',
            ),
            array(
                'field' => 'shipping_address_1',
                'label' => 'Shipping Address 1',
                'rules' => 'trim|required',
            ),
            array(
                'field' => 'shipping_address_2',
                'label' => 'Shipping Address 2',
                'rules' => 'trim',
            ),
            array(
                'field' => 'shipping_city',
                'label' => 'Shipping City',
                'rules' => 'trim|required',
            ),
            array(
                'field' => 'shipping_state',
                'label' => 'Shipping State',
                'rules' => 'trim|required',
            ),
            array(
                'field' => 'shipping_pincode',
                'label' => 'Shipping Pincode',
                'rules' => 'trim|required|exact_length[6]|numeric',
            ),
            array(
                'field' => 'shipping_phone',
                'label' => 'Shipping Phone Number',
                'rules' => 'trim|required|exact_length[10]|numeric',
            ),
            array(
                'field' => 'shipping_email',
                'label' => 'Shipping Email',
                'rules' => 'trim|valid_email|max_length[100]',
            ),
            array(
                'field' => 'billing_name',
                'label' => 'Billing Customer First Name',
                'rules' => 'trim',
            ),
            array(
                'field' => 'billing_company_name',
                'label' => 'Billing Company Name',
                'rules' => 'trim',
            ),
            array(
                'field' => 'billing_address_1',
                'label' => 'Billing Address 1',
                'rules' => 'trim',
            ),
            array(
                'field' => 'billing_address_2',
                'label' => 'Billing Address 2',
                'rules' => 'trim',
            ),
            array(
                'field' => 'billing_city',
                'label' => 'Billing City',
                'rules' => 'trim',
            ),
            array(
                'field' => 'billing_state',
                'label' => 'Billing State',
                'rules' => 'trim',
            ),
            array(
                'field' => 'billing_pincode',
                'label' => 'Billing Pincode',
                'rules' => 'trim|exact_length[6]|numeric',
            ),
            array(
                'field' => 'billing_phone',
                'label' => 'Billing Phone Number',
                'rules' => 'trim|exact_length[10]|numeric',
            ),
            array(
                'field' => 'billing_gst_number',
                'label' => 'Billing GST Number',
                'rules' => 'trim|exact_length[15]',
            ),
            array(
                'field' => 'weight',
                'label' => 'Weight',
                'rules' => 'trim|numeric',
            ),
            array(
                'field' => 'length',
                'label' => 'Length',
                'rules' => 'trim|numeric',
            ),
            array(
                'field' => 'height',
                'label' => 'Height',
                'rules' => 'trim|numeric',
            ),
            array(
                'field' => 'breadth',
                'label' => 'Breadth',
                'rules' => 'trim|numeric',
            ),
            array(
                'field' => 'shipping_charges',
                'label' => 'Shipping Charges',
                'rules' => 'trim|numeric',
            ),
            array(
                'field' => 'cod_charges',
                'label' => 'COD Charges',
                'rules' => 'trim|numeric',
            ),
            array(
                'field' => 'tax_amount',
                'label' => 'Tax Amount',
                'rules' => 'trim|numeric',
            ),
            array(
                'field' => 'discount',
                'label' => 'Discount',
                'rules' => 'trim|numeric',
            ),
        );

        $this->form_validation->set_rules($config);

        if ($this->form_validation->run()) {
            $save_product = array();
            $order_total = 0;
            $products = $this->input->post('products');
            foreach ($products as $product) {
                if (((filter_var($product['product_qty'], FILTER_VALIDATE_INT) === false)) || (!is_numeric($product['product_price']))) {
                    $this->data['error'] = 'Invalid product quantity/price.';

                    if (!empty($this->input->post('products')))
                        $this->data['product'] = $this->input->post('products');

                    if ($order_payment_type == 'reverse')
                        $this->layout('orders/reverse_create');
                    else
                        $this->layout('orders/create');

                    return;
                }

                $save_product[] = array(
                    'product_name' => $product['product_name'],
                    'product_qty' => $product['product_qty'],
                    'product_sku' => $product['product_sku'],
                    'product_price' => $product['product_price'],
                    'product_id' => !empty($product['product_id']) ? $product['product_id'] : '',
                );

                $order_total += $product['product_qty'] * $product['product_price'];
            }

            $order_total = round($order_total + $this->input->post('shipping_charges') + $this->input->post('cod_charges') + $this->input->post('tax_amount') - $this->input->post('discount'));

            $hyperlocal_status = ($this->input->post('hyperlocal_check') == '1') ? '1' : '0';
            if ($hyperlocal_status && !empty($this->input->post('latitude')) && !empty($this->input->post('longitude'))) {
                $this->order_type = 'hyperlocal';
            }

            $order_save = array(
                'order_no' => (strtolower($this->input->post('payment_method')) == 'reverse') ? 'R-' . $this->input->post('order_id') : $this->input->post('order_id'),
                'order_amount' => $order_total,
                'order_payment_type' => $this->input->post('payment_method'),
                'billing_fname' => $this->input->post('billing_name'),
                'billing_lname' => $this->input->post('billing_lname'),
                'billing_company_name' => $this->input->post('billing_company_name'),
                'billing_address' => $this->input->post('billing_address_1'),
                'billing_address_2' => $this->input->post('billing_address_2'),
                'billing_phone' => $this->input->post('billing_phone'),
                'billing_city' => $this->input->post('billing_city'),
                'billing_state' => $this->input->post('billing_state'),
                'billing_country' => 'India',
                'billing_zip' => trim($this->input->post('billing_pincode')),
                'billing_gst_number' => $this->input->post('billing_gst_number'),
                'shipping_fname' => $this->input->post('shipping_name'),
                'shipping_lname' => $this->input->post('shipping_lname'),
                'shipping_company_name' => $this->input->post('shipping_company_name'),
                'shipping_address' => $this->input->post('shipping_address_1'),
                'shipping_address_2' => $this->input->post('shipping_address_2'),
                'shipping_phone' => $this->input->post('shipping_phone'),
                'shipping_email' => !empty($this->input->post('shipping_email')) ? trim($this->input->post('shipping_email')) : '',
                'shipping_city' => $this->input->post('shipping_city'),
                'shipping_state' => $this->input->post('shipping_state'),
                'shipping_country' => 'India',
                'shipping_zip' => trim($this->input->post('shipping_pincode')),
                'package_weight' => (!empty($this->input->post('weight')) && floor($this->input->post('weight') / 500) >= 1) ? $this->input->post('weight') : 500,
                'package_length' => $this->input->post('length'),
                'package_height' => $this->input->post('height'),
                'package_breadth' => $this->input->post('breadth'),
                'shipping_charges' => $this->input->post('shipping_charges'),
                'cod_charges' => $this->input->post('cod_charges'),
                'discount' => $this->input->post('discount'),
                'tax_amount' => $this->input->post('tax_amount'),
                'order_type' => $this->order_type,
                'latitude' => !empty($this->input->post('latitude')) ? trim($this->input->post('latitude')) : '',
                'longitude' => !empty($this->input->post('longitude')) ? trim($this->input->post('longitude')) : '',
                'hyperlocal_status' => $hyperlocal_status,
                'hyperlocal_address' => !empty($this->input->post('hyperlocal_address')) ? htmlentities(trim($this->input->post('hyperlocal_address'))) : '',
                'package_volumatic_weight' => trim($this->input->post('volumetric_weight'))
            );

        // pr($order_save); 
         //echo $this->user->account_id;
         
       
           
              
            switch ($this->data['order_type']) {
                case 'new':
                case 'checkout':
                    $order_save['user_id'] = $this->user->account_id;
                    $order_save['order_date'] = time();
                    //insert the product and get the order ID
                    $order_id = $this->orders_lib->insertOrder($order_save);
                    $this->load->library('notification_lib');
                    $response = $this->notification_lib->sendNotification(null, 'new', $order_id);
                   
                    break;
                case 'edit':
                    $order_id = $this->data['order_id'];
                    $this->orders_lib->update($order_id, $order_save);
                    //delte existing products for this order
                    $this->orders_lib->deleteOrderProduct($order_id);
                    // $this->whatsappengage_lib->orderupdate($order_id, $save_product);
                    break;
                default:
            }

            // pr($save_product);
            foreach ($save_product as $single_product) {
                $single_product['order_id'] = $order_id;
                $result_id = $this->orders_lib->insertProduct($single_product);
                if ($result_id) {
                    $this->load->library('products_lib');
                    $this->products_lib->CheckUpdateProductDetails($this->user->account_id, $single_product);
                }
            }

            $enable_not = $this->orders_lib->getEnable_custom_order($this->user->account_id);
            if(!empty($enable_not) && $enable_not[0]->custom_order_confirm=='1' && strtolower($this->input->post('payment_method')) == 'cod' )
            {
               do_action('whatsapp_neworder.message', $order_id); 

               $this->load->library('Whatsappengage_charges');
              // $this->whatsappengage_charges->deductCharges('order', $this->user->account_id);
               //do_action('whatsapp_deduction.update', $this->user->account_id); 

               
              // $this->load->library('whatsappengage_lib');
              // $this->whatsappengage_lib->create_order($order_id); 
            }


            $this->session->set_flashdata('success', 'Order Saved');
            redirect('orders/view/' . $order_id, true);
        } else {
            $this->data['error'] = validation_errors();

            if (!empty($this->input->post('products')))
                $this->data['product'] = $this->input->post('products');
        }

        if ($order_payment_type == 'reverse')
            $this->layout('orders/reverse_create');
        else
            $this->layout('orders/create');
    }

    function refresh()
    {
        $this->load->library('channels_lib');
        $channels = $this->channels_lib->getChannelsByUserID($this->user->account_id);

        if (!empty($channels)) {
            foreach ($channels as $channel) {
                do_action('channels.refreshOrders', $channel->id,strtolower($channel->channel));
            }
        }

        $this->data['json'] = array('success' => 'Refresh request received.');
        $this->layout(false, 'json');
    }

    function add_edit_segment($id = false)
    {
        $edit_data = false;

        if ($id) {
            $edit_data = $this->segment_lib->getByID($id);
            if (empty($edit_data) || $edit_data->user_id != $this->user->account_id) {
                $this->data['json'] = array('error' => 'Invalid Request');
                $this->layout(false, 'json');
                return;
            }
        }

        $this->data['edit_data'] = $edit_data;

        $this->layout('orders/segment_form', 'NONE');
    }

    function add_segment()
    {
        $this->load->library('segment_lib');
        $this->load->library('form_validation');
        $config = array(
            array(
                'field' => 'name',
                'label' => 'Segment Name',
                'rules' => 'trim|required|max_length[20]'
            ),
            array(
                'field' => 'filter_id',
                'label' => 'Segment ID',
                'rules' => 'trim|numeric'
            ),
            array(
                'field' => 'filter[]',
                'label' => 'Rules',
                'rules' => 'trim|required'
            ),
            array(
                'field' => 'filter_type',
                'label' => 'Filter Type',
                'rules' => 'trim|required|in_list[or,and]'
            ),
        );

        $this->form_validation->set_rules($config);
        if (!$this->form_validation->run()) {
            $this->data['json'] = array('error' => strip_tags(validation_errors()));
            $this->layout(false, 'json');
            return;
        }

        $name = strtolower($this->input->post('name'));
        $filter_id = $this->input->post('filter_id');
        $filters = $this->input->post('filter');
        $filter_type = $this->input->post('filter_type');

        $rules = array();


        foreach ($filters as $filter) {
            if (empty($filter['field']) || empty($filter['condition']) || empty($filter['value'])) {
                $this->data['json'] = array('error' => 'All fileds are required');
                $this->layout(false, 'json');
                return;
            }
            $rules[] = $filter;
        }


        $save = array(
            'filter_name' => $name,
            'filter_type' => $filter_type,
            'user_id' => $this->user->account_id,
            'conditions' => base64_encode(json_encode($rules)),
        );

        if ($filter_id) {
            $edit_data = $this->segment_lib->getByID($filter_id);
            if (empty($edit_data) || $edit_data->user_id != $this->user->account_id) {
                $this->data['json'] = array('error' => 'Invalid Request');
                $this->layout(false, 'json');
                return;
            }
            $this->segment_lib->update($filter_id, $save);
        } else {
            $this->segment_lib->create($save);
        }

        $this->data['json'] = array('success' => 'Segment created successfully');
        $this->layout(false, 'json');
        return;
    }

    function delete_segment()
    {
        $this->load->library('form_validation');
        $config = array(
            array(
                'field' => 'filter_id',
                'label' => 'Filter ID',
                'rules' => 'trim|numeric|required'
            ),
        );

        $this->form_validation->set_rules($config);
        if (!$this->form_validation->run()) {
            $this->data['json'] = array('error' => strip_tags(validation_errors()));
            $this->layout(false, 'json');
            return;
        }

        $filter_id = $this->input->post('filter_id');

        $filter_data = $this->segment_lib->getByID($filter_id);
        if (empty($filter_data) || $filter_data->user_id != $this->user->account_id) {
            $this->data['json'] = array('error' => 'Invalid Request');
            $this->layout(false, 'json');
            return;
        }

        $this->segment_lib->delete($filter_id);

        $this->data['json'] = array('success' => 'Record deleted successfully');
        $this->layout(false, 'json');
        return;
    }

    function invoice($id = false)
    {
        if (!$id)
            redirect('orders/all', true);
        $order = $this->orders_lib->getByID($id);

        if (empty($order) || $order->user_id != $this->user->account_id) {
            $this->session->set_flashdata('error', 'Invalid Request');
            redirect('orders/all', true);
        }

        $this->load->library('shipping_lib');
        $shipping = $this->shipping_lib->getByOrderID($order->id);
        $products = $this->orders_lib->getOrderProducts($order->id);
        $product_order = array();
        $prod = array();
        if (!empty($products)) {

            foreach ($products as $key => $value) {

                $prod['id'] = $value->id;
                $prod['order_id'] = $value->order_id;
                $prod['product_id'] = $value->product_id;
                $prod['product_name'] = $value->product_name;
                $prod['product_qty'] = $value->product_qty;
                $prod['product_sku'] = $value->product_sku;
                $prod['product_weight'] = $value->product_weight;
                $prod['product_price'] = $value->product_price;
                $prod['length'] = '';
                $prod['breadth'] = '';
                $prod['height'] = '';
                $prod['weight'] = '';
                $prod['igst'] = 0;
                $prod['hsn_code'] = '';

                if (!empty($value->product_sku)) {
                    $product = $this->orders_lib->getOrderProductsWith($value->product_sku, $order->user_id, "product_sku");
                    if (!empty($product)) {
                        $prod['length'] = $product->length;
                        $prod['breadth'] = $product->breadth;
                        $prod['height'] = $product->height;
                        $prod['weight'] = $product->weight;
                        $prod['igst'] = $product->igst;
                        $prod['hsn_code'] = $product->hsn_code;
                        $product_order[$key] = (object)$prod;
                        continue;
                    }
                }
                if (!empty($value->product_name)) {
                    $product = $this->orders_lib->getOrderProductsWith($value->product_name, $order->user_id, "product_name");
                    if (!empty($product)) {
                        $prod['length'] = $product->length;
                        $prod['breadth'] = $product->breadth;
                        $prod['height'] = $product->height;
                        $prod['weight'] = $product->weight;
                        $prod['igst'] = $product->igst;
                        $prod['hsn_code'] = $product->hsn_code;
                        $product_order[$key] = (object)$prod;
                        continue;
                    }
                }
                $product_order[$key] = (object)$prod;
            }
        }

        $shipping_status = array('new', 'canceled');
        if (empty($shipping) || in_array($shipping->ship_status, $shipping_status)) {
            $this->session->set_flashdata('error', 'Invalid Request');
            redirect('orders/all', true);
        }

        $order = $this->shipping_lib->getInvoiceData($shipping->id);

        $this->data['orders'] = $order;
        $this->data['products'] = $product_order;
        $this->data['shipping_id'] = $shipping->id;
        $this->layout('orders/invoice');
    }

    function generateinvoice()
    {
        $shipping_ids = $this->input->post('shipping_ids');
        $this->load->library('user_lib');
        $user = $this->user_lib->getByID($this->user->account_id);
        $this->load->library('shipping_lib');

        if (!empty($shipping_ids)) {
            $pdf = $this->shipping_lib->autoInvoice($shipping_ids, $user->label_format);
            $this->data['json'] = array('success' => $pdf);
        } else {
            $this->data['json'] = array('error' => 'Please select Shipments first');
        }

        $this->layout(false, 'json');
    }

    public function getcitystate()
    {
        $pincode = $this->input->post('pincode');
        $this->load->library('pincode_lib');
        $pin_code = $this->pincode_lib->get_citystate($pincode);
        if (!empty($pin_code)) {
            $arr['city'] = $pin_code->city;
            $arr['state'] = $pin_code->state;
            echo json_encode($arr);
        }
    }

    function reverse_create($id = false, $clone = false)
    {
        $this->data['order_type'] = 'new';

        if ($clone == 'clone') {
            $clone = true;
        } else
            $clone = false;

        if ($id) {
            $order = $this->orders_lib->getByID($id);
            $this->load->library('channels_lib');
            if (empty($order) || $order->user_id != $this->user->account_id) {
                $this->session->set_flashdata('error', 'Not Found');
                redirect('orders/all', true);
            }

            if ($order->fulfillment_status != 'new' && !$clone) {
                $this->session->set_flashdata('error', 'Order is not editable');
                redirect('orders/all', true);
            }

            $this->data['order'] = $order;
            //get order products

            $products = $this->orders_lib->getOrderProducts($order->id);
            $this->data['product'] = $products;
            $this->data['order_id'] = $id;
        }

        $this->data['clone'] = $clone;

        if ($id && !$clone)
            $this->data['order_type'] = 'edit';

        $this->createUpdateOrder('reverse');
    }

    function reverse_qc_create($id = false, $clone = false)
    {
        if ($clone == 'clone') {
            $clone = true;
        } else {
            $clone = false;
        }

        if ($id) {
            $order = $this->orders_lib->getByID($id);
            if (empty($order) || $order->user_id != $this->user->account_id) {
                $this->session->set_flashdata('error', 'Not Found');
                redirect('orders/all', true);
            }

            if ($order->fulfillment_status != 'new' && !$clone) {
                $this->session->set_flashdata('error', 'Order is not editable');
                redirect('orders/all', true);
            }

            $this->data['order'] = $order;
            $products = $this->orders_lib->getOrderProducts($order->id);
            $this->data['product'] = $products[0];
            $this->data['order_id'] = $id;

            if ($order->qccheck == '1') {
                $qc_products = $this->orders_lib->getReverseQCOrderProducts($order->id);
                $this->data['qc_product'] = $qc_products[0];
            }
        }

        $this->data['order_type'] = 'new';
        $this->data['clone'] = $clone;
        if ($id && !$clone) {
            $this->data['order_type'] = 'edit';
        }

        $ordercategories = $this->orders_lib->getOrdercategories();
        $this->data['ordercategories'] = $ordercategories;

        $this->createreverseUpdateOrder();
    }

    private function createreverseUpdateOrder()
    {
        $this->load->library('form_validation');
        $this->form_validation->set_message('alpha_numeric_spaces', 'Only Characters, Numbers & Spaces are  allowed in %s');
        $config = array(
            array(
                'field' => 'order_id',
                'label' => 'Order ID',
                'rules' => 'trim|required',
            ),
            array(
                'field' => 'shipping_name',
                'label' => 'Shipping First Name',
                'rules' => 'trim|required',
            ),
            array(
                'field' => 'shipping_company_name',
                'label' => 'Shipping Company Name',
                'rules' => 'trim',
            ),
            array(
                'field' => 'shipping_address_1',
                'label' => 'Shipping Address 1',
                'rules' => 'trim|required',
            ),
            array(
                'field' => 'shipping_address_2',
                'label' => 'Shipping Address 2',
                'rules' => 'trim',
            ),
            array(
                'field' => 'shipping_city',
                'label' => 'Shipping City',
                'rules' => 'trim|required',
            ),
            array(
                'field' => 'shipping_state',
                'label' => 'Shipping State',
                'rules' => 'trim|required',
            ),
            array(
                'field' => 'shipping_pincode',
                'label' => 'Shipping Pincode',
                'rules' => 'trim|required|exact_length[6]|numeric',
            ),
            array(
                'field' => 'shipping_phone',
                'label' => 'Shipping Phone Number',
                'rules' => 'trim|required|exact_length[10]|numeric',
            ),
            array(
                'field' => 'product_name',
                'label' => 'Product Name',
                'rules' => 'trim|required',
            ),
            array(
                'field' => 'product_qty',
                'label' => 'Product Quantity',
                'rules' => 'trim|required|greater_than[0]',
            ),
            array(
                'field' => 'product_price',
                'label' => 'Product Amount',
                'rules' => 'trim|required|numeric',
            ),
            array(
                'field' => 'weight',
                'label' => 'Weight',
                'rules' => 'trim|numeric',
            ),
            array(
                'field' => 'length',
                'label' => 'Length',
                'rules' => 'trim|numeric',
            ),
            array(
                'field' => 'height',
                'label' => 'Height',
                'rules' => 'trim|numeric',
            ),
            array(
                'field' => 'breadth',
                'label' => 'Breadth',
                'rules' => 'trim|numeric',
            )
        );

        if ($this->input->post('qccheck') == '1') {
            $order_category_id = $this->input->post('order_category_id');
            $product_usage = $this->input->post('product_usage');
            $product_damage = $this->input->post('product_damage');

            $config[] = array(
                'field' => 'product_qty',
                'label' => 'Product Quantity',
                'rules' => 'trim|required|in_list[1]',
            );

            $product_brandname = $this->input->post('brandname');
            $brandnametype = "";
            if ($product_brandname == '1') {
                $brandnametype = $this->input->post('brandnametype');
                $config[] = array(
                    'field' => 'brandnametype',
                    'label' => 'Brand Name',
                    'rules' => 'trim|required'
                );
            }

            $product_productsize = $this->input->post('productsize');
            $productsizetype = "";
            if ($product_productsize == '1') {
                $productsizetype = $this->input->post('productsizetype');
                $config[] = array(
                    'field' => 'productsizetype',
                    'label' => 'Product Size',
                    'rules' => 'trim|required'
                );
            }

            $product_productcolor = $this->input->post('productcolor');
            $productcolourtype = "";
            if ($product_productcolor == '1') {
                $productcolourtype = $this->input->post('productcolourtype');
                $config[] = array(
                    'field' => 'productcolourtype',
                    'label' => 'Brand Name',
                    'rules' => 'trim|required'
                );
            }

            if ($this->input->post('uploadedimage') == '' && $this->input->post('uploadedimage_2') == '' && $this->input->post('uploadedimage_3') == '' && $this->input->post('uploadedimage_4') == '') {
                $config[] = array(
                    'field' => 'uploadedimage',
                    'label' => 'Product Image',
                    'rules' => 'trim|required',
                );
            }

            $product_img_1 = $this->input->post('uploadedimage');
            $product_img_2 = $this->input->post('uploadedimage_2');
            $product_img_3 = $this->input->post('uploadedimage_3');
            $product_img_4 = $this->input->post('uploadedimage_4');
        } else {
            $order_category_id = '';
            $product_usage = '';
            $product_damage = '';
            $product_brandname = '';
            $brandnametype = '';
            $product_productsize = '';
            $productsizetype = '';
            $product_productcolor = '';
            $productcolourtype = '';
            $product_img_1 = '';
            $product_img_2 = '';
            $product_img_3 = '';
            $product_img_4 = '';
        }

        $this->form_validation->set_rules($config);
        if ($this->form_validation->run()) {
            $order_total = 0;
            $order_total = $this->input->post('product_qty') * $this->input->post('product_price');
            $order_save = array(
                'order_id' => 'R-' . '' . $this->input->post('order_id'),
                'order_amount' => $order_total,
                'order_payment_type' => 'reverse',
                'shipping_fname' => $this->input->post('shipping_name'),
                'shipping_lname' => $this->input->post('shipping_lname'),
                'shipping_company_name' => $this->input->post('shipping_company_name'),
                'shipping_address' => $this->input->post('shipping_address_1'),
                'shipping_address_2' => $this->input->post('shipping_address_2'),
                'shipping_phone' => $this->input->post('shipping_phone'),
                'shipping_city' => $this->input->post('shipping_city'),
                'shipping_state' => $this->input->post('shipping_state'),
                'shipping_country' => 'India',
                'shipping_zip' => $this->input->post('shipping_pincode'),
                'package_weight' => (!empty($this->input->post('weight')) && floor($this->input->post('weight') / 500) >= 1) ? $this->input->post('weight') : 500,
                'package_length' => $this->input->post('length'),
                'package_height' => $this->input->post('height'),
                'package_breadth' => $this->input->post('breadth'),
                'package_volumatic_weight' => trim($this->input->post('vol_weight')),
                'qccheck' => (!empty($this->input->post('qccheck')) && ($this->input->post('qccheck') == '1')) ? '1' : '0'
            );

            $save_product = array(
                'product_name' => $this->input->post('product_name'),
                'product_qty' => $this->input->post('product_qty'),
                'product_price' => $this->input->post('product_price')
            );

            switch ($this->data['order_type']) {
                case 'new':
                case 'checkout':
                    $order_save['user_id'] = $this->user->account_id;
                    $order_save['order_date'] = time();
                    //insert the product and get the order ID
                    $order_id = $this->orders_lib->insertOrder($order_save);
                    $save_product['order_id'] = $order_id;
                    $this->orders_lib->insertProduct($save_product);
                    break;
                case 'edit':
                    $order_id = $this->data['order_id'];
                    $save_product['order_id'] = $order_id;
                    $this->orders_lib->update($order_id, $order_save);
                    $this->orders_lib->deleteOrderProduct($order_id);
                    $this->orders_lib->insertProduct($save_product);
                    break;
                default:
            }

            $this->orders_lib->deleteReverseQCOrderProduct($order_id);
            if ($this->input->post('qccheck') == '1') {
                $save_qc_product = array(
                    'order_id' => $order_id,
                    'order_category_id' => $order_category_id,
                    'product_usage' => $product_usage,
                    'product_damage' => $product_damage,
                    'brandname' => $product_brandname,
                    'brand_name_text' => $brandnametype,
                    'productsize' => $product_productsize,
                    'product_size_text' => $productsizetype,
                    'productcolor' => $product_productcolor,
                    'product_color_text ' => $productcolourtype,
                    'product_img_1' => $product_img_1,
                    'product_img_2' => $product_img_2,
                    'product_img_3' => $product_img_3,
                    'product_img_4' => $product_img_4,
                );

                $this->orders_lib->insertReverseQCProduct($save_qc_product);
            }

            $this->session->set_flashdata('success', 'Order Saved');
            redirect('orders/view/' . $order_id, true);
        } else {
            $this->data['error'] = validation_errors();
        }

        $this->layout('orders/reverse_qc_create');
    }

    function upload_first_img()
    {
        $this->load->library('s3');
        $ext = pathinfo($_FILES['product_img_1']['name'], PATHINFO_EXTENSION);
        $img_ext_chk = array('jpg', 'JPG', 'jpeg', 'JPEG', 'png', 'PNG');
        if (in_array($ext, $img_ext_chk)) {
            $file_name = time() . rand(1111, 9999);
            $upload_folder = "assets/order_product";
            $fileTempName = $_FILES['product_img_1']['tmp_name'];
            $image_name = $file_name . '.' . strtolower($ext);
            $file_info = $this->s3->amazonS3Upload($image_name, $fileTempName, $upload_folder);
            $this->data['json'] = array('success' => $file_info);
            $this->layout(false, 'json');
            return;
        } else {
            $this->data['json'] = array('error' => 'Invalid File Type.(Allowed only JPG, JPEG and PNG)');
            $this->layout(false, 'json');
            return;
        }
    }

    function upload_second_file()
    {
        $this->load->library('s3');
        $ext_2 = pathinfo($_FILES['product_img_2']['name'], PATHINFO_EXTENSION);
        $img_ext_chk_2 = array('jpg', 'JPG', 'jpeg', 'JPEG', 'png', 'PNG');
        if (in_array($ext_2, $img_ext_chk_2)) {
            $file_name = time() . rand(1111, 9999);
            $upload_folder = "assets/order_product";
            $fileTempName = $_FILES['product_img_2']['tmp_name'];
            $image_name = $file_name . '.' . strtolower($ext_2);
            $file_info2 = $this->s3->amazonS3Upload($image_name, $fileTempName, $upload_folder);
            $this->data['json'] = array('success' => $file_info2);
            $this->layout(false, 'json');
            return;
        } else {
            $this->data['json'] = array('error' => 'Invalid File Type.(Allowed only JPG, JPEG and PNG)');
            $this->layout(false, 'json');
            return;
        }
    }

    function upload_third_file()
    {
        $this->load->library('s3');
        $ext_3 = pathinfo($_FILES['product_img_3']['name'], PATHINFO_EXTENSION);
        $img_ext_chk_3 = array('jpg', 'JPG', 'jpeg', 'JPEG', 'png', 'PNG');
        if (in_array($ext_3, $img_ext_chk_3)) {
            $file_name = time() . rand(1111, 9999);
            $upload_folder = "assets/order_product";
            $fileTempName = $_FILES['product_img_3']['tmp_name'];
            $image_name = $file_name . '.' . strtolower($ext_3);
            $file_info3 = $this->s3->amazonS3Upload($image_name, $fileTempName, $upload_folder);
            $this->data['json'] = array('success' => $file_info3);
            $this->layout(false, 'json');
            return;
        } else {
            $this->data['json'] = array('error' => 'Invalid File Type.(Allowed only JPG, JPEG and PNG)');
            $this->layout(false, 'json');
            return;
        }
    }

    function upload_fourth_file()
    {
        $this->load->library('s3');
        $ext_4 = pathinfo($_FILES['product_img_4']['name'], PATHINFO_EXTENSION);
        $img_ext_chk_4 = array('jpg', 'JPG', 'jpeg', 'JPEG', 'png', 'PNG');
        if (in_array($ext_4, $img_ext_chk_4)) {
            $file_name = time() . rand(1111, 9999);
            $upload_folder = "assets/order_product";
            $fileTempName = $_FILES['product_img_4']['tmp_name'];
            $image_name = $file_name . '.' . strtolower($ext_4);
            $file_info4 = $this->s3->amazonS3Upload($image_name, $fileTempName, $upload_folder);
            $this->data['json'] = array('success' => $file_info4);
            $this->layout(false, 'json');
            return;
        } else {
            $this->data['json'] = array('error' => 'Invalid File Type.(Allowed only JPG, JPEG and PNG)');
            $this->layout(false, 'json');
            return;
        }
    }

    function engagehistory()
    {

        $order_id = $this->input->post('order_id');
        $order = $this->orders_lib->getByID($order_id);
        $this->load->library('whatsappengage_lib');
        $engages = $this->whatsappengage_lib->ordershowbyid($order->id, $order->user_id);
        if ($engages['code'] != '404') {
            if (!empty($engages)) {

                $dataget = $engages['data']['whatsappEngagements']['record'];

                $html = '';

                if (!empty($engages)) {

                    foreach ($dataget as $enghistory) {
                        //  pr($enghistory['requested_address']);
                        $message = $enghistory["formatView"]["displayMessage"];
                        $request_address = $enghistory['requested_address'];
                        $messageformat = $enghistory['formatView']['displayMessage'];

                        $color = $enghistory['formatView']['displayColor'];
                        // $address= !empty($enghistory['requested_address'])?empty($enghistory['requested_address']):'';
                        $time = $enghistory['formatView']['displayTime'];

                        $html .= '<div class="timeline-item">';

                        $html .= '<div class="timeline-wrapper align-items-start">';
                        $html .= '<div class="">';
                        $html .= '<div class="avatar avatar-sm">';
                        $html .= '<img class="avatar-img rounded-circle" src="assets/img/logos/stripe.jpg" alt="">';
                        $html .= '</div>';
                        $html .= '</div>';
                        $html .= '<div class="col-auto">';


                        $html .= '<h6 class="m-0">' . $messageformat . '<i class="mdi mdi-check-circle  text-' . $color . '"></i></h6>';



                        $html .= '<div class="col-auto">';
                        $html .= '<h6 class="m-0"></h6>';
                        $html .= '<span>' . $request_address . '</span>';
                        $html .= '</div>';
                        $html .= '<div class="ml-auto col-auto text-muted">' . $time . '<br>';
                        if (!empty($enghistory['address_status'])  && $enghistory['address_status'] == '5') {
                            $html .= ' <button class="btn btn-sm btn-success" onClick="addressupdate(' . $order->id . ')"> button </button>';
                        }

                        $html .= '</div></div>';



                        $html .= '</div>';


                        $html .= '</div>';
                    }
                }

                echo $html;
            } else {
                echo $html = '<p>No Records Founds</p>';
            }
        } else {
            echo $html = '<p>No Records Founds</p>';
        }
        // return $html;   
    }

    public function seller_update_address()
    {

        $order_id = $this->input->post('order_id');
        $order = $this->orders_lib->getByID($order_id);
        $this->load->library('whatsappengage_lib');
        $engages = $this->whatsappengage_lib->ordershowbyid($order->id, $order->user_id);

        if (!empty($engages)) {


            $html = '';
            $dataget = $engages['data']['whatsappEngagements']['record'];
            $newaddress = '';
            $pincode = '';
            foreach ($dataget as $enghistory) {
                if (!empty($enghistory['address_status']) && $enghistory['address_status'] == '5') {
                    $newaddress = $enghistory['requested_address'];
                }
                if (!empty($enghistory['address_status']) && $enghistory['address_status'] == '7') {
                    $pincode = $enghistory['requested_address'];
                }
            }
            $this->load->library('pincode_lib');

            $citystate = $this->pincode_lib->get_citystate($pincode);

            $html .= '<div class="row">
                                <div class="col-12">
                           
                               <div class="form-group row mb-n25">
                                   <div class="col-md-6 mb-25">

                                       <label> Full Address<span class="required">*</span></label>
                                       <input readonly type="text" name="requested_address" value="' . $newaddress . '" class="form-control ih-medium ip-lightradius-xs b-light address_pop" id="inputNameIcon" placeholder="Please enter the customer full address.">
                                      
                                   </div>
                                   <div class="col-md-6 mb-25">
                                       <label> Pincode<span class="required">*</span></label>
                                       <input readonly type="text" class="form-control ih-medium ip-lightradius-xs b-light pincode_pop" name="requested_pincode" id="inputNameIcon" placeholder="Please enter the customer pincode" value="' . $pincode . '">
                                     
                                   </div>
                                   <div class="col-md-6 mb-25">
                                       <label> City<span class="required">*</span></label>
                                       <input readonly type="text" class="form-control ih-medium ip-lightradius-xs b-light city_pop" value="' . $citystate->city . '" name="requested_city" id="inputNameIcon" placeholder="Please enter the customer city">
                                      
                                   </div>
                                   <div class="col-md-6 mb-25">
                                       <label> State<span class="required">*</span></label>
                                       <input readonly type="text" class="form-control ih-medium ip-lightradius-xs b-light state_pop" value="' . $citystate->state . '" name="requested_state" id="inputNameIcon" placeholder="Please enter the customer state">
                                       <input type="hidden" class="form-control ih-medium ip-lightradius-xs b-light state_pop" value="' . $order->id . '" name="order_id" id="inputNameIcon">
                                 
                                   </div>

                   </div>
               </div>
           </div>';
            echo $html;
        }
    }

    public function whatsapp_addressUpdate()
    {
        $order_id = $this->input->post('order_id');
        $update = array(
            'shipping_address' => $this->input->post('requested_address'),
            'shipping_city' => $this->input->post('requested_city'),
            'shipping_state' => $this->input->post('requested_state'),
            'shipping_zip' => $this->input->post('requested_pincode'),
            'whatsapp_status' => 'confirm'
        );
        $this->orders_lib->update($order_id, $update);
        $this->session->set_flashdata('success', 'Order Saved');
        redirect('orders/view/' . $order_id, true);
    }
}
