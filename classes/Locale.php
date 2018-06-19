<?php
/**
 * \JwpA11y\Locale
 *
 * @package    WordPress
 * @version    1.0
 * @author     Jidaikobo Inc.
 * @license    GPL
 * @copyright  Jidaikobo Inc.
 * @link       http://www.jidaikobo.com
 */
namespace JwpA11y;

class Locale
{
	/**
	 * get simple locale.
	 *
	 * @return  string
	 */
	public static function get_simple_locale($locale)
	{
		// language
		$lang = 'en';
		$langs = glob(WP_PLUGIN_DIR.'/jwp-a11y/libs/a11yc/resources/*');
		$langs = array_map('basename', $langs);

		if (in_array($locale, $langs))
		{
			$lang = $locale;
		}
		else
		{
			// check simple locale
			$simple_locale = substr($locale, 0, strpos($locale, '_'));
			$langs = array_map(function ($str) {
				return strpos($str, '_') !== false ? substr($str, 0, strpos($str, '_')) : $str;
			}, $langs);
			if (in_array($simple_locale, $langs))
			{
				$lang = $simple_locale;
			}
		}
		return $lang;
	}
}
