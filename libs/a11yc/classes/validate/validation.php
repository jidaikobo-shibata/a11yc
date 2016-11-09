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
	 * alt attr of img
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
			// alt_attr_of_img
			$attrs = static::get_attributes($m);
			if ( ! array_key_exists('alt', $attrs))
			{
				static::$error_ids['alt_attr_of_img'][$k]['id'] = $ms[0][$k];
				static::$error_ids['alt_attr_of_img'][$k]['str'] = @basename(@$attrs['src']);
			}

			// role presentation
			if (isset($attrs['role']) && $attrs['role'] == 'presentation') continue;

			// alt_attr_of_blank_only
			if (
				array_key_exists('alt', $attrs) &&
				preg_match('/^[ 　]+?$/', $attrs['alt'])
			)
			{
				static::$error_ids['alt_attr_of_blank_only'][$k]['id'] = $ms[0][$k];
				static::$error_ids['alt_attr_of_blank_only'][$k]['str'] = @basename(@$attrs['src']);
			}

			// alt_attr_of_empty
			if (
				array_key_exists('alt', $attrs) &&
				empty($attrs['alt'])
			)
			{
				static::$error_ids['alt_attr_of_empty'][$k]['id'] = $ms[0][$k];
				static::$error_ids['alt_attr_of_empty'][$k]['str'] = @basename(@$attrs['src']);
			}
		}
		static::add_error_to_html('alt_attr_of_empty', static::$error_ids, 'ignores');
		static::add_error_to_html('alt_attr_of_img', static::$error_ids, 'ignores');
		static::add_error_to_html('alt_attr_of_blank_only', static::$error_ids, 'ignores');
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
			if ( ! isset($attrs['type'])) continue; // unless type it is recognized as a text at html5
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

		// to locate first element at error
		$ms = static::get_elements_by_re($str, 'tags');

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
			if ( ! isset($attrs['alt']) || ! isset($attrs['src'])) continue;
			if (empty($attrs['alt'])) continue;

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
		foreach ($ms[0] as $k => $m)
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
		if ( ! in_array('form', $ms[1])) return;

		// lackness_of_form_ends
		// this error is critical. so, if this error exists continue.
		if (substr_count($str, '<form') != substr_count($str, '</form'))
		{
			static::$error_ids['lackness_of_form_ends'][0]['id'] = $v['form'];
			static::$error_ids['lackness_of_form_ends'][0]['str'] = $action;
			static::add_error_to_html('lackness_of_form_ends', static::$error_ids, 'ignores');
			return;
		}

		// collect form items
		$form_items = array('<form ', '</form' ,'<label', '<input', '<select', '<texta', '<butto');
		$forms = array();
		$target = '';
		$n = 0;
		foreach ($ms[0] as $k => $m)
		{
			$tag = substr($m, 0, 6);
			if ( ! in_array($tag, $form_items)) continue;

			if ($tag == '</form')
			{
				$n++;
				continue;
			}

			// prepare
			if ($tag == '<form ')
			{
				$n++;
				$forms[$n]['form'] = $m;
				$forms[$n]['labels'] = array();
				$forms[$n]['eles'] = array();
				$forms[$n]['fors'] = array();
				$forms[$n]['ids'] = array();
				$forms[$n]['types'] = array();
				$forms[$n]['names'] = array();
				continue;
			}

			// label, for, id, type
			if ($tag == '<label')
			{
				$forms[$n]['labels'][] = $m;
			}
			else
			{
				$forms[$n]['eles'][] = $ms[1][$k];
			}

			$attrs = static::get_attributes($m);
			if (isset($attrs['for']))  $forms[$n]['fors'][] = $attrs['for'];
			if (isset($attrs['id']))   $forms[$n]['ids'][] = $attrs['id'];
			if (isset($attrs['type'])) $forms[$n]['types'][] = $attrs['type'];
		}

			// formless form elements.  maybe JavaScript?
		// there might be plural forms. so do not remove this.
		foreach ($forms as $k => $v)
		{
			if ( ! isset($v['form'])) unset($forms[$k]);
		}

		// errors
		$n = 0;
		$tmp_html = $str;
		foreach ($forms as $k => $v)
		{
			// ignore form
			$uniqued_types = array_unique($v['types']);
			$uniqued_eles = array_unique($v['eles']);
			if (
				$uniqued_eles == array('button') || // button only
				array_diff($uniqued_types, array('submit', 'hidden')) == array() || // submit and hidden
				(
					$uniqued_eles == array('button') &&
					array_diff($uniqued_types, array('submit', 'hidden')) == array()
				) // button, submit and hidden
			)
			{
				continue;
			}

			// get action attribute to tell user which form cause error
			$attrs = static::get_attributes($v['form']);
			$action = isset($attrs['action']) ? $attrs['action'] : $k;

			// labelless
			if ( ! $v['labels'])
			{
				static::$error_ids['labelless'][$n]['id'] = $v['form'];
				static::$error_ids['labelless'][$n]['str'] = $action;
			}

			// submitless
			if (
				( ! in_array('input', $uniqued_eles) && ! in_array('button', $uniqued_eles)) ||
				( ! in_array('submit', $uniqued_types) && ! in_array('image', $uniqued_types))
			)
			{
				static::$error_ids['submitless'][$n]['id'] = $v['form'];
				static::$error_ids['submitless'][$n]['str'] = $action;
			}

			// whole form
			$replace = str_replace(
				array('<', '>', '/', '.', '[', ']'),
				array('\<', '\>', '\/', '\.', '\[', '\]'),
				$v['form']);
			preg_match('/'.$replace.'.+?<\/form\>*/is', $tmp_html, $whole_form);
			$whole_form = $whole_form[0];

			// avoid get same form
			$tmp_html = mb_substr(
				$tmp_html,
				mb_strpos($tmp_html, $v['form']) + mb_strlen($whole_form),
				null,
				"UTF-8");

			// unique_label
			preg_match_all("/\<label[^\>]*?\>(.+?)\<\/label\>/is", $whole_form, $ms);
			if (isset($ms[1]) && count($ms[1]) != count(array_unique($ms[1])))
			{
				static::$error_ids['unique_label'][$k]['id'] = $v['form'];
				static::$error_ids['unique_label'][$k]['str'] = $action;
			}

			// duplicated_names
			preg_match_all("/\<(?:input|select|textarea) .+?\>/si", $whole_form, $names);
			if (isset($names[0]))
			{
				$name_arrs = array();
				foreach ($names[0] as $tag)
				{
					$attrs = static::get_attributes($tag);
					if ( ! isset($attrs['name'])) continue;
					if (strpos($tag, 'checkbox') !== false || strpos($tag, 'radio') !== false) continue;
					if (in_array($attrs['name'], $name_arrs))
					{
						static::$error_ids['duplicated_names'][$k]['id'] = $v['form'];
						static::$error_ids['duplicated_names'][$k]['str'] = $action;
					}
					$name_arrs[] = $attrs['name'];
				}
			}

			// miss match "for" and "id"
			if (isset($ms[1]))
			{
				foreach ($ms[0] as $k => $m)
				{
					// check tacit label
					preg_match_all("/\<(?:input|select|textarea) .+?\>/si", $m, $mmms);
					if ($mmms[0])
					{
						// is exists plural labelable elements?
						// submit seems labelable...
						// see https://www.w3.org/TR/html/sec-forms.html and search "labelable"
						$ele_types = array();
						foreach ($mmms[0] as $ele)
						{
							$ele_attrs = static::get_attributes($ele);
							if (strtolower($ele_attrs['type']) == 'hidden') continue;
							$ele_types[] = $ele_attrs['type'];
						}

						// place
						preg_match('/\<label[^\>]*?\>/is', $m, $label_m);

						// tacit label can contain plural labelable elements at html4.01.
						// but I think it confuses users.  so, mention it.
						if (count($ele_types) >= 2)
						{
							static::$error_ids['contain_plural_form_elements'][$n]['id'] = $label_m[0];
							static::$error_ids['contain_plural_form_elements'][$n]['str'] = $label_m[0];
						}

			// 			// is for and id are valid?
			// 			$inner_attrs_label = static::get_attributes($m);
			// 			$for = isset($inner_attrs_label['for']) ? $inner_attrs_label['for'] : false;

			// 			// exclude id in tacit label
			// 			$inner_attrs_form = static::get_attributes($mmms[0][0]);
			// 			$id = isset($inner_attrs_form['id']) ? $inner_attrs_form['id'] : false;

			// 			// if for exists compare with for and id
			// 			if ($for)
			// 			{
			// 				if ($id != $for)
			// 				{
			// 					static::$error_ids['tacit_label_miss_maches'][$n]['id'] = $v['form'];
			// 					static::$error_ids['tacit_label_miss_maches'][$n]['str'] = $action.': '.$inner_attrs_form['id'].', '.$for;
			// 				}
			// 			}
			// 			// for is not exist, therefore this element's id is ignorable
			// 			else if ($id && in_array($id, $v['ids']))
			// 			{
			// 				$v['ids'] = array_flip($v['ids']);
			// 				unset($v['ids'][$id]);
			// 				$v['ids'] = array_flip($v['ids']);
			// 			}
					}
				}
			}

			// if (isset($v['fors']) && isset($v['ids']))
			// {
			// 	// id can exist without for...
			// 	// $miss_maches_fors = array_diff($v['fors'], $v['ids']);
			// 	// $miss_maches_ids = array_diff($v['ids'], $v['fors']);
			// 	// $miss_maches = array_merge($miss_maches_ids, $miss_maches_fors);
			// 	$miss_maches = array_diff($v['fors'], $v['ids']);
			// 	if ($miss_maches)
			// 	{
			// 		static::$error_ids['label_miss_maches'][$n]['id'] = $v['form'];
			// 		static::$error_ids['label_miss_maches'][$n]['str'] = $action.': '.join(', ', $miss_maches);
			// 	}
			// }
			$n++;
		}
		static::add_error_to_html('labelless', static::$error_ids, 'ignores');
		static::add_error_to_html('submitless', static::$error_ids, 'ignores');
		static::add_error_to_html('duplicated_names', static::$error_ids, 'ignores');
		static::add_error_to_html('unique_label', static::$error_ids, 'ignores');
		static::add_error_to_html('contain_plural_form_elements', static::$error_ids, 'ignores');
		// static::add_error_to_html('label_miss_maches', static::$error_ids, 'ignores');
		// static::add_error_to_html('tacit_label_miss_maches', static::$error_ids, 'ignores');
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

		$errs1 = array();
		$errs2 = array();
		foreach ($ms[0] as $k => $m)
		{
			// newline character must not exists in attr
			$attrs = static::get_attributes($m);
			foreach ($attrs as $key => $val)
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
			'pdf',
			'doc',
			'docx',
			'xls',
			'xlsx',
			'ppt',
			'pptx',
			'zip',
			'tar',
		);

		foreach ($ms[1] as $k => $m)
		{
			foreach ($suspicious as $kk => $vv)
			{
				$m = str_replace("'", '"', $m);
				if (strpos($m, '.'.$vv.'"') !== false)
				{
					$attrs = static::get_attributes($m);

					if ( ! isset($attrs['href'])) continue;
					$href = strtolower($attrs['href']);
					$inner = substr($ms[0][$k], strpos($ms[0][$k], '>') + 1);
					$inner = str_replace('</a>', '', $inner);
					$f_inner = $inner;

					// allow application name
					if (
						(($vv == 'doc' || $vv == 'docx') && strpos($href, 'word')  !== false) ||
						(($vv == 'xls' || $vv == 'xlsx') && strpos($href, 'excel') !== false) ||
						(($vv == 'ppt' || $vv == 'pptx') && strpos($href, 'power') !== false)
					)
					{
						$f_inner.= 'doc,docx,xls,xlsx,ppt,pptx';
					}

					if (
						strpos(strtolower($f_inner), $vv) === false || // lacknesss of file type
						preg_match("/\d/", $f_inner) == false // lacknesss of filesize?
					)
					{
						static::$error_ids['tell_user_file_type'][$k]['id'] = $ms[0][$k];
						static::$error_ids['tell_user_file_type'][$k]['str'] = $href.': '.$inner;
					}
				}
			}
		}
		static::add_error_to_html('tell_user_file_type', static::$error_ids, 'ignores');
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

			// ignore zero refresh
			// see http://www.ciaj.or.jp/access/web/docs/WCAG-TECHS/H76.html
			if (
				array_key_exists('http-equiv', $attrs) &&
				array_key_exists('content', $attrs) &&
				$attrs['http-equiv'] == 'refresh' &&
				(
					trim(substr($attrs['content'], 0, strpos($attrs['content'], ';'))) != '0' ||
					(strpos($attrs['content'], ';') === false && trim($attrs['content']) != '0')
				)
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
