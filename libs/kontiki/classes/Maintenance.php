<?php
/**
 * Kontiki\Maintenance
 *
 * @package    part of Kontiki
 * @author     Jidaikobo Inc.
 * @license    The MIT License (MIT)
 * @copyright  Jidaikobo Inc.
 * @link       http://www.jidaikobo.com
 */
namespace Kontiki;
class Maintenance
{
	/**
	 * sqlite
	 * Be care for directory traversal
	 *
	 * @param String $path
	 * @param String $file
	 * @param Bool $is_force
	 * @return Void
	 */
	public static function sqlite ($path, $file, $is_force = FALSE)
	{
		if (defined('A11YC_DB_TYPE') && A11YC_DB_TYPE == 'mysql') return;

		if ( ! file_exists($path.$file))
		{
			Util::error('sqlite file is missing.');
		}

		// prepare
		$backup_file = $path.'/backup.'.date('ymd', time()).'.sqlite';
		$path = $path.$file;

		// run backup once in a day
		if ( ! file_exists($backup_file) || $is_force)
		{
			// maintenance
			Db::forge(
				'maintenance',
				array(
					'dbtype' => 'sqlite',
					'path' => $path,
				));

			$sql = 'BEGIN TRANSACTION;VACUUM;COMMIT;';
			Db::execute($sql, array(), 'maintenance');

			// copy
			if ( ! copy($path, $backup_file))
			{
				Session::add('messages', 'errors', 'Auto back up was failed.');
			}
			else
			{
				Session::add('messages', 'messages', 'Auto back up was succeed.');
			}

			// garbage collection
			self::garbageCollection($path);
		}
	}

	/**
	 * garbageCollection
	 *
	 * @param String $path
	 * @return Void
	 */
	private static function garbageCollection ($path)
	{
		$path = dirname($path);
		foreach (glob($path.'/'.'*') as $file)
		{
			$file = basename($file);

			if (substr($file, 0, 7) == 'backup.')
			{
				if (filectime($path.'/'.$file) <= time() - 86400 * 10)
				{
					unlink($path.'/'.$file);
				}
			}
		}
	}
}
