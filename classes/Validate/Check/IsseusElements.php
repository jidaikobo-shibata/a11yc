<?php
/**
 * * A11yc\Validate\Check\IsseusElements
 *
 * @package    part of A11yc
 * @author     Jidaikobo Inc.
 * @license    The MIT License (MIT)
 * @copyright  Jidaikobo Inc.
 * @link       http://www.jidaikobo.com
 */
namespace A11yc\Validate\Check;

use A11yc\Element;
use A11yc\Validate;
use A11yc\Model;

class IsseusElements extends Validate
{
	/**
	 * elements
	 *
	 * @param String $url
	 * @return Void
	 */
	public static function check($url)
	{
		$regex = '/\<[^\>]+?\>[^\<]+?\</';
		Issues\Base::check($url, $regex);
	}
}
