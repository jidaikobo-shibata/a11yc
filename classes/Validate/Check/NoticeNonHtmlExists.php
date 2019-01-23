<?php
/**
 * * A11yc\Validate\Check\NoticeNonHtmlExists
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

class NoticeNonHtmlExists extends Validate
{
	/**
	 * non html exists
	 *
	 * @param String $url
	 * @return Void
	 */
	public static function check($url)
	{
		Validate\Set::log($url, 'notice_non_html_exists', self::$unspec, 1);
		$str = Element\Get::ignoredHtml($url);

		$ms = Element\Get::elementsByRe($str, 'ignores', 'anchors_and_values');
		if ( ! $ms[1]) return;

		$suspicious = array(
			'pdf',
			'doc',
			'docx',
			'xls',
			'xlsx',
			'ppt',
			'pptx',
		);
		$exists = array();

		foreach ($ms[1] as $m)
		{
			foreach ($suspicious as $vv)
			{
				$m = str_replace("'", '"', $m);
				if (strpos($m, '.'.$vv.'"') !== false)
				{
					if ( ! isset($exists[$vv])) $exists[$vv] = 0;
					$exists[$vv] = $exists[$vv] + 1;
				}
			}
		}

		if ( ! empty($exists))
		{
			$err_strs = array();
			foreach ($exists as $ext => $times)
			{
				$err_strs[] = $ext.' ('.sprintf(A11YC_LANG_COUNT_ITEMS, $times).')';
			}
			Validate\Set::error($url, 'notice_non_html_exists', 0, '', join(', ', $err_strs));
		}
		static::addErrorToHtml($url, 'notice_non_html_exists', static::$error_ids[$url], 'ignores');
	}
}
