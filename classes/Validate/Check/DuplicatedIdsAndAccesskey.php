<?php
/**
 * * A11yc\Validate\Check\DuplicatedIdsAndAccesskey
 *
 * @package    part of A11yc
 * @author     Jidaikobo Inc.
 * @license    The MIT License (MIT)
 * @copyright  Jidaikobo Inc.
 * @link       http://www.jidaikobo.com
 */
namespace A11yc\Validate\Check;

use A11yc\Element;
use A11yc\Validate;

class DuplicatedIdsAndAccesskey extends Validate
{
	/**
	 * duplicated ids and accesskey
	 *
	 * @param String $url
	 * @return Void
	 */
	public static function check($url)
	{
		Validate\Set::log($url, 'duplicated_ids', self::$unspec, 1);
		Validate\Set::log($url, 'duplicated_accesskeys', self::$unspec, 1);
		$str = Element\Get::ignoredHtml($url);

		$ms = Element\Get::elementsByRe($str, 'ignores', 'tags');
		if ( ! $ms[0]) return;

		$is_exists_id = false;
		$is_exists_ack = false;
		$ids = array();
		$accesskeys = array();
		foreach ($ms[0] as $k => $m)
		{
			$attrs = Element\Get::attributes($m);
			$tstr = $ms[0][$k];

			// duplicated_ids
			if (isset($attrs['id']))
			{
				$is_exists_id = true;
				if (in_array($attrs['id'], $ids))
				{
					Validate\Set::error($url, 'duplicated_ids', $k, $tstr, $attrs['id']);
				}
				$ids[] = $attrs['id'];
			}

			// duplicated_accesskeys
			if (isset($attrs['accesskey']))
			{
				$is_exists_ack = true;
				if (in_array($attrs['accesskey'], $accesskeys))
				{
					Validate\Set::error($url, 'duplicated_accesskeys', $k, $tstr, $attrs['accesskey']);
				}
				$accesskeys[] = $attrs['accesskey'];
			}
		}

		$duplicated_ids_flag = $is_exists_id ? 3 : 4;
		$duplicated_accesskeys_flag = $is_exists_ack ? 3 : 4;
		Validate\Set::log($url, 'duplicated_ids', self::$unspec, $duplicated_ids_flag);
		Validate\Set::log($url, 'duplicated_accesskeys', self::$unspec, $duplicated_accesskeys_flag);

		static::addErrorToHtml($url, 'duplicated_ids', static::$error_ids[$url], 'ignores');
		static::addErrorToHtml($url, 'duplicated_accesskeys', static::$error_ids[$url], 'ignores');
	}
}
