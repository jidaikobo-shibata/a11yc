<?php
/**
 * A11yc\Validate\SameAltAndFilenameOfImg
 *
 * @package    part of A11yc
 * @author     Jidaikobo Inc.
 * @license    The MIT License (MIT)
 * @copyright  Jidaikobo Inc.
 * @link       http://www.jidaikobo.com
 */
namespace A11yc\Validate;

class SameAltAndFilenameOfImg extends Validate
{
	/**
	 * same alt and filename of img
	 *
	 * @param  String $url
	 * @return Void
	 */
	public static function check($url)
	{
		$str = static::ignoreElements(static::$hl_htmls[$url]);
		$ms = static::getElementsByRe($str, 'ignores', 'imgs');
		if ( ! $ms[1]) return;

		foreach ($ms[1] as $k => $m)
		{
			$attrs = static::getAttributes($m);
			if ( ! isset($attrs['alt']) || ! isset($attrs['src'])) continue;
			if (empty($attrs['alt'])) continue;

			$filename = basename($attrs['src']);
			if (
				$attrs['alt'] == $filename || // within extension
				$attrs['alt'] == substr($filename, 0, strrpos($filename, '.')) || // without extension
				$attrs['alt'] == substr($filename, 0, strrpos($filename, '-')) // without size
			)
			{
				static::$error_ids[$url]['same_alt_and_filename_of_img'][$k]['id'] = $ms[0][$k];
				static::$error_ids[$url]['same_alt_and_filename_of_img'][$k]['str'] = '"'.$filename.'"';
			}
		}
		static::addErrorToHtml($url, 'same_alt_and_filename_of_img', static::$error_ids[$url], 'ignores');
	}
}
