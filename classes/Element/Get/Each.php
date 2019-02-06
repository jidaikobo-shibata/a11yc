<?php
/**
 * A11yc\Element\Get\Each
 *
 * @package    part of A11yc
 * @author     Jidaikobo Inc.
 * @license    The MIT License (MIT)
 * @copyright  Jidaikobo Inc.
 * @link       http://www.jidaikobo.com
 */
namespace A11yc\Element\Get;

class Each
{
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
				$attrs = \A11yc\Element\Get::attributes($img.">");

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

		$attrs = \A11yc\Element\Get::attributes($ms[0]);
		if ( ! isset($attrs['lang'])) return '';
		static::$langs[$url] = $attrs['lang'];
		return static::$langs[$url];
	}
}
