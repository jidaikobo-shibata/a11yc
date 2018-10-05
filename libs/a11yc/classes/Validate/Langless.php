<?php
/**
 * A11yc\Validate\Langless
 *
 * @package    part of A11yc
 * @author     Jidaikobo Inc.
 * @license    The MIT License (MIT)
 * @copyright  Jidaikobo Inc.
 * @link       http://www.jidaikobo.com
 */
namespace A11yc\Validate;

use A11yc\Element;

class Langless extends Validate
{
	/**
	 * langless
	 *
	 * @param  String $url
	 * @return Void
	 */
	public static function check($url)
	{
		static::$logs[$url]['langless'][self::$unspec] = 5;
		if (Validate::$is_partial == true) return;
		static::$logs[$url]['langless'][self::$unspec] = 1;

		// do not use Element\Get::ignoredHtml() and Element\Get::elementsByRe()
		// in case of "<html>" is in comment out
		preg_match_all(
			"/\<([a-zA-Z1-6]+?) +?([^\>]*?)[\/]*?\>|\<([a-zA-Z1-6]+?)[ \/]*?\>/i",
			static::$hl_htmls[$url],
			$ms);

		$has_langs = array();
		foreach ($ms[0] as $k => $v)
		{
			$attrs = Element\Get::attributes($v);
			if ( ! isset($attrs['lang']) && ! isset($attrs['xml:lang']) ) continue;
			$has_langs[0][$k] = $ms[0][$k];
			$has_langs[1][$k] = $ms[1][$k];
			$has_langs[2][$k] = $ms[2][$k];
			$has_langs[3][$k] = $attrs;
		}

		// is lang exists?
		if ( ! isset($has_langs[1]) || ! in_array('html', $has_langs[1]))
		{
			static::$logs[$url]['langless'][self::$unspec] = -1;
			static::$error_ids[$url]['langless'][0]['id'] = false;
			static::$error_ids[$url]['langless'][0]['str'] = Arr::get($ms, '0.0');
			static::addErrorToHtml($url, 'langless', static::$error_ids[$url]);
			return;
		}
		else
		{
			static::$logs[$url]['langless'][self::$unspec] = 2;
		}

		// valid language?
		// case-insensitive
		// http://www.wiley.com/legacy/compbooks/graham/html4ed/appe/iso3166.html

		/*
		$lln = file_get_contents('http://www.iana.org/assignments/language-subtag-registry/language-subtag-registry');

		$llns = explode('%%', $lln);
		foreach ($llns as $k => $ll)
		{
			if (strpos($ll, 'Type: language') === false)
			{
				unset($llns[$k]);
			}
		}

		$ietf_subtags = array();

		foreach ($llns as $k => $ll)
		{
			preg_match('/Subtag: (.+?)\nDescription: (.+?)\n/', $ll, $matches);
			$ietf_subtags[$matches[1]] = $matches[2];
		}
		*/

		$ietf_subtags = require(A11YC_PATH.'/resources/ietf_langs.php');

		foreach ($has_langs[3] as $k => $v)
		{
			$tstr = $ms[0][$k];

			// it must be at leaset one of them is exist
			$lang = isset($v['lang']) ? $v['lang'] : $v['xml:lang'];

			// lang check
			$ls = explode('-', $lang);
			if ( ! array_key_exists(strtolower($ls[0]), $ietf_subtags))
			{
				// 3.1.1
				if ($has_langs[1][$k] == 'html')
				{
					static::$logs[$url]['invalid_page_lang'][$tstr] = -1;
					static::$error_ids[$url]['invalid_page_lang'][$k]['id'] = $tstr;
					static::$error_ids[$url]['invalid_page_lang'][$k]['str'] = $tstr;
				}
				// 3.1.2
				else
				{
					static::$logs[$url]['invalid_partial_lang'][$tstr] = -1;
					static::$error_ids[$url]['invalid_partial_lang'][$k]['id'] = $tstr;
					static::$error_ids[$url]['invalid_partial_lang'][$k]['str'] = $tstr;
				}
			}
		}
		static::addErrorToHtml($url, 'invalid_page_lang', static::$error_ids[$url]);
		static::addErrorToHtml($url, 'invalid_partial_lang', static::$error_ids[$url]);
	}
}
