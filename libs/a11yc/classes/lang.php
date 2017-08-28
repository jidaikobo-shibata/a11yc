<?php
/**
 * A11yc\Lang
 *
 * @package    part of A11yc
 * @author     Jidaikobo Inc.
 * @license    The MIT License (MIT)
 * @copyright  Jidaikobo Inc.
 * @link       http://www.jidaikobo.com
 */
namespace A11yc;

class Lang extends \Kontiki\Lang
{
	/**
	 * get_lang
	 *
	 * @return String
	 */
	public static function get_lang()
	{
		// get languages
		$langs = Lang::get_langs(A11YC_PATH.'/languages');

		// parse url
		$requests = explode('/', substr(\A11yc\Util::uri(), strlen(dirname(A11YC_URL)) + 1));

		// is available language?
		$lang = '';
		if (in_array(Arr::get($requests, 0), $langs))
		{
			$lang = Arr::get($requests, 0);
		}

		// default language?
		if (empty($lang) && count($requests) < 2)
		{
			$lang = A11YC_LANG;
		}
		return $lang;
	}
}
