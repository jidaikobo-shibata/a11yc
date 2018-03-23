<?php
/**
 * A11yc\Validate\FormAndLabels
 *
 * @package    part of A11yc
 * @author     Jidaikobo Inc.
 * @license    The MIT License (MIT)
 * @copyright  Jidaikobo Inc.
 * @link       http://www.jidaikobo.com
 */
namespace A11yc\Validate;

class FormAndLabels extends Validate
{
	/**
	 * form and labels
	 *
	 * @param  String $url
	 * @return Void
	 */
	public static function check($url)
	{
		$str = Element::ignoreElements(static::$hl_htmls[$url]);
		$ms = Element::getElementsByRe($str, 'ignores', 'tags');
		if ( ! $ms[1]) return;

		// is form exists?
		if ( ! in_array('form', $ms[1])) return;

		// lackness_of_form_ends
		// this error is critical. so, if this error exists continue.
		if (self::lacknessOfFormEnds($url, $str)) return;

		// collect form items
		$forms = self::collectFormItems($ms);

		// errors
		$n = 0;
		$tmp_html = $str;
		foreach ($forms as $k => $v)
		{
			$uniqued_types = array_unique($v['types']);
			$uniqued_eles = array_unique($v['eles']);

			// ignore form
			if (self::ignoreForm($v, $uniqued_types, $uniqued_eles)) continue;

			// get action attribute to tell user which form cause error
			$attrs = Element::getAttributes($v['form']);
			$action = isset($attrs['action']) ? $attrs['action'] : $k;

			// labelless
			self::labelless($n, $url, $v, $action);

			// submitless
			self::submitless($n, $url, $v, $action, $uniqued_types, $uniqued_eles);

			// whole form
			$replace = str_replace(
				array('<', '>', '/', '.', '[', ']'),
				array('\<', '\>', '\/', '\.', '\[', '\]'),
				$v['form']);
			preg_match('/'.$replace.'.+?<\/form\>*/is', $tmp_html, $whole_form);
			$whole_form = Arr::get($whole_form, 0, '');

			// avoid get same form
			$tmp_html = mb_substr(
				$tmp_html,
				mb_strpos($tmp_html, $v['form']) + mb_strlen($whole_form),
				null,
				"UTF-8");

			// unique_label
			self::uniqueLabel($k, $url, $whole_form, $v);

			// duplicated_names
			self::duplicatedNames($k, $url, $whole_form, $v, $action);

			// miss match "for" and "id"
			self::missMatchForAndId($n, $url, $ms);

			$n++;
		}
		static::addErrorToHtml($url, 'labelless', static::$error_ids[$url], 'ignores');
		static::addErrorToHtml($url, 'submitless', static::$error_ids[$url], 'ignores');
		static::addErrorToHtml($url, 'duplicated_names', static::$error_ids[$url], 'ignores');
		static::addErrorToHtml($url, 'unique_label', static::$error_ids[$url], 'ignores');
		static::addErrorToHtml($url, 'contain_plural_form_elements', static::$error_ids[$url], 'ignores');
	}

	/**
	 * endlress form
	 *
	 * @param  String $url
	 * @param  String $str
	 * @return Bool
	 */
	private static function lacknessOfFormEnds($url, $str)
	{
		if (substr_count($str, '<form') != substr_count($str, '</form'))
		{
			static::$error_ids[$url]['lackness_of_form_ends'][0]['id'] = '';
			static::$error_ids[$url]['lackness_of_form_ends'][0]['str'] = '';
			static::addErrorToHtml($url, 'lackness_of_form_ends', static::$error_ids[$url], 'ignores');
			return true;
		}
		return false;
	}

	/**
	 * collect form items
	 *
	 * @param  Array $ms
	 * @return Array
	 */
	private static function collectFormItems($ms)
	{
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

			$attrs = Element::getAttributes($m);
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
		return $forms;
	}

	/**
	 * ignore form
	 *
	 * @param  Array $v
	 * @param  Array $uniqued_types
	 * @param  Array $uniqued_eles
	 * @return Bool
	 */
	private static function ignoreForm($v, $uniqued_types, $uniqued_eles)
	{
		if (
			$uniqued_eles == array('button') || // button only
			array_diff($uniqued_types, array('submit', 'hidden')) == array() || // submit and hidden
			(
				$uniqued_eles == array('button') &&
				array_diff($uniqued_types, array('submit', 'hidden')) == array()
			) // button, submit and hidden
		)
		{
			return true;
		}
		return false;
	}

	/**
	 * labelless
	 *
	 * @param  Integer $n
	 * @param  String $url
	 * @param  Array $v
	 * @param  String $action
	 * @return Bool
	 */
	private static function labelless($n, $url, $v, $action)
	{
		if ( ! $v['labels'])
		{
			static::$error_ids[$url]['labelless'][$n]['id'] = $v['form'];
			static::$error_ids[$url]['labelless'][$n]['str'] = $action;
		}
	}

	/**
	 * submitless
	 *
	 * @param  Integer $n
	 * @param  String $url
	 * @param  Array $v
	 * @param  String $action
	 * @param  Array $uniqued_types
	 * @param  Array $uniqued_eles
	 * @return Bool
	 */
	private static function submitless($n, $url, $v, $action, $uniqued_types, $uniqued_eles)
	{
		if (
			( ! in_array('input', $uniqued_eles) && ! in_array('button', $uniqued_eles)) ||
			(
				! in_array('button', $uniqued_eles) &&
				! in_array('submit', $uniqued_types) &&
				! in_array('image', $uniqued_types)
			)
		)
		{
			static::$error_ids[$url]['submitless'][$n]['id'] = $v['form'];
			static::$error_ids[$url]['submitless'][$n]['str'] = $action;
		}
	}

	/**
	 * unique label
	 *
	 * @param  Integer $k
	 * @param  String $url
	 * @param  String $whole_form
	 * @param  String $v
	 * @return Bool
	 */
	private static function uniqueLabel($k, $url, $whole_form, $v)
	{
		preg_match_all("/\<label[^\>]*?\>(.+?)\<\/label\>/is", $whole_form, $ms);

		if (isset($ms[1]))
		{
			foreach ($ms[1] as $kk => $each_label)
			{
				$alt = '';
				if (strpos($each_label, '<img') !== false)
				{
					$mms = Element::getElementsByRe($each_label, 'ignores', 'imgs', true);
					foreach ($mms[0] as $in_img)
					{
						$attrs = Element::getAttributes($in_img);
						foreach ($attrs as $kkk => $vvv)
						{
							if (strpos($kkk, 'alt') !== false)
							{
								$alt.= $vvv;
							}
						}
					}
					$alt = trim($alt);
				}
				$ms[1][$kk] = trim(strip_tags($each_label)).$alt;
			}

			if (count($ms[1]) != count(array_unique($ms[1])))
			{
				$suspicion_labels = array_diff_assoc($ms[1],array_unique($ms[1]));
				$suspicion_labels = join(', ', array_unique($suspicion_labels));

				static::$error_ids[$url]['unique_label'][$k]['id'] = $v['form'];
				static::$error_ids[$url]['unique_label'][$k]['str'] = $suspicion_labels;
			}
		}
	}

	/**
	 * duplicated names
	 *
	 * @param  Integer $k
	 * @param  String $url
	 * @param  String $whole_form
	 * @param  Array $v
	 * @param  String $action
	 * @return Bool
	 */
	private static function duplicatedNames($k, $url, $whole_form, $v, $action)
	{
		preg_match_all("/\<(?:input|select|textarea) .+?\>/si", $whole_form, $names);
		if (isset($names[0]))
		{
			$name_arrs = array();
			foreach ($names[0] as $tag)
			{
				$attrs = Element::getAttributes($tag);
				if ( ! isset($attrs['name'])) continue;
				if (strpos($tag, 'checkbox') !== false || strpos($tag, 'radio') !== false) continue;
				if (in_array($attrs['name'], $name_arrs))
				{
					static::$error_ids[$url]['duplicated_names'][$k]['id'] = $v['form'];
					static::$error_ids[$url]['duplicated_names'][$k]['str'] = $action;
				}
				$name_arrs[] = $attrs['name'];
			}
		}
	}

	/**
	 * miss match "for" and "id"
	 *
	 * @param  Integer $n
	 * @param  String $url
	 * @param  Array $ms
	 * @return Bool
	 */
	private static function missMatchForAndId($n, $url, $ms)
	{
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
						$ele_attrs = Element::getAttributes($ele);
						if ( ! isset($ele_attrs['type'])) continue;
						if (strtolower($ele_attrs['type']) == 'hidden') continue;
						$ele_types[] = $ele_attrs['type'];
					}

					// place
					preg_match('/\<label[^\>]*?\>/is', $m, $label_m);

					// tacit label can contain plural labelable elements at html4.01.
					// but I think it confuses users.  so, mention it.
					if (count($ele_types) >= 2)
					{
						static::$error_ids[$url]['contain_plural_form_elements'][$n]['id'] = $label_m[0];
						static::$error_ids[$url]['contain_plural_form_elements'][$n]['str'] = $label_m[0];
					}
				}
			}
		}
	}
}
