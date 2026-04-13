<?php
function modifiedTrackingHistory($data = false, $courier = false)
{
    if(empty($data) || empty($courier))
        return false;

    $picked_date = !empty($data['picked_date']) ? $data['picked_date'] : '';
    $shipped_date = !empty($data['shipped_date']) ? $data['shipped_date'] : '';
    $total_ofd_attempts = !empty($data['total_ofd_attempts']) ? $data['total_ofd_attempts'] : '';
    $ofd_attempt_1_date = !empty($data['ofd_attempt_1_date']) ? $data['ofd_attempt_1_date'] : '';
    $last_attempt_date = !empty($data['last_attempt_date']) ? $data['last_attempt_date'] : '';
    $delivery_attempt_count = !empty($data['delivery_attempt_count']) ? $data['delivery_attempt_count'] : '';
    $delivered_time = !empty($data['delivered_time']) ? $data['delivered_time'] : '';

    $display_name = strtolower($courier->display_name);

    $shipment_date = '';
    $shipment_date_minus_one_day = '';
    $shipment_datetime = '';
    $shipment_hour = 0;

    if($shipped_date) {
        $shipment_date = date('Y-m-d', $shipped_date);
        $shipment_date_minus_one_day = date('Y-m-d', strtotime('-1 day', $shipped_date));
        $shipment_datetime = date('Y-m-d H:i:s', $shipped_date);
        $shipment_hour = (int) date('H', $shipped_date);
    }

    array_multisort(array_column($data['history'], 'event_time'), SORT_ASC, $data['history']);

    if(empty($picked_date) && !empty($shipped_date)) {
        if((23 > $shipment_hour) && ($shipment_hour < 6)) {
            $data['picked_date'] = strtotime(date('Y-m-d 08:00:00', strtotime($shipment_date_minus_one_day)));
        } else {
            $data['picked_date'] = $data['shipped_date'] - 3600;
        }

        if($data['picked_date'] <= $data['history'][0]['event_time']) {
            $data['picked_date'] = $data['shipped_date'] - 1;
        }

        $CI =& get_instance();
        $courier_status = $CI->config->item('cred_webhook_status');
        $courier_status = !empty($courier_status[$display_name]) ? $courier_status[$display_name] : '';

        if($key = array_search('picked', $courier_status)) {
            $data['history'][] = array(
                'event_time' => $data['picked_date'],
                'status_code' => $key,
                'location' => '',
                'message' => 'picked',
                'status' => $key,
                'ship_status' => 'pending pickup'
            );

            $data['first_pickup_attempt'] = $data['picked_date'];
            $data['last_pickup_attempt'] = $data['picked_date'];
            $data['pickup_attempt_count'] = 1;
        }
    }

    if(empty($ofd_attempt_1_date) && !empty($delivered_time)) {
        $data['total_ofd_attempts'] = 1;
        $data['ofd_attempt_1_date'] = $ofd_attempt_1_date = $data['delivered_time'] - 14400;
        $data['last_attempt_date'] = $data['delivered_time'] - 14400;

        if(empty($delivery_attempt_count)) {
            $delivery_attempt_count = $data['delivery_attempt_count'] = 1;
        }
    }

    if(!empty($ofd_attempt_1_date) && !empty($delivery_attempt_count) && ($delivery_attempt_count > 1)) {
        $ofd_attempt_date = $ofd_attempt_1_date;

        foreach ($data['history'] as $awb_history) {
            $event_time = $awb_history['event_time'];
            if($awb_history['ship_status'] == 'out for delivery') {
                $ofd_attempt_date = $event_time;
            }
            if($delivery_attempt_count && ($awb_history['ship_status'] == 'exception') && (empty($ofd_attempt_date) || ($event_time < $ofd_attempt_date))) {
                $ofd_attempt_date = '';
                $delivery_attempt_count = $delivery_attempt_count - 1;
            }
        }

        $data['delivery_attempt_count'] = $delivery_attempt_count;
    } else if(!empty($ofd_attempt_1_date) && !empty($delivery_attempt_count)) {
        $data['delivery_attempt_count'] = 1;
    } else {
        $data['delivery_attempt_count'] = 0;
    }

    if(empty($delivery_attempt_count) && !empty($delivered_time)) {
        $data['delivery_attempt_count'] = 1;
    }

    array_multisort(array_column($data['history'], 'event_time'), SORT_DESC, $data['history']);

    if (defined('print_tracking') && print_tracking == 'yes') {
        pr($data);
    }

    return $data;
}