<?php
/**
 * Kontiki\Performance
 *
 * @package    part of Kontiki
 * @author     Jidaikobo Inc.
 * @license    The MIT License (MIT)
 * @copyright  Jidaikobo Inc.
 * @link       http://www.jidaikobo.com
 */
namespace Kontiki;

class Performance
{
	public static $beg_time;
	public static $beg_memory;

	/**
	 * set_time
	 *
	 * @return Void
	 */
	public static function set_time()
	{
		static::$beg_time = microtime(true);
	}

	/**
	 * set_memory
	 *
	 * @return Void
	 */
	public static function set_memory()
	{
		static::$beg_memory = memory_get_usage(false);
	}

	/**
	 * calc_time
	 *
	 * @return String
	 */
	public static function calc_time()
	{
		return number_format(microtime(true) - static::$beg_time, 2).' sec.';
	}

	/**
	 * calc_memory
	 *
	 * @return String
	 */
	public static function calc_memory()
	{
		return round((memory_get_usage(false) - static::$beg_memory) / 1048576, 2).' MB.';
	}
}
