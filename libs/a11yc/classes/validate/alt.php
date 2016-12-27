<?php
/**
 * A11yc\Validate_Alt
 *
 * @package    part of A11yc
 * @version    1.0
 * @author     Jidaikobo Inc.
 * @license    The MIT License (MIT)
 * @copyright  Jidaikobo Inc.
 * @link       http://www.jidaikobo.com
 */
namespace A11yc;
class Validate_Alt extends Validate
{
	/**
	 * alt attr of img
	 *
	 * @return  void
	 */
	public static function alt_attr_of_img()
	{
		$str = static::ignore_elements(static::$hl_html);

		$ms = static::get_elements_by_re($str, 'imgs');
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
	 * @return  void
	 */
	public static function empty_alt_attr_of_img_inside_a()
	{
		$str = static::ignore_elements(static::$hl_html);

		$ms = static::get_elements_by_re($str, 'anchors_and_values');
		if ( ! $ms[2]) return;

		foreach ($ms[2] as $k => $m)
		{
			if (strpos($m, '<img') === false) continue; // without image
			if (static::is_ignorable($ms[0][$k])) continue; // ignorable
			if ( ! empty(trim(strip_tags($m)))) continue; // not image only
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

		$ms = static::get_elements_by_re($str, 'tags');
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

		$ms = static::get_elements_by_re($str, 'tags');
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
	 * @return  void
	 */
	public static function same_alt_and_filename_of_img()
	{
		$str = static::ignore_elements(static::$hl_html);
		$ms = static::get_elements_by_re($str, 'imgs');
		if ( ! $ms[1]) return;

		foreach ($ms[1] as $k => $m)
		{
			$attrs = static::get_attributes($m);
			if ( ! isset($attrs['alt']) || ! isset($attrs['src'])) continue;
			if (empty($attrs['alt'])) continue;

			$filename = basename($attrs['src']);
			if (
				$attrs['alt'] == $filename || // within extension
				$attrs['alt'] == substr($filename, 0, strrpos($filename, '.')) // without extension
			)
			{
				static::$error_ids['same_alt_and_filename_of_img'][$k]['id'] = $ms[0][$k];
				static::$error_ids['same_alt_and_filename_of_img'][$k]['str'] = '"'.$filename.'"';
			}
		}
		static::add_error_to_html('same_alt_and_filename_of_img', static::$error_ids, 'ignores');
	}
}
