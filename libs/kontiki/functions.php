<?php
/**
 * Kontiki
 *
 * @package    part of Kontiki
 * @author     Jidaikobo Inc.
 * @license    The MIT License (MIT)
 * @copyright  Jidaikobo Inc.
 * @link       http://www.jidaikobo.com
 */

/**
 * Encodes the given string
 *
 * @param  string  $str
 * @return  string
 */
if ( ! function_exists('s'))
{
	function s($str)
	{
    return \Kontiki\Util::s($str);
	}
}
