<?php
/**
 * A11yc\Validate
 *
 * @package    part of A11yc
 * @author     Jidaikobo Inc.
 * @license    The MIT License (MIT)
 * @copyright  Jidaikobo Inc.
 * @link       http://www.jidaikobo.com
 */
namespace A11yc;

use A11yc\Model;

class Validate
{
	protected static $is_partial = false;
	protected static $do_link_check = false;
	protected static $do_css_check = false;
	protected static $error_ids = array();
	protected static $first_tag = '';
	protected static $res = array();
	protected static $ignored_str = '';
	protected static $html = '';
	protected static $hl_html = ''; // HighLighted
	protected static $csses = array();

	public static $ignores = array(
		"/\<script.+?\<\/script\>/si",
		"/\<style.+?\<\/style\>/si",
		"/\<rdf:RDF.+?\<\/rdf:RDF\>/si",
	);

	public static $ignores_comment_out = array(
		"/\<!--.+?--\>/si",
	);

	public static $codes = array(
			// elements
			'EmptyAltAttrOfImgInsideA',
			'HereLink',
			'TellUserFileType',
			'SameUrlsShouldHaveSameText',
			'EmptyLinkElement',
			'FormAndLabels',
			'HeaderlessSection',
			'IsseusElements',
			'Table',

			// single tag
			'AltAttrOfImg',
			'ImgInputHasAlt',
			'AreaHasAlt',
			'SameAltAndFilenameOfImg',
			'NotLabelButTitle',
			'UnclosedElements',
			'InvalidSingleTagClose',
			'SuspiciousElements',
			'MeanlessElement',
			'StyleForStructure',
			'InvalidTag',
			'TitlelessFrame',
			'CheckDoctype',
			'MetaRefresh',
			'Titleless',
			'Langless',
			'Viewport',
			'IssuesSingle',

			// link check
			'LinkCheck',

			// non tag
			'AppropriateHeadingDescending',
			'JaWordBreakingSpace',
			'SuspiciousAttributes',
			'DuplicatedIdsAndAccesskey',
			'MustBeNumericAttr',
			'SamePageTitleInSameSite',
			'NoticeImgExists',
			'NoticeNonHtmlExists',
			'IssuesNonTag',

			// css
			'CssTotal',
			'CssContent',
			'CssInvisibles',
		);

	protected static $results = array();
	protected static $hl_htmls = array();
	static public $err_cnts = array('a' => 0, 'aa' => 0, 'aaa' => 0);

	/**
	 * codes2name
	 *
	 * @param  Array  $codes
	 * @return String
	 */
	public static function codes2name($codes = array())
	{
		return md5(join($codes));
	}

	/**
	 * html2id
	 *
	 * @param  String $html
	 * @return String
	 */
	public static function html2id($html)
	{
		return str_replace(array('+', '/', '*', '='), '', base64_encode($html));
	}

	/**
	 * html
	 *
	 * @param  String $url
	 * @param  String $html
	 * @param  Array  $codes
	 * @param  String $ua
	 * @param  Bool   $force
	 * @return Void
	 */
	public static function html($url, $html, $codes = array(), $ua = 'using', $force = 0)
	{
		$codes = $codes ?: self::$codes;

		$name = static::codes2name($codes);
		if (isset(static::$results[$url][$name][$ua]) && ! $force) return static::$results[$url][$name][$ua];

		static::$hl_htmls[$url] = $html;

		// errors
		static::$error_ids[$url] = array();

		// validate
		foreach ($codes as $class)
		{
			$class = 'A11yc\\Validate\\'.$class;
			$class::check($url);
		}

		// errors
		$yml = Yaml::fetch();
		$all_errs = array(
			'notices' => array(),
			'errors' => array()
		);
		if (static::$error_ids[$url])
		{
			foreach (static::$error_ids[$url] as $code => $errs)
			{
				foreach ($errs as $key => $err)
				{
					$err_type = isset($yml['errors'][$code]['notice']) ? 'notices' : 'errors';
					$all_errs[$err_type][] = static::message($url, $code, $err, $key);
				}
			}
		}
		static::$results[$url][$name][$ua]['html'] = $html;
		static::$results[$url][$name][$ua]['hl_html'] = self::revertHtml(Util::s(static::$hl_htmls[$url]));
		static::$results[$url][$name][$ua]['errors'] = $all_errs;
		static::$results[$url][$name][$ua]['errs_cnts'] = static::$err_cnts;
	}

	/**
	 * url
	 *
	 * @param  String $url
	 * @param  Array  $codes
	 * @param  String $ua
	 * @param  Bool   $force
	 * @return Void
	 */
	public static function url($url, $codes = array(), $ua = 'using', $force = 0)
	{
		// cache
		$codes = $codes ?: self::$codes;
		$name = static::codes2name($codes);
		if (isset(static::$results[$url][$name][$ua]) && ! $force) return static::$results[$url][$name][$ua];

		// get html and set it to temp value
		self::html($url, Model\Html::getHtml($url, $ua), $codes, $ua, $force);
	}

	/**
	 * css
	 *
	 * @param  String $url
	 * @param  String $ua
	 * @return Void
	 */
	public static function css($url, $ua = 'using')
	{
		if (isset(static::$csses[$url][$ua])) return static::$csses[$url][$ua];
		return Model\Css::fetchCss($url, $ua);
	}

	/**
	 * getErrorCnts
	 *
	 * @param  String $url
	 * @param  Array  $codes
	 * @param  String $ua
	 * @param  Bool   $force
	 * @return Array
	 */
	public static function getErrorCnts($url, $codes = array(), $ua = 'using', $force = false)
	{
		$codes = $codes ?: self::$codes;
		$name = static::codes2name($codes);
		if (isset(static::$results[$url][$name][$ua]['errs_cnts']) && ! $force) return static::$results[$url][$name][$ua]['errs_cnts'];
		return array();
	}

	/**
	 * getErrors
	 *
	 * @param  String $url
	 * @param  Array  $codes
	 * @param  String $ua
	 * @param  Bool   $force
	 * @return String
	 */
	public static function getErrors($url, $codes = array(), $ua = 'using', $force = false)
	{
		$codes = $codes ?: self::$codes;
		$name = static::codes2name($codes);
		if (isset(static::$results[$url][$name][$ua]['errors']) && ! $force) return static::$results[$url][$name][$ua]['errors'];
		return false;
	}

	/**
	 * getHighLightedHtml
	 *
	 * @param  String $url
	 * @param  Array  $codes
	 * @param  String $ua
	 * @param  Bool   $force
	 * @return String
	 */
	public static function getHighLightedHtml($url, $codes = array(), $ua = 'using', $force = false)
	{
		$codes = $codes ?: self::$codes;
		$name = static::codes2name($codes);

		if (isset(static::$results[$url][$name][$ua]['hl_html']) && ! $force) return static::$results[$url][$name][$ua]['hl_html'];
		return false;
	}

	/**
	 * get error ids
	 *
	 * @param  String $url
	 * @return Array
	 */
	public static function getErrorIds($url)
	{
		return isset(static::$error_ids[$url]) ? static::$error_ids[$url] : array();
	}

	/**
	 * set_do_link_check
	 *
	 * @param  bool
	 * @return  void
	 */
	public static function setDoLinkCheck($bool)
	{
		static::$do_link_check = $bool;
	}

	/**
	 * do_link_check
	 *
	 * @return  bool
	 */
	public static function doLinkCheck()
	{
		if ( ! \A11yc\Guzzle::envCheck()) return false;
		return static::$do_link_check;
	}

	/**
	 * set_do_css_check
	 *
	 * @param  bool
	 * @return  void
	 */
	public static function setDoCssCheck($bool)
	{
		static::$do_css_check = $bool;
	}

	/**
	 * do_css_check
	 *
	 * @return  bool
	 */
	public static function doCssCheck()
	{
		return static::$do_css_check;
	}

	/**
	 * set_is_partial
	 *
	 * @param  bool
	 * @return  void
	 */
	public static function setIsPartial($bool)
	{
		static::$is_partial = $bool;
	}

	/**
	 * is_partial
	 *
	 * @return  bool
	 */
	public static function isPartial()
	{
		return static::$is_partial;
	}

	/**
	 * is_ignorable
	 *
	 * @param  String $str
	 * @return Bool
	 */
	public static function isIgnorable($str)
	{
		$attrs = static::getAttributes($str);

		if (
			// Strictly this is not so correct. but it seems be considered.
			(isset($attrs['tabindex']) && $attrs['tabindex'] = -1) ||
			(isset($attrs['aria-hidden']) && $attrs['tabindex'] = 'true') ||

			// occasionally JavaScript provides function by id or class.
			(isset($attrs['href']) && strpos($attrs['href'], 'javascript') === 0) ||

			// occasionally JavaScript use #
			(isset($attrs['href']) && $attrs['href'] == '#') ||

			// mailto
			(isset($attrs['href']) && substr($attrs['href'], 0, 7) == 'mailto:')
		)
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
		$first_tags = static::getElementsByRe($str, 'ignores', 'tags');
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

	/**
	 * add error to html
	 *
	 * @param  String $url
	 * @param  String $error_id
	 * @param  Array  $errors
	 * @param  String $ignore_vals
	 * @param  String $issue_html
	 * @return Void
	 */
	public static function addErrorToHtml(
		$url,
		$error_id,
		$s_errors,
		$ignore_vals = '',
		$issue_html = ''
	)
	{
		// values
		$yml = Yaml::fetch();
		$html = static::$hl_htmls[$url];

		// Yaml not exist
		$current_err = array();
		if ( ! isset($yml['errors'][$error_id]))
		{
			$issue = Model\Issues::fetch4Validation($url, $issue_html);
			if ( ! $issue) return;
			$current_err['message']   = $issue['error_message'];
			if (strpos($issue['criterion'], ',') !== false)
			{
				$issue_criterions = explod(',', $issue['criterion']);
				$current_err['criterions'] = array(trim($issue_criterions[0])); // use one
			}
			else
			{
				$current_err['criterions'] = array(trim($issue['criterion']));
			}
			$current_err['code']      = '';
			$current_err['notice']    = ($issue['n_or_e'] == 0);
		}
		else
		{
			$current_err = $yml['errors'][$error_id];
		}

		// errors
		if ( ! isset($s_errors[$error_id])) return;
		$errors = array();
		foreach ($s_errors[$error_id] as $k => $v)
		{
			$errors[$k] = $v['id'] === false ? static::$first_tag : $v['id'];
		}

		// ignore elements or comments
		$replaces_ignores = array();
		if ($ignore_vals)
		{
			$ignores = static::$$ignore_vals;

			foreach ($ignores as $k => $ignore)
			{
				preg_match_all($ignore, $html, $ms);
				if ($ms)
				{
					foreach ($ms[0] as $kk => $vv)
					{
						$original = $vv;
						$replaced = hash("sha256", $vv);
						$replaces_ignores[$k][$kk] = array(
							'original' => $original,
							'replaced' => $replaced,
						);
						$html = str_replace($original, $replaced, $html);
					}
				}
			}
		}

		$lv = strtolower($yml['criterions'][$current_err['criterions'][0]]['level']['name']);

		// replace errors
		$results = array();
		$replaces = array();

		// notice
		$is_notice = isset($current_err['notice']) && $current_err['notice'];

		foreach ($errors as $k => $error)
		{
			$offset = 0;
			$error_len = mb_strlen($error, "UTF-8");

			// hash strgings to avoid wrong replace
			$rplc = $is_notice ? 'a11yc_notice_rplc' : 'a11yc_rplc';
			$original = '[==='.$rplc.'==='.$error_id.'_'.$k.'==='.$rplc.'_title==='.$current_err['message'].'==='.$rplc.'_class==='.$lv.'==='.$rplc.'===][==='.$rplc.'_strong_class==='.$lv.'==='.$rplc.'_strong===]';
			$replaced = '==='.$rplc.'==='.hash("sha256", $original).'===/'.$rplc.'===';

			$end_original = '[===end_'.$rplc.'==='.$error_id.'_'.$k.'==='.$rplc.'_back_class==='.$lv.'===end_'.$rplc.'===]';
			$end_replaced = '===end_'.$rplc.'==='.hash("sha256", $end_original).'===/end_'.$rplc.'===';

			$replaces[$k] = array(
				'original' => $original,
				'replaced' => $replaced,

				'end_original' => $end_original,
				'end_replaced' => $end_replaced,
			);
			$err_rep_len = strlen($replaced);

			// normal search
			if ($error)
			{
				// first search
				$pos = mb_strpos($html, $error, $offset, "UTF-8");

				// is already replaced?
				if (in_array($pos, $results))
				{
					//  search next
					$offset = max($results) + 1;
					$pos = mb_strpos($html, $error, $offset, "UTF-8");
				}
			}
			else
			{
				// cannot define error place
				continue;
				// always search first tag
				// $pos = static::$first_tag ? mb_strpos($html, static::$first_tag, 0, "UTF-8") : 0;
			}

			// add error
			// not use null. see http://php.net/manual/ja/function.mb-substr.php#77515
			$html = mb_substr($html, 0, $pos, "UTF-8").
						$replaced.
						mb_substr($html, $pos, mb_strlen($html), "UTF-8");

			// and end point
			$end_pos = $pos + $err_rep_len + $error_len;
			$html = mb_substr($html, 0, $end_pos, "UTF-8").
						$end_replaced.
						mb_substr($html, $end_pos, mb_strlen($html), "UTF-8");

			$results[] = $pos + $err_rep_len;
		}

		// recover error html
		foreach ($replaces as $v)
		{
			$html = str_replace($v['replaced'], $v['original'], $html);
			$html = str_replace($v['end_replaced'], $v['end_original'], $html);
		}

		// recover ignores
		foreach ($replaces_ignores as $v)
		{
			foreach ($v as $vv)
			{
				$html = str_replace($vv['replaced'], $vv['original'], $html);
			}
		}

		static::$hl_htmls[$url] = $html;
	}

	/**
	 * revert html
	 *
	 * @param  String $html
	 * @return String
	 */
	public static function revertHtml($html)
	{
		$retval = str_replace(
			array(
				// ERROR!
				// span
				'[===a11yc_rplc===',
				'===a11yc_rplc_title===',
				'===a11yc_rplc_class===',

				// strong
				'===a11yc_rplc===][===a11yc_rplc_strong_class===',
				'===a11yc_rplc_strong===]',

				// strong to end
				'[===end_a11yc_rplc===',
				'===a11yc_rplc_back_class===',
				'===end_a11yc_rplc===]',

				// NOTICE
				// span
				'[===a11yc_notice_rplc===',
				'===a11yc_notice_rplc_title===',
				'===a11yc_notice_rplc_class===',

				// strong
				'===a11yc_notice_rplc===][===a11yc_notice_rplc_strong_class===',
				'===a11yc_notice_rplc_strong===]',

				// strong to end
				'[===end_a11yc_notice_rplc===',
				'===a11yc_notice_rplc_back_class===',
				'===end_a11yc_notice_rplc===]'
			),
			array(
				// ERROR!
				// span
				'<span id="',
				'" title="',
				'" class="a11yc_validation_code_error a11yc_level_',

				// span to strong
				'" tabindex="0">ERROR!</span><strong class="a11yc_level_',
				'">',

				// strong to end
				'</strong><!-- a11yc_strong_end --><a href="#index_',
				'" class="a11yc_back_link a11yc_hasicon a11yc_level_',
				'" title="'.A11YC_LANG_CHECKLIST_BACK_TO_MESSAGE.'"><span class="a11yc_icon_fa a11yc_icon_arrow_u" role="presentation" aria-hidden="true"></span><span class="a11yc_skip">back</span></a>',

				// NOTICE
				// span
				'<span id="',
				'" title="',
				'" class="a11yc_validation_code_error a11yc_validation_code_notice a11yc_level_',

				// span to strong
				'" tabindex="0">NOTICE</span><strong class="a11yc_level_',
				'">',

				// strong to end
				'</strong><!-- a11yc_strong_end --><a href="#index_',
				'" class="a11yc_back_link a11yc_hasicon a11yc_level_',
				'" title="'.A11YC_LANG_CHECKLIST_BACK_TO_MESSAGE.'"><span class="a11yc_icon_fa a11yc_icon_arrow_u" role="presentation" aria-hidden="true"></span><span class="a11yc_skip">back</span></a>',
			),
			$html);
		return $retval;
	}

	/**
	 * message
	 *
	 * @param String $url
	 * @param String $code_str
	 * @param Array $place
	 * @param String $key
	 * @param String $docpath
	 * @return String|Bool
	 */
	public static function message($url, $code_str, $place, $key, $docpath = '')
	{
		$yml = Yaml::fetch();

		// Yaml not exist
		$current_err = array();

		if ( ! isset($yml['errors'][$code_str]))
		{
			$issue = Model\Issues::fetch4Validation($url, $place['str']);
			if ( ! $issue) return;

			$current_err['message']   = $issue['error_message'];
			if (strpos($issue['criterion'], ',') !== false)
			{
				$issue_criterions = explod(',', $issue['criterion']);
				$current_err['criterions'] = array_map('trim', $issue_criterions);
			}
			else
			{
				$current_err['criterions'] = array(trim($issue['criterion']));
			}
			$current_err['notice'] = ($issue['n_or_e'] == 0);
		}
		else
		{
			$current_err = $yml['errors'][$code_str];
		}

		// set error to message
		if ($current_err)
		{
			$docpath = $docpath ?: A11YC_DOC_URL;

			$anchor = $code_str.'_'.$key;

			// level - use lower level
			$lv = strtolower($yml['criterions'][$current_err['criterions'][0]]['level']['name']);

			// count errors
			if ( ! isset($current_err['notice'])) static::$err_cnts[$lv]++;

			// dt
			$ret = '<dt id="index_'.$anchor.'" tabindex="-1" class="a11yc_level_'.$lv.'">'.$current_err['message'];

			// dt - information
			foreach ($current_err['criterions'] as $each_criterion)
			{
				$level = $yml['criterions'][$each_criterion]['level']['name'];
				$criterion = $yml['criterions'][$each_criterion];

				$ret.= '<span class="a11yc_validation_reference_info"><strong>'.A11YC_LANG_LEVEL.strtoupper($lv).'</strong> <strong>'.Util::key2code($criterion['code']).'</strong> ';
				$ret.= '<a href="'.$docpath.$each_criterion.'" target="a11yc_doc">'.A11YC_LANG_CHECKLIST_SEE_DETAIL.'('.Util::key2code($criterion['code']).')</a> ';

				$ret.= '[<a href="'.A11YC_ISSUES_ADD_URL.$url.'&amp;criterion='.$each_criterion.'&amp;err_id='.$code_str.'&amp;src='.Util::s($place['str']).'" target="a11yc_issue">'.A11YC_LANG_ISSUES_ADD.'</a>] ';

				$ret.= '</span>';
			}

			if ($place['id'])
			{
				$ret.= '<a href="#'.$anchor .'" class="a11yc_validation_error_link a11yc_level_'.$lv.' a11yc_hasicon"><span class="a11yc_icon_fa a11yc_icon_arrow_b" role="presentation" aria-hidden="true"></span>Code</a>';
			}
			$ret.= '</dt>';

			// dd
			$ret.= '<dd class="a11yc_validation_error_str a11yc_level_'.$lv.'" data-level="'.$level.'" data-place="'.Util::s($place['id']).'">'.Util::s($place['str']).'</dd>';
//			$ret.= '<dd class="a11yc_validation_error_link a11yc_level_'.$lv.'"><a href="#'.$anchor .'" class="a11yc_hasicon">Code</a></dd>';
			return $ret;
		}
		return FALSE;
	}
}
