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
	 * is exist alt attr of img
	 *
	 * @param   strings     $str
	 * @return  mixed
	 */
	public static function is_exist_alt_attr_of_img($str)
	{
		$error_ids = array();
		preg_match_all("/\<img ([^\>]+)\>/i", $str, $ms);
		foreach ($ms[1] as $k => $m)
		{
			if ( ! preg_match("/alt=[\"|']/i", $m))
			{
				preg_match("/src=\"([^\"]+)\"/i", $m, $im);
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
		preg_match_all("/\<a +[^\>]+\>\<img ([^\>]+)\>\<\/a\>/i", $str, $ms);
		foreach ($ms[1] as $k => $m)
		{
			if (preg_match("/alt=[\"|'] *?[\"|']/i", $m))
			{
				preg_match("/src=\"([^\"]+)\"/i", $m, $im);
				$error_ids[] = @basename($im[1]);
			}
		}
		return $error_ids ?: false;
	}

	/**
	 * is not here link
	 *
	 * @param   strings     $str
	 * @return  void
	 */
	public static function is_not_here_link($str)
	{
		$error_ids = array();
		preg_match_all(
			"/\<a +[^\>]*?href *?= *?['\"](.+?)['\"][^\>]*?\>".A11YC_LANG_HERE."\<\/a\>/i",
			strtolower($str),
			$ms);
		foreach ($ms[1] as $k => $m)
		{
			$error_ids[] = @basename($m[0]);
		}
		return $error_ids ?: false;
	}

	/**
	 * is area has alt
	 *
	 * @param   strings     $str
	 * @return  void
	 */
	public static function is_are_has_alt($str)
	{
		$error_ids = array();
		preg_match_all("/\<area ([^\>]+)\>/i", $str, $matches);
		foreach ($matches[1] as $k => $m)
		{
			if ( ! preg_match("/alt=[\"|']/i", $m) || preg_match("/alt=[\"|'] *?[\"|']/i", $m))
			{
				preg_match("/coords=\"([^\"]+)\"/i", $m, $im);
				$error_ids[] = @basename($im[1]);
			}
		}
		return $error_ids ?: false;
	}

	/**
	 * is img input has alt
	 *
	 * @param   strings     $str
	 * @return  void
	 */
	public static function is_img_input_has_alt($str)
	{
		$error_ids = array();
		preg_match_all("/\<input ([^\>]+)\>/i", $str, $matches);
		foreach($matches[1] as $k => $m){
			if (
				(strpos($m, 'image') && ! preg_match("/alt=[\"|']/i", $m)) ||
				(strpos($m, 'image') && preg_match("/alt=[\"|'] *?[\"|']/i", $m))
			)
			{
				preg_match("/src=\"([^\"]+)\"/i", $m, $im);
				$error_ids[] = @basename($im[1]);
			}
		}
		return $error_ids ?: false;
	}

	/**
	 * suspicious_elements
	 *
	 * @param   strings     $str
	 * @return  void
	 */
	public static function suspicious_elements($str)
	{
		$error_ids = array();
		$body_html = str_replace(array("\n", "\r"), '', $str);
		$body_html = strtolower($body_html);

		// ignore comment out, script, style
		$body_html = preg_replace("/\<!--.+?-->/i", '', $body_html);
		$body_html = preg_replace("/\<script.+?<\/script>/i", '', $body_html);
		$body_html = preg_replace("/\<style.+?<\/style>/i", '', $body_html);

		// tags
		preg_match_all("/\<([^\>| ]+)/i", $body_html, $tags);

		// ignore elements
		$ignores = array('img', 'br', 'hr', 'input', 'param', 'area', 'embed', '!doctype', 'meta', 'link', 'html', '/html');

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

		$suspicious = array_merge(
			array_diff($suspicious_opens, $suspicious_ends),
			array_diff($suspicious_ends, $suspicious_opens));

		if ($suspicious)
		{
			foreach ($suspicious as $v)
			{
				$error_ids[] = $v;
			}
		}
		return $error_ids ?: false;
	}
}
