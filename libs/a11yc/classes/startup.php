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
	 * @return  void
	 */
	public static function install ()
	{
		// already done
		if (file_exists(A11YC_DATA_PATH)) return;

		// make directories
		if (mkdir(A11YC_DATA_PATH) && mkdir(A11YC_CACHE_PATH))
		{
			Session::add('messages', 'messages', 'データ保存用ディレクトリとキャッシュディレクトリを設置しました。');
		}
		else
		{
			Util::error('データ保存用ディレクトリとキャッシュディレクトリの設置に失敗しました。'.A11YC_DATA_PATH.'と'.A11YC_CACHE_PATH.'を設置してください。');
		}

		// set .htaccess
		Security::deny_http_directories();
	}

	/**
	 * check_progress
	 *
	 * @return  void
	 */
	public static function check_progress ()
	{
	}
}