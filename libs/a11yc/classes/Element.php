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
	protected static $ignored_str = '';
	protected static $res = array();
	protected static $attrs = array();
	protected static $langs = array();

	// escape attributes
	protected static $attrs_oris = array();
	protected static $attrs_reps = array();
	protected static $attrs_reped = array();

	public static $ignores = array(
		"/\<script.+?\<\/script\>/si",
		"/\<style.+?\<\/style\>/si",
		"/\<rdf:RDF.+?\<\/rdf:RDF\>/si",
	);

	public static $ignores_comment_out = array(
		"/\<!--.+?--\>/si",
	);

	protected static $ruled_attrs = array(
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

		// variables
		protected static $double = '"';
		protected static $single = "'";
		protected static $quoted_double = '[---a11yc_quoted_double---]';
		protected static $quoted_single = '[---a11yc_quoted_open---]';
		protected static $open_double   = '[---a11yc_open_double---]';
		protected static $close_double  = '[---a11yc_close_double---]';
		protected static $open_single   = '[---a11yc_open_single---]';
		protected static $close_single  = '[---a11yc_close_single---]';
		protected static $inner_double  = '[---a11yc_inner_double---]';
		protected static $inner_single  = '[---a11yc_inner_single---]';
		protected static $inner_space   = '[---a11yc_inner_space---]';
		protected static $inner_equal   = '[---a11yc_inner_equal---]';
		protected static $inner_newline = '[---a11yc_inner_newline---]';

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
	 * escapeAttrs
	 *
	 * @param  String $str
	 * @return String
	 */
	public static function escapeAttrs($str)
	{
		$key = sha1($str);
		if (isset(self::$attrs_reped[$key])) return self::$attrs_reped[$key];

		preg_match_all('/([\'"][^\'"]*?[\'"])/', $str, $ms);
		if ( ! isset($ms[0])) return $str;

		if ( ! isset(self::$attrs_oris[$str]))
		{
			$reps = array();
			$oris = array();
			foreach ($ms[0] as $m)
			{
				$ori = $m;
				$rep = sha1($m);
				$oris[] = $m;
				$reps[] = $rep;
			}

			self::$attrs_oris[$key] = $oris;
			self::$attrs_reps[$key] = $reps;
		}

		self::$attrs_reped[$key] = str_replace(
			self::$attrs_oris[$key],
			self::$attrs_reps[$key],
			$str
		);

		return self::$attrs_reped[$key];
	}

	/**
	 * get first tag
	 *
	 * @param  String $str
	 * @return String
	 */
	public static function getFirstTag($str)
	{
		$str = trim($str);

		if (strpos($str, '<') !== false)
		{
			$key = sha1($str);
			$str_mod = self::escapeAttrs($str);

			preg_match('/\<[^\>]+?\>/is', $str_mod, $mms);
			if ( ! isset($mms[0])) return '';

			// recover attribute
			$str = str_replace(
				self::$attrs_reps[$key],
				self::$attrs_oris[$key],
				$mms[0]
			);
		}
		$str = ' '.$str; //?

		// blankless
		$str = str_replace('/>', ' />', $str);

		return $str;
	}

	/**
	 * prepare strings
	 *
	 * @param  String $str
	 * @return Array
	 */
	public static function prepareStrings($str)
	{
		// escaped quote
		$str = str_replace(
			array("\\'", '\\"'),
			array(self::$quoted_single, self::$quoted_double),
			$str
		);

		$suspicious_end_quote = false;

		$loop = true;
		while($loop)
		{
			// start with which quotation?
			$d_offset = mb_strpos($str, '"', 0, 'UTF-8');
			$s_offset = mb_strpos($str, "'", 0, 'UTF-8');

			$target = '';
			if ($d_offset && $s_offset)
			{
				$target = $d_offset < $s_offset ? self::$double : self::$single;
			}
			else if($d_offset)
			{
				$target = self::$double;
			}
			else if($s_offset)
			{
				$target = self::$single;
			}
			else
			{
				$loop = false;
				break;
			}
			$opp = $target == self::$double ? self::$single : self::$double;

			// quote
			$open = $target == self::$double ? self::$open_double : self::$open_single;
			$close = $target == self::$double ? self::$close_double : self::$close_single;
			$inner = $target == self::$double ? self::$inner_single : self::$inner_double;

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
					array('', $inner, self::$inner_space, self::$inner_equal, self::$inner_newline, self::$inner_newline),
					$search);
				$replace = $open.$replace.$close;
				// replace value
				$str = str_replace($search, $replace, $str);
			}
		}

		$str = preg_replace("/ {2,}/", " ", $str); // remove plural spaces
		$str = preg_replace("/ *?= */", "=", $str); // remove plural spaces
		$str = str_replace(array("\n", "\r"), " ", $str); // newline to blank

		return array($str, $suspicious_end_quote);
	}

	/**
	 * explode Strings
	 *
	 * @param  String $str
	 * @return Array
	 */
	public static function explodeStrings($str)
	{
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
					self::$quoted_double,
					self::$quoted_single,
					self::$open_double,
					self::$close_double,
					self::$open_single,
					self::$close_single,
					self::$inner_double,
					self::$inner_single,
					self::$inner_space,
					self::$inner_equal,
					self::$inner_newline
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
				in_array($key, self::$ruled_attrs) ||
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
		return $attrs;
	}

	/**
	 * getAttributes
	 *
	 * @param  String $str
	 * @return Array
	 */
	public static function getAttributes($str)
	{
		if (isset(static::$attrs[$str])) return static::$attrs[$str];
		$keep = $str;

		// first tag only
		$str = self::getFirstTag($str);

		// prepare strings
		list($str, $suspicious_end_quote) = self::prepareStrings($str);

		// explode strings
		$attrs = self::explodeStrings($str);

		// suspicious_end_quote
		$attrs['suspicious_end_quote'] = $suspicious_end_quote;
		static::$attrs[$keep] = $attrs;

		return $attrs;
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

		// escape all attrs
		$str_mod = self::escapeAttrs($str);

		$ret = array();
		switch ($type)
		{
			case 'anchors':
				if (preg_match_all("/\<(?:a|area) ([^\>]+?)\>/i", $str_mod, $ms))
				{
					$ret = $ms;
				}
				break;
			case 'anchors_and_values':
				if (preg_match_all("/\<a ([^\>]+)\>(.*?)\<\/a\>|\<area ([^\>]+?)\/\>/si", $str_mod, $ms))
				{
					$ret = $ms;
				}
				break;
			case 'imgs':
				if (preg_match_all("/\<img ([^\>]+?)\>/i", $str_mod, $ms))
				{
					$ret = $ms;
				}
				break;
			default:
				if (preg_match_all("/\<([a-zA-Z1-6]+?) +?([^\>]*?)[\/]*?\>|\<([a-zA-Z1-6]+?)[ \/]*?\>/i", $str_mod, $ms))
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

		// recover attribute
		$key = sha1($str);
		foreach ($ret as $k => $v)
		{
			$ret[$k] = str_replace(
				self::$attrs_reps[$key],
				self::$attrs_oris[$key],
				$v
			);
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
		if (empty(Validate::$hl_htmls[$url])) return false;

		preg_match("/\<!DOCTYPE [^\>]+?\>/", Validate::$hl_htmls[$url], $ms);

		// html5
		if ( ! isset($ms[0]))
		{
			preg_match("/\<!DOCTYPE html\>/i", Validate::$hl_htmls[$url], $ms);
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

	/**
	 * get lang
	 *
	 * @param  String $url
	 * @return String
	 */
	public static function getLang($url)
	{
		if (isset(static::$langs[$url])) return static::$langs[$url];
		if (empty(Validate::$hl_htmls[$url])) return '';

		preg_match("/\<html ([^\>]+?)\>/is", Validate::$hl_htmls[$url], $ms);
		if ( ! isset($ms[0])) return ''; // langless

		$attrs = self::getAttributes($ms[0]);
		if ( ! isset($attrs['lang'])) return '';
		static::$langs[$url] = $attrs['lang'];
		return static::$langs[$url];
	}
}
