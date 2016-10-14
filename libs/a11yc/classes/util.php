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
		static $htmls = array();
		if (isset($htmls[$url])) return $htmls[$url];

		// Browser
		$ua = isset($_SERVER['HTTP_USER_AGENT']) ? Util::s($_SERVER['HTTP_USER_AGENT']) : false;
		if ($ua)
		{
			$options = array(
				'http' => array(
					'method' => 'GET',
					'header' => 'User-Agent: '.$ua,
				),
			);
			$context = stream_context_create($options);
			$html = strtolower(@file_get_contents($url, false, $context));
		}
		else
		{
			$html = strtolower(@file_get_contents($url));
		}

		if ( ! $html) return false;

		$encodes = array("ASCII", "SJIS-win", "SJIS", "ISO-2022-JP", "EUC-JP");
		$encode = mb_detect_encoding($html, array_merge($encodes, array("UTF-8")));
		if (in_array($encode, $encodes))
		{
			$html = mb_convert_encoding($html, "UTF-8", $encode);
		}
		$htmls[$url] = $html;
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
		$title[$url] = static::fetch_page_title_from_html($html);
		return $title[$url];
	}

	/**
	 * fetch page title from html
	 *
	 * @param   string     $html
	 * @return  string
	 */
	public static function fetch_page_title_from_html($html)
	{
		preg_match("/<title.*?>(.+?)<\/title>/s", $html, $m);
		$tmp = isset($m[1]) ? $m[1] : '';
		$title = str_replace(array("\n", "\r"), '', $tmp);
		return $title;
	}

	/**
	 * is page exist
	 *
	 * @param   string     $url
	 * @return  bool
	 */
	public static function is_page_exist($url)
	{
		return (static::fetch_html($url));
	}
}
