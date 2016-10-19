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
	 * @param   strings     $str
	 * @return  void
	 */
	public static function is_exist_alt_attr_of_img()
	{
		$str = static::ignore_elements(static::$hl_html);

		$ms = static::get_elements_by_re($str, 'imgs');

		$errs = array();
		foreach ($ms[1] as $k => $m)
		{
			$attrs = static::get_attributes($m);
			if ( ! array_key_exists('alt', $attrs))
			{
				static::$error_ids['is_exist_alt_attr_of_img'][$k]['id'] = Util::s($ms[0][$k]);
				static::$error_ids['is_exist_alt_attr_of_img'][$k]['str'] = Util::s(@basename(@$attrs['src']));
				static::$error_ids['is_exist_alt_attr_of_img'][$k]['name'] = max(array_flip($errs))===false ? 0 : max(array_flip($errs))+1;
				static::$errors[] = Util::s($ms[0][$k]);
				$errs[] = $ms[0][$k];
			}
		}
		static::add_error_to_html('is_exist_alt_attr_of_img', $errs, 'ignores');
	}

	/**
	 * is not empty alt attr of img inside a
	 *
	 * @param   strings     $str
	 * @return  void
	 */
	public static function is_not_empty_alt_attr_of_img_inside_a()
	{
		$str = static::ignore_elements(static::$hl_html);

		$ms = static::get_elements_by_re($str, 'anchors_and_values');

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
				static::$error_ids['is_not_empty_alt_attr_of_img_inside_a'][$k]['name'] = max(array_flip($errs))===false ? 0 : max(array_flip($errs))+1;
				static::$errors[] = Util::s($ms[0][$k]);
				$errs[] = $ms[0][$k];
			}
		}
		static::add_error_to_html('is_not_empty_alt_attr_of_img_inside_a', $errs, 'ignores');
	}

	/**
	 * is not here link
	 *
	 * @param   strings     $str
	 * @return  bool
	 */
	public static function is_not_here_link()
	{
		$str = static::ignore_elements(static::$hl_html);

		$ms = static::get_elements_by_re($str, 'anchors_and_values');

		$errs = array();
		foreach ($ms[2] as $k => $m)
		{
			$m = trim($m);
			if ($m == A11YC_LANG_HERE)
			{
				static::$error_ids['is_not_here_link'][$k]['id'] = Util::s($ms[0][$k]);
				static::$error_ids['is_not_here_link'][$k]['str'] = @Util::s($m);
				static::$errors[] = Util::s($ms[0][$k]);
				$errs[] = $ms[0][$k];
			}
		}
		static::add_error_to_html('is_not_here_link', $errs, 'ignores');
	}

	/**
	 * is area has alt
	 *
	 * @param   strings     $str
	 * @return  bool
	 */
	public static function is_are_has_alt()
	{
		$str = static::ignore_elements(static::$hl_html);

		$ms = static::get_elements_by_re($str, 'tags');

		$errs = array();
		foreach ($ms[0] as $k => $m)
		{
			if (substr($m, 0, 5) !== '<area') continue;
			$attrs = static::get_attributes($m);
			if ( ! isset($attrs['alt']) || empty($attrs['alt']))
			{
				static::$error_ids['is_are_has_alt'][$k]['id'] = Util::s($ms[0][$k]);
				static::$error_ids['is_are_has_alt'][$k]['str'] = Util::s(@basename(@$attrs['coords']));
				static::$error_ids['is_are_has_alt'][$k]['name'] = max(array_flip($errs))===false ? 0 : max(array_flip($errs))+1;
				static::$errors[] = Util::s($ms[0][$k]);
				$errs[] = $ms[0][$k];
			}
		}
		static::add_error_to_html('is_are_has_alt', $errs, 'ignores');
	}

	/**
	 * is img input has alt
	 *
	 * @param   strings     $str
	 * @return  bool
	 */
	public static function is_img_input_has_alt()
	{
		$str = static::ignore_elements(static::$hl_html);

		$ms = static::get_elements_by_re($str, 'tags');

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
				static::$error_ids['is_img_input_has_alt'][$k]['name'] = max(array_flip($errs))===false ? 0 : max(array_flip($errs))+1;
				static::$errors[] = Util::s($ms[0][$k]);
				$errs[] = $ms[0][$k];
			}
		}
		static::add_error_to_html('is_img_input_has_alt', $errs, 'ignores');
	}

	/**
	 * appropriate heading descending
	 *
	 * @param   strings     $str
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
				static::$error_ids['appropriate_heading_descending'][$k]['name'] = max(array_flip($errs))===false ? 0 : max(array_flip($errs))+1;
				static::$errors[] = $str;
				$errs[] = $str;
			}
			$prev = $current_level;
		}
		static::add_error_to_html('appropriate_heading_descending', $errs, 'ignores');
	}

	/**
	 * suspicious_elements
	 *
	 * @param   strings     $str
	 * @return  bool
	 */
	public static function suspicious_elements()
	{
		$body_html = static::ignore_elements(static::$hl_html);

		// tags
		preg_match_all("/\<([^\> ]+)/i", $body_html, $tags);

		// ignore elements
		$endless = array('img', 'wbr', 'br', 'hr', 'base', 'input', 'param', 'area', 'embed', 'meta', 'link', 'track', 'source', 'col', 'command');
		$ignores = array('!doctype', 'html', '/html', '![if', '![endif]', '?xml');
		$ignores = array_merge($ignores, $endless);

		// tag suspicious elements
		$suspicious_opens = array();
		$suspicious_ends = array();
		foreach ($tags[1] as $tag)
		{
			if (in_array(strtolower($tag), $ignores)) continue; // ignore

			// collect tags
			if (substr($tag, 0, 1) =='/')
			{
				$suspicious_ends[] = substr($tag, 1);
			}
			else
			{
				$suspicious_opens[] = $tag;
			}
		}

		$suspicious_ends_results = array_diff($suspicious_ends, $suspicious_opens);
		$suspicious_opens_results = array_diff($suspicious_opens, $suspicious_ends);

		// add slash to end tags
		$suspicious_ends_results = array_map(function($s){return '/'.$s;} , $suspicious_ends_results);

		// suspicious
		$suspicious = array_merge(
			$suspicious_ends_results,
			$suspicious_opens_results);

		// endless
		foreach ($endless as $v)
		{
			if (strpos($body_html, '</'.$v) !== false)
			{
				$suspicious[] = '</'.$v;
			}
		}

		// add errors
		if ($suspicious)
		{
			$errs = array();
			foreach ($suspicious as $k => $v)
			{
				static::$error_ids['suspicious_elements'][$k]['id'] = Util::s('<'.$v);
				static::$error_ids['suspicious_elements'][$k]['str'] = Util::s($v);
				static::$error_ids['suspicious_elements'][$k]['name'] = max(array_flip($errs))===false ? 0 : max(array_flip($errs))+1;
				static::$errors[] = Util::s('<'.$v);
				$errs[] = '<'.$v;
			}
			static::add_error_to_html('suspicious_elements', $errs, 'ignores');
		}
	}

	/**
	 * is not same alt and filename of img
	 *
	 * @param   strings     $str
	 * @return  void
	 */
	public static function is_not_same_alt_and_filename_of_img()
	{
		$str = static::ignore_elements(static::$hl_html);

		$ms = static::get_elements_by_re($str, 'imgs');
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
				static::$error_ids['is_not_same_alt_and_filename_of_img'][$k]['str'] = Util::s($filename);
				static::$error_ids['is_not_same_alt_and_filename_of_img'][$k]['name'] = max(array_flip($errs))===false ? 0 : max(array_flip($errs))+1;
				static::$errors[] = Util::s($ms[0][$k]);
				$errs[] = $ms[0][$k];
			}
		}
		static::add_error_to_html('is_not_same_alt_and_filename_of_img', $errs, 'ignores');
	}

	/**
	 * is not exists ja word breaking space
	 *
	 * @param   strings     $str
	 * @return  void
	 */
	public static function is_not_exists_ja_word_breaking_space()
	{
		if (A11YC_LANG != 'ja') return false;
		$str = str_replace(array("\n", "\r"), '', $str);
		$str = static::ignore_elements(static::$hl_html);

		preg_match_all("/([^\x01-\x7E][ 　][ 　]+[^\x01-\x7E])/iu", $str, $ms);
		$errs = array();
		foreach ($ms[1] as $k => $m)
		{
			static::$error_ids['is_not_exists_ja_word_breaking_space'][$k]['id'] = Util::s($ms[0][$k]);
			static::$error_ids['is_not_exists_ja_word_breaking_space'][$k]['str'] = Util::s($m);
			static::$error_ids['is_not_exists_ja_word_breaking_space'][$k]['name'] = max(array_flip($errs))===false ? 0 : max(array_flip($errs))+1;
			static::$errors[] = Util::s($ms[0][$k]);
			$errs[] = $ms[0][$k];
		}
		static::add_error_to_html('is_not_exists_ja_word_breaking_space', $errs, 'ignores');
	}

	/**
	 * is not exists meanless element
	 *
	 * @param   strings     $str
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

		$errs = array();
		foreach ($ms[0] as $k => $m)
		{
			foreach ($banneds as $banned)
			{
				if (substr($m, 0, strlen($banned)) == $banned)
				{
					static::$error_ids['is_not_exists_meanless_element'][$k]['id'] = Util::s('<'.$m);
					static::$error_ids['is_not_exists_meanless_element'][$k]['str'] = Util::s($m);
					static::$error_ids['is_not_exists_meanless_element'][$k]['name'] = max(array_flip($errs))===false ? 0 : max(array_flip($errs))+1;
					static::$errors[] = Util::s('<'.$m);
					$errs[] = '<'.$m;
					break;
				}
			}
		}
		static::add_error_to_html('is_not_exists_meanless_element', $errs, 'ignores');
	}

	/**
	 * is not style for structure
	 *
	 * @param   strings     $str
	 * @return  void
	 */
	public static function is_not_style_for_structure()
	{
		$str = static::ignore_elements(static::$hl_html);

		$ms = static::get_elements_by_re($str, 'tags');
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
				static::$error_ids['is_not_style_for_structure'][$k]['name'] = max(array_flip($errs))===false ? 0 : max(array_flip($errs))+1;
				static::$errors[] = Util::s($ms[0][$k]);
				$errs[] = $ms[0][$k];
			}
		}
		static::add_error_to_html('is_not_style_for_structure', $errs, 'ignores');
	}

	/**
	 * duplicated attributes
	 *
	 * @param   strings     $str
	 * @return  void
	 */
	public static function duplicated_attributes()
	{
		$str = static::ignore_elements(static::$hl_html);

		$ms = static::get_elements_by_re($str, 'tags');

		$errs = array();
		foreach ($ms[1] as $k => $m)
		{
			$attrs = static::get_attributes($m);
			if (isset($attrs['suspicious']))
			{
				static::$error_ids['duplicated_attributes'][$k]['id'] = Util::s($ms[0][$k]);
				static::$error_ids['duplicated_attributes'][$k]['str'] = Util::s($m);
				static::$error_ids['duplicated_attributes'][$k]['name'] = max(array_flip($errs))===false ? 0 : max(array_flip($errs))+1;
				static::$errors[] = Util::s($ms[0][$k]);
				$errs[] = $ms[0][$k];
			}
		}
		static::add_error_to_html('duplicated_attributes', $errs, 'ignores');
	}

	/**
	 * invalid tag
	 *
	 * @param   strings     $str
	 * @return  void
	 */
	public static function invalid_tag()
	{
		$str = static::ignore_elements(static::$hl_html);
		$ms = static::get_elements_by_re($str, 'tags');

		$errs1 = array();
		$errs2 = array();
		foreach ($ms[1] as $k => $m)
		{
			// unbalanced_quotation
			$tag = str_replace(array("\\'", '\\"'), '', $m);
			if ((substr_count($tag, '"') + substr_count($tag, "'")) % 2 !== 0)
			{
				static::$error_ids['unbalanced_quotation'][$k]['id'] = Util::s($ms[0][$k]);
				static::$error_ids['unbalanced_quotation'][$k]['str'] = Util::s($m);
				static::$error_ids['unbalanced_quotation'][$k]['name'] = max(array_flip($errs1))===false ? 0 : max(array_flip($errs1))+1;
				static::$errors[] = Util::s($ms[0][$k]);
				$errs1[] = $ms[0][$k];
			}

			if (A11YC_LANG != 'ja') continue;
			// multi-byte space
			$tag = preg_replace("/(\".+?\"|'.+?')/", '', $tag);
			if (strpos($tag, '　') !== false)
			{
				static::$error_ids['cannot_contain_multibyte_space'][$k]['id'] = Util::s($ms[0][$k]);
				static::$error_ids['cannot_contain_multibyte_space'][$k]['str'] = Util::s($m);
				static::$error_ids['cannot_contain_multibyte_space'][$k]['name'] = max(array_flip($errs2))===false ? 0 : max(array_flip($errs2))+1;
				static::$errors[] = Util::s($ms[0][$k]);
				$errs2[] = $ms[0][$k];
			}
		}
		static::add_error_to_html('unbalanced_quotation', $errs1, 'ignores');
		static::add_error_to_html('cannot_contain_multibyte_space', $errs2, 'ignores');
	}

	/**
	 * tell user file type
	 *
	 * @param   strings     $str
	 * @return  void
	 */
	public static function tell_user_file_type()
	{
		$str = static::ignore_elements(static::$hl_html);
		$ms = static::get_elements_by_re($str, 'anchors_and_values');
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
						static::$error_ids['tell_user_file_type'][$kk]['name'] = max(array_flip($errs))===false ? 0 : max(array_flip($errs))+1;
						static::$errors[] = Util::s($ms[0][$k]);
						$errs[] = $ms[0][$k];
					}
				}
			}
		}
		static::add_error_to_html('tell_user_file_type', $errs, 'ignores');
	}

	/**
	 * titleless
	 *
	 * @param   strings     $str
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
			static::$error_ids['titleless'][0]['name'] = 0;
			static::$errors[] = '';
			$errs[] = '';
		}
		static::add_error_to_html('titleless', $errs, 'ignores');
	}

	/**
	 * langless
	 *
	 * @param   strings     $str
	 * @return  void
	 */
	public static function langless()
	{
		// do not use static::ignore_elements() in case it is in comment out

		$errs = array();
		if ( ! preg_match("/\<html[^\>]*?lang *?= *?[^\>]*?\>/i", $str))
		{
			static::$error_ids['langless'][0]['id'] = Util::s('<html');
			static::$error_ids['langless'][0]['str'] = '';
			static::$error_ids['langless'][0]['name'] = 0;
			static::$errors[] = Util::s('<html');
			$errs[] = '<html';
		}
		static::add_error_to_html('langless', $errs);
	}

	/**
	 * is not exist same page title in same site
	 *
	 * @param   strings     $str
	 * @return  void
	 */
	public static function is_not_exist_same_page_title_in_same_site()
	{
		$title = Util::fetch_page_title_from_html($str);
		$sql = 'SELECT count(*) as num FROM '.A11YC_TABLE_PAGES.' WHERE `page_title` = ?;';
		$results = Db::fetch($sql, array($title));

		$errs = array();
		if (intval($results['num']) >= 2)
		{
			static::$error_ids['is_not_exist_same_page_title_in_same_site'][$k]['id'] = Util::s($title);
			static::$error_ids['is_not_exist_same_page_title_in_same_site'][$k]['str'] = Util::s($title);
			static::$error_ids['is_not_exist_same_page_title_in_same_site'][$k]['name'] = max(array_flip($errs))===false ? 0 : max(array_flip($errs))+1;
			static::$errors[] = Util::s($title);
			$errs[] = '<title>'.$title;
		}
		static::add_error_to_html('is_not_exist_same_page_title_in_same_site', $errs);
	}

	/**
	 * same_urls_should_have_same_text
			// some screen readers read anchor's title attribute.
			// and user cannot understand that title is exist or not.
	 *
	 * @param   strings     $str
	 * @return  void
	 */
	public static function same_urls_should_have_same_text()
	{
		$str = static::ignore_comment_out(static::$hl_html);

		// urls
		$ms = static::get_elements_by_re($str, 'anchors_and_values');

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
				static::$error_ids['same_urls_should_have_same_text'][$k]['str'] = Util::s($url).': "'.Util::s($urls[$url]).'" OR "'.Util::s($text).'"';
				static::$error_ids['same_urls_should_have_same_text'][$k]['name'] = max(array_flip($errs))===false ? 0 : max(array_flip($errs))+1;
				static::$errors[] = Util::s($ms[0][$k]);
				$errs[] = $ms[0][$k];
			}
		}
	static::add_error_to_html('same_urls_should_have_same_text', $errs, 'ignores_comment_out');
	}

	/**
	 * link_check
	 *
	 * @param   strings     $str
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
			$urls[] = static::correct_url($v);
		}
		$urls = array_unique($urls);

		// fragments
		preg_match_all("/ (?:id|name) *?= *?[\"']([^\"']+?)[\"']/i", $str, $fragments);

		// check
		$errs = array();
		foreach ($urls as $k => $url)
		{
			if ($url[0] == '#')
			{
				if ( ! in_array(substr($url, 1), $fragments[1]))
				{
					static::$error_ids['link_check'][$k]['id'] = Util::s($url);
					static::$error_ids['link_check'][$k]['str'] = 'Fragment Not Found: '.Util::s($url);
					static::$error_ids['link_check'][$k]['name'] = max(array_flip($errs))===false ? 0 : max(array_flip($errs))+1;
					static::$errors[] = Util::s($url);
					$errs[] = $url;
				}
				continue;
			}

			$headers = @get_headers($url);
			if ($headers !== false)
			{
				// OK TODO: think about redirection
				if (strpos($headers[0], ' 20') !== false || strpos($headers[0], ' 30') !== false) continue;

				// not OK
				static::$error_ids['link_check'][$k]['id'] = Util::s($url);
				static::$error_ids['link_check'][$k]['str'] = Util::s(substr($headers[0], strpos($headers[0], ' '))).': '.Util::s($url);
				static::$error_ids['link_check'][$k]['name'] = max(array_flip($errs))===false ? 0 : max(array_flip($errs))+1;
				static::$errors[] = Util::s($url);
				$errs[] = $url;
			}
			else
			{
				static::$error_ids['link_check'][$k]['id'] = 'Not Found: '.Util::s($url);
				static::$error_ids['link_check'][$k]['str'] = 'Not Found: '.Util::s($url);
				static::$error_ids['link_check'][$k]['name'] = max(array_flip($errs))===false ? 0 : max(array_flip($errs))+1;
				static::$errors[] = Util::s($url);
				$errs[] = $url;
			}
		}
		static::add_error_to_html('link_check', $errs, 'ignores_comment_out');
	}
}
