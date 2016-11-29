<?php
/**
 * A11yc\Validation_Link
 *
 * @package    part of A11yc
 * @version    1.0
 * @author     Jidaikobo Inc.
 * @license    The MIT License (MIT)
 * @copyright  Jidaikobo Inc.
 * @link       http://www.jidaikobo.com
 */
namespace A11yc;
class Validate_Link extends Validate
{
	/**
	 * link_check
	 *
	 * @return  void
	 */
	public static function link_check()
	{
		$str = static::ignore_comment_out(static::$hl_html);

		// ordinary urls
		preg_match_all("/ (?:href|src|cite|data|poster|action) *?= *?[\"']([^\"']+?)[\"']/i", $str, $ms);

		// og
		$mms = static::get_elements_by_re($str, 'tags');
		if (isset($mms[0]))
		{
			foreach ($mms[0] as $m)
			{
				if (strpos($m, '<meta') === false) continue;
				$attrs = static::get_attributes($m);
				if ( ! isset($attrs['property'])) continue;

				if ($attrs['property'] == 'og:url' && isset($attrs['content']))
				{
					$ms[1][] = $attrs['content'];
				}
				if ($attrs['property'] == 'og:image' && isset($attrs['content']))
				{
					$ms[1][] = $attrs['content'];
				}
			}
		}

		$urls = array();
		foreach ($ms[1] as $k => $v)
		{
			if (static::is_ignorable($ms[0][$k])) continue;
			$urls[$v] = static::correct_url($v);
		}

		// fragments
		preg_match_all("/ (?:id|name) *?= *?[\"']([^\"']+?)[\"']/i", $str, $fragments);

		// check
		$k = 0;
		foreach ($urls as $original => $url)
		{
			if ($url[0] == '#')
			{
				if ( ! in_array(substr($url, 1), $fragments[1]))
				{
					static::$error_ids['link_check'][$k]['id'] = $original;
					static::$error_ids['link_check'][$k]['str'] = 'Fragment Not Found: '.$original;
				}
				continue;
			}

			$headers = @get_headers($url);
			if ($headers !== false)
			{
				// OK TODO: think about redirection
//				if (strpos($headers[0], ' 20') !== false || strpos($headers[0], ' 30') !== false) continue;
				if (strpos($headers[0], ' 20') !== false) continue;

				// not OK
				static::$error_ids['link_check'][$k]['id'] = $original;
				static::$error_ids['link_check'][$k]['str'] = substr($headers[0], strpos($headers[0], ' ')).': '.$original;
			}
			else
			{
				static::$error_ids['link_check'][$k]['id'] = 'Not Found: '.$original;
				static::$error_ids['link_check'][$k]['str'] = 'Not Found: '.$original;
			}
			$k++;
		}
		static::add_error_to_html('link_check', static::$error_ids, 'ignores_comment_out');
	}
}
