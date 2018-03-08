<?php
/**
 * A11yc\Validate\TellUserFileType
 *
 * @package    part of A11yc
 * @author     Jidaikobo Inc.
 * @license    The MIT License (MIT)
 * @copyright  Jidaikobo Inc.
 * @link       http://www.jidaikobo.com
 */
namespace A11yc\Validate;

class TellUserFileType extends Validate
{
	/**
	 * tell user file type
	 *
	 * @param  String $url
	 * @return Void
	 */
	public static function check($url)
	{
		$str = static::ignoreElements(static::$hl_htmls[$url]);
		$ms = static::getElementsByRe($str, 'ignores', 'anchors_and_values');
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
					$attrs = static::getAttributes($m);

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
					if (\A11yc\Guzzle::envCheck())
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
						static::$error_ids[$url]['tell_user_file_type'][$k]['id'] = $ms[0][$k];
						static::$error_ids[$url]['tell_user_file_type'][$k]['str'] = $href.': '.$inner.$len;
					}

					// broken link
					if (is_null($is_exists)) continue;
					if ($is_exists === false)
					{
						static::$error_ids[$url]['link_check'][$k]['id'] = $ms[0][$k];
						static::$error_ids[$url]['link_check'][$k]['str'] = $href;
					}
				}
			}
		}
		static::addErrorToHtml($url, 'tell_user_file_type', static::$error_ids[$url], 'ignores');
		static::addErrorToHtml($url, 'link_check', static::$error_ids[$url], 'ignores_comment_out');
	}
}
