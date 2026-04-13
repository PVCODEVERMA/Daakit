<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Scheduler extends MY_controller
{
    public function __construct()
    {
        parent::__construct();
    }

    function get_history_1($courier_id = false, $ignore_limits = false)
    { 
        $this->load->library('shipping_lib');
        $filter = array(
            'status_in' => array(
                'in transit',
                'out for delivery',
                'exception'
            ),
            // 'before_last_tracking' => strtotime('-3 hours')
        );
        if ($ignore_limits)
            $filter['before_last_tracking'] = '';

        if ($courier_id)
            $filter['courier_in'] = array($courier_id);
        $shipments = $this->shipping_lib->tracableOrders($filter);
        if (!empty($shipments)) {
            echo count($shipments);
            foreach ($shipments as $shipment) {
                do_action('shipping.track', $shipment->id);
            }
        }
    }

    function get_all_history_1($courier_id = false, $ignore_limits = false)
    { // tracking history of all shipments 
        $this->load->library('shipping_lib');
        $filter = array(
            'status_in' => array(
                'booked',
                'pending pickup',
                'out for delivery',
                'exception'
            ),
            // 'before_last_tracking' => strtotime('-3 hours')
        );

        if ($ignore_limits)
            $filter['before_last_tracking'] = '';

        if ($courier_id)
            $filter['courier_in'] = array($courier_id);

        $shipments = $this->shipping_lib->tracableOrders($filter);
        if (!empty($shipments)) {
            echo count($shipments);

            foreach ($shipments as $shipment) {
                do_action('shipping.track', $shipment->id);
            }
        }
    }

       function get_in_transit_history($courier_id = false, $ignore_limits = false)
{
    $this->load->library('shipping_lib');

    $filter = array(
        'status_in' => array(
            'in transit'
        ),
    );

    if ($ignore_limits) {
        $filter['before_last_tracking'] = '';
    }

    if ($courier_id) {
        $filter['courier_in'] = array($courier_id);
    }

    $shipments = $this->shipping_lib->tracableOrders($filter);

    if (!empty($shipments)) {
        echo count($shipments);

        foreach ($shipments as $shipment) {
            do_action('shipping.track', $shipment->id);
        }
    }
}

    function get_rto_tracking($courier_id = false, $ignore_limits = false)
    { //only for couriers where rto details are in same awb and no separate awb is assigned
        $this->load->library('shipping_lib');
        $filter = array(
            'status_in' => array(
                'rto'
            ),
            'rto_status_in' => array(
                'in transit'
            ),
            'before_last_tracking' => strtotime('-3 hours')
        );

        if ($ignore_limits)
            $filter['before_last_tracking'] = '';

        if ($courier_id)
            $filter['courier_in'] = array($courier_id);

        $shipments = $this->shipping_lib->tracableOrders($filter);
        if (!empty($shipments)) {
            echo count($shipments);
            foreach ($shipments as $shipment) {
                do_action('shipping.track_rto', $shipment->id);
            }
        }
    }

       function club_wallet_history($date = null)
{
    $this->load->library('shipping_lib');
    $this->shipping_lib->club_wallet_history($date);
}

}
