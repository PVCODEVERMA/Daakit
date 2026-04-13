<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Shopify extends MY_lib
{

    private $api_key;
    private $api_password;
    private $api_secret;
    private $api_host;
    private $api_version;
    private $install_type;
    private $channel_id;
    private $user_id;



    public function __construct($config = false)
    {
        parent::__construct();

        if (empty($config['channel_id']))
            return false;

        if (!$this->_init_properties($config['channel_id']))
            return false;

        $this->api_version = '2023-04';
    }

    function processWebookOrders($data = false, $headers = false)
    {

        //validate webhook token 
        $headers = array_change_key_case($headers, CASE_LOWER);
        if (!array_key_exists('x-shopify-hmac-sha256', $headers))
            return false;

        $hmac_header = $headers['x-shopify-hmac-sha256'];
        $calculated_hmac = base64_encode(hash_hmac('sha256', $data, $this->api_secret, true));
        $verified = hash_equals($hmac_header, $calculated_hmac);



        if (!$verified)
            return false;

        $return = array();
        $order = $this->formatOrder(json_decode($data));

        if (empty($order['api_order_id']))
            return false;

        $return[] = $order;

        return $return;
    }

    private function _init_properties($channel_id = false)
    {
        if (!$channel_id)
            return false;

        $this->CI->db->where('id', $channel_id);
        $this->CI->db->limit(1);
        $q = $this->CI->db->get('user_channels');

        if ($q->num_rows() >= 1)
            $channel = $q->row();
        else
            return false;

        $this->install_type = $channel->integration_type;

        if ($channel->channel == 'shopify_oneclick') {
            $this->api_key = $this->CI->config->item('shopify_key');
            $this->api_secret = $this->CI->config->item('shopify_secret');
        } else {
            $this->api_key = $channel->api_field_2;
            $this->api_secret = $channel->api_field_4;
        }

        $this->api_host = $channel->api_field_1;
        $this->api_password = $channel->api_field_3;
        $this->channel_id = $channel_id;
        $this->user_id = $channel->user_id;        
        return true;
    }

    function fetchUnshippedOrders($limit = 250)
    {
        $filters = array(
            'fulfillment_status' => 'unshipped',
            'limit' => $limit
        );
        $orders = $this->execute('orders', 'GET', $filters);

        $api_orders = array();
        if (!empty($orders->orders)) {
            foreach ($orders->orders as $order) {
                
                $api_order = $this->formatOrder($order);
                $api_orders[$api_order['api_order_id']] = $api_order;
            }
        }
        
        return $api_orders;
    }

    function formatPhone($phone = false)
    {
        if (!$phone)
            return false;
        $phone = preg_replace('/\D/', '', $phone);
        $phone = substr($phone, -10);
        return $phone;
    }

    

    function formatOrder($order = false)
    {
         
        if (!$order)
            return false;

        if ($order->fulfillment_status == 'fulfilled') {
            $this->updateOrderTag($order->id, $this->channel_id);
            return false;
        }

        $tax_amount = "0";
        $current_weight = "0"; //gram;
        if (empty($order->taxes_included)) {
            $tax_amount = $order->current_total_tax;
        }
        //check billing city and state is empty
        $billing_shipping_data = $this->formatCityState($order);
        $billing_city = isset($billing_shipping_data['billing_city'])?$billing_shipping_data['billing_city']:"";
        $billing_state = isset($billing_shipping_data['billing_state'])?$billing_shipping_data['billing_state']:"";
        $shipping_city = isset($billing_shipping_data['shipping_city'])?$billing_shipping_data['shipping_city']:"";
        $shipping_state = isset($billing_shipping_data['shipping_state'])?$billing_shipping_data['shipping_state']:"";
       
        $api_order = array(
            'api_order_id' => $order->id,
            'order_no' => $order->name,
            'order_date' => strtotime($order->created_at),
            'checkout_id' => !empty($order->checkout_id) ? $order->checkout_id : '',
            'order_amount' => !empty($order->current_total_price) ? round($order->current_total_price, 2) : 0,
            'order_payment_type' => ($order->financial_status == 'paid' ? 'prepaid' : 'COD'),
            'customer_name' => !empty($order->customer->first_name) ? $order->customer->first_name : (!empty($order->customer->last_name) ?  $order->customer->last_name : ''),
            'customer_phone' => !empty($order->customer->phone) ?  $this->formatPhone($order->customer->phone) : '',
            'customer_email' => !empty($order->customer->email) ?  $order->customer->email : '',
            'billing_fname' => !empty($order->billing_address->first_name) ? $order->billing_address->first_name : '',
            'billing_company_name' => !empty($order->billing_address->company) ? $order->billing_address->company : '',
            'billing_lname' => !empty($order->billing_address->last_name) ? $order->billing_address->last_name : '',
            'billing_address' => !empty($order->billing_address->address1) ? $order->billing_address->address1 : '',
            'billing_address_2' => !empty($order->billing_address->address2) ? $order->billing_address->address2 : '',
            'billing_phone' => $this->formatPhone(!empty($order->billing_address->phone) ? $order->billing_address->phone : ''),
            'billing_city' => $billing_city,
            'billing_state' => $billing_state,
            'billing_country' => !empty($order->billing_address->country) ? $order->billing_address->country : '',
            'billing_zip' => preg_replace('/\D/', '', !empty($order->billing_address->zip) ? $order->billing_address->zip : ''),
            'shipping_fname' => !empty($order->shipping_address->first_name) ? $order->shipping_address->first_name : (!empty($order->billing_address->first_name) ? $order->billing_address->first_name : ''),
            'shipping_company_name' => !empty($order->shipping_address->company) ? $order->shipping_address->company : (!empty($order->billing_address->company) ? $order->billing_address->company : ''),
            'shipping_lname' => !empty($order->shipping_address->last_name) ? $order->shipping_address->last_name : (!empty($order->billing_address->last_name) ? $order->billing_address->last_name : ''),
            'shipping_address' => !empty($order->shipping_address->address1) ? $order->shipping_address->address1 : (!empty($order->billing_address->address1) ? $order->billing_address->address1 : ''),
            'shipping_address_2' => !empty($order->shipping_address->address2) ? $order->shipping_address->address2 : (!empty($order->billing_address->address2) ? $order->billing_address->address2 : ''),
            'shipping_phone' => $this->formatPhone(!empty($order->shipping_address->phone) ? $order->shipping_address->phone : (!empty($order->billing_address->phone) ? $order->billing_address->phone : '')),
            'shipping_city' => $shipping_city,
            'shipping_state' => $shipping_state,
            'shipping_country' => !empty($order->shipping_address->country) ? $order->shipping_address->country : (!empty($order->billing_address->country) ? $order->billing_address->country : ''),
            'shipping_zip' => preg_replace('/\D/', '', !empty($order->shipping_address->zip) ? $order->shipping_address->zip : (!empty($order->billing_address->zip) ? $order->billing_address->zip : '')),
            'order_tags' =>  !empty($order->tags) ? $order->tags : '',
            'tax_amount' => $tax_amount,
            'products' => array(),
            'status' => !empty($order->cancelled_at) ? 'cancelled' : 'new',
            'shipping_charges' => !empty($order->total_shipping_price_set->shop_money->amount) ? $order->total_shipping_price_set->shop_money->amount : '0',
            'discount' => !empty($order->total_discounts_set->shop_money->amount) ? $order->total_discounts_set->shop_money->amount : '0',
        );
        foreach ($order->line_items as $product) {

            if (empty($product->fulfillable_quantity)) {
                continue;
            }

            $api_order['products'][] = array(
                'product_id' => $product->variant_id,
                'product_name' => $product->name,
                'product_qty' => $product->fulfillable_quantity,
                'product_sku' => $product->sku,
                'product_weight' => $product->grams,
                'product_price' => round($product->price, 2),
            );
            $current_weight += $product->grams * $product->fulfillable_quantity;
        }
        $api_order['package_weight'] = $current_weight;
        return $api_order;
    }


    function formatCityState($order){
        $formatdata = array();
        $billing_city = $shipping_city = $shipping_state = $billing_state =  "";
        $billing_city = !empty($order->billing_address->city) ? $order->billing_address->city : '';
        $billing_state = !empty($order->billing_address->province) ? $order->billing_address->province : '';
        $billing_pincode = preg_replace('/\D/', '', !empty($order->billing_address->zip) ? $order->billing_address->zip : '');
        $shipping_city  = !empty($order->shipping_address->city) ? $order->shipping_address->city : (!empty($order->billing_address->city) ? $order->billing_address->city : '');
        $shipping_state = !empty($order->shipping_address->province) ? $order->shipping_address->province : (!empty($order->billing_address->province) ? $order->billing_address->province : '');
        $shipping_zip   = preg_replace('/\D/', '', !empty($order->shipping_address->zip) ? $order->shipping_address->zip : (!empty($order->billing_address->zip) ? $order->billing_address->zip : ''));
        
        if((empty($billing_city)) ||  (preg_match('/^[^a-zA-Z0-9]+$/', $billing_city))){
           if($this->checkCityState('city',$billing_city,$billing_pincode)){
              $billing_city = $this->checkCityState('city',$billing_city,$billing_pincode);
            }
            if($this->checkCityState('state',$billing_state,$billing_pincode)){
                $billing_state = $this->checkCityState('state',$billing_state,$billing_pincode);
            }
        }
       
        if((empty($shipping_city)) ||  (preg_match('/^[^a-zA-Z0-9]+$/', $shipping_city))){
            if($this->checkCityState('city',$shipping_city,$shipping_zip)){
               $shipping_city = $this->checkCityState('city',$shipping_city,$shipping_zip);
             }
             if($this->checkCityState('state',$shipping_state,$shipping_zip)){
                $shipping_state = $this->checkCityState('state',$shipping_state,$shipping_zip);
              }
        }
       
        $formatdata['billing_city'] =   $billing_city;
        $formatdata['billing_state'] =  $billing_state;
        $formatdata['shipping_city'] =  $shipping_city;
        $formatdata['shipping_state'] = $shipping_state;
        return $formatdata;
    }


    function checkCityState($key,$val,$pincode){
            if(!empty($pincode)){
                 $result = $this->getCityStateByPincode($pincode);
                if(!empty($result)){
                    $result = isset($result[$key])?$result[$key]:"";
                    return $result;
                }
            }
        
        return false;
    }

    function getCityStateByPincode($pincode){
       
         $this->CI->load->library('pincode_lib');
         $to_pincode = $this->CI->pincode_lib->get_citystate(trim($pincode));

        
        if (empty($to_pincode)) {
            $pincodedata = $this->CI->pincode_lib->get_pincodecitystate(trim($pincode));
            
            if(!empty($pincodedata)){
              
                if( (isset($pincodedata->state_code)) &&  (strlen($pincodedata->state_code) == 2)){
                    $this->CI->load->library('state_lib');
                    $statedeatil = $this->CI->state_lib->getStateName($pincodedata->state_code);
                    $pincodedata->state_code = $statedeatil->state_name;
                }
               
                $save = array(
                    'pincode'=> trim($pincode),
                    'city'  => $pincodedata->city,
                    'state' => $pincodedata->state_code
                );
                $this->CI->pincode_lib->insert($save);

            }else{
               return false;
            }
          
           
           
        }
         
        $city = !empty($to_pincode)?isset($to_pincode->city)?$to_pincode->city:"":"";
        if(empty($city )){
         $city = !empty($pincodedata)?isset($pincodedata->city)?$pincodedata->city:"":"";
        }
        $state = !empty($to_pincode)?isset($to_pincode->state)?$to_pincode->state:"":"";
        if(empty($state )){
         $state = !empty($pincodedata)?isset($pincodedata->state_code)?$pincodedata->state_code:"":"";
        }

           $result =  array("city"=>$city,"state"=>$state);
           return $result;

           
    }

    function lisWebhooks()
    {
        $orders = $this->execute('webhooks', 'GET');
        return $orders->webhooks;
    }

    function createOrderWebhook($url = false, $order_status = false)
    {
        $post_data = array(
            'webhook' => array(
                'topic' => 'orders/create',
                'address' => $url,
                'format' => 'json'
            )
        );

        if ($order_status == 'cancelled') {
            $post_data['webhook']['topic'] = 'orders/cancelled';
        }
        if ($order_status == 'updated') {
            $post_data['webhook']['topic'] = 'orders/updated';
        }

        $postdata = http_build_query($post_data);

        echo $api_url = "https://{$this->api_key}:{$this->api_password}@{$this->api_host}/admin/api/$this->api_version/webhooks.json";
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt_array($curl, array(
            CURLOPT_URL => $api_url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "POST",
            CURLOPT_HTTPHEADER => array(
                "content-type: application/x-www-form-urlencoded",
                //"X-Shopify-Access-Token: " . $this->api_password
            ),
            CURLOPT_POSTFIELDS => $postdata
        ));
        $response = curl_exec($curl);
        $err = curl_error($curl);
        curl_close($curl);

        $response = json_decode($response);

        if (!empty($response->errors))
            return false;

        return true;
    }


    function fulfill_order_old($order_id = false, $courier = false, $tracking_no = false, $subdomain = false){
        try {
                if (!$order_id || !$courier || !$tracking_no) {
                    $this->error = 'Invalid Data';
                    return false;
                }
                
                $FulfillmentData = $this->getPreFulfillmentOrder($order_id);
                switch ($courier) {
                    case 'Delhivery Air':
                    case 'Delhivery Surface':
                    case 'Delhivery Surface 2 K.G':
                    case 'Delhivery Surface 5 K.G':
                        $courier = 'Delhivery';
                        break;
                    case 'DTDC Air':
                    case 'DTDC Surface':
                    case 'DTDC Surface 5 K.G':
                        $courier = 'DTDC';
                        break;
                    default:
                }
                if($FulfillmentData){
                    $fulfillment = array();
                    $fulfillment['fulfillment']['message'] = "Order shipped with ".$courier;
                    $fulfillment['fulfillment']['notify_customer'] = false;
                    $fulfillment['fulfillment']['tracking_info']['number'] = $tracking_no;
                    $fulfillment['fulfillment']['tracking_info']['url'] = 'https://' . $subdomain . 'ordr.live/trk/' . $tracking_no;
                    $fulfillment['fulfillment']['tracking_info']['company'] = $courier;
                    foreach($FulfillmentData->fulfillment_orders as $key => $fulfill_result){
                        $fulfillment['fulfillment']['line_items_by_fulfillment_order'][$key]['fulfillment_order_id'] = $fulfill_result->id; 
                        foreach($fulfill_result->line_items as $keys => $line_items){
                        $fulfillment['fulfillment']['line_items_by_fulfillment_order'][$key]['fulfillment_order_line_items'][$keys]['id'] = $line_items->id;
                        $fulfillment['fulfillment']['line_items_by_fulfillment_order'][$key]['fulfillment_order_line_items'][$keys]['quantity'] = $line_items->quantity;
                        }  
                    }
                    $result =   $this->createFulfill($fulfillment);
                    if(!empty($result)){
                       return true;
                    }
                    return false;
                }    
          
        } catch (Exception $e) {
            $this->error =  $e->getMessage();
            return false;
        }
        
    }


    function createFulfill($data){
        $api_url = "https://{$this->api_host}/admin/api/$this->api_version/fulfillments.json";
        $curl = curl_init();
        curl_setopt_array($curl, array(
          CURLOPT_URL => $api_url,
          CURLOPT_RETURNTRANSFER => true,
          CURLOPT_ENCODING => '',
          CURLOPT_MAXREDIRS => 10,
          CURLOPT_TIMEOUT => 0,
          CURLOPT_FOLLOWLOCATION => true,
          CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
          CURLOPT_CUSTOMREQUEST => 'POST',
          CURLOPT_POSTFIELDS => json_encode($data),
          CURLOPT_HTTPHEADER => array(
            'X-Shopify-Access-Token: '.$this->api_password.'',
            'Content-Type: application/json'
          ),
        ));
        $response = curl_exec($curl);
        $err = curl_error($curl);
        curl_close($curl);
        if(empty($response)){
            $this->error = "No Response from fulfillment api";
            return false; 
        }
        $response = json_decode($response);
        if (!empty($response->errors)) {
            $this->error = json_encode($response->errors);
            return false;
        }
        return $response;
        
    }

    function getPreFulfillmentOrder($order_id){
        $api_url = "https://{$this->api_key}:{$this->api_password}@{$this->api_host}/admin/api/$this->api_version/orders/{$order_id}/fulfillment_orders.json";
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt_array($curl, array(
            CURLOPT_URL => $api_url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "GET",
        ));
        $response = curl_exec($curl);
        $err = curl_error($curl);
        curl_close($curl);
        if(empty($response)){
            $this->error = "No Response from fulfillment api";
            return false; 
        }
        $response = json_decode($response);
        if (!empty($response->errors)) {
            $this->error = json_encode($response->errors);
            return false;
        }

    
        if ((isset($response->fulfillment_orders)) &&  (empty($response->fulfillment_orders))) {
            $this->error = 'Fulfillment order data is not found';
            return false;
        }
        return $response;
      


    }

    function fulfill_order_old1($order_id = false, $courier = false, $tracking_no = false, $subdomain = false)
    {


        if (!$order_id || !$courier || !$tracking_no) {
            $this->error = 'Invalid Data';
            return false;
        }

        //get locations from shopify
        $locations = $this->execute('locations', 'GET', array('active' => 'true'));

        if (empty($locations->locations)) {
            $this->error = 'No Location Found';
            return false;
        }


        //mark an order as fulfilled inside shopify

       
        $subdomain = !empty($subdomain) ? $subdomain . '.' : '';

        foreach ($locations->locations as $location) {

            $post_data = array(
                'fulfillment' => array(
                    'location_id' => $location->id,
                    'tracking_company' => $courier,
                    'tracking_number' => $tracking_no,
                    'tracking_url' => '',
                )
            );

            $postdata = http_build_query($post_data);

            echo $api_url = "https://{$this->api_key}:{$this->api_password}@{$this->api_host}/admin/api/$this->api_version/orders/{$order_id}/fulfillments.json";
            $curl = curl_init();
            curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 0);
            curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0);
            curl_setopt_array($curl, array(
                CURLOPT_URL => $api_url,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => "",
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 30,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => "POST",
                CURLOPT_HTTPHEADER => array(
                    "content-type: application/x-www-form-urlencoded",
                    //"X-Shopify-Access-Token: " . $this->api_password
                ),
                CURLOPT_POSTFIELDS => $postdata
            ));
            
            $response = curl_exec($curl);

            $err = curl_error($curl);
            curl_close($curl);

            $response = json_decode($response);
            pr($err,1);
            do_action('log.create', 'shopify', [
                'action' => 'shopify_orders',
                'ref_id' => $order_id,
                'user_id' => $this->user_id,
                'data' => array('request'=>$postdata,'response'=>$response,'error'=>$err)
            ]);


            if (!empty($response->fulfillment))
                return true;
        }

        if (!empty($response->errors)) {
            $this->error = json_encode($response->errors);
            return false;
        }

        return true;
    }

    function fulfill_order($order_id = false, $courier = false, $tracking_no = false, $subdomain = false)
    {
        if (!$order_id || !$courier || !$tracking_no) {
            $this->error = 'Invalid Data';
            return false;
        }

        $api_url = "https://{$this->api_host}/admin/api/$this->api_version/orders/{$order_id}/fulfillment_orders.json?status=open";
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt_array($curl, array(
            CURLOPT_URL => $api_url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "GET",
            CURLOPT_HTTPHEADER => array(
                "content-type: application/json",
                "X-Shopify-Access-Token: " . $this->api_password
            )
        ));
        $fulfillmentOrder = curl_exec($curl);
        $err = curl_error($curl);
        curl_close($curl);
        $fulfillmentOrder = json_decode($fulfillmentOrder);

        if (empty($fulfillmentOrder) || empty($fulfillmentOrder->fulfillment_orders)) {
            $this->error = 'Fulfill id not found';
            return false;
        }

        // $fulfillment_order_id = 13851960344894;
        $fulfillment_order_id = $fulfillmentOrder->fulfillment_orders[0]->id;

        $subdomain = !empty($subdomain) ? $subdomain . '.' : '';
        $post_data = array(
            'fulfillment' => array(
                'line_items_by_fulfillment_order' => array(
                    array(
                        'fulfillment_order_id' => $fulfillment_order_id
                    )
                ),
                'notify_customer'=>(empty($this->customer_notify) ? false : true),
                'tracking_info' => array(
                    'number' => $tracking_no,
                    'url' => '',
                    "company" => $courier
                )
            )
        );
        
        $postdata = json_encode($post_data);

        $api_url = "https://{$this->api_key}:{$this->api_password}@{$this->api_host}/admin/api/$this->api_version/fulfillments.json";
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt_array($curl, array(
            CURLOPT_URL => $api_url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "POST",
            CURLOPT_HTTPHEADER => array(
                "content-type: application/json",
                //"X-Shopify-Access-Token: " . $this->api_password
            ),
            CURLOPT_POSTFIELDS => $postdata
        ));
        $response = curl_exec($curl);
        $err = curl_error($curl);
        curl_close($curl);

        $response = json_decode($response);
        do_action('log.create', 'shopify', [
            'action' => 'shopify_orders',
            'ref_id' => $order_id,
            'user_id' => $this->user_id,
            'user_id' => $this->user_id,
            'data' => array('request'=>$postdata,'response'=>$response,'error'=>$err)
        ]);

        if (!empty($response->fulfillment))
            return true;


        if (!empty($response->errors)) {
            $this->error = json_encode($response->errors);
            return false;
        }

        return true;
    }

    function codMarkAsPaid($order_id = false)
    {

        if (!$order_id)
            return false;

        $post_data = array(
            'transaction' => array(
                'kind' => 'capture',
                'source' => 'external',
            )
        );

        $postdata = http_build_query($post_data);

        $api_url1 = "https://{$this->api_key}:{$this->api_password}@{$this->api_host}/admin/api/$THIS->API_VERSION/orders/{$order_id}/transactions.json";
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt_array($curl, array(
            CURLOPT_URL => $api_url1,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "POST",
            CURLOPT_HTTPHEADER => array(
                "content-type: application/x-www-form-urlencoded",
                //"X-Shopify-Access-Token: " . $this->api_password
            ),
            CURLOPT_POSTFIELDS => $postdata
        ));
        $response = curl_exec($curl);
        $err = curl_error($curl);
        curl_close($curl);

        $response = json_decode($response);

        if (!empty($response->errors))
            return false;

        return true;
    }

    function cancel_order($order_id = false)
    {

        if (!$order_id)
            return false;


        $api_url = "https://{$this->api_key}:{$this->api_password}@{$this->api_host}/admin/api/$THIS->API_VERSION/orders/{$order_id}/cancel.json";

        $curl = curl_init();
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt_array($curl, array(
            CURLOPT_URL => $api_url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "POST",
            CURLOPT_HTTPHEADER => array(
                "content-type: application/x-www-form-urlencoded",
                //"X-Shopify-Access-Token: " . $this->api_password
            ),
        ));
        $response = curl_exec($curl);
        $err = curl_error($curl);
        curl_close($curl);

        $response = json_decode($response);

        if (!empty($response->error))
            return false;

        return true;
    }

    function execute($endpoint = false, $method = 'GET', $send_data = array())
    {
        try {
            $postdata = http_build_query($send_data);
            $api_url = "https://{$this->api_key}:{$this->api_password}@{$this->api_host}/admin/api/$this->api_version/{$endpoint}.json";
            // Initialize cURL session
            $ch = curl_init();
            // Set default headers
            $headers = array(
                'Content-type: application/x-www-form-urlencoded',
                // "X-Shopify-Access-Token: " . $this->api_password
            );
            // Common cURL options based on the request method
            switch ($method) {
                case 'POST':
                    curl_setopt($ch, CURLOPT_URL, $api_url);
                    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                    curl_setopt($ch, CURLOPT_POST, true);
                    curl_setopt($ch, CURLOPT_POSTFIELDS, $postdata);
                    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
                    break;
                case 'PUT':
                    curl_setopt($ch, CURLOPT_URL, $api_url);
                    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'PUT');
                    curl_setopt($ch, CURLOPT_POSTFIELDS, $postdata);
                    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
                    break;
                default: // GET method
                    curl_setopt($ch, CURLOPT_URL, $api_url . '?' . $postdata);
                    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
                    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // Disable SSL verification
                    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false); // Disable host verification
                    break;
            }
            // Execute cURL session and get response
            $api_result = curl_exec($ch);
            // Check for errors
            $error_message='';
            if (curl_errno($ch)) {
                 $error_message= 'cURL error: ' . curl_error($ch);
            } else {
                // Decode the JSON response
                $api_result = json_decode($api_result);
            }
            if(!empty($api_result->errors)){
                $error_message=$api_result->errors;
            }

            if($error_message){
                do_action('log.create', 'shopify', [
                    'action' => 'shopify_orders',
                    'ref_id' => $this->channel_id,
                    'user_id' => $this->user_id,
                    'data' => array('request'=>$postdata,'response'=>$error_message)
                ]);
            }
            // Close cURL session
            curl_close($ch);
            return $api_result;
            
        } catch (Exception $e) {
            echo $e->getMessage();
        }
    }

    function fetchAbandonedCheckouts($limit = 250)
    {
        $filters = array(
            //'status' => 'open',
            'limit' => $limit,
            'order' => 'created_at desc',
            'created_at_max' => date('Y-m-d\TH:i:s')
        );

        $postdata = http_build_query($filters);
        $api_url = "https://{$this->api_key}:{$this->api_password}@{$this->api_host}/admin/api/$this->api_version/checkouts.json";

        try {
            $opts = array(
                'http' =>
                array(
                    'method' => 'GET',
                    'header' => array(
                        'Content-type: application/x-www-form-urlencoded',
                        //"X-Shopify-Access-Token: " . $this->api_password
                    ),
                    //'content' => $postdata
                )
            );

            $context = stream_context_create($opts);
            $api_result = file_get_contents($api_url . '?' . $postdata, false, $context);

            $api_result = json_decode($api_result);



            if (empty($api_result->checkouts))
                return false;

            return $api_result->checkouts;
        } catch (Exception $e) {
            return false;
        }
    }
    /**catalog functions */
    public function productFeed()
    {
        $api_url = "https://{$this->api_key}:{$this->api_password}@{$this->api_host}/admin/api/$this->api_version/products.json";
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt_array($curl, array(
            CURLOPT_URL => $api_url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "GET",
            CURLOPT_HTTPHEADER => array(
                "content-type: application/x-www-form-urlencoded",
                //"X-Shopify-Access-Token: " . $this->api_password
            ),
        ));
        $response = curl_exec($curl);
        $err = curl_error($curl);
        curl_close($curl);
        $response = json_decode($response);
        $response = $this->formatProduct($response);
        return $response;
    }
    function formatProduct($product = array())
    {
        if (empty($product->products)) {
            return false;
        }
        $productData = $product->products;
        $data = array();
        $reqData = array();
        foreach ($productData as $productArr) {
            $data['parent_product_id'] = $productArr->id;
            $data['product_image'] = isset($productArr->image->src) ? $productArr->image->src : '';
            $productVariants = $productArr->variants;
            foreach ($productVariants as $variants) {
                $data['product_id'] = $variants->id;
                $data['product_name'] = $productArr->title . '-' . $variants->title;
                $data['product_weight'] = $variants->grams;
                $data['product_sku'] = $variants->sku;
                $data['product_price'] = $variants->price;
                $reqData[$variants->id] = $data;
            }
        }
        return $reqData;
    }

    /* 
    * Function to update the shopify status 
    * @order_id  : Actual order id coming from Shopify by API to us
    * @o_status : status to update the shopify order
    * @ch_shopurl :  url of the shop got from channel table 
    */
    function shopify_status_update($order_id = '', $status = '')
    {
        if ($status == 'cancelled')
            return true;
        
            if ($order_id != '' && $status != '') {
            $fullfillment_id = $this->shopify_get_fulfillmentid($order_id);

            if ($fullfillment_id) {
                if ($this->push_shopify_status($order_id, $status, $fullfillment_id))
                    return true;
                else
                    return false;
            }

            //  Normal order without fullfillment can be cancelled using below code line //
            if (!$fullfillment_id  &&  $status == 'cancelled') {
                $this->cancel_order($order_id);
            }

            return false;
        }
    }

    /* 
    * Function to get fullfillment id by orderid 
    * @order_id  : Actual order id coming from Shopify by API to us
    * @ch_shopurl : url of the shop got from channel table 
    */
    function shopify_get_fulfillmentid($order_id = '')
    {
        if ($order_id != '') {

            try {
                $api_url    = "https://{$this->api_key}:{$this->api_password}@{$this->api_host}/admin/api/$this->api_version/orders/" . $order_id . "/fulfillments.json";
                $curl       = curl_init();

                curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 0);
                curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0);
                curl_setopt_array($curl, array(
                    CURLOPT_URL => $api_url,
                    CURLOPT_RETURNTRANSFER => true,
                    CURLOPT_ENCODING => "",
                    CURLOPT_MAXREDIRS => 10,
                    CURLOPT_TIMEOUT => 30,
                    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                    CURLOPT_CUSTOMREQUEST => "GET",
                    CURLOPT_HTTPHEADER => array(
                        "X-Shopify-Access-Token: " . $this->api_password,
                    ),
                ));
                $response = curl_exec($curl);
                $err = curl_error($curl);
                curl_close($curl);

                if ($response) {
                    $response      = json_decode($response);

                    if (isset($response->fulfillments[0]->id))
                        return  $response->fulfillments[0]->id;
                    else
                        return false;
                } else {
                    return false;
                }
            } catch (Exception $e) {
                return false;
            }
        }
        return false;
    }
   /*
    *  
    Different status messages for SHOPIFY from shopify docs
    in_transit: The shipment is being transported between shipping facilities on the way to its destination.
    out_for_delivery: The shipment is being delivered to its final destination.
    delivered: The shipment was successfully delivered.
    failure: Something went wrong when pulling tracking information for the shipment, such as the tracking number was invalid or the shipment was canceled.
    
    */
    function push_shopify_status($orderid = '', $status = '',  $fullfillment_id = '')
    {
        
        $api_url  = "https://{$this->api_key}:{$this->api_password}@{$this->api_host}/admin/api/$this->api_version/orders/" . $orderid . "/fulfillments/" . $fullfillment_id . "/events.json";
        $status = trim(strtolower(str_replace(" ", "_", $status)));

        if ($status == 'cancelled')
            $status = 'failure';
        else
            $status = $status;

        $mapped = array('in_transit', 'failure', 'out_for_delivery', 'delivered');

        if (in_array($status, $mapped)) {

            $date       = date(DATE_ISO8601, strtotime(date("Y-m-d H:i:s")));
            $postdata   = array('event' => array('status' => $status, 'created_at' => $date, 'happened_at' => $date));

            if ($status == 'failure') { // in case of fullfillment order cancellation just remove the AWB of order shipment //
                $this->CI->load->library('shipping_lib');
                $this->CI->shipping_lib->remove_shopify_orderawb($orderid);
                $api_url =   "https://{$this->api_key}:{$this->api_password}@{$this->api_host}/admin/api/$this->api_version/fulfillments/" . $fullfillment_id . "/cancel.json";
                $postdata = array();
            }
            // $this->CI->load->library('channels_lib');
            // $this->CI->channels_lib->delete_retrigger($orderid);
            try {
                $curl = curl_init();
                curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 0);
                curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0);
                curl_setopt_array($curl, array(
                    CURLOPT_URL => $api_url,
                    CURLOPT_RETURNTRANSFER => true,
                    CURLOPT_ENCODING => "",
                    CURLOPT_MAXREDIRS => 10,
                    CURLOPT_TIMEOUT => 30,
                    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                    CURLOPT_CUSTOMREQUEST => "POST",
                    CURLOPT_HTTPHEADER => array(
                        // "content-type: application/x-www-form-urlencoded",
                        "X-Shopify-Access-Token: " . $this->api_password,
                        'Content-Type: application/json'
                    ),
                    CURLOPT_POSTFIELDS => json_encode($postdata)
                ));
                $response   = curl_exec($curl);
                $err        = curl_error($curl);
                curl_close($curl);

                $response      = json_decode($response);

                if ($response){
                    return true;
                }
                else{
                    $this->CI->channels_lib->save_retrigger(['order_id'=>$orderid]);
                    return false;
                }
            } catch (Exception $e) {
                $this->CI->channels_lib->save_retrigger(['order_id'=>$orderid]);
                return false;
            }
        }
        return false;
    }



    function updateOrderTag($api_order_id, $channel_id)
    {
        $this->CI->load->library('orders_lib');
        $result =  $this->CI->orders_lib->checkShopifyOrderExist($api_order_id, $channel_id);
        if (empty($result))
            return false;
        $orderId = isset($result->id) ? $result->id : "";
        if (!empty($orderId)) {
            $save['order_tags'] = "fulfilled";
            $this->CI->orders_lib->update($orderId, $save);
        }
        return false;
    }


    function whatsupConfirm($order_id,$channel_id,$data){
        if((empty($data)) && (empty($channel_id)) && (empty($order_id)))
        return false;
        $whatsapp_status = isset($data->order_status)?$data->order_status:"";
        $message = "";
        switch($whatsapp_status){
            case 'confirm':
                $message = "Order confirmed on Whatsapp";
            break;
            case 'cancel':
                $message = "Order canceled on Whatsapp";
            break;
            default:
            return false;
        }
        $get_existing_tag = $this->fetch_signle_order($order_id);
        if($get_existing_tag){
            $message = $get_existing_tag." , ".$message;  
        }
        $postdata = array("order"=>array("id"=>$order_id,"tags"=>$message));
        $orders = $this->execute('orders/'.$order_id , 'PUT', $postdata);
    }


    function fetch_signle_order($order_id){

        $orders = $this->execute('orders/'.$order_id , 'GET');
        $tag = isset($orders->order->tags)?$orders->order->tags:"";
        if(!empty($tag)){
            return $tag;
        }
        return false;

    }





    /**end catalog functions */
}
