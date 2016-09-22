<?php
/**
 * config
 *
 * @package    part of A11yc
 */

// base url
define('A11YC_URL', 'http://example.com/a11yc/index.php');

// users
define('A11YC_USERS', serialize(array(
	1 => array('root', 'password', 'root', ''),
)));

// for css and js
define('A11YC_URL_DIR', dirname(A11YC_URL).'/libs/a11yc');

// language
define('A11YC_LANG', 'ja');

// target
define('A11YC_TARGET',     ' target="a11y_target"');
define('A11YC_TARGET_OUT', ' target="a11y_target_out"');
