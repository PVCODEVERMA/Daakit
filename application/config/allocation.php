<?php

defined('BASEPATH') or exit('No direct script access allowed');

$config['default_allocation_rules'] = array(
    array(
        'id' => '1',
        'filter_name' => 'COD and Prepaid',
        'filter_type' => 'or',
        'conditions' => array(
            array(
                'field' => 'payment_type',
                'condition' => 'is',
                'value' => 'cod'
            ),
            array(
                'field' => 'payment_type',
                'condition' => 'is',
                'value' => 'prepaid'
            )
        ),
        'priority' => '1',
        'status' => '1',
        'courier_priority_1' => '24',
        'courier_priority_2' => '5',
        'courier_priority_3' => '3',
        'courier_priority_4' => '4',
        'courier_priority_5' => '1',
        'courier_priority_6' => '8',
        'courier_priority_7' => '15',
        'courier_priority_8' => '14',
        'courier_priority_9' => '6',
        'courier_priority_10' => '9',
    ),
    array(
        'id' => '1',
        'filter_name' => 'Reverse Shipment',
        'filter_type' => 'or',
        'conditions' => array(
            array(
                'field' => 'payment_type',
                'condition' => 'is',
                'value' => 'reverse'
            )
        ),
        'priority' => '1',
        'status' => '1',
        'courier_priority_1' => '1',
        'courier_priority_2' => '4',
    )
);



$config['smart_price_allocation_rules'] = array(
    array(
        'filter_name' => '',
        'courier_priority_1' => '24',
        'courier_priority_2' => '5',
        'courier_priority_3' => '3',
        'courier_priority_4' => '4'
    )
);

