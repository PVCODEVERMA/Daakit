<?php

defined('BASEPATH') or exit('No direct script access allowed');

require APPPATH . 'libraries/RestController.php';

class WebsiteTracking extends RestController
{
    public function __construct()
    {
        parent::__construct('rest_api');
    }


public function get_tracking_post()
{
    $input_json = $this->input->raw_input_stream;
    $data = json_decode($input_json, true);

    $awb_number = $data['awb_number'] ?? false;

    if (!$awb_number) {
        return $this->response([
            'status' => false,
            'message' => 'AWB number is required'
        ], 400);
    }

    
    $this->db->where('awb_number', $awb_number);
    $this->db->order_by('event_time', 'ASC');
    $query = $this->db->get('tbl_awb_tracking');
    $result = $query->result();

    if (empty($result)) {
        return $this->response([
            'status' => false,
            'message' => 'Order not found for this AWB number'
        ], 404);
    }

    
    $this->db->where('awb_number', $awb_number);
    $order = $this->db->get('tbl_order_shipping')->row();

    $order_placed_at = null;
    $courier_name = null;
    $order_no = null;

    if ($order) {

        
	    $order_placed_at = $order->created ? date('Y-m-d H:i', $order->created) : null;
	    $edd = $order->edd_time ? date('Y-m-d H:i', $order->edd_time) : null;
        $order_current_status = $order->ship_status ? $order->ship_status : null;
        
        if ($order->order_id) {
            $this->db->where('id', $order->order_id);
            $order_main = $this->db->get('tbl_orders')->row();

            if ($order_main) {
                $order_no = $order_main->order_no;
            }
        }

        if ($order->courier_id) {
            $this->db->where('id', $order->courier_id);
            $courier = $this->db->get('tbl_courier')->row();

            if ($courier) {
                $courier_name = $courier->display_name;
            }
        }
    }

    
    $tracking_history = [];

    foreach ($result as $trk) {
        $tracking_history[] = [
            'awb_number' => $trk->awb_number,
            'location'   => $trk->location,
            'message'    => $trk->message,
            'status'     => $trk->ship_status,
            'event_time' => date('Y-m-d H:i', $trk->event_time)
        ];
    }

    
    return $this->response([
        'status'          => true,
        'message'         => 'Tracking history fetched successfully',
        'awb'             => $awb_number,
        'order_no'        => $order_no,
        'order_placed_at' => $order_placed_at,
	'courier_name'    => $courier_name,
	'order_status'    => $order_current_status,
	'edd'             => $edd,
        'history'         => $tracking_history
    ], 200);
}



}
