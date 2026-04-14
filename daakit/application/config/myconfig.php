<?php
defined('BASEPATH') or exit('No direct script access allowed');

if (empty($this->config['assets_base']))
    $config['assets_base'] = $this->config['base_url'];

$config['jwt_key'] = '7dWaR8NqhipZqUqFjA6XuiHYOUbytRLdThL7OPFUoN5Dhv3erDGL0nI6/ddQ/2ZsAiuEk8h21Xe5s+nE3Kb4CSQ==';

$config['bulk_awb_limit'] = '500';

$config['bulkawb_limit'] = '100';

$config['weight_dispute_time_limit'] = 60 * 60 * 24 * 7; //7 days

$config['password_expired'] = '90'; //90 days



//email configuration

// Delhivery API Keys
$config['courier_holiday_list'] = array(
    '2020-03-10',
    '2020-10-02'
);



$config['smtp_host'] = '';
$config['smtp_port'] = '';
$config['smtp_user'] = '';
$config['smtp_pass'] = '';

$config['from_email'] = 'care@daakit.com';
$config['from_name'] = 'daakit';

$config['permissions'] = array(
    'orders',
    'shipments',
    'weight',
    'ndr',
    'billing',
    'tools',
    'apps',
    'settings',
    'employees',
    'reports',
    'refers',
    'abandoned_checkouts',
    'escalations'
);


$config['courier_ndr_email'] = array(
    'delhivery' => array(
    ),
    'fedex' => array(
    ),
    'bluedart' => array(
    ),
    'bluedart express' => array(
    ),
    'dtdc' => array(
    ),
    'ekart' => array(
    ),
    'xpressbees' => array(
    ),
    'shadowfax' => array(
    ),
    'ecom' => array(
    ),
    'gati' => array(
    )
);



$config['courier_ndr_email_cc'] = array(
    'ops@daakit.com',
);




$config['courier_ndr_from_email'] = 'ops@daakit.com';


$config['trello_key'] = '';
$config['trello_secret'] = '';
$config['trello_list_id'] = '';

$config['leadsquad_api_key'] = '';
$config['leadsquad_api_secret'] = '';




$config['invoice_email_bcc'] = array(
    'accounts@daakit.com',
    'finance@daakit.com',
);


$config['exotel_api_key'] = '';
$config['exotel_api_token'] = '';
$config['exotel_api_sid'] = '';
$config['exotel_callerid'] = '';
$config['exotel_admin_callerid'] = '';
$config['exotel_callerid_cod'] = '';

$config['esc_billing_tat'] = 60 * 60 * 24 * 3; //3 days
$config['esc_pickup_tat'] = 60 * 60 * 24 * 1; //1 days 
$config['esc_weight_tat'] = 60 * 60 * 24 * 14; //14 days 
$config['esc_tech_tat'] = 60 * 60 * 24 * 1; //1 days 
$config['esc_rcb_tat'] = 60 * 30; //30 minutes
$config['esc_shipment_re_attempt'] = 60 * 60 * 24 * 3; //3 days 
$config['esc_shipment_re_attempt_fr'] = 60 * 60 * 24 * 2; //2 days 
$config['esc_shipment_urgent_delivery'] = 60 * 60 * 24 * 3; //3 days 
$config['esc_shipment_rto_instructions'] = 60 * 60 * 24 * 1; //1 days 
$config['esc_shipment_stuck_shipment'] = 60 * 60 * 24 * 4; //4 days
$config['esc_shipment_status_mismatch'] = 60 * 60 * 24 * 2; //2 days
$config['esc_shipment_hold_shipment'] = 60 * 60 * 24 * 3; //3 days
$config['esc_shipment_charges_reversal'] = 60 * 60 * 24 * 3; //3 days
$config['esc_shipment_lost_damanged'] = 60 * 60 * 24 * 10; //10 days
$config['esc_shipment_self_collect'] = 60 * 60 * 24 * 1; //1 days
$config['esc_shipment_change_payment_type'] = 60 * 60 * 24 * 1; //1 days
$config['esc_shipment_proof_of_delivery'] = 60 * 60 * 24 * 3; //3 days
$config['esc_shipment_others'] = 60 * 60 * 24 * 3; //3 days 

$config['aws_access_key'] = '';
$config['aws_secret_key'] = '';


$config['aws_log_group_name'] = 'daakit';


$config['shipment_in_transit_status'] = array(
    'in transit',
    'out for delivery',
    'exception'
);

$config['shipment_closed_status'] = array(
    'delivered',
    'lost',
    'damaged',
    'rto'
);

$config['gst_state_codes'] = array(
    '01' => 'jammu & kashmir',
    '02' => 'himachal pradesh',
    '03' => 'punjab',
    '04' => 'chandigarh',
    '05' => 'uttarakhand',
    '06' => 'haryana',
    '07' => 'delhi',
    '08' => 'rajasthan',
    '09' => 'uttar pradesh',
    '10' => 'bihar',
    '11' => 'sikkim',
    '12' => 'arunachal pradesh',
    '13' => 'nagaland',
    '14' => 'manipur',
    '15' => 'mizoram',
    '16' => 'tripura',
    '17' => 'meghalaya',
    '18' => 'assam',
    '19' => 'west bengal',
    '20' => 'jharkhand',
    '21' => 'odisha',
    '22' => 'chhattisgarh',
    '23' => 'madhya Pradesh',
    '24' => 'gujarat',
    '25' => 'daman & diu',
    '26' => 'dadra & nagar haveli & daman & diu',
    '27' => 'maharashtra',
    '29' => 'karnataka',
    '30' => 'goa',
    '31' => 'lakshdweep',
    '32' => 'kerala',
    '33' => 'tamil nadu',
    '34' => 'puducherry',
    '35' => 'andaman & nicobar islands',
    '36' => 'telangana',
    '37' => 'andhra pradesh',
    '38' => 'ladakh',
    '96' => 'foreign country',
    '97' => 'other territory'
);


$config['failed_webhook_numbers'] = '50'; //after this webhook will get disabled


$config['auto_assign_escalation_to_poc'] = array(
    '5' => '3901', 
    '12' => '3901',
    '24' => '3901',
    '76' => '3901',
    '77' => '3901',
);

$config['delta_sales_crm_api_key'] = '';

$config['seller_report_send_to_email'] = array(
);

$config['send_weekly_review_report'] = array(
);

$config['seller_report_send_cc_email'] = array(
);

$config['cod_outstanding_summary_send'] = array(
);

$config['cod_outstanding_summary_send_cc'] = array(
);

$config['wallet_report_send_cc_email'] = array(
);

$config['ofd_attempt_limit'] = 3;

$config['state_codes'] = array('01' => 'jammu & kashmir', '02' => 'himachal pradesh', '03' => 'punjab', '04' => 'chandigarh', '05' => 'uttarakhand', '06' => 'haryana', '07' => 'delhi', '08' => 'rajasthan', '09' => 'uttar pradesh', '10' => 'bihar', '11' => 'sikkim', '12' => 'arunachal pradesh', '13' => 'nagaland', '14' => 'manipur', '15' => 'mizoram', '16' => 'tripura', '17' => 'meghalaya', '18' => 'assam', '19' => 'west bengal', '20' => 'jharkhand', '21' => 'odisha', '22' => 'chhattisgarh', '23' => 'madhya Pradesh', '24' => 'gujarat', '25' => 'daman & diu', '26' => 'dadra & nagar haveli', '27' => 'maharashtra', '29' => 'karnataka',    '30' => 'goa', '31' => 'lakshdweep', '32' => 'kerala', '33' => 'tamil nadu', '34' => 'puducherry', '35' => 'andaman & nicobar islands', '36' => 'telangana', '37' => 'andhra pradesh', '38' => 'ladakh');


// Courier Estimate Delivery Date in Days (Zone wise)
$config['courier_edd_days'] = array(
    'z1'    => array(
        'air'       => 2,
        'surface'   => 2
    ),
    'z2'    => array(
        'air'       => 4,
        'surface'   => 4
    ),
    'z3'    => array(
        'air'       => 4,
        'surface'   => 6
    ),
    'z4'    => array(
        'air'       => 5,
        'surface'   => 8
    ),
    'z5'    => array(
        'air'       => 7,
        'surface'   => 10
    )
);

$config['without_kyc_order_limit'] = 0;
$config['exotel_new_number_cost'] = 590;

$config['zone_price_division'] = '1.18';

$config['exotel_call_rate'] = 0.60; //per minute call rate of exotel
$config["free_unit"] = 0;

$config['coupon_type'] = array(
    'discount' => 'Percentage',
    'fixed' => 'Fixed amount',
    'extra credit' => 'Extra credit',
);
$config['user_type'] = array(
    '0' => 'All',
    '1'  => 'First Recharge',
    '2' => 'Specific Seller',
);


$config['seller_categories'] = array(
    'Funded Brand',
    'Brand',
    'Aggregator',
    'Liquidation',
    'Viral',
    'Amazon Seller',
    'delta Test Account',
    'Other'
);

$config['seller_clusters'] = array(
    'north' => array(
        'gurgaon' =>  'Gurgaon  [ Gurgaon - Faridabad ]',
        'east delhi' => 'East Delhi [ Noida - Okhla ]',
        'south delhi' => 'South Delhi',
        'west delhi' => 'West Delhi',
        'chandigarh'  => 'Chandigarh',
        'agra' => 'Agra',
        'lukhnow' => 'Lukhnow',
        'ludhiana' => 'Ludhiana',
        'north-other' => 'Others'
    ),
    'south' => array(
        'bangalore' => 'Bangalore',
        'chennai' => 'Chennai',
        'hyderabad' => 'Hyderabad',
        'south-other' => 'Others'
    ),
    'west' => array(
        'surat' => 'Surat',
        'mumbai' => 'Mumbai',
        'pune' => 'Pune',
        'west-other' => 'Others'
    ),
    'east' => array(
        'kolkota' => 'Kolkota',
        'east-other' => 'Others'
    )
);


/* User Load Bucket For KAM Report */
$config['user_load_bucket'] = array(
    '1-100' => array(1, 100),
    '100-250' => array(101, 250),
    '250-500' => array(251, 500),
    '500-1000' => array(501, 1000),
    '1000+' => array(1001)
);
//***************************Payment Gateway details start**************************/ by Deep Rana
$config['payment_gateway'] = array(
    0 => ['name' => 'easebuzz', 'status' => 1, 'features' => 'UPI / Net Banking  <br> Credit & Debit Cards'], //1:Active 0:disable
    1 => ['name' => 'easebuzz', 'status' => 1, 'features' => 'UPI / Net Banking  <br> Credit & Debit Cards'], //1:Active 0:disable
);
$config['easebuzz_key'] = '3UDGABY1AP';
$config['easebuzz_access_key'] = 'V1JSHHXSO5';

//***************************Payment Gateway details end**************************/ by Deep Rana

$financial_year_to = (date('m') > 3) ? date('y') + 1 : date('y');
$financial_year_from = $financial_year_to - 1;
$config['financial_year'] = $financial_year_from . $financial_year_to;
$config['inv_prefix'] = 'DKT/';
$config['inv_prefix_credit'] = 'DKT/CN/';


$config['whatsapp_charges'] = [
    'order_confirm' => '1.18',
    'custom_order_confirm' => '1.18',
    'shipment' => '2.36',
    'ndr' => '2.36',
    'abandoned' => '1.18'
];


$config['exotel_cycle'] = 30;
$config['exotel_recurring_charges'] = 590;


//***************************Courier credential start here**************************/ by Deep Rana
$config['delhivery_api_key']='33c06c884ad508c112743618c7df789265aa325c';
$config['delhivery_test_mode']='1';
$config['delhivery_air_api_key'] = '40e5339ea28568a87403fe1bd4e3d6bab3b77b39';
$config['delhivery_surface_api_key'] = '40e5339ea28568a87403fe1bd4e3d6bab3b77b39';
$config['delhivery_surface_2kg_api_key'] = 'e3fa871029c440bcaa8b63650785e5ef97c8f5c2';
$config['delhivery_surface_5kg_api_key'] = 'e0de851ebaa01f0a71e755a6e74793239a6236dd';
$config['delhivery_surface_10_api_key'] = '64e266f2c14c2ed929cc52fd7449bef200b6f525';
//***************************Courier credential end here**************************/ by Deep Rana

// Xpressbees Surface
$config['xpressbees_api_key_surface'] = 'Pnduw36891Yvduq';
$config['xpressbees_secretkey_surface'] = '2a9167659d7db76e8f701bd2f9e2b7a5a9c47f5be40362902860fc37b85f9560';
$config['xpressbees_business_account_name_surface'] = 'Daakit';
$config['xpressbees_username_surface'] = 'admin@dktsml.com';
$config['xpressbees_password_surface'] = '$dktsml$';

// Xpressbees 2 kg Heavies
$config['xpressbees_api_key_2_kg'] = 'Tviws37005Ecdup';
$config['xpressbees_secretkey_2_kg'] = '4977c42d0477dcd5e6510c82050fa731f841f618436e895383255396e07f6626';
$config['xpressbees_business_account_name_2_kg'] = 'Daakit 2 Kg';
$config['xpressbees_username_2_kg'] = 'admin@dktsml2kg.com';
$config['xpressbees_password_2_kg'] = '$dktsml2kg$';

// Xpressbees 5 kg Heavies
$config['xpressbees_api_key_5_kg'] = 'Tviws37006Ecdup';
$config['xpressbees_secretkey_5_kg'] = 'faa3570b19caefdb8b4ee1550b3523b98689ee70393881f0d68946a626ae696f';
$config['xpressbees_business_account_name_5_kg'] = 'Daakit 5 Kg';
$config['xpressbees_username_5_kg'] = 'admin@dktsml5kg.com';
$config['xpressbees_password_5_kg'] = '$dktsml5kg$';

// Xpressbees 10 kg Heavies
$config['xpressbees_api_key_10_kg'] = 'Tviws37007Ecdup';
$config['xpressbees_secretkey_10_kg'] = 'c9c1707b984cf79395e8fed5f8ffcc8675218239ee0525394ad21a1289a7e544';
$config['xpressbees_business_account_name_10_kg'] = 'Daakit 10 Kg';
$config['xpressbees_username_10_kg'] = 'admin@dktsml10kg.com';
$config['xpressbees_password_10_kg'] = '$dktsml10kg$';

// Xpressbees 20 kg Heavies
$config['xpressbees_api_key_20_kg'] = 'Tviws37008Ecdup';
$config['xpressbees_secretkey_20_kg'] = 'f7f2260835c5e98b79fb9b5b0e6171fb3ff6935ade9be5cf808da1f684edfcff';
$config['xpressbees_business_account_name_20_kg'] = 'Daakit 20 Kg';
$config['xpressbees_username_20_kg'] = 'admin@dktsml20kg.com';
$config['xpressbees_password_20_kg'] = '$dktsml20kg$';



