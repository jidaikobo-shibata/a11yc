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
	 * tell user file type
	 *
	 * @return  void
	 */
	public static function tell_user_file_type()
	{
		$str = static::ignore_elements(static::$hl_html);
		$ms = static::get_elements_by_re($str, 'anchors_and_values');
		if ( ! $ms[1]) return;

		$suspicious = array(
			'pdf',
			'doc',
			'docx',
			'xls',
			'xlsx',
			'ppt',
			'pptx',
			'zip',
			'tar',
		);

		foreach ($ms[1] as $k => $m)
		{
			foreach ($suspicious as $vv)
			{
				$m = str_replace("'", '"', $m);
				if (strpos($m, '.'.$vv.'"') !== false)
				{
					$attrs = static::get_attributes($m);

					if ( ! isset($attrs['href'])) continue;
					$href = strtolower($attrs['href']);
					$inner = substr($ms[0][$k], strpos($ms[0][$k], '>') + 1);
					$inner = str_replace('</a>', '', $inner);
					$f_inner = $inner;

					// allow application name
					if (
						(($vv == 'doc' || $vv == 'docx') && strpos($href, 'word')  !== false) ||
						(($vv == 'xls' || $vv == 'xlsx') && strpos($href, 'excel') !== false) ||
						(($vv == 'ppt' || $vv == 'pptx') && strpos($href, 'power') !== false)
					)
					{
						$f_inner.= 'doc,docx,xls,xlsx,ppt,pptx';
					}

					if (
						strpos(strtolower($f_inner), $vv) === false || // lacknesss of file type
						preg_match("/\d/", $f_inner) == false // lacknesss of filesize?
					)
					{
						static::$error_ids['tell_user_file_type'][$k]['id'] = $ms[0][$k];
						static::$error_ids['tell_user_file_type'][$k]['str'] = $href.': '.$inner;
					}
				}
			}
		}
		static::add_error_to_html('tell_user_file_type', static::$error_ids, 'ignores');
	}

	/**
	 * same_urls_should_have_same_text
			// some screen readers read anchor's title attribute.
			// and user cannot understand that title is exist or not.
	 *
	 * @return  void
	 */
	public static function same_urls_should_have_same_text()
	{
		// urls
		$str = static::ignore_elements(static::$hl_html);
		$ms = static::get_elements_by_re($str, 'anchors_and_values');
		if ( ! $ms[1]) return;

		$urls = array();
		foreach ($ms[1] as $k => $v)
		{
			if (static::is_ignorable($ms[0][$k])) continue;

			$attrs = static::get_attributes($v);

			if ( ! isset($attrs['href'])) continue;
			$url = static::correct_url($attrs['href']);

			// strip m except for alt
			$text = $ms[2][$k];
			preg_match_all("/\<\w+ +?[^\>]*?alt *?= *?[\"']([^\"']*?)[\"'][^\>]*?\>/", $text, $mms);
			if ($mms)
			{
				foreach ($mms[0] as $kk => $vv)
				{
					$text = str_replace($mms[0][$kk], $mms[1][$kk], $text);
				}
			}
			$text = strip_tags($text);
			$text = trim($text);

			// check
			if ( ! array_key_exists($url, $urls))
			{
				$urls[$url] = $text;
			}
			// ouch! same text
			else if ($urls[$url] != $text)
			{
				static::$error_ids['same_urls_should_have_same_text'][$k]['id'] = $ms[0][$k];
				static::$error_ids['same_urls_should_have_same_text'][$k]['str'] = $url.': ('.mb_strlen($urls[$url], "UTF-8").') "'.$urls[$url].'" OR ('.mb_strlen($text, "UTF-8").') "'.$text.'"';
			}
		}
	static::add_error_to_html('same_urls_should_have_same_text', static::$error_ids, 'ignores_comment_out');
	}

	/**
	 * here link
	 *
	 * @return  bool
	 */
	public static function here_link()
	{
		$str = static::ignore_elements(static::$hl_html);
		$ms = static::get_elements_by_re($str, 'anchors_and_values');
		if ( ! $ms[2]) return;

		foreach ($ms[2] as $k => $m)
		{
			$m = trim($m);
			if ($m == A11YC_LANG_HERE)
			{
				static::$error_ids['here_link'][$k]['id'] = $ms[0][$k];
				static::$error_ids['here_link'][$k]['str'] = @$ms[1][$k];
			}
		}
		static::add_error_to_html('here_link', static::$error_ids, 'ignores');
	}

	/**
	 * link_check
	 *
	 * @return  void
	 */
	public static function link_check()
	{
		$str = static::ignore_elements(static::$hl_html);
		$ms = static::get_elements_by_re($str, 'tags');
		if ( ! $ms[0]) return;

		// candidates
		$checks = array(
			'a',
			'img',
			'form',
			'meta',
		);

		// fragments
		preg_match_all("/ (?:id|name) *?= *?[\"']([^\"']+?)[\"']/i", $str, $fragments);
		$is_error = false;

		foreach ($ms[0] as $k => $tag)
		{
			$url = '';
			$ele = $ms[1][$k];

			if ( ! in_array($ele, $checks)) continue;
			$attrs = static::get_attributes($tag);

			if (isset($attrs['href']))
			{
				$url = $attrs['href'];
			}
			elseif (isset($attrs['src']))
			{
				$url = $attrs['src'];
			}
			elseif (isset($attrs['action']))
			{
				$url = $attrs['action'];
			}
			elseif (isset($attrs['property']))
			{
				if ($attrs['property'] == 'og:url' || $attrs['property'] == 'og:image')
				{
					$url = $attrs['content'];
				}
			}
			else
			{
				continue;
			}

			// correct url
			if (static::is_ignorable($tag)) continue;
			$url = static::correct_url($url);

			// fragments
			if ($url[0] == '#')
			{
				if ( ! in_array(substr($url, 1), $fragments[1]))
				{
					static::$error_ids['link_check'][$k]['id'] = $tag;
					static::$error_ids['link_check'][$k]['str'] = 'Fragment Not Found: '.$original;
					$is_error = true;
				}
				continue;
			}

			$headers = @get_headers($url);

			// links
			if ($headers === false)
			{
				static::$error_ids['link_check'][$k]['id'] = $tag;
				static::$error_ids['link_check'][$k]['str'] = 'Not Found: '.$tag;
				$is_error = true;
				continue;
			}

			// page found
			if (
				strpos($headers[0], ' 20') !== false ||
				strpos($headers[0], ' 30') !== false
			) continue;

			// 40x
			static::$error_ids['link_check'][$k]['id'] = $tag;
			static::$error_ids['link_check'][$k]['str'] = substr($headers[0], strpos($headers[0], ' ')).': '.$original;
			$is_error = true;
		}
		static::add_error_to_html('link_check', static::$error_ids, 'ignores_comment_out');

		// message
		if ( ! $is_error)
		{
			static::$error_ids['no_broken_link_found'][0]['id'] = '';
			static::$error_ids['no_broken_link_found'][0]['str'] = A11YC_LANG_NO_BROKEN_LINK_FOUND;
		}
		static::add_error_to_html('no_broken_link_found', static::$error_ids, 'ignores_comment_out');
	}
}
