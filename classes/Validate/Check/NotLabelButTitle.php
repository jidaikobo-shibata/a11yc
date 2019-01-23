<?php
/**
 * * A11yc\Validate\Check\NotLabelButTitle
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

class NotLabelButTitle extends Validate
{
	/**
	 * not_label_but_title
	 *
	 * @return  bool
	 */
	public static function check($url)
	{
		Validate\Set::log($url, 'not_label_but_title', self::$unspec, 1);
		$str = Element\Get::ignoredHtml($url);
		$ms = Element\Get::elementsByRe($str, 'ignores', 'tags');
		if ( ! $ms[0]) return;

		// labels_eles
		list($eles, $fors) = self::setLabelAndElement($ms);

		// no form elements
		if (empty($eles))
		{
			Validate\Set::log($url, 'not_label_but_title', self::$unspec, 4);
			return;
		}

		// find "id" which make pair with existing "for" attribute
		$del_eles = self::setDeleteElement($eles, $fors);

		// find valid "label"s
		$del_fors = self::setDeleteFor($eles, $del_eles);

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
		$del_eles = self::setSecondaryDeleteElement($str, $eles);

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
			Validate\Set::errorAndLog(
				empty($title),
				$url,
				'not_label_but_title',
				$k,
				$ele['tag'],
				$ele['tag']
			);
		}
		static::addErrorToHtml($url, 'not_label_but_title', static::$error_ids[$url], 'ignores');
	}

	/**
	 * set label and element
	 *
	 * @param Array $ms
	 * @return Array
	 */
	private static function setLabelAndElement($ms)
	{
		$eles = array();
		$fors = array();

		foreach ($ms[1] as $k => $m)
		{
			if ( ! in_array($m, array('label', 'input', 'textarea', 'select'))) continue;

			$attrs = Element\Get::attributes($ms[0][$k]);
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
		return array($eles, $fors);
	}

	/**
	 * set delete element
	 *
	 * @param Array $eles
	 * @param Array $fors
	 * @return Array
	 */
	private static function setDeleteElement($eles, $fors)
	{
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
		return $del_eles;
	}

	/**
	 * set delete for
	 *
	 * @param Array $eles
	 * @param Array $del_eles
	 * @return Array
	 */
	private static function setDeleteFor($eles, $del_eles)
	{
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
		return $del_fors;
	}

	/**
	 * set secondary delete element
	 *
	 * @param String $str
	 * @param Array $eles
	 * @return Array
	 */
	private static function setSecondaryDeleteElement($str, $eles)
	{
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
		return $del_eles;
	}
}
