<?php
/**
 * A11yc\Validation
 *
 * @package    part of A11yc
 * @version    1.0
 * @author     Jidaikobo Inc.
 * @license    The MIT License (MIT)
 * @copyright  Jidaikobo Inc.
 * @link       http://www.jidaikobo.com
 */
namespace A11yc;
class Validate_Validation extends Validate
{
	/**
	 * check doctype
	 *
	 * @return  void
	 */
	public static function check_doctype()
	{
		$ms = static::get_elements_by_re(static::$hl_html, 'tags');
		if ( ! $ms[0]) return;

		if ( ! static::get_doctype())
		{
			static::$error_ids['check_doctype'][0]['id'] = false;
			static::$error_ids['check_doctype'][0]['str'] = $ms[0][0];
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
		$ms = static::get_elements_by_re(static::$hl_html, 'tags');
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
	 * appropriate heading descending
	 *
	 * @return  bool
	 */
	public static function appropriate_heading_descending()
	{
		$str = static::ignore_elements(static::$hl_html);

		$secs = preg_split("/\<h([1-6])[^\>]*\>(.+?)\<\/h\d/", $str, -1, PREG_SPLIT_DELIM_CAPTURE);
		if ( ! $secs) return;

		$prev = 1;
		foreach ($secs as $sec)
		{
			if (is_numeric($sec))
			{
				$prev = $sec;
				break;
			}
		}

		foreach ($secs as $k => $v)
		{
			if ( ! is_numeric($v)) continue; // skip non heading
			$current_level = $v;

			if ($current_level - $prev >= 2)
			{
				$str = isset($secs[$k + 1]) ? $secs[$k + 1] : $v;
				static::$error_ids['appropriate_heading_descending'][$k]['id'] = $str;
				static::$error_ids['appropriate_heading_descending'][$k]['str'] = $str;
			}
			$prev = $current_level;
		}
		static::add_error_to_html('appropriate_heading_descending', static::$error_ids, 'ignores');
	}

	/**
	 * suspicious_elements
	 *
	 * @return  bool
	 */
	public static function suspicious_elements()
	{
		$str = static::ignore_elements(static::$hl_html);

		// tags
		preg_match_all("/\<([^\> \n]+)/i", $str, $tags);

		// elements
		$endless = array('img', 'wbr', 'br', 'hr', 'base', 'input', 'param', 'area', 'embed', 'meta', 'link', 'track', 'source', 'col', 'command', 'frame');
		$ignores = array('!doctype', 'html', '![if', '![endif]', '?xml');
		$omissionables = array('li', 'dt', 'dd', 'p', 'rt', 'rp', 'optgroup', 'option', 'tr', 'td', 'th', 'thead', 'tfoot', 'tbody', 'colgroup');
		$ignores = array_merge($ignores, $endless, $omissionables);

		// tags
		$opens = array();
		$ends = array();
		foreach ($tags[1] as $tag)
		{
			$tag = strtolower($tag);
			if (in_array($tag, $ignores)) continue; // ignore
			if (in_array(substr($tag, 1), $ignores)) continue; // ignore

			// collect tags
			if ($tag[0] =='/')
			{
				$ends[] = substr($tag, 1);
			}
			else
			{
				$opens[] = $tag;
			}
		}

		// count tags
		$opens_cnt = array_count_values($opens);
		$ends_cnt = array_count_values($ends);

		// check nums of opens
		$too_much_opens = array();
		$too_much_ends = array();
		foreach ($opens_cnt as $tag => $num)
		{
			if ( ! isset($ends_cnt[$tag]) || $opens_cnt[$tag] > $ends_cnt[$tag])
			{
				$too_much_opens[] = $tag;
			}
			elseif ($opens_cnt[$tag] < $ends_cnt[$tag])
			{
				$too_much_ends[] = $tag;
			}
		}

		// endless
		$suspicious_ends = array();
		foreach ($endless as $v)
		{
			if (strpos($str, '</'.$v) !== false)
			{
				$suspicious_ends[] = '/'.$v;
			}
		}

		// add errors
		foreach ($too_much_opens as $k => $v)
		{
			static::$error_ids['too_much_opens'][$k]['id'] = false;
			static::$error_ids['too_much_opens'][$k]['str'] = $v;
		}
		static::add_error_to_html('too_much_opens', static::$error_ids, 'ignores');

		foreach ($too_much_ends as $k => $v)
		{
			static::$error_ids['too_much_ends'][$k]['id'] = false;
			static::$error_ids['too_much_ends'][$k]['str'] = $v;
		}
		static::add_error_to_html('too_much_ends', static::$error_ids, 'ignores');

		foreach ($suspicious_ends as $k => $v)
		{
			static::$error_ids['suspicious_ends'][$k]['id'] = false;
			static::$error_ids['suspicious_ends'][$k]['str'] = $v;
		}
		static::add_error_to_html('suspicious_ends', static::$error_ids, 'ignores');
	}

	/**
	 * ja word breaking space
	 *
	 * @return  void
	 */
	public static function ja_word_breaking_space()
	{
		if (A11YC_LANG != 'ja') return false;
		$str = str_replace(array("\n", "\r"), '', static::$hl_html);
		$str = static::ignore_elements(static::$hl_html);

		preg_match_all("/([^\x01-\x7E][ 　]{2,}[^\x01-\x7E])/iu", $str, $ms);
		foreach ($ms[1] as $k => $m)
		{
			static::$error_ids['ja_word_breaking_space'][$k]['id'] = $ms[0][$k];
			static::$error_ids['ja_word_breaking_space'][$k]['str'] = $m;
		}
		static::add_error_to_html('ja_word_breaking_space', static::$error_ids, 'ignores');
	}

	/**
	 * meanless element
	 *
	 * @return  void
	 */
	public static function meanless_element()
	{
		$str = static::ignore_elements(static::$hl_html);

		$banneds = array(
			'big',
			'tt',
			'center',
			'font',
			'blink',
			'marquee',
			'b',
			'i',
			'u',
			's',
			'strike',
			'basefont',
		);

		$ms = static::get_elements_by_re($str, 'tags');
		if ( ! $ms[0]) return;

		$n = 0;
		foreach ($ms[0] as $m)
		{
			foreach ($banneds as $banned)
			{
				preg_match_all('/\<'.$banned.' [^\>]*?\>|\<'.$banned.'\>/', $m, $mms);
				if ( ! $mms[0]) continue;
				foreach ($mms[0] as $tag)
				{
					if (strpos($tag, '<blink') !== false || strpos($tag, '<marquee') !== false )
					{
						static::$error_ids['meanless_element_timing'][$n]['id'] = $tag;
						static::$error_ids['meanless_element_timing'][$n]['str'] = $tag;
					}
					else
					{
						static::$error_ids['meanless_element'][$n]['id'] = $tag;
						static::$error_ids['meanless_element'][$n]['str'] = $tag;
					}
					$n++;
				}
			}
		}
		static::add_error_to_html('meanless_element', static::$error_ids, 'ignores');
	}

	/**
	 * style for structure
	 *
	 * @return  void
	 */
	public static function style_for_structure()
	{
		$str = static::ignore_elements(static::$hl_html);

		$ms = static::get_elements_by_re($str, 'tags');
		if ( ! $ms[0]) return;
		foreach ($ms[0] as $k => $m)
		{
			$attrs = static::get_attributes($m);
			if ( ! array_key_exists('style', $attrs)) continue;
			if (
				strpos($attrs['style'], 'size') !== false ||
				strpos($attrs['style'], 'color') !== false // includes background-color
			)
			{
				static::$error_ids['style_for_structure'][$k]['id'] = $ms[0][$k];
				static::$error_ids['style_for_structure'][$k]['str'] = $m;
			}
		}
		static::add_error_to_html('style_for_structure', static::$error_ids, 'ignores');
	}

	/**
	 * suspicious attributes
	 *
	 * @return  void
	 */
	public static function suspicious_attributes()
	{
		$str = static::ignore_elements(static::$hl_html);

		$ms = static::get_elements_by_re($str, 'tags');
		if ( ! $ms[0]) return;

		foreach ($ms[0] as $k => $m)
		{
			$attrs = static::get_attributes($m);

			// suspicious attributes
			if (isset($attrs['suspicious']))
			{
				static::$error_ids['suspicious_attributes'][$k]['id'] = $ms[0][$k];
				static::$error_ids['suspicious_attributes'][$k]['str'] = join(', ', $attrs['suspicious']);
			}

			// duplicated_attributes
			if (isset($attrs['plural']))
			{
				static::$error_ids['duplicated_attributes'][$k]['id'] = $ms[0][$k];
				static::$error_ids['duplicated_attributes'][$k]['str'] = $m;
			}
		}
		static::add_error_to_html('suspicious_attributes', static::$error_ids, 'ignores');
		static::add_error_to_html('duplicated_attributes', static::$error_ids, 'ignores');
	}

	/**
	 * duplicated ids and accesskey
	 *
	 * @return  void
	 */
	public static function duplicated_ids_and_accesskey()
	{
		$str = static::ignore_elements(static::$hl_html);

		$ms = static::get_elements_by_re($str, 'tags');
		if ( ! $ms[0]) return;

		// duplicated_ids
		$ids = array();
		foreach ($ms[0] as $k => $m)
		{
			$attrs = static::get_attributes($m);
			if ( ! isset($attrs['id'])) continue;

			if (in_array($attrs['id'], $ids))
			{
				static::$error_ids['duplicated_ids'][$k]['id'] = $ms[0][$k];
				static::$error_ids['duplicated_ids'][$k]['str'] = $attrs['id'];
			}
			$ids[] = $attrs['id'];
		}
		static::add_error_to_html('duplicated_ids', static::$error_ids, 'ignores');

		// duplicated_accesskeys
		$accesskeys = array();
		foreach ($ms[0] as $k => $m)
		{
			$attrs = static::get_attributes($m);
			if ( ! isset($attrs['accesskey'])) continue;

			if (in_array($attrs['accesskey'], $accesskeys))
			{
				static::$error_ids['duplicated_accesskeys'][$k]['id'] = $ms[0][$k];
				static::$error_ids['duplicated_accesskeys'][$k]['str'] = $attrs['accesskey'];
			}
			$accesskeys[] = $attrs['accesskey'];
		}
		static::add_error_to_html('duplicated_accesskeys', static::$error_ids, 'ignores');
	}

	/**
	 * invalid tag
	 *
	 * @return  void
	 */
	public static function invalid_tag()
	{
		$str = static::ignore_elements(static::$hl_html);
		$ms = static::get_elements_by_re($str, 'tags');
		if ( ! $ms[0]) return;

		foreach ($ms[0] as $k => $m)
		{
			// newline character must not exists in attr
			$attrs = static::get_attributes($m);

			foreach ($attrs as $val)
			{
				if (strpos($val, "\n") !== false)
				{
					static::$error_ids['cannot_contain_newline'][$k]['id'] = $ms[0][$k];
					static::$error_ids['cannot_contain_newline'][$k]['str'] = $m;
					break;
				}
			}

			// unbalanced_quotation
			// delete qouted quotation
			$tag = str_replace(array("\\'", '\\"'), '', $m);

			// TODO: in Englsih, single quote is frequent on grammar
			// if ((substr_count($tag, '"') + substr_count($tag, "'")) % 2 !== 0)
			if (substr_count($tag, '"') % 2 !== 0)
			{
				static::$error_ids['unbalanced_quotation'][$k]['id'] = $ms[0][$k];
				static::$error_ids['unbalanced_quotation'][$k]['str'] = $m;
			}

			if (A11YC_LANG != 'ja') continue;

			// multi-byte space
			// ignore values of attributes
			$tag = preg_replace("/(\".+?\"|'.+?')/is", '', $tag);

			if (strpos($tag, '　') !== false)
			{
				static::$error_ids['cannot_contain_multibyte_space'][$k]['id'] = $ms[0][$k];
				static::$error_ids['cannot_contain_multibyte_space'][$k]['str'] = $m;
			}
		}
		static::add_error_to_html('unbalanced_quotation', static::$error_ids, 'ignores');
		static::add_error_to_html('cannot_contain_multibyte_space', static::$error_ids, 'ignores');
		static::add_error_to_html('cannot_contain_newline', static::$error_ids, 'ignores');
	}

	/**
	 * titleless_frame
	 *
	 * @return  void
	 */
	public static function titleless_frame()
	{
		$str = static::ignore_elements(static::$hl_html);
		$ms = static::get_elements_by_re($str, 'tags');
		if ( ! $ms[0]) return;

		foreach ($ms[0] as $k => $v)
		{
			if ($ms[1][$k] != 'frame' && $ms[1][$k] != 'iframe') continue;
			$attrs = static::get_attributes($v);

			if (
				! isset($attrs['title']) ||
				(isset($attrs['title']) && empty(trim($attrs['title'])))
			)
			{
				static::$error_ids['titleless_frame'][0]['id'] = $ms[0][$k];
				static::$error_ids['titleless_frame'][0]['str'] = $ms[0][$k];
			}
		}
		static::add_error_to_html('titleless_frame', static::$error_ids, 'ignores');
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
		// do not use static::ignore_elements() in case of "<html>" is in comment out

		$ms = static::get_elements_by_re(static::$hl_html, 'tags');

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
			static::$error_ids['langless'][0]['str'] = $ms[0][0];
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
