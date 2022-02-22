<?php
/**
 * * A11yc\Validate\Check\TellUserFileType
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

class TellUserFileType extends Validate
{
	/**
	 * tell user file type
	 *
	 * @param String $url
	 * @return Void
	 */
	public static function check($url)
	{
		Validate\Set::log($url, 'tell_user_file_type', self::$unspec, 1);
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
					$inner = Element\Get\Each::textFromElement($m);
					$f_inner = self::addCheckStrings($inner, $vv);

					list($len, $is_exists) = self::existCheck($href);
					$tstr = $ms[0][$k];

					// better text
					$is_better_text =
						(is_null($is_exists) || $is_exists === true) && // null means lower php version
						(
							strpos(strtolower($f_inner), $vv) === false || // lacknesss of file type
							preg_match("/\d/", $f_inner) === false // lacknesss of filesize?
						);

					Validate\Set::errorAndLog(
						$is_better_text,
						$url,
						'tell_user_file_type',
						$k,
						$tstr,
						$href.': '.$inner.$len
					);

					// broken link
					if (is_null($is_exists)) continue;
					if ($is_exists === false && static::$do_link_check)
					{
						Validate\Set::error($url, 'link_check', $k, $tstr, $href);
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
	 * @param String|Bool $inner
	 * @param String $vv
	 * @return String
	 */
	private static function addCheckStrings($inner, $vv)
	{
		if (is_bool($inner)) return '';

		$exts = array(
			array(
				'ext' => array('doc', 'docx'),
				'str' => 'word'
			),
			array(
				'ext' => array('xls', 'xlsx'),
				'str' => 'excel'
			),
			array(
				'ext' => array('ppt', 'pptx'),
				'str' => 'power',
			),
		);
		foreach ($exts as $ext)
		{
			if (in_array($vv, $ext['ext']) && strpos($inner, $ext['str']) !== false)
			{
				$inner.= join(',', $ext['ext']);
			}
		}

		return $inner;
	}

	/**
	 * exist Check
	 *
	 * @param String $href
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
