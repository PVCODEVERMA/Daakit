<?php

defined('BASEPATH') or exit('No direct script access allowed');

/*
  | -------------------------------------------------------------------------
  | URI ROUTING
  | -------------------------------------------------------------------------
  | This file lets you re-map URI requests to specific controller functions.
  |
  | Typically there is a one-to-one relationship between a URL string
  | and its corresponding controller class/method. The segments in a
  | URL normally follow this pattern:
  |
  |	example.com/class/method/id/
  |
  | In some instances, however, you may want to remap this relationship
  | so that a different class/function is called than the one
  | corresponding to the URL.
  |
  | Please see the user guide for complete details:
  |
  |	https://codeigniter.com/user_guide/general/routing.html
  |
  | -------------------------------------------------------------------------
  | RESERVED ROUTES
  | -------------------------------------------------------------------------
  |
  | There are three reserved routes:
  |
  |	$route['default_controller'] = 'welcome';
  |
  | This route indicates which controller class should be loaded if the
  | URI contains no data. In the above example, the "welcome" class
  | would be loaded.
  |
  |	$route['404_override'] = 'errors/page_missing';
  |
  | This route will tell the Router which controller/method to use if those
  | provided in the URL cannot be matched to a valid route.
  |
  |	$route['translate_uri_dashes'] = FALSE;
  |
  | This is not exactly a route, but allows you to automatically route
  | controller and method names that contain dashes. '-' isn't a valid
  | class or method name character, so it requires translation.
  | When you set this option to TRUE, it will replace ALL dashes in the
  | controller and method URI segments.
  |
  | Examples:	my-controller/index	-> my_controller/index
  |		my-controller/my-method	-> my_controller/my_method
 */

$host = strtolower($_SERVER['HTTP_HOST']);
if (preg_match("/\.daakit\.com$/", $host)) {
    $host = 'localhost';
}
$host_type = '';
$domain = '';
if ($host == 'localhost') {
  $route['dash'] = 'analytics';
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

} //else {

//   if ($host == 'ordr.live') {
//     $route['n/(:num)/(:num)'] = 'forms/ndr/$1/$2';
//     $route['forms/success'] = 'forms/success';
//   }
//   if (preg_match("/.ordr\.live$/", $host)) {
//     $host_type = 'subdomain';
//     $domain = explode('.', $host)[0];
//   }
//   // else {
//   //   $host_type = 'domain';
//   //   $domain = $host;
//   // }
//   $route['shipping/tracking_order/(:any)'] = 'trk/tracking_ordernumber/$1';
//   $route['track'] = 'trk/track_order';
//   $route['trk/(:any)'] = 'trk/tracking/$1';;
//   $route['default_controller'] = 'trk/track_order';
//   if (!is_cli()) {
//     $route['(:any)'] = 'xyz';
//     $route['(:any)/(:any)'] = 'xyz';
//     $route['(:any)/(:any)/(:any)'] = 'xyz';
//   }
//   $route['translate_uri_dashes'] = FALSE;
// }
define('HOST_TYPE', $host_type);
define('DOMAIN', $domain);
