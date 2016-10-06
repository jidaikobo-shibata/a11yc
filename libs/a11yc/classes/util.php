<?php
/**
 * A11yc\Util
 *
 * @package    part of A11yc
 * @version    1.0
 * @author     Jidaikobo Inc.
 * @license    WTFPL2.0
 * @copyright  Jidaikobo Inc.
 * @link       http:/www.jidaikobo.com
 */
namespace A11yc;
class Util extends \Kontiki\Util
{
	/**
	 * number to 'A'
	 * to get conformance level string
	 *
	 * @return  string
	 */
	public static function num2str($num, $default = '-')
	{
		$num = intval($num);
		return $num ? str_repeat('A', $num) : $default ;
	}

	/**
	 * replace '-' to '.' to convert '1-1-1' to '1.1.1'
	 *
	 * @return  string
	 */
	public static function key2code($str)
	{
		return str_replace('-', '.', $str);
	}

	/**
	 * create doc link of '\d-\d-\d\w' in the text
	 *
	 * @return  string
	 */
	public static function key2link($text)
	{
		$yml = Yaml::fetch();

		preg_match("/\d-\d-\d+?\w/", $text, $ms);
		if ($ms)
		{
			foreach ($ms as $m)
			{
				$criterion = static::get_criterion_from_code($m);
				if ( ! isset($yml['checks'][$criterion][$m])) continue;
				$text = str_replace(
					$m,
					'<a href="'.A11YC_DOC_URL.$m.'&criterion='.$criterion.'">'.static::key2code($m).' ('.$yml['checks'][$criterion][$m]['name'].')</a> ',
					$text);
			}
		}

		return $text;
	}

	/**
	 * get criterion from a11yc code
	 *
	 * @return  string
	 */
	public static function get_criterion_from_code($code)
	{
		static $retvals = array();
		if (array_key_exists($code, $retvals)) return $retvals[$code];

		$yml = Yaml::fetch();
		foreach ($yml['checks'] as $criterion => $v)
		{
			if (array_key_exists($code, $v))
			{
				$retvals[$code] = $criterion;
				return $criterion;
			}
		}
		return false;
	}

	/**
	 * fetch html
	 *
	 * @param   string     $url
	 * @return  string
	 */
	public static function fetch_html($url)
	{
		static $html = array();
		if (isset($html[$url])) return $html[$url];
		$html = strtolower(@file_get_contents($url));

		$encodes = array("ASCII", "SJIS-win", "SJIS", "ISO-2022-JP", "EUC-JP");
		$encode = mb_detect_encoding($html, array_merge($encodes, array("UTF-8")));
		if (in_array($encode, $encodes))
		{
			$html = mb_convert_encoding($html, "UTF-8", $encode);
		}
		$html[$url] = $html;
		return $html;
	}

	/**
	 * fetch page title
	 *
	 * @param   string     $url
	 * @return  string
	 */
	public static function fetch_page_title($url)
	{
		static $title = array();
		if (isset($title[$url])) return $title[$url];
		$html = static::fetch_html($url);
		preg_match("/<title.*?>(.+?)<\/title>/", $html, $m);
		$title[$url] = isset($m[1]) ? $m[1] : '';
		return $title[$url];
	}
}
