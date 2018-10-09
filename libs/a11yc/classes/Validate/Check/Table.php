<?php
/**
 * * A11yc\Validate\Check\Table
 *
 * @package    part of A11yc
 * @author     Jidaikobo Inc.
 * @license    The MIT License (MIT)
 * @copyright  Jidaikobo Inc.
 * @link       http://www.jidaikobo.com
 */
namespace A11yc\Validate\Check;

use A11yc\Element;

class Table extends Validate
{
	/**
	 * table
	 *
	 * @param  String $url
	 * @return Void
	 */
	public static function check($url)
	{
		$error_names = array(
			'table_use_th',
			'table_use_scope',
			'table_use_valid_scope',
			'table_use_summary',
			'table_use_caption',
		);

		static::setLog($url, $error_names, self::$unspec, 1);
		$str = Element\Get::ignoredHtml($url);

		preg_match_all('/\<table[^\>]*?\>.+?\<\/table\>/ims', $str, $ms);

		if ( ! $ms[0])
		{
			static::setLog($url, $error_names, self::$unspec, 4);
			return;
		}

		$n = 0;
		foreach ($ms[0] as $n => $m)
		{
			$attrs = Element\Get::attributes($m);
			if (Arr::get($attrs, 'role') == 'presentation') continue;

			preg_match('/\<table[^\>]*?\>/i', $m, $table_tag);

			// th less
			$tstr = $table_tag[0];
			if (strpos($m, '<th') === false)
			{
				static::setError($url, 'table_use_th', $n, $tstr, $tstr);
			}
			else
			{
				static::setLog($url, 'table_use_th', self::$unspec, 2);
			}

			// scope less
			if (strpos($m, ' scope') === false)
			{
				static::setError($url, 'table_use_scope', $n, $tstr, $tstr);
			}
			else if (preg_match_all('/scope *?= *?[\'"]([^\'"]+?)[\'"]/i', $m, $mms))
			{
				foreach ($mms[1] as $nn => $mm)
				{
					if ( ! in_array($mm, array('col', 'row', 'rowgroup', 'colgroup')))
					{
						static::setError($url, 'table_use_valid_scope', $n, $tstr, $mms[0][$nn]);
					}
				}
			}
			else
			{
				static::setLog($url, 'table_use_scope', $m, 2);
				static::setLog($url, 'table_use_valid_scope', $m, 2);
			}

			if (in_array(Element\Get::doctype($url), array('html4', 'xhtml')))
			{
				// summary less
				if ( ! array_key_exists('summary', Element\Get::attributes($table_tag[0])))
				{
					static::setError($url, 'table_use_summary', $n, $tstr, $tstr);
				}
				else
				{
					static::setLog($url, 'table_use_summary', $m, 2);
				}
			}
			else
			{
				static::setLog($url, 'table_use_summary', $m, 5);
			}

			// caption less
			if (strpos($m, '</caption>') === false)
			{
				static::setError($url, 'table_use_caption', $n, $tstr, $tstr);
			}
			else
			{
				static::setLog($url, 'table_use_caption', $m, 3);
			}
		}

		static::addErrorToHtml($url, 'table_use_th', static::$error_ids[$url], 'ignores');
		static::addErrorToHtml($url, 'table_use_scope', static::$error_ids[$url], 'ignores');
		static::addErrorToHtml($url, 'table_use_valid_scope', static::$error_ids[$url], 'ignores');
		static::addErrorToHtml($url, 'table_use_summary', static::$error_ids[$url], 'ignores');
		static::addErrorToHtml($url, 'table_use_caption', static::$error_ids[$url], 'ignores');
	}
}
