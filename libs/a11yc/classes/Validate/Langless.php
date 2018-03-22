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
		if (Validate::isPartial() == true) return;

		// do not use Element::ignoreElements() and Element::getElementsByRe()
		// in case of "<html>" is in comment out
		preg_match_all(
			"/\<([a-zA-Z1-6]+?) +?([^\>]*?)[\/]*?\>|\<([a-zA-Z1-6]+?)[ \/]*?\>/i",
			static::$hl_htmls[$url],
			$ms);

		$has_langs = array();
		foreach ($ms[0] as $k => $v)
		{
			$attrs = Element::getAttributes($v);
			if ( ! isset($attrs['lang']) && ! isset($attrs['xml:lang']) ) continue;
			$has_langs[0][$k] = $ms[0][$k];
			$has_langs[1][$k] = $ms[1][$k];
			$has_langs[2][$k] = $ms[2][$k];
			$has_langs[3][$k] = $attrs;
		}

		// is lang exists?
		if ( ! isset($has_langs[1]) || ! in_array('html', $has_langs[1]))
		{
			static::$error_ids[$url]['langless'][0]['id'] = false;
			static::$error_ids[$url]['langless'][0]['str'] = Arr::get($ms, '0.0');
			static::addErrorToHtml($url, 'langless', static::$error_ids[$url]);
			return;
		}

		// valid language?
		// http://www.w3schools.com/tags/ref_language_codes.asp
		// http://www.w3schools.com/tags/ref_country_codes.asp
		// case-insensitive
		// http://www.wiley.com/legacy/compbooks/graham/html4ed/appe/iso3166.html

		$langs = array(
			'ab', 'aa', 'af', 'sq', 'am', 'ar', 'an', 'hy', 'as', 'ay', 'az', 'ba', 'eu',
			'bn', 'dz', 'bh', 'bi', 'br', 'bg', 'my', 'be', 'km', 'ca', 'zh', 'zh-Hans',
			'zh-Hant', 'co', 'hr', 'cs', 'da', 'nl', 'en', 'eo', 'et', 'fo', 'fa', 'fj',
			'fi', 'fr', 'fy', 'gl', 'gd', 'gv', 'ka', 'de', 'el', 'kl', 'gn', 'gu', 'ht',
			'ha', 'he', 'iw', 'hi', 'hu', 'is', 'io', 'id', 'in', 'ia', 'ie', 'iu', 'ik',
			'ga', 'it', 'ja', 'jv', 'kn', 'ks', 'kk', 'rw', 'ky', 'rn', 'ko', 'ku', 'lo',
			'la', 'lv', 'li', 'ln', 'lt', 'mk', 'mg', 'ms', 'ml', 'mt', 'mi', 'mr', 'mo',
			'mn', 'na', 'ne', 'no', 'oc', 'or', 'om', 'ps', 'pl', 'pt', 'pa', 'qu', 'rm',
			'ro', 'ru', 'sm', 'sg', 'sa', 'sr', 'sh', 'st', 'tn', 'sn', 'ii', 'sd', 'si',
			'ss', 'sk', 'sl', 'so', 'es', 'su', 'sw', 'sv', 'tl', 'tg', 'ta', 'tt', 'te',
			'th', 'bo', 'ti', 'to', 'ts', 'tr', 'tk', 'tw', 'ug', 'uk', 'ur', 'uz', 'vi',
			'vo', 'wa', 'cy', 'wo', 'xh', 'yi', 'ji', 'yo', 'zu'
		);

		$countries = array(
			'af', 'al', 'dz', 'as', 'ad', 'ao', 'aq', 'ag', 'ar', 'am', 'aw', 'au', 'at',
			'az', 'bs', 'bh', 'bd', 'bb', 'by', 'be', 'bz', 'bj', 'bm', 'bt', 'bo', 'ba',
			'bw', 'bv', 'br', 'io', 'bn', 'bg', 'bf', 'bi', 'kh', 'cm', 'ca', 'cv', 'ky',
			'cf', 'td', 'cl', 'cn', 'cx', 'cc', 'co', 'km', 'cg', 'cd', 'ck', 'cr', 'ci',
			'hr', 'cu', 'cy', 'cz', 'dk', 'dj', 'dm', 'do', 'ec', 'eg', 'sv', 'gq', 'er',
			'ee', 'et', 'fk', 'fo', 'fj', 'fi', 'fr', 'gf', 'pf', 'tf', 'ga', 'gm', 'ge',
			'de', 'gh', 'gi', 'gr', 'gl', 'gd', 'gp', 'gu', 'gt', 'gn', 'gw', 'gy', 'ht',
			'hm', 'hn', 'hk', 'hu', 'is', 'in', 'id', 'ir', 'iq', 'ie', 'il', 'it', 'jm',
			'jp', 'jo', 'kz', 'ke', 'ki', 'kp', 'kr', 'kw', 'kg', 'la', 'lv', 'lb', 'ls',
			'lr', 'ly', 'li', 'lt', 'lu', 'mo', 'mk', 'mg', 'mw', 'my', 'mv', 'ml', 'mt',
			'mh', 'mq', 'mr', 'mu', 'yt', 'mx', 'fm', 'md', 'mc', 'mn', 'me', 'ms', 'ma',
			'mz', 'mm', 'na', 'nr', 'np', 'nl', 'an', 'nc', 'nz', 'ni', 'ne', 'ng', 'nu',
			'nf', 'mp', 'no', 'om', 'pk', 'pw', 'ps', 'pa', 'pg', 'py', 'pe', 'ph', 'pn',
			'pl', 'pt', 'pr', 'qa', 're', 'ro', 'ru', 'rw', 'sh', 'kn', 'lc', 'pm', 'vc',
			'ws', 'sm', 'st', 'sa', 'sn', 'rs', 'sc', 'sl', 'sg', 'sk', 'si', 'sb', 'so',
			'za', 'gs', 'es', 'lk', 'sd', 'sr', 'sj', 'sz', 'se', 'ch', 'sy', 'tw', 'tj',
			'tz', 'th', 'tl', 'tg', 'tk', 'to', 'tt', 'tn', 'tr', 'tm', 'tc', 'tv', 'ug',
			'ua', 'ae', 'gb', 'us', 'um', 'uy', 'uz', 'vu', 've', 'vn', 'vg', 'vi', 'wf',
			'eh', 'ye', 'zm', 'zw'
		);

		foreach ($has_langs[3] as $k => $v)
		{
			// different lang
			if (isset($v['lang']) && isset($v['xml:lang']) && $v['lang'] != $v['xml:lang'])
			{
				static::$error_ids[$url]['different_lang'][$k]['id'] = $ms[0][$k];
				static::$error_ids[$url]['different_lang'][$k]['str'] = $ms[0][$k];
			}

			// it must be at leaset one of them is exist
			$lang = isset($v['lang']) ? $v['lang'] : $v['xml:lang'];

			// lang check
			$ls = explode('-', $lang);
			if (
				! in_array(strtolower($ls[0]), $langs) ||
				isset($ls[1]) && ! in_array(strtolower($ls[1]), $countries)
			)
			{
				// 3.1.1
				if ($has_langs[1][$k] == 'html')
				{
					static::$error_ids[$url]['invalid_page_lang'][$k]['id'] = $ms[0][$k];
					static::$error_ids[$url]['invalid_page_lang'][$k]['str'] = $ms[0][$k];
				}
				// 3.1.2
				else
				{
					static::$error_ids[$url]['invalid_partial_lang'][$k]['id'] = $ms[0][$k];
					static::$error_ids[$url]['invalid_partial_lang'][$k]['str'] = $ms[0][$k];
				}
			}
		}
		static::addErrorToHtml($url, 'different_lang', static::$error_ids[$url]);
		static::addErrorToHtml($url, 'invalid_page_lang', static::$error_ids[$url]);
		static::addErrorToHtml($url, 'invalid_partial_lang', static::$error_ids[$url]);
	}
}
