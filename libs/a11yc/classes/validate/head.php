<?php
/**
 * A11yc\Validation_Head
 *
 * @package    part of A11yc
 * @author     Jidaikobo Inc.
 * @license    The MIT License (MIT)
 * @copyright  Jidaikobo Inc.
 * @link       http://www.jidaikobo.com
 */
namespace A11yc;
class Validate_Head extends Validate
{
	/**
	 * check doctype
	 *
	 * @return  void
	 */
	public static function check_doctype()
	{
		if ( ! static::get_doctype())
		{
			static::$error_ids['check_doctype'][0]['id'] = false;
			static::$error_ids['check_doctype'][0]['str'] = 'doctype not found';
		}
		static::add_error_to_html('check_doctype', static::$error_ids);
	}

	/**
	 * viewport
	 *
	 * @return  void
	 */
	public static function viewport()
	{
		$str = static::ignore_elements(static::$hl_html);
		$ms = static::get_elements_by_re($str, 'tags');
		if ( ! $ms[0]) return;

		foreach ($ms[1] as $k => $tag)
		{
			if ($tag == 'meta' && strpos($ms[2][$k], 'user-scalable=no') !== false)
			{
				static::$error_ids['user_scalable_no'][0]['id'] = $ms[0][$k];
				static::$error_ids['user_scalable_no'][0]['str'] = 'user-scalable=no';
			}
		}
		static::add_error_to_html('user_scalable_no', static::$error_ids);
	}

	/**
	 * meta_refresh
	 *
	 * @return  void
	 */
	public static function meta_refresh()
	{
		$str = static::ignore_elements(static::$hl_html);
		$ms = static::get_elements_by_re($str, 'tags');
		if ( ! $ms[0]) return;

		foreach ($ms[0] as $k => $v)
		{
			if ($ms[1][$k] != 'meta') continue;
			$attrs = static::get_attributes($v);

			if ( ! array_key_exists('http-equiv', $attrs)) continue;
			if ( ! array_key_exists('content', $attrs)) continue;
			if ( $attrs['http-equiv'] !== 'refresh') continue;

			// ignore zero refresh
			// see http://www.ciaj.or.jp/access/web/docs/WCAG-TECHS/H76.html
			$content = $attrs['content'];
		if (
				trim(substr($content, 0, strpos($content, ';'))) != '0' ||
				(strpos($content, ';') === false && trim($content) != '0')
			)
			{
				static::$error_ids['meta_refresh'][0]['id'] = $ms[0][$k];
				static::$error_ids['meta_refresh'][0]['str'] = $ms[0][$k];
			}
		}
		static::add_error_to_html('meta_refresh', static::$error_ids, 'ignores');
	}

	/**
	 * titleless
	 *
	 * @return  void
	 */
	public static function titleless()
	{
		$str = static::ignore_elements(static::$hl_html);

		// to locate first element at the error
		$ms = static::get_elements_by_re($str, 'tags');

		if (
			strpos(strtolower($str), '<title') === false || // lacknesss of title element
			preg_match("/\<title[^\>]*?\>[ 　]*?\<\/title/si", $str) // lacknesss of title
		)
		{
			static::$error_ids['titleless'][0]['id'] = false;
			static::$error_ids['titleless'][0]['str'] = $ms[0][0];
		}
		static::add_error_to_html('titleless', static::$error_ids, 'ignores');
	}

	/**
	 * langless
	 *
	 * @return  void
	 */
	public static function langless()
	{
		// do not use static::ignore_elements() and static::get_elements_by_re()
		// in case of "<html>" is in comment out
		preg_match_all(
			"/\<([a-zA-Z1-6]+?) +?([^\>]*?)[\/]*?\>|\<([a-zA-Z1-6]+?)[ \/]*?\>/i",
			static::$hl_html,
			$ms);

		$has_langs = array();
		foreach ($ms[0] as $k => $v)
		{
			$attrs = static::get_attributes($v);
			if ( ! isset($attrs['lang']) && ! isset($attrs['xml:lang']) ) continue;
			$has_langs[0][$k] = $ms[0][$k];
			$has_langs[1][$k] = $ms[1][$k];
			$has_langs[2][$k] = $ms[2][$k];
			$has_langs[3][$k] = $attrs;
		}

		// is lang exists?
		if ( ! isset($has_langs[1]) || ! in_array('html', $has_langs[1]))
		{
			static::$error_ids['langless'][0]['id'] = false;
			static::$error_ids['langless'][0]['str'] = Arr::get($ms, '0.0');
			static::add_error_to_html('langless', static::$error_ids);
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
				static::$error_ids['different_lang'][$k]['id'] = $ms[0][$k];
				static::$error_ids['different_lang'][$k]['str'] = $ms[0][$k];
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
					static::$error_ids['invalid_page_lang'][$k]['id'] = $ms[0][$k];
					static::$error_ids['invalid_page_lang'][$k]['str'] = $ms[0][$k];
				}
				// 3.1.2
				else
				{
					static::$error_ids['invalid_partial_lang'][$k]['id'] = $ms[0][$k];
					static::$error_ids['invalid_partial_lang'][$k]['str'] = $ms[0][$k];
				}
			}
		}
		static::add_error_to_html('different_lang', static::$error_ids);
		static::add_error_to_html('invalid_page_lang', static::$error_ids);
		static::add_error_to_html('invalid_partial_lang', static::$error_ids);
	}

	/**
	 * same page title in same site
	 *
	 * @return  void
	 */
	public static function same_page_title_in_same_site()
	{
		$title = Util::fetch_page_title_from_html(static::$hl_html);
		$sql = 'SELECT count(*) as num FROM '.A11YC_TABLE_PAGES.' WHERE `page_title` = ?;';
		$results = Db::fetch($sql, array($title));

		if (intval($results['num']) >= 2)
		{
			static::$error_ids['same_page_title_in_same_site'][0]['id'] = $title;
			static::$error_ids['same_page_title_in_same_site'][0]['str'] = $title;
		}
		static::add_error_to_html('same_page_title_in_same_site', static::$error_ids);
	}
}
