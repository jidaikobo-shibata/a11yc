<?php
/**
 * A11yc\Validate_Form
 *
 * @package    part of A11yc
 * @version    1.0
 * @author     Jidaikobo Inc.
 * @license    The MIT License (MIT)
 * @copyright  Jidaikobo Inc.
 * @link       http://www.jidaikobo.com
 */
namespace A11yc;
class Validate_Form extends Validate
{
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
}
