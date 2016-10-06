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
	/**
	 * ignore_elements
	 *
	 * @param   strings     $str
	 * @return  $str
	 */
	public static function ignore_elements($str, $force = false)
	{
		static $retval = '';
		if ($retval && ! $force) return $retval;

		$retval = str_replace(array("\n", "\r"), ' ', $str);
		$retval = strtolower($retval);

		// ignore comment out, script, style
		$retval = preg_replace("/\<!--.+?-->/i", '', $retval);
		$retval = preg_replace("/\<script.+?<\/script>/i", '', $retval);
		$retval = preg_replace("/\<style.+?<\/style>/i", '', $retval);

		return $retval;
	}

	/**
	 * is exist alt attr of img
	 *
	 * @param   strings     $str
	 * @return  mixed
	 */
	public static function is_exist_alt_attr_of_img($str)
	{
		$error_ids = array();
		$str = static::ignore_elements($str);

		preg_match_all("/\<img ([^\>]+)\>/i", $str, $ms);
		foreach ($ms[1] as $k => $m)
		{
			if ( ! preg_match("/alt=[\"|']/i", $m))
			{
				preg_match("/src=[\"|']([^\"]+)[\"|']/i", $m, $im);
				$error_ids[] = @basename($im[1]);
			}
		}
		return $error_ids ?: false;
	}

	/**
	 * is not empty alt attr of img inside a
	 *
	 * @param   strings     $str
	 * @return  mixed
	 */
	public static function is_not_empty_alt_attr_of_img_inside_a($str)
	{
		$error_ids = array();
		$str = static::ignore_elements($str);

		preg_match_all("/\<a +[^\>]+\>\<img ([^\>]+)\>\<\/a\>/i", $str, $ms);
		foreach ($ms[1] as $k => $m)
		{
			if (preg_match("/alt=[\"|'] *?[\"|']/i", $m))
			{
				preg_match("/src=[\"|']([^\"]+)[\"|']/i", $m, $im);
				$error_ids[] = @basename($im[1]);
			}
		}
		return $error_ids ?: false;
	}

	/**
	 * is not here link
	 *
	 * @param   strings     $str
	 * @return  bool
	 */
	public static function is_not_here_link($str)
	{
		$error_ids = array();
		$str = static::ignore_elements($str);

		preg_match_all(
			"/<a +[^>]*?href *?= *?['\"]([^\"]+?)['\"][^>]*?> *?".A11YC_LANG_HERE." *?<\/a>/i",
			strtolower($str),
			$ms);

		foreach ($ms[1] as $k => $m)
		{
			$error_ids[] = @Util::s($m);
		}
		return $error_ids ?: false;
	}

	/**
	 * is area has alt
	 *
	 * @param   strings     $str
	 * @return  bool
	 */
	public static function is_are_has_alt($str)
	{
		$error_ids = array();
		$str = static::ignore_elements($str);

		preg_match_all("/\<area ([^\>]+)\>/i", $str, $matches);
		foreach ($matches[1] as $k => $m)
		{
			if ( ! preg_match("/alt=[\"|']/i", $m) || preg_match("/alt=[\"|'] *?[\"|']/i", $m))
			{
				preg_match("/coords=[\"|']([^\"]+)[\"|']/i", $m, $im);
				$error_ids[] = @basename($im[1]);
			}
		}
		return $error_ids ?: false;
	}

	/**
	 * is img input has alt
	 *
	 * @param   strings     $str
	 * @return  bool
	 */
	public static function is_img_input_has_alt($str)
	{
		$error_ids = array();
		$str = static::ignore_elements($str);

		preg_match_all("/\<input ([^\>]+)\>/i", $str, $matches);
		foreach($matches[1] as $k => $m){
			if (
				(strpos($m, 'image') && ! preg_match("/alt=[\"|']/i", $m)) ||
				(strpos($m, 'image') && preg_match("/alt=[\"|'] *?[\"|']/i", $m))
			)
			{
				preg_match("/src=[\"|']([^\"]+)[\"|']/i", $m, $im);
				$error_ids[] = @basename($im[1]);
			}
		}
		return $error_ids ?: false;
	}

	/**
	 * appropriate heading descending
	 *
	 * @param   strings     $str
	 * @return  bool
	 */
	public static function appropriate_heading_descending($str)
	{
		$error_ids = array();
		$str = static::ignore_elements($str);

		$secs = preg_split("/(<h\d)>(.+?)<\/h/", $str, -1, PREG_SPLIT_DELIM_CAPTURE);
		$prev = 1;
		foreach ($secs as $k => $v)
		{
			if (strlen($v) != 3) continue; // skip non heading
			$current_level = intval($v[2]);
			if ($current_level - $prev >= 2)
			{
				if (isset($secs[$k + 1]))
				{
					$error_ids[] = $secs[$k + 1];
				}
				else
				{
					$error_ids[] = $v;
				}
			}
			$prev = $current_level;
		}

		return $error_ids ?: false;
	}

	/**
	 * suspicious_elements
	 *
	 * @param   strings     $str
	 * @return  bool
	 */
	public static function suspicious_elements($str)
	{
		$error_ids = array();
		$body_html = static::ignore_elements($str);

		// tags
		preg_match_all("/\<([^\>| ]+)/i", $body_html, $tags);

		// ignore elements
		$ignores = array('img', 'br', 'hr', 'input', 'param', 'area', 'embed', '!doctype', 'meta', 'link', 'html', '/html', '![if', '![endif]', '?xml', 'track', 'source');

		// tag suspicious elements
		$suspicious_opens = array();
		$suspicious_ends = array();
		foreach ($tags[1] as $tag)
		{
			if (in_array($tag, $ignores)) continue; // ignore

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
				$error_ids[] = $v;
			}
		}
		return $error_ids ?: false;
	}

	/**
	 * is not same alt and filename of img
	 *
	 * @param   strings     $str
	 * @return  mixed
	 */
	public static function is_not_same_alt_and_filename_of_img($str)
	{
		$error_ids = array();
		$str = static::ignore_elements($str);

		preg_match_all("/\<img ([^\>]+)\>/i", $str, $ms);
		foreach ($ms[1] as $k => $m)
		{
			if (preg_match("/alt=[\"|']([^\"]+)[\"|']/i", $m, $m_alt))
			{
				preg_match("/src=[\"|']([^\"]+)[\"|']/i", $m, $m_src);
				$filename = basename($m_src[1]);
				if ($filename == $m_alt[1])
				{
					$error_ids[] = $filename;
				}
			}
		}
		return $error_ids ?: false;
	}

	/**
	 * is not exists ja word breaking space
	 *
	 * @param   strings     $str
	 * @return  mixed
	 */
	public static function is_not_exists_ja_word_breaking_space($str)
	{
		if (A11YC_LANG != 'ja') return false;
		$error_ids = array();
		$str = str_replace(array("\n", "\r"), '', $str);
		$str = static::ignore_elements($str, true);

		preg_match_all("/([^\x01-\x7E][ |　][ |　]+[^\x01-\x7E])/u", $str, $ms);
		foreach ($ms[1] as $k => $m)
		{
			$error_ids[] = $m;
		}

		return $error_ids ?: false;
	}

	/**
	 * is not exists meanless element
	 *
	 * @param   strings     $str
	 * @return  mixed
	 */
	public static function is_not_exists_meanless_element($str)
	{
		$error_ids = array();
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
				$error_ids[] = $tag;
			}
		}

		return $error_ids ?: false;
	}

	/**
	 * is not style for structure
	 *
	 * @param   strings     $str
	 * @return  mixed
	 */
	public static function is_not_style_for_structure($str)
	{
		$error_ids = array();
		$str = static::ignore_elements($str, true);

		preg_match_all("/\<[a-zA-Z1-6]+? ([^\>]+)\>/i", $str, $ms);
		foreach ($ms[1] as $k => $m)
		{
			if (strpos($m, 'style=') !== false)
			{
				$error_ids[] = $m;
			}
		}

		return $error_ids ?: false;
	}

	/**
	 * tell user file type
	 *
	 * @param   strings     $str
	 * @return  mixed
	 */
	public static function tell_user_file_type($str)
	{
		$error_ids = array();
		$str = static::ignore_elements($str, true);

		preg_match_all("/\<a [^\>]*href=[\"|']([^\"|']+?)[\"|'][^\>]*?\>([^\<|\>]+?)\<\/a\>/i", $str, $ms);
		$suspicious = array(
			'pdf',
			'doc',
			'docx',
			'xls',
			'xlsx',
			'ppt',
			'pptx',
			'zip',
			'tar',
		);

		foreach ($ms[1] as $k => $m)
		{
			foreach ($suspicious as $vv)
			{
				if (strpos($m, $vv) !== false)
				{
					$val = $ms[2][$k];
					if (
						(($vv == 'doc' || $vv == 'docx') && strpos($val, 'word') !== false) ||
						(($vv == 'xls' || $vv == 'xlsx') && strpos($val, 'excel') !== false) ||
						(($vv == 'ppt' || $vv == 'pptx') && strpos($val, 'power') !== false)
					)
					{
						$val.= 'doc,docx,xls,xlsx,ppt,pptx';
					}
					if (
						strpos($val, $vv) === false ||
						preg_match("/\d/", $val) == false
					)
					{
						$error_ids[] = $m;
					}

				}
			}
		}

		return $error_ids ?: false;
	}

	/**
	 * titleless
	 *
	 * @param   strings     $str
	 * @return  mixed
	 */
	public static function titleless($str)
	{
		$error_ids = array();
		$str = static::ignore_elements($str, true);

		if ( ! preg_match("/\<title[^\>]*?\>/u", $str))
		{
			$error_ids[] = 'title';
		}

		return $error_ids ?: false;
	}

	/**
	 * langless
	 *
	 * @param   strings     $str
	 * @return  mixed
	 */
	public static function langless($str)
	{
		$error_ids = array();
		$str = static::ignore_elements($str, true);

		if ( ! preg_match("/\<html[^\>]*?lang=[^\>]*?\>/u", $str))
		{
			$error_ids[] = 'language';
		}

		return $error_ids ?: false;
	}
}
