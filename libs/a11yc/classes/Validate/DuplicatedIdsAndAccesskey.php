<?php
/**
 * A11yc\Validate\DuplicatedIdsAndAccesskey
 *
 * @package    part of A11yc
 * @author     Jidaikobo Inc.
 * @license    The MIT License (MIT)
 * @copyright  Jidaikobo Inc.
 * @link       http://www.jidaikobo.com
 */
namespace A11yc\Validate;

class DuplicatedIdsAndAccesskey extends Validate
{
	/**
	 * duplicated ids and accesskey
	 *
	 * @param  String $url
	 * @return Void
	 */
	public static function check($url)
	{
		$str = Element::ignoreElements(static::$hl_htmls[$url]);

		$ms = Element::getElementsByRe($str, 'ignores', 'tags');
		if ( ! $ms[0]) return;

		$ids = array();
		$accesskeys = array();
		foreach ($ms[0] as $k => $m)
		{
			$attrs = Element::getAttributes($m);

			// duplicated_ids
			if (isset($attrs['id']))
			{
				if (in_array($attrs['id'], $ids))
				{
					static::$error_ids[$url]['duplicated_ids'][$k]['id'] = $ms[0][$k];
					static::$error_ids[$url]['duplicated_ids'][$k]['str'] = $attrs['id'];
				}
				$ids[] = $attrs['id'];
			}

			// duplicated_accesskeys
			if (isset($attrs['accesskey']))
			{
				if (in_array($attrs['accesskey'], $accesskeys))
				{
					static::$error_ids[$url]['duplicated_accesskeys'][$k]['id'] = $ms[0][$k];
					static::$error_ids[$url]['duplicated_accesskeys'][$k]['str'] = $attrs['accesskey'];
				}
				$accesskeys[] = $attrs['accesskey'];
			}
		}
		static::addErrorToHtml($url, 'duplicated_ids', static::$error_ids[$url], 'ignores');
		static::addErrorToHtml($url, 'duplicated_accesskeys', static::$error_ids[$url], 'ignores');
	}
}
