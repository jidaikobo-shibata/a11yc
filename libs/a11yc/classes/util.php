<?php
/**
 * A11yc\Util
 *
 * @package    part of A11yc
 * @author     Jidaikobo Inc.
 * @license    The MIT License (MIT)
 * @copyright  Jidaikobo Inc.
 * @link       http://www.jidaikobo.com
 */
namespace A11yc;

class Util extends \Kontiki\Util
{
	/**
	 * number to 'A' or 'AA' or 'AAA'
	 * to get conformance level string
	 *
	 * @param  Integer $num
	 * @param  String $default
	 * @return String
	 */
	public static function num2str($num, $default = '-')
	{
		$num = intval($num);
		return $num ? str_repeat('A', $num) : $default ;
	}

	/**
	 * replace '-' to '.' to convert '1-1-1' to '1.1.1'
	 *
	 * @param  String $str
	 * @return String
	 */
	public static function key2code($str)
	{
		return str_replace('-', '.', $str);
	}

	/**
	 * create doc link of '\d-\d-\d\w' in the text
	 *
	 * @param  String $text
	 * @return String
	 */
	public static function key2link($text)
	{
		if (defined('A11YC_POST_SCRIPT_NAME')) return $text;

		$yml = Yaml::fetch();

		preg_match_all("/\d-\d-\d+?\w/", $text, $ms);

		$replaces = array();
		if (isset($ms[0][0]) && $ms[0][0])
		{
			foreach ($ms[0] as $m)
			{
				$criterion = static::get_criterion_from_code($m);
				if ( ! isset($yml['checks'][$criterion][$m])) continue;
				$original = $m;
				$replaced = hash("sha256", $m);
				$replaces[] = array(
					'original' => $original,
					'replaced' => $replaced,
				);
				$text = str_replace($original, $replaced, $text);
			}

			foreach ($replaces as $v)
			{
				$criterion = static::get_criterion_from_code($v['original']);
				$text = str_replace(
					$v['replaced'],
					'<a href="'.A11YC_DOC_URL.$v['original'].'&criterion='.$criterion.'"'.A11YC_TARGET.' title="'.$yml['checks'][$criterion][$v['original']]['name'].' ('.$yml['checks'][$criterion][$v['original']]['level']['name'].')">'.static::key2code($criterion).'</a>',
					$text);
			}
		}

		return $text;
	}

	/**
	 * get criterion from a11yc code
	 * '1-3-1a' to '1-3-1'
	 *
	 * @param  String $code
	 * @return String|Bool
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
	 * fetch page title
	 * do not use DB. because page title is possible to be changed.
	 *
	 * @param  String $url
	 * @return String
	 */
	public static function fetch_page_title($url)
	{
		static $titles = array();
		if (isset($titles[$url])) return $titles[$url];
		$html = Crawl::fetch_html($url);
		$titles[$url] = static::fetch_page_title_from_html($html);
		return $titles[$url];
	}

	/**
	 * fetch page title from html
	 *
	 * @param  String $html
	 * @return String
	 */
	public static function fetch_page_title_from_html($html)
	{
		preg_match("/<title.*?>(.+?)<\/title>/si", $html, $m);
		$tmp = isset($m[1]) ? $m[1] : '';
		$title = str_replace(array("\n", "\r"), '', $tmp);
		return $title;
	}

	/**
	 * fetch page title from DB
	 *
	 * @param  String $url
	 * @return String
	 */
	public static function fetch_page_title_from_db($url)
	{
		static $titles = array();
		if (isset($titles[$url])) return $titles[$url];
		$url = Util::urldec(Input::post('url', '', FILTER_VALIDATE_URL));
		$exist = Db::fetch('SELECT * FROM '.A11YC_TABLE_PAGES.' WHERE `url` = ?;', array($url));
		if ($exist)
		{
			$titles[$url] = $exist['page_title'];
			return $titles[$url];
		}
		$titles[$url] = false;
	}
}
