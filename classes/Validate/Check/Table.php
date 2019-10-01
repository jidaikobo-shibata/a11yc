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
use A11yc\Validate;

class Table extends Validate
{
	/**
	 * table
	 *
	 * @param String $url
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

		Validate\Set::log($url, $error_names, self::$unspec, 1);
		$str = Element\Get::ignoredHtml($url);

		preg_match_all('/\<table[^\>]*?\>.+?\<\/table\>/ims', $str, $ms);

		if ( ! $ms[0])
		{
			Validate\Set::log($url, $error_names, self::$unspec, 4);
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
			Validate\Set::errorAndLog(
				strpos($m, '<th') === false,
				$url,
				'table_use_th',
				$n,
				$tstr,
				$tstr
			);

			// scopeless
			self::scopeless($n, $m, $url, $tstr);

			// summaryless
			self::summaryless($n, $m, $url, $tstr, $table_tag[0]);

			// caption less
			Validate\Set::errorAndLog(
				strpos($m, '</caption>') === false,
				$url,
				'table_use_caption',
				$n,
				$tstr,
				$tstr
			);
		}

		static::addErrorToHtml($url, 'table_use_th', static::$error_ids[$url], 'ignores');
		static::addErrorToHtml($url, 'table_use_scope', static::$error_ids[$url], 'ignores');
		static::addErrorToHtml($url, 'table_use_valid_scope', static::$error_ids[$url], 'ignores');
		static::addErrorToHtml($url, 'table_use_summary', static::$error_ids[$url], 'ignores');
		static::addErrorToHtml($url, 'table_use_caption', static::$error_ids[$url], 'ignores');
	}

	/**
	 * scopeless
	 *
	 * @param Integer $n
	 * @param String $m
	 * @param String $url
	 * @param String $tstr
	 * @return Void
	 */
	private static function scopeless($n, $m, $url, $tstr)
	{
		// return because simple table
		if (substr_count($m, '<th') == substr_count($m, '<td')) return;

		// check
		if (strpos($m, ' scope') === false)
		{
			Validate\Set::error($url, 'table_use_scope', $n, $tstr, $tstr);
		}
		else if (preg_match_all('/scope *?= *?[\'"]([^\'"]+?)[\'"]/i', $m, $mms))
		{
			foreach ($mms[1] as $nn => $mm)
			{
				if ( ! in_array($mm, array('col', 'row', 'rowgroup', 'colgroup')))
				{
					Validate\Set::error($url, 'table_use_valid_scope', $n, $tstr, $mms[0][$nn]);
				}
			}
		}
		else
		{
			Validate\Set::log($url, 'table_use_scope', $m, 2);
			Validate\Set::log($url, 'table_use_valid_scope', $m, 2);
		}
	}

	/**
	 * summaryless
	 *
	 * @param Integer $n
	 * @param String $m
	 * @param String $url
	 * @param String $tstr
	 * @param String $table_tag
	 * @return Void
	 */
	private static function summaryless($n, $m, $url, $tstr, $table_tag)
	{
		if (in_array(Element\Get\Each::doctype($url), array('html4', 'xhtml')))
		{
			// summary less
			Validate\Set::errorAndLog(
				! array_key_exists('summary', Element\Get::attributes($table_tag)),
				$url,
				'table_use_summary',
				$n,
				$tstr,
				$tstr
			);
		}
		else
		{
			Validate\Set::log($url, 'table_use_summary', $m, 5);
		}
	}
}
