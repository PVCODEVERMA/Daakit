<?php

defined('BASEPATH') or exit('No direct script access allowed');

require APPPATH . 'libraries/RestController.php';

class Daakitgo_webhook extends RestController
{
    public function __construct()
    {
        parent::__construct('rest_api');

        $this->load->library('tracking_lib');
        $this->load->library('shipping_lib');
        $this->load->model('shipping_model');
    }

    /**
     * Daakit Go pushes status update here.
     * POST JSON: { "awb_number": "...", "status": "...", "message": "..." }
     */
    public function update_status_post()
    {
        $json = $this->input->raw_input_stream;
        $data = json_decode($json, true);

        if (empty($data['awb_number']) || empty($data['status'])) {
            return $this->response([
                'status' => false,
                'message' => 'AWB number and status are required.'
            ], 400);
        }

        $awb_number = trim($data['awb_number']);
        $status = strtolower(trim($data['status']));
        $message = !empty($data['message']) ? trim($data['message']) : 'Status updated to ' . ucfirst($status);
        $now = !empty($data['event_time']) && is_numeric($data['event_time'])
        ? (int)$data['event_time']
        : time();
        
	$edd_time = !empty($data['edd']) && is_numeric($data['edd'])
    ? (int)$data['edd']
    : null;
        // Get shipment
        $shipment = $this->db->get_where('tbl_order_shipping', ['awb_number' => $awb_number])->row();

        if (!$shipment) {
            return $this->response([
                'status' => false,
                'message' => 'AWB not found.'
            ], 404);
        }

        $ship_status = '';
        $tracking_ship_status = '';
        $tracking_status = '';
        $rto_status = '';
        $fulfillment_status = null;

        switch ($status) {
            case 'cancelled':
                $ship_status = 'cancelled';
                $tracking_ship_status = 'cancelled';
                $tracking_status = 'cancelled';
                $fulfillment_status = 'new';
                break;

            case 'pending pickup':
                $ship_status = 'pending pickup';
                $tracking_ship_status = 'pending pickup';
                $tracking_status = 'pending pickup';
                break;

            case 'in transit':
                $ship_status = 'in transit';
                $tracking_ship_status = 'in transit';
                $tracking_status = 'in transit';
                break;

            case 'picked':
                $ship_status = 'in transit';
                $tracking_ship_status = 'in transit';
                $tracking_status = 'in transit';
                break;

            case 'out for delivery':
                $ship_status = 'out for delivery';
                $tracking_ship_status = 'out for delivery';
                $tracking_status = 'out for delivery';
                break;

            case 'delivered':
                $ship_status = 'delivered';
                $tracking_ship_status = 'delivered';
                $tracking_status = 'delivered';
                break;

            case 'ndr':
                $ship_status = 'exception';
                $tracking_ship_status = 'exception';
                $tracking_status = 'exception';
                break;

            case 'rto in transit':
                $ship_status = 'rto';
                $rto_status = 'in transit';
                $tracking_ship_status = 'rto in transit';
                $tracking_status = 'rto';
                break;

            case 'rto delivered':
                $ship_status = 'rto';
                $rto_status = 'delivered';
                $tracking_ship_status = 'rto delivered';
                $tracking_status = 'rto';
                break;

            default:
                return $this->response([
                    'status' => false,
                    'message' => 'Invalid status.'
                ], 400);
	}

	$statusCode = strtoupper(str_replace(' ', '_', $status));
        
	$latestTracking = $this->db
        ->select('event_time')
        ->from('tbl_awb_tracking')
	->where('awb_number', $awb_number)
        ->order_by('event_time', 'DESC')
        ->limit(1)
        ->get()
        ->row();

        $latestEventTime = $latestTracking ? (int)$latestTracking->event_time : 0;
        $isLatestEvent = ($now > $latestEventTime);

        
        $tracking_event = [
            'awb_number' => $awb_number,
            'event_time' => $now,
            'status_code' => $statusCode,
            'location' => '',
            'message' => $message,
            'status' => $tracking_status,
            'ship_status' => $tracking_ship_status,
            'rto_awb' => '',
        ];
        
	$duplicateTracking = $this->db
            ->select('id')
            ->from('tbl_awb_tracking')
            ->where([
                'awb_number' => $awb_number,
                'event_time' => $now,
                'status_code' => $statusCode
            ])
            ->limit(1)
            ->get()
            ->row();
        if ($duplicateTracking) {
            return $this->response([
                'status' => true,
                'message' => 'Duplicate tracking event ignored',
                'ship_status' => $shipment->ship_status,
                'order_id' => $shipment->order_id
            ], 200);
        }
        $this->tracking_lib->batchInsert([$tracking_event]);

        // Build shipment tracking updates
        $save_shipment_tracking = [
            'shipment_id' => $shipment->id,
        ];

        if ($status === 'delivered') {
            $save_shipment_tracking['delivered_time'] = $now;
	}

	if ($status === 'picked') {
	    $save_shipment_tracking['pickup_time'] = $now;
	    if (!empty($edd_time)) {
        $save_shipment_tracking['edd_time'] = $edd_time;
    }
        }

        if ($status === 'rto in transit') {
            $save_shipment_tracking['rto_mark_date'] = $now;
        }

        if ($status === 'rto delivered') {
            $save_shipment_tracking['rto_delivered_date'] = $now;
        }

        if ($status === 'ndr') {
            $save_shipment_tracking['last_ndr_reason'] = $message;
            $save_shipment_tracking['last_ndr_date'] = $now;
        }

	if ($status === 'out for delivery' && $isLatestEvent) {

            $shipmentTracking = $this->db
                ->get_where('tbl_shipment_tracking', ['shipment_id' => $shipment->id])
                ->row();

            if ($shipmentTracking) {

                // Only allow OFD when shipment was previously in transit or exception
                if (in_array($shipment->ship_status, ['in transit', 'exception'])) {

                    // Increment total_ofd_attempts safely
                    $save_shipment_tracking['total_ofd_attempts'] =
                        (int)$shipmentTracking->total_ofd_attempts + 1;

                    // OFD Attempt 1 (ONLY from in transit)
                    if (
                        empty($shipmentTracking->ofd_attempt_1_date) &&
                        $shipment->ship_status === 'in transit'
                    ) {
                        $save_shipment_tracking['ofd_attempt_1_date'] = $now;
                    }

                    // OFD Attempt 2 (ONLY after OFD 1 + exception)
                    else if (
                        !empty($shipmentTracking->ofd_attempt_1_date) &&
                        empty($shipmentTracking->ofd_attempt_2_date) &&
                        $shipment->ship_status === 'exception'
                    ) {
                        $save_shipment_tracking['ofd_attempt_2_date'] = $now;
                    }

                    // OFD Attempt 3 (ONLY after OFD 2 + exception)
                    else if (
                        !empty($shipmentTracking->ofd_attempt_2_date) &&
                        empty($shipmentTracking->ofd_attempt_3_date) &&
                        $shipment->ship_status === 'exception'
                    ) {
                        $save_shipment_tracking['ofd_attempt_3_date'] = $now;
                    }
                }
            }
	}
	if ($status === 'in transit' && $isLatestEvent) {

            $shipmentTracking = $this->db
                ->get_where('tbl_shipment_tracking', ['shipment_id' => $shipment->id])
                ->row();

            // pickup_time is INT NOT NULL, so check explicitly for 0
            if ($shipmentTracking && (int)$shipmentTracking->pickup_time === 0) {

                // Find earliest picked event if it exists
                $pickedTracking = $this->db
                    ->select('event_time')
                    ->from('tbl_awb_tracking')
                    ->where('awb_number', $awb_number)
                    ->where('status_code', 'PICKED')
                    ->order_by('event_time', 'ASC')
                    ->limit(1)
                    ->get()
                    ->row();

                $pickupTimeToSet = $pickedTracking
                    ? (int)$pickedTracking->event_time
                    : $now; // fallback to in-transit time

                $save_shipment_tracking['pickup_time'] = $pickupTimeToSet;
            }
        }
	// Update shipment tracking
	if ($isLatestEvent) {
        $this->tracking_lib->createUpdateShipmentTracking($shipment->id, $save_shipment_tracking);
        }
	// Update main shipping table
	if ($isLatestEvent) {
        $update = [
            'ship_status' => $ship_status,
            'modified' => $now,
            'status_updated_at' => $now,
            'rto_status' => $rto_status,
	];

	if ($status === 'picked') {
            // set only once (optional safety)
            $update['pickup_time'] = $now;
	}

	if ($status === 'delivered') {
            $update['delivered_time'] = $now;
        }

        // if ($status === 'ndr') {
        //     $update['last_ndr_reason'] = $message;
        //     $update['last_ndr_date'] = $now;
        // }

        // if ($status === 'rto in transit') {
        //     $update['rto_mark_date'] = $now;
        // }

        // if ($status === 'rto delivered') {
        //     $update['rto_delivered_date'] = $now;
        // }

        $this->db->where('awb_number', $awb_number)->update('tbl_order_shipping', $update);
         }
        // Update orders table if needed
        if ($isLatestEvent && !empty($fulfillment_status)) {
            $this->db->where('id', $shipment->order_id)
                     ->update('tbl_orders', ['fulfillment_status' => $fulfillment_status]);
        }

	// Fire hook same as getTrackingHistoryLive
	if ($isLatestEvent) {
        do_action('shipping.status', $shipment->id, $tracking_event);
        }
        return $this->response([
            'status' => true,
            'message' => 'Status updated',
            'ship_status' => $ship_status,
            'order_id' => $shipment->order_id
        ], 200);
    }
}
