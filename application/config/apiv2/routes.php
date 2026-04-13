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


$route['dash'] = 'analytics';
$route['admin'] = 'admin/analytics';
$route['admin/dash'] = 'admin/analytics';
$route['n/(:num)/(:num)'] = 'forms/ndr/$1/$2';
$route['shipping/tracking/(:any)'] = 'trk/tracking/$1';
$route['shipping/tracking/r/(:any)'] = 'trk/tracking/$1/rto';
$route['international_shipping/tracking/(:any)'] = 'trk/tracking/$1';
$route['international_shipping/tracking/r/(:any)'] = 'trk/tracking/$1/rto';
$route['track'] = 'trk/track_order';
$route['trk/(:any)'] = 'trk/tracking/$1';
$route['apps'] = 'apps/front';

$route['wms'] = 'wms/onboard/index';
$route['wms/onboard'] = 'wms/onboard/index';

$route['caller'] = 'caller/dash/index';

$route['404_override'] = '';
$route['default_controller'] = 'users';
$route['translate_uri_dashes'] = FALSE;

unset($route['(:any)']);
unset($route['(:any)/(:any)']);
unset($route['(:any)/(:any)/(:any)']);

