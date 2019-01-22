<?php
/**
 * * A11yc\Validate\Check\Fieldsetless
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

class Fieldsetless extends Validate
{
	/**
	 * fieldsetless
	 *
	 * @param String $url
	 * @return Void
	 */
	public static function check($url)
	{
		Validate\Set::log($url, 'fieldsetless', self::$unspec, 1);
		Validate\Set::log($url, 'legendless', self::$unspec, 1);
		$str = Element\Get::ignoredHtml($url);

		$ms = Element\Get::elementsByRe($str, 'ignores');
		if ( ! $ms[1]) return;

		$radio_check_names = self::getRadioCheckNames($ms[0]);
		if (isset($radio_check_names[0]) && is_null($radio_check_names[0]))
		{
			Validate\Set::log($url, 'fieldsetless', self::$unspec, 4);
			Validate\Set::log($url, 'legendless', self::$unspec, 4);
			return;
		}

		// get fieldset if exists
		preg_match_all('/\<fieldset\>(.+?)\<\/fieldset\>/is', $str, $mms);

		// legendless
		self::legendless($url, $mms[0]);

		// get troubled radio and checkboxes name
		$radio_check_names = self::eliminateRadioCheckNames($mms[0], $radio_check_names);
		self::fieldsetless($url, $ms[0], $radio_check_names);

		// add errors
		static::addErrorToHtml($url, 'legendless', static::$error_ids[$url], 'ignores');
		static::addErrorToHtml($url, 'fieldsetless', static::$error_ids[$url], 'ignores');
	}

	/**
	 * getRadioCheckNames
	 *
	 * @param Array $ms
	 * @return Array
	 */
	private static function getRadioCheckNames($ms)
	{
		// radio button existence check
		$radio_check_names = array();
		foreach ($ms as $m)
		{
			$attrs = Element\Get::attributes($m);
			if (
				Arr::get($attrs, 'type') == 'radio' ||
				Arr::get($attrs, 'type') == 'checkbox'
			)
			{
				$radio_check_names[] = Arr::get($attrs, 'name');
			}
		}

		return  array_unique($radio_check_names);
	}

	/**
	 * eliminateRadioCheckNames
	 *
	 * @param Array $mms
	 * @param Array $radio_check_names
	 * @return Array
	 */
	private static function eliminateRadioCheckNames($mms, $radio_check_names)
	{
		foreach ($mms as $mm)
		{
			$mm_mod = Element::ignoreElementsByStr($mm);
			preg_match_all('/\<[^\/].+?\>/', $mm_mod, $eles);

			// check in fieldset, if name found un-candidate it.
			foreach ($radio_check_names as $erase_key => $radio_check_name)
			{
				foreach ($eles[0] as $ele)
				{
					$attrs = Element\Get::attributes($ele);
					if (Arr::get($attrs, 'name') == $radio_check_name)
					{
						unset($radio_check_names[$erase_key]);
						break;
					}
				}
			}
		}
		return $radio_check_names;
	}

	/**
	 * legendless
	 *
	 * @param String $url
	 * @param Array $mms
	 * @return Void
	 */
	private static function legendless($url, $mms)
	{
		foreach ($mms as $k => $mm)
		{
			if (strpos($mm, '</legend>') === false)
			{
				$tstr = mb_substr($mm, 0, mb_strpos($mm, '>') + 1);
				Validate\Set::error($url, 'legendless', $k, $tstr, $tstr);
			}
		}
	}

	/**
	 * legendless
	 *
	 * @param String $url
	 * @param Array $ms
	 * @param Array $radio_check_names
	 * @return Void
	 */
	private static function fieldsetless($url, $ms, $radio_check_names)
	{
		foreach ($radio_check_names as $radio_check_name)
		{
			foreach ($ms as $k => $tstr)
			{
				$attrs = Element\Get::attributes($tstr);
				if (Arr::get($attrs, 'name') == $radio_check_name)
				{
					Validate\Set::error($url, 'fieldsetless', $k, $tstr, $tstr);
					break;
				}
			}
		}
	}

}
