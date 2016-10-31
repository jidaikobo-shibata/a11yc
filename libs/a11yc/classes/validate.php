<?php
/**
 * A11yc\Validate
 *
 * @package    part of A11yc
 * @version    1.0
 * @author     Jidaikobo Inc.
 * @license    WTFPL2.0
 * @copyright  Jidaikobo Inc.
 * @link       http:/www.jidaikobo.com
 */
namespace A11yc;
class Validate
{
	protected static $target_path;
	protected static $error_ids = array();
	protected static $html = '';
	protected static $hl_html = ''; // HighLighted

	public static $ignores = array(
		"/\<script.+?\<\/script\>/si",
		"/\<style.+?\<\/style\>/si",
		"/\<rdf:RDF.+?\<\/rdf:RDF\>/si",
	);

	public static $ignores_comment_out = array(
		"/\<!--.+?--\>/si",
	);

	/**
	 * set_target_path
	 *
	 * param string $target_path
	 * @return  void
	 */
	public static function set_target_path($target_path)
	{
		static::$target_path = rtrim($target_path, '/');
	}

	/**
	 * upper_path
	 *
	 * param string $str
	 * @return  string
	 */
	public static function upper_path($str)
	{
		$num = substr_count($str, '../');
		$ret = '';
		for ($n = 1; $n <= $num; $n++)
		{
			$ret = dirname(static::$target_path);
		}

		$headers = @get_headers($ret);
		if (
			$headers !== false &&
			(
				strpos($headers[0], ' 20') !== false ||
				strpos($headers[0], ' 30') !== false
			)
		)
		{
			return $ret;
		}

		return static::$target_path;
	}

	/**
	 * get_errors
	 *
	 * @return  array
	 */
	public static function get_errors()
	{
		return static::$errors;
	}

	/**
	 * get_error_ids
	 *
	 * @return  array
	 */
	public static function get_error_ids()
	{
		return static::$error_ids;
	}

	/**
	 * set_html
	 *
	 * @param   strings     $str
	 * @return  void
	 */
	public static function set_html($str)
	{
		static::$html = $str;
		static::$hl_html = $str;
	}

	/**
	 * get_html
	 *
	 * @return  array
	 */
	public static function get_html()
	{
		return static::$html;
	}

	/**
	 * get_hl_html
	 *
	 * @return  array
	 */
	public static function get_hl_html()
	{
		return static::$hl_html;
	}

	/**
	 * ignore_elements
	 *
	 * @param   strings     $str
	 * @param   bool        $force
	 * @return  $str
	 */
	public static function ignore_elements($str, $force = false)
	{
		static $retval = '';
		if ($retval && ! $force) return $retval;

		// ignore comment out, script, style
		$ignores = array_merge(static::$ignores, static::$ignores_comment_out);
		foreach ($ignores as $ignore)
		{
			$str = preg_replace($ignore, '', $str);
		}

		return $str;
	}

	/**
	 * ignore_comment_out
	 *
	 * @param   strings     $str
	 * @return  $str
	 */
	public static function ignore_comment_out($str)
	{
		static $retval = '';
		if ($retval) return $retval;

		// ignore comment out only
		foreach (static::$ignores_comment_out as $ignore)
		{
			$str = preg_replace($ignore, '', $str);
		}

		return $str;
	}

	/**
	 * is_ignorable
	 *
	 * @param   strings     $str
	 * @return  bool
	 */
	public static function is_ignorable($str)
	{
		$attrs = static::get_attributes($str);

		// Strictly this is not so correct. but it seems be considered.
		if (
			isset($attrs['tabindex']) && $attrs['tabindex'] = -1 ||
			isset($attrs['aria-hidden']) && $attrs['tabindex'] = 'true'
		)
		{
			return true;
		}

		// occasionally JavaScript provides function by id or class.
		if (isset($attrs['href']) && strpos($attrs['href'], 'javascript') === 0)
		{
			return true;
		}

		// occasionally JavaScript use #.
		if (isset($attrs['href']) && $attrs['href'] == '#')
		{
			return true;
		}

		// mailto
		if (isset($attrs['href']) && substr($attrs['href'], 0, 7) == 'mailto:')
		{
			return true;
		}

		return false;
	}

	/**
	 * correct url
	 *
	 * @param   strings     $str
	 * @return  strings
	 */
	public static function correct_url($str)
	{
		// base path
		$root_path = join("/", array_slice(explode("/", static::$target_path), 0, 3));
		if (empty($str)) return static::$target_path;

		// do not contain "mailto" because mailto doesn't return header.
		$schemes = array('http', 'https', 'file', 'ftp', 'gopher', 'news', 'nntp', 'telnet', 'wais', 'prospero');

		// care with start with '//'
		if (substr($str, 0, 2) == '//')
		{
			$str = $str;
		}
		else
		{
			// root relative path.
			if ($str[0] == '/' && isset($str[1]) && $str[1] != '/')
			{
				$str = $root_path.$str;
			}
			else if(substr($str, 0, 2) == './')
			{
				$str = static::$target_path.'/'.substr($str, 2);
			}
			else if(substr($str, 0, 3) == '../')
			{
				$str = static::upper_path($str).'/'.substr($str, 3);
			}

			// scheme
			$scheme = substr($str, 0, strpos($str, ':'));
			if (
				in_array($scheme, $schemes) ||
				(strpos($str, ':') !== false && ! in_array($scheme, $schemes))
			)
			{
				$str = $str;
			}
			// maybe link to file
			else if ($str[0] != '#')
			{
				$str = static::$target_path.'/'.$str;
			}
			// maybe fragment
			else
			{
				$str = $str;
			}
		}

		return $str;
	}

	/**
	 * get_attributes
	 *
	 * @param   strings $attrs
	 * @return  array()
	 */
	public static function get_attributes($str)
	{
		static $retvals = array();
		if (isset($retvals[$str])) return $retvals[$str];

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
			$str = $ms[0];
		}
		$str = ' '.$str;

		//
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
		$inner_space   = '[---a11yc_innerspace---]';

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

		$qoutes = array();
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
					array($target, $opp, ' '),
					array('', $inner, $inner_space),
					$search);
				$replace = $open.$replace.$close;
				// replace value
				$str = str_replace($search, $replace, $str);
			}
		}

		$str = preg_replace("/  +/", " ", $str); // remove plural spaces
		$str = str_replace(" = ", "=", $str); // remove plural spaces
		$attrs = array();
		$strs = explode(' ', $str);

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
					$inner_space
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
					" "
				),
				$val
			);

			// valid attributes
			if (
				in_array($key, $ruled_attrs) ||
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
		$retvals[$str] = $attrs;

		return $retvals[$str];
	}

	/**
	 * get elements by regular expression
	 *
	 * @param   strings $str
	 * @param   strings $type (anchors|anchors_and_values|imgs|tags)
	 * @return  void
	 */
	public static function get_elements_by_re($str, $type = 'tags')
	{
		static $retvals = array();
		if (isset($retvals[$type])) return $retvals[$type];

		switch ($type)
		{
			case 'anchors':
				if (preg_match_all("/\<(?:a|area) ([^\>]+?)\>/i", $str, $ms))
				{
					$retvals[$type] = $ms;
				}
				break;
			case 'anchors_and_values':
				if (preg_match_all("/\<a ([^\>]+)\>(.*?)\<\/a\>|\<area ([^\>]+?)\/\>/si", $str, $ms))
				{
					$retvals[$type] = $ms;
				}
				break;
			case 'imgs':
				if (preg_match_all("/\<img ([^\>]+?)\>/i", $str, $ms))
				{
					$retvals[$type] = $ms;
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
					$retvals[$type] = $ret;
				}
				break;
		}
		return isset($retvals[$type]) ? $retvals[$type] : false;
	}

	/**
	 * get_doctype
	 *
	 * @return  mixed
	 */
	public static function get_doctype()
	{
		if (empty(static::$hl_html)) die('invalid access at A11yc\Validate::get_doctype().');

		preg_match("/\<!DOCTYPE [^\>]+?\>/", static::$hl_html, $ms);

		if ( ! isset($ms[0])) return false; // doctypeless

		// doctype
		$doctype = false;

		// html5
		if($ms[0] == '<!DOCTYPE html>')
		{
			$doctype = 'html5';
		}
		// HTML4
		else if (strpos($ms[0], 'DTD HTML 4.01') !== false)
		{
			$doctype = 'html4';
		}
		// xhtml1
		else if(strpos($ms[0], 'DTD XHTML 1.0 ') !== false)
		{
			$doctype = 'xhtml1';
		}

		return $doctype;
	}

	/**
	 * add error to html
	 *
	 * @param   strings  $error_id
	 * @param   array    $errors
	 * @param   str      $ignore_vals
	 * @return  void
	 */
	public static function add_error_to_html($error_id, $s_errors, $ignore_vals = '')
	{
		// values
		$yml = Yaml::fetch();
		$html = static::$hl_html;

		// errors
		if ( ! isset($s_errors[$error_id])) return;
		$errors = array();
		foreach ($s_errors[$error_id] as $k => $v)
		{
			$errors[$k] = $v['id'];
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

		// first tag
		$first_tags = static::get_elements_by_re(static::$hl_html, 'tags');
		$first_tag = $first_tags[0][0];

		$lv = strtolower($yml['criterions'][$yml['errors'][$error_id]['criterion']]['level']['name']);

		// replace errors
		$results = array();
		$replaces = array();

		foreach ($errors as $k => $error)
		{
			$offset = 0;
			$error_len = mb_strlen($error, "UTF-8");

			// hash strgings to avoid wrong replace
			$original = '[===a11yc_rplc==='.$error_id.'_'.$k.'===a11yc_rplc_title==='.$yml['errors'][$error_id]['message'].'===a11yc_rplc_class==='.$lv.'===a11yc_rplc===][===a11yc_rplc_strong_class==='.$lv.'===a11yc_rplc_strong===]';
			$replaced = '===a11yc_rplc==='.hash("sha256", $original).'===a11yc_rplc===';

			$end_original = '[===end_a11yc_rplc==='.$error_id.'_'.$k.'===a11yc_rplc_back_class==='.$lv.'===end_a11yc_rplc===]';
			$end_replaced = '===aend_11yc_rplc==='.hash("sha256", $end_original).'===end_a11yc_rplc===';
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
				// always search first tag
				$pos = mb_strpos($html, $first_tag, 0, "UTF-8");
			}

			// add error
			$html = mb_substr($html, 0, $pos, "UTF-8").$replaced.mb_substr($html, $pos, NULL, "UTF-8");

			// and end point
			$end_pos = $pos + $err_rep_len + $error_len;
			$html = mb_substr($html, 0, $end_pos, "UTF-8").$end_replaced.mb_substr($html, $end_pos, NULL, "UTF-8");

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

		static::$hl_html = $html;
	}
}
