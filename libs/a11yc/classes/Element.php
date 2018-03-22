<?php
/**
 * A11yc\Element
 *
 * @package    part of A11yc
 * @author     Jidaikobo Inc.
 * @license    The MIT License (MIT)
 * @copyright  Jidaikobo Inc.
 * @link       http://www.jidaikobo.com
 */
namespace A11yc;

class Element
{
	protected static $first_tag = '';
	protected static $ignored_str = '';
	protected static $res = array();

	public static $ignores = array(
		"/\<script.+?\<\/script\>/si",
		"/\<style.+?\<\/style\>/si",
		"/\<rdf:RDF.+?\<\/rdf:RDF\>/si",
	);

	public static $ignores_comment_out = array(
		"/\<!--.+?--\>/si",
	);

	/**
	 * is_ignorable
	 *
	 * @param  String $str
	 * @return Bool
	 */
	public static function isIgnorable($str)
	{
		$attrs = self::getAttributes($str);

		// Strictly this is not so correct. but it seems be considered.
		if (
			(isset($attrs['tabindex']) && $attrs['tabindex'] = -1) ||
			(isset($attrs['aria-hidden']) && $attrs['tabindex'] = 'true')
		)
		{
			return true;
		}

		// occasionally JavaScript provides function by id or class.
		if (isset($attrs['href']) && strpos($attrs['href'], 'javascript') === 0)
		{
			return true;
		}

		// occasionally JavaScript use #
		if (isset($attrs['href']) && $attrs['href'] == '#')
		{
			return true;
		}

		// mail to
		if (isset($attrs['href']) && substr($attrs['href'], 0, 7) == 'mailto:')
		{
			return true;
		}

		return false;
	}

	/**
	 * ignoreElements
	 *
	 * @param  String $str
	 * @param  Bool $force
	 * @return String
	 */
	public static function ignoreElements($str, $force = false)
	{
		if (static::$ignored_str && ! $force) return static::$ignored_str;

		// ignore comment out, script, style
		$ignores = array_merge(static::$ignores, static::$ignores_comment_out);

		foreach ($ignores as $ignore)
		{
			$str = preg_replace($ignore, '', $str);
		}

		// set first tag
		$first_tags = Element::getElementsByRe($str, 'ignores', 'tags');
		static::$first_tag = Arr::get($first_tags, '0.0');

		static::$ignored_str = $str;
		return $str;
	}

	/**
	 * ignoreCommentOut
	 *
	 * @param  String $str
	 * @return String
	 */
	public static function ignoreCommentOut($str)
	{
		static $retval = '';
		if ($retval) return $retval;

		// ignore comment out only
		foreach (static::$ignores_comment_out as $ignore)
		{
			$str = preg_replace($ignore, '', $str);
		}
		$retval = $str;
		return $retval;
	}

	/**
	 * getAttributes
	 *
	 * @param  String $str
	 * @return Array
	 */
	public static function getAttributes($str)
	{
		static $retvals = array();
		if (isset($retvals[$str])) return $retvals[$str];
		$keep = $str;

		static $ruled_attrs = array(
			'accept', 'accept-charset', 'accesskey', 'action', 'align', 'alt',
			'async', 'autocomplete', 'autofocus', 'autoplay', 'bgcolor', 'border',
			'buffered', 'challenge', 'charset', 'checked', 'cite', 'class', 'code',
			'codebase', 'color', 'cols', 'colspan', 'content', 'contenteditable',
			'contextmenu', 'controls', 'coords', 'data', 'datetime', 'default',
			'defer', 'dir', 'dirname', 'disabled', 'draggable', 'dropzone', 'enctype',
			'for', 'form', 'headers', 'height', 'hidden', 'high', 'href', 'hreflang',
			'http-equiv', 'icon', 'id', 'ismap', 'itemprop', 'keytype', 'kind',
			'label', 'lang', 'language', 'list', 'loop', 'low', 'manifest', 'max',
			'maxlength', 'media', 'method', 'min', 'multiple', 'name', 'novalidate',
			'open', 'optimum', 'pattern', 'ping', 'placeholder', 'poster', 'preload',
			'pubdate', 'radiogroup', 'readonly', 'rel', 'required', 'reversed',
			'rows', 'rowspan', 'sandbox', 'spellcheck', 'scope', 'scoped', 'seamless',
			'selected', 'shape', 'size', 'sizes', 'span', 'src', 'srcdoc', 'srclang',
			'start', 'step', 'style', 'summary', 'tabindex', 'target', 'title',
			'type', 'usemap', 'value', 'width', 'wrap',

			// ?
			'cellspacing', 'cellpadding',

			// header
			'xmlns', 'rev', 'profile', 'property', 'role', 'prefix', 'itemscope', 'xml:lang',

			// JavaScript
			'onclick', 'ondblclick', 'onkeydown', 'onkeypress', 'onkeyup', 'onmousedown',
			'onmouseup', 'onmouseover', 'onmouseout', 'onmousemove', 'onload', 'onunload',
			'onfocus', 'onblur', 'onsubmit', 'onreset', 'onchange', 'onresize', 'onmove',
			'ondragdrop', 'onabort', 'onerror', 'onselect',
		);

		// first tag only
		$str = trim($str);
		if (strpos($str, '<') !== false)
		{
			preg_match('/\<[^\>]+?\>/is', $str, $ms);
			if ( ! isset($ms[0])) return $retvals;
			$str = $ms[0];
		}
		$str = ' '.$str;

		// blankless
		$str = str_replace('/>', ' />', $str);

		// variables
		$double = '"';
		$single = "'";
		$quoted_double = '[---a11yc_quoted_double---]';
		$quoted_single = '[---a11yc_quoted_open---]';
		$open_double   = '[---a11yc_open_double---]';
		$close_double  = '[---a11yc_close_double---]';
		$open_single   = '[---a11yc_open_single---]';
		$close_single  = '[---a11yc_close_single---]';
		$inner_double  = '[---a11yc_inner_double---]';
		$inner_single  = '[---a11yc_inner_single---]';
		$inner_space   = '[---a11yc_inner_space---]';
		$inner_equal   = '[---a11yc_inner_equal---]';
		$inner_newline = '[---a11yc_inner_newline---]';

		// escaped quote
		$str = str_replace(
			array("\\'", '\\"'),
			array($quoted_single, $quoted_double),
			$str);

		// start with which?
		$d_offset = mb_strpos($str, '"', 0, 'UTF-8');
		$s_offset = mb_strpos($str, "'", 0, 'UTF-8');

		$ex_order = array();
		if ($d_offset && $s_offset)
		{
			$ex_order = $d_offset < $s_offset ? array('"', "'") : array("'", '"');
		}
		else if($d_offset)
		{
			$ex_order = array('"');
		}
		else if($s_offset)
		{
			$ex_order = array("'");
		}

		$suspicious_end_quote = false;

		$loop = true;
		while($loop)
		{
			// start with which?
			$d_offset = mb_strpos($str, '"', 0, 'UTF-8');
			$s_offset = mb_strpos($str, "'", 0, 'UTF-8');

			$target = '';
			if ($d_offset && $s_offset)
			{
				$target = $d_offset < $s_offset ? $double : $single;
			}
			else if($d_offset)
			{
				$target = $double;
			}
			else if($s_offset)
			{
				$target = $single;
			}
			else
			{
				$loop = false;
				break;
			}
			$opp = $target == $double ? $single : $double;

			// quote
			$open = $target == $double ? $open_double : $open_single;
			$close = $target == $double ? $close_double : $close_single;
			$inner = $target == $double ? $inner_single : $inner_double;

			// search open quote
			if ($open_pos = mb_strpos($str, $target, 0, 'UTF-8'))
			{
				// search close quote
				$close_pos = mb_strpos($str, $target, $open_pos + 1, 'UTF-8');

				// close quote was not found. this tag is not beautiful.
				if ( ! $close_pos)
				{
					$str.= $close;
					$suspicious_end_quote = TRUE;
				}

				// replaces
				$search = mb_substr($str, $open_pos, $close_pos - $open_pos + 1, 'UTF-8');
				$replace = str_replace(
					array($target, $opp, ' ', '=', "\n", "\r"),
					array('', $inner, $inner_space, $inner_equal, $inner_newline, $inner_newline),
					$search);
				$replace = $open.$replace.$close;
				// replace value
				$str = str_replace($search, $replace, $str);
			}
		}

		$str = preg_replace("/ {2,}/", " ", $str); // remove plural spaces
		$str = preg_replace("/ *?= */", "=", $str); // remove plural spaces
		$str = str_replace(array("\n", "\r"), " ", $str); // newline to blank
		$attrs = array();

		foreach (explode(' ', $str) as $k => $v)
		{
			$v = trim($v, '>');
			if (empty($v)) continue;
			if ($v =='/') continue;
			if ($v[0] == '<') continue;
			if (strpos($v, '=') !== false)
			{
				list($key, $val) = explode("=", $v);
				$key = trim(strtolower($key));
			}
			else
			{
				// boolean attribute
				$key = $v;
				$val = $v;
			}

			$val = str_replace(
				array(
					$quoted_double,
					$quoted_single,
					$open_double,
					$close_double,
					$open_single,
					$close_single,
					$inner_double,
					$inner_single,
					$inner_space,
					$inner_equal,
					$inner_newline
				),
				array(
					'\\"',
					"\\'",
					'',
					'',
					"",
					"",
					'"',
					"'",
					" ",
					"=",
					"\n"
				),
				$val
			);

			// valid attributes
			if (
				in_array($key, $ruled_attrs) ||
				substr($key, 0, 5) == 'aria-' ||
				substr($key, 0, 5) == 'data-' ||
				substr($key, 0, 4) == 'xml:'
			)
			{
				// plural
				if (array_key_exists($key, $attrs))
				{
					$key = $key.'_'.$k;
					$attrs['plural'] = TRUE;
				}
				$attrs[$key] = $val;
			}
			// exclude JavaScript TODO
			else if( ! substr($k, 0, 5) == 'this.')
			{
				$attrs['suspicious'][$k] = trim($key, "'");
			}
		}
		$attrs['suspicious_end_quote'] = $suspicious_end_quote;
		$retvals[$keep] = $attrs;

		return $retvals[$keep];
	}

	/**
	 * get elements by regular expression
	 *
	 * @param  String $str
	 * @param  String $ignore_type
	 * @param  String $type (anchors|anchors_and_values|imgs|tags)
	 * @param  Bool $force
	 * @return Array
	 */
	public static function getElementsByRe($str, $ignore_type, $type = 'tags', $force = false)
	{
		if (isset(static::$res[$ignore_type][$type]) && $force == false)
		{
			return static::$res[$ignore_type][$type];
		}

		$ret = '';
		switch ($type)
		{
			case 'anchors':
				if (preg_match_all("/\<(?:a|area) ([^\>]+?)\>/i", $str, $ms))
				{
					$ret = $ms;
				}
				break;
			case 'anchors_and_values':
				if (preg_match_all("/\<a ([^\>]+)\>(.*?)\<\/a\>|\<area ([^\>]+?)\/\>/si", $str, $ms))
				{
					$ret = $ms;
				}
				break;
			case 'imgs':
				if (preg_match_all("/\<img ([^\>]+?)\>/i", $str, $ms))
				{
					$ret = $ms;
				}
				break;
			default:
				if (preg_match_all("/\<([a-zA-Z1-6]+?) +?([^\>]*?)[\/]*?\>|\<([a-zA-Z1-6]+?)[ \/]*?\>/i", $str, $ms))
				{
					foreach ($ms[1] as $k => $v)
					{
						if(empty($v)) unset($ms[1][$k]);
					}
					$tags = $ms[1] + $ms[3];
					ksort($tags);
					$ret = array(
						$ms[0],
						$tags,
						$ms[2],
					);
				}
				break;
		}

		// no influence
		if ($ret && ! $force)
		{
			static::$res[$ignore_type][$type] = $ret;
		}
		elseif ($ret)
		{
			return $ret;
		}

		return isset(static::$res[$ignore_type][$type]) ? static::$res[$ignore_type][$type] : false;
	}

	/**
	 * get doctype
	 *
	 * @param  String $url
	 * @return Mixed|String|Bool|Null
	 */
	public static function getDoctype($url)
	{
		if (empty(static::$hl_htmls[$url])) return false;

		preg_match("/\<!DOCTYPE [^\>]+?\>/", static::$hl_htmls[$url], $ms);

		// html5
		if ( ! isset($ms[0]))
		{
			preg_match("/\<!DOCTYPE html\>/i", static::$hl_htmls[$url], $ms);
		}

		if ( ! isset($ms[0])) return false; // doctypeless

		// doctype
		$doctype = null;

		// html5
		if(strtolower($ms[0]) == '<!doctype html>')
		{
			$doctype = 'html5';
		}
		// HTML4
		else if (strpos($ms[0], 'DTD HTML 4.01') !== false)
		{
			$doctype = 'html4';
		}
		// xhtml1x
		else if(strpos($ms[0], 'DTD XHTML 1') !== false)
		{
			$doctype = 'xhtml1';
		}

		return $doctype;
	}

}
