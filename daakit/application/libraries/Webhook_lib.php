<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Webhook_lib extends MY_lib
{

    public function __construct()
    {
        parent::__construct();

        $this->CI->load->model('webhook_model');
    }

    public function __call($method, $arguments)
    {
        if (!method_exists($this->CI->webhook_model, $method)) {
            throw new Exception('Undefined method webhook_model::' . $method . '() called');
        }

        return call_user_func_array([$this->CI->webhook_model, $method], $arguments);
    }


    function execute($webhook_id = false, $webhook_data = false)
    {
        if (!$webhook_id || empty($webhook_data))
            return false;

        if (empty($webhook_data['awb_number']) || empty($webhook_data['ship_status']))
            return false;

        $webhook = $this->getByID($webhook_id);

        if (empty($webhook) || $webhook->status != '1')
            return false; //webhook is not active

        $shipment_id = $webhook_data['shipment_id'];

        $send_data = array(
            'order_id' => isset($webhook_data['order_id']) ? $webhook_data['order_id'] : '',
            'order_number' => isset($webhook_data['order_number']) ? $webhook_data['order_number'] : '',
            'awb_number' => isset($webhook_data['awb_number']) ? $webhook_data['awb_number'] : '',
            'status' => strtolower(isset($webhook_data['ship_status']) ? $webhook_data['ship_status'] : ''),
            'event_time' => date('Y-m-d H:i:s', isset($webhook_data['event_time']) ? $webhook_data['event_time'] : time()),
            'location' => isset($webhook_data['location']) ? $webhook_data['location'] : '',
            'message' => isset($webhook_data['message']) ? $webhook_data['message'] : '',
            'rto_awb' => isset($webhook_data['rto_awb']) ? $webhook_data['rto_awb'] : '',
        );
        $webhook_data = json_encode($send_data);

        $hash = base64_encode(hash_hmac('sha256', $webhook_data, $webhook->secret, true));

        $headers = array(
            'Content-Type: application/json',
            'X-Hmac-SHA256:' . $hash
        );

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $webhook->url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_TIMEOUT, 5);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $webhook_data);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

        $result = curl_exec($ch);
        $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

        $last_event = $this->getShipmentLastEvent($shipment_id, $webhook->id);

        $save = array(
            'webhook_id' => $webhook->id,
            'shipment_id' => $shipment_id,
            'last_event_time' => isset($webhook_data['event_time']) ? $webhook_data['event_time'] : time(),
            'last_http_status' => $httpcode
        );

        if (!empty($last_event)) {
            //update existing record
            $this->updateEvent($last_event->id, $save);
        } else {
            //create new record
            $this->createEvent($save);
        }

        //update webhook if webhook failed
        $save_webhook = array();

        if (!in_array($httpcode, [200, 201])) { //increment failed count
            $save_webhook = array(
                'failed_count' => $webhook->failed_count + 1,
            );

            if (($webhook->failed_count + 1) >= $this->CI->config->item('failed_webhook_numbers')) {
                $save_webhook['status'] = '2';
            }
        } elseif ($webhook->failed_count > 0) {
            //reset failed count
            $save_webhook = array(
                'failed_count' => '0',
            );
        }

        if (!empty($save_webhook)) {
            $this->update($webhook_id, $save_webhook);
        }

        return true;
    }

    public function cred_webhook_trigger($shipment_id = false, $user_id = false)
    {
        if (!$shipment_id)
            return false;

        $cred_user_id = $this->CI->config->item('cred_user_enviroment');
        if (empty($cred_user_id[$user_id]['api_key']) || empty($cred_user_id[$user_id]['url']))
            return false;
        $cred_zone = $this->CI->config->item('cred_courier_edd_days');

        $api_key = $cred_user_id[$user_id]['api_key'];
        $url = $cred_user_id[$user_id]['url'];

        $this->CI->load->library('shipping_lib');
        $shipment = $this->CI->shipping_lib->getShipmentByID($shipment_id);

        $this->CI->load->config('cred_status');
        $courier_status = $this->CI->config->item('cred_webhook_status');
        $ncourier_status = $this->CI->config->item('delta_webhook_status');

        $this->CI->load->library('courier_lib');
        $courier = $this->CI->courier_lib->getByCourierid($shipment->courier_id);

        $this->CI->load->library('tracking_lib');
        $tracking = $this->CI->tracking_lib->getByAWB($shipment->awb_number);

        if (!empty($shipment->rto_awb)) {
            $rto_tracking = $this->CI->tracking_lib->getByAWB($shipment->rto_awb);

            if (!empty($rto_tracking)) {
                $tracking = array_merge($tracking, $rto_tracking);
            }
        }

        array_multisort(array_column($tracking, 'event_time'), SORT_ASC, $tracking);

        $status = array();
        $this->CI->load->library('cred_lib');
        $status = $this->CI->cred_lib->getWebookStatusByAwb($shipment->awb_number);

        $status = !empty($status['event_time']) ? explode(',', $status['event_time']) : '';

        $insert = array();
        $picked = $shipped = false;
        foreach ($tracking as $trk) {
            $status_code = !empty($courier_status[strtolower($courier->display_name)][strtoupper($trk->status_code)]) ? $courier_status[strtolower($courier->display_name)][strtoupper($trk->status_code)] : '';
            if (empty($status_code)) {
                $status_code = !empty($ncourier_status[$trk->ship_status]) ? $ncourier_status[$trk->ship_status] : '';
            }

            if ($status && in_array($trk->event_time, $status) && !in_array($status_code, ['picked', 'shipped'])) {
                continue;
            }

            $statuscode = $this->CI->cred_lib->getWebookStatuscodeByAwb($shipment->awb_number, $status_code);

            if (!empty($statuscode['status_code']) && in_array($statuscode['status_code'], ['picked', 'shipped'])) {
                continue;
            }

            if (!empty($picked) && ($status_code == 'picked'))
                continue;

            if (!empty($shipped) && ($status_code == 'shipped'))
                continue;

            if ($status_code == 'picked') {
                $picked = true;
            }

            if ($status_code == 'shipped') {
                $shipped = true;
            }

            $insert[] = array(
                'event_time' => $trk->event_time,
                'awb_number' => $shipment->awb_number,
                'created' => time(),
                'status_code' => $status_code
            );

            $data = array(
                "tracking_id" => $shipment->awb_number,
                "client_order_id" => $shipment->order_id,
                // "order_type" => "forward",
                "length" => $shipment->package_length, // courier captured length not what we sent
                "height" =>  $shipment->package_height, // courier captured height
                "breadth" =>  $shipment->package_breadth, // courier captured breadth
                "weight" =>  $shipment->package_weight / 1000, // courier captured weight
                "edd_stamp" => date('d-m-Y H:i', $shipment->edd_time), //latest edd stamp
                "status" => [
                    "received_by" => "",
                    "current_status_body" => $trk->message,
                    "current_status_type" => $status_code,
                    "current_status_location" => $trk->location,
                    "current_status_time" => date('d-m-Y H:i', $trk->event_time),
                ],

                "billing_zone" => $cred_zone[$shipment->zone]['zone']
            );

            //attach tracking details to this shipment

            $input = json_encode($data);

            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_HTTPHEADER, array("api-key: $api_key", "Content-Type: application/json"));
            // curl_setopt($ch, CURLOPT_URL, "https://enxi2s1mt8qvm.x.pipedream.net/");
            // curl_setopt($ch, CURLOPT_HTTPHEADER, array("Content-Type: application/json"));
            curl_setopt($ch, CURLOPT_POSTFIELDS, $input);
            curl_setopt($ch, CURLOPT_MAXREDIRS, 10);
            curl_setopt($ch, CURLOPT_TIMEOUT, 20);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
            curl_exec($ch);
            curl_close($ch);

            do_action('log.create', 'CRED Webhook', [
                'action' => 'webhook',
                'ref_id' => $shipment_id,
                'time' => date('Y-m-d H:i:s'),
                'data' => $data
            ]);
        }

        $this->CI->cred_lib->create($insert);
        return true;
    }
}
