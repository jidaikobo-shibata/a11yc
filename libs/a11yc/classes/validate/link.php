<?php
/**
 * A11yc\Validation_Link
 *
 * @package    part of A11yc
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
	 * @return Void
	 */
	public static function tell_user_file_type()
	{
		$str = static::ignore_elements(static::$hl_html);
		$ms = static::get_elements_by_re($str, 'ignores', 'anchors_and_values');
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

					$len = '';
					$is_exists = null;
					if (version_compare(PHP_VERSION, '5.4.0') >= 0)
					{
						\A11yc\Guzzle::forge($href);
						$is_exists = \A11yc\Guzzle::instance($href)->is_exists;
						if ($is_exists)
						{
							$tmps = \A11yc\Guzzle::instance($href)->headers;
							if (isset($tmps['Content-Length'][0]))
							{
								$ext = strtoupper(substr($href, strrpos($href, '.') + 1));
								$len = ' ('.$ext.', '.Util::byte2Str(intval($tmps['Content-Length'][0])).')';
							}
						}
					}

					// better text
					if (
						// null means lower php version
						(is_null($is_exists) || $is_exists === true) &&
						(
							strpos(strtolower($f_inner), $vv) === false || // lacknesss of file type
							preg_match("/\d/", $f_inner) == false // lacknesss of filesize?
						)
					)
					{
						static::$error_ids['tell_user_file_type'][$k]['id'] = $ms[0][$k];
						static::$error_ids['tell_user_file_type'][$k]['str'] = $href.': '.$inner.$len;
					}

					// broken link
					if (is_null($is_exists)) continue;
					if ($is_exists === false)
					{
						static::$error_ids['link_check'][$k]['id'] = $ms[0][$k];
						static::$error_ids['link_check'][$k]['str'] = $href;
					}
				}
			}
		}
		static::add_error_to_html('tell_user_file_type', static::$error_ids, 'ignores');
		static::add_error_to_html('link_check', static::$error_ids, 'ignores_comment_out');
	}

	/**
	 * same_urls_should_have_same_text
	 * NOTE: some screen readers read anchor's title attribute.
	 * and user cannot understand that title is exist or not.
	 *
	 * @return Void
	 */
	public static function same_urls_should_have_same_text()
	{
		// urls
		$str = static::ignore_elements(static::$hl_html);
		$ms = static::get_elements_by_re($str, 'ignores', 'anchors_and_values');
		if ( ! $ms[1]) return;

		$urls = array();
		foreach ($ms[1] as $k => $v)
		{
			if (static::is_ignorable($ms[0][$k])) continue;

			$attrs = static::get_attributes($v);

			if ( ! isset($attrs['href'])) continue;
			$url = Crawl::keep_url_unique($attrs['href']);

			// strip m except for alt
			$text = $ms[2][$k];
			preg_match_all("/\<\w+ +?[^\>]*?alt *?= *?[\"']([^\"']*?)[\"'][^\>]*?\>/", $text, $mms);
			if ($mms)
			{
				foreach (array_keys($mms[0]) as $kk)
				{
					$text = str_replace($mms[0][$kk], $mms[1][$kk], $text);
				}
			}
			$text = strip_tags($text);
			$text = trim($text);
			$text = preg_replace("/\s{2,}/i", ' ', $text);

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
	 * @return  void
	 */
	public static function here_link()
	{
		$str = static::ignore_elements(static::$hl_html);
		$ms = static::get_elements_by_re($str, 'ignores', 'anchors_and_values');
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
	 * @return Void
	 */
	public static function link_check()
	{
		if (Validate::do_link_check() == false) return;

		$str = static::ignore_elements(static::$hl_html);
		$ms = static::get_elements_by_re($str, 'ignores', 'tags');
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

			if ( ! $url) continue;

			// fragments
			if ($url[0] == '#')
			{
				if ( ! in_array(substr($url, 1), $fragments[1]))
				{
					static::$error_ids['link_check'][$k]['id'] = $tag;
					static::$error_ids['link_check'][$k]['str'] = 'Fragment Not Found: '.$url;
				}
				continue;
			}

			// correct url
			if (static::is_ignorable($tag)) continue;

			// relative
			$target_path = '';
			if(
				substr($url, 0, 2) !== '//' &&
				(
					substr($url, 0, 1) == '/' ||
					substr($url, 0, 1) == '.'
				)
			)
			{
				$target_path = Crawl::get_target_path();
			}

			// inside of site: HTTP_HOST or relative
			if ($target_path || strpos($url, Input::server('HTTP_HOST')) !== false)
			{
				Crawl::set_target_path($target_path ?: $url);
				$url = Crawl::keep_url_unique($url);
				$url = Crawl::real_url($url);
			}

			// remove strange ampersand. seems depend on environment ?-(
			$url = str_replace('&#038;', '&', $url);

			// get_headers
			$headers = @get_headers($url);

			// try once more
			// thx http://www.mogumagu.com/wp/wordpress/archives/1601
			// thx http://qiita.com/kino0104/items/8a6a6dc2404c27bc43ea
			if (function_exists('curl_init'))
			{
				$ch = curl_init();
				curl_setopt($ch, CURLOPT_URL, $url);
				curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
				curl_setopt($ch, CURLOPT_HEADER, true);
				curl_setopt($ch, CURLOPT_SSLVERSION, 1);
				curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
				if(curl_exec($ch) === false) echo 'Curl error: ' . curl_error($ch);
				$info = curl_getinfo($ch);
				curl_close($ch);
				if ($info['http_code'])
				{
					$headers[0] = ' '.$info['http_code'];
				}

				// insecure challenge
				if ( ! $headers)
				{
					$ch2 = curl_init();
					curl_setopt($ch2, CURLOPT_URL, $url);
					curl_setopt($ch2, CURLOPT_RETURNTRANSFER, true);
					curl_setopt($ch2, CURLOPT_HEADER, true);
					curl_setopt($ch2, CURLOPT_SSLVERSION, 1);
					curl_setopt($ch2, CURLOPT_SSL_VERIFYPEER, FALSE);
					if(curl_exec($ch2) === false) echo 'Curl error2: ' . curl_error($ch2);
					$info = curl_getinfo($ch2);
					curl_close($ch2);
					if ($info['http_code'])
					{
						$headers[0] = ' '.$info['http_code'];
					}
				}
			}

			// links
			if ($headers === false)
			{
				static::$error_ids['link_check'][$k]['id'] = $tag;
				static::$error_ids['link_check'][$k]['str'] = 'Not Found: '.$tag;
				continue;
			}

			// page found
			if (
				strpos($headers[0], ' 20') !== false ||
				strpos($headers[0], ' 30') !== false
			) continue;

			// 40x
			static::$error_ids['link_check'][$k]['id'] = $tag;
			static::$error_ids['link_check'][$k]['str'] = substr($headers[0], strpos($headers[0], ' ')).': '.$url;
		}
		static::add_error_to_html('link_check', static::$error_ids, 'ignores_comment_out');
	}
}
