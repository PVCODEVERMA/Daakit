<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Xpressbees extends MY_lib
{
    private $api_key;
    private $mode;

    private $username;
    private $password;
    private $secretkey;
    private $authorization;
    private $business_account_name;
    private $versionnumber;
    private $courier_id;
    private $debug;
    private $weight;

    public function __construct($config = false)
    {
        parent::__construct();

        $this->authorization = 'Bearer xyz';
        $this->versionnumber = 'v1';
        $this->debug = false;
        $this->weight = '0.5';
        if (!empty($config['mode']))
            switch (strtolower($config['mode'])) {
                case 'xpressbees_surface':
                    $this->api_key = $this->CI->config->item('xpressbees_api_key_surface');
                    $this->username = $this->CI->config->item('xpressbees_username_surface');
                    $this->password = $this->CI->config->item('xpressbees_password_surface');
                    $this->secretkey = $this->CI->config->item('xpressbees_secretkey_surface');
                    $this->business_account_name = $this->CI->config->item('xpressbees_business_account_name_surface');
                    $this->mode = 'surface';
                    $this->courier_id = '28';
                break;

                case 'xpressbees_surface_1kg':
                    $this->api_key = $this->CI->config->item('xpressbees_api_key_1_kg');
                    $this->username = $this->CI->config->item('xpressbees_username_1_kg');
                    $this->password = $this->CI->config->item('xpressbees_password_1_kg');
                    $this->secretkey = $this->CI->config->item('xpressbees_secretkey_1_kg');
                    $this->business_account_name = $this->CI->config->item('xpressbees_business_account_name_1_kg');
                    $this->mode = '1_kg';
                    $this->courier_id = '28';
                    $this->weight = '1.0';
                break;

                case 'xpressbees_surface_2kg':
                    $this->api_key = $this->CI->config->item('xpressbees_api_key_2_kg');
                    $this->username = $this->CI->config->item('xpressbees_username_2_kg');
                    $this->password = $this->CI->config->item('xpressbees_password_2_kg');
                    $this->secretkey = $this->CI->config->item('xpressbees_secretkey_2_kg');
                    $this->business_account_name = $this->CI->config->item('xpressbees_business_account_name_2_kg');
                    $this->mode = '2_kg';
                    $this->courier_id = '29';
                    $this->weight = '2.0';
                break;

                case 'xpressbees_surface_5kg':
                    $this->api_key = $this->CI->config->item('xpressbees_api_key_5_kg');
                    $this->username = $this->CI->config->item('xpressbees_username_5_kg');
                    $this->password = $this->CI->config->item('xpressbees_password_5_kg');
                    $this->secretkey = $this->CI->config->item('xpressbees_secretkey_5_kg');
                    $this->business_account_name = $this->CI->config->item('xpressbees_business_account_name_5_kg');
                    $this->mode = '5_kg';
                    $this->courier_id = '30';
                    $this->weight = '5.0';
                break;

                case 'xpressbees_surface_10kg':
                    $this->api_key = $this->CI->config->item('xpressbees_api_key_10_kg');
                    $this->username = $this->CI->config->item('xpressbees_username_10_kg');
                    $this->password = $this->CI->config->item('xpressbees_password_10_kg');
                    $this->secretkey = $this->CI->config->item('xpressbees_secretkey_10_kg');
                    $this->business_account_name = $this->CI->config->item('xpressbees_business_account_name_10_kg');
                    $this->mode = '10_kg';
                    $this->courier_id = '31';
                    $this->weight = '10.0';
                break;

                case 'xpressbees_surface_20kg':
                    $this->api_key = $this->CI->config->item('xpressbees_api_key_20_kg');
                    $this->username = $this->CI->config->item('xpressbees_username_20_kg');
                    $this->password = $this->CI->config->item('xpressbees_password_20_kg');
                    $this->secretkey = $this->CI->config->item('xpressbees_secretkey_20_kg');
                    $this->business_account_name = $this->CI->config->item('xpressbees_business_account_name_20_kg');
                    $this->mode = '20_kg';
                    $this->courier_id = '32';
                    $this->weight = '20.0';
                break;
                default:
                    $this->api_key = $this->CI->config->item('xpressbees_api_key_surface');
                    $this->username = $this->CI->config->item('xpressbees_username_surface');
                    $this->password = $this->CI->config->item('xpressbees_password_surface');
                    $this->secretkey = $this->CI->config->item('xpressbees_secretkey_surface');
                    $this->business_account_name = $this->CI->config->item('xpressbees_business_account_name_surface');
                    $this->mode = 'surface';
                    $this->courier_id = '28';
        }
    }

    function createUpdateToken($expired = false)
    {
        $created_time = time();

        if(!$expired) {
            $this->CI->db->where('courier_id', $this->courier_id);
            $this->CI->db->where('expired >=', $created_time);
            $q = $this->CI->db->get('courier_api_token');
            $api_token = $q->row();

            if (!empty($api_token->token)) {
                return $api_token->token;
            }
        }

        if ($this->debug)
            $url = "http://stageusermanagementapi.xbees.in/api/auth/generateToken"; // Demo
        else
            $url = "http://userauthapis.xbees.in/api/auth/generateToken"; // Live

        $data = array(
            'username' => $this->username,
            'password' => $this->password,
            'secretkey' => $this->secretkey
        );

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
            CURLOPT_POSTFIELDS => json_encode($data),
            CURLOPT_HTTPHEADER => array(
                "Content-Type: application/json",
                "Authorization: $this->authorization"
            )
        ));

        $response = curl_exec($curl);
        $err = curl_error($curl);
        curl_close($curl);

        $response = !empty($response) ? json_decode($response) : '';

        if (empty($response) || empty($response->token)) {
            $this->error = 'No response from authorization api';
            return false;
        }

        $this->CI->db->where('courier_id', $this->courier_id);
        $q = $this->CI->db->get('courier_api_token');
        $api_token = $q->row();

        $save = array();
        $save['token'] = $response->token;
        $save['expired'] = $created_time + 1800;

        if (empty($api_token)) {
            $save['created'] = $created_time;
            $save['courier_id'] = $this->courier_id;
            $this->CI->db->insert('courier_api_token', $save);
        } else {
            $this->CI->db->where('id', $api_token->id);
            $this->CI->db->set($save);
            $this->CI->db->update('courier_api_token');
        }

        return $response->token;
    }

    function createOrder($order = array())
    {
        if (empty($order)) {
            $this->error = 'Invalid Order';
            return false;
        }

        /* Code for Reverse Shipment*/
        if ((strtolower($order['order']['payment_method']) == 'reverse')) {
            return $this->createReverseOrder($order);
        }
        /* End: Code for Reverse Shipment*/

        $token = $this->createUpdateToken();
        if (empty($token)) {
            $this->error = ($this->get_error()) ? $this->get_error() : 'No response from token api';
            return false;
        }

        $ord = $order['order'];
        $products = $order['products'];
        $customer = $order['customer'];
        $pickup = $order['pickup'];
        $rto = (!empty($order['rto'])) ? $order['rto'] : $pickup;

        //get AWB no from DB

        $payment_type = (strtolower($ord['payment_method']) == 'prepaid') ? 'prepaid' : 'COD';

        $this->CI->db->where('courier_id', $this->courier_id);
        $this->CI->db->where('used', '0');
        $this->CI->db->where('mode', 'forward');
        $this->CI->db->where('awb_type', $payment_type);
        $this->CI->db->order_by('rand()');
        $this->CI->db->limit(1);
        $q = $this->CI->db->get('awb_list');
        $awb_row = $q->row();
        if (empty($awb_row)) {
            $this->error = 'AWB list empty';
            return false;
        }

        $awb = $awb_row->awb_number;

        //$weight = $this->weight;

        $weight = round($ord['weight'] / 1000, 2);

        $length = !empty($ord['length']) ? (int) $ord['length'] : '10';
        $breadth = !empty($ord['breadth']) ? (int) $ord['breadth'] : '10';
        $height = !empty($ord['height']) ? (int) $ord['height'] : '10';

        $data = array(
            'AirWayBillNO' => $awb,
            'BusinessAccountName' => $this->business_account_name,
            'OrderNo' => $ord['shipment_id'],
            'SubOrderNo' => $ord['shipment_id'],
            'OrderType' => (strtolower($ord['payment_method']) == 'prepaid') ? 'Prepaid' : 'COD',
            'CollectibleAmount' => (strtolower($ord['payment_method']) == 'cod') ? $ord['total'] : 0,
            'DeclaredValue' => $ord['total'],
            'PickupType' => 'Vendor',
            'Quantity' => !empty($products) ? array_sum(array_column($products, 'qty')) : 1,
            'ServiceType' => 'SD',
            'DropDetails' => array(
                'Addresses' => array(
                    array(
                        'Address' => str_replace(',', ' ', (!empty($customer['address']) ? $customer['address'] : '') . ',' . (!empty($customer['address_2']) ? $customer['address_2'] : '')),
                        'City' => !empty($customer['city']) ? mb_strimwidth($customer['city'], 0, 49) : '',
                        'EmailID' => '',
                        'Name' => !empty($customer['name']) ? $customer['name'] : '',
                        'PinCode' => !empty($customer['zip']) ? $customer['zip'] : '',
                        'State' => !empty($customer['state']) ? $customer['state'] : '',
                        'Type' => 'Primary'
                    )
                ),
                'ContactDetails' => array(
                    array(
                        'PhoneNo' => !empty($customer['phone']) ? $customer['phone'] : '',
                        'Type' => 'Primary',
                        'VirtualNumber' => ''
                    )
                ),
                'IsGenSecurityCode' => '',
                'SecurityCode' => '',
                'IsGeoFencingEnabled' => '',
                'Latitude' => '',
                'Longitude' => '',
                'MaxThresholdRadius' => '',
                'MidPoint' => '',
                'MinThresholdRadius' => '',
                'RediusLocation' => ''
            ),
            'PickupDetails' => array(
                'Addresses' => array(
                    array(
                        'Address' => str_replace(',', ' ', $pickup['address_1'] . ',' . $pickup['address_2']),
                        'City' => mb_strimwidth($pickup['city'], 0, 49),
                        'EmailID' => '',
                        'Name' => $pickup['name'],
                        'PinCode' => $pickup['zip'],
                        'State' => $pickup['state'],
                        'Type' => 'Primary'
                    )
                ),
                'ContactDetails' => array(
                    array(
                        'PhoneNo' => $pickup['phone'],
                        'Type' => 'Primary'
                    )
                ),
                'PickupVendorCode' => $pickup['warehouse_id'],
                'IsGenSecurityCode' => '',
                'SecurityCode' => '',
                'IsGeoFencingEnabled' => '',
                'Latitude' => '',
                'Longitude' => '',
                'MaxThresholdRadius' => '',
                'MidPoint' => '',
                'MinThresholdRadius' => '',
                'RediusLocation' => ''
            ),
            'RTODetails' => array(
                'Addresses' => array(
                    array(
                        'Address' => str_replace(',', ' ', $rto['address_1'] . ',' . $rto['address_2']),
                        'City' => mb_strimwidth($rto['city'], 0, 49),
                        'EmailID' => '',
                        'Name' => $rto['contact_name'],
                        'PinCode' => $rto['zip'],
                        'State' => $rto['state'],
                        'Type' => 'Primary'
                    )
                ),
                'ContactDetails' => array(
                    array(
                        'PhoneNo' => $rto['phone'],
                        'Type' => 'Primary'
                    )
                )
            ),
            'Instruction' => '',
            'CustomerPromiseDate' => '',
            'IsCommercialProperty' => '',
            'IsDGShipmentType' => ($ord['dg_order']) ? true : false,
            'IsOpenDelivery' => '',
            'IsSameDayDelivery' => '',
            'ManifestID' => $ord['shipment_id'],
            'MultiShipmentGroupID' => '',
            'SenderName' => '',
            'IsEssential' => ($ord['essential_order']) ? true : false,
            'IsSecondaryPacking' => false,
            'PackageDetails' => array(
                'Dimensions' => array(
                    'Height' => $height,
                    'Length' => $length,
                    'Width' => $breadth,
                    'Weight' => array(
                        'BillableWeight' => $weight,
                        'PhyWeight' => $weight,
                        'VolWeight' => $weight
                    )
                )
            )
        );
        $data = json_encode($data);
        if ($this->debug)
            $url = "http://api.staging.shipmentmanifestation.xbees.in/shipmentmanifestation/forward"; // Demo
        else
            $url ="https://apishipmentmanifestation.xbees.in/shipmentmanifestation/forward"; // New with HTTPS Live

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
                "Content-Type: application/json",
                "token: $token",
                "versionnumber: $this->versionnumber"
            ),
        ));
        $response = curl_exec($curl);
        $err = curl_error($curl);
        curl_close($curl);

        $response = json_decode($response);
        
        do_action('log.create', 'shipment', [
            'action' => 'xpressbees_request_response',
            'ref_id' => $ord['shipment_id'],
            'user_id' => $ord['user_id'],
            'data' => array('request'=>$data,'response'=>$response,'error'=>$err)
        ]);

        $this->CI->db->where('id', $awb_row->id);
        $this->CI->db->where('mode', 'forward');
        $this->CI->db->set('used', '1');
        $this->CI->db->update('awb_list');

        if (empty($response->TokenNumber)) {
            $this->error = (!empty($response->ReturnMessage)) ? $response->ReturnMessage : 'Internal Error';
            return false;
        }

        $return = array();
        $return[$ord['shipment_id']]['status'] = 'error';
        $return[$ord['shipment_id']]['awb'] = $response->ReturnMessage;
        if ((strtolower($response->ReturnMessage) == 'successful') || (strtolower($response->ReturnMessage) == 'successfull')) {
            $return[$ord['shipment_id']]['status'] = 'success';
            $return[$ord['shipment_id']]['awb'] = $response->AWBNo;
            $return[$ord['shipment_id']]['shipment_weight'] = $weight * 1000;
        }

        return $return;
    }

    function cancelAWB($awb = false)
    {
        if (!$awb)
            return false;

        $data = array(
            'XBkey' => $this->api_key,
            'AWBNumber' => $awb,
            'RTOReason' => 'Seller Cancelled'
        );

        $data = json_encode($data);

        if ($this->debug)
            $url = "http://114.143.206.69:803/StandardForwardStagingService.svc/RTONotifyShipment"; // Demo
        else
            $url = "http://xbclientapi.xbees.in/POSTShipmentService.svc/RTONotifyShipment"; // Live

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
                "content-type: application/json",
            ),
        ));
        $response = curl_exec($curl);
        $err = curl_error($curl);
        curl_close($curl);

        $response = json_decode($response);
        $log = new Logs();
        $log->create('cancel_awb_' .$awb, 'Request Data', array('request'=>json_decode($data),'response'=>$response,'error'=>$err));
        if (empty($response))
            return false;

        return true;
    }

    function trackOrder($awb = false)
    {
        if (!$awb)
            return false;

        if (in_array($this->mode, ['reverse', 'reverse_qc'])) {
            return   $this->trackOrder_reverse($awb);
        }

        $data = array(
            'XBkey' => $this->api_key,
            'AWBNo' => $awb,
        );

        $data = json_encode($data);

        if ($this->debug)
            $url = 'http://114.143.206.69:803/StandardForwardStagingService.svc/GetShipmentSummaryDetails'; // Demo
        else
            $url = 'http://xbclientapi.xbees.in/TrackingService.svc/GetShipmentSummaryDetails'; // Live

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
                "content-type: application/json",
            ),
        ));

        $response = curl_exec($curl);
        $err = curl_error($curl);
        curl_close($curl);

        $response = json_decode($response);

        if (defined('print_tracking') && print_tracking == 'yes') {
            pr($response);
        }
        // pr($response);

        $return = array();
        if (empty($response))
            return false;

        $otp_verified = '0';

        $this->CI->load->config('cred_status');
        $courier_status = $this->CI->config->item('cred_webhook_status');
        $courier_status = !empty($courier_status['xpressbees']) ? $courier_status['xpressbees'] : '';
        $first_pickup_attempt = $last_pickup_attempt = $pickup_attempt_count = $picked_date = $shipped_date = 0;

        foreach ($response as $shipment) {
            $history = array();
            $retn = array();
            $reached_at_destination = array();
            $status_wise_logs = array();
            if (!empty($shipment->ShipmentSummary)) {
                $shipment->ShipmentSummary = array_reverse((array) $shipment->ShipmentSummary);
                foreach ($shipment->ShipmentSummary as $k => $scan) {
                    $status_code = $scan->StatusCode;
                    $ship_status = $this->getShipStatus($status_code);

                    $event_time = strtotime($scan->StatusDate . ' ' . $scan->StatusTime) + $k;

                    $his = array(
                        'event_time' => $event_time,
                        'status_code' => $status_code,
                        'location' => $scan->Location,
                        'message' => $scan->Status,
                        'status' => $scan->Status,
                        'ship_status' => $ship_status,
                    );

                    if (!empty($scan->PickUpDate))
                        $retn['pickup_time'] = strtotime($scan->PickUpDate . ' ' . $scan->PickUpTime);

                    if (!empty($scan->ExpectedDeliveryDate))
                        $retn['expected_delivery_date'] = strtotime($scan->ExpectedDeliveryDate);

                    if (!empty($scan->Weight))
                        $retn['weight'] = $scan->Weight;

                    if (!empty($scan->Comment) && ((strpos(strtolower($scan->Comment), 'isauthenticndr : true') !== false) || (strpos(strtolower($scan->Comment), 'otp verified') !== false) || (strpos(strtolower($scan->Comment), 'isotpverified=yes') !== false))) {
                        $otp_verified = '1';
                    }

                    if (!empty($scan->Comment) && in_array($ship_status, array('out for delivery'))) {
                        $comment = explode(",", $scan->Comment);

                        $retn['additional_tracking_info'] = [];

                        foreach ($comment as $key => $value) {
                            if (strpos(strtolower($value), 'out for delivery') !== false) {
                                $name = explode("-", $value);
                                $name = !empty($name[1]) ? trim($name[1]) : $value;
                                $name = explode("(", $name);
                                $retn['additional_tracking_info']['name'] = !empty($name[0]) ? trim($name[0]) : $name;
                            } else if (strpos(strtolower($value), 'mobileno') !== false) {
                                $mobile = explode("-", $value);
                                $retn['additional_tracking_info']['mobile'] = !empty($mobile[1]) ? trim($mobile[1]) : $value;
                            }
                        }

                        $retn['additional_tracking_info'] = json_encode($retn['additional_tracking_info']);
                    }

                    if ($scan->StatusCode == 'RAD')
                        $reached_at_destination[] = strtotime($scan->StatusDate . ' ' . $scan->StatusTime);

                    $status_wise_logs[$ship_status][] = $his;

                    $history[] = $his;

                    if($courier_status && array_key_exists(strtoupper($status_code), $courier_status)) {
                        switch (strtolower($courier_status[strtoupper($status_code)])) {
                            case 'out_for_pickup':
                                $first_pickup_attempt = !empty($first_pickup_attempt) ? $first_pickup_attempt : $event_time;
                                $last_pickup_attempt = $event_time;
                                $pickup_attempt_count++;
                                break;

                            case 'picked':
                                $picked_date = !empty($picked_date) ? $picked_date : $event_time;
                                break;

                            case 'shipped':
                                $shipped_date = !empty($shipped_date) ? $shipped_date : $event_time;
                                break;
                            
                            default:
                                break;
                        }
                    }
                }

                foreach ($status_wise_logs as $sw_key => $s_w_l) {
                    switch ($sw_key) {
                        case 'out for delivery':
                            $ofd_logs = $s_w_l;
                            $retn['total_ofd_attempts'] = count($ofd_logs);
                            array_multisort(array_column($ofd_logs, 'event_time'), SORT_ASC, $ofd_logs);
                            $i = 0;
                            foreach ($ofd_logs as $of_key => $of_log) {
                                $retn['ofd_attempt_' . ($of_key + 1) . '_date'] = $of_log['event_time'];
                                $retn['last_attempt_date'] = $of_log['event_time'];
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
                            $retn['delivery_attempt_count'] = count($exception_logs);
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

                $retn['reached_at_destination_hub'] = (!empty($reached_at_destination)) ? min($reached_at_destination) : 0;
                $retn['history'] = $history;

                array_multisort(array_column($history, 'event_time'), SORT_DESC, $history);

                $retn['shipment_status'] = strtolower($history[0]['status']);

                $retn['otp_verified'] = !empty($retn['delivered_time']) ? $otp_verified : '0';
                $retn['first_pickup_attempt'] = $first_pickup_attempt;
                $retn['last_pickup_attempt'] = $last_pickup_attempt;
                $retn['pickup_attempt_count'] = $pickup_attempt_count;
                $retn['picked_date'] = $picked_date;
                $retn['shipped_date'] = $shipped_date;
            }

            $return[$shipment->AWBNo] = $retn;
        }

        if (defined('print_tracking') && print_tracking == 'yes') {
            pr($return);
        }

        // pr($return, 1);
        return $return;
    }

    function getShipStatus($status = false)
    {
        $status = strtolower($status);

        switch ($status) {
            case 'drc':
            case 'puc':
            case 'ofp':
            case 'pnd':
            case 'rppickuppending':
            case 'rpoutforpickup':
            case 'rpattemptnotpick':
                $ship_status = 'pending pickup';
                break;
            case 'pud':
            case 'pkd':
            case 'it':
            case 'rad':
            case 'lost':
            case 'std':
            case 'stg':
            case 'rppickdone':
            case 'it':
            case 'rad':
                $ship_status = 'in transit';
                break;
            case 'ofd':
                $ship_status = 'out for delivery';
                break;
            case 'ud':
            case 'rpcancel':
            case 'rvpcancelledqcfail':
                $ship_status = 'exception';
                break;
            case 'dlvd':
                $ship_status = 'delivered';
                break;
            case 'rton':
            case 'rto':
            case 'rao':
            case 'rto-it':
            case 'rto-ofd':
            case 'rto-stg':
            case 'rtu':
                $ship_status = 'rto in transit';
                break;
            case 'rtd':
                $ship_status = 'rto delivered';
                break;
            default:
                $ship_status = '';
        }

        return $ship_status;
    }

    function pushNDRAction($ndr_data = false)
    {
        if (empty($ndr_data['awb_number']) || empty($ndr_data['action']))
            return false;

        $awb_number = $ndr_data['awb_number'];

        switch ($ndr_data['action']) {
            case 're-attempt':
                $post_data =  array(
                    "ShippingID" => $awb_number,
                    "DeferredDeliveryDate" => (!empty($ndr_data['re_attempt_date'])) ? date('Y-m-d H:i:s', $ndr_data['re_attempt_date']) : date('Y-m-d H:i:s', strtotime('+1 day'))
                );
                break;
            case 'change address':
                $post_data =  array(
                    "ShippingID" => $awb_number,
                    "AlternateCustomerMobileNumber" => (!empty($ndr_data['change_phone'])) ? $ndr_data['change_phone'] : '',
                    "AlternateCustomerAddress" => ((!empty($ndr_data['change_address_1'])) ? $ndr_data['change_address_1'] : '') . ' ' . ((!empty($ndr_data['change_address_2'])) ? $ndr_data['change_address_2'] : ''),
                );
                break;
            case 'change phone':
                $post_data =  array(
                    "ShippingID" => $awb_number,
                    "AlternateCustomerMobileNumber" => (!empty($ndr_data['change_phone'])) ? $ndr_data['change_phone'] : ''
                );
                break;

            default:
                return false;
        }

        if ($this->debug)
            $url = 'http://114.143.206.69:803/StandardForwardStagingService.svc/UpdateNDRDeferredDeliveryDate'; // Demo
        else
            $url = 'http://xbclientapi.xbees.in/POSTShipmentService.svc/UpdateNDRDeferredDeliveryDate'; // Live


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
                "accept: application/json",
                "Content-Type: application/json",
                "XBKey:" . $this->api_key
            ),
            CURLOPT_POSTFIELDS => json_encode($post_data),
        ));
        $response = curl_exec($curl);
        $err = curl_error($curl);
        curl_close($curl);

        $response = json_decode($response);

        if (empty($response)) {
            $this->error = 'No response from server';
            return false;
        }

        if (empty($response->UpdateNDRDeferredDeliveryDate->ReturnMessage) || $response->UpdateNDRDeferredDeliveryDate->ReturnCode != "100") {
            $this->error = 'Error in API request';
            return false;
        }

        $return = array(
            'message' => $response->UpdateNDRDeferredDeliveryDate->ReturnMessage,
        );

        return $return;
    }

}
