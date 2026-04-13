<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Fship extends MY_lib
{

    private $api_username;
    private $api_password;
    private $api_url;
    private $customer_code;
    private $api_signature;
    
    public function __construct($config = false)
    {
        parent::__construct();
        //$this->api_url = 'https://capi-qc.fship.in/api/';  //testing
        $this->api_url = 'https://capi.fship.in/api/';
        //$this->api_signature = '085c36066064af83c66b9dbf44d190d40feec79f437bc1c1cb'; //testing
        $this->api_signature = '841f85cb2b863e77326d4186a86e01f7ecc2ab39b45ec406988db275f3854faa';
    }

    function createWarehouse($warehouseData)
    {
        if(!empty($warehouseData['fship_warehouse_id']))
            return $warehouseData['fship_warehouse_id'];

        unset($warehouseData['fship_warehouse_id']);
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt_array($curl, array(
            CURLOPT_URL => $this->api_url . 'addwarehouse',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "POST",
            CURLOPT_POSTFIELDS => json_encode($warehouseData),
            CURLOPT_HTTPHEADER => array(
                "content-type: application/json-patch+json",
                "signature: $this->api_signature"
            ),
        ));
        $response = curl_exec($curl);
        $err = curl_error($curl);
        curl_close($curl);
        $response = json_decode($response);
        if(empty($response) || empty($response->status)) {
            $this->error = !empty($response->response) ? $response->response : 'No response from authorization api';
            return $this->error;
        }
        if (!empty($response->status)) {
            $fship_warehouse_id = $response->warehouseId;
            $this->CI->db->where('id', $warehouseData['warehouseId']);
            $this->CI->db->set('fship_warehouse_id', $fship_warehouse_id);
            $this->CI->db->update('warehouse');
            return $fship_warehouse_id;
        } 
    }
    function createOrder($order = array())
    {
        if (empty($order)) {
            $this->error = 'Invalid Order';
            return false;
        }

        $ord = $order['order'];
        $products = $order['products'];
        $customer = $order['customer'];
        $pickup = $order['pickup'];
        $rto = (!empty($order['rto'])) ? $order['rto'] : $pickup;
        $isSameAsPickup = (!empty($order['rto'])) ? true : false;

        $payment_type = (strtolower($ord['payment_method']) == 'prepaid') ? '0' : '1';
        $cod_amount = (strtolower($ord['payment_method']) == 'prepaid') ? '0' : (int)$ord['total'];

        $weight = round($ord['weight'] / 1000, 2);
        $products_array = array();
        foreach ($products as $product) {
            $products_array[] = array(
                'productDetailId' => $ord['seller_order_id'],
                'productId' => $product['id'],
                'sku' => $product['sku'],
                'productName' => $product['name'],
                'unitPrice' => $product['price'],
                'quantity' => $product['qty'],
                'hsnCode' => '',
                'taxRate' => 0,
                'productDiscount' => 0,
            );
        }

        $warehouse_array= array (
            'warehouseId' => $pickup['warehouse_id'],
            'fship_warehouse_id' => $pickup['fship_warehouse_id'],
            'warehouseName' => $pickup['name'].'1222',
            'contactName' => $pickup['contact_name'],
            'addressLine1' => $pickup['address_1'],
            'addressLine2' => $pickup['address_2'],
            'city' => $pickup['city'],
            'stateId' => 0,
            'countryId' => 0,
            'pincode' => $pickup['zip'],
            'phoneNumber' => $pickup['phone'],
            'email' => $pickup['email']??'test@gmail.com',
            "isSameAsPickup"=> $isSameAsPickup,
            "rtoAddress"=>array (
                'warehouseName' => $rto['name'],
                'contactName' => $rto['contact_name'],
                'addressLine1' => $rto['address_1'],
                'addressLine2' => $rto['address_2'],
                'pincode' => $rto['zip'],
                'city' => $rto['city'],
                'phoneNumber' => $rto['phone'],
                'email' => $rto['email']??'test@gmail.com',
            ),
        );
        $warehouse_id=$this->createWarehouse($warehouse_array);
        if(!empty($this->error))
            return false;

        $json_data=array (
            'courierId' => $order['courier']['id'],
            'orderId' =>$ord['seller_order_id'],
            'customer_Name' => $customer['name'],
            'customer_Address' => $customer['address'],
            'landMark' => ($customer['address_2'])??$customer['address'],
            "customer_Address_Type"=> "string",
            'customer_City' => $customer['city'],
            'customer_PinCode' => $customer['zip'],
            "customer_Emailid"=> "test@gmail.com",
            'customer_Mobile' => $customer['phone'],
            'payment_Mode' => strtolower($payment_type),
            "express_Type"=> "surface",
            "is_Ndd"=> 0,
            "tax_Amount"=> 0,
            "extra_Charges"=> 0,
            'cod_Amount' => $cod_amount,
            'order_Amount' => (int)$ord['total'],
            'total_Amount' => (int)$ord['total'],
            'shipment_Weight' => $weight,
            'shipment_Length' => (int)$ord['length'],
            'shipment_Width' => (int)$ord['breadth'],
            'shipment_Height' => (int)$ord['height'],
            "volumetric_Weight"=> 0,
            "latitude"=> 0,
            "longitude"=> 0,
            "pick_Address_ID"=> $warehouse_id,
            "return_Address_ID"=> 0,
            'products' => $products_array
        );
        $json_data=json_encode($json_data);

        $url = $this->api_url . 'createforwardorder';

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
            CURLOPT_POSTFIELDS => $json_data,
            CURLOPT_HTTPHEADER => array(
                "content-type: application/json-patch+json",
                "signature: $this->api_signature"
        )));
        $response = curl_exec($curl);
        //pr($response,1);
        $err = curl_error($curl);
        curl_close($curl);

        do_action('log.create', 'shipment', [
            'action' => 'fship_request_response',
            'ref_id' => $ord['shipment_id'],
            'user_id' => $ord['user_id'],
            'data' => array('request'=>json_decode($json_data),'response'=>$response,'error'=>$err)
        ]);
        $response=json_decode($response);
        if (empty($response)) {
            $this->error = 'Internal Server Error';
            return false;
        }

        $return = array();

        if (!empty($response->status) && !empty($response->waybill)) {
            $return[$ord['shipment_id']]['status'] = 'success';
            $return[$ord['shipment_id']]['awb'] = $response->waybill;
            $return[$ord['shipment_id']]['shipment_info_1'] = json_encode(array('apiorderid'=>$response->apiorderid,'route_code'=>(!empty($response->route_code) ? $response->route_code : '')));
        } else { 
            $response = array_values((array) $response);
            $return[$ord['shipment_id']]['status'] = 'error';
            $message = !empty($response['5']) ? $response['5'] :'';
            if (strpos($message, "insufficient balance in your wallet") !== false) {
                $message = 'Something went wrong. Kindly contact the administrator.';
            } 
            $return[$ord['shipment_id']]['awb'] = $message;
        }
        return $return;
    }

    function pickup($shipment_ids = false, $token = false, $pickup_date = false, $pickup_time = false)
    {
        if (empty($shipment_ids))
            return false;

        $this->CI->load->library('shipping_lib');
        $shipmentDta = $this->CI->shipping_lib->getByIDBulk($shipment_ids);
        if (empty($shipmentDta))
            return false;

        $waybillNos=array_column($shipmentDta,'awb_number');
        $waybills=json_encode($waybillNos);
        $post_data = '{"waybills": '.$waybills.'}';
        $url = $this->api_url . 'registerpickup';
        $rand = date('H_i_s');
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
            CURLOPT_HTTPHEADER => array(
                "content-type: application/json-patch+json",
                "signature: $this->api_signature"
            ),
            CURLOPT_POSTFIELDS => $post_data,
        ));
        $response = curl_exec($curl);

        $err = curl_error($curl);
        curl_close($curl);

        $response = json_decode($response);
        if (!empty($response->apipickuporderids[0]->pickupOrderId))
            return $response->apipickuporderids[0]->pickupOrderId;

        return false;
    }

    function cancelAWB($awb = false)
    {
        if (!$awb)
            return false;

        $post_data  = array(
            'reason' => 'Cancel by seller',
            'waybill' => $awb,
        );

        $curl = curl_init();
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt_array($curl, array(
            CURLOPT_URL => $this->api_url . 'cancelorder',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "POST",
            CURLOPT_HTTPHEADER => array(
                "content-type: application/json-patch+json",
                "signature: $this->api_signature"
            ),
            CURLOPT_POSTFIELDS => json_encode($post_data),
        ));
        $response = curl_exec($curl);
        $err = curl_error($curl);
        curl_close($curl);

        $response = json_decode($response);

        if (empty($response))
            return false;

        return true;
    }


    function trackOrder($awb = false)
    {
        if (!$awb)
            return false;

        $data = array(
            'waybill' => $awb
        );

        $curl = curl_init();
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt_array($curl, array(
            CURLOPT_URL => $this->api_url . 'trackinghistory',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "POST",
            CURLOPT_HTTPHEADER => array(
                "content-type: application/json-patch+json",
                "signature: $this->api_signature"
            ),
            CURLOPT_POSTFIELDS => json_encode($data),
        ));

        $response = curl_exec($curl);
        $err = curl_error($curl);
        curl_close($curl);

        $response = json_decode($response);
        pr($response);
        $return = array();
        if (empty($response->trackingdata))
            return false;

        $shipment = $response->trackingdata;
        $summary[]= $response->summary;

        if (empty($shipment) || empty($summary))
            return false;

        $history = array();
        $retn = array();
        $status_wise_logs = array();
        if (!empty($summary)) {
            foreach ($summary as $key=>$scan) {
                $ship_status = $this->getShipStatus($scan->statusid);
                $his = array(
                    'event_time' => strtotime($scan->lastscandate),
                    'status_code' => $shipment[$key]->status,
                    'location' => $shipment[$key]->location,
                    'message' => $shipment[$key]->remark,
                    'status' => $shipment[$key]->status,
                    'ship_status' => $ship_status
                );
                $status_wise_logs[$ship_status][] = $his;
                $history[] = $his;
            }
            $this->CI->load->library('tracking_lib');
            $tracking_history = $this->CI->tracking_lib->getByAWB($awb);
            if (!empty($tracking_history)) {
                $tracking_history=array_merge($history,$tracking_history);
                foreach ($tracking_history as $th) {
                    $th=(object)$th;
                    $status_wise_logs[strtolower($th->ship_status)][] = (array)$th;
                }
            }
            foreach ($status_wise_logs as $sw_key => $s_w_l) {
                switch ($sw_key) {
                    case 'in transit':
                        $transit_logs = $s_w_l;
                        array_multisort(array_column($transit_logs, 'event_time'), SORT_ASC, $transit_logs);
                        $retn['pickup_time'] = $transit_logs[0]['event_time'];
                        break;
                    case 'out for delivery':
                        $ofd_logs = $s_w_l;
                        $retn['total_ofd_attempts'] = count($ofd_logs);
                        array_multisort(array_column($ofd_logs, 'event_time'), SORT_ASC, $ofd_logs);
                        $i = 0;
                        foreach ($ofd_logs as $of_key => $of_log) {
                            $retn['ofd_attempt_' . ($of_key + 1) . '_date'] = $of_log['event_time'];
                        }
                        break;
                    case 'rto in transit':
                        $rto_logs = $s_w_l;
                        array_multisort(array_column($rto_logs, 'event_time'), SORT_ASC, $rto_logs);
                        $retn['rto_mark_date'] = $rto_logs[0]['event_time'];
                        break;
                    case 'delivered':
                        $delivered_logs = $s_w_l;
                        array_multisort(array_column($delivered_logs, 'event_time'), SORT_DESC, $delivered_logs);
                        $retn['delivered_time'] = $delivered_logs[0]['event_time'];
                        break;
                    case 'exception':
                        $exception_logs = $s_w_l;
                        array_multisort(array_column($exception_logs, 'event_time'), SORT_DESC, $exception_logs);
                        $retn['last_ndr_date'] = $exception_logs[0]['event_time'];
                        $retn['last_ndr_reason'] = $exception_logs[0]['message'];
                        break;
                    case 'rto delivered':
                        $rto_delivered_logs = $s_w_l;
                        array_multisort(array_column($rto_delivered_logs, 'event_time'), SORT_DESC, $rto_delivered_logs);
                        $retn['rto_delivered_date'] = $rto_delivered_logs[0]['event_time'];
                        break;
                }
            }
            //calculate out for delivery attempts
            $retn['history'] = $history;
        }
        $return[$awb] = $retn;
        return $return;
    }

    function getShipStatus($status = false)
    {
        $status = strtolower($status);

        switch ($status) {
            case '4':
            case '5':
            case '6':
            case '7':
            case '22':
                $ship_status = 'pending pickup';
                break;
            case '8':
            case '9':
                $ship_status = 'in transit';
                break;
            case '11':
                $ship_status = 'out for delivery';
                break;
            case '12':
                $ship_status = 'delivered';
                break;
            case '10':               
                $ship_status = 'exception';
                break;
            case '13':
            case '14':
                $ship_status = 'rto in transit';
                break;
            case '15':
                $ship_status = 'rto delivered';
                break;
            default:
                $ship_status = '';
        }

        return $ship_status;
    }

    function pushNDRAction($ndr_data = false)
    {
        if (empty($ndr_data['awb_number']) || empty($ndr_data['action']) || empty($ndr_data['api_order_id']))
            return false;

        $awb_number = $ndr_data['awb_number'];

        switch ($ndr_data['action']) {
            case 're-attempt':
                $post_data =  array(
                    'apiorderid' => $ndr_data['api_order_id'],
                    'action' => 're-attempt',
                    'reattempt_date' => (!empty($ndr_data['re_attempt_date'])) ? date('Y-m-d', $ndr_data['re_attempt_date']) : date('Y-m-d', strtotime('+1 day')),
                    'contact_name' => '',
                    'complete_address' => '',
                    'landmark' => '',
                    'mobilenumber' => '',
                    'remarks' => $ndr_data['remarks'] ?? '',
                );
                break;
            case 'change address':
                $post_data =  array(
                    'apiorderid' => $ndr_data['api_order_id'],
                    'action' => 'change address',
                    'reattempt_date' => (!empty($ndr_data['re_attempt_date'])) ? date('Y-m-d', $ndr_data['re_attempt_date']) : date('Y-m-d', strtotime('+1 day')),
                    'contact_name' => $ndr_data['change_name'],
                    'complete_address' => (!empty($ndr_data['change_address_1'])) ? $ndr_data['change_address_1'] : '',
                    'landmark' => '',
                    'mobilenumber' => (!empty($ndr_data['change_phone'])) ? $ndr_data['change_phone'] : '',
                    'remarks' => $ndr_data['remarks'] ?? '',
                );
                break;
            case 'change phone':
                $post_data =  array(
                    'apiorderid' => $ndr_data['api_order_id'],
                    'action' => 'change phone',
                    'reattempt_date' => '',
                    'contact_name' => '',
                    'complete_address' => '',
                    'landmark' => '',
                    'mobilenumber' => (!empty($ndr_data['change_phone'])) ? $ndr_data['change_phone'] : '',
                    'remarks' => $ndr_data['remarks'] ?? '',
                );
            break;

            default:
                return false;
        }

        $post_data['waybill'] = $awb_number;

        // $post_data = array(
        //     'data' => array($post_data)
        // );

        $json_data = json_encode($post_data);

        $url = $this->api_url . 'reattemptorder';

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
            CURLOPT_POSTFIELDS => $json_data,
            CURLOPT_HTTPHEADER => array(
                "content-type: application/json-patch+json",
                "signature: $this->api_signature"
        )));
        $response = curl_exec($curl);
        $err = curl_error($curl);
        curl_close($curl);

        $response = json_decode($response);

        if (empty($response)) {
            $this->error = 'No response from server';
            return false;
        }

        if (empty($response->response)) {
            $this->error = 'Error in API request';
            return false;
        }

        $return = array(
            'message' => $response->response,
        );

        return $return;
    }
}
