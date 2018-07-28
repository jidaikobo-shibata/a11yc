<?php
/**
 * A11yc\Validate\Fieldsetless
 *
 * @package    part of A11yc
 * @author     Jidaikobo Inc.
 * @license    The MIT License (MIT)
 * @copyright  Jidaikobo Inc.
 * @link       http://www.jidaikobo.com
 */
namespace A11yc\Validate;

class Fieldsetless extends Validate
{
	/**
	 * fieldsetless
	 *
	 * @param  String $url
	 * @return Void
	 */
	public static function check($url)
	{
		static::$logs[$url]['fieldsetless'][self::$unspec] = 1;
		static::$logs[$url]['legendless'][self::$unspec] = 1;
		$str = Element::ignoreElements($url);

		$ms = Element::getElementsByRe($str, 'ignores');
		if ( ! $ms[1]) return;

		// radio button existence check
		$radio_check_names = array();
		foreach ($ms[0] as $m)
		{
			$attrs = Element::getAttributes($m);
			if (
				Arr::get($attrs, 'type') == 'radio' ||
				Arr::get($attrs, 'type') == 'checkbox'
			)
			{
				$radio_check_names[] = Arr::get($attrs, 'name');
			}
		}
		$radio_check_names = array_unique($radio_check_names);

		if (isset($radio_check_names[0]) && is_null($radio_check_names[0]))
		{
			static::$logs[$url]['fieldsetless'][self::$unspec] = 4;
			static::$logs[$url]['legendless'][self::$unspec] = 4;
			return;
		}

		// get fieldset if exists
		$ignores = array_merge(Element::$ignores, Element::$ignores_comment_out);
		preg_match_all('/\<fieldset\>(.+?)\<\/fieldset\>/is', $str, $mms);

		// legendless
		foreach ($mms[0] as $k => $mm)
		{
			if (strpos($mm, '</legend>') === false)
			{
				$tstr = mb_substr($mm, 0, mb_strpos($mm, '>') + 1);
				static::$logs[$url]['legendless'][$tstr] = -1;
				static::$error_ids[$url]['legendless'][$k]['id'] = $tstr;
				static::$error_ids[$url]['legendless'][$k]['str'] = $tstr;
			}
		}

		// fieldsetless
		$fileds = array();
		foreach ($mms[0] as $k => $mm)
		{
			$mm_mod = $mm;
			foreach ($ignores as $ignore)
			{
				$mm_mod = preg_replace($ignore, '', $mm_mod);
			}
			preg_match_all('/\<[^\/].+?\>/', $mm_mod, $eles);

			// check in fieldset, if name found un-candidate it.
			foreach ($radio_check_names as $erase_key => $radio_check_name)
			{
				foreach ($eles[0] as $ele)
				{
					$attrs = Element::getAttributes($ele);
					if (Arr::get($attrs, 'name') == $radio_check_name)
					{
						unset($radio_check_names[$erase_key]);
						break;
					}
				}
			}
		}

		$flags = array();
		// $radio_check_names are currently, troubled forms
		foreach ($radio_check_names as $radio_check_name)
		{
			foreach ($ms[0] as $k => $v)
			{
				$attrs = Element::getAttributes($v);
				if (Arr::get($attrs, 'name') == $radio_check_name)
				{
					$flags[] = $radio_check_name;

					static::$logs[$url]['fieldsetless'][$v] = -1;
					static::$error_ids[$url]['fieldsetless'][$k]['id'] = $v;
					static::$error_ids[$url]['fieldsetless'][$k]['str'] = $v;
					break;
				}
			}
		}

		static::addErrorToHtml($url, 'legendless', static::$error_ids[$url], 'ignores');
		static::addErrorToHtml($url, 'fieldsetless', static::$error_ids[$url], 'ignores');
	}
}
