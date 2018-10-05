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

use A11yc\Element;

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
		static::$logs[$url]['tell_user_file_type'][self::$unspec] = 1;
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
			'zip',
			'tar',
		);

		foreach ($ms[0] as $k => $m)
		{
			foreach ($suspicious as $vv)
			{
				$tmp = str_replace("'", '"', $m);
				if (strpos($tmp, '.'.$vv.'"') !== false)
				{
					$attrs = Element\Get::attributes($m);

					if ( ! isset($attrs['href'])) continue;
					$href = strtolower($attrs['href']);
					$inner = Element\Get::textFromElement($m);
					$f_inner = self::addCheckStrings($inner, $vv, $href);

					list($len, $is_exists) = self::existCheck($href);
					$tstr = $ms[0][$k];

					// better text
					if (
						// null means lower php version
						(is_null($is_exists) || $is_exists === true) &&
						(
							strpos(strtolower($f_inner), $vv) === false || // lacknesss of file type
							preg_match("/\d/", $f_inner) === false // lacknesss of filesize?
						)
					)
					{
						static::$logs[$url]['tell_user_file_type'][$tstr] = -1;
						static::$error_ids[$url]['tell_user_file_type'][$k]['id'] = $tstr;
						static::$error_ids[$url]['tell_user_file_type'][$k]['str'] = $href.': '.$inner.$len;
					}
					else
					{
						static::$logs[$url]['tell_user_file_type'][$tstr] = 2;
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

	/**
	 * add check strings
	 *
	 * @param  String $f_inner
	 * @param  String $vv
	 * @param  String $href
	 * @return String
	 */
	private static function addCheckStrings($f_inner, $vv, $href)
	{
		// allow application name - word
		if (($vv == 'doc' || $vv == 'docx') && strpos($href, 'word')  !== false)
		{
			$f_inner.= 'doc,docx';
		}

		// allow application name - excel
		if (($vv == 'xls' || $vv == 'xlsx') && strpos($href, 'excel') !== false)
		{
			$f_inner.= 'xls,xlsx';
		}

		// allow application name - ppt
		if (($vv == 'ppt' || $vv == 'pptx') && strpos($href, 'power') !== false)
		{
			$f_inner.= 'ppt,pptx';
		}
		return $f_inner;
	}

	/**
	 * exist Check
	 *
	 * @param  String $href
	 * @return Array
	 */
	private static function existCheck($href)
	{
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
		return array($len, $is_exists);
	}
}
