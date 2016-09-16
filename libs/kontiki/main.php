<?php
/**
 * Kontiki
 *
 * @package    part of Kontiki
 * @version    1.0
 * @author     Jidaikobo Inc.
 * @license    WTFPL2.0
 * @copyright  Jidaikobo Inc.
 * @link       http:/www.jidaikobo.com
 */

// load function
include __DIR__.'/functions.php';

// Autoloader
spl_autoload_register(
	function ($class_name)
	{
		if (strtolower(substr($class_name, 0, 7)) !== 'kontiki') return;
		require __DIR__.'/classes/'.substr($class_name, 8).'.php';
	}
);

/*
include __DIR__.'/classes/db.php';
include __DIR__.'/classes/auth.php';
include __DIR__.'/classes/users.php';
include __DIR__.'/classes/util.php';
include __DIR__.'/classes/view.php';
*/