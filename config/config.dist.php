<?php
/**
 * config
 *
 * @package    part of A11yc
 */

// base url
define('A11YC_URL', 'http://example.com/a11yc/index.php');

// users
// array must be started with 1 (not 0)
// array(1 => array(username, password, display_name, memo))
define('A11YC_USERS', serialize(array(
	1 => array('root', 'password', 'administrator', ''),
)));

// for css and js
define('A11YC_URL_DIR', dirname(A11YC_URL).'/libs/a11yc');

// language
define('A11YC_LANG', 'ja');
define('KONTIKI_DEFAULT_LANG', 'ja');

// path
define('A11YC_PATH', dirname(__DIR__).'/libs/a11yc');

// target
define('A11YC_TARGET',     ' target="a11y_target"');
define('A11YC_TARGET_OUT', ' target="a11y_target_out"');
