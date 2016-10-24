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
	public static function alt_attr_of_img()
	{
		$str = static::ignore_elements(static::$hl_html);

		$ms = static::get_elements_by_re($str, 'imgs');
		if ( ! $ms[1]) return;

		foreach ($ms[1] as $k => $m)
		{
			$attrs = static::get_attributes($m);
			if ( ! array_key_exists('alt', $attrs))
			{
				static::$error_ids['alt_attr_of_img'][$k]['id'] = $ms[0][$k];
				static::$error_ids['alt_attr_of_img'][$k]['str'] = @basename(@$attrs['src']);
			}
		}
		static::add_error_to_html('alt_attr_of_img', static::$error_ids, 'ignores');
	}

	/**
	 * empty alt attr of img inside a
	 *
	 * @return  void
	 */
	public static function empty_alt_attr_of_img_inside_a()
	{
		$str = static::ignore_elements(static::$hl_html);

		$ms = static::get_elements_by_re($str, 'anchors_and_values');
		if ( ! $ms[2]) return;

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
				static::$error_ids['empty_alt_attr_of_img_inside_a'][$k]['id'] = $ms[0][$k];
				static::$error_ids['empty_alt_attr_of_img_inside_a'][$k]['str'] = @basename(@$attrs['src']);
			}
		}
		static::add_error_to_html('empty_alt_attr_of_img_inside_a', static::$error_ids, 'ignores');
	}

	/**
	 * here link
	 *
	 * @return  bool
	 */
	public static function here_link()
	{
		$str = static::ignore_elements(static::$hl_html);

		$ms = static::get_elements_by_re($str, 'anchors_and_values');
		if ( ! $ms[2]) return;

		foreach ($ms[2] as $k => $m)
		{
			$m = trim($m);
			if ($m == A11YC_LANG_HERE)
			{
				static::$error_ids['here_link'][$k]['id'] = $ms[0][$k];
				static::$error_ids['here_link'][$k]['str'] = @$ms[1][$k];
			}
		}
		static::add_error_to_html('here_link', static::$error_ids, 'ignores');
	}

	/**
	 * area has alt
	 *
	 * @return  bool
	 */
	public static function area_has_alt()
	{
		$str = static::ignore_elements(static::$hl_html);

		$ms = static::get_elements_by_re($str, 'tags');
		if ( ! $ms[0]) return;

		foreach ($ms[0] as $k => $m)
		{
			if (substr($m, 0, 5) !== '<area') continue;
			$attrs = static::get_attributes($m);
			if ( ! isset($attrs['alt']) || empty($attrs['alt']))
			{
				static::$error_ids['area_has_alt'][$k]['id'] = $ms[0][$k];
				static::$error_ids['area_has_alt'][$k]['str'] = @basename(@$attrs['href']);
			}
		}
		static::add_error_to_html('area_has_alt', static::$error_ids, 'ignores');
	}

	/**
	 * is img input has alt
	 *
	 * @return  bool
	 */
	public static function img_input_has_alt()
	{
		$str = static::ignore_elements(static::$hl_html);

		$ms = static::get_elements_by_re($str, 'tags');
		if ( ! $ms[0]) return;

		foreach($ms[0] as $k => $m)
		{
			if (substr($m, 0, 6) !== '<input') continue;
			$attrs = static::get_attributes($m);
			if ( ! isset($attrs['type'])) continue; // unless type it is recognized as a text
			if (isset($attrs['type']) && $attrs['type'] != 'image') continue;

			if ( ! isset($attrs['alt']) || empty($attrs['alt']))
			{
				static::$error_ids['img_input_has_alt'][$k]['id'] = $ms[0][$k];
				static::$error_ids['img_input_has_alt'][$k]['str'] = @basename(@$attrs['src']);
			}
		}
		static::add_error_to_html('img_input_has_alt', static::$error_ids, 'ignores');
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
		foreach ($secs as $k => $v)
		{
			if (strlen($v) != 3) continue; // skip non heading
			if (substr($v, 0, 2) != '<h') continue; // skip non heading
			$current_level = intval($v[2]);

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
		foreach ($suspicious_opens as $k => $v)
		{
			static::$error_ids['suspicious_opens'][$k]['id'] = '<'.$v;
			static::$error_ids['suspicious_opens'][$k]['str'] = $v;
		}
		static::add_error_to_html('suspicious_opens', static::$error_ids, 'ignores');

		foreach ($suspicious_ends as $k => $v)
		{
			static::$error_ids['suspicious_ends'][$k]['id'] = '<'.$v;
			static::$error_ids['suspicious_ends'][$k]['str'] = $v;
		}
		static::add_error_to_html('suspicious_ends', static::$error_ids, 'ignores');
	}

	/**
	 * same alt and filename of img
	 *
	 * @return  void
	 */
	public static function same_alt_and_filename_of_img()
	{
		$str = static::ignore_elements(static::$hl_html);

		$ms = static::get_elements_by_re($str, 'imgs');
		if ( ! $ms[1]) return;

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
				static::$error_ids['same_alt_and_filename_of_img'][$k]['id'] = $ms[0][$k];
				static::$error_ids['same_alt_and_filename_of_img'][$k]['str'] = '"'.$filename.'"';
			}
		}
		static::add_error_to_html('same_alt_and_filename_of_img', static::$error_ids, 'ignores');
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

		preg_match_all("/([^\x01-\x7E][ 　][ 　]+[^\x01-\x7E])/iu", $str, $ms);
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
		$body_html = static::ignore_elements(static::$hl_html);

		$banneds = array(
			'<center',
			'<font',
			'<blink',
			'<marquee',
		);

		$ms = static::get_elements_by_re($body_html, 'tags');
		if ( ! $ms[0]) return;

		foreach ($ms[0] as $k => $m)
		{
			foreach ($banneds as $banned)
			{
				if (substr($m, 0, strlen($banned)) == $banned)
				{
					static::$error_ids['meanless_element'][$k]['id'] = $m;
					static::$error_ids['meanless_element'][$k]['str'] = $m;
					break;
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
		if ( ! $ms[1]) return;
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
				static::$error_ids['style_for_structure'][$k]['id'] = $ms[0][$k];
				static::$error_ids['style_for_structure'][$k]['str'] = $m;
			}
		}
		static::add_error_to_html('style_for_structure', static::$error_ids, 'ignores');
	}

	/**
	 * form and labels
	 *
	 * @return  void
	 */
	public static function form_and_labels()
	{
		$str = static::ignore_elements(static::$hl_html);

		$ms = static::get_elements_by_re($str, 'tags');
		if ( ! $ms[1]) return;

		// is form exists?
		$tags = array_map(function($v){return substr($v, 0, 6);}, $ms[0]);
		if ( ! in_array('<form ', $tags)) return;

		// collect form items
		$form_items = array('<form ', '<label', '<input', '<select', '<texta', '<butto');
		$forms = array();
		$target = '';
		$n = 0;
		foreach ($ms[0] as $k => $m)
		{
			if ( ! in_array(substr($m, 0, 6), $form_items)) continue;

			// target
			if (substr($m, 0, 6) == '<form ')
			{
				$n++;
				$forms[$n]['form'] = $m;
				continue;
			}

			// label, for, id, type
			if (substr($m, 0, 6) == '<label')
			{
				$forms[$n]['labels'][] = $m;
			}
			$attrs = static::get_attributes($m);
			if (isset($attrs['for']))
			{
				$forms[$n]['fors'][] = $attrs['for'];
			}
			if (isset($attrs['id']))
			{
				$forms[$n]['ids'][] = $attrs['id'];
			}
			if (isset($attrs['type']))
			{
				$forms[$n]['types'][] = $attrs['type'];
			}
		}

		// errors
		$n = 0;
		foreach ($forms as $k => $v)
		{
			if ( ! isset($v['types'])) continue;

			// ignore hidden only
			$each_types = array_unique($v['types']);
			if (
				isset($each_types[0]) &&
				count($each_types) == 1 &&
				$each_types[0] == 'hidden'
			)
			{
				continue;
			}

			// to get action attribute
			$attrs = static::get_attributes($v['form']);
			$action = isset($attrs['action']) ? $attrs['action'] : $k;

			// labelless
			if ( ! isset($v['labels']))
			{
				static::$error_ids['labelless'][$n]['id'] = $v['form'];
				static::$error_ids['labelless'][$n]['str'] = $action;
			}

			// lackness_of_form_ends
			// this error is critical. so, if this error exists continue.
			if (empty($errs3) && substr_count($str, '</form') != count($forms))
			{
				static::$error_ids['lackness_of_form_ends'][$k]['id'] = $v['form'];
				static::$error_ids['lackness_of_form_ends'][$k]['str'] = $action;

				// after here, use '</form'. so lackness of form ends is critical.
				continue;
			}

			// whole form
			$form_begin = mb_strpos($str, $v['form'], false, "UTF-8");
			$length = mb_strpos($str, '</form', $form_begin + 1, "UTF-8") - $form_begin + strlen('</form>');
			$whole_form = mb_substr($str, $form_begin, $length, "UTF-8");

			// unique_label
			preg_match_all("/\<label[^\>]*?\>(.+?)\<\/label\>/is", $whole_form, $ms);
			if (isset($ms[1]) && count($ms[1]) != count(array_unique($ms[1])))
			{
				static::$error_ids['unique_label'][$k]['id'] = $v['form'];
				static::$error_ids['unique_label'][$k]['str'] = $action;
			}

			// miss match for and id
			if (isset($ms[1]))
			{
				foreach ($ms[0] as $k => $m)
				{
					// check tacit label
					preg_match_all("/\<(?:input|select|textarea) .+?\>/si", $m, $mmms);
					if ($mmms[0])
					{
						// tacit label can contain single for element
						if (count($mmms[0]) >= 2)
						{
							static::$error_ids['contain_plural_form_elements'][$n]['id'] = $mmms[0][0];
							static::$error_ids['contain_plural_form_elements'][$n]['str'] = $mmms[0][0];
						}

						// is for and id are valid?
						$inner_attrs_label = static::get_attributes($m);
						$for = isset($inner_attrs_label['for']) ? $inner_attrs_label['for'] : false;

						// exclude id in tacit label
						$inner_attrs_form = static::get_attributes($mmms[0][0]);
						$id = isset($inner_attrs_form['id']) ? $inner_attrs_form['id'] : false;

						// if for exists compare with for and id
						if ($for)
						{
							if ($id != $for)
							{
								static::$error_ids['tacit_label_miss_maches'][$n]['id'] = $v['form'];
								static::$error_ids['tacit_label_miss_maches'][$n]['str'] = $action.': '.$inner_attrs_form['id'].', '.$for;
							}
						}
						// for is not exist, therefore this element's id is ignorable
						else if ($id && in_array($id, $v['ids']))
						{
							$v['ids'] = array_flip($v['ids']);
							unset($v['ids'][$id]);
							$v['ids'] = array_flip($v['ids']);
						}
					}
				}
			}

			if (isset($v['fors']) && isset($v['ids']))
			{
				$miss_maches_ids = array_diff($v['ids'], $v['fors']);
				$miss_maches_fors = array_diff($v['fors'], $v['ids']);
				$miss_maches = array_merge($miss_maches_ids, $miss_maches_fors);
				if ($miss_maches)
				{
					static::$error_ids['label_miss_maches'][$n]['id'] = $v['form'];
					static::$error_ids['label_miss_maches'][$n]['str'] = $action.': '.join(', ', $miss_maches);
				}

			}

			$n++;
		}
		static::add_error_to_html('labelless', static::$error_ids, 'ignores');
		static::add_error_to_html('label_miss_maches', static::$error_ids, 'ignores');
		static::add_error_to_html('lackness_of_form_ends', static::$error_ids, 'ignores');
		static::add_error_to_html('unique_label', static::$error_ids, 'ignores');
		static::add_error_to_html('tacit_label_miss_maches', static::$error_ids, 'ignores');
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

		foreach ($ms[1] as $k => $m)
		{
			$attrs = static::get_attributes($m);
			if (isset($attrs['suspicious']))
			{
				static::$error_ids['duplicated_attributes'][$k]['id'] = $ms[0][$k];
				static::$error_ids['duplicated_attributes'][$k]['str'] = $m;
			}
		}
		static::add_error_to_html('duplicated_attributes', static::$error_ids, 'ignores');
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
		foreach ($ms[1] as $k => $m)
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
				static::$error_ids['unbalanced_quotation'][$k]['id'] = $ms[0][$k];
				static::$error_ids['unbalanced_quotation'][$k]['str'] = $m;
			}

			if (A11YC_LANG != 'ja') continue;
			// multi-byte space
			$tag = preg_replace("/(\".+?\"|'.+?')/", '', $tag);
			if (strpos($tag, '　') !== false)
			{
				static::$error_ids['cannot_contain_multibyte_space'][$k]['id'] = $ms[0][$k];
				static::$error_ids['cannot_contain_multibyte_space'][$k]['str'] = $m;
			}
		}
		static::add_error_to_html('unbalanced_quotation', static::$error_ids, 'ignores');
		static::add_error_to_html('cannot_contain_multibyte_space', static::$error_ids, 'ignores');
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
						static::$error_ids['tell_user_file_type'][$kk]['id'] = $ms[0][$k];
						static::$error_ids['tell_user_file_type'][$kk]['str'] = $val;
//						$errs[$k] = $ms[0][$k]; $k?
					}
				}
			}
		}
		static::add_error_to_html('tell_user_file_type', static::$error_ids, 'ignores');
	}

	/**
	 * titleless
	 *
	 * @return  void
	 */
	public static function titleless()
	{
		$str = static::ignore_elements(static::$hl_html);

		if (strpos(strtolower($str), '<title') === false)
		{
			static::$error_ids['titleless'][0]['id'] = '';
			static::$error_ids['titleless'][0]['str'] = '';
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
		// do not use static::ignore_elements() in case it is in comment out

		if ( ! preg_match("/\<html[^\>]*?lang *?= *?[^\>]*?\>/i", static::$hl_html))
		{
			static::$error_ids['langless'][0]['id'] = '<html';
			static::$error_ids['langless'][0]['str'] = '<html';
		}
		static::add_error_to_html('langless', static::$error_ids);
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
			static::$error_ids['same_page_title_in_same_site'][$k]['id'] = $title;
			static::$error_ids['same_page_title_in_same_site'][$k]['str'] = $title;
		}
		static::add_error_to_html('same_page_title_in_same_site', static::$error_ids);
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
				static::$error_ids['same_urls_should_have_same_text'][$k]['id'] = $ms[0][$k];
				static::$error_ids['same_urls_should_have_same_text'][$k]['str'] = $url.': ('.mb_strlen($urls[$url], "UTF-8").') "'.$urls[$url].'" OR ('.mb_strlen($text, "UTF-8").') "'.$text.'"';
			}
		}
	static::add_error_to_html('same_urls_should_have_same_text', static::$error_ids, 'ignores_comment_out');
	}

	/**
	 * link_check
	 *
	 * @return  void
	 */
	public static function link_check()
	{
		$str = static::ignore_comment_out(static::$hl_html);

		// ordinary urls
		preg_match_all("/ (?:href|src|cite|data|poster|action) *?= *?[\"']([^\"']+?)[\"']/i", $str, $ms);

		// og
		$mms = static::get_elements_by_re($str, 'tags');
		if (isset($mms[0]))
		{
			foreach ($mms[0] as $m)
			{
				if (strpos($m, '<meta') === false) continue;
				$attrs = static::get_attributes($m);
				if ( ! isset($attrs['property'])) continue;

				if ($attrs['property'] == 'og:url' && isset($attrs['content']))
				{
					$ms[1][] = $attrs['content'];
				}
				if ($attrs['property'] == 'og:image' && isset($attrs['content']))
				{
					$ms[1][] = $attrs['content'];
				}
			}
		}

		$urls = array();
		foreach ($ms[1] as $k => $v)
		{
			if (static::is_ignorable($ms[0][$k])) continue;
			$urls[$v] = static::correct_url($v);
		}

		// fragments
		preg_match_all("/ (?:id|name) *?= *?[\"']([^\"']+?)[\"']/i", $str, $fragments);

		// check
		$k = 0;
		foreach ($urls as $original => $url)
		{
			if ($url[0] == '#')
			{
				if ( ! in_array(substr($url, 1), $fragments[1]))
				{
					static::$error_ids['link_check'][$k]['id'] = $original;
					static::$error_ids['link_check'][$k]['str'] = 'Fragment Not Found: '.$original;
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
				static::$error_ids['link_check'][$k]['id'] = $original;
				static::$error_ids['link_check'][$k]['str'] = substr($headers[0], strpos($headers[0], ' ')).': '.$original;
			}
			else
			{
				static::$error_ids['link_check'][$k]['id'] = 'Not Found: '.$original;
				static::$error_ids['link_check'][$k]['str'] = 'Not Found: '.$original;
			}
			$k++;
		}
		static::add_error_to_html('link_check', static::$error_ids, 'ignores_comment_out');
	}
}
