<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Pickndel extends MY_lib
{
    private $api_url;
    private $api_username;
    private $api_password;
    public function __construct($config = false)
    {
        parent::__construct();
        $this->api_username = 'daakit'; //live
        $this->api_password = 'Pikndel@123'; //live
        $this->api_url = 'https://api.pikndel.com/backoffice/api/'; //live
    }

    function createUpdateToken($expired = false)
    {
        $created_time = time();

        $url = $this->api_url . 'account/login';

        $data = array(
            'Control'=>array(
                'RequestId'=>time(),
                'Source'=>'3',
                'RequestTime'=>time(),
                'Version'=>'3.2'
            ),
            'Data'=>array(
                "Username"=> $this->api_username,
                "Password"=> $this->api_password,
                "GrantType"=> "password"
            )
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

        if(empty($response) || empty($response->Data->Token)) {
            $this->error = 'No response from authorization api';
            return false;
        }

        return $response->Data->Token."@_@".$response->Data->UserId;
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
        $weight =!empty($ord['weight']) ? round($ord['weight'] / 1000, 2) : '0.5';
        
        $tokenDetails=self::createUpdateToken();
        list($token,$userId)=explode("@_@",$tokenDetails);
        if(empty($token)) {
            $this->error = 'Invalid authorization';
            return false;
        }
        $ship_id = $ord['shipment_id'];
        $products_array = array();
        foreach ($products as $product) {
            $products_array[] = array(
                "Qty"=> $product['qty'],
                "Type"=> "Goods",
                "IsFragile"=> "0",
                "IsLiquid"=> "0",
                "Name"=> $product['name'],
                "Cost"=> $product['price'],
                "Length"=> (int)$ord['length'],
                "Width"=> (int)$ord['breadth'],
                "Height"=> (int)$ord['height'],
                "ActualWeight"=> '0.100',
                "EWayBillNo"=> ""
            );
        }
        $data = array(
            'Control'=>array(
                'RequestId'=>time(),
                'Source'=>'3',
                'RequestTime'=>time(),
                'Version'=>'3.2'
            ),
            'Data'=>array(
                'UserId'=>$userId,
                'OrderDetails'=>array(
                    array(
                        "PreAWBNo"=> "",
                        "ClientUniqueNo"=> $ship_id,
                        "VehicleType"=> "Bike",
                        "BrandName"=> !empty($pickup['seller_company']) ? $pickup['seller_company'] : '',
                        "OrderType"=> "B2C",
                        "InvoiceNo"=> $ord['id'],
                        "InvoiceUrl"=> "",
                        "InvoiceValue"=> round($ord['total'], 2),
                        "EWAYBillNo"=> "",
                        "TotalActualWeight"=> $weight,
                        "Info"=>array(
                            array(
                                "Pickup"=>array(
                                    "UniqueNo"=> "",
                                    "PersonName"=> $pickup['name'],
                                    "Mobile"=> $pickup['phone'],
                                    "AddressType"=> "Home",
                                    "HouseNo"=> "",
                                    "Landmark"=> "",
                                    "Address"=> $pickup['address_1'],
                                    "Lat"=> "",
                                    "Lng"=> "",
                                    "Pincode"=> $pickup['zip'],
                                    "CashPaid"=> "0",
                                    "CashCollection"=> "0",
                                    "Comment"=> "",
                                    "PickupDate"=> "",
                                    "PickupSlot"=> "",
                                    "RTOAddr"=> ""
                                ),
                                "Item"=>$products_array,
                                "Delivery"=>array(
                                    "UniqueNo"=> "",
                                    "PersonName"=> $customer['name'],
                                    "Mobile"=> $customer['phone'],
                                    "AddressType"=> "Office",
                                    "HouseNo"=> "",
                                    "Landmark"=> "",
                                    "Address"=> $customer['address']." ".$customer['address']." ".$customer['city'] ." ".$customer['state'],
                                    "Lat"=> "",
                                    "Lng"=> "",
                                    "Pincode"=> $customer['zip'],
                                    "CashCollection"=> (strtolower($ord['payment_method'])=='cod') ? round($ord['total'], 2) : 0,
                                    "Comment"=> ""
                                )
                            )
                        )
                    )
                )
            )
        );

        $data_json = json_encode($data);
        
        $url = $this->api_url . 'pikndel/place_order';

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
                "Authorization: Bearer" . $token,
            ),
        ));
        $response = curl_exec($curl);
        $err = curl_error($curl);
        curl_close($curl);
        $response = json_decode($response);

        do_action('log.create', 'shipment', [
            'action' => 'pickndel_request_response',
            'ref_id' => $ord['shipment_id'],
            'user_id' => $ord['user_id'],
            'data' => array('request'=>$data,'response'=>$response,'error'=>$err)
        ]);

        if (empty($response->Control->Status)) {
            $this->error = !empty($response->Control->Message) ? $response->Control->Message :  'Internal Server Error';
            return false;
        }
        if(empty($response) || empty($response = $response->Data[0]->Details)) {
            $this->error = 'No response from api';
            return false;
        }
        if (empty($response[0]->AWBNo)) {
            $this->error = 'Unable to generate awb';
            return false;
        }
        $return = array();
        $return[$ord['shipment_id']]['status'] = 'success';
        $return[$ord['shipment_id']]['awb'] = $response[0]->AWBNo;
        $return[$ord['shipment_id']]['shipment_info_1'] = '';
        $return[$ord['shipment_id']]['shipment_weight'] = $weight * 1000;
        return $return;
    }

    function cancelAWB($awb = false, $shipId = false)
    {
        if (empty($awb))
            return false;

        $tokenDetails=self::createUpdateToken();
        list($token,$userId)=explode("@_@",$tokenDetails);
        if(empty($token)) {
            $this->error = 'Invalid authorization';
            return false;
        }
    
        $post_data = array(
            'Control'=>array(
                'RequestId'=>time(),
                'Source'=>'3',
                'RequestTime'=>time(),
                'Version'=>'3.2'
            ),
            'Data'=>array(
                array(
                    "UserId"=> $userId,
                    "Orders"=>array(
                        'ClientUniqueNo'=>"DKT/$shipId",
                        'AWBNo'=>array($awb)
                    )
                )
            )
        );

        $json_post_data = json_encode($post_data);

        $curl = curl_init();
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt_array($curl, array(
            CURLOPT_URL => $this->api_url . 'pikndel/order/cancel',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "POST",
            CURLOPT_HTTPHEADER => array(
                "Content-Type: application/json",
                "Authorization: Bearer" . $token,
            ),
            CURLOPT_POSTFIELDS => $json_post_data,
        ));
        $response = curl_exec($curl);
        $err = curl_error($curl);
        curl_close($curl);

        $response = json_decode($response);
        $log = new Logs();
        $log->create('cancel_awb_' .$awb, 'Request Data', array('request'=>$post_data,'response'=>$response,'error'=>$err));
        if (empty($response))
            return false;

        return true;
    }

    function trackOrder($awb = false, $token = false)
    {   
        if (empty($awb))
            return false;

        $url = $this->api_url . 'pikndel/order/get_status';

        $post_data = array(
            'Control'=>array(
                'RequestId'=>time(),
                'Source'=>'3',
                'RequestTime'=>time(),
                'Version'=>'3.2'
            ),
            'Data'=>array(
                "AWBNo"=> $awb,
            )
        );
        $post_data = json_encode($post_data);
        $tokenDetails=self::createUpdateToken();
        list($token,$userId)=explode("@_@",$tokenDetails);
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
                "Content-Type: application/json",
                "Authorization: Bearer" . $token
            ),
            CURLOPT_POSTFIELDS => $post_data,
        ));
        $response = curl_exec($curl);
        $err = curl_error($curl);
        curl_close($curl);

        $response = json_decode($response);

        if (empty($response->Control->Status))
            return false;

        if (defined('print_tracking') && print_tracking == 'yes') {
            pr($response);
        }

        $return = array();
        $delivered_time = 0;
        $history = array();
        $status_wise_logs = array();
        $hub_wise_logs = array();
        if (!empty($response->Data)) {
            foreach ($response->Data as $scan) {
                $ship_status = $this->getShipStatus($scan->short_code);
                $his = array(
                    'event_time' => strtotime($scan->reported_on),
                    'status_code' => $scan->short_code,
                    'location' => $scan->google_location,
                    'message' => trim($scan->activity),
                    'status' => $scan->short_code,
                    'ship_status' => $ship_status,
                );
                $history[] = $his;

                if ($ship_status == 'delivered') {
                    $delivered_time = strtotime($scan->event_date);
                }
                $status_wise_logs[$ship_status][] = $his;

            }
        }

        $return_awb = array(
            'delivered_time' => $delivered_time,
            'weight' => (!empty($shipment->weight)) ? $shipment->weight * 1000 :  0,
            'expected_delivery_date' => (!empty($scan->expected_delivery_date)) ? strtotime($scan->expected_delivery_date) :  '',
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

        $return[$scan->pod_no] = $return_awb;

        if (defined('print_tracking') && print_tracking == 'yes') {
            pr($return);
        }
        return $return;
    }

    function getShipStatus($status_code = false)
    {
        switch ($status_code) {
            case 'NEW':
            case 'RAP':
            case 'ARP':
            case 'OFP':
            case 'ARV':
            case 'PCN':
                $ship_status = 'pending pickup';
                break;
            case 'DTH':
            case 'RAH':
            case 'ITR':
            case 'RAD':
            case 'ARD':
            case 'PCK':
                $ship_status = 'in transit';
                break;
            case 'OFD':
                $ship_status = 'out for delivery';
                break;
            case 'PEN':
            case 'ANT':
            case 'CLJ':
            case 'CAN':
            case 'IWA':
            case 'LOC':
            case 'NSP':
            case 'PNM':
            case 'RTA':
            case 'SHI':
            case 'POS':
            case 'CROC':
            case 'TAFC':
            case 'LSV':
            case 'CTZ':
            case 'COV':
            case 'CLD':
            case 'GDL':
            case 'WFH':
            case 'CNR':
            case 'OSA':
            case 'CANT':
            case 'CER':
            case 'CIDR':
            case 'CIWA':
            case 'CLOC':
            case 'CNSP':
            case 'CNSA':
            case 'CPNM':
            case 'CSHI':
            case 'CPOS':
                $ship_status = 'exception';
                break;
            case 'DLD':
                $ship_status = 'delivered';
                break;
            case 'RTU':
                $ship_status = 'rto in transit';
                break;
            case 'RTO':
                $ship_status = 'rto delivered';
                break;
            default:
                $ship_status = '';
        }

        return $ship_status;
    }
}

