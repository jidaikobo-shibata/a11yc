<?php
/**
 * A11yc\Util
 *
 * @package    part of A11yc
 * @version    1.0
 * @author     Jidaikobo Inc.
 * @license    WTFPL2.0
 * @copyright  Jidaikobo Inc.
 * @link       http:/www.jidaikobo.com
 */
namespace A11yc;
class Util extends \Kontiki\Util
{
	/**
	 * number to 'A'
	 *
	 * @return  string
	 */
	public static function num2str($num, $default = '-')
	{
		$num = intval($num);
		return $num ? str_repeat('A', $num) : $default ;
	}

	/**
	 * replace '-' to '.' to convert '1-1-1' to '1.1.1'
	 *
	 * @return  string
	 */
	public static function key2code($str)
	{
		return str_replace('-', '.', $str);
	}
}
