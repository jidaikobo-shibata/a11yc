<?php
/**
 * A11yc\Element\Get
 *
 * @package    part of A11yc
 * @author     Jidaikobo Inc.
 * @license    The MIT License (MIT)
 * @copyright  Jidaikobo Inc.
 * @link       http://www.jidaikobo.com
 */
namespace A11yc\Element;

use A11yc\Model;

class Get extends Element
{
	protected static $ignored_strs = array();
	protected static $res = array();
	protected static $attrs = array();
	protected static $langs = array();

	/**
	 * get Elements ignored HTML
	 *
	 * @param String $url
	 * @param Bool $force
	 * @return String
	 */
	public static function ignoredHtml($url, $force = false)
	{
		if (isset(static::$ignored_strs[$url]) && ! $force) return static::$ignored_strs[$url];

		if ( ! isset(Validate::$hl_htmls[$url]))
		{
			Validate::$hl_htmls[$url] = Model\Html::fetch($url);
		}

		$str = self::ignoreElementsByStr(Validate::$hl_htmls[$url]);
		static::$ignored_strs[$url] = $str;
		return $str;
	}

	/**
	 * get first tag
	 *
	 * @param String $str
	 * @return String
	 */
	public static function firstTag($str)
	{
		$str = trim($str);

		if (strpos($str, '<') === false) return '';

		preg_match('/\<("[^"]*"|\'[^\']*\'|[^\'">])*\>/is', $str, $mms);
		if ( ! isset($mms[0])) return '';
		$str = $mms[0];

		// blankless
		$str = str_replace('/>', ' />', $str);

		return $str;
	}

	/**
	 * attributes
	 *
	 * @param String $str
	 * @return Array
	 */
	public static function attributes($str)
	{
		if (isset(static::$attrs[$str])) return static::$attrs[$str];
		$keep = $str;

		// first tag only
		$str = self::firstTag($str);

		// prepare strings
		list($str, $suspicious_end_quote, $no_space_between_attributes) = self::prepareStrings($str);

		// explode strings
		$attrs = self::explodeStrings($str);

		// suspicious_end_quote
		$attrs['suspicious_end_quote'] = $suspicious_end_quote;
		$attrs['no_space_between_attributes'] = $no_space_between_attributes;
		static::$attrs[$keep] = $attrs;

		return $attrs;
	}

	/**
	 * get elements by regular expression
	 *
	 * @param String $str
	 * @param String $ignore_type
	 * @param String $type (anchors|anchors_and_values|imgs|tags)
	 * @param Bool $force
	 * @return Array
	 */
	public static function elementsByRe($str, $ignore_type, $type = 'tags', $force = false)
	{
		if (isset(static::$res[$ignore_type][$type]) && $force === false)
		{
			return static::$res[$ignore_type][$type];
		}

		switch ($type)
		{
			case 'anchors':
				$ret = self::anchors($str);
				break;
			case 'anchors_and_values':
				$ret = self::anchorsAndValues($str);
				break;
			default:
				$ret = self::tags($str);
				break;
		}

		// imgs
		if (isset($ret[1]) && $type == 'imgs')
		{
			$ret = self::prepareForImage($ret);
		}

		// return
		if ($force) return $ret;
		static::$res[$ignore_type][$type] = $ret;
		return static::$res[$ignore_type][$type];
	}

	/**
	 * Prepare For Image
	 *
	 * @param Array $ret
	 * @return Array
	 */
	private static function prepareForImage($ret)
	{
		foreach ($ret[1] as $k => $v)
		{
			if ($v != 'img')
			{
				unset($ret[0][$k]);
				unset($ret[1][$k]);
				unset($ret[2][$k]);
			}
		}
		return $ret;
	}

	/**
	 * anchors
	 *
	 * @param String $str
	 * @return Array
	 */
	private static function anchors($str)
	{
		$ret = array(
			0 => array(),
			1 => array(),
			2 => array(),
		);
		if (preg_match_all("/\<(?:a|area) ([^\>]+?)\>/i", $str, $ms))
		{
			$ret = $ms;
		}
		return $ret;
	}

	/**
	 * anchors_and_values
	 *
	 * @param String $str
	 * @return Array
	 */
	private static function anchorsAndValues($str)
	{
		$ret = array(
			0 => array(),
			1 => array(),
			2 => array(),
			3 => array(),
		);
		if (preg_match_all("/\<a ([^\>]+)\>(.*?)\<\/a\>|\<area ([^\>]+?)\/\>/si", $str, $ms))
		{
			$ret = $ms;
		}
		return $ret;
	}

	/**
	 * tags
	 *
	 * @param String $str
	 * @return Array
	 */
	private static function tags($str)
	{
		$ret = array(
			0 => array(),
			1 => array(),
			2 => array(),
		);
		if (preg_match_all('/\<[^\/]("[^"]*"|\'[^\']*\'|[^\'">])*\>/is', $str, $ms))
		{
			foreach ($ms[0] as $k => $v)
			{
				$ret[0][$k] = $v; // whole
				if (strpos($v, ' ') !== false)
				{
					$ret[1][$k] = mb_substr($v, 1, mb_strpos($v, ' ') - 1); // element
					$ret[2][$k] = mb_substr($v, mb_strpos($v, ' '), -1); // values
				}
				else
				{
					$ret[1][$k] = mb_substr($v, 1, - 1); // element
					$ret[2][$k] = ''; // values
				}
			}
		}
		return $ret;
	}

	/**
	 * ElementById
	 * I gived up with http://php.net/manual/ja/class.domdocument.php
	 * DOMDocument doesn't return appropriate value for me.
	 *
	 * @param String $str whole html
	 * @param String $id
	 * @return String|Bool
	 */
	public static function elementById($str, $id)
	{
		// search first id
		$pattern = '/\<([^\>]+?) [^\>]*?id *?\= *?[\'"]'.$id.'[\'"].*?\>/ism';
		preg_match($pattern, $str, $ms);
		if (empty($ms)) return false;

		// alias
		$start = preg_quote($ms[0]);
		$elename = $ms[1];
		$end = '\<\/'.$elename.'\>';
		$end_pure = '</'.$elename.'>';

		// maximum much
		if ( ! preg_match('/'.$start.'.+'.$end.'/ism', $str, $mms)) return false;
		$target = $mms[0];

		// nest
		$close = self::getClosePos($target, $elename, $end_pure);
		if ( ! $close) return false;

		// whole tag
		$target = mb_substr($target, 0, $close).$end_pure;

		return $target;
	}

	/**
	 * getClosePos
	 *
	 * @param String $target
	 * @param String $elename
	 * @param String $end_pure
	 * @return Integer|Bool
	 */
	private static function getClosePos($target, $elename, $end_pure)
	{
		$loop = true;
		$open_pos = 1;
		$close_pos = 1;
		$close = 0;
		$failsafe = 0;

		while ($loop)
		{
			$failsafe++;
			if ($failsafe >= 100) $loop = false;

			$open = mb_strpos($target, '<'.$elename, $open_pos);
			$close = mb_strpos($target, $end_pure, $close_pos);

			// if inner open tag was not found
			if ( ! $open) break;

			// if open tag appears before end tag keep loop
			if ($open < $close)
			{
				$open_pos = $open + 1;
				$close_pos = $close + 1;
				continue;
			}

			$loop = false;
		}
		return $close;
	}

	/**
	 * TextFromElement
	 *
	 * @param String $str whole html
	 * @return String|Bool
	 */
	public static function textFromElement($str)
	{
		$text = '';

		// alt of img
		if (strpos($str, 'img') !== false)
		{
			$imgs = explode('>', $str);
			foreach ($imgs as $img)
			{
				if (strpos($img, 'img') === false) continue;
				$attrs = self::attributes($img.">");

				foreach ($attrs as $kk => $vv)
				{
					if (strpos($kk, 'alt') !== false)
					{
						$text.= $vv;
					}
				}
			}
			$text = trim($text);
		}

		// others
		$text = strip_tags($str).$text;
		$text = trim($text);

		return $text;
	}

	/**
	 * get doctype
	 *
	 * @param String $url
	 * @return Mixed|String|Bool|Null
	 */
	public static function doctype($url)
	{
		if (empty(Validate::$hl_htmls[$url])) return false;
		$html = Validate::$hl_htmls[$url];

		preg_match("/\<!DOCTYPE [^\>]+?\>/is", $html, $ms);

		if ( ! isset($ms[0])) return false; // doctypeless

		// doctype
		$doctype = null;
		$target_str = strtolower(str_replace(array("\n", ' '), '', $ms[0]));

		// html5
		if(strpos($target_str, 'doctypehtml>') !== false)
		{
			$doctype = 'html5';
		}
		// HTML4
		else if (strpos($target_str, 'dtdhtml4.0') !== false)
		{
			$doctype = 'html4';
		}
		// xhtml1x
		else if(strpos($target_str, 'dtdxhtml1') !== false)
		{
			$doctype = 'xhtml1';
		}

		return $doctype;
	}

	/**
	 * get lang
	 *
	 * @param String $url
	 * @return String
	 */
	public static function lang($url)
	{
		if (isset(static::$langs[$url])) return static::$langs[$url];
		if (empty(Validate::$hl_htmls[$url])) return '';

		preg_match("/\<html ([^\>]+?)\>/is", Validate::$hl_htmls[$url], $ms);
		if ( ! isset($ms[0])) return ''; // langless

		$attrs = self::attributes($ms[0]);
		if ( ! isset($attrs['lang'])) return '';
		static::$langs[$url] = $attrs['lang'];
		return static::$langs[$url];
	}
}
