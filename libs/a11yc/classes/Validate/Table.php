<?php
/**
 * A11yc\Validate\Table
 *
 * @package    part of A11yc
 * @author     Jidaikobo Inc.
 * @license    The MIT License (MIT)
 * @copyright  Jidaikobo Inc.
 * @link       http://www.jidaikobo.com
 */
namespace A11yc\Validate;

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
		static::$logs[$url]['table_use_th'][self::$unspec] = 1;
		static::$logs[$url]['table_use_scope'][self::$unspec] = 1;
		static::$logs[$url]['table_use_valid_scope'][self::$unspec] = 1;
		static::$logs[$url]['table_use_summary'][self::$unspec] = 1;
		static::$logs[$url]['table_use_caption'][self::$unspec] = 1;
		$str = Element::ignoreElements($url);

		preg_match_all('/\<table[^\>]*?\>.+?\<\/table\>/ims', $str, $ms);

		if ( ! $ms[0])
		{
			static::$logs[$url]['table_use_th'][self::$unspec] = 4;
			static::$logs[$url]['table_use_scope'][self::$unspec] = 4;
			static::$logs[$url]['table_use_valid_scope'][self::$unspec] = 4;
			static::$logs[$url]['table_use_summary'][self::$unspec] = 4;
			static::$logs[$url]['table_use_caption'][self::$unspec] = 4;
			return;
		}

		$n = 0;
		foreach ($ms[0] as $n => $m)
		{
			$attrs = Element::getAttributes($m);
			if (Arr::get($attrs, 'role') == 'presentation') continue;

			preg_match('/\<table[^\>]*?\>/i', $m, $table_tag);

			// th less
			if (strpos($m, '<th') === false)
			{
				static::$logs[$url]['table_use_th'][$m] = -1;
				static::$error_ids[$url]['table_use_th'][$n]['id'] = $table_tag[0];
				static::$error_ids[$url]['table_use_th'][$n]['str'] = $table_tag[0];
			}
			else
			{
				static::$logs[$url]['table_use_th'][$m] = 2;
			}

			// scope less
			if (strpos($m, ' scope') === false)
			{
				static::$logs[$url]['table_use_scope'][$m] = -1;
				static::$error_ids[$url]['table_use_scope'][$n]['id'] = $table_tag[0];
				static::$error_ids[$url]['table_use_scope'][$n]['str'] = $table_tag[0];
			}
			else if (preg_match_all('/scope *?= *?[\'"]([^\'"]+?)[\'"]/i', $m, $mms))
			{
				foreach ($mms[1] as $nn => $mm)
				{
					if ( ! in_array($mm, array('col', 'row', 'rowgroup', 'colgroup')))
					{
						static::$logs[$url]['table_use_valid_scope'][$m] = -1;
						static::$error_ids[$url]['table_use_valid_scope'][$n]['id'] = $table_tag[0];
						static::$error_ids[$url]['table_use_valid_scope'][$n]['str'] = $mms[0][$nn];
					}
				}
			}
			else
			{
				static::$logs[$url]['table_use_scope'][$m] = 2;
				static::$logs[$url]['table_use_valid_scope'][$m] = 2;
			}

			if (in_array(Element::getDoctype($url), array('html4', 'xhtml')))
			{
				// summary less
				if ( ! array_key_exists('summary', Element::getAttributes($table_tag[0])))
				{
					static::$logs[$url]['table_use_summary'][$m] = -1;
					static::$error_ids[$url]['table_use_summary'][$n]['id'] = $table_tag[0];
					static::$error_ids[$url]['table_use_summary'][$n]['str'] = $table_tag[0];
				}
				else
				{
					static::$logs[$url]['table_use_summary'][$m] = 2;
				}
			}
			else
			{
				static::$logs[$url]['table_use_summary'][$m] = 5;
			}

			// caption less
			if (strpos($m, '</caption>') === false)
			{
				static::$logs[$url]['table_use_caption'][$m] = -1;
				static::$error_ids[$url]['table_use_caption'][$n]['id'] = $table_tag[0];
				static::$error_ids[$url]['table_use_caption'][$n]['str'] = $table_tag[0];
			}
			else
			{
				static::$logs[$url]['table_use_caption'][$m] = 3;
			}
		}

		static::addErrorToHtml($url, 'table_use_th', static::$error_ids[$url], 'ignores');
		static::addErrorToHtml($url, 'table_use_scope', static::$error_ids[$url], 'ignores');
		static::addErrorToHtml($url, 'table_use_valid_scope', static::$error_ids[$url], 'ignores');
		static::addErrorToHtml($url, 'table_use_summary', static::$error_ids[$url], 'ignores');
		static::addErrorToHtml($url, 'table_use_caption', static::$error_ids[$url], 'ignores');
	}
}
