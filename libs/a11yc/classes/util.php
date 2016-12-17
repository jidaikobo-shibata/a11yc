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


/*
is_url_exists()
is_url_html()
fetch_html()
fetch_page_title_from_html
URLかどうか
//ignoreできるurlかどうか
200が返るか？
file_get_contents()で取得できるかどうか
*/

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
		$headers[$url] = @get_headers($url, 1);
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

		$basic_auth = 'denki:key@';

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
			$location = str_replace('://', '://'.$basic_auth, $headers['Location']);
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
		$headers = @get_headers($url);
		$is_html = false;

		if ($headers)
		{
			foreach ($headers as $v)
			{
				if (strpos($v, 'text/html') !== false)
				{
					$is_html = true;
					break;
				}
			}
		}
		return $is_html;
	}





	/**
	 * fetch html
	 *
	 * @param   string     $url
	 * @return  string
	 */
	public static function fetch_html($url)
	{
		$url = str_replace('&amp;', '&', $url);

		$url = 'https://denki:key@denki.cwsnara.co.jp/?p=22&a=1&jwp-a11y=ssl';
		$url = static::real_url($url);

echo '<textarea style="width:100%;height:200px;background-color:#fff;color:#111;font-size:90%;font-family:monospace;position:relative;z-index:9999">1';
var_dump($url);
echo '</textarea>';
die();
//	$url = 'https://denki:key@denki.cwsnara.co.jp/points/points-22/?a=1&jwp-a11y=ssl';



		static $htmls = array();
		if (isset($htmls[$url])) return $htmls[$url];
		if ( ! static::is_html($url)) return;

		// try simple file_get_contents()
		$ua = Util::s(Input::user_agent());
		if ($ua)
		{
			$options = array(
				'http' => array(
					'method' => 'GET',
					'header' => 'User-Agent: '.$ua,
				),
				'ssl' => array(
					// bad know-how
					'verify_peer' => false,
					'verify_peer_name' => false,
				)
			);
			$context = stream_context_create($options);
			$html = @file_get_contents($url, false, $context);
		}
		else
		{
			$html = @file_get_contents($url);
		}

		$headers = static::headers($url);


echo '<textarea style="width:100%;height:200px;background-color:#fff;color:#111;font-size:90%;font-family:monospace;position:relative;z-index:9999">';
var_dump($ua);
var_dump($url);
var_dump($headers);
var_dump($html);
echo '</textarea>';
die();

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
	 * is page exist
	 *
	 * @param   string     $url
	 * @return  mixed
	 */
	public static function is_page_exist($url)
	{
		$url = Util::urldec($url);

		// not exists
		$headers = @get_headers($url, 1);
		if ($headers === false) return false;

		// exists
		if (strpos($headers[0], ' 20') !== false)
		{
			return $url;
		}

		// redirect - depth 1 times
		if (
			strpos($headers[0], ' 30') !== false &&
			isset($headers['Location'])
		)
		{
			$redirect_headers = @get_headers($headers['Location']);
			if (strpos($redirect_headers[0], ' 20') !== false)
			{
				return $headers['Location'];
			}
		}
	}
}
