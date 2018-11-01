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
	 * deny http directories
	 *
	 * @return Void
	 */
	public static function denyHttpDirectories()
	{
		$result = true;
		$content = 'Deny From All';

		// use constant only
		$dirs = array(
			A11YC_CONFIG_PATH,
			A11YC_LIB_PATH,
		);

		// deny from all
		foreach ($dirs as $dir)
		{
			$path = $dir.'/.htaccess';
			if (file_exists($path)) continue;
			$result = file_put_contents($path, $content);
		}

		// faild
		if ( ! $result)
		{

Session::add('messages', 'error', 'failred to put .htaccess. add .htaccess with "deny from all"'.A11YC_CONFIG_PATH.', '.A11YC_LIB_PATH);
		}
	}
}