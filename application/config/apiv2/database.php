<?php
defined('BASEPATH') or exit('No direct script access allowed');

use Illuminate\Database\Capsule\Manager as Capsule;


$active_group = 'default';
$query_builder = TRUE;

$db['default'] = array(
	'dsn'	=> '',
	'hostname' => 'localhost',
	'username' => 'root',
	'password' => '',
	// 'username' => 'webhook_user',
	// 'password' => 'Dpv$243aUJ6y',
	'database' => 'delta_shipping',
	'dbdriver' => 'mysqli',
	'dbprefix' => '',
	'pconnect' => FALSE,
	'db_debug' => (ENVIRONMENT !== 'production'),
	'cache_on' => FALSE,
	'cachedir' => '',
	'char_set' => 'utf8',
	'dbcollat' => 'utf8_general_ci',
	'swap_pre' => '',
	'encrypt' => FALSE,
	'compress' => FALSE,
	'stricton' => FALSE,
	'failover' => array(),
	'save_queries' => TRUE
);
//$db['slave'] = $db['default'];
$db['slave'] = array(
	'dsn'	=> '',
	'hostname' => 'localhost',
	'username' => 'root',
	'password' => '',
	'database' => 'delta_shipping',
	'dbdriver' => 'mysqli',
	'dbprefix' => '',
	'pconnect' => FALSE,
	'db_debug' => (ENVIRONMENT !== 'production'),
	'cache_on' => FALSE,
	'cachedir' => '',
	'char_set' => 'utf8',
	'dbcollat' => 'utf8_general_ci',
	'swap_pre' => '',
	'encrypt' => FALSE,
	'compress' => FALSE,
	'stricton' => FALSE,
	'failover' => array(),
	'save_queries' => TRUE
);

$db['awb_tracking'] = array(
	'dsn'	=> '',
	'hostname' => 'root',
	'username' => 'root',
	'password' => '',
	'database' => 'awb_tracking',
	'dbdriver' => 'mysqli',
	'dbprefix' => '',
	'pconnect' => FALSE,
	'db_debug' => (ENVIRONMENT !== 'production'),
	'cache_on' => FALSE,
	'cachedir' => '',
	'char_set' => 'utf8',
	'dbcollat' => 'utf8_general_ci',
	'swap_pre' => '',
	'encrypt' => FALSE,
	'compress' => FALSE,
	'stricton' => FALSE,
	'failover' => array(),
	'save_queries' => TRUE
);





$db['logs'] = array(
	'dsn'   => '',
	'hostname' => 'root',
	'username' => 'root',
	'password' => '',
	'database' => 'activity_logs',
	'dbdriver' => 'mysqli',
	'dbprefix' => '',
	'pconnect' => FALSE,
	'db_debug' => (ENVIRONMENT !== 'production'),
	'cache_on' => FALSE,
	'cachedir' => '',
	'char_set' => 'utf8',
	'dbcollat' => 'utf8_general_ci',
	'swap_pre' => '',
	'encrypt' => FALSE,
	'compress' => FALSE,
	'stricton' => FALSE,
	'failover' => array(),
	'save_queries' => TRUE
);


$capsule = new Capsule;

$capsule->addConnection(
	[
		'driver'    => 'mysql',
		'host'      => $db['default']['hostname'],
		'database'  => $db['default']['database'],
		'username'  => $db['default']['username'],
		'password'  => $db['default']['password'],
		'charset'   => $db['default']['char_set'],
		'collation' => $db['default']['dbcollat'],
		'prefix'    => $db['default']['dbprefix'],
	],
	'default'
);
$capsule->addConnection(
	[
		'driver'    => 'mysql',
		'host'      => $db['slave']['hostname'],
		'database'  => $db['slave']['database'],
		'username'  => $db['slave']['username'],
		'password'  => $db['slave']['password'],
		'charset'   => $db['slave']['char_set'],
		'collation' => $db['slave']['dbcollat'],
		'prefix'    => $db['slave']['dbprefix'],
	],
	'slave'
);

$capsule->setAsGlobal();
$capsule->bootEloquent();
