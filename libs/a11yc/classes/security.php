<?php
/**
 * A11yc\Security
 *
 * @package    part of A11yc
 * @author     Jidaikobo Inc.
 * @license    The MIT License (MIT)
 * @copyright  Jidaikobo Inc.
 * @link       http://www.jidaikobo.com
 */
namespace A11yc;

class Security extends \Kontiki\Security
{
	/**
	 * deny_http_directories
	 *
	 * @return Void
	 */
	public static function deny_http_directories()
	{
		$result = true;
		$content = 'Deny From All';

		// use constant only
		$dirs = array(
			A11YC_CONFIG_PATH,
//			A11YC_DATA_PATH,
		);

		// deny from all
		foreach ($dirs as $dir)
		{
			$path = $dir.'/.htaccess';
			if (file_exists($path)) continue;
			$result = file_put_contents($path, $content);
		}

		// deny some files
		$path = A11YC_LIB_PATH.'/.htaccess';
		if ( ! file_exists($path))
		{
			$result = file_put_contents($path, '<Files ~ "\.(php|yml)$">\n  '.$content.'\n</Files>');
		}

		// faild
		if ( ! $result)
		{
			Session::add('messages', 'error', 'failred to put .htaccess. add .htaccess with "deny from all"'.A11YC_CONFIG_PATH.''.A11YC_CACHE_PATH.''.A11YC_DATA_PATH);
		}
	}
}