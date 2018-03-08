<?php
/**
 * A11yc\Validate\HereLink
 *
 * @package    part of A11yc
 * @author     Jidaikobo Inc.
 * @license    The MIT License (MIT)
 * @copyright  Jidaikobo Inc.
 * @link       http://www.jidaikobo.com
 */
namespace A11yc\Validate;

class HereLink extends Validate
{
	/**
	 * here link
	 *
	 * @return  void
	 */
	public static function check($url)
	{
		$str = static::ignoreElements(static::$hl_htmls[$url]);
		$ms = static::getElementsByRe($str, 'ignores', 'anchors_and_values');
		if ( ! $ms[2]) return;

		$heres = array_map('trim', explode(',', A11YC_LANG_HERE));
		foreach ($ms[2] as $k => $m)
		{
			$m = trim($m);
			if (in_array(strtolower($m), $heres))
			{
				static::$error_ids[$url]['here_link'][$k]['id'] = $ms[0][$k];
				static::$error_ids[$url]['here_link'][$k]['str'] = Util::s($ms[0][$k]);
			}
		}
		static::addErrorToHtml($url, 'here_link', static::$error_ids[$url], 'ignores');
	}
}
