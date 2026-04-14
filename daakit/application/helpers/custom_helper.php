<?php

function pr($data = false, $exit = false)
{
    echo '<pre>';
    print_r($data);
    if ($exit)
        exit;
}

function isValidPhone($phone = false)
{
    if (!$phone || !is_numeric($phone))
        return false;

    if (strlen($phone) != 10)
        return false;

    return true;
}

function isValidZip($zip = false)
{
    if (!$zip || !is_numeric($zip))
        return false;

    if (strlen($zip) != 6)
        return false;

    return true;
}

function thousandsFormat($num)
{
    if ($num > 1000) {

        $x = round($num);
        $x_number_format = number_format($x);
        $x_array = explode(',', $x_number_format);
        $x_parts = array('k', 'm', 'b', 't');
        $x_count_parts = count($x_array) - 1;
        $x_display = $x;
        $x_display = $x_array[0] . ((int) $x_array[1][0] !== 0 ? '.' . $x_array[1][0] : '');
        $x_display .= $x_parts[$x_count_parts - 1];

        return $x_display;
    }

    return $num;
}

function time_left_for_weight_dispute($date = false)
{
    if (!$date)
        return false;

    $seconds = strtotime($date . ' 23:59:59') - time();

    $days = floor($seconds / 86400);
    if ($days > 0)
        return $days . ' day(s)';

    $seconds %= 86400;

    $hours = floor($seconds / 3600);
    if ($hours > 0)
        return  '0 day(s)';
        //return $hours . ' hour(s)';

    $seconds %= 3600;

    $minutes = floor($seconds / 60);
    if ($minutes > 0)
       // return $minutes . ' minute(s)';
        return   '0 day(s)';

    return 'No time';
}

function get_user_bucket($shipments = false) {
    if(empty($shipments) || !is_numeric($shipments)) {
        return 0;
    }

    $CI =& get_instance();
    $user_load_bucket = $CI->config->item('user_load_bucket');

    foreach ($user_load_bucket as $key => $load_bucket) {
        if(!empty($load_bucket[0]) && !empty($load_bucket[1]) && $shipments >= $load_bucket[0] && $shipments <= $load_bucket[1]) {
            return $key;
        } else if(!empty($load_bucket[0]) && empty($load_bucket[1]) && $shipments >= $load_bucket[0]) {
            return $key;
        }
    }

    return 0;
}

function distanceBetweenLatLng($origin, $destination) {
    if(empty($origin['lat']) || empty($origin['lng']) || empty($destination['lat']) || empty($destination['lng']))
        return false;

    $lat1 = $origin['lat'];
    $lng1 = $origin['lng'];
    $lat2 = $destination['lat'];
    $lng2 = $destination['lng'];
    $R = 6371e3;
    $φ1 = deg2rad($lat1);
    $φ2 = deg2rad($lat2);
    $Δφ = deg2rad($lat2 - $lat1);
    $Δλ = deg2rad($lng2 - $lng1);
    $a = sin($Δφ / 2) * sin($Δφ / 2) + cos($φ1) * cos($φ2) * sin($Δλ / 2) * sin($Δλ / 2);
    $c = 2 * atan2(sqrt($a), sqrt(1 - $a));
    $d = $R * $c;
    if($d) { $d = ceil($d / 1000); }

    //$d = acos(sin($φ1) * sin($φ2) + cos($φ1) * cos($φ2) * cos($Δλ)) * $R;
    return $d;
}

function remove_special_charcater($str)
{
    if(empty($str))
        return false;

    return str_ireplace(array('\'','"',',',';','<','>','$','&','.','-','*','(', ')','#','%','!','@','×','’','–','·','—','‘','“','”'), '', $str);
}

function url_title_address($str, $separator = '-', $lowercase = FALSE)
{
	if ($separator === 'dash') {
		$separator = '-';
	} elseif ($separator === 'underscore') {
		$separator = '_';
	}

	$q_separator = preg_quote($separator, '#');

	$trans = array(
		'&.+?;'			=> '',
		','			    => ' ',
		'[^\w\d _-]'	=> '',
		'\s+'			=> $separator,
		'('.$q_separator.')+'	=> $separator
	);

	$str = strip_tags($str);
	foreach ($trans as $key => $val) {
		$str = preg_replace('#'.$key.'#i'.(UTF8_ENABLED ? 'u' : ''), $val, $str);
	}

	if ($lowercase === TRUE) {
		$str = strtolower($str);
	}

	return trim(trim($str, $separator));
}

function get_label($awb_number = false, $courier_id = false)
{
    if(empty($awb_number) || empty($courier_id))
        return false;
    
    $url = 'https://nimubs-assets.s3.amazonaws.com/';
    $folder_array = [
        '40' =>'lp_fedex_labels/pdf/',
        '140'=>'lp_fedex_labels/pdf/',
        '54' =>'aramex_labels/pdf/',
        '68' =>'aramex_labels/pdf/',
        '86'=>'atlantic_labels/pdf/',
        '87'=>'atlantic_labels/pdf/',
        '57'=>'lexship_labels/pdf/',
        '58'=>'lexship_labels/pdf/',
        '38' =>'ocs_labels/pdf/',
        '39' =>'ocs_labels/pdf/',
        '38' =>'ocs_labels/pdf/',
        '98' =>'ocs_labels/pdf/',
        '100'=>'ocs_labels/pdf/',
        '102' =>'ocs_labels/pdf/',
        '104'=>'ocs_labels/pdf/',
        '106' =>'ocs_labels/pdf/',
        '97'=>'ocs_labels/pdf/',
        '99'=>'ocs_labels/pdf/',
        '101'=>'ocs_labels/pdf/',
        '103'=>'ocs_labels/pdf/',
        '105'=>'ocs_labels/pdf/',
        '125'=>'ocs_labels/pdf/',
        '126'=>'ocs_labels/pdf/',
        '127'=>'ocs_labels/pdf/',
        '128'=>'ocs_labels/pdf/',
        '78'=>'rajdhani_labels/pdf/',
        '84'=>'rajdhani_labels/pdf/',
        '85'=>'rajdhani_labels/pdf/',
        '89'=>'rajdhani_labels/pdf/',
        '90'=>'rajdhani_labels/pdf/',
        '91'=>'rajdhani_labels/pdf/',
        '114'=>'skynet_labels/pdf/',
        '115' =>'skynet_labels/pdf/',
        '117' =>'skynet_labels/pdf/',
        '118' =>'skynet_labels/pdf/',
        '119' =>'skynet_labels/pdf/',
        '120' =>'skynet_labels/pdf/',
        '121' =>'skynet_labels/pdf/',
        '122' =>'skynet_labels/pdf/',
        '123' =>'skynet_labels/pdf/',
        '124' =>'skynet_labels/pdf/',
        '129'=>'skynet_labels/pdf/',
        '130'=>'skynet_labels/pdf/',
        '136'=>'skynet_labels/pdf/',
        '138'=>'skynet_labels/pdf/',
        '139'=>'skynet_labels/pdf/',
        '95'=>'united_labels/pdf',
        '131'=>'pod_links/pdf/'
    ];
   
    $folder_name = $folder_array[$courier_id] ?? '';
    
    $label_url = $url.$folder_name.$awb_number.'.pdf';
    if(@file_get_contents($label_url)) {
        return $label_url;
    }

    return false;
}

function getQueueName($courier_id = false) {
    if (empty($courier_id))
        return false;

    //get courier details
    $CI =& get_instance();
    $CI->load->library('courier_lib');
    $courier = $CI->courier_lib->getByID($courier_id);

    if (empty($courier))
        return false;

    $order_type = strtolower($courier->order_type);

    $queue_name = 'others';
    switch ($order_type) {
        case 'ecom':
            $display_name = strtolower($courier->display_name);
            switch (strtolower($display_name)) {
                case 'amazon shipping':
                    $queue_name = 'ats';
                    break;

                case 'kerry indev':
                    $queue_name = 'kerry';
                    break;

                case 'bluedart':
                    $queue_name = $display_name;
                    if (in_array($courier_id, [137, 161])) {
                        $queue_name = 'smartship';
                    }
                    break;

                case 'delhivery':
                    $queue_name = $display_name;
                    if (in_array($courier_id, [160, 162, 164])) {
                        $queue_name = 'shipway';
                    }
                    break;

                case 'dtdc':
                case 'ecomexpress':
                case 'ekart':
                // case 'fedex':
                // case 'gati':
                case 'shadowfax':
                case 'smartr':
                // case 'udaan':
                case 'xpressbees':
                    $queue_name = $display_name;
                    break;

                default:
                    break;
            }
            break;
        
        
        default:
            break;
    }

    return $queue_name;
}

function save_system_log($save=array()) {
    if (empty($save))
        return false;

    //get courier details
    $CI =& get_instance();
    $CI->load->model('logs_model');
    $CI->logs_model->insert($save);
    return true;
}
