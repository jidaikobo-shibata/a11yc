<?php
/**
 * Kontiki
 *
 * @package    part of Kontiki
 * @version    1.0
 * @author     Jidaikobo Inc.
 * @license    WTFPL2.0
 * @copyright  Jidaikobo Inc.
 * @link       http://www.jidaikobo.com
 */

/**
 * Encodes the given string
 *
 * @param   string  $str
 * @return  string
 */
if ( ! function_exists('s'))
{
	function s($str)
	{
    return \Kontiki\Util::s($str);
	}
}
