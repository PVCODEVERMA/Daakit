<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Delhivery extends MY_lib
{

    private $api_key;
    private $api_url;
    private $courier_id_api_token;
    private $mode;

    public function __construct($config = false)
    {
        parent::__construct();
        $this->api_key = $this->CI->config->item('delhivery_surface_api_key');
        $this->mode = 'surface';
        if (!empty($config['mode']))
            switch ($config['mode']) {
                case 'delhivery_surface':
                    $this->api_key = $this->CI->config->item('delhivery_surface_api_key');
                    $this->mode = 'surface';
                    break;
                case 'delhivery_air':
                    $this->api_key = $this->CI->config->item('delhivery_surface_api_key');
                    $this->mode = 'air';
                    break;                    
                case 'delhivery_surface_2kg':
                    $this->api_key = $this->CI->config->item('delhivery_surface_2kg_api_key');
                    $this->mode = 'surface';
                    break;
                case 'delhivery_surface_5kg':
                    $this->api_key = $this->CI->config->item('delhivery_surface_5kg_api_key');
                    $this->mode = 'surface';
                    break;
                case 'delhivery_surface_10kg':
                    $this->api_key = $this->CI->config->item('delhivery_surface_10_api_key');
                    $this->mode = 'surface';
                    break;
                default:
                    $this->api_key = $this->CI->config->item('delhivery_surface_api_key');
                    $this->mode = 'surface';
                    break;
            }

        $this->api_url = 'https://track.delhivery.com/';
    }

    function createOrder($order = array())
    {
        if (empty($order)) {
            $this->error = 'Invalid Order';
            return false;
        }

        $data = array(
            'format' => 'json',
            'data' => array()
        );

        if (!empty($order['pickup'])) {
            $pickup = $order['pickup'];
            $data['data']['pickup_location'] = array(
                'name' => 'DKT_' . $pickup['warehouse_id'],
                'add' => $pickup['address_1'] . ' ' . $pickup['address_2'] . ' ' . $pickup['city'] . ' ' . $pickup['state'] . ' ' . $pickup['zip'],
                'city' => $pickup['city'],
                'country' => 'India',
                'phone' => $pickup['phone'],
                'pin' => $pickup['zip']
            );
        }

        $ord = $order['order'];
        $products = $order['products'];
        $customer = $order['customer'];
        $rto = (!empty($order['rto'])) ? $order['rto'] : array();

        $courier_type = (!empty($order['courier']['courier_type']) && strtolower($order['courier']['courier_type']) == 'surface') ? 'Surface' : 'Express';
        $weight = $ord['weight'];
        $data['data']['shipments'][] = array(
            'name' => !empty($customer['name']) ? $customer['name'] : '',
            'order' => !empty($ord['shipment_id']) ? $ord['shipment_id'] : rand(11111, 99999),
            'shipping_mode' => $courier_type,
            'products_desc' => !empty($products) ? mb_strimwidth(implode(',', array_column($products, 'name')), 0, 50) : '',
            'order_date' => !empty($ord['date']) ? date('Y-m-d h:i:s', $ord['date']) : '',
            'payment_mode' => !empty($ord['payment_method']) ? ((strtolower($ord['payment_method']) == 'reverse') ? 'Pickup' : $ord['payment_method']) : '',
            'total_amount' => !empty($ord['total']) ? $ord['total'] : '',
            'cod_amount' => !empty($ord['total']) ? $ord['total'] : '',
            'weight' => $weight,
            'shipment_length' => (int)$ord['length'],
            'shipment_width' => (int)$ord['breadth'],
            'shipment_height' => (int)$ord['height'],
            'add' => !empty($customer['address']) ? $customer['address'] : '',
            'add2' => !empty($customer['address_2']) ? $customer['address_2'] : '',
            'city' => !empty($customer['city']) ? $customer['city'] : '',
            'state' => !empty($customer['state']) ? $customer['state'] : '',
            'country' => !empty($customer['country']) ? $customer['country'] : 'India',
            'phone' => !empty($customer['phone']) ? $customer['phone'] : '',
            'pin' => !empty($customer['zip']) ? $customer['zip'] : '',
            'quantity' => !empty($products) ? array_sum(array_column($products, 'qty')) : '1',
            'return_state' => !empty($rto['state']) ? $rto['state'] : '',
            'return_city' => !empty($rto['city']) ? $rto['city'] : '',
            'return_phone' => !empty($rto['phone']) ? $rto['phone'] : '',
            'return_add' => (!empty($rto['address_1']) ? $rto['address_1'] : '') . (!empty($rto['address_2']) ? ' ' . $rto['address_2'] : ''),
            'return_pin' => !empty($rto['zip']) ? $rto['zip'] : '',
            'return_name'  => !empty($rto['contact_name']) ? $rto['contact_name'] : '',
        );

        $data['data'] = json_encode($data['data']);
        $opts = array(
            'http' =>
            array(
                'method' => 'POST',
                'header' => array(
                    'Content-type: application/x-www-form-urlencoded',
                    'Authorization: Token ' . $this->api_key
                ),
                'content' => http_build_query($data)
            )
        );
        
        $context = stream_context_create($opts);
        $api_result = file_get_contents($this->api_url . 'api/cmu/create.json', false, $context);
        $response = json_decode($api_result);

        do_action('log.create', 'shipment', [
            'action' => 'delhivery_request_response',
            'ref_id' => $ord['shipment_id'],
            'user_id' => $ord['user_id'],
            'data' => array('request'=>$data,'response'=>$response)
        ]);
        $return = array();
        if (empty($response->packages)) {
            $this->error = !empty($response->rmk) ? $response->rmk : 'Internal Error';
            return false;
        }

        foreach ($response->packages as $pkg) {
            $return[$pkg->refnum]['status'] = 'success';
            $return[$pkg->refnum]['awb'] = $pkg->waybill;
            $return[$pkg->refnum]['shipment_info_1'] = $pkg->sort_code;
            $return[$pkg->refnum]['shipment_weight'] = '0';

            if ($pkg->status == 'Fail') {
                $return[$pkg->refnum]['status'] = 'error';
                $return[$pkg->refnum]['awb'] = $pkg->remarks[0];
            }
        }

        return $return;
    }

    function pickup($packets = 1, $date = false, $time = false, $location = 'Jet 42')
    {
        if (!$date)
            $date = date('Y-m-d');

        if (!$time)
            $time = '19:00:00';

        $data = array(
            'pickup_time' => $time,
            'pickup_date' => $date,
            'pickup_location' => 'DKT_' . $location,
            'expected_package_count' => $packets
        );

        $rand = date('H_i_s');

        $log = new Logs();
        $log->create('Pickup_Request_delhivery' . $rand, 'Request Data', $data);

        $curl = curl_init();
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt_array($curl, array(
            CURLOPT_URL => $this->api_url . 'fm/request/new/',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "POST",
            CURLOPT_HTTPHEADER => array(
                "accept: application/json",
                "authorization: Token " . $this->api_key,
                "content-type: application/json"
            ),
            CURLOPT_POSTFIELDS => json_encode($data),
        ));
        $response = curl_exec($curl);
        $err = curl_error($curl);
        curl_close($curl);

        $response = json_decode($response);

        $log = new Logs();
        $log->create('Pickup_Request_delhivery_' . $rand, 'Response Data', $response);

        if (!empty($response->pickup_id))
            return $response->pickup_id;

        return false;
    }

    function cancelAWB($awb = false)
    {
        if (!$awb)
            return false;

        $data = array(
            'waybill' => $awb,
            'cancellation' => 'true'
        );

        $curl = curl_init();
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt_array($curl, array(
            CURLOPT_URL => $this->api_url . 'api/p/edit',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "POST",
            CURLOPT_HTTPHEADER => array(
                "accept: application/json",
                "authorization: Token " . $this->api_key,
                "content-type: application/json"
            ),
            CURLOPT_POSTFIELDS => json_encode($data),
        ));
        $response = curl_exec($curl);
        $err = curl_error($curl);
        curl_close($curl);

        $response = json_decode($response);

        if (empty($response) || $response->status != 'true')
            return false;

        return true;
    }

    function trackOrder($awb = false)
    {
        if (!$awb)
            return false;

        // $data = array(
        //     'waybill' => $awb,
        //     'verbose' => 2
        // );
        $data = array(
            'waybill' => $awb,
            'verbose' => 2,
            'token' => $this->api_key
        );
        $opts = array(
            'http' => array(
            'method'  => 'GET',
            'header'  => "Content-type: application/x-www-form-urlencoded\r\n" .
            "User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:112.0) Gecko/20100101 Firefox/112.0\r\n"
            )
        );

        $context = stream_context_create($opts);
        $api_result = file_get_contents($this->api_url . 'api/v1/packages/json/?' . http_build_query($data), false, $context);
        $response = json_decode($api_result);
       // pr($response,1);
        if (defined('print_tracking') && print_tracking == 'yes') {
            pr($response);
        }
        if (empty($response->ShipmentData))
            return false;

        $otp_base_delivery = '0';
        $otp_verified_cancelled='0';
        $ivr_verified_cancelled = '0';

        $this->CI->load->config('cred_status');
        $courier_status = $this->CI->config->item('cred_webhook_status');
        $courier_status = !empty($courier_status['delhivery']) ? $courier_status['delhivery'] : '';
        $first_pickup_attempt = $last_pickup_attempt = $pickup_attempt_count = $picked_date = $shipped_date = 0;

        $return = array();
        foreach ($response->ShipmentData as $shipment) {
            $shipment = $shipment->Shipment;
            $history = array();
            $status_wise_logs = array();
            if (!empty($shipment->Scans)) {
                foreach ($shipment->Scans as $k => $scan) {
                    $ship_status = $this->getShipStatus($scan->ScanDetail->ScanType, $scan->ScanDetail->StatusCode, $scan->ScanDetail->Scan);

                    $status_code = strtoupper(str_replace(' ', '_', ($scan->ScanDetail->ScanType . '_' . $scan->ScanDetail->Scan . '_' . $scan->ScanDetail->StatusCode)));

                    $event_time = strtotime($scan->ScanDetail->ScanDateTime) + $k;

                    $his = array(
                        'event_time' => $event_time,
                        'status_code' => $status_code,
                        'location' => $scan->ScanDetail->ScannedLocation,
                        'message' => $scan->ScanDetail->Instructions,
                        'status' => $scan->ScanDetail->Scan,
                        'ship_status' => $ship_status,
                    );
                    
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
            }

            if(!empty($shipment->Status->StatusCode) && (strtolower($shipment->Status->StatusCode) == 'eod-135')) {
                $otp_base_delivery = '1';
            }
            if(!empty($shipment->Status->StatusCode) && (strtolower($shipment->Status->StatusCode) == 'eod-6o')) {
                $otp_verified_cancelled = '1';
            }           
            if(!empty($shipment->Status->StatusCode) && (strtolower($shipment->Status->StatusCode) == 'eod-6i')) {
                $ivr_verified_cancelled = '1';
            }

            $return_awb = array(
                'shipment_status' => (!empty($shipment->Status->Status)) ? $shipment->Status->Status : '',
                'reached_at_destination_hub' => (!empty($shipment->DestRecieveDate)) ? strtotime($shipment->DestRecieveDate) : '',
                'history' => $history
            );

            foreach ($status_wise_logs as $sw_key => $s_w_l) {
                switch ($sw_key) {
                    case 'in transit':
                        $transit_logs = $s_w_l;
                        array_multisort(array_column($transit_logs, 'event_time'), SORT_ASC, $transit_logs);
                        $return_awb['pickup_time'] = $transit_logs[0]['event_time'];
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
            
            $return_awb['otp_base_delivery'] = $otp_base_delivery;
            $return_awb['otp_verified_cancelled'] = $otp_verified_cancelled;
            $return_awb['ivr_verified_cancelled'] = $ivr_verified_cancelled;

            $return_awb['first_pickup_attempt'] = $first_pickup_attempt;
            $return_awb['last_pickup_attempt'] = $last_pickup_attempt;
            $return_awb['pickup_attempt_count'] = $pickup_attempt_count;
            $return_awb['picked_date'] = $picked_date;
            $return_awb['shipped_date'] = $shipped_date;

            $return[$shipment->AWB] = $return_awb;
        }

        if (defined('print_tracking') && print_tracking == 'yes') {
            pr($return);
        }

        // pr($return, 1);

        return $return;
    }

    function getShipStatus($scan_type = false, $status_code = false, $scan = false)
    {
        if (!$scan_type)
            return false;

        $status_code = strtoupper($status_code);
        $scan_type = strtoupper($scan_type);
        $scan = strtoupper($scan);

        $status_string = $scan_type . '_' . $scan . '_' . $status_code;

        $status_string = str_replace(' ', '_', $status_string);

        switch ($status_string) {
            case 'UD_MANIFESTED_X-UCI':
            case 'UD_MANIFESTED_FMEOD-139':
            case 'UD_NOT_PICKED_PNP-101':
            case 'UD_NOT_PICKED_X-PNP':
            case 'UD_IN_TRANSIT_PNP-102':
            case 'PP_OPEN_X-UCO':
            case 'PP_SCHEDULED_X-ASP':
            case 'PP_SCHEDULED_CL-101':
            case 'PP_DISPATCHED_X-DDD3FP':
            case 'PP_DISPATCHED_X-DDD1FP':
            case 'PP_DISPATCHED_X-DDD2FP':
            case 'PP_DISPATCHED_X-DDD4FP':
            case 'PP_DISPATCHED_X-DDD4FR':
            case 'PP_DISPATCHED_X-DDD2FR':
            case 'PP_DISPATCHED_X-DDD1FR':
            case 'PP_DISPATCHED_X-DDD3FR':
            case 'PP_DISPATCHED_X-LM1P':
            case 'CN_CANCELED_EOD-21':
            case 'CN_CANCELED_EOD-84':
            case 'CN_CANCELED_EOD-131':
            case 'CN_CANCELED_EOD-132':
            case 'CN_CANCELED_EOD-95':
            case 'CN_CANCELED_EOD-106':
            case 'CN_CANCELED_EOD-108':
            case 'PP_SCHEDULED_EOD-73':
            case 'PP_SCHEDULED_EOD-65':
            case 'PP_SCHEDULED_EOD-121':
            case 'PP_SCHEDULED_EOD-68':
            case 'PP_SCHEDULED_EOD-26':
            case 'CN_CANCELED_ST-109':
            case 'CN_CANCELED_ST-112':
            case 'PP_CANCELED_CL-102':
            case 'PP_CLOSED_CL-103':
            case 'UD_MANIFESTED_X-NSZ':
            case 'UD_MANIFESTED_DTUP-219':
            case 'UD_MANIFESTED_FMEOD-101':
            case 'UD_MANIFESTED_FMEOD-103':
            case 'UD_MANIFESTED_FMEOD-106':
            case 'UD_MANIFESTED_FMEOD-152':
            case 'PP_OPEN_X-UCI':
            case 'PP_DISPATCHED_ST-114':
            case 'CN_CLOSED_CL-107':
            case 'UD_MANIFESTED_FMEOD-110':
            case 'UD_MANIFESTED_DTUP-231':
            case 'UD_MANIFESTED_DTUP-205':
            case 'UD_MANIFESTED_FMPUR-101':
                $ship_status = 'pending pickup';
                break;
            case 'UD_IN_TRANSIT_X-PPOM':
            case 'UD_IN_TRANSIT_X-PPONM':
            case 'UD_IN_TRANSIT_X-PROM':
            case 'UD_IN_TRANSIT_X-PRONM':
            case 'UD_IN_TRANSIT_X-PIOM':
            case 'UD_IN_TRANSIT_X-DLO2F':
            case 'UD_IN_TRANSIT_X-DLD1F':
            case 'UD_IN_TRANSIT_X-DLO1F':
            case 'UD_IN_TRANSIT_X-DLD0F':
            case 'UD_IN_TRANSIT_X-DLL0F':
            case 'UD_IN_TRANSIT_X-DLL2F':
            case 'UD_IN_TRANSIT_X-DLL1F':
            case 'UD_IN_TRANSIT_X-DLD2F':
            case 'UD_IN_TRANSIT_CS-101':
            case 'UD_IN_TRANSIT_X-DLO0F':
            case 'UD_IN_TRANSIT_X-ILO1F':
            case 'UD_IN_TRANSIT_X-ILL1F':
            case 'UD_IN_TRANSIT_X-ILO0F':
            case 'UD_IN_TRANSIT_X-ILD0F':
            case 'UD_IN_TRANSIT_X-ILL2F':
            case 'UD_IN_TRANSIT_X-ILO2F':
            case 'UD_IN_TRANSIT_X-ILD1F':
            case 'UD_IN_TRANSIT_X-ILL0F':
            case 'UD_IN_TRANSIT_X-ILD2F':
            case 'UD_PENDING_X-IBD3F':
            case 'UD_IN_TRANSIT_X-IBD1F':
            case 'UD_IN_TRANSIT_ST-107':
            case 'UD_IN_TRANSIT_ST-105':
            case 'UD_PENDING_ST-110':
            case 'UD_IN_TRANSIT_DLYRG-125':
            case 'UD_IN_TRANSIT_DLYLH-109':
            case 'UD_IN_TRANSIT_DLYRG-124':
            case 'UD_IN_TRANSIT_DLYLH-106':
            case 'UD_IN_TRANSIT_DLYMR-118':
            case 'UD_IN_TRANSIT_DLYRG-130':
            case 'UD_IN_TRANSIT_DLYLH-104':
            case 'UD_IN_TRANSIT_RT-104':
            case 'UD_DISPATCHED_DLYRPC-416':
            case 'UD_IN_TRANSIT_DLYLH-133':
            case 'UD_IN_TRANSIT_DLYLH-115':
            case 'UD_IN_TRANSIT_DLYRG-120':
            case 'UD_IN_TRANSIT_DLYRPC-419':
            case 'UD_PENDING_DLYDC-102':
            case 'UD_IN_TRANSIT_DLYLH-142':
            case 'UD_IN_TRANSIT_DOFF-128':
            case 'UD_IN_TRANSIT_DLYLH-135':
            case 'UD_IN_TRANSIT_S-MAR':
            case 'UD_IN_TRANSIT_DLYLH-139':
            case 'UD_IN_TRANSIT_DLYFM-102':
            case 'PU_IN_TRANSIT_EOD-77':
            case 'PU_IN_TRANSIT_X-PRC':
            case 'PU_IN_TRANSIT_X-DLD1R':
            case 'PU_IN_TRANSIT_X-DLL2R':
            case 'PU_IN_TRANSIT_X-DLL0R':
            case 'PU_IN_TRANSIT_X-DLD0R':
            case 'PU_IN_TRANSIT_X-DLO0R':
            case 'PU_IN_TRANSIT_X-DLL1R':
            case 'PU_IN_TRANSIT_X-DLD2R':
            case 'PU_IN_TRANSIT_X-DLO2R':
            case 'PU_IN_TRANSIT_X-ILD2R':
            case 'PU_IN_TRANSIT_X-ILD1R':
            case 'PU_IN_TRANSIT_X-ILD0R':
            case 'PU_IN_TRANSIT_X-ILO1R':
            case 'PU_IN_TRANSIT_X-ILO0R':
            case 'PU_IN_TRANSIT_X-ILL2R':
            case 'PU_IN_TRANSIT_X-ILL0R':
            case 'PU_IN_TRANSIT_X-ILL1R':
            case 'PU_IN_TRANSIT_X-ILO2R':
            case 'PU_PENDING_X-IBD4R':
            case 'PU_PENDING_X-IBD3R':
            case 'PU_PENDING_X-IBD1R':
            case 'PU_DISPATCHED_X-DRO2R':
            case 'PU_DISPATCHED_X-DRD2R':
            case 'PU_DISPATCHED_X-DRD1R':
            case 'PU_DISPATCHED_X-DRD4R':
            case 'PU_DISPATCHED_X-DRO1R':
            case 'PU_DISPATCHED_X-DRO4R':
            case 'PU_DISPATCHED_X-DRD3R':
            case 'PU_DISPATCHED_X-DRO3R':
            case 'PU_IN_TRANSIT_DLYRG-125':
            case 'PU_IN_TRANSIT_DLYLH-109':
            case 'PU_IN_TRANSIT_DLYRG-124':
            case 'PU_IN_TRANSIT_DLYLH-106':
            case 'PU_IN_TRANSIT_DLYMR-118':
            case 'PU_IN_TRANSIT_DLYRG-130':
            case 'PU_IN_TRANSIT_DLYLH-104':
            case 'PU_IN_TRANSIT_DLYLH-133':
            case 'PU_IN_TRANSIT_DLYLH-115':
            case 'PU_IN_TRANSIT_DLYRG-120':
            case 'PU_IN_TRANSIT_DLYRPC-419':
            case 'PU_IN_TRANSIT_DOFF-128':
            case 'PU_IN_TRANSIT_DLYLH-139':
            case 'PU_IN_TRANSIT_DLYLH-142':
            case 'PU_DISPATCHED_EOD-140':
            case 'UD_IN_TRANSIT_FM-101':
            case 'UD_IN_TRANSIT_GOR-DWS':
            case 'UD_IN_TRANSIT_X-DBO1F':
            case 'UD_IN_TRANSIT_X-OLL2F':
            case 'UD_IN_TRANSIT_X-IBL1F':
            case 'UD_IN_TRANSIT_X-DBL1F':
            case 'UD_DISPATCHED_ST-114':
            case 'UD_PENDING_ST-115':
            case 'UD_PENDING_ST-108':
            case 'UD_PENDING_ST-107':
            case 'UD_PENDING_ST-116':
            case 'RT_PENDING_RT-109':
            case 'RT_PENDING_RT-109':
            case 'UD_PENDING_L-PMA':
            case 'UD_PENDING_U-PMA':
            case 'UD_IN_TRANSIT_X-DWS':
            case 'UD_IN_TRANSIT_CS-CSL':
            case 'UD_IN_TRANSIT_X-IBO1F':
            case 'UD_IN_TRANSIT_CS-104':
            case 'UD_IN_TRANSIT_DLYLH-105':
            case 'UD_IN_TRANSIT_X-DBD1F':
            case 'UD_IN_TRANSIT_DTUP-205':
            case 'UD_IN_TRANSIT_DTUP-207':
            case 'UD_IN_TRANSIT_DTUP-208':
            case 'UD_IN_TRANSIT_X-NSZ':
            case 'UD_IN_TRANSIT_S-XIN':
            case 'UD_IN_TRANSIT_S-TAT2':
            case 'UD_IN_TRANSIT_X-IBD3F':
            case 'UD_IN_TRANSIT_DLYLH-126':
            case 'UD_IN_TRANSIT_DLYLH-136':
            case 'UD_IN_TRANSIT_DLYLH-152':
            case 'UD_IN_TRANSIT_DLYSOR-101':
            case 'UD_IN_TRANSIT_DLYSHRTBAG-115':
            case 'UD_IN_TRANSIT_X-UNEX':
            case 'UD_PENDING_X-UNEX':
            case 'UD_PENDING_DTUP-205':
            case 'UD_PENDING_DTUP-207':
            case 'UD_PENDING_X-AWD':
            case 'UD_PENDING_X-RWD':
            case 'UD_PENDING_DLYRG-130':
            case 'UD_PENDING_DLYB2B-101':
            case 'UD_IN_TRANSIT_DLYRG-135':
            case 'UD_IN_TRANSIT_DTUP-231':
            case 'UD_IN_TRANSIT_DLYRG-132':
            case 'UD_PENDING_ST-105':
            case 'UD_PENDING_DLYHD-007':
            case 'UD_IN_TRANSIT_DLYDC-101':
            case 'UD_PENDING_DLYDC-105':
            case 'UD_IN_TRANSIT_DLYDC-102':
            case 'UD_PENDING_DTUP-209':
            case 'UD_IN_TRANSIT_DLYLH-151':
            case 'LT_LOST_LT-100':
                $ship_status = 'in transit';
                break;
            case 'UD_DISPATCHED_X-DDD3FD':
            case 'UD_DISPATCHED_X-_LM1D':
            case 'UD_DISPATCHED_X-DDD3F':
            case 'UD_DISPATCHED_X-DDD2FD':
            case 'UD_DISPATCHED_X-DDD4FD':
            case 'UD_DISPATCHED_X-DDO3F':
            case 'UD_DISPATCHED_X-DDD1FD':
            case 'UD_DISPATCHED_X-DDD1LF':
            case 'UD_DISPATCHED_X-DDD3LF':
                $ship_status = 'out for delivery';
                break;
            case 'UD_PENDING_EOD-40':
            case 'UD_PENDING_EOD-43':
            case 'UD_PENDING_EOD-3':
            case 'UD_PENDING_EOD-6':
            case 'UD_PENDING_EOD-69':
            case 'UD_PENDING_EOD-134':
            case 'UD_PENDING_EOD-105':
            case 'UD_PENDING_EOD-104':
            case 'UD_PENDING_EOD-86':
            case 'UD_PENDING_EOD-15':
            case 'UD_PENDING_EOD-11':
            case 'UD_PENDING_EOD-16':
            case 'UD_PENDING_EOD-133':
            case 'UD_PENDING_EOD-_146':
            case 'UD_PENDING_EOD-111':
            case 'UD_PENDING_EOD-74':
            case 'UD_PENDING_SC-102':
            case 'UD_PENDING_SC-103':
            case 'UD_PENDING_SC-106':
            case 'UD_PENDING_SC-104':
            case 'UD_PENDING_X-SC':
            case 'UD_IN_TRANSIT_DLYRG-127':
            case 'UD_IN_TRANSIT_DLYRPC-417':
            case 'UD_PENDING_DLYDC-132':
            case 'UD_PENDING_DLYDC-107':
            case 'UD_PENDING_DLYDC-101':
            case 'UD_PENDING_DLYDC-416':
            case 'UD_PENDING_EOD-137':
            case 'UD_PENDING_EOD-138':
                //case 'UD_PENDING_ST-NI':
            case 'UD_PENDING_EOD-6o':
            case 'UD_PENDING_EOD-6I':
            case 'UD_PENDING_ST-NI6':
            case 'PU_PENDING_RD-PD4':
            case 'PU_PENDING_RD-PD12':
            case 'PU_PENDING_RD-PD7':
            case 'PU_PENDING_RD-PD8':
            case 'PU_PENDING_RD-PD10':
            case 'PU_PENDING_RD-PD11':
            case 'PU_PENDING_RD-PD17':
            case 'PU_PENDING_RD-PD18':
            case 'PU_PENDING_RD-PD15':
            case 'PU_PENDING_RD-PD3':
            case 'PU_PENDING_RD-PD20':
            case 'PU_PENDING_RD-PD21':
            case 'PU_PENDING_CL-105':
            case 'PU_DISPATCHED_DLYRPC-416':
            case 'PU_IN_TRANSIT_DLYRG-127':
            case 'PU_IN_TRANSIT_DLYRPC-417':
            case 'PP_SCHEDULED_DLYDC-107':
            case 'PP_SCHEDULED_DLYDC-101':
            case 'UD_PENDING_ST-NI':
                $ship_status = 'exception';
                break;
            case 'DL_DELIVERED_PL-105':
            case 'DL_DELIVERED_EOD-600':
            case 'DL_DELIVERED_EOD-141':
            case 'DL_DELIVERED_EOD-145':
            case 'DL_DELIVERED_EOD-135':
            case 'DL_DELIVERED_EOD-143':
            case 'DL_DELIVERED_EOD-37':
            case 'DL_DELIVERED_EOD-136':
            case 'DL_DELIVERED_EOD-144':
            case 'DL_DELIVERED_EOD-38':
            case 'DL_DELIVERED_EOD-36':
            case 'DL_DELIVERED_SC-101':
            case 'DL_DTO_RT-111':
            case 'DTO_DELIVERED_RD-AC':
            case 'DL_DTO_RD-AC':
                $ship_status = 'delivered';
                break;
            case 'RT_IN_TRANSIT_ST-108':
            case 'RT_IN_TRANSIT_X-DLD1R':
            case 'RT_IN_TRANSIT_X-DLO1R':
            case 'RT_IN_TRANSIT_X-DLD0R':
            case 'RT_IN_TRANSIT_X-DLL2R':
            case 'RT_IN_TRANSIT_X-DLL0R':
            case 'RT_IN_TRANSIT_X-DLO0R':
            case 'RT_IN_TRANSIT_X-DLL1R':
            case 'RT_IN_TRANSIT_X-DLD4R':
            case 'RT_IN_TRANSIT_X-DLD2R':
            case 'RT_IN_TRANSIT_X-DLO2R':
            case 'RT_IN_TRANSIT_X-ILD2R':
            case 'RT_IN_TRANSIT_X-ILD1R':
            case 'RT_IN_TRANSIT_X-ILO1R':
            case 'RT_IN_TRANSIT_X-ILD0R':
            case 'RT_IN_TRANSIT_X-ILO0R':
            case 'RT_IN_TRANSIT_X-ILL0R':
            case 'RT_IN_TRANSIT_X-ILL2R':
            case 'RT_IN_TRANSIT_X-ILL1R':
            case 'RT_IN_TRANSIT_X-ILO2R':
            case 'RT_PENDING_X-IBD4R':
            case 'RT_PENDING_X-IBD1R':
            case 'RT_PENDING_X-IBD3R':
            case 'RT_DISPATCHED_X-DRO4R':
            case 'RT_DISPATCHED_X-DRD4R':
            case 'RT_DISPATCHED_X-DRO2R':
            case 'RT_DISPATCHED_X-DRD2R':
            case 'RT_DISPATCHED_X-DRD1R':
            case 'RT_DISPATCHED_X-DRO1R':
            case 'RT_DISPATCHED_X-DRD3R':
            case 'RT_DISPATCHED_X-DRO3R':
            case 'RT_PENDING_RD-PD4':
            case 'RT_PENDING_RD-PD12':
            case 'RT_PENDING_RD-PD7':
            case 'RT_PENDING_RD-PD8':
            case 'RT_PENDING_RD-PD10':
            case 'RT_PENDING_RD-PD11':
            case 'RT_PENDING_RD-PD17':
            case 'RT_PENDING_RD-PD18':
            case 'RT_PENDING_RD-PD15':
            case 'RT_PENDING_RD-PD3':
            case 'RT_PENDING_RD-PD20':
            case 'RT_PENDING_RD-PD21':
            case 'RT_PENDING_RD-PD22':
            case 'RT_PENDING_CL-105':
            case 'RT_IN_TRANSIT_DLYRG-125':
            case 'RT_IN_TRANSIT_DLYLH-109':
            case 'RT_IN_TRANSIT_DLYRG-124':
            case 'RT_IN_TRANSIT_DLYLH-106':
            case 'RT_IN_TRANSIT_DLYMR-118':
            case 'RT_IN_TRANSIT_DLYRG-130':
            case 'RT_IN_TRANSIT_DLYLH-104':
            case 'RT_DISPATCHED_DLYRPC-416':
            case 'RT_IN_TRANSIT_DLYRG-127':
            case 'RT_IN_TRANSIT_DLYLH-133':
            case 'RT_IN_TRANSIT_DLYLH-115':
            case 'RT_IN_TRANSIT_DLYRG-120':
            case 'RT_IN_TRANSIT_DLYRPC-419':
            case 'RT_IN_TRANSIT_DLYRPC-417':
            case 'RT_PENDING_DLYDC-102':
            case 'RT_IN_TRANSIT_DLYDC-132':
            case 'RT_PENDING_DLYDC-107':
            case 'RT_IN_TRANSIT_DLYLH-142':
            case 'RT_PENDING_DLYDC-101':
            case 'RT_IN_TRANSIT_DLYLH-139':
            case 'RT_IN_TRANSIT_RT-104':
            case 'RT_IN_TRANSIT_DOFF-128':
            case 'RT_IN_TRANSIT_DLYLH-135':
            case 'RT_IN_TRANSIT_RT-106':
            case 'RT_IN_TRANSIT_RT-101':
            case 'RT_IN_TRANSIT_RT-113':
            case 'RT_IN_TRANSIT_RT-107':
            case 'RT_IN_TRANSIT_RT-109':
            case 'RT_IN_TRANSIT_RT-_108':
            case 'RT_IN_TRANSIT_DTUP-204':
            case 'RT_IN_TRANSIT_DTUP-205':
            case 'RT_IN_TRANSIT_X-DBO3R':
            case 'RT_IN_TRANSIT_X-DLL2F':
            case 'RT_IN_TRANSIT_X-ILL2F':
            case 'RT_IN_TRANSIT_X-ILL1F':
            case 'RT_IN_TRANSIT_X-IBL1R':
            case 'RT_IN_TRANSIT_X-DBL1R':
            case 'RT_IN_TRANSIT_EOD-6O':
            case 'RT_IN_TRANSIT_X-UNEX':
            case 'RT_IN_TRANSIT_X-DBO1R':
            case 'RT_IN_TRANSIT_X-IBO1R':
            case 'RT_IN_TRANSIT_DLYLH-105':
            case 'RT_IN_TRANSIT_DLYRG-132':
            case 'RT_IN_TRANSIT_X-DDD3FD':
            case 'RT_IN_TRANSIT_X-DBL1F':
            case 'RT_IN_TRANSIT_X-DWS':
            case 'RT_IN_TRANSIT_X-NSZ':
            case 'RT_IN_TRANSIT_X-PIOM':
            case 'RT_IN_TRANSIT_X-PROM':
            case 'RT_IN_TRANSIT_DLYSHRTBAG-115':
            case 'RT_PENDING_X-DBL1F':
            case 'RT_PENDING_X-IBD3F':
            case 'RT_DISPATCHED_X-DDD3FD':
            case 'RT_PENDING_EOD-148':
            case 'RT_IN_TRANSIT_X-IBD3F':
            case 'RT_IN_TRANSIT_RT-108':
            case 'RT_IN_TRANSIT_CS-CSL':
            case 'RT_IN_TRANSIT_ST-110':
            case 'RT_IN_TRANSIT_ST-120':
            case 'RT_IN_TRANSIT_CS-101':
            case 'RT_IN_TRANSIT_CS-104':
            case 'RT_IN_TRANSIT_EOD-6I':
            case 'RT_IN_TRANSIT_X-OLL2F':
                $ship_status = 'rto in transit';
                break;

            case 'DL_RTO_RT-110':
            case 'DL_RTO_RD-AC1':
            case 'DL_RTO_RD-AC':
            case 'DL_RTO_CL-109':
                $ship_status = 'rto delivered';
                break;

            default:
                $ship_status = '';
        }

        return $ship_status;

        $rto_status_codes = array(
            'ST-108',
            'X-DLD1R',
            'X-DLO1R',
            'X-DLD0R',
            'X-DLL2R',
            'X-DLL0R',
            'X-DLO0R',
            'X-DLL1R',
            'X-DLD4R',
            'X-DLD2R',
            'X-DLO2R',
            'X-ILD2R',
            'X-ILD1R',
            'X-ILO1R',
            'X-ILD0R',
            'X-ILO0R',
            'X-ILL0R',
            'X-ILL2R',
            'X-ILL1R',
            'X-ILO2R',
            'X-IBD4R',
            'X-IBD1R',
            'X-IBD3R',
            'X-DRO4R',
            'X-DRD4R',
            'X-DRO2R',
            'X-DRD2R',
            'X-DRD1R',
            'X-DRO1R',
            'X-DRD3R',
            'X-DRO3R',
            'RD-PD4',
            'RD-PD12',
            'RD-PD7',
            'RD-PD8',
            'RD-PD10',
            'RD-PD11',
            'RD-PD17',
            'RD-PD18',
            'RD-PD15',
            'RD-PD3',
            'RD-PD20',
            'RD-PD21',
            'RD-PD22',
            'DLYRG-125',
            'DLYLH-109',
            'DLYRG-124',
            'DLYLH-106',
            'DLYMR-118',
            'DLYRG-130',
            'DLYLH-104',
            'DLYRPC-416',
            'DLYRG-127',
            'DLYLH-133',
            'DLYLH-115',
            'DLYRG-120',
            'DLYRPC-419',
            'DLYRPC-417',
            'DLYDC-102',
            'DLYDC-132',
            'DLYDC-107',
            'DLYLH-142',
            'DLYDC-101',
            'DLYLH-139',
            'RT-104',
            'DOFF-128',
            'DLYLH-135',
            'RT-106',
            'RT-101',
            'RT-113',
            'RT-107',
            'RT-109',
            'RT- 108',
            'DTUP-204',
            'X-DBO3R',
            'X-DBD4R'
        );

        if ($scan_type == 'RT' && in_array($status_code, $rto_status_codes)) {
            return 'rto in transit';
        }

        switch ($status_code) {
            case 'X-UCI':
            case 'PNP-101':
            case 'X-PNP':
            case 'PNP-102':
            case 'X-UCO':
            case 'X-ASP':
            case 'CL-101':
            case 'CL-102':
            case 'CL-103':
            case 'X-DDD3FP':
            case 'X-DDD1FP':
            case 'X-DDD2FP':
            case 'X-DDD4FP':
            case 'X-DDD4FR':
            case 'X-DDD2FR':
            case 'X-DDD1FR':
            case 'X-DDD3FR':
            case 'X-LM1P':
            case 'EOD-21':
            case 'EOD-84':
            case 'EOD-131':
            case 'EOD-132':
            case 'EOD-95':
            case 'EOD-106':
            case 'EOD-108':
            case 'EOD-73':
            case 'EOD-65':
            case 'EOD-121':
            case 'EOD-68':
            case 'EOD-26':
            case 'ST-109':
            case 'ST-112':
                $ship_status = 'pending pickup';
                break;
            case 'X-PRC':
            case 'EOD-77':
            case 'X-PPOM':
            case 'X-PPONM':
            case 'X-PROM':
            case 'X-PRONM':
            case 'X-PIOM':
            case 'X-DLO2F':
            case 'X-DLD1F':
            case 'X-DLO1F':
            case 'X-DLD0F':
            case 'X-DLL0F':
            case 'X-DLL2F':
            case 'X-DLL1F':
            case 'X-DLD2F':
            case 'CS-101':
            case 'X-DLO0F':
            case 'X-ILO1F':
            case 'X-ILL1F':
            case 'X-ILO0F':
            case 'X-ILD0F':
            case 'X-ILL2F':
            case 'X-ILO2F':
            case 'X-ILD1F':
            case 'X-ILL0F':
            case 'X-ILD2F':
            case 'X-IBD3F':
            case 'X-IBD1F':
            case 'ST-107':
            case 'ST-105':
            case 'ST-110':
            case 'DLYRG-125':
            case 'DLYLH-109':
            case 'DLYRG-124':
            case 'DLYLH-106':
            case 'DLYMR-118':
            case 'DLYRG-130':
            case 'DLYLH-104':
            case 'RT-104':
            case 'DLYRPC-416':
            case 'DLYRG-127':
            case 'DLYLH-133':
            case 'DLYLH-115':
            case 'DLYRG-120':
            case 'DLYRPC-419':
            case 'DLYRPC-417':
            case 'DLYDC-102':
            case 'DLYLH-142':
            case 'EOD-140':
            case 'DLYDC-101':
            case 'DOFF-128':
            case 'DLYLH-135':
            case 'S-MAR':
            case 'DLYLH-139':
            case 'X-DLD1R':
            case 'X-DLL2R':
            case 'X-DLL0R':
            case 'X-DLD0R':
            case 'X-DLO0R':
            case 'X-DLL1R':
            case 'X-DLD2R':
            case 'X-DLO2R':
            case 'X-ILD2R':
            case 'X-ILD1R':
            case 'X-ILD0R':
            case 'X-ILO1R':
            case 'X-ILO0R':
            case 'X-ILL2R':
            case 'X-ILL0R':
            case 'X-ILL1R':
            case 'X-ILO2R':
            case 'X-IBD4R':
            case 'X-IBD3R':
            case 'X-IBD1R':
            case 'X-DRO2R':
            case 'X-DRD2R':
            case 'X-DRD1R':
            case 'X-DRD4R':
            case 'X-DRO1R':
            case 'X-DRO4R':
            case 'X-DRD3R':
            case 'X-DRO3R':
            case 'RD-PD4':
            case 'RD-PD12':
            case 'RD-PD7':
            case 'RD-PD8':
            case 'RD-PD10':
            case 'RD-PD11':
            case 'RD-PD17':
            case 'RD-PD18':
            case 'RD-PD15':
            case 'RD-PD3':
            case 'RD-PD20':
            case 'RD-PD21':
            case 'CL-105':
            case 'X-DBO1F':
            case 'X-IBL1F':
            case 'X-DBL1F':
            case 'GOR-DWS':
            case 'X-DBD1F':
            case 'ST-114':
            case 'FM-101':
            case 'X-OLL2F':
            case 'ST-115':
            case 'ST-116':
                $ship_status = 'in transit';
                break;
            case 'X-DDD3FD':
            case 'X- LM1D':
            case 'X-LM1D':
            case 'X-DDD3F':
            case 'X-DDD2FD':
            case 'X-DDD4FD':
            case 'X-DDO3F':
            case 'X-DDD1FD':
            case 'X-DDD1LF':
            case 'X-DDD3LF':
                $ship_status = 'out for delivery';
                break;
            case 'EOD-600':
            case 'EOD-141':
            case 'EOD-145':
            case 'EOD-135':
            case 'EOD-143':
            case 'EOD-37':
            case 'EOD-136':
            case 'EOD-144':
            case 'EOD-38':
            case 'EOD-36':
            case 'SC-101':
            case 'RT-111':
            case 'RD-AC':
                $ship_status = 'delivered';
                break;
            case 'EOD-40':
            case 'EOD-43':
            case 'EOD-3':
            case 'EOD-6':
            case 'EOD-69':
            case 'EOD-134':
            case 'EOD-105':
            case 'EOD-104':
            case 'EOD-86':
            case 'EOD-15':
            case 'EOD-11':
            case 'EOD-16':
            case 'EOD-133':
            case 'EOD- 146':
            case 'EOD-111':
            case 'EOD-74':
            case 'SC-102':
            case 'SC-103':
            case 'SC-106':
            case 'SC-104':
            case 'X-SC':
            case 'DLYDC-132':
            case 'DLYDC-107':
            case 'DLYDC-416':
            case 'DLYFM-102':
            case 'EOD-137':
            case 'EOD-138':
            case 'ST-NI':
            case 'EOD-6I':
            case 'ST-NI6':
            case 'ST-108':
                $ship_status = 'exception';
                break;
            case 'RD-AC1':
            case 'RT-110':
            case 'RD-AC':
                $ship_status = 'rto delivered';
                break;
            default:
                $ship_status = 'in transit';
        }

        return $ship_status;
    }

    function createUpdateWarehouse($data = array(), $update = false)
    {
        $save_data = array(
            'phone' => !empty($data['phone']) ? $data['phone'] : '',
            'city' => !empty($data['city']) ? $data['city'] : '',
            'name' => !empty($data['name']) ? $data['name'] : '',
            'pin' => !empty($data['pin']) ? $data['pin'] : '',
            'address' => (!empty($data['address_1']) ? $data['address_1'] : '') . (!empty($data['address_2']) ? $data['address_2'] : ''),
            'country' => 'India',
            //'person' => !empty($data['contact_person']) ? $data['contact_person'] : '',
            //'contact_person' => !empty($data['contact_person']) ? $data['contact_person'] : '',
            'email' => !empty($data['email']) ? $data['email'] : '',
            'registered_name' => !empty($data['contact_person']) ? $data['contact_person'] : '',
            'return_city' => !empty($data['city']) ? $data['city'] : '',
            'return_state' => !empty($data['state']) ? $data['state'] : '',
            'return_pin' => !empty($data['pin']) ? $data['pin'] : '',
            'return_address' => (!empty($data['address_1']) ? $data['address_1'] : '') . (!empty($data['address_2']) ? $data['address_2'] : ''),
            'return_country' => 'India',
        );
        //pr($update,1);
        if ($update) {
            $url = $this->api_url . 'api/backend/clientwarehouse/edit/';
        } else {
            $url = $this->api_url . 'api/backend/clientwarehouse/create/';
        }

        do_action('log.create', 'Delhivery Warehouse', [
            'action' => 'create',
            'warehouse_name' => $data['name'],
            'data' => $save_data,
            'url' => $url
        ]);

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
                "authorization: Token " . $this->api_key,
                "content-type: application/json"
            ),
            CURLOPT_POSTFIELDS => json_encode($save_data),
        ));

        $response = curl_exec($curl);
        $err = curl_error($curl);
        curl_close($curl);

        $response = json_decode($response);

        do_action('log.create', 'Delhivery Warehouse', [
            'action' => 'response',
            'warehouse_name' => $data['name'],
            'data' => $response,
        ]);

        if (empty($response)) {
            $this->error = 'Internal Error';
            return false;
        }

        if ($response->success == '1' || (!empty($response->error_code[0]) && $response->error_code[0] == '2000'))
            return true;

        if (empty($response->success)) {
            $error = !empty($response->error) ? (array) $response->error : '';
            $this->error = !empty($error[0]) ? $error[0] : 'Internal Error';
            return false;
        }

        return false;
    }

    function pushNDRAction($ndr_data = false)
    {
        if (empty($ndr_data['awb_number']) || empty($ndr_data['action']))
            return false;

        $awb_number = $ndr_data['awb_number'];

        switch ($ndr_data['action']) {
            case 're-attempt':
                $post_data =  array(
                    'act' => 'DEFER_DLV',
                    'action_data' => array(
                        'deferred_date' => (!empty($ndr_data['re_attempt_date'])) ? date('Y-m-d', $ndr_data['re_attempt_date']) : date('Y-m-d', strtotime('+1 day')),
                    ),
                );
                break;
            case 'change address':
                $post_data =  array(
                    'update_request_type' => 'EDIT_DETAILS',
                    'action_data' => array(
                        'customer_address' =>     array(
                            'name' => $ndr_data['change_name'],
                            'add' => (!empty($ndr_data['change_address_1'])) ? $ndr_data['change_address_1'] : '',
                            'phone' => (!empty($ndr_data['change_phone'])) ? $ndr_data['change_phone'] : '',
                        )
                    ),
                );
                break;
            case 'change phone':
                $post_data =  array(
                    'update_request_type' => 'EDIT_DETAILS',
                    'action_data' => array(
                        'customer_address' =>     array(
                            'phone' => (!empty($ndr_data['change_phone'])) ? $ndr_data['change_phone'] : '',
                        )
                    ),
                );
                break;

            default:
                return false;
        }

        $post_data['waybill'] = $awb_number;

        $post_data = array(
            'data' => array($post_data)
        );

        $url = $this->api_url . 'api/p/update';

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
                "authorization: Token " . $this->api_key,
                "content-type: application/json"
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

        if (empty($response->message)) {
            $this->error = 'Error in API request';
            return false;
        }

        $return = array(
            'message' => $response->message,
        );

        return $return;
    }
}
