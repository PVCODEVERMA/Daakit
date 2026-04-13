<?php

defined('BASEPATH') or exit('No direct script access allowed');

require APPPATH . 'libraries/RestController.php';

class Test extends RestController
{
    public function __construct()
    {
        parent::__construct('rest_api');
        $this->load->library('notification_lib');
        $this->load->library('shipping_lib');
    }


    public function send_notification_post()
{
    $input_json = $this->input->raw_input_stream;
    $data = json_decode($input_json, true);

    $courier_id     = $data['courier_id']     ?? false;
    $ignore_limits  = $data['ignore_limits']  ?? false;

    // Load library
    $this->load->library('shipping_lib');

    // Build filter
    $filter = array(
        'status_in' => array(

            'in transit',

        )
    );

    if ($ignore_limits) {
        $filter['before_last_tracking'] = '';
    }

    if ($courier_id) {
        $filter['courier_in'] = array($courier_id);
    }

    // Get shipments
    $shipments = $this->shipping_lib->tracableOrders($filter);

    if (empty($shipments)) {
        return $this->response([
            'status' => false,
            'message' => 'No shipments found'
        ], 404);
    }

    // Track each shipment
    $tracking_results = [];
    foreach ($shipments as $shipment) {
        do_action('shipping.track', $shipment->id);
        $tracking_results[] = $shipment->id;
    }

    // Send final response
    return $this->response([
        'status'        => true,
        'message'       => 'Tracking executed successfully',
        'count'         => count($tracking_results),
        'shipment_ids'  => $tracking_results
    ], 200);
    }
}
