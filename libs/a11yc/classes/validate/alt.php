<?php
/**
 * A11yc\Validate_Alt
 *
 * @package    part of A11yc
 * @author     Jidaikobo Inc.
 * @license    The MIT License (MIT)
 * @copyright  Jidaikobo Inc.
 * @link       http://www.jidaikobo.com
 */
namespace A11yc;

class Validate_Alt extends Validate
{
	/**
	 * img exists
	 *
	 * @return Void
	 */
	public static function notice_img_exists()
	{
		$str = static::ignore_elements(static::$hl_html);

		$ms = static::get_elements_by_re($str, 'ignores', 'imgs');
		if ( ! $ms[1]) return;

		static::$error_ids['notice_img_exists'][0]['id'] = 0;
		static::$error_ids['notice_img_exists'][0]['str'] = A11YC_LANG_IMAGE.' '.sprintf(A11YC_LANG_COUNT_ITEMS, count($ms[1]));
		static::add_error_to_html('notice_img_exists', static::$error_ids, 'ignores');
	}

	/**
	 * alt attr of img
	 *
	 * @return Void
	 */
	public static function alt_attr_of_img()
	{
		$str = static::ignore_elements(static::$hl_html);

		$ms = static::get_elements_by_re($str, 'ignores', 'imgs');
		if ( ! $ms[1]) return;

		foreach ($ms[1] as $k => $m)
		{
			// alt_attr_of_img
			$attrs = static::get_attributes($m);
			if ( ! array_key_exists('alt', $attrs))
			{
				static::$error_ids['alt_attr_of_img'][$k]['id'] = $ms[0][$k];
				static::$error_ids['alt_attr_of_img'][$k]['str'] = @basename(@$attrs['src']);

				// below here alt attribute has to exist.
				continue;
			}

			// role presentation
			if (isset($attrs['role']) && $attrs['role'] == 'presentation') continue;

			// alt_attr_of_blank_only
			if (preg_match('/^[ 　]+?$/', $attrs['alt']))
			{
				static::$error_ids['alt_attr_of_blank_only'][$k]['id'] = $ms[0][$k];
				static::$error_ids['alt_attr_of_blank_only'][$k]['str'] = @basename(@$attrs['src']);
			}

			// alt_attr_of_empty
			if (empty($attrs['alt']))
			{
				static::$error_ids['alt_attr_of_empty'][$k]['id'] = $ms[0][$k];
				static::$error_ids['alt_attr_of_empty'][$k]['str'] = @basename(@$attrs['src']);
			}
		}
		static::add_error_to_html('alt_attr_of_empty', static::$error_ids, 'ignores');
		static::add_error_to_html('alt_attr_of_img', static::$error_ids, 'ignores');
		static::add_error_to_html('alt_attr_of_blank_only', static::$error_ids, 'ignores');
	}

	/**
	 * empty alt attr of img inside a
	 *
	 * @return Void
	 */
	public static function empty_alt_attr_of_img_inside_a()
	{
		$str = static::ignore_elements(static::$hl_html);

		$ms = static::get_elements_by_re($str, 'ignores', 'anchors_and_values');
		if ( ! $ms[2]) return;

		foreach ($ms[2] as $k => $m)
		{
			if (strpos($m, '<img') === false) continue; // without image
			if (static::is_ignorable($ms[0][$k])) continue; // ignorable
			$t = trim(strip_tags($m)); // php <= 5.5 cannot use function return value
			if ( ! empty($t)) continue; // not image only
			$attrs = static::get_attributes($m);
			$alt = '';

			foreach ($attrs as $kk => $vv)
			{
				if (strpos($kk, 'alt') !== false)
				{
					$alt.= $vv;
				}
			}
			$alt = trim($alt);

			if ( ! $alt)
			{
				static::$error_ids['empty_alt_attr_of_img_inside_a'][$k]['id'] = $ms[0][$k];
				static::$error_ids['empty_alt_attr_of_img_inside_a'][$k]['str'] = @basename(@$attrs['src']);
			}
		}
		static::add_error_to_html('empty_alt_attr_of_img_inside_a', static::$error_ids, 'ignores');
	}

	/**
	 * area has alt
	 *
	 * @return  bool
	 */
	public static function area_has_alt()
	{
		$str = static::ignore_elements(static::$hl_html);

		$ms = static::get_elements_by_re($str, 'ignores', 'tags');
		if ( ! $ms[0]) return;

		foreach ($ms[0] as $k => $m)
		{
			if (substr($m, 0, 5) !== '<area') continue;
			$attrs = static::get_attributes($m);
			if ( ! isset($attrs['alt']) || empty($attrs['alt']))
			{
				static::$error_ids['area_has_alt'][$k]['id'] = $ms[0][$k];
				static::$error_ids['area_has_alt'][$k]['str'] = @basename(@$attrs['href']);
			}
		}
		static::add_error_to_html('area_has_alt', static::$error_ids, 'ignores');
	}

	/**
	 * is img input has alt
	 *
	 * @return  bool
	 */
	public static function img_input_has_alt()
	{
		$str = static::ignore_elements(static::$hl_html);

		$ms = static::get_elements_by_re($str, 'ignores', 'tags');
		if ( ! $ms[0]) return;

		foreach($ms[0] as $k => $m)
		{
			if (substr($m, 0, 6) !== '<input') continue;
			$attrs = static::get_attributes($m);
			if ( ! isset($attrs['type'])) continue; // unless type it is recognized as a text at html5
			if (isset($attrs['type']) && $attrs['type'] != 'image') continue;

			if ( ! isset($attrs['alt']) || empty($attrs['alt']))
			{
				static::$error_ids['img_input_has_alt'][$k]['id'] = $ms[0][$k];
				static::$error_ids['img_input_has_alt'][$k]['str'] = @basename(@$attrs['src']);
			}
		}
		static::add_error_to_html('img_input_has_alt', static::$error_ids, 'ignores');
	}

	/**
	 * same alt and filename of img
	 *
	 * @return Void
	 */
	public static function same_alt_and_filename_of_img()
	{
		$str = static::ignore_elements(static::$hl_html);
		$ms = static::get_elements_by_re($str, 'ignores', 'imgs');
		if ( ! $ms[1]) return;

		foreach ($ms[1] as $k => $m)
		{
			$attrs = static::get_attributes($m);
			if ( ! isset($attrs['alt']) || ! isset($attrs['src'])) continue;
			if (empty($attrs['alt'])) continue;

			$filename = basename($attrs['src']);
			if (
				$attrs['alt'] == $filename || // within extension
				$attrs['alt'] == substr($filename, 0, strrpos($filename, '.')) || // without extension
				$attrs['alt'] == substr($filename, 0, strrpos($filename, '-')) // without size
			)
			{
				static::$error_ids['same_alt_and_filename_of_img'][$k]['id'] = $ms[0][$k];
				static::$error_ids['same_alt_and_filename_of_img'][$k]['str'] = '"'.$filename.'"';
			}
		}
		static::add_error_to_html('same_alt_and_filename_of_img', static::$error_ids, 'ignores');
	}

	/**
	 * get_images
	 * This Method is NOT a validator
	 * @return  array()
	 */
	public static function get_images()
	{
		$retvals = array();
		$str = static::ignore_elements(static::$hl_html);

		// at first, get images in a
		preg_match_all('/\<a [^\>]+\>.+?\<\/a\>/is', $str, $as);
		$n = 0;
		foreach ($as[0] as $v)
		{
			if (strpos($v, '<img ') === false) continue;

			// link
			$attrs = static::get_attributes($v);
			$href = Arr::get($attrs, 'href');
			$aria_hidden = Arr::get($attrs, 'aria-hidden');
			$tabindex = Arr::get($attrs, 'tabindex');

			// plural images can be exist.
			preg_match_all('/\<img[^\>]+\>/is', $v, $ass);
			foreach ($ass[0] as $vv)
			{
				$retvals[$n]['element'] = 'img (a)';
				$retvals[$n]['is_important'] = true;
				$retvals[$n]['href'] = $href;
				$retvals[$n]['aria_hidden'] = $aria_hidden;
				$retvals[$n]['tabindex'] = $tabindex;
				$retvals[$n]['attrs'] = static::get_attributes($vv);
				$n++;
			}

			// remove a within images
			$str = str_replace($v, '', $str);
		}

		// secondary, get areas
		preg_match_all('/\<area [^\>]+\>/is', $str, $as);
		foreach ($as[0] as $v)
		{
			// link
			$attrs = static::get_attributes($v);
			$retvals[$n]['element'] = 'area';
			$retvals[$n]['is_important'] = true;
			$retvals[$n]['href'] = Arr::get($attrs, 'href');
			$retvals[$n]['attrs'] = $attrs;
			$n++;

			// remove a within images
			$str = str_replace($v, '', $str);
		}

		// get buttons
		preg_match_all('/\<button [^\>]+\>.+?\<\/button\>/is', $str, $as);
		foreach ($as[0] as $v)
		{
			if (strpos($v, '<img ') === false) continue;

			// link
			$attrs = static::get_attributes($v);
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
				$retvals[$n]['attrs'] = static::get_attributes($vv);
				$n++;
			}

			// remove a within images
			$str = str_replace($v, '', $str);
		}

		// input and img
		$ms = static::get_elements_by_re($str, 'ignores', 'tags', $force = true);

		$targets = array('img', 'input');
		foreach ($ms[1] as $k => $v)
		{
			if ( ! in_array($v, $targets)) continue;
			$attrs = static::get_attributes($ms[0][$k]);
			if ($v == 'input' && ( ! isset($attrs['type']) || $attrs['type'] != 'image')) continue;

			$retvals[$n]['element'] = $v;
			$retvals[$n]['is_important'] = $v == 'input' ? true : false ;
			$retvals[$n]['href'] = NULL;
			$retvals[$n]['attrs'] = $attrs;
			$n++;
		}

		// tidy
		foreach ($retvals as $k => $v)
		{
			// src exists
			if (isset($v['attrs']['src']))
			{
				$retvals[$k]['attrs']['src'] = Crawl::keep_url_unique($v['attrs']['src']);
			}

			// alt exists
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
}
