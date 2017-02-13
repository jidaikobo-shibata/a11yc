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
	 * @return  void
	 */
	public static function sqlite ($path, $file, $is_force = FALSE)
	{
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
			static::garbage_collection($path);
		}
	}

	/**
	 * mysql
	 *
	 * @return  void
	 */
	public static function mysql ($path, $is_force = FALSE)
	{
		// in preparation
		return;

		$backup_file = $path.'/backup_.'.date('ymd', time()).'.mysql';
		$path = $path;

		// run backup once in a day
		if ( ! file_exists($backup_file) || $is_force)
		{
			$oView->clearAllCache();

			// CHECK TABLEやREPAIR TABLEすべき？
			// dump
			$dump = "SET NAMES UTF8;\n\n";
			/*
				foreach(Db::getTables() as $table)
				{
				$dump.= Db::dump($table);
				}
			*/

			// put file
			if ( ! file_put_contents($backup_file, $dump))
			{
				Session::add('messages', 'errors', 'Auto back up was failed.');
			}
			else
			{
				Session::add('messages', 'messages', 'Auto back up was succeed.');
			}

			// garbage collection
			static::garbage_collection($path);
		}
	}

	/**
	 * garbage_collection
	 *
	 * @return  void
	 */
	private static function garbage_collection ($path)
	{
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
