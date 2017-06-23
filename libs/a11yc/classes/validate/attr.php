<?php
/**
 * A11yc\Validation
 *
 * @package    part of A11yc
 * @author     Jidaikobo Inc.
 * @license    The MIT License (MIT)
 * @copyright  Jidaikobo Inc.
 * @link       http://www.jidaikobo.com
 */
namespace A11yc;
class Validate_Attr extends Validate
{
	/**
	 * suspicious attributes
	 *
	 * @return  void
	 */
	public static function suspicious_attributes()
	{
		$str = static::ignore_elements(static::$hl_html);

		$ms = static::get_elements_by_re($str, 'ignores', 'tags');
		if ( ! $ms[0]) return;

		foreach ($ms[0] as $k => $m)
		{
			$attrs = static::get_attributes($m);

			// suspicious attributes
			if (isset($attrs['suspicious']))
			{
				static::$error_ids['suspicious_attributes'][$k]['id'] = $ms[0][$k];
				static::$error_ids['suspicious_attributes'][$k]['str'] = join(', ', $attrs['suspicious']);
			}

			// duplicated_attributes
			if (isset($attrs['plural']))
			{
				static::$error_ids['duplicated_attributes'][$k]['id'] = $ms[0][$k];
				static::$error_ids['duplicated_attributes'][$k]['str'] = $m;
			}
		}
		static::add_error_to_html('suspicious_attributes', static::$error_ids, 'ignores');
		static::add_error_to_html('duplicated_attributes', static::$error_ids, 'ignores');
	}

	/**
	 * duplicated ids and accesskey
	 *
	 * @return  void
	 */
	public static function duplicated_ids_and_accesskey()
	{
		$str = static::ignore_elements(static::$hl_html);

		$ms = static::get_elements_by_re($str, 'ignores', 'tags');
		if ( ! $ms[0]) return;

		$ids = array();
		$accesskeys = array();
		foreach ($ms[0] as $k => $m)
		{
			$attrs = static::get_attributes($m);

			// duplicated_ids
			if (isset($attrs['id']))
			{
				if (in_array($attrs['id'], $ids))
				{
					static::$error_ids['duplicated_ids'][$k]['id'] = $ms[0][$k];
					static::$error_ids['duplicated_ids'][$k]['str'] = $attrs['id'];
				}
				$ids[] = $attrs['id'];
			}

			// duplicated_accesskeys
			if (isset($attrs['accesskey']))
			{
				if (in_array($attrs['accesskey'], $accesskeys))
				{
					static::$error_ids['duplicated_accesskeys'][$k]['id'] = $ms[0][$k];
					static::$error_ids['duplicated_accesskeys'][$k]['str'] = $attrs['accesskey'];
				}
				$accesskeys[] = $attrs['accesskey'];
			}
		}
		static::add_error_to_html('duplicated_ids', static::$error_ids, 'ignores');
		static::add_error_to_html('duplicated_accesskeys', static::$error_ids, 'ignores');
	}

	/**
	 * titleless_frame
	 *
	 * @return  void
	 */
	public static function titleless_frame()
	{
		$str = static::ignore_elements(static::$hl_html);
		$ms = static::get_elements_by_re($str, 'ignores', 'tags');
		if ( ! $ms[0]) return;

		foreach ($ms[0] as $k => $v)
		{
			if ($ms[1][$k] != 'frame' && $ms[1][$k] != 'iframe') continue;
			$attrs = static::get_attributes($v);

			if ( ! trim(Arr::get($attrs, 'title')))
			{
				static::$error_ids['titleless_frame'][$k]['id'] = $ms[0][$k];
				static::$error_ids['titleless_frame'][$k]['str'] = $ms[0][$k];
			}
		}
		static::add_error_to_html('titleless_frame', static::$error_ids, 'ignores');
	}

	/**
	 * numeric attr
	 *
	 * @return  bool
	 */
	public static function must_be_numeric_attr()
	{
		$str = static::ignore_elements(static::$hl_html);
		$ms = static::get_elements_by_re($str, 'ignores', 'tags');
		if ( ! $ms[0]) return;

		$targets = array(
			'width',
			'height',
			'border',
		);

		foreach ($ms[0] as $k => $v)
		{
			$attrs = static::get_attributes($v);

			foreach ($attrs as $attr => $val)
			{
				if ( ! in_array($attr, $targets)) continue;
				if ( ! is_numeric($val))
				{
					static::$error_ids['must_be_numeric_attr'][$k]['id'] = $v;
					static::$error_ids['must_be_numeric_attr'][$k]['str'] = $attr;
				}
			}
		}
		static::add_error_to_html('must_be_numeric_attr', static::$error_ids, 'ignores');
	}
}
