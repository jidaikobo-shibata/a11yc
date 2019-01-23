<?php
/**
 * config
 *
 * @package    part of A11yc
 */

// base url (include script filename)
define('A11YC_URL', 'http://example.com/a11yc/index.php');

// system language - en, ja
define('A11YC_LANG', 'ja');

// time zone
define('A11YC_TIMEZONE', 'Asia/Tokyo');

// database
define('A11YC_DB_TYPE',   'sqlite'); // 'mysql', 'sqlite', 'none'
define('A11YC_DATA_PATH', dirname(__DIR__).'/db');
define('A11YC_DATA_FILE', '/db.sqlite');

// pathes
defined('A11YC_CONFIG_PATH') or define('A11YC_CONFIG_PATH', dirname(__FILE__));
define('A11YC_LIB_PATH',    dirname(__DIR__).'/libs');
define('A11YC_PATH',        A11YC_LIB_PATH.'/a11yc');

// users
// array must be started with 1 (not 0)
// array(1 => array(username, password, display_name, memo))
// username 'root' is development user to show development information.
// to hash password:
// php -r "echo password_hash('password', CRYPT_BLOWFISH);\n"
define('A11YC_USERS', serialize(array(
	1 => array(
		'admin',
		'$2y$10$4cik/nEzePSnrSwaUsUaleosAHLUPDk0SchWDmn5LCM1LqqqYH6xC', // 'adminpass'
		'administrator',
		''
	),
)));

// administration access approved IPs - must be array
// define('A11YC_APPROVED_IPS', serialize(array(
// 	'::1',
// 	'127.0.0.1',
// )));

// post access approved IPs - must be array
// define('A11YC_APPROVED_GUEST_IPS', serialize(array(
// 	'::1',
// 	'127.0.0.1',
// )));
