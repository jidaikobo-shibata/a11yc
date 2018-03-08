<?php
/**
 * A11yc\Validate\NotLabelButTitle
 *
 * @package    part of A11yc
 * @author     Jidaikobo Inc.
 * @license    The MIT License (MIT)
 * @copyright  Jidaikobo Inc.
 * @link       http://www.jidaikobo.com
 */
namespace A11yc\Validate;

class NotLabelButTitle extends Validate
{
	/**
	 * not_label_but_title
	 *
	 * @return  bool
	 */
	public static function check($url)
	{
		$str = static::ignoreElements(static::$hl_htmls[$url]);
		$ms = static::getElementsByRe($str, 'ignores', 'tags');
		if ( ! $ms[0]) return;

		// labels_eles
		$eles = array();
		$fors = array();
		foreach ($ms[1] as $k => $m)
		{
			if ( ! in_array($m, array('label', 'input', 'textarea', 'select'))) continue;

			$attrs = static::getAttributes($ms[0][$k]);
			if ($m == 'label')
			{
				$eles[$k]['tag'] = $ms[0][$k];
				$eles[$k]['tag_name'] = $ms[1][$k];
				$for = isset($attrs['for']) ? $attrs['for'] : '';
				$eles[$k]['for'] = $for;
				if ($for) $fors[] = $for;
			}
			elseif (in_array($m, array('textarea', 'select')))
			{
				$eles[$k]['tag'] = $ms[0][$k];
				$eles[$k]['tag_name'] = $ms[1][$k];
				$eles[$k]['title'] = isset($attrs['title']) ? $attrs['title'] : '';
				$eles[$k]['id'] = isset($attrs['id']) ? $attrs['id'] : '';
			}
			elseif ($m == 'input')
			{
				// typeless means text
				$attrs['type'] = isset($attrs['type']) ? $attrs['type'] : 'text';

				// target attributes
				if (in_array($attrs['type'], array('text', 'checkbox', 'radio', 'file', 'password')))
				{
					$eles[$k]['tag'] = $ms[0][$k];
					$eles[$k]['tag_name'] = $ms[1][$k];
					$eles[$k]['title'] = isset($attrs['title']) ? $attrs['title'] : '';
					$eles[$k]['id'] = isset($attrs['id']) ? $attrs['id'] : '';
				}
			}
		}

		// no form elements
		if ( ! $eles) return;

		// find "id" which make pair with existing "for" attribute
		$del_eles = array();
		foreach ($fors as $for)
		{
			foreach ($eles as $k => $ele)
			{
				// pick up target ids
				if (isset($ele['id']) && $ele['id'] == $for)
				{
					// id must be unique
					if (array_key_exists($for, $del_eles)) continue;
					$del_eles[$for] = $k;
				}
			}
		}

		// find valid "label"s
		$del_fors = array();
		foreach (array_keys($del_eles) as $id)
		{
			foreach ($eles as $k => $ele)
			{
				// pick up target fors
				if (isset($ele['for']) && $ele['for'] == $id)
				{
					// for must NOT be unique
					$del_fors[] = $k;
				}
			}
		}

		// first elimination - delete valid pairs
		foreach ($del_eles as $k)
		{
			unset($eles[$k]);
		}
		foreach ($del_fors as $k)
		{
			unset($eles[$k]);
		}

		// after here, tacit labels or labelless items remain. I hope so...
		// check tacit labels
		$del_eles = array();
		$pattern = '/\<label[^\>]*?\>.*?\<\/label\>/is';
		preg_match_all($pattern, $str, $mms);

		if ($mms[0])
		{
			foreach ($mms[0] as $m)
			{
				$prev = '';
				foreach ($eles as $k => $ele)
				{
					if ($ele['tag_name'] == 'label')
					{
						$prev = $k;
					}
					elseif (strpos($m, $ele['tag']) !== false)
					{
						$del_eles[] = $prev;
						$del_eles[] = $k;
					}
				}
			}
		}

		// second elimination - tacit label
		foreach ($del_eles as $k)
		{
			unset($eles[$k]);
		}

		// after here, remained labels are meanless.
		// check title attribute
		foreach ($eles as $k => $ele)
		{
			if ($ele['tag_name'] == 'label') continue;

			// empty or titleless
			$title = trim(mb_convert_kana($ele['title'], 's'));
			if (empty($title))
			{
				static::$error_ids[$url]['not_label_but_title'][$k]['id'] = $ele['tag'];
				static::$error_ids[$url]['not_label_but_title'][$k]['str'] = $ele['tag'];
			}
		}
		static::addErrorToHtml($url, 'not_label_but_title', static::$error_ids[$url], 'ignores');
	}
}
