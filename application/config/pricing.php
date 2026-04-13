<?php

defined('BASEPATH') or exit('No direct script access allowed');

$config['mrp_markup'] = array(
    'surface' => array(
        'percentage' => array(
            '500' => '200',
            'other' => '200'
        )
    ),
    'air' => array(
        'percentage' => array(
            '500' => '200',
            'other' => '200'
        )
    )
);

$config['cargo_gst_percentage'] = 18;

// hyperlocal charges in percentage
$config['hyperlocal_pricing_plans'] = array(
    '48'  => array(
        'landing' => array(
            'base_freight' => '60',
            'min_cod' => '25',
            'cod_percent' => '2.36',
            'base_add_distance_freight' => '12'
        ),
        'default' => array(
            'base_freight' => '75',
            'min_cod' => '38',
            'cod_percent' => '2.5',
            'base_add_distance_freight' => '19'
        ),
        'standard' => array(
            'base_freight' => '72',
            'min_cod' => '35',
            'cod_percent' => '2.47',
            'base_add_distance_freight' => '18'
        ),
        'enterprise' => array(
            'base_freight' => '67',
            'min_cod' => '32',
            'cod_percent' => '2.42',
            'base_add_distance_freight' => '17'
        ),
        'enterprise pro' => array(
            'base_freight' => '67',
            'min_cod' => '30',
            'cod_percent' => '2.4',
            'base_add_distance_freight' => '16'
        )
    ),
    '49'  => array(
        'landing' => array(
            'base_freight' => '60',
            'min_cod' => '25',
            'cod_percent' => '2.36',
            'base_add_distance_freight' => '12'
        ),
        'default' => array(
            'base_freight' => '75',
            'min_cod' => '38',
            'cod_percent' => '2.5',
            'base_add_distance_freight' => '19'
        ),
        'standard' => array(
            'base_freight' => '72',
            'min_cod' => '35',
            'cod_percent' => '2.47',
            'base_add_distance_freight' => '18'
        ),
        'enterprise' => array(
            'base_freight' => '67',
            'min_cod' => '32',
            'cod_percent' => '2.42',
            'base_add_distance_freight' => '17'
        ),
        'enterprise pro' => array(
            'base_freight' => '67',
            'min_cod' => '30',
            'cod_percent' => '2.4',
            'base_add_distance_freight' => '16'
        )
    ),
    '65'  => array(
        'landing' => array(
            'base_freight' => '60',
            'min_cod' => '25',
            'cod_percent' => '2.36',
            'base_add_distance_freight' => '12'
        ),
        'default' => array(
            'base_freight' => '75',
            'min_cod' => '38',
            'cod_percent' => '2.5',
            'base_add_distance_freight' => '19'
        ),
        'standard' => array(
            'base_freight' => '72',
            'min_cod' => '35',
            'cod_percent' => '2.47',
            'base_add_distance_freight' => '18'
        ),
        'enterprise' => array(
            'base_freight' => '67',
            'min_cod' => '32',
            'cod_percent' => '2.42',
            'base_add_distance_freight' => '17'
        ),
        'enterprise pro' => array(
            'base_freight' => '67',
            'min_cod' => '30',
            'cod_percent' => '2.4',
            'base_add_distance_freight' => '16'
        )
    )
);