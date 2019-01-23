<?php
/**
 * * A11yc\Validate\Check\TitlelessFrame
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

class TitlelessFrame extends Validate
{
	/**
	 * titleless frame
	 *
	 * @param String $url
	 * @return Void
	 */
	public static function check($url)
	{
		Validate\Set::log($url, 'titleless_frame', self::$unspec, 1);

		$str = Element\Get::ignoredHtml($url);
		$ms = Element\Get::elementsByRe($str, 'ignores', 'tags');
		if ( ! $ms[0]) return;

		foreach ($ms[0] as $k => $v)
		{
			if ($ms[1][$k] != 'frame' && $ms[1][$k] != 'iframe') continue;
			$attrs = Element\Get::attributes($v);
			$tstr = $ms[0][$k];


			Validate\Set::errorAndLog(
				 ! trim(Arr::get($attrs, 'title')),
				$url,
				'titleless_frame',
				$k,
				'',
				$tstr
			);
		}
		static::addErrorToHtml($url, 'titleless_frame', static::$error_ids[$url], 'ignores');
	}
}
