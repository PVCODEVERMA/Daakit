<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Purpledrone extends MY_lib
{
    private $api_url;
    private $api_username;
    private $api_password;
    public function __construct($config = false)
    {
        parent::__construct();
        $this->api_username = 'daakit@fulfillzy.com'; //live
        $this->api_password = 'D@@4!tFzIntegn'; //live
        $this->api_url = 'https://backend.fulfillzy.com/'; //live
    }

    function createUpdateToken($expired = false)
    {
        $created_time = time();

        $url = $this->api_url . 'authToken';

        $data = array(
                "username"=> $this->api_username,
                "password"=> $this->api_password
        );
        $data=json_encode($data);
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt_array($curl, array(
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "POST",
            CURLOPT_POSTFIELDS => $data,
            CURLOPT_HTTPHEADER => array(
                "content-type: application/json"
            ),
        ));
        
        $response = curl_exec($curl);
        $err = curl_error($curl);
        curl_close($curl);
        $response = json_decode($response);

        if(empty($response) || empty($response->token)) {
            $this->error = 'No response from authorization api';
            return false;
        }

        return $response->token;
    }

    function createOrder($order = array(), $token_number = array())
    {
        if (empty($order) ) {
            $this->error = 'Invalid Order';
            return false;
        }

        $ord = $order['order'];
        $products = $order['products'];
        $customer = $order['customer'];
        $pickup = $order['pickup'];
        $rto = (!empty($order['rto'])) ? $order['rto'] : $pickup;

        $product_full_name = !empty($products) ? implode(', ', array_column($products, 'name')) : '';
        $weight =!empty($ord['weight']) ? round($ord['weight'], 2) : '500';
        
        $token=self::createUpdateToken();
        if(empty($token)) {
            $this->error = 'Invalid authorization';
            return false;
        }
        $ship_id = $ord['shipment_id'];
        $products_array = array();
        foreach ($products as $product) {
            $products_array[] = array(
                "name" => $product['name'],
                "description" => $product['name'],
                "quantity" =>  (int) $product['qty'],
                "skuCode" => "",
                "itemPrice" => (float)$product['price'],
                "hsnCode" => ""
            );
        }

        $data = [
            "source" => "",
            "returnShipmentFlag" => false,
            "Shipment" => [
                "code" => "SLR000182",
                "orderCode" => $ship_id,
                "saleOrderCode" => "",
                "invoiceCode" => $ship_id,
                "orderDate" => date("d-M-Y H:i:s"),
                "weight" => $weight, // grams
                "length"   => (int)$ord['length'] * 10, // in mm
                "height"   => (int)$ord['breadth'] * 10, // in mm
                "breadth"  => (int)$ord['height'] * 10, // in mm
                "items" => $products_array
            ],
            "deliveryAddressDetails" => [
                "name" =>  $customer['name'],
                "email" => "",
                "phone" => !empty($customer['phone']) ? $customer['phone'] : '',
                "address1" => !empty($customer['address']) ? $customer['address'] : '',
                "address2" => !empty($customer['address_2']) ? $customer['address_2'] : '',
                "pincode" => !empty($customer['zip']) ? $customer['zip'] : '',
                "city" => !empty($customer['city']) ? $customer['city'] : '',
                "state" => !empty($customer['state']) ? $customer['state'] : '',
                "country" => "India",
                "gstin" => ""
            ],
            "pickupAddressDetails" => [
                "name" => $pickup['name'],
                "email" => "",
                "phone" => $pickup['phone'],
                "address1" => !empty($pickup['address_1']) ? $pickup['address_1'] : '',
                "address2" => !empty($pickup['address_2']) ? $pickup['address_2'] : '',
                "pincode" => $pickup['zip'],
                "city" => $pickup['city'],
                "state" => $pickup['state'],
                "country" => "India",
                "gstin" => ""
            ],
            "returnAddressDetails" => [
                "name" => !empty($rto['name']) ? $rto['name'] : '',
                "email" => "",
                "phone" => !empty($rto['phone']) ? $rto['phone'] : '',
                "address1" => !empty($rto['address_1']) ? $rto['address_1'] : '',
                "address2" => !empty($rto['address_2']) ? $rto['address_2'] : '',
                "pincode" => !empty($rto['zip']) ? $rto['zip'] : '',
                "city" => !empty($rto['city']) ? $rto['city'] : '',
                "state" => !empty($rto['state']) ? $rto['state'] : '',
                "country" => "India",
                "gstin" => ""
            ],
            "currencyCode" => "INR",
            "paymentMode" => !empty($ord['payment_method']) ? strtoupper($ord['payment_method']) : 'COD',
            "totalAmount" => round($ord['total'], 2),
            "collectableAmount" => (strtolower($ord['payment_method'])=='cod') ? round($ord['total'], 2) : 0,
        ];
        $data_json = json_encode($data);
        $url = $this->api_url . 'order_allocation/api/allocate_waybill';
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt_array($curl, array(
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "POST",
            CURLOPT_POSTFIELDS => $data_json,
            CURLOPT_HTTPHEADER => array(
                "content-type: application/json",
                "Authorization: Bearer $token",
            ),
        ));
        $response = curl_exec($curl);
        $err = curl_error($curl);
        curl_close($curl);
        $response = json_decode($response);

        do_action('log.create', 'shipment', [
            'action' => 'purpledrone_request_response',
            'ref_id' => $ord['shipment_id'],
            'user_id' => $ord['user_id'],
            'data' => array('request'=>$data,'response'=>$response,'error'=>$err)
        ]);

        if(empty($response)) {
            $this->error = 'No response from api';
            return false;
        }

        if (empty($response->status) || $response->status=='FAILED') {
            $this->error = !empty($response->message) ? $response->message :  'Internal Server Error';
            return false;
        }

        if (empty($response->waybill)) {
            $this->error = 'Unable to generate awb';
            return false;
        }
        $return = array();
        $return[$ord['shipment_id']]['status'] = 'success';
        $return[$ord['shipment_id']]['awb'] = $response->waybill;
        $return[$ord['shipment_id']]['shipment_info_1'] = '';
        $return[$ord['shipment_id']]['shipment_weight'] = $weight;
        return $return;
    }

    function cancelAWB($awb = false, $shipId = false)
    {
        if (empty($awb))
            return false;

        $token=self::createUpdateToken();
        if(empty($token)) {
            $this->error = 'Invalid authorization';
            return false;
        }
    
        $post_data = array(
                        'waybill'=>$awb
                    );

        $json_post_data = json_encode($post_data);

        $curl = curl_init();
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt_array($curl, array(
            CURLOPT_URL => $this->api_url . 'cancel',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "POST",
            CURLOPT_HTTPHEADER => array(
                "Content-Type: application/json",
                "Authorization: Bearer $token",
            ),
            CURLOPT_POSTFIELDS => $json_post_data,
        ));
        $response = curl_exec($curl);
        $err = curl_error($curl);
        curl_close($curl);

        $response = json_decode($response);
        $log = new Logs();
        $log->create('cancel_awb', 'cancel_awb_' .$awb, array('request'=>$post_data,'response'=>$response,'error'=>$err));
        if (empty($response))
            return false;

        return true;
    }

    function trackOrder($awb = false, $token = false)
    {   
        if (empty($awb))
            return false;

        $url = $this->api_url . "api/orders/?query_type=track&query_value=$awb";

        $token=self::createUpdateToken();
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt_array($curl, array(
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "GET",
            CURLOPT_HTTPHEADER => array(
                "Content-Type: application/json",
                "Authorization: Bearer $token",
            ),
           // CURLOPT_POSTFIELDS => $post_data,
        ));
        $response = curl_exec($curl);
        $err = curl_error($curl);
        curl_close($curl);

        $response = json_decode($response);

        if ($response->message!='Success')
            return false;

        if (defined('print_tracking') && print_tracking == 'yes') {
            pr($response);
        }

        $return = array();
        $delivered_time = 0;
        $history = array();
        $status_wise_logs = array();
        $hub_wise_logs = array();
        if (!empty($response->data[0]->events)) {
            foreach ($response->data[0]->events as $scan) {
                if(in_array($scan->code,['120']))
                    continue;
                
                $ship_status = $this->getShipStatus($scan->code);
                $his = array(
                    'event_time' => strtotime($scan->date.$scan->time),
                    'status_code' => $scan->code,
                    'location' => $scan->location,
                    'message' => ($scan->remark),
                    'status' => $scan->status,
                    'ship_status' => $ship_status,
                );
                $history[] = $his;

                if ($ship_status == 'delivered') {
                    $delivered_time = strtotime($scan->date.$scan->time);
                }
                $status_wise_logs[$ship_status][] = $his;

            }
        }

        $return_awb = array(
            'delivered_time' => $delivered_time,
            'weight' => (!empty($response->data[0]->weight)) ? $response->data[0]->weight :  0,
            'expected_delivery_date' => (!empty($response->data[0]->delivered_on)) ? strtotime($response->data[0]->delivered_on) :  '',
            'history' => $history
        );

        foreach ($status_wise_logs as $sw_key => $s_w_l) {
            switch ($sw_key) {
                case 'in transit':
                    $transit_logs = $s_w_l;
                    array_multisort(array_column($transit_logs, 'event_time'), SORT_ASC, $transit_logs);
                    $pickupDateArr=array_column($transit_logs ,'event_time', 'status_code');
                    $return_awb['pickup_time'] = $transit_logs[0]['event_time'];
                    if(!empty($pickupDateArr['pickup_complete']) && array_key_exists('pickup_complete',$pickupDateArr))
                        $return_awb['pickup_time'] = $pickupDateArr['pickup_complete'];
                    break;
                case 'out for delivery':
                    $ofd_logs = $s_w_l;
                    $return_awb['total_ofd_attempts'] = count($ofd_logs);
                    array_multisort(array_column($ofd_logs, 'event_time'), SORT_ASC, $ofd_logs);
                    $i = 0;
                    foreach ($ofd_logs as $of_key => $of_log) {
                        $return_awb['ofd_attempt_' . ($of_key + 1) . '_date'] = $of_log['event_time'];
                        $return_awb['last_attempt_date'] = $of_log['event_time'];
                    }
                    break;
                case 'delivered':
                    $delivered_logs = $s_w_l;
                    array_multisort(array_column($delivered_logs, 'event_time'), SORT_DESC, $delivered_logs);
                    $return_awb['delivered_time'] = $delivered_logs[0]['event_time'];
                    break;
                case 'exception':
                    $exception_logs = $s_w_l;
                    $return_awb['delivery_attempt_count'] = count($exception_logs);
                    array_multisort(array_column($exception_logs, 'event_time'), SORT_DESC, $exception_logs);
                    $return_awb['last_ndr_date'] = $exception_logs[0]['event_time'];
                    $return_awb['last_ndr_reason'] = $exception_logs[0]['message'];
                    break;
                case 'rto in transit':
                    $rto_logs = $s_w_l;
                    array_multisort(array_column($rto_logs, 'event_time'), SORT_ASC, $rto_logs);
                    $return_awb['rto_mark_date'] = $rto_logs[0]['event_time'];
                    break;
                case 'rto delivered':
                    $rto_d_logs = $s_w_l;
                    array_multisort(array_column($rto_d_logs, 'event_time'), SORT_ASC, $rto_d_logs);
                    $return_awb['rto_delivered_date'] = $rto_d_logs[0]['event_time'];
                    break;
            }
        }

        array_multisort(array_column($history, 'event_time'), SORT_DESC, $history);
        $return_awb['shipment_status'] = strtolower($history[0]['ship_status']);

        $return[$awb] = $return_awb;

        if (defined('print_tracking') && print_tracking == 'yes') {
            pr($return);
        }
        return $return;
    }

    function getShipStatus($status_code = false)
    {
        switch ($status_code) {
            case '101':
            case '102':
            case '103':
            case '104':
            case '110':
            case '202':
            case '203':
            case '204':
            case '111':
                $ship_status = 'pending pickup';
                break;
            case '205':
            case '206':
            case '208':
            case '301':
            case '302':
                $ship_status = 'in transit';
                break;
            case '207':
            case '303':
                $ship_status = 'out for delivery';
                break;
            case '304':
            case '400':
            case '411':
            case '412':
            case '413':
            case '414':
            case '499':
            case '501':
            case '502':
            case '503':
            case '888':
                $ship_status = 'exception';
                break;
            case '999':
                $ship_status = 'delivered';
                break;
            case '401':
            case '402':
            case '403':
            case '404':
            case '405':
            case '406':
                $ship_status = 'rto in transit';
                break;
            case '407':
                $ship_status = 'rto delivered';
                break;
            default:
                $ship_status = '';
        }

        return $ship_status;
    }
}

