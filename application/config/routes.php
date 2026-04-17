<?php

defined('BASEPATH') or exit('No direct script access allowed');


$host = strtolower($_SERVER['HTTP_HOST']);
if (preg_match("/\.daakit\.com$/", $host)) {
  $host = 'localhost';
}
$host_type = '';
$domain = '';
if ($host == 'localhost') {
  $route['dash'] = 'dashboard';
  $route['kyc'] = 'dashboard/kyc';
  $route['kyc/(:any)'] = 'dashboard/kyc/$1';
  $route['analytics'] = 'dashboard';
  $route['analytics/(.*)'] = 'dashboard/$1';
  $route['admin'] = 'admin/analytics';
  $route['admin/dash'] = 'admin/analytics';
  $route['n/(:num)/(:num)'] = 'forms/ndr/$1/$2';
  $route['awb/tracking/(:any)'] = 'trk/tracking/$1';
  $route['shipping/tracking_order/(:any)'] = 'trk/tracking_ordernumber/$1';
  $route['shipping/tracking/r/(:any)'] = 'trk/tracking/$1/rto';
  $route['track'] = 'trk/track_order';
  $route['trk/(:any)'] = 'trk/tracking/$1';
  $route['apps'] = 'apps/front';

  $route['caller'] = 'caller/dash/index';

  $route['404_override'] = '';
  $route['default_controller'] = 'users';
  $route['translate_uri_dashes'] = FALSE;

}

define('HOST_TYPE', $host_type);
define('DOMAIN', $domain);
