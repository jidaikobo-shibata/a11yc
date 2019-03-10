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
	 * @param String $word
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
	 * @param String $uri
	 * @param String $base_uri
	 * @return String
	 */
	public static function enuniqueUri($uri, $base_uri = '')
	{
		if (empty($uri)) return '';
		$base_url = $base_uri ?: Model\Data::baseUrl();

		if (strlen($uri) >= 2 && $uri[0] == '/' && $uri[1] != '/')
		{
			$uri = $base_url.$uri;
		}
		// started with "./"
		elseif (strlen($uri) >= 2 && substr($uri, 0, 2) == './')
		{
			$uri = $base_url.substr($uri, 1);
		}
		// started with "../"
		elseif (strlen($uri) >= 3 && substr($uri, 0, 3) == '../')
		{
			$strs = explode('../', $uri);
			$uri = $base_url.'/'.end($strs);
		}
		// started with "//"
		elseif (strlen($uri) >= 3 && substr($uri, 0, 2) == '//')
		{
			$strs = explode('//', $uri);
			$uri = substr($base_url, 0, strpos($base_url, ':')).'://'.end($strs);
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
	 * @param String $txt
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
	 * @param Integer $num
	 * @param String $default
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
	 * @param String $str
	 * @return String
	 */
	public static function key2code($str)
	{
		return str_replace('-', '.', $str);
	}

	/**
	 * replace '.' to '-' to convert '1.1.1' to '1-1-1'
	 *
	 * @param String $str
	 * @return String
	 */
	public static function code2key($str)
	{
		return str_replace('.', '-', $str);
	}

	/**
	 * create doc link of '\d-\d-\d\w' in the text
	 *
	 * @param String $text
	 * @param String $doc_url
	 * @return String
	 */
	public static function key2link($text, $doc_url = '')
	{
		preg_match_all("/\[[^\]]+?\]/", $text, $ms);

		if ( ! $ms[0]) return $text;

		$yml = Yaml::fetch();
		$doc_url = $doc_url ?: A11YC_DOC_URL;

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
				$url = $doc_url.Util::s($criterion);
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
	 * criterionsOfLevels
	 *
	 * @return Array
	 */
	public static function criterionsOfLevels()
	{
		$yml = Yaml::fetch();
		// levels
		$levels = array();
		foreach ($yml['levels'] as $v)
		{
			foreach ($yml['criterions'] as $criterion => $vv)
			{
				if ($vv['level']['name'] != $v['name']) continue;
				$levels[$v['name']][] = $criterion;
			}
		}
		$levels['AA'] = array_merge($levels['AA'], $levels['A']);
		$levels['AAA'] = array_merge($levels['AAA'], $levels['AA']);
		return $levels;
	}

	/**
	 * set message
	 *
	 * @param Bool $succeed
	 * @param String $success_message
	 * @param String $error_message
	 * @return Void
	 */
	public static function setMassage($succeed, $success_message = A11YC_LANG_UPDATE_SUCCEED, $error_message = A11YC_LANG_UPDATE_FAILED)
	{
		if ($succeed)
		{
			Session::add('messages', 'messages', $success_message);
			return;
		}
		Session::add('messages', 'errors', $error_message);
	}

	/**
	 * set counter
	 *
	 * @param Bool $exp
	 * @param Integer $success
	 * @param Integer $failure
	 * @return Array
	 */
	public static function setCounter($exp, $success = 0, $failure = 0)
	{
		if ($exp)
		{
			$success++;
		}
		else
		{
			$failure++;
		}
		return array($success, $failure);
	}
}
