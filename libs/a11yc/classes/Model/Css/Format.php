<?php
/**
 * A11yc\Model\Css\Format
 *
 * @package    part of A11yc
 * @author     Jidaikobo Inc.
 * @license    The MIT License (MIT)
 * @copyright  Jidaikobo Inc.
 * @link       http://www.jidaikobo.com
 */
namespace A11yc\Model\Css;

use A11yc\Model;

class Format
{
	/**
	 * makeArray
	 *
	 * @param  String $css
	 * @return Array
	 */
	public static function makeArray($css)
	{
		// remove comments, import or so
		$css = preg_replace('/\/\*.+?\*\//is', '', $css);
		$css = preg_replace('/^@import.+?$/mis', '', $css);
		$css = preg_replace('/^@charset.+?$/mis', '', $css);
		$css = preg_replace('/^@(?:page|media)[^{]*?{[\n\s\t]*?}/mis', '', $css); // empty

		// check paren num
		$start = mb_substr_count($css, '{');
		$end = mb_substr_count($css, '}');
		Model\Css::$is_suspicious_paren_num = $start != $end;

		// media query and keyframes
		preg_match_all(
			'/@(?:page|media|font-face|keyframes|-webkit-keyframes).+?}.*?}/is',
			$css,
			$ms
		);
		$css = str_replace($ms[0], '', $css);

		// divide blocks
		$csses = self::divideBlocks($ms[0], $css);

		// divide selectors and properties
		$rets = self::divideSelectorsAndProperties($csses);

		// remove vendor prefix
		foreach (Model\Css::$suspicious_props as $k => $v)
		{
			foreach (Model\Css::props() as $prop)
			{
				foreach (Model\Css::vendors() as $vendor)
				{
					if ($v == $vendor.$prop) unset(Model\Css::$suspicious_props[$k]);
				}
			}
		}

		return $rets;
	}

	/**
	 * divide blocks
	 *
	 * @param  Array  $arr
	 * @param  String $css
	 * @return Array
	 */
	private static function divideBlocks($arr, $css)
	{
		$csses = array();
		$csses['base'] = explode('}', $css);

		foreach ($arr as $m)
		{
			$atmarks = substr($m, 0, strpos($m, '{'));
			$atmarks = trim($atmarks);
			$vals    = substr($m, strpos($m, '{'));
			$vals    = trim(trim($vals), '}');
			$csses[$atmarks] = explode('}', $vals);
		}
		return $csses;
	}

	/**
	 * divide selectors and properties
	 *
	 * @param  Array  $csses
	 * @return Array
	 */
	private static function divideSelectorsAndProperties($csses)
	{
		$rets = array();

		foreach ($csses as $type => $type_css)
		{
			$rets[$type] = array();
			foreach ($type_css as $each)
			{
				if (strpos($each, '{') === false) continue; // invalid
				list($selectors, $properties) = explode('{', $each);

				$selectors = trim($selectors);
				$properties = trim($properties);
				if (empty($selectors) || empty($properties)) continue;

				// divide selector and properties
				$each_selectors  = self::divideStrs($selectors, ',');
				$each_properties = self::divideStrs($properties, ';');

				// divide each properties
				$props = self::divideEachProperties($each_properties);

				foreach ($each_selectors as $each_selector)
				{
					if ( ! isset($rets[$type][$each_selector])) $rets[$type][$each_selector] = array();
					$tmps = array_merge($rets[$type][$each_selector], $props);
					ksort($tmps);
					$rets[$type][$each_selector] = $tmps;
				}
			}
			ksort($rets[$type]);
		}
		return $rets;
	}

	/**
	 * divideStr
	 *
	 * @param  String $strs
	 * @param  String $delimiter
	 * @return Array
	 */
	private static function divideStrs($strs, $delimiter)
	{
		if (strpos($strs, $delimiter) !== false)
		{
			$each_strs = explode($delimiter, $strs);
			$each_strs = array_map('trim', $each_strs);
		}
		else
		{
			$each_strs = array(trim($strs));
		}
		return $each_strs;
	}
	/**
	 * divide each properties
	 *
	 * @param  Array $each_properties
	 * @return Array
	 */
	private static function divideEachProperties($each_properties)
	{
		$props = array();
		foreach ($each_properties as $prop_and_val)
		{
			$prop_and_val = trim($prop_and_val);

			// property does't have colon
			$prop_and_vals = array();
			if (strpos($prop_and_val, ':') !== false)
			{
				$prop_and_vals = explode(':', $prop_and_val);
				$prop_and_vals = array_map('trim', $prop_and_vals);
			}
			else if( ! empty($prop_and_val))
			{
				Model\Css::$suspicious_prop_and_vals[] = $prop_and_val;
				continue;
			}

			if (empty($prop_and_vals)) continue;

			// suspicious properties
			if ( ! in_array($prop_and_vals[0], Model\Css::props()))
			{
				Model\Css::$suspicious_props[] = $prop_and_vals[0];
			}

			if ( ! preg_match('/^[a-zA-Z0-9! \.,\(\)\/#"\'%_+\\\-]+$/', $prop_and_vals[1]))
			{
				Model\Css::$suspicious_val_prop[] = array($prop_and_vals[0], $prop_and_vals[1]);
			}

			$props[$prop_and_vals[0]] = $prop_and_vals[1];
		}
		return $props;
	}
}
