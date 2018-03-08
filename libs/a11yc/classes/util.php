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
	 * search words
	 *
	 * @param  String $word
	 * @return Array
	 */
	public static function searchWords2Arr($word)
	{
		$word  = mb_convert_kana($word, 'asKV');
		$words = array();
		foreach (explode(' ', $word) as $v)
		{
			$v = trim($v);
			if (empty($v)) continue;
			$words[] = $v;
		}
		return $words;
	}

	/**
	 * enunique Uri
	 *
	 * @param  String $uri
	 * @return String
	 */
	public static function enuniqueUri($uri)
	{
		if (empty($uri)) return '';
		$base_url = Model\Settings::fetch('base_url');

		if (strlen($uri) >= 2 && $uri[0] == '/' && $uri[1] != '/')
		{
			$uri = $base_url.$uri;
		}
		// started with "./"
		elseif (strlen($uri) >= 2 && $uri[0] == '.' && $uri[1] == '/')
		{
			$uri = $base_url.substr($uri, 1);
		}
		// started with "../"
		elseif (strlen($uri) >= 3 && $uri[0] == '.' && $uri[1] == '.' && $uri[2] == '/')
		{
			$strs = explode('../', $uri);
			$uri = $base_url.'/'.end($strs);
		}
		elseif (strpos($uri, 'http') !== 0)
		{
			$uri = $base_url.'/'.$uri;
		}

		return self::urldec($uri);
	}

	/**
	 * doc Html Whitelist
	 *
	 * @param  String $word
	 * @return Array
	 */
	public static function docHtmlWhitelist($txt)
	{
		return str_replace(
			array(
				'&lt;code&gt;',
				'&lt;/code&gt;',
			),
			array(
				'<code>',
				'</code>',
			),
			$txt
		);
	}

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
		if ($num == -1) return '';
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
	 * replace '.' to '-' to convert '1.1.1' to '1-1-1'
	 *
	 * @param  String $str
	 * @return String
	 */
	public static function code2key($str)
	{
		return str_replace('.', '-', $str);
	}

	/**
	 * create doc link of '\d-\d-\d\w' in the text
	 *
	 * @param  String $text
	 * @return String
	 */
	public static function key2link($text)
	{
		preg_match_all("/\[[^\]]+?\]/", $text, $ms);

		if ( ! $ms[0]) return $text;

		$yml = Yaml::fetch();

		foreach ($ms[0] as $str)
		{
			// prepare
			$code = ltrim($str, '[');
			$code = rtrim($code, ']');
			$tech = preg_replace('/[\.\d]/', '', $code);
			$search = $str;
			$url = '';
			$str = '';

			// criterion
			if (is_numeric($code[0]))
			{
				$criterion = self::code2key($code);
				$url = A11YC_DOC_URL.Util::s($criterion);
				$str = Arr::get($yml['criterions'][$criterion], 'name');
			}

			// Techniques
			elseif (in_array($tech, Values::techsTypes()))
			{
				$url = A11YC_REF_WCAG20_TECH_URL.Util::s($code);
				$str = Arr::get($yml['techs'][$code], 'title');
			}

			if (empty($url)) return;
			$replace = '"<a href="'.$url.'">'.$str.'</a>"';

			$text = str_replace($search, $replace, $text);
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
}
