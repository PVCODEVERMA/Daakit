<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Orders_lib extends MY_lib
{

    public function __construct()
    {
        parent::__construct();

        $this->CI->load->model('orders_model');
    }

    public function __call($method, $arguments)
    {
        if (!method_exists($this->CI->orders_model, $method)) {
            throw new Exception('Undefined method orders_model::' . $method . '() called');
        }

        return call_user_func_array([$this->CI->orders_model, $method], $arguments);
    }

    function fetchOrders($channel_id = false)
    {
        if (!$channel_id)
            return false;
        //fetch channel api details for this user
        $this->CI->load->library('channels_lib');
        if (!$channel = $this->CI->channels_lib->getByID($channel_id))
            return false;


        switch ($channel->channel) {
            case 'shopify_oneclick':
                $config = array(
                    'channel_id' => $channel_id
                );
                $this->CI->load->library('channels/shopify');
                $shopify = new Shopify($config);
                $orders = $shopify->fetchUnshippedOrders();
                break;
            case 'shopify':
                $config = array(
                    'channel_id' => $channel_id
                );
                $this->CI->load->library('channels/shopify');
                $shopify = new Shopify($config);
                $orders = $shopify->fetchUnshippedOrders();
                break;

            default:
                return false;
        }

        if (!empty($orders)) {
            foreach ($orders as $order) {
                $order['channel_id'] = $channel_id;
                $order['user_id'] = $channel->user_id;
                $order['fulfillment_status'] = $order['status'];
                $easyecomOrderId = isset($order['easyecom_order_id']) ? $order['easyecom_order_id'] : "";
                unset($order['easyecom_order_id']);
                unset($order['status']);
                $order_id = $this->insertOrder($order);
                if ((isset($order['order_source'])) && (($order['order_source'] == 'easyecom'))) {
                    $this->insertEasyecomOrderId($order_id, $easyecomOrderId);
                }
            }
        }
        $this->CI->channels_lib->update($channel->id, array('last_order_fetch_at' => time()));
        return true;
    }


    function fetchSingleOrder($channel_id,$order_id)
    {
        

        if (!$channel_id)
            return false;

        $this->CI->load->library('channels_lib');
        if (!$channel = $this->CI->channels_lib->getByID($channel_id))
            return false;

        switch ($channel->channel) {
            case 'woocommerce':
                $config = array(
                    'channel_id' => $channel_id
                );

                $load_name = 'woocommerce_' . $channel_id;
                $this->CI->load->library('channels/woocommerce', $config, $load_name);

                $orders = $this->CI->{$load_name}->get_single_orders($order_id);

                break;
           
               default:
                return false;
        }
        
        if (!empty($orders)) {
            foreach ($orders as $order) {
                $order['channel_id'] = $channel_id;
                $order['user_id'] = $channel->user_id;
                $order['fulfillment_status'] = $order['status'];
                $order_id = $this->insertOrder($order);
                
            }
        }
        
    }

    function insertOrder($order = false, $id = false)
    {
       
        if (!$order)
            return false;

        $order_products = !empty($order['products']) ? $order['products'] : array();
        unset($order['products']);

        $order_id = false;
        if ($id) {
            $order_user_id = isset($order['user_id']) ? $order['user_id'] : "";
            unset($order['user_id']);
            $this->update($id, $order);
            $order_id = $id;
            if ($order_id && !empty($order_products)) {
                $this->deleteOrderProduct($order_id);
            }
            if (!empty($order_products)) {
                foreach ($order_products as $product) {
                    $save_prod = array();
                    $save_prod['order_id'] = $order_id;
                    $save_prod['product_id'] = isset($product['product_id']) ? $product['product_id'] : '';
                    $save_prod['product_name'] = $product['product_name'];
                    $save_prod['product_qty'] = $product['product_qty'];
                    $save_prod['product_sku'] = $product['product_sku'];
                    $save_prod['product_price'] = $product['product_price'];
                    $id = $this->CI->orders_model->insertProduct($save_prod);
                    if (($id) && (!empty($order_user_id))) {
                        $this->CI->load->library('products_lib');
                        $this->CI->products_lib->CheckUpdateProductDetails($order_user_id, $save_prod);
                    }
                    if ((!empty($order_products)) && (count($order_products) == 1)) {
                        if (($id) && (!empty($order_user_id))) {
                            $this->CI->load->library('products_lib');
                            $this->CI->products_lib->CheckWeightApplyandReplace($order_user_id, $save_prod);
                        }
                    }
                }
            }
        } else {
            $order_id = $this->CI->orders_model->insertOrder($order);
            //do_action('orders.verify_cod', $order_id);
            if ($id && !empty($order_products)) {
                $this->deleteOrderProduct($id);
            }

            if (!empty($order_products)) {
                foreach ($order_products as $product) {
                    $save_prod = array();
                    $save_prod['order_id'] = $order_id;
                    $save_prod['product_id'] = isset($product['product_id']) ? $product['product_id'] : '';
                    $save_prod['product_name'] = $product['product_name'];
                    $save_prod['product_qty'] = $product['product_qty'];
                    $save_prod['product_sku'] = $product['product_sku'];
                    $save_prod['product_price'] = $product['product_price'];
                    $id = $this->CI->orders_model->insertProduct($save_prod);
                    if ($id) {
                        $this->CI->load->library('products_lib');
                        $this->CI->products_lib->CheckUpdateProductDetails($order['user_id'], $save_prod);
                    }
                    if ((!empty($order_products)) && (count($order_products) == 1)) {

                        if (($id) && (!empty($order['user_id']))) {
                            $this->CI->load->library('products_lib');
                            $this->CI->products_lib->CheckWeightApplyandReplace($order['user_id'], $save_prod);
                        }
                    }
                }
            }
        }

        return $order_id;
    }

    function processOrderShipment($order_id = false, $courier_id = false, $user_id = false, $warehouse_id = false, $rto_warehouse_id = false, $essential_order = false, $dg_order = false, $is_insurance = false)
    {
        $this->error = '';

        if (!$user_id || !$order_id) {
            $this->error = 'Invalid Data';
            return false;
        }

        $order = $this->getByID($order_id);

        if (!$order || $order->user_id != $user_id) {
            $this->error = 'Invalid Request';
            return false;
        }

        if ($order->order_type == 'hyperlocal') {
            $this->CI->load->library('hyperlocal_orders_lib');
            if (!$ship_id = $this->CI->hyperlocal_orders_lib->processOrderShipment($order_id, $courier_id, $user_id, $warehouse_id, $rto_warehouse_id)) {
                $this->CI->orders_model->updateFulfillmentStatus($order_id, 'new');

                $this->error = ($this->CI->hyperlocal_orders_lib->get_error()) ? $this->CI->hyperlocal_orders_lib->get_error() : 'Unable to create hyperlocal shipment.';
                return false;
            }
            return true;
        } else if ($order->order_type == 'cargo') {
            $this->CI->load->library('cargo_shipping_lib');
            if (!$ship_id = $this->CI->cargo_shipping_lib->processShipment($order_id, $courier_id, $user_id, $warehouse_id, $rto_warehouse_id)) {
                $this->CI->orders_model->updateFulfillmentStatus($order_id, 'new');

                $this->error = ($this->CI->cargo_shipping_lib->get_error()) ? $this->CI->cargo_shipping_lib->get_error() : 'Unable to create cargo shipment.';
                return false;
            }
            return true;
        }

        if ($order->package_weight > 50000) {
            $this->error = 'Package weight should be less than 50 KG.';
            return false;
        }

        if (!empty($order->package_length) && !empty($order->package_breadth) && !empty($order->package_height)) {
            $vol_weight = round(($order->package_length * $order->package_breadth * $order->package_height) / 5000, 3);
            if ($vol_weight > 50) {
                $this->error = 'Volumetric weight should be less than 50 KG.';
                return false;
            }
        }

        //get warehouse details
        $this->CI->load->library('warehouse_lib');
        $warehouse = $this->CI->warehouse_lib->getByID($warehouse_id);

        if (empty($warehouse) || empty($warehouse->zip)) {
            $this->error = 'Warehouse Details Missing';
            return false;
        }

        if ($warehouse->user_id != $order->user_id) {
            $this->error = 'Warehouse not matched';
            return false;
        }

        $this->CI->load->library('user_lib');
        $this->CI->load->library('plans_lib');
        $user = $this->CI->user_lib->getByID($user_id);

        $courier_slab = '';
        if ($custom_plan = $this->CI->plans_lib->getCustomPlanByName($user->pricing_plan)) {
            $courier_slab = $courier_id;
            $courier_id = $this->getCourierIdForCustomPlan($user_id, $courier_id, $order, $warehouse);
            if (empty($courier_id)) {
                $this->error = 'Courier not serviceable.';
                return false;
            }
        }

        $this->CI->load->library('shipping_lib');

        if ($user->verified == '0') {   //process limit 50 shipment
            $countshipment = $this->CI->shipping_lib->countShipment($user_id);
            if ($countshipment->total >= $this->CI->config->item('without_kyc_order_limit')) {
                $this->error = 'Please complete your KYC. Before process your orders.';
                return false;
            }
        }

        if ($user->verified == '2') {  //junk users
            $this->error = 'Please complete your KYC in profile section.';
            return false;
        }

        $order_products = $this->getOrderProductsGrouped($order_id);

        $order->order_products_grouped = $order_products->product_name;
        $order->order_sku_grouped = $order_products->product_sku;

        $courier_id = apply_filters('order_ship.courier_filter', $courier_id, $order, $warehouse);
        //****************************Courier rule allocation***************************/
        $diplay_courier_id = '';
        $actual_courier_id = '';
        if (!empty($user_id) && !empty($courier_id)) {
            $diplay_courier_id = $courier_id;
            $courier_id = $this->getCourierIdForCourierRule($user_id, $courier_id, $order, $warehouse);
            if (empty($courier_id)) 
                $courier_id=$diplay_courier_id;
            else
                $actual_courier_id=$courier_id;
                $courier_id=$diplay_courier_id;
        }

        if (!$courier_id) {
            // $cred_order = $this->CI->db->from('cred_errors')->where(['order_id' => $order->order_id])->order_by('id', 'desc')->get()->row();
            if(!empty($cred_order->response_data)) {
                $this->error = implode(", ", json_decode($cred_order->response_data, 1));
                return false;
            }

            $this->error = 'No Autoship rule found';
            return false;
        }

        //check if courier is approved to user
        $this->CI->load->library('courier_lib');
        if (!$this->CI->courier_lib->isUSerApprovedToCourier($order->user_id, $courier_id)) {
            $this->error = 'Courier is inactive.';
            return false;
        }

        if ($order->fulfillment_status != 'new') {
            $this->error = 'Already Booked.';
            return false;
        }

        //check if order is serviceable by this courier
        $this->CI->load->library('pincode_lib');

        $this->CI->load->library('courier_lib');
        $courier = $this->CI->courier_lib->getByID($courier_id);

        if (strtolower($order->order_payment_type) == 'reverse') {
            if ($order->qccheck == '1' && !$courier->reverse_qc_pickup) {
                $this->error = 'Courier not serviceable';
                return false;
            }

            if (!$pincode_service = $this->CI->pincode_lib->checkReversePincodeServiceByCourier($warehouse->zip, $courier_id, $order->order_payment_type)) {
                $this->error = 'Pincode not serviceable.';
                return false;
            }

            if (!$pincode_service = $this->CI->pincode_lib->checkReversePickupServiceByCourier($order->shipping_zip, $courier_id)) {
                $this->error = 'Pickup not available.';
                return false;
            }
        } else {
            if ($courier->reverse_pickup == '1' || $courier->reverse_qc_pickup == '1') {
                $this->error = 'Courier not serviceable';
                return false;
            }

            if (!$pincode_service = $this->CI->pincode_lib->checkPincodeServiceByCourier($order->shipping_zip, $courier_id, $order->order_payment_type)) {
                $this->error = 'Pincode not serviceable.';
                return false;
            }

            if (!$pincode_service = $this->CI->pincode_lib->checkPickupServiceByCourier($warehouse->zip, $courier_id)) {
                $this->error = 'Pickup not available.';
                return false;
            }
        }

        if ($user->verified != '1') {
            $this->error = 'Please complete your KYC in profile section.';
            return false;
        }

        $insurance_price = 0;
        if ($is_insurance == '1') {
            $percentage = $this->CI->config->item('insurance_percentage');
            $service_charge = $this->CI->config->item('insurance_service_charge');
            if (!empty($percentage)) {
                $price = round((($percentage / 100) * $order->order_amount), 2);
                $insurance_price = round($price + ($price * ($service_charge / 100)), 2);
            }
        }
        $this->CI->orders_model->updateFulfillmentStatus($order_id, 'booked');

        //do_action('order.status.booked', $order_id);

        //add shipping details to shipping table

        $shipping_save = array(
            'order_id' => $order_id,
            'user_id' => $order->user_id,
            'courier_id' => $courier_id,
            'actual_courier_id'=>!empty($actual_courier_id) ? $actual_courier_id : NULL,
            'warehouse_id' => $warehouse_id,
            'rto_warehouse_id' => $rto_warehouse_id,
            'essential_order' => $essential_order,
            'order_total_amount' => $order->order_amount,
            'payment_type' => $order->order_payment_type,
            /*'courier_type' => $courier_type,*/
            'dg_order' => $dg_order,
            'is_insurance' => $is_insurance,
            'insurance_price' => $insurance_price,
            'courier_slab' => $courier_slab
        );
        //pr($shipping_save,1);
        $this->CI->load->library('shipping_lib');

        $ship_id = $this->CI->shipping_lib->insert($shipping_save);

        do_action('shipping.new', $ship_id, $courier_id);

        return $ship_id;
    }

    function webhookOrders($channel_id = false, $order_data = false, $headers = false, $fetch_via_api = true)
    {
        if (!$channel_id)
            return false;
        //fetch channel api details for this user
        $this->CI->load->library('channels_lib');
        $this->CI->load->library('orders_lib');
        if (!$channel = $this->CI->channels_lib->getByID($channel_id))
            return false;

        switch ($channel->channel) {
            case 'shopify':
                $config = array(
                    'channel_id' => $channel_id
                );

                $load_name = 'shopify_' . $channel_id;
                $this->CI->load->library('channels/shopify', $config, $load_name);

                if ($fetch_via_api)
                    $orders = $this->CI->{$load_name}->fetchUnshippedOrders();
                else
                    $orders = $this->CI->{$load_name}->processWebookOrders($order_data, $headers);

                break;

            default:
                return false;
        }

        if (!empty($orders)) {
            foreach ($orders as $order) {

                $order['channel_id'] = $channel_id;
                $order['user_id'] = $channel->user_id;

                $order_status = $order['status'];
                $easyecomOrderId = isset($order['easyecom_order_id']) ? $order['easyecom_order_id'] : "";
                unset($order['easyecom_order_id']);
                unset($order['status']); // remove status from array
                //get order from db if exists for api order id

                $existing = $this->CI->orders_lib->getByChannelOrderID($channel_id, $order['api_order_id']);
                if (empty($existing)) {
                    if ($order_status != 'cancelled') {
                        $order_id = $this->insertOrder($order);
                        if ((isset($order['order_source'])) && (($order['order_source'] == 'easyecom'))) {
                            $this->insertEasyecomOrderId($order_id, $easyecomOrderId);
                        }
                        do_action('orders.new', $order_id);

                        if ($order['order_date'] >= $channel->created) {
                            do_action('whatsapp_neworder.message', $order_id);
                        }

                        if (!empty($order['checkout_id']) && $channel->abandoned_checkouts == '1') {
                            //mark as checkout complete inside abandoned
                            do_action('checkout.complete', $order['checkout_id']);
                        }
                    }
                } else {
                    $update = array();

                    if ($order_status == 'cancelled') {
                        //cancel order at our end
                        $update = array(
                            'fulfillment_status' => 'cancelled',
                        );

                        $this->update($existing->id, $update);

                        do_action('order.channel_cancelled', $existing->id);
                    } else {

                        //update order tags
                        $update = array(
                            'order_tags' => !empty($order['order_tags']) ? $order['order_tags'] : '',
                        );

                        if ($existing->fulfillment_status == 'new') {
                            $update = array(
                                'order_amount' => !empty($order['order_amount']) ? $order['order_amount'] : '',
                                'tax_amount' => !empty($order['tax_amount']) ? $order['tax_amount'] : '',
                                'shipping_charges' => !empty($order['shipping_charges']) ? $order['shipping_charges'] : '',
                                'discount' => !empty($order['discount']) ? $order['discount'] : '',
                                'billing_fname' => !empty($order['billing_fname']) ? $order['billing_fname'] : '',
                                'billing_company_name' => !empty($order['billing_company_name']) ? $order['billing_company_name'] : '',
                                'billing_lname' => !empty($order['billing_lname']) ? $order['billing_lname'] : '',
                                'billing_address' => !empty($order['billing_address']) ? $order['billing_address'] : '',
                                'billing_address_2' => !empty($order['billing_address_2']) ? $order['billing_address_2'] : '',
                                'billing_phone' => !empty($order['billing_phone']) ? $order['billing_phone'] : '',
                                'billing_city' => !empty($order['billing_city']) ? $order['billing_city'] : '',
                                'billing_state' => !empty($order['billing_state']) ? $order['billing_state'] : '',
                                'billing_country' => !empty($order['billing_country']) ? $order['billing_country'] : '',
                                'billing_zip' => !empty($order['billing_zip']) ? $order['billing_zip'] : '',
                                'shipping_fname' => !empty($order['shipping_fname']) ? $order['shipping_fname'] : '',
                                'shipping_company_name' => !empty($order['shipping_company_name']) ? $order['shipping_company_name'] : '',
                                'shipping_lname' => !empty($order['shipping_lname']) ? $order['shipping_lname'] : '',
                                'shipping_address' => !empty($order['shipping_address']) ? $order['shipping_address'] : '',
                                'shipping_address_2' => !empty($order['shipping_address_2']) ? $order['shipping_address_2'] : '',
                                'shipping_phone' => !empty($order['shipping_phone']) ? $order['shipping_phone'] : '',
                                'shipping_city' => !empty($order['shipping_city']) ? $order['shipping_city'] : '',
                                'shipping_state' => !empty($order['shipping_state']) ? $order['shipping_state'] : '',
                                'shipping_country' => !empty($order['shipping_country']) ? $order['shipping_country'] : '',
                                'shipping_zip' => !empty($order['shipping_zip']) ? $order['shipping_zip'] : '',
                                'package_weight' => !empty($order['package_weight']) ? $order['package_weight'] : '',
                                'order_tags' => !empty($order['order_tags']) ? $order['order_tags'] : '',
                                'user_id' => !empty($order['user_id']) ? $order['user_id'] : '',
                            );
                            if ((strtolower($channel->channel) == 'shopify') || (strtolower($channel->channel) == 'shopify_oneclick') ) {
                                $update['products'] = $order['products'];
                                $update['order_payment_type'] = $order['order_payment_type'];
                                
                            }
                        }

                        $this->insertOrder($update, $existing->id);
                    }
                }
            }
        }

        $this->CI->channels_lib->update($channel->id, array('last_order_fetch_at' => time()));
        return true;
    }

    function insertEasyecomOrderId($order_id, $easyecomOrderId)
    {
        $save = array("order_id" => $order_id, "easyecom_order_id" => $easyecomOrderId);
        $this->CI->load->library('easyecom_lib');
        $this->CI->easyecom_lib->insertEasyEcomData($save);
    }

    function cancelOrder($id = false, $user_id = false)
    {
        if (!$id) {
            $this->error = 'Invalid Order ID';
            return false;
        }
        $order = $this->getByID($id);
        if (empty($order) || $order->user_id != $user_id) {
            $this->error = 'No Records Found';
            return false;
        }
        if ($order->fulfillment_status != 'new') {
            $this->error = 'Order is already booked';
            return false;
        }
        if (empty($order) || $order->user_id != $user_id) {
            $this->error = 'Invalid Request';
            return false;
        }
        $update = array(
            'fulfillment_status' => 'cancelled',
        );
        $this->update($id, $update);
        do_action('order.cancelled', $id);
        return true;
    }


    function cancelChannelOrder($order_id = false)
    {
        if (!$order_id)
            return false;

        $order = $this->getByID($order_id);
        if (empty($order))
            return false;

        $this->CI->load->library('channels_lib');
        $channel = $this->CI->channels_lib->getByID($order->channel_id);

        if (empty($channel) || $channel->auto_cancel != '1')
            return false;


        switch (strtolower($channel->channel)) {
            case 'shopify_oneclick':
                $config = array(
                    'channel_id' => $channel->id
                );

                $load_name = 'shopify_' . $channel->id;
                $this->CI->load->library('channels/shopify', $config, $load_name);

                $this->CI->{$load_name}->cancel_order($order->api_order_id);
                break;
            case 'shopify':
                $config = array(
                    'channel_id' => $channel->id
                );

                $load_name = 'shopify_' . $channel->id;
                $this->CI->load->library('channels/shopify', $config, $load_name);

                $this->CI->{$load_name}->cancel_order($order->api_order_id);
                break;

            default:
                return false;
        }

        return true;
    }

    function getAPIOrders($filters = array())
    {
        $return = array();
        $orders = $this->fetchAPIOrders($filters);

        $order_ids = array_column($orders, 'id');

        if (!empty($orders)) {
            $products = array();
            $products_raw = $this->getBulkOrderProducts($order_ids);
            if (!empty($products_raw)) {
                foreach ($products_raw as $product_raw) {
                    $products[$product_raw->order_id][] = array(
                        'product_name' => $product_raw->product_name,
                        'product_qty' => $product_raw->product_qty,
                        'product_sku' => $product_raw->product_sku,
                        'product_weight' => $product_raw->product_weight,
                        'product_price' => $product_raw->product_price
                    );
                }
            }

            foreach ($orders as $order) {
                $order->products = array();
                if (array_key_exists($order->id, $products)) {
                    $order->products = $products[$order->id];
                }

                $return[] = array(
                    'id' => $order->id,
                    'channel_id' => $order->channel_id,
                    'order_number' => $order->order_id,
                    'order_date' => date('Y-m-d', $order->order_date),
                    'order_amount' => $order->order_amount,
                    'payment_method' => $order->order_payment_type,
                    'shipping_fname' => $order->shipping_fname,
                    'shipping_lname' => $order->shipping_lname,
                    'shipping_address' => $order->shipping_address,
                    'shipping_address_2' => $order->shipping_address_2,
                    'shipping_phone' => $order->shipping_phone,
                    'shipping_city' => $order->shipping_city,
                    'shipping_state' => $order->shipping_state,
                    'shipping_country' => $order->shipping_country,
                    'shipping_zip' => $order->shipping_zip,
                    'package_weight' => $order->package_weight,
                    'package_length' => $order->package_length,
                    'package_height' => $order->package_height,
                    'package_breadth' => $order->package_breadth,
                    'status' => $order->fulfillment_status,
                    'products' => $order->products,
                );
            }
        };

        return $return;
    }

    function processDumpOrder($dump_id = false)
    {

        if (!$dump_id)
            return false;

        $this->CI->load->library('dump_lib');
        $dump = $this->CI->dump_lib->getByID($dump_id);

        if (empty($dump))
            return false;

        $channel_id = $dump->channel_id;
        $data = json_decode(base64_decode($dump->order_data));
        $headers = (array)json_decode(base64_decode($dump->order_headers));

        $this->webhookOrders($channel_id, $data, $headers, false);

        $this->CI->dump_lib->delete($dump_id);

        return true;
    }

    function shipAPIOrder($order_id = false, $user_id = false, $warehouse_id = false, $rto_warehouse_id = false, $selected_courier = 'autoship', $essential_order = false, $dg_order = false, $is_insurance = false, $is_price_calculate = true, $tags = '')
    {
        if (!$order_id || !$user_id) {
            $this->error = 'Invalid Data';
            return false;
        }
        $order = $this->getByID($order_id);
        if (!$order || $order->user_id != $user_id) {
            $this->error = 'Invalid Request';
            return false;
        }
        $order_products = $this->getOrderProductsGrouped($order_id);
        $order->order_products_grouped = $order_products->product_name;
        $order->order_sku_grouped = $order_products->product_sku;
        //get warehouse details
        $this->CI->load->library('warehouse_lib');
        $warehouse = $this->CI->warehouse_lib->getByID($warehouse_id);
        if (empty($warehouse) || empty($warehouse->zip)) {
            $this->error = 'Warehouse Details Missing';
            return false;
        }
        if ($warehouse->user_id != $order->user_id) {
            $this->error = 'Warehouse not matched';
            return false;
        }
        $order->dg_order = $dg_order;
        $this->CI->load->library('user_lib');
        $user = $this->CI->user_lib->getByID($user_id);
        $this->CI->load->library('plans_lib');
        if (($order->order_type == 'ecom') && ($custom_plan = $this->CI->plans_lib->getCustomPlanByName($user->pricing_plan))) {
            if (!empty($custom_plan) && $custom_plan->plan_type=='smart') {
                $selected_courier='autoship';
                $courier_id = apply_filters('order_ship.courier_filter',$selected_courier, $order, $warehouse, true);
            }
        }
        else{
            $courier_id = apply_filters('order_ship.courier_filter', $selected_courier, $order, $warehouse, true);
        }
        if (!$courier_id) {
            $this->error = 'No autoship rule found.';
            return false;
        }
        if(!empty($courier_id))
        {
            $this->CI->load->library('courier_lib');
            $unapproved_courier = $this->CI->courier_lib->approvedToUser($user_id);
            if (!empty($unapproved_courier) && !empty($unapproved_courier->disabled_couriers)) {
                $disabled_couriers = explode(',', $unapproved_courier->disabled_couriers);
                if (in_array($courier_id, $disabled_couriers)) {
                    $this->error = 'Courier disabled.';
                    return false;
                }
            }
        }
        if ($order->fulfillment_status != 'new') {
            $this->error = 'Already Booked.';
            return false;
        }
        if ($is_price_calculate && $user->verified != '1') {
            $this->error = 'Please complete your KYC.';
            return false;
        }
        $courier_slab = '';          
        if (($order->order_type == 'ecom') && !empty($custom_plan)) {
            if (!empty($custom_plan) && $custom_plan->plan_type=='smart') {
                $plan_pricing = $this->CI->plans_lib->getSmartPlanById($custom_plan->id,'1');
                if(empty($plan_pricing)) {
                    $this->error = 'Plan not found.';
                    return false;
                }
                $smart_order = $this->CI->db->from('allocation_rules')->where(['user_id' => $user->id,'user_plan' =>'1','status' =>'1'])->order_by('id', 'desc')->get()->row();
                if(empty($smart_order)) {
                    $this->error = 'Auto allocation rule not found.';
                    return false;
                }
                if(empty($courier_id)) {
                    $this->error = 'Plan not found.';
                    return false;
                }
            }
            if($selected_courier=='autoship')
            {
                $courier_slab =$courier_id;
                $courier_id = $this->getCourierIdForCustomPlan($user_id, $courier_id, $order, $warehouse);
                if (empty($courier_id)) {
                    $this->error = 'Courier not serviceable';
                    return false;
                }
            }
            // else{
            //     $this->CI->load->library('courier_lib');
            //     $courier = $this->CI->courier_lib->getByID($courier_id);
            //     pr($courier,1);
            //     $courier_slab = $courier_id = $courier->courier_type . '_' . $courier->weight . '_' . $courier->additional_weight;
            //     $courier_id = $this->getCourierIdForCustomPlan($user_id, $courier_id, $order, $warehouse);
            //     if (empty($courier_id)) {
            //         $this->error = 'Courier not serviceable.';
            //         return false;
            //     }
            // }
        }
        $this->CI->orders_model->updateFulfillmentStatus($order_id, 'booked');
        $insurance_price = 0;
        if ($is_insurance == '1') {
            $percentage = $this->CI->config->item('insurance_percentage');
            $service_charge = $this->CI->config->item('insurance_service_charge');
            if (!empty($percentage)) {
                $price = round((($percentage / 100) * $order->order_amount), 2);
                $insurance_price = round($price + ($price * ($service_charge / 100)), 2);
            }
        }
        //add shipping details to shipping table
        $shipping_save = array(
            'order_id' => $order_id,
            'user_id' => $order->user_id,
            'courier_id' => $courier_id,
            'warehouse_id' => $warehouse_id,
            'rto_warehouse_id' => $rto_warehouse_id,
            'order_total_amount' => $order->order_amount,
            'payment_type' => $order->order_payment_type,
            'essential_order' => $essential_order,
            'dg_order' => $dg_order,
            'is_insurance' => $is_insurance,
            'insurance_price' => $insurance_price,
            'courier_slab' => $courier_slab,
            'applied_tags' => $tags,
            'order_type' => $order->order_type
        );
        //pr($shipping_save,1);
        $this->CI->load->library('shipping_lib');
        $ship_id = $this->CI->shipping_lib->insert($shipping_save);
        if (!$awb_data = $this->CI->shipping_lib->processShipment($ship_id, true, $is_price_calculate, $selected_courier)) {
            //cancel order + shipment
            $this->error = $this->CI->shipping_lib->get_error();
            $this->CI->shipping_lib->update($ship_id, array('ship_status' => 'cancelled'));
            return false;
        }
        $awb_data['shipment_id'] = $ship_id;
        return $awb_data;
    }

    public function getCourierIdForCustomPlan($user_id = false, $custom_plan = false, $order = array(), $warehouse = array(), $skip_couriers = array(), $is_smart = '0')
    {
        if (!$user_id || !$custom_plan || is_numeric($custom_plan)) {
            $this->error = 'Invalid Data';
            return false;
        }
        $this->CI->load->library('custom_allocation_lib');
        $courier_id = $this->CI->custom_allocation_lib->_getCustomCourier($user_id, $custom_plan, $order, $warehouse, $skip_couriers);

        return $courier_id;
    }

    public function getCourierIdForCourierRule($user_id = false, $custom_plan = false, $order = array(), $warehouse = array(), $skip_couriers = array())
    {
        if (!$user_id || !$custom_plan || !is_numeric($custom_plan)) {
            $this->error = 'Invalid Data';
            return false;
        }
        $this->CI->load->library('courier_rule_allocation_lib');
        $courier_id = $this->CI->courier_rule_allocation_lib->_getCustomCourierRule($user_id, $custom_plan, $order, $warehouse, $skip_couriers);

        return $courier_id;
    }
}
