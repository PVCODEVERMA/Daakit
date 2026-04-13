<?php

defined('BASEPATH') or exit('No direct script access allowed');

$config['cred_webhook_status'] = array(
    'xpressbees' => array(
        'DRC'    => 'shipment_created',
        'OFP'    => 'out_for_pickup',
        'PUD'    => 'picked',
        'PKD'    => 'shipped',
        'OFD'    => 'out_for_delivery',
        'UD'     => 'delivery_attempted',
        'LOST'   => 'lost',
        'RTON'   => 'rto_initiated',
    ),
    'delhivery' => array(
        'PP_DISPATCHED_X-DDD1FP' => 'out_for_pickup',
        'PP_DISPATCHED_X-DDD2FP' => 'out_for_pickup',
        'PP_DISPATCHED_X-DDD3FP' => 'out_for_pickup',
        'PP_DISPATCHED_X-DDD4FP' => 'out_for_pickup',
        'PU_IN_TRANSIT_EOD-77'   => 'picked',
        'PU_IN_TRANSIT_X-PRC'    => 'picked',
        'UD_IN_TRANSIT_X-PPOM'   => 'picked',
        'UD_IN_TRANSIT_X-PROM'   => 'picked',
        'UD_IN_TRANSIT_X-PIOM'   => 'shipped',
        /*'UD_DISPATCHED_ST-114'   => 'out_for_delivery',*/
        'UD_DISPATCHED_X-DDD3FD' => 'out_for_delivery',
        'RT_IN_TRANSIT_EOD-6O'   => 'rto_initiated',
    ),
    'ecomexpress' => array(
        '001'    => 'shipment_created',
        '014'    => 'out_for_pickup',
        '1230'   => 'out_for_pickup',
        '1260'   => 'picked',
        '0011'   => 'picked',
        '002'    => 'shipped',
        '006'    => 'out_for_delivery',
        '77'     => 'rto_initiated',
    ),
    'bluedart' => array(
        '501-PU-S'   => 'out_for_pickup',
        '015-UD-S'   => 'picked',
        '538-PU-T'   => 'picked',
        '001-UD-S'   => 'shipped',
        '074-RT-T'   => 'rto_initiated',
        '129-T'      => 'lost',
        '129-RT'     => 'lost'
    ),
    'smartr' => array(
        'BOOKED'            => 'shipment_created',
        'OUTFORPICKUP'      => 'out_for_pickup',
        'PICKED UP'         => 'picked',
        'ACCEPTED'          => 'shipped',
        'DELIVERY ATTEMPT'  => 'delivery_attempted',
        'RTO LOCKED'        => 'rto_initiated',
    ),
    'ekart' => array(
        'SHIPMENT_CREATED'  => 'shipment_created',
        'OUT_FOR_PICKUP'    => 'out_for_pickup',
        'PICKUP_COMPLETE'   => 'picked',
    ),
    'dtdc' => array(
        'SPL'               => 'shipment_created',
        'PCSC'              => 'out_for_pickup',
        'PCUP'              => 'picked',
        'OBMN'              => 'shipped',
        'OUTDLV'            => 'out_for_delivery',
        'NONDLV'            => 'delivery_attempted',
        'RTO'               => 'rto_initiated',
    ),
    'kerry indev' => array(
        'OFP'               => 'out_for_pickup',
        'PUD'               => 'picked',
        'SAO'               => 'shipped',
        'RTA'               => 'rto_initiated',
    )
);

$config['delta_webhook_status'] = array(
    'pending pickup'        => 'shipment_created',
    'in transit'            => 'in_transit',
    'out for delivery'      => 'out_for_delivery',
    'delivered'             => 'delivered',
    'exception'             => 'delivery_attempted',
    'lost'                  => 'lost',
    'rto in transit'        => 'rto_in_transit',
    'rto delivered'         => 'rto_delivered',
);

$config['nimb_webhook_status'] = array(
    'bluedart' => array(
        '015-UD-S'   => 'pkd',
        '538-PU-T'   => 'pkd',
        '001-UD-S'   => 'spd',
    ),
    'smartship' => array(
        '10'  => 'spd',
    )
);