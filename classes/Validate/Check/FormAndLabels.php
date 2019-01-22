<?php
/**
 * * A11yc\Validate\Check\FormAndLabels
 *
 * @package    part of A11yc
 * @author     Jidaikobo Inc.
 * @license    The MIT License (MIT)
 * @copyright  Jidaikobo Inc.
 * @link       http://www.jidaikobo.com
 */
namespace A11yc\Validate\Check;

use A11yc\Element;
use A11yc\Validate;

class FormAndLabels extends Validate
{
	/**
	 * form and labels
	 *
	 * @param String $url
	 * @return Void
	 */
	public static function check($url)
	{
		$error_names = array(
			'labelless',
			'submitless',
			'duplicated_names',
			'unique_label',
			'contain_plural_form_elements',
			'lackness_of_form_ends',
		);

		Validate\Set::log($url, $error_names, self::$unspec, 1);
		$str = Element\Get::ignoredHtml($url);
		$ms = Element\Get::elementsByRe($str, 'ignores', 'tags');
		if ( ! $ms[1]) return;

		// is form exists?
		if ( ! in_array('form', $ms[1]))
		{
			Validate\Set::log($url, $error_names, self::$unspec, 4);
			return;
		}

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
			if (self::ignoreForm($uniqued_types, $uniqued_eles)) continue;

			// get action attribute to tell user which form cause error
			$attrs = Element\Get::attributes($v['form']);
			$action = isset($attrs['action']) ? $attrs['action'] : $v['form'];

			// labelless
			self::labelless($n, $url, $v, $action);

			// submitless
			self::submitless($n, $url, $v, $action, $uniqued_types, $uniqued_eles);

			// whole form
			$replace = preg_quote($v['form'], '/').'.+?\<\/form\>*';
			preg_match('/'.$replace.'/is', $tmp_html, $whole_form);
			$whole_form = Arr::get($whole_form, 0, '');

			// avoid get same form
			$start = mb_strpos($tmp_html, $v['form']) + mb_strlen($whole_form);
			$tmp_html = mb_substr($tmp_html, $start, null, "UTF-8");

			// unique_label
			self::uniqueLabel($k, $url, $whole_form, $v);

			// duplicated_names
			self::duplicatedNames($k, $url, $whole_form, $v, $action);

			// miss match "for" and "id"
			self::missMatchForAndId($n, $url, $ms);
			$n++;
		}

		foreach ($error_names as $error_name)
		{
			static::addErrorToHtml($url, $error_name, static::$error_ids[$url], 'ignores');
		}
	}

	/**
	 * endlress form
	 *
	 * @param String $url
	 * @param String $str
	 * @return Bool
	 */
	private static function lacknessOfFormEnds($url, $str)
	{
		if (substr_count($str, '<form') != substr_count($str, '</form'))
		{
			Validate\Set::error($url, 'lackness_of_form_ends', 0, '', '');
			return true;
		}

		Validate\Set::log($url, 'lackness_of_form_ends', self::$unspec, 2);
		return false;
	}

	/**
	 * collect form items
	 *
	 * @param Array $ms
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
				$forms[$n]['form']   = $m;
				$forms[$n]['labels'] = array();
				$forms[$n]['eles']   = array();
				$forms[$n]['types']  = array();
				$forms[$n]['names']  = array();
				continue;
			}

			// label, for, id, type
			if ($tag == '<label')
			{
				$forms[$n]['labels'][] = $m;
			}

			$forms[$n]['eles'][] = $ms[1][$k];

			$attrs = Element\Get::attributes($m);

			if (isset($attrs['type'])) $forms[$n]['types'][] = $attrs['type'];
		}

		// formless form elements. maybe JavaScript?
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
	 * @param Array $uniqued_types
	 * @param Array $uniqued_eles
	 * @return Bool
	 */
	private static function ignoreForm($uniqued_types, $uniqued_eles)
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
	 * @param Integer $n
	 * @param String $url
	 * @param Array $v
	 * @param String $action
	 * @return Bool
	 */
	private static function labelless($n, $url, $v, $action)
	{
		Validate\Set::errorAndLog(
			 ! $v['labels'],
			$url,
			'labelless',
			$n,
			$v['form'],
			$action
		);
	}

	/**
	 * submitless
	 *
	 * @param Integer $n
	 * @param String $url
	 * @param Array $v
	 * @param String $action
	 * @param Array $uniqued_types
	 * @param Array $uniqued_eles
	 * @return Void
	 */
	private static function submitless($n, $url, $v, $action, $uniqued_types, $uniqued_eles)
	{
		$eleless = ! in_array('input', $uniqued_eles) &&
						 ! in_array('button', $uniqued_eles);
		$typeless = ! in_array('button', $uniqued_eles) &&
							! in_array('submit', $uniqued_types) &&
							! in_array('image', $uniqued_types);

		Validate\Set::errorAndLog(
			$eleless || $typeless,
			$url,
			'submitless',
			$n,
			$v['form'],
			$action
		);
	}

	/**
	 * unique label
	 *
	 * @param Integer $k
	 * @param String $url
	 * @param String $whole_form
	 * @param String $v
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
					$mms = Element\Get::elementsByRe($each_label, 'ignores', 'imgs', true);
					foreach ($mms[0] as $in_img)
					{
						$attrs = Element\Get::attributes($in_img);
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

			$suspicion_labels = array_diff_assoc($ms[1],array_unique($ms[1]));
			$suspicion_labels = join(', ', array_unique($suspicion_labels));

			Validate\Set::errorAndLog(
				count($ms[1]) != count(array_unique($ms[1])),
				$url,
				'unique_label',
				$k,
				$v['form'],
				$suspicion_labels
			);
		}
	}

	/**
	 * duplicated names
	 *
	 * @param Integer $k
	 * @param String $url
	 * @param String $whole_form
	 * @param Array $v
	 * @param String $action
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
				$attrs = Element\Get::attributes($tag);
				if ( ! isset($attrs['name'])) continue;
				if (strpos($tag, 'checkbox') !== false || strpos($tag, 'radio') !== false) continue;

				Validate\Set::errorAndLog(
					in_array($attrs['name'], $name_arrs),
					$url,
					'duplicated_names',
					$k,
					$v['form'],
					$action
				);
				$name_arrs[] = $attrs['name'];
			}
		}
	}

	/**
	 * miss match "for" and "id"
	 *
	 * @param Integer $n
	 * @param String $url
	 * @param Array $ms
	 * @return Bool
	 */
	private static function missMatchForAndId($n, $url, $ms)
	{
		if (isset($ms[1]))
		{
			foreach ($ms[0] as $m)
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
						$ele_attrs = Element\Get::attributes($ele);
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
						$tstr = $label_m[0];
						Validate\Set::error($url, 'contain_plural_form_elements', $n, $tstr, $tstr);
					}
				}
			}
		}
	}
}
