<?php
/**
 * A11yc\Util
 *
 * @package    part of A11yc
 * @version    1.0
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
	 * is_basic_auth
	 *
	 * @param   string     $url
	 * @return  string
	 */
	public static function is_basic_auth($url)
	{
		$headers = @get_headers($url, 1);
		return (strpos($headers[0], 'Authorization Required') !== false);
	}

	/**
	 * basic_auth_prefix
	 *
	 * @param   string     $url
	 * @return  string
	 */
	public static function basic_auth_prefix($url)
	{
		$setup = Controller_Setup::fetch_setup();
		$basic_user = Arr::get($setup, 'basic_user');
		$basic_pass = Arr::get($setup, 'basic_pass');

		if ( ! static::is_basic_auth($url)) return $url;

		if ($basic_user && $basic_pass)
		{
			return str_replace( '://', '://'.$basic_user.':'.$basic_pass.'@', $url);
		}
		else
		{
			return false;
		}
	}

	/**
	 * headers
	 *
	 * @param   string     $url
	 * @return  string
	 */
	public static function headers($url)
	{
		static $headers = array();
		if (isset($headers[$url])) return $headers[$url];

		// setup
		$setup = Controller_Setup::fetch_setup();

		// frist try
		$hs = @get_headers($url, 1);

		// basic auth?
		if (static::is_basic_auth($url))
		{
			$url = static::basic_auth_prefix($url);
			$hs = @get_headers($url, 1);

			if ($hs === false)
			{
				Session::add('messages', 'errors', A11YC_LANG_ERROR_BASIC_AUTH);
				return false;
			}
			elseif (strpos($hs[0], 'Authorization Required') !== false)
			{
				Session::add('messages', 'errors', A11YC_LANG_ERROR_BASIC_AUTH_WRONG);
				return false;
			}
		}

		$headers[$url] = $hs;
		return $headers[$url];
	}

	/**
	 * real_url
	 *
	 * @param   string     $url
	 * @return  string
	 */
	public static function real_url($url, $depth = 2)
	{
		static $target_url = '';
		static $urls = array();
		static $current_depth = 0;
		if ($current_depth == 0)
		{
			$target_url = $url;
		}
		if (isset($urls[$target_url])) return $urls[$target_url];

		$headers = static::headers($url);

		// couldn't get headers or max depth
		if (
			$headers === false ||
			$current_depth >= $depth
		)
		{
			$current_depth = 0;
			$urls[$target_url] = false;
			return false;
		}
		// return url
		else if (strpos($headers[0], ' 20') !== false)
		{
			$current_depth = 0;
			$urls[$target_url] = $url;
			return $url;
		}
		else if (
			strpos($headers[0], ' 30') !== false &&
			isset($headers['Location'])
		)
		{
			$location = static::basic_auth_prefix($headers['Location']);
			$current_depth++;
			return static::real_url($location, $depth);
		}
	}

	/**
	 * is html
	 *
	 * @param   string     $url
	 * @return  bool
	 */
	public static function is_html($url)
	{
		return static::fetch_html($url) ? true : false;
	}

	/**
	 * fetch html
	 *
	 * @param   string     $url
	 * @return  string
	 */
	public static function fetch_html($url)
	{
		$url = Util::urldec($url);

		static $htmls = array();
		if (isset($htmls[$url])) return $htmls[$url];

		// check redirect
		$headers = static::headers($url);
		if (strpos($headers[0], ' 30') !== false)
		{
			$target_url = static::real_url($url);
			if ($target_url === false) return false;
		}
		// 400 or 500
		elseif ( ! strpos($headers[0], ' 20') !== false)
		{
			return false;
		}
		// 200
		else
		{
			$target_url = $url;
		}

		// ssl
		$setup = Controller_Setup::fetch_setup();
		$ignore_cert = array();
		if (strpos($target_url, 'https') !== false)
		{
			if (strpos($target_url, Arr::get($setup, 'trust_ssl_url')) !== false)
			{
				$ignore_cert = array(
					'verify_peer' => false,
					'verify_peer_name' => false,
				);
			}
			else
			{
				Session::add('messages', 'errors', A11YC_LANG_ERROR_SSL);
				return false;
			}
		}

		// basic_auth?
		$target_url = static::basic_auth_prefix($target_url);

		// is HTML
		$headers = static::headers($target_url);
		$content_types = is_array($headers) ? Arr::get($headers, 'Content-Type') : false;

		 // in case array
		$content_type = is_array($content_types) ? $content_types[0] : $content_types;
		if ( ! $content_types || strpos($content_type, 'text/html') === false)
		{
			return false;
		}

		// try simple file_get_contents()
		$ua = Util::s(Input::user_agent());
		if ($ua)
		{
			$options = array(
				'http' => array(
					'method' => 'GET',
					'header' => 'User-Agent: '.$ua,
				),
				'ssl' => $ignore_cert
			);
			$context = stream_context_create($options);
			$html = @file_get_contents($target_url, false, $context);
		}
		else
		{
			$html = @file_get_contents($target_url);
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
	 * do not use DB. because page title is possible to be changed.
	 *
	 * @param   string     $url
	 * @return  string
	 */
	public static function fetch_page_title($url)
	{
		static $titles = array();
		if (isset($titles[$url])) return $titles[$url];
		$html = static::fetch_html($url);
		$titles[$url] = static::fetch_page_title_from_html($html);
		return $titles[$url];
	}

	/**
	 * fetch page title from html
	 *
	 * @param   string     $html
	 * @return  string
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
	 * @param   string     $url
	 * @return  string
	 */
	public static function fetch_page_title_from_db($url)
	{
		static $titles = array();
		if (isset($titles[$url])) return $titles[$url];
		$url = Util::urldec(Input::get('url'));
		$exist = Db::fetch('SELECT * FROM '.A11YC_TABLE_PAGES.' WHERE `url` = ?;', array($url));
		if ($exist)
		{
			$titles[$url] = $exist['page_title'];
			return $titles[$url];
		}
		$titles[$url] = false;
	}

	/**
	 * is page exist
	 *
	 * @param   string     $url
	 * @return  mixed
	 */
	public static function is_page_exist($url)
	{
		$url = Util::urldec($url);

		// not exists
		$headers = static::headers($url);
		if ($headers === false) return false;

		// exists
		if (strpos($headers[0], ' 20') !== false)
		{
			return $url;
		}
		// retry once
		elseif (strpos($headers[0], ' 30') !== false)
		{
			$headers = static::headers(static::real_url($url));
			if (strpos($headers[0], ' 20') !== false)
			{
				return $url;
			}
		}

		return false;
	}
}
