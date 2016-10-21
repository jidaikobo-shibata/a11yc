<?php
/**
 * A11yc\Validate
 *
 * @package    part of A11yc
 * @version    1.0
 * @author     Jidaikobo Inc.
 * @license    WTFPL2.0
 * @copyright  Jidaikobo Inc.
 * @link       http:/www.jidaikobo.com
 */
namespace A11yc;
class Validate_Validation extends Validate
{
	/**
	 * is exist alt attr of img
	 *
	 * @return  void
	 */
	public static function is_exist_alt_attr_of_img()
	{
		$str = static::ignore_elements(static::$hl_html);

		$ms = static::get_elements_by_re($str, 'imgs');
		if ( ! $ms[1]) return;

		$errs = array();
		foreach ($ms[1] as $k => $m)
		{
			$attrs = static::get_attributes($m);
			if ( ! array_key_exists('alt', $attrs))
			{
				static::$error_ids['is_exist_alt_attr_of_img'][$k]['id'] = Util::s($ms[0][$k]);
				static::$error_ids['is_exist_alt_attr_of_img'][$k]['str'] = Util::s(@basename(@$attrs['src']));
				$errs[$k] = $ms[0][$k];
			}
		}
		static::add_error_to_html('is_exist_alt_attr_of_img', $errs, 'ignores');
	}

	/**
	 * is not empty alt attr of img inside a
	 *
	 * @return  void
	 */
	public static function is_not_empty_alt_attr_of_img_inside_a()
	{
		$str = static::ignore_elements(static::$hl_html);

		$ms = static::get_elements_by_re($str, 'anchors_and_values');
		if ( ! $ms[2]) return;

		$errs = array();
		foreach ($ms[2] as $k => $m)
		{
			if (strpos($m, '<img') === false) continue; // without image
			if (static::is_ignorable($ms[0][$k])) continue; // ignorable
			if ( ! empty(trim(strip_tags($m)))) continue; // not image only
			$attrs = static::get_attributes($m);
			$alt = '';

			foreach ($attrs as $kk => $vv)
			{
				if (strpos($kk, 'alt') !== false)
				{
					$alt.= $vv;
				}
			}
			$alt = trim($alt);

			if ( ! $alt)
			{
				static::$error_ids['is_not_empty_alt_attr_of_img_inside_a'][$k]['id'] = Util::s($ms[0][$k]);
				static::$error_ids['is_not_empty_alt_attr_of_img_inside_a'][$k]['str'] = Util::s(@basename(@$attrs['src']));
				$errs[$k] = $ms[0][$k];
			}
		}
		static::add_error_to_html('is_not_empty_alt_attr_of_img_inside_a', $errs, 'ignores');
	}

	/**
	 * is not here link
	 *
	 * @return  bool
	 */
	public static function is_not_here_link()
	{
		$str = static::ignore_elements(static::$hl_html);

		$ms = static::get_elements_by_re($str, 'anchors_and_values');
		if ( ! $ms[2]) return;

		$errs = array();
		foreach ($ms[2] as $k => $m)
		{
			$m = trim($m);
			if ($m == A11YC_LANG_HERE)
			{
				static::$error_ids['is_not_here_link'][$k]['id'] = Util::s($ms[0][$k]);
				static::$error_ids['is_not_here_link'][$k]['str'] = @Util::s($ms[1][$k]);
				$errs[$k] = $ms[0][$k];
			}
		}
		static::add_error_to_html('is_not_here_link', $errs, 'ignores');
	}

	/**
	 * is area has alt
	 *
	 * @return  bool
	 */
	public static function is_area_has_alt()
	{
		$str = static::ignore_elements(static::$hl_html);

		$ms = static::get_elements_by_re($str, 'tags');
		if ( ! $ms[0]) return;

		$errs = array();
		foreach ($ms[0] as $k => $m)
		{
			if (substr($m, 0, 5) !== '<area') continue;
			$attrs = static::get_attributes($m);
			if ( ! isset($attrs['alt']) || empty($attrs['alt']))
			{
				static::$error_ids['is_area_has_alt'][$k]['id'] = Util::s($ms[0][$k]);
				static::$error_ids['is_area_has_alt'][$k]['str'] = Util::s(@basename(@$attrs['href']));
				$errs[$k] = $ms[0][$k];
			}
		}
		static::add_error_to_html('is_area_has_alt', $errs, 'ignores');
	}

	/**
	 * is img input has alt
	 *
	 * @return  bool
	 */
	public static function is_img_input_has_alt()
	{
		$str = static::ignore_elements(static::$hl_html);

		$ms = static::get_elements_by_re($str, 'tags');
		if ( ! $ms[0]) return;

		$errs = array();
		foreach($ms[0] as $k => $m)
		{
			if (substr($m, 0, 6) !== '<input') continue;
			$attrs = static::get_attributes($m);
			if ( ! isset($attrs['type'])) continue; // unless type it is recognized as a text
			if (isset($attrs['type']) && $attrs['type'] != 'image') continue;

			if ( ! isset($attrs['alt']) || empty($attrs['alt']))
			{
				static::$error_ids['is_img_input_has_alt'][$k]['id'] = Util::s($ms[0][$k]);
				static::$error_ids['is_img_input_has_alt'][$k]['str'] = Util::s(@basename(@$attrs['src']));
				$errs[$k] = $ms[0][$k];
			}
		}
		static::add_error_to_html('is_img_input_has_alt', $errs, 'ignores');
	}

	/**
	 * appropriate heading descending
	 *
	 * @return  bool
	 */
	public static function appropriate_heading_descending()
	{
		$str = static::ignore_elements(static::$hl_html);

		$secs = preg_split("/(\<h\d)[^\>]*\>(.+?)\<\/h\d/", $str, -1, PREG_SPLIT_DELIM_CAPTURE);

		$prev = 1;
		$errs = array();
		foreach ($secs as $k => $v)
		{
			if (strlen($v) != 3) continue; // skip non heading
			if (substr($v, 0, 2) != '<h') continue; // skip non heading
			$current_level = intval($v[2]);

			if ($current_level - $prev >= 2)
			{
				$str = isset($secs[$k + 1]) ? Util::s($secs[$k + 1]) : Util::s($v);
				static::$error_ids['appropriate_heading_descending'][$k]['id'] = $str;
				static::$error_ids['appropriate_heading_descending'][$k]['str'] = $str;
				$errs[$k] = $str;
			}
			$prev = $current_level;
		}
		static::add_error_to_html('appropriate_heading_descending', $errs, 'ignores');
	}

	/**
	 * suspicious_elements
	 *
	 * @return  bool
	 */
	public static function suspicious_elements()
	{
		$body_html = static::ignore_elements(static::$hl_html);

		// tags
		preg_match_all("/\<([^\> ]+)/i", $body_html, $tags);

		// elements
		$endless = array('img', 'wbr', 'br', 'hr', 'base', 'input', 'param', 'area', 'embed', 'meta', 'link', 'track', 'source', 'col', 'command');
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

		// check nums
		$suspicious_opens = array();
		foreach ($opens_cnt as $tag => $num)
		{
			if ( ! isset($ends_cnt[$tag]) || $opens_cnt[$tag] != $ends_cnt[$tag])
			{
				$suspicious_opens[] = $tag;
			}
		}

		// check nums
		$suspicious_ends = array();
		foreach ($ends_cnt as $tag => $num)
		{
			if ( ! isset($opens_cnt[$tag]) || $opens_cnt[$tag] != $ends_cnt[$tag])
			{
				$suspicious_ends[] = $tag;
			}
		}

		// endless
		foreach ($endless as $v)
		{
			if (strpos($body_html, '</'.$v) !== false && ! in_array('/'.$v, $suspicious_ends))
			{
				$suspicious_ends[] = '/'.$v;
			}
		}

		// add errors
		$errs = array();
		foreach ($suspicious_opens as $k => $v)
		{
			static::$error_ids['suspicious_opens'][$k]['id'] = Util::s('<'.$v);
			static::$error_ids['suspicious_opens'][$k]['str'] = Util::s($v);
			$errs[$k] = '<'.$v;
		}
		static::add_error_to_html('suspicious_opens', $errs, 'ignores');

		$errs = array();
		foreach ($suspicious_ends as $k => $v)
		{
			static::$error_ids['suspicious_ends'][$k]['id'] = Util::s('<'.$v);
			static::$error_ids['suspicious_ends'][$k]['str'] = Util::s($v);
			$errs[$k] = '<'.$v;
		}
		static::add_error_to_html('suspicious_ends', $errs, 'ignores');
	}

	/**
	 * is not same alt and filename of img
	 *
	 * @return  void
	 */
	public static function is_not_same_alt_and_filename_of_img()
	{
		$str = static::ignore_elements(static::$hl_html);

		$ms = static::get_elements_by_re($str, 'imgs');
		if ( ! $ms[1]) return;

		$errs = array();
		foreach ($ms[1] as $k => $m)
		{
			$attrs = static::get_attributes($m);
			if ( ! isset($attrs['alt']) ||  ! isset($attrs['src'])) continue;

			$filename = basename($attrs['src']);
			if (
				$attrs['alt'] == $filename || // within extension
				$attrs['alt'] == substr($filename, 0, strrpos($filename, '.')) // without extension
			)
			{
				static::$error_ids['is_not_same_alt_and_filename_of_img'][$k]['id'] = Util::s($ms[0][$k]);
				static::$error_ids['is_not_same_alt_and_filename_of_img'][$k]['str'] = '"'.Util::s($filename).'"';
				$errs[$k] = $ms[0][$k];
			}
		}
		static::add_error_to_html('is_not_same_alt_and_filename_of_img', $errs, 'ignores');
	}

	/**
	 * is not exists ja word breaking space
	 *
	 * @return  void
	 */
	public static function is_not_exists_ja_word_breaking_space()
	{
		if (A11YC_LANG != 'ja') return false;
		$str = str_replace(array("\n", "\r"), '', static::$hl_html);
		$str = static::ignore_elements(static::$hl_html);

		preg_match_all("/([^\x01-\x7E][ 　][ 　]+[^\x01-\x7E])/iu", $str, $ms);
		$errs = array();
		foreach ($ms[1] as $k => $m)
		{
			static::$error_ids['is_not_exists_ja_word_breaking_space'][$k]['id'] = Util::s($ms[0][$k]);
			static::$error_ids['is_not_exists_ja_word_breaking_space'][$k]['str'] = Util::s($m);
			$errs[$k] = $ms[0][$k];
		}
		static::add_error_to_html('is_not_exists_ja_word_breaking_space', $errs, 'ignores');
	}

	/**
	 * is not exists meanless element
	 *
	 * @return  void
	 */
	public static function is_not_exists_meanless_element()
	{
		$body_html = static::ignore_elements(static::$hl_html);

		$banneds = array(
			'<center',
			'<font',
			'<blink',
			'<marquee',
		);

		$ms = static::get_elements_by_re($body_html, 'tags');
		if ( ! $ms[0]) return;

		$errs = array();
		foreach ($ms[0] as $k => $m)
		{
			foreach ($banneds as $banned)
			{
				if (substr($m, 0, strlen($banned)) == $banned)
				{
					static::$error_ids['is_not_exists_meanless_element'][$k]['id'] = Util::s($m);
					static::$error_ids['is_not_exists_meanless_element'][$k]['str'] = Util::s($m);
					$errs[$k] = $m;
					break;
				}
			}
		}
		static::add_error_to_html('is_not_exists_meanless_element', $errs, 'ignores');
	}

	/**
	 * is not style for structure
	 *
	 * @return  void
	 */
	public static function is_not_style_for_structure()
	{
		$str = static::ignore_elements(static::$hl_html);

		$ms = static::get_elements_by_re($str, 'tags');
		if ( ! $ms[1]) return;
		$errs = array();
		foreach ($ms[1] as $k => $m)
		{
			if (
				strpos($m, 'style=') !== false &&
				(
					strpos($m, 'size') !== false ||
					strpos($m, 'color') !== false
				)
			)
			{
				static::$error_ids['is_not_style_for_structure'][$k]['id'] = Util::s($ms[0][$k]);
				static::$error_ids['is_not_style_for_structure'][$k]['str'] = Util::s($m);
				$errs[$k] = $ms[0][$k];
			}
		}
		static::add_error_to_html('is_not_style_for_structure', $errs, 'ignores');
	}

	/**
	 * duplicated attributes
	 *
	 * @return  void
	 */
	public static function duplicated_attributes()
	{
		$str = static::ignore_elements(static::$hl_html);

		$ms = static::get_elements_by_re($str, 'tags');
		if ( ! $ms[1]) return;

		$errs = array();
		foreach ($ms[1] as $k => $m)
		{
			$attrs = static::get_attributes($m);
			if (isset($attrs['suspicious']))
			{
				static::$error_ids['duplicated_attributes'][$k]['id'] = Util::s($ms[0][$k]);
				static::$error_ids['duplicated_attributes'][$k]['str'] = Util::s($m);
				$errs[$k] = $ms[0][$k];
			}
		}
		static::add_error_to_html('duplicated_attributes', $errs, 'ignores');
	}

	/**
	 * duplicated ids
	 *
	 * @return  void
	 */
	public static function duplicated_ids()
	{
		$str = static::ignore_elements(static::$hl_html);

		$ms = static::get_elements_by_re($str, 'tags');
		if ( ! $ms[1]) return;

		$ids = array();
		$errs = array();
		foreach ($ms[1] as $k => $m)
		{
			$attrs = static::get_attributes($m);
			if ( ! isset($attrs['id'])) continue;

			if (in_array($attrs['id'], $ids))
			{
				static::$error_ids['duplicated_ids'][$k]['id'] = Util::s($ms[0][$k]);
				static::$error_ids['duplicated_ids'][$k]['str'] = Util::s($attrs['id']);
				$errs[$k] = $ms[0][$k];
			}
			$ids[] = $attrs['id'];
		}
		static::add_error_to_html('duplicated_ids', $errs, 'ignores');
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
		if ( ! $ms[1]) return;

		$errs1 = array();
		$errs2 = array();
		foreach ($ms[1] as $k => $m)
		{
			// unbalanced_quotation
			$tag = str_replace(array("\\'", '\\"'), '', $m);

			// TODO: in Englsih, single quote is frequent on grammar
			// if ((substr_count($tag, '"') + substr_count($tag, "'")) % 2 !== 0)
			if (substr_count($tag, '"') % 2 !== 0)
			{
				static::$error_ids['unbalanced_quotation'][$k]['id'] = Util::s($ms[0][$k]);
				static::$error_ids['unbalanced_quotation'][$k]['str'] = Util::s($m);
				$errs1[] = $ms[0][$k];
			}

			if (A11YC_LANG != 'ja') continue;
			// multi-byte space
			$tag = preg_replace("/(\".+?\"|'.+?')/", '', $tag);
			if (strpos($tag, '　') !== false)
			{
				static::$error_ids['cannot_contain_multibyte_space'][$k]['id'] = Util::s($ms[0][$k]);
				static::$error_ids['cannot_contain_multibyte_space'][$k]['str'] = Util::s($m);
				$errs2[] = $ms[0][$k];
			}
		}
		static::add_error_to_html('unbalanced_quotation', $errs1, 'ignores');
		static::add_error_to_html('cannot_contain_multibyte_space', $errs2, 'ignores');
	}

	/**
	 * tell user file type
	 *
	 * @return  void
	 */
	public static function tell_user_file_type()
	{
		$str = static::ignore_elements(static::$hl_html);
		$ms = static::get_elements_by_re($str, 'anchors_and_values');
		if ( ! $ms[1]) return;

		$suspicious = array(
			'.pdf',
			'.doc',
			'.docx',
			'.xls',
			'.xlsx',
			'.ppt',
			'.pptx',
			'.zip',
			'.tar',
		);

		$errs = array();
		foreach ($ms[1] as $k => $m)
		{
			foreach ($suspicious as $kk => $vv)
			{
				if (strpos($m, $vv) !== false)
				{
					$attrs = static::get_attributes($m);
					$val = isset($attrs['href']) ? $attrs['href'] : '';

					// allow application name
					if (
						(($vv == '.doc' || $vv == '.docx') && strpos($val, 'word') !== false) ||
						(($vv == '.xls' || $vv == '.xlsx') && strpos($val, 'excel') !== false) ||
						(($vv == '.ppt' || $vv == '.pptx') && strpos($val, 'power') !== false)
					)
					{
						$val.= 'doc,docx,xls,xlsx,ppt,pptx';
					}

					if (
						strpos($val, substr($vv, 1)) === false ||
						preg_match("/\d/", $val) == false
					)
					{
						static::$error_ids['tell_user_file_type'][$kk]['id'] = Util::s($ms[0][$k]);
						static::$error_ids['tell_user_file_type'][$kk]['str'] = Util::s($val);
						$errs[$k] = $ms[0][$k];
					}
				}
			}
		}
		static::add_error_to_html('tell_user_file_type', $errs, 'ignores');
	}

	/**
	 * titleless
	 *
	 * @return  void
	 */
	public static function titleless()
	{
		$str = static::ignore_elements(static::$hl_html);

		$errs = array();
		if (strpos(strtolower($str), '<title') === false)
		{
			static::$error_ids['titleless'][0]['id'] = '';
			static::$error_ids['titleless'][0]['str'] = '';
			$errs[$k] = '';
		}
		static::add_error_to_html('titleless', $errs, 'ignores');
	}

	/**
	 * langless
	 *
	 * @return  void
	 */
	public static function langless()
	{
		// do not use static::ignore_elements() in case it is in comment out

		$errs = array();
		if ( ! preg_match("/\<html[^\>]*?lang *?= *?[^\>]*?\>/i", static::$hl_html))
		{
			static::$error_ids['langless'][0]['id'] = Util::s('<html');
			static::$error_ids['langless'][0]['str'] = Util::s('<html');
			$errs[0] = '<html';
		}
		static::add_error_to_html('langless', $errs);
	}

	/**
	 * is not exist same page title in same site
	 *
	 * @return  void
	 */
	public static function is_not_exist_same_page_title_in_same_site()
	{
		$title = Util::fetch_page_title_from_html(static::$hl_html);
		$sql = 'SELECT count(*) as num FROM '.A11YC_TABLE_PAGES.' WHERE `page_title` = ?;';
		$results = Db::fetch($sql, array($title));

		$errs = array();
		if (intval($results['num']) >= 2)
		{
			static::$error_ids['is_not_exist_same_page_title_in_same_site'][$k]['id'] = Util::s($title);
			static::$error_ids['is_not_exist_same_page_title_in_same_site'][$k]['str'] = Util::s($title);
			$errs[0] = '<title>'.$title;
		}
		static::add_error_to_html('is_not_exist_same_page_title_in_same_site', $errs);
	}

	/**
	 * same_urls_should_have_same_text
			// some screen readers read anchor's title attribute.
			// and user cannot understand that title is exist or not.
	 *
	 * @return  void
	 */
	public static function same_urls_should_have_same_text()
	{
		$str = static::ignore_comment_out(static::$hl_html);

		// urls
		$ms = static::get_elements_by_re($str, 'anchors_and_values');
		if ( ! $ms[1]) return;

		$urls = array();
		$errs = array();
		foreach ($ms[1] as $k => $v)
		{
			if (static::is_ignorable($ms[0][$k])) continue;

			$attrs = static::get_attributes($v);
			if ( ! isset($attrs['href'])) continue;
			$url = static::correct_url($attrs['href']);

			// strip m except for alt
			$text = $ms[2][$k];
			preg_match_all("/\<\w+ +?[^\>]*?alt *?= *?[\"']([^\"']*?)[\"'][^\>]*?\>/", $text, $mms);
			if ($mms)
			{
				foreach ($mms[0] as $kk => $vv)
				{
					$text = str_replace($mms[0][$kk], $mms[1][$kk], $text);
				}
			}
			$text = strip_tags($text);
			$text = trim($text);

			// check
			if ( ! array_key_exists($url, $urls))
			{
				$urls[$url] = $text;
			}
			// ouch! same text
			else if ($urls[$url] != $text)
			{
				static::$error_ids['same_urls_should_have_same_text'][$k]['id'] = Util::s($ms[0][$k]);
				static::$error_ids['same_urls_should_have_same_text'][$k]['str'] = Util::s($url).': ('.mb_strlen($urls[$url], "UTF-8").') "'.Util::s($urls[$url]).'" OR ('.mb_strlen($text, "UTF-8").') "'.Util::s($text).'"';
				$errs[$k] = $ms[0][$k];
			}
		}
	static::add_error_to_html('same_urls_should_have_same_text', $errs, 'ignores_comment_out');
	}

	/**
	 * link_check
	 *
	 * @return  void
	 */
	public static function link_check()
	{
		$str = static::ignore_comment_out(static::$hl_html);

		// urls
		preg_match_all("/ (?:href|src|cite|data|poster|action) *?= *?[\"']([^\"']+?)[\"']/i", $str, $ms);
		$urls = array();
		foreach ($ms[1] as $k => $v)
		{
			if (static::is_ignorable($ms[0][$k])) continue;
			$urls[$v] = static::correct_url($v);
		}

		// fragments
		preg_match_all("/ (?:id|name) *?= *?[\"']([^\"']+?)[\"']/i", $str, $fragments);

		// check
		$errs = array();
		$k = 0;
		foreach ($urls as $original => $url)
		{
			if ($url[0] == '#')
			{
				if ( ! in_array(substr($url, 1), $fragments[1]))
				{
					static::$error_ids['link_check'][$k]['id'] = Util::s($original);
					static::$error_ids['link_check'][$k]['str'] = 'Fragment Not Found: '.Util::s($original);
					$errs[$k] = $original;
				}
				continue;
			}

			$headers = @get_headers($url);
			if ($headers !== false)
			{
				// OK TODO: think about redirection
//				if (strpos($headers[0], ' 20') !== false || strpos($headers[0], ' 30') !== false) continue;
				if (strpos($headers[0], ' 20') !== false) continue;

				// not OK
				static::$error_ids['link_check'][$k]['id'] = Util::s($original);
				static::$error_ids['link_check'][$k]['str'] = Util::s(substr($headers[0], strpos($headers[0], ' '))).': '.Util::s($original);
				$errs[$k] = $original;
			}
			else
			{
				static::$error_ids['link_check'][$k]['id'] = 'Not Found: '.Util::s($original);
				static::$error_ids['link_check'][$k]['str'] = 'Not Found: '.Util::s($original);
				$errs[$k] = $original;
			}
			$k++;
		}
		static::add_error_to_html('link_check', $errs, 'ignores_comment_out');
	}
}
