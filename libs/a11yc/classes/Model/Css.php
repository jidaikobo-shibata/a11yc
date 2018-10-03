<?php
/**
 * A11yc\Model\Css
 *
 * @package    part of A11yc
 * @author     Jidaikobo Inc.
 * @license    The MIT License (MIT)
 * @copyright  Jidaikobo Inc.
 * @link       http://www.jidaikobo.com
 */
namespace A11yc\Model;

class Css
{
	protected static $csses = array();
	public static $is_suspicious_paren_num = false;
	public static $suspicious_prop_and_vals = array();
	public static $suspicious_props = array();
	public static $suspicious_val_prop = array();

	protected static $vendors = array(
		'-ms-', '-moz-', '-webkit-', '-o-', '-moz-osx-'
	);

	protected static $css_props = array(
		'color', 'opacity', 'background', 'background-attachment', 'background-clip',
		'background-color', 'background-image', 'background-origin', 'background-position',
		'background-repeat', 'background-size', 'border', 'border-bottom',
		'border-bottom-color', 'border-bottom-left-radius', 'border-bottom-right-radius',
		'border-bottom-style', 'border-bottom-width', 'border-color', 'border-image',
		'border-image-outset', 'border-image-repeat', 'border-image-slice',
		'border-image-source', 'border-image-width', 'border-left', 'border-left-color',
		'border-left-style', 'border-left-width', 'border-radius', 'border-right',
		'border-right-color', 'border-right-style', 'border-right-width', 'border-style',
		'border-top', 'border-top-color', 'border-top-left-radius',
		'border-top-right-radius', 'border-top-style', 'border-top-width', 'border-width',
		'box-decoration-break', 'box-shadow', 'image-resolution', 'object-fit',
		'object-position', 'marquee-direction', 'marquee-play-count', 'marquee-speed',
		'marquee-style', 'break-after', 'break-before', 'break-inside', 'column-count',
		'column-fill', 'column-gap', 'column-rule', 'column-rule-color', 'column-rule-style',
		'column-rule-width', 'column-span', 'column-width', 'columns', 'cue', 'cue-after',
		'cue-before', 'pause', 'pause-after', 'pause-before', 'rest', 'rest-after',
		'rest-before', 'speak', 'speak-as', 'voice-balance', 'voice-duration', 'voice-family',
		'voice-pitch', 'voice-range', 'voice-rate', 'voice-stress', 'voice-volume',
		'backface-visibility', 'perspective', 'perspective-origin',

		'transform', 'transform-origin', 'transform-style', 'transition', 'transition-delay',
		'transition-duration', 'transition-property', 'transition-timing-function',
		'animation', 'animation-delay', 'animation-direction', 'animation-duration',
		'animation-fill-mode', 'animation-iteration-count', 'animation-name',
		'animation-play-state', 'animation-timing-function',

		'align-content', 'align-items', 'align-self', 'flex', 'flex-basis', 'flex-direction',
		'flex-flow', 'flex-grow', 'flex-shrink', 'flex-wrap', 'justify-content', 'order',
		'font', 'font-family', 'font-feature-settings', 'font-kerning',
		'font-language-override', 'font-size', 'font-size-adjust', 'font-stretch',
		'font-style', 'font-synthesis', 'font-variant', 'font-variant-alternates',
		'font-variant-caps', 'font-variant-east-asian', 'font-variant-ligatures',
		'font-variant-numeric', 'font-variant-position', 'font-weight',

		'fit', 'fit-position', 'image-orientation', 'orphans', 'page', 'page-break-after',
		'page-break-before', 'page-break-inside', 'size', 'widows', 'hanging-punctuation',
		'hyphens', 'letter-spacing', 'line-break', 'overflow-wrap', 'tab-size',

		'text-align', 'text-align-last', 'text-decoration', 'text-decoration-color',
		'text-decoration-line', 'text-decoration-skip', 'text-decoration-style',
		'text-emphasis', 'text-emphasis-color', 'text-emphasis-position',
		'text-emphasis-style', 'text-indent', 'text-justify', 'text-shadow',
		'text-transform', 'text-underline-position', 'white-space', 'word-break',
		'word-spacing', 'box-sizing', 'cursor', 'icon', 'ime-mode',

		'nav-down', 'nav-index', 'nav-left', 'nav-right', 'nav-up', 'outline',
		'outline-color', 'outline-offset', 'outline-style', 'outline-width',
		'resize', 'text-overflow', 'direction', 'text-combine-horizontal',
		'text-combine-mode', 'text-orientation', 'unicode-bidi', 'writing-mode',
		'marks', 'grid-cell', 'grid-column', 'grid-column-align', 'grid-column-sizing',
		'grid-column-span', 'grid-columns', 'grid-flow', 'grid-row', 'grid-row-align',
		'grid-row-sizing', 'grid-row-span', 'grid-rows', 'grid-template', 'list-style',
		'list-style-image', 'list-style-position', 'list-style-type', 'bottom', 'clip',
		'left', 'position', 'right', 'top', 'z-index', 'border-collapse', 'border-spacing',
		'caption-side', 'empty-cells', 'table-layout',

		'clear', 'display', 'float', 'height', 'margin', 'margin-bottom', 'margin-left',
		'margin-right', 'margin-top', 'max-height', 'max-width', 'min-height', 'min-width',
		'overflow', 'overflow-style', 'overflow-x', 'overflow-y', 'padding',
		'padding-bottom', 'padding-left', 'padding-right', 'padding-top',

		'visibility', 'width', 'content', 'counter-increment', 'counter-reset', 'crop',
		'move-to', 'page-policy', 'quotes', 'alignment-adjust', 'alignment-baseline',
		'baseline-shift', 'dominant-baseline', 'drop-initial-after-adjust',
		'drop-initial-after-align', 'drop-initial-before-adjust',
		'drop-initial-before-align', 'drop-initial-size', 'drop-initial-value',
		'inline-box-align', 'line-height', 'line-stacking', 'line-stacking-ruby',
		'line-stacking-shift', 'line-stacking-strategy', 'text-height', 'vertical-align',
		'ruby-align', 'ruby-overhang', 'ruby-position', 'ruby-span',
		'target', 'target-name', 'target-new', 'target-position',

		'filter', 'text-rendering', 'font-smoothing', 'appearance'
	);

	/**
	 * fetch css
	 *
	 * @param  String $url
	 * @return Array
	 */
	public static function fetchCss($url, $ua = 'using')
	{
		$ua = $ua == 'using' ? Input::userAgent() : $ua;
		if ( ! is_string($ua)) Util::error();
		if (isset(static::$csses[$url][$ua])) return static::$csses[$url][$ua];

		$css = self::getConvinedCssFromHtml($url, Html::fetchHtml($url, $url), $ua);
		static::$csses[$url][$ua] = self::makeArray($css);

		return static::$csses[$url][$ua];
	}

	/**
	 * get css from html
	 *
	 * @param  String $html_url
	 * @param  String $html
	 * @param  String $ua
	 * @return String
	 */
	private static function getConvinedCssFromHtml($html_url, $html, $ua)
	{
		$css = '';
		$ua = $ua == 'using' ? Input::userAgent() : $ua;

		// style tag
		if (preg_match_all("/\<style[^\>]*\>(.*?)\<\/style\>/si", $html, $ms))
		{
			foreach ($ms[1] as $m)
			{
				$css.= $m;
			}
		}

		// file
		if (preg_match_all("/\<link [^\>]*\>/si", $html, $ms))
		{
			foreach ($ms[0] as $m)
			{
				if (strpos($m, 'stylesheet') === false) continue;
				$attrs = Element\Get::attributes($m);
				if ( ! isset($attrs['href'])) continue;
				$url = $attrs['href'];
				$url = self::enuniqueUri($url);
				if ( ! Crawl::isPageExist($url)) continue;
				$current_css = self::fetchFromInternet($url, $ua);
				$css.= $current_css;

				// @import
				if (preg_match_all("/@import *?url *?\((.+?)\)/si", $current_css, $mms))
				{
					foreach ($mms[1] as $import_url)
					{
						$import_url = trim($import_url, '"');
						$import_url = trim($import_url, "'");
						$import_url = self::enuniqueUri($import_url, $url);
						$css.= self::fetchFromInternet($import_url, $ua);
					}
				}
			}
		}

		return $css;
	}

	/**
	 * fetch from internet
	 *
	 * @param  String $url
	 * @param  String $ua
	 * @return String
	 */
	private static function fetchFromInternet($url, $ua = 'using')
	{
		Guzzle::forge($url);
		Guzzle::instance($url)
			->set_config(
				'User-Agent',
				Util::s($ua.' GuzzleHttp/a11yc (+http://www.jidaikobo.com)')
			);
		return Guzzle::instance($url)->body;
	}

	/**
	 * enuniqueUri
	 *
	 * @param  String $url
	 * @param  String $css_url
	 * @return String
	 */
	private static function enuniqueUri($url, $css_url = '')
	{
		if (strpos($url, 'http') === 0)
		{
			return $url;
		}

		if (strlen($url) >= 2 && $url[0] == '/' && $url[1] != '/')
		{
			return Util::enuniqueUri($url);
		}

		if ($css_url)
		{
			$css_urls = explode('/', $css_url);
			array_pop($css_urls);
			$css_url = join('/', $css_urls);

			// started with "./"
			if (strlen($url) >= 2 && $url[0] == '.' && $url[1] == '/')
			{
				$url = $css_url.substr($url, 1);
			}
			// started with "../"
			elseif (strlen($url) >= 3 && $url[0] == '.' && $url[1] == '.' && $url[2] == '/')
			{
				$strs = explode('../', $url);
				$upper_num = count($strs) - 1;
				for ($n = 1; $n <= $upper_num; $n++)
				{
					array_pop($css_urls);
				}
				$css_url = join('/', $css_urls);
				$url = $css_url.'/'.end($strs);
			}
			elseif (strpos($url, 'http') !== 0)
			{
				$url = $css_url.'/'.$url;
			}
		}

		return Util::urldec($url);
	}

	/**
	 * makeArray
	 *
	 * @param  String $css
	 * @return Array
	 */
	private static function makeArray($css)
	{
		// remove comments, import or so
		$css = preg_replace('/\/\*.+?\*\//is', '', $css);
		$css = preg_replace('/^@import.+?$/mis', '', $css);
		$css = preg_replace('/^@charset.+?$/mis', '', $css);
		$css = preg_replace('/^@(?:page|media)[^{]*?{[\n\s\t]*?}/mis', '', $css); // empty

		// check paren num
		$start = mb_substr_count($css, '{');
		$end = mb_substr_count($css, '}');
		static::$is_suspicious_paren_num = $start != $end;

		// media query and keyframes
		preg_match_all(
			'/@(?:page|media|font-face|keyframes|-webkit-keyframes).+?}.*?}/is',
			$css,
			$ms
		);
		$css = str_replace($ms[0], '', $css);

		// divide blocks
		$csses = array();
		$rets = array();
		$csses['base'] = explode('}', $css);

		foreach ($ms[0] as $m)
		{
			$atmarks = substr($m, 0, strpos($m, '{'));
			$atmarks = trim($atmarks);
			$vals    = substr($m, strpos($m, '{'));
			$vals    = trim(trim($vals), '}');
			$csses[$atmarks] = explode('}', $vals);
		}

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

		// remove vendor prefix
		foreach (static::$suspicious_props as $k => $v)
		{
			foreach (static::$css_props as $prop)
			{
				foreach (static::$vendors as $vendor)
				{
					if ($v == $vendor.$prop) unset(static::$suspicious_props[$k]);
				}
			}
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
			if (strpos($prop_and_val, ':') !== false)
			{
				$prop_and_vals = explode(':', $prop_and_val);
				$prop_and_vals = array_map('trim', $prop_and_vals);
			}
			else if( ! empty($prop_and_val))
			{
				static::$suspicious_prop_and_vals[] = $prop_and_val;
				continue;
			}

			// suspicious properties
			if ( ! in_array($prop_and_vals[0], static::$css_props))
			{
				static::$suspicious_props[] = $prop_and_vals[0];
			}

			if ( ! preg_match('/^[a-zA-Z0-9! \.,\(\)\/#"\'%_+\\\-]+$/', $prop_and_vals[1]))
			{
				static::$suspicious_val_prop[] = array($prop_and_vals[0], $prop_and_vals[1]);
			}

			$props[$prop_and_vals[0]] = $prop_and_vals[1];
		}
		return $props;
	}
}
