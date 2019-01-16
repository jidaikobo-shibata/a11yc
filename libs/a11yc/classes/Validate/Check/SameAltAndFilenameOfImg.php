<?php
/**
 * * A11yc\Validate\Check\SameAltAndFilenameOfImg
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

class SameAltAndFilenameOfImg extends Validate
{
	/**
	 * same alt and filename of img
	 *
	 * @param String $url
	 * @return Void
	 */
	public static function check($url)
	{
		Validate\Set::log($url, 'same_alt_and_filename_of_img', self::$unspec, 1);
		$str = Element\Get::ignoredHtml($url);
		$ms = Element\Get::elementsByRe($str, 'ignores', 'imgs');
		if ( ! $ms[0])
		{
			Validate\Set::log($url, 'same_alt_and_filename_of_img', self::$unspec, 4);
			return;
		}

		foreach ($ms[0] as $k => $m)
		{
			$attrs = Element\Get::attributes($m);
			if ( ! isset($attrs['alt']) || ! isset($attrs['src'])) continue;
			if (empty($attrs['alt'])) continue;
			$tstr = $m;

			$filename = basename($attrs['src']);

			Validate\Set::errorAndLog(
				$attrs['alt'] == $filename || // within extension
				$attrs['alt'] == substr($filename, 0, strrpos($filename, '.')) || // without extension
				$attrs['alt'] == substr($filename, 0, strrpos($filename, '-')), // without size
				$url,
				'same_alt_and_filename_of_img',
				$k,
				$tstr,
				'"'.$filename.'"'
			);
		}
		static::addErrorToHtml($url, 'same_alt_and_filename_of_img', static::$error_ids[$url], 'ignores');
	}
}
