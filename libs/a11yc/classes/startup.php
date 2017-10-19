<?php
/**
 * A11yc\Startup
 *
 * @package    part of A11yc
 * @author     Jidaikobo Inc.
 * @license    The MIT License (MIT)
 * @copyright  Jidaikobo Inc.
 * @link       http://www.jidaikobo.com
 */
namespace A11yc;

class Startup
{
	/**
	 * install
	 *
	 * @return Void
	 */
	public static function install ()
	{
/*
		// already done
		if (file_exists(A11YC_DATA_PATH)) return;

		// make directories
		if (mkdir(A11YC_DATA_PATH) && mkdir(A11YC_CACHE_PATH))
		{
			Session::add('messages', 'messages', A11YC_LANG_STARTUP_SETDIRS);
		}
		else
		{
			Util::error(A11YC_LANG_STARTUP_ERROR_DIR);
		}

		// set .htaccess
		Security::deny_http_directories();
*/
	}

	/**
	 * check_progress
	 *
	 * @return Void
	 */
	public static function check_progress ()
	{
	}
}