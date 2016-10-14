<?php
/**
 * A11yc\Validate
 *
 * @package    part of A11yc
 * @version    1.0
 * @author     Jidaikobo Inc.
 * @license    WTFPL2.0
 * @copyright  Jidaikobo Inc.
 * @link       http:/www.jidaikobo.com
 */
namespace A11yc;
class Validate
{
	protected static $root_path;
	protected static $error_ids = array();
	protected static $errors = array();

	public static $ignores = array(
		"/\<script.+?\<\/script\>/si",
		"/\<style.+?\<\/style\>/si",
		"/\<rdf:RDF.+?\<\/rdf:RDF\>/si",
		"/\<!--.+?--\>/si",
	);

	/**
	 * set_root_path
	 *
	 * @param   strings     $url
	 * @return  void
	 */
	public static function set_root_path($url)
	{
		$paths = explode('/', $url);
		static::$root_path = join('/', array_slice($paths, 0, 3));
	}

	/**
	 * get_errors
	 *
	 * @return  array
	 */
	public static function get_errors()
	{
		return static::$errors;
	}

	/**
	 * get_error_ids
	 *
	 * @return  array
	 */
	public static function get_error_ids()
	{
		return static::$error_ids;
	}

	/**
	 * ignore_elements
	 *
	 * @param   strings     $str
	 * @param   bool        $force
	 * @return  $str
	 */
	public static function ignore_elements($str, $force = false)
	{
		static $retval = '';
		if ($retval && ! $force) return $retval;

//		$str = str_replace(array("\n", "\r"), ' ', $str);
//		$retval = strtolower($str);

		// ignore comment out, script, style
		foreach (static::$ignores as $ignore)
		{
			$str = preg_replace($ignore, '', $str);
		}

		return $str;
	}

	/**
	 * is exist alt attr of img
	 *
	 * @param   strings     $str
	 * @return  void
	 */
	public static function is_exist_alt_attr_of_img($str)
	{
		$str = static::ignore_elements($str);

		preg_match_all("/\<img ([^\>]+)\>/i", $str, $ms);
		foreach ($ms[1] as $k => $m)
		{
			if ( ! preg_match("/alt=[\"|']/i", $m))
			{
				preg_match("/src=[\"|']([^\"]+)[\"|']/i", $m, $im);
				static::$error_ids['is_exist_alt_attr_of_img'][] = Util::s(@basename($im[1]));
				static::$errors[] = Util::s($ms[0][$k]);
			}
		}
	}

	/**
	 * is not empty alt attr of img inside a
	 *
	 * @param   strings     $str
	 * @return  void
	 */
	public static function is_not_empty_alt_attr_of_img_inside_a($str)
	{
		$str = static::ignore_elements($str);

		preg_match_all("/\<a +[^\>]+\>\<img ([^\>]+)\>\<\/a\>/i", $str, $ms);
		foreach ($ms[1] as $k => $m)
		{
			if (preg_match("/alt=[\"|'] *?[\"|']/i", $m))
			{
				preg_match("/src=[\"|']([^\"]+)[\"|']/i", $m, $im);
				if ($im)
				{
					static::$error_ids['is_not_empty_alt_attr_of_img_inside_a'][] = Util::s(@basename($im[1]));
				}
				else
				{
					static::$error_ids['is_not_empty_alt_attr_of_img_inside_a'][] = '" "';
				}
				static::$errors[] = Util::s($ms[0][$k]);
			}
		}
	}

	/**
	 * is not here link
	 *
	 * @param   strings     $str
	 * @return  bool
	 */
	public static function is_not_here_link($str)
	{
		$str = static::ignore_elements($str);

		preg_match_all(
			"/<a +[^>]*?href *?= *?['\"]([^\"]+?)['\"][^>]*?> *?".A11YC_LANG_HERE." *?<\/a>/i",
			$str,
			$ms);

		foreach ($ms[1] as $k => $m)
		{
			static::$error_ids['is_not_here_link'][] = @Util::s($m);
			static::$errors[] = Util::s($ms[0][$k]);
		}
	}

	/**
	 * is area has alt
	 *
	 * @param   strings     $str
	 * @return  bool
	 */
	public static function is_are_has_alt($str)
	{
		$str = static::ignore_elements($str);

		preg_match_all("/\<area ([^\>]+)\>/i", $str, $ms);
		foreach ($ms[1] as $k => $m)
		{
			if ( ! preg_match("/alt=[\"|']/i", $m) || preg_match("/alt=[\"|'] *?[\"|']/i", $m))
			{
				preg_match("/coords=[\"|']([^\"]+)[\"|']/i", $m, $im);
				static::$error_ids['is_are_has_alt'][] = Util::s(@basename($im[1]));
				static::$errors[] = Util::s($ms[0][$k]);
			}
		}
	}

	/**
	 * is img input has alt
	 *
	 * @param   strings     $str
	 * @return  bool
	 */
	public static function is_img_input_has_alt($str)
	{
		$str = static::ignore_elements($str);

		preg_match_all("/\<input ([^\>]+?)\>/i", $str, $ms);
		foreach($ms[1] as $k => $m){
			if (
				(strpos($m, 'image') && ! preg_match("/alt=[\"|']/i", $m)) ||
				(strpos($m, 'image') && preg_match("/alt=[\"|'] *?[\"|']/i", $m))
			)
			{
				preg_match("/src=[\"|']([^\"]+)[\"|']/i", $m, $im);
				static::$error_ids['is_img_input_has_alt'][] = Util::s(@basename($im[1]));
				static::$errors[] = Util::s($ms[0][$k]);
			}
		}
	}

	/**
	 * appropriate heading descending
	 *
	 * @param   strings     $str
	 * @return  bool
	 */
	public static function appropriate_heading_descending($str)
	{
		$str = static::ignore_elements($str);

		$secs = preg_split("/(\<h\d)[^\>]*\>(.+?)\<\/h\d/", $str, -1, PREG_SPLIT_DELIM_CAPTURE);

		$prev = 1;
		foreach ($secs as $k => $v)
		{
			if (strlen($v) != 3) continue; // skip non heading
			if (substr($v, 0, 2) != '<h') continue; // skip non heading
			$current_level = intval($v[2]);

			if ($current_level - $prev >= 2)
			{
				$str = isset($secs[$k + 1]) ? Util::s($secs[$k + 1]) : Util::s($v);
				static::$error_ids['appropriate_heading_descending'][] = $str;
				static::$errors[] = $str;
			}
			$prev = $current_level;
		}

	}

	/**
	 * suspicious_elements
	 *
	 * @param   strings     $str
	 * @return  bool
	 */
	public static function suspicious_elements($str)
	{
		$body_html = static::ignore_elements($str);

		// tags
		preg_match_all("/\<([^\>| ]+)/i", $body_html, $tags);

		// ignore elements
		$ignores = array('img', 'br', 'hr', 'base', 'input', 'param', 'area', 'embed', '!doctype', 'meta', 'link', 'html', '/html', '![if', '![endif]', '?xml', 'track', 'source');

		// tag suspicious elements
		$suspicious_opens = array();
		$suspicious_ends = array();
		foreach ($tags[1] as $tag)
		{
			if (in_array(strtolower($tag), $ignores)) continue; // ignore

			// collect tags
			if (substr($tag, 0, 1) =='/')
			{
				$suspicious_ends[] = substr($tag, 1);
			}
			else
			{
				$suspicious_opens[] = $tag;
			}
		}

		$suspicious_ends_results = array_diff($suspicious_ends, $suspicious_opens);
		$suspicious_opens_results = array_diff($suspicious_opens, $suspicious_ends);

		// add slash to end tags
		$suspicious_ends_results = array_map(function($s){return '/'.$s;} , $suspicious_ends_results);

		// suspicious
		$suspicious = array_merge(
			$suspicious_ends_results,
			$suspicious_opens_results);

		if ($suspicious)
		{
			foreach ($suspicious as $v)
			{
				static::$error_ids['suspicious_elements'][] = Util::s($v);
				static::$errors[] = Util::s('<'.$v);
			}
		}
	}

	/**
	 * is not same alt and filename of img
	 *
	 * @param   strings     $str
	 * @return  void
	 */
	public static function is_not_same_alt_and_filename_of_img($str)
	{
		$str = static::ignore_elements($str);

		preg_match_all("/\<img ([^\>]+)\>/i", $str, $ms);
		foreach ($ms[1] as $k => $m)
		{
			if (preg_match("/alt=[\"|']([^\"]+)[\"|']/i", $m, $m_alt))
			{
				preg_match("/src=[\"|']([^\"]+)[\"|']/i", $m, $m_src);
				$filename = basename($m_src[1]);
				if (
					$filename == $m_alt[1] || // within extension
					substr($filename, 0, strrpos($filename, '.')) == $m_alt[1] // without extension
				)
				{
					static::$error_ids['is_not_same_alt_and_filename_of_img'][] = Util::s($filename);
					static::$errors[] = Util::s($ms[0][$k]);
				}
			}
		}
	}

	/**
	 * is not exists ja word breaking space
	 *
	 * @param   strings     $str
	 * @return  void
	 */
	public static function is_not_exists_ja_word_breaking_space($str)
	{
		if (A11YC_LANG != 'ja') return false;
		$str = str_replace(array("\n", "\r"), '', $str);
		$str = static::ignore_elements($str, true);

		preg_match_all("/([^\x01-\x7E][ 　][ 　]+[^\x01-\x7E])/iu", $str, $ms);
		foreach ($ms[1] as $k => $m)
		{
			static::$error_ids['is_not_exists_ja_word_breaking_space'][] = Util::s($m);
			static::$errors[] = Util::s($ms[0][$k]);
		}
	}

	/**
	 * is not exists meanless element
	 *
	 * @param   strings     $str
	 * @return  void
	 */
	public static function is_not_exists_meanless_element($str)
	{
		$body_html = static::ignore_elements($str, true);

		$banneds = array(
			'center',
			'font',
			'blink',
			'marquee',
		);

		preg_match_all("/\<([^\>| ]+)/i", $body_html, $tags);

		foreach ($tags[1] as $tag)
		{
			if (in_array($tag, $banneds))
			{
				static::$error_ids['is_not_exists_meanless_element'][] = Util::s($tag);
				static::$errors[] = Util::s('<'.$tag);
			}
		}

	}

	/**
	 * is not style for structure
	 *
	 * @param   strings     $str
	 * @return  void
	 */
	public static function is_not_style_for_structure($str)
	{
		$str = static::ignore_elements($str, true);

		preg_match_all("/\<[a-zA-Z1-6]+? ([^\>]+)\>/i", $str, $ms);
		foreach ($ms[1] as $k => $m)
		{
			if (
				strpos($m, 'style=') !== false &&
				(
					strpos($m, 'size') !== false ||
					strpos($m, 'color') !== false
				)
			)
			{
				static::$error_ids['is_not_style_for_structure'][] = Util::s($m);
				static::$errors[] = Util::s($ms[0][$k]);
			}
		}

	}

	/**
	 * tell user file type
	 *
	 * @param   strings     $str
	 * @return  void
	 */
	public static function tell_user_file_type($str)
	{
		$str = static::ignore_elements($str, true);

		preg_match_all("/\<a [^\>]*href=[\"|']([^\"|']+?)[\"|'][^\>]*?\>([^\<|\>]+?)\<\/a\>/i", $str, $ms);
		$suspicious = array(
			'.pdf',
			'.doc',
			'.docx',
			'.xls',
			'.xlsx',
			'.ppt',
			'.pptx',
			'.zip',
			'.tar',
		);

		foreach ($ms[1] as $k => $m)
		{
			foreach ($suspicious as $vv)
			{
				if (strpos($m, $vv) !== false)
				{
					$val = $ms[2][$k];

					// allow application name
					if (
						(($vv == '.doc' || $vv == '.docx') && strpos($val, 'word') !== false) ||
						(($vv == '.xls' || $vv == '.xlsx') && strpos($val, 'excel') !== false) ||
						(($vv == '.ppt' || $vv == '.pptx') && strpos($val, 'power') !== false)
					)
					{
						$val.= 'doc,docx,xls,xlsx,ppt,pptx';
					}

					if (
						strpos($val, substr($vv, 1)) === false ||
						preg_match("/\d/", $val) == false
					)
					{
						static::$error_ids['tell_user_file_type'][] = Util::s($val);
						static::$errors[] = Util::s($ms[0][$k]);
					}

				}
			}
		}

	}

	/**
	 * titleless
	 *
	 * @param   strings     $str
	 * @return  void
	 */
	public static function titleless($str)
	{
		$str = static::ignore_elements($str, true);

		if ( ! preg_match("/\<title[^\>]*?\>/i", $str))
		{
			static::$error_ids['titleless'][] = 'title';
			static::$errors[] = Util::s('<title');
		}
	}

	/**
	 * langless
	 *
	 * @param   strings     $str
	 * @return  void
	 */
	public static function langless($str)
	{
		// do not use static::ignore_elements() in case it is in comment out

		if ( ! preg_match("/\<html[^\>]*?lang=[^\>]*?\>/i", $str))
		{
			static::$error_ids['langless'][] = 'language';
			static::$errors[] = Util::s('<html');
		}

	}

	/**
	 * is not exist same page title in same site
	 *
	 * @param   strings     $str
	 * @return  void
	 */
	public static function is_not_exist_same_page_title_in_same_site($str)
	{
		$title = Util::fetch_page_title_from_html($str);
		$sql = 'SELECT count(*) as num FROM '.A11YC_TABLE_PAGES.' WHERE `page_title` = ?;';
		$results = Db::fetch($sql, array($title));
		if (intval($results['num']) >= 2)
		{
			static::$error_ids['is_not_exist_same_page_title_in_same_site'][] = Util::s($title);
			static::$errors[] = Util::s('<title');
		}

	}

	/**
	 * link_check
	 *
	 * @param   strings     $str
	 * @return  void
	 */
	public static function link_check($str)
	{
		$str = static::ignore_elements($str, true);

		preg_match_all("/(?:href|src|cite|data|poster|action)=[\"|']([^\"]+)[\"|']/i", $str, $ms);
		$urls = array_map(function($v){if($v[0] == '/'){return \A11yc\Validate::$root_path.$v;}return $v;}, $ms[1]);
		$urls = array_unique($urls);

		// check
		foreach ($urls as $url)
		{
			$headers = @get_headers($url);
			if ($headers !== false)
			{
				// OK
				if (strpos($headers[0], ' 20') !== false) continue;

				// not OK
				static::$error_ids['link_check'][] = Util::s(substr($headers[0], strpos($headers[0], ' '))).': '.Util::s($url);
				static::$errors[] = Util::s($url);
			}
		}
	}
}
