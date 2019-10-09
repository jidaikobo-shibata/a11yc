<?php
/**
 * A11yc\Image
 *
 * @package    part of A11yc
 * @author     Jidaikobo Inc.
 * @license    The MIT License (MIT)
 * @copyright  Jidaikobo Inc.
 * @link       http://www.jidaikobo.com
 */
namespace A11yc;

use A11yc\Model;

class Image
{
	/**
	 * get images
	 *
	 * @param String $url
	 * @param String $base_uri
	 * @param String $target_html
	 * @return  Array
	 */
	public static function getImages($url, $base_uri = '', $target_html = '')
	{
		$retvals = array();
		$str = empty($target_html) ? Element\Get::ignoredHtml($url) : $target_html;
		$n = 0;

		// at first, get images in a
		list($retvals, $str, $n) = self::imagesInA($n, $str, $retvals);

		// secondary, get areas
		list($retvals, $str, $n) = self::getArea($n, $str, $retvals);

		// get buttons
		list($retvals, $str, $n) = self::getButton($n, $str, $retvals);

		// input and img
		$retvals = self::getInput($n, $str, $retvals);

		// tidy
		foreach ($retvals as $k => $v)
		{
			// src exists
			if (isset($v['attrs']['src']))
			{
				$retvals[$k]['attrs']['src'] = Util::enuniqueUri($v['attrs']['src'], $base_uri);
			}

			// alt exists
			$retvals = self::tidyAlt($k, $v, $retvals);

			// aria-*
			$retvals[$k]['aria'] = array();
			foreach ($retvals[$k]['attrs'] as $kk => $vv)
			{
				if (substr($kk, 0, 5) != 'aria-') continue;
				$retvals[$k]['aria'][$kk] = $vv;
			}
		}

		return $retvals;
	}

	/**
	 * imagesInA
	 *
	 * @param Integer $n
	 * @param String $str
	 * @param Array $retvals
	 * @return  Array
	 */
	private static function imagesInA($n, $str, $retvals)
	{
		preg_match_all('/\<a [^\>]+\>.+?\<\/a\>/is', $str, $as);
		foreach ($as[0] as $v)
		{
			if (strpos($v, '<img ') === false) continue;

			// link
			$attrs       = Element\Get::attributes($v);
			$href        = Arr::get($attrs, 'href');
			$aria_hidden = Arr::get($attrs, 'aria-hidden');
			$tabindex    = Arr::get($attrs, 'tabindex');
			$text_in_a   = Util::s(strip_tags($v));

			// plural images can be exist.
			preg_match_all('/\<img[^\>]+\>/is', $v, $ass);

			foreach ($ass[0] as $vv)
			{
				$retvals[$n]['element'] = 'img (a)';
				$retvals[$n]['is_important'] = true;
				$retvals[$n]['href'] = $href;
				$retvals[$n]['aria_hidden'] = $aria_hidden;
				$retvals[$n]['tabindex'] = $tabindex;
				$retvals[$n]['attrs'] = Element\Get::attributes($vv);
				$retvals[$n]['near_text'] = $text_in_a;
				$n++;
			}

			// remove a within images
			$str = str_replace($v, '', $str);
		}

		return array($retvals, $str, $n);
	}

	/**
	 * get Area
	 *
	 * @param Integer $n
	 * @param String $str
	 * @param Array $retvals
	 * @return  Array
	 */
	private static function getArea($n, $str, $retvals)
	{
		preg_match_all('/\<area [^\>]+\>/is', $str, $as);
		foreach ($as[0] as $v)
		{
			// link
			$attrs = Element\Get::attributes($v);
			$retvals[$n]['element'] = 'area';
			$retvals[$n]['is_important'] = true;
			$retvals[$n]['href'] = Arr::get($attrs, 'href');
			$retvals[$n]['attrs'] = $attrs;
			$n++;

			// remove a within images
			$str = str_replace($v, '', $str);
		}
		return array($retvals, $str, $n);
	}

	/**
	 * get Button
	 *
	 * @param Integer $n
	 * @param String $str
	 * @param Array $retvals
	 * @return  Array
	 */
	private static function getButton($n, $str, $retvals)
	{
		preg_match_all('/\<button [^\>]+\>.+?\<\/button\>/is', $str, $as);
		foreach ($as[0] as $v)
		{
			if (strpos($v, '<img ') === false) continue;

			// link
			$attrs = Element\Get::attributes($v);
			$aria_hidden = Arr::get($attrs, 'aria-hidden');
			$tabindex = Arr::get($attrs, 'tabindex');

			// plural images can be exist.
			preg_match_all('/\<img[^\>]+\>/is', $v, $ass);
			foreach ($ass[0] as $vv)
			{
				$retvals[$n]['element'] = 'img (button)';
				$retvals[$n]['href'] = null;
				$retvals[$n]['is_important'] = 1;
				$retvals[$n]['aria_hidden'] = $aria_hidden;
				$retvals[$n]['tabindex'] = $tabindex;
				$retvals[$n]['attrs'] = Element\Get::attributes($vv);
				$n++;
			}

			// remove a within images
			$str = str_replace($v, '', $str);
		}
		return array($retvals, $str, $n);
	}

	/**
	 * get Input
	 *
	 * @param Integer $n
	 * @param String $str
	 * @param Array $retvals
	 * @return  Array
	 */
	private static function getInput($n, $str, $retvals)
	{
		$force = true;
		$ms = Element\Get::elementsByRe($str, 'ignores', 'tags', $force);

		if ( ! is_array($ms[1])) return $retvals;

		$targets = array('img', 'input');
		foreach ($ms[1] as $k => $v)
		{
			if ( ! in_array($v, $targets)) continue;
			$attrs = Element\Get::attributes($ms[0][$k]);
			if ($v == 'input' && ( ! isset($attrs['type']) || $attrs['type'] != 'image')) continue;

			$retvals[$n]['element'] = $v;
			$retvals[$n]['is_important'] = $v == 'input' ? true : false ;
			$retvals[$n]['href'] = NULL;
			$retvals[$n]['attrs'] = $attrs;
			$n++;
		}
		return $retvals;
	}

	/**
	 * tidy alt
	 *
	 * @param Integer $k
	 * @param Array $v
	 * @param Array $retvals
	 * @return  Array
	 */
	private static function tidyAlt($k, $v, $retvals)
	{
		if (isset($v['attrs']['alt']))
		{
			// empty alt
			if (empty($v['attrs']['alt']))
			{
				$retvals[$k]['attrs']['alt'] = '';
			}
			else
			{
				// alt of blank chars
				$alt = str_replace('　', ' ', $v['attrs']['alt']);
				$alt = trim($alt);

				if (empty($alt))
				{
					$retvals[$k]['attrs']['alt'] = '===a11yc_alt_of_blank_chars===';
				}
				// alt text
				else
				{
					$retvals[$k]['attrs']['alt'] = $v['attrs']['alt'];
				}
			}

			// newline in attr
			$retvals[$k]['attrs']['newline'] = preg_match("/[\n\r]/is", $v['attrs']['alt']);
		}
		return $retvals;
	}
}
