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
	 * setBegTime
	 *
	 * @return Void
	 */
	public static function setBegTime()
	{
		static::$beg_time = microtime(true);
	}

	/**
	 * setBegMemory
	 *
	 * @return Void
	 */
	public static function setBegMemory()
	{
		static::$beg_memory = memory_get_usage(false);
	}

	/**
	 * calcTime
	 *
	 * @return String
	 */
	public static function calcTime()
	{
		return number_format(microtime(true) - static::$beg_time, 2).' sec.';
	}

	/**
	 * calcMemory
	 *
	 * @return String
	 */
	public static function calcMemory()
	{
		return round((memory_get_usage(false) - static::$beg_memory) / 1048576, 2).' MB.';
	}
}
