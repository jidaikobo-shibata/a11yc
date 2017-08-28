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

class Validate
{
	protected static $is_partial = false;
	protected static $do_link_check = false;
	protected static $error_ids = array();
	protected static $first_tag = '';
	protected static $res = array();
	protected static $ignored_str = '';
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

	public static $codes                  = array(
			// elements
			'empty_alt_attr_of_img_inside_a'  => '\A11yc\Validate_Alt',
			'here_link'                       => '\A11yc\Validate_Link',
			'tell_user_file_type'             => '\A11yc\Validate_Link',
			'same_urls_should_have_same_text' => '\A11yc\Validate_Link',
			'form_and_labels'                 => '\A11yc\Validate_Form',
			'headerless_section'              => '\A11yc\Validate_Validation',

			// single tag
			'alt_attr_of_img'                 => '\A11yc\Validate_Alt',
			'img_input_has_alt'               => '\A11yc\Validate_Alt',
			'area_has_alt'                    => '\A11yc\Validate_Alt',
			'same_alt_and_filename_of_img'    => '\A11yc\Validate_Alt',
			'not_label_but_title'             => '\A11yc\Validate_Form',
			'unclosed_elements'               => '\A11yc\Validate_Validation',
			'invalid_single_tag_close'        => '\A11yc\Validate_Validation',
			'suspicious_elements'             => '\A11yc\Validate_Validation',
			'meanless_element'                => '\A11yc\Validate_Validation',
			'style_for_structure'             => '\A11yc\Validate_Validation',
			'invalid_tag'                     => '\A11yc\Validate_Validation',
			'titleless_frame'                 => '\A11yc\Validate_Attr',
			'check_doctype'                   => '\A11yc\Validate_Head',
			'meta_refresh'                    => '\A11yc\Validate_Head',
			'titleless'                       => '\A11yc\Validate_Head',
			'langless'                        => '\A11yc\Validate_Head',
			'viewport'                        => '\A11yc\Validate_Head',

			// link check
			'link_check'                      => '\A11yc\Validate_Link',

			// non tag
			'appropriate_heading_descending'  => '\A11yc\Validate_Validation',
			'ja_word_breaking_space'          => '\A11yc\Validate_Validation',
			'suspicious_attributes'           => '\A11yc\Validate_Attr',
			'duplicated_ids_and_accesskey'    => '\A11yc\Validate_Attr',
			'must_be_numeric_attr'            => '\A11yc\Validate_Attr',
			'same_page_title_in_same_site'    => '\A11yc\Validate_Head',
		);

	/**
	 * set_do_link_check
	 *
	 * @param  bool
	 * @return  void
	 */
	public static function set_do_link_check($bool)
	{
		static::$do_link_check = $bool;
	}

	/**
	 * do_link_check
	 *
	 * @return  bool
	 */
	public static function do_link_check()
	{
		return static::$do_link_check;
	}

	/**
	 * set_is_partial
	 *
	 * @param  bool
	 * @return  void
	 */
	public static function set_is_partial($bool)
	{
		static::$is_partial = $bool;
	}

	/**
	 * is_partial
	 *
	 * @return  bool
	 */
	public static function is_partial()
	{
		return static::$is_partial;
	}

	/**
	 * get_errors
	 *
	 * @return Array
	 */
	public static function get_errors()
	{
		return static::$errors;
	}

	/**
	 * get_error_ids
	 *
	 * @return Array
	 */
	public static function get_error_ids()
	{
		return static::$error_ids;
	}

	/**
	 * set_html
	 *
	 * @param  String $str
	 * @return Void
	 */
	public static function set_html($str)
	{
		static::$html = $str;
		static::$hl_html = $str;
	}

	/**
	 * get_html
	 *
	 * @return String
	 */
	public static function get_html()
	{
		return static::$html;
	}

	/**
	 * get_hl_html
	 *
	 * @return String
	 */
	public static function get_hl_html()
	{
		return static::$hl_html;
	}

	/**
	 * ignore_elements
	 *
	 * @param  String $str
	 * @param  Bool $force
	 * @return String
	 */
	public static function ignore_elements($str, $force = false)
	{
		if (static::$ignored_str && ! $force) return static::$ignored_str;

		// ignore comment out, script, style
		$ignores = array_merge(static::$ignores, static::$ignores_comment_out);

		foreach ($ignores as $ignore)
		{
			$str = preg_replace($ignore, '', $str);
		}

		// set first tag
		$first_tags = static::get_elements_by_re($str, 'ignores', 'tags');
		static::$first_tag = Arr::get($first_tags, '0.0');

		static::$ignored_str = $str;
		return $str;
	}

	/**
	 * ignore_comment_out
	 *
	 * @param  String $str
	 * @return String
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
		$retval = $str;
		return $retval;
	}

	/**
	 * is_ignorable
	 *
	 * @param  String $str
	 * @return Bool
	 */
	public static function is_ignorable($str)
	{
		$attrs = static::get_attributes($str);

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
	 * get_attributes
	 *
	 * @param  String $str
	 * @return Array
	 */
	public static function get_attributes($str)
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
	public static function get_elements_by_re($str, $ignore_type, $type = 'tags', $force = false)
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
	 * get_doctype
	 *
	 * @return String|Bool
	 */
	public static function get_doctype()
	{
		if (empty(static::$hl_html)) Util::error('invalid access at A11yc\Validate::get_doctype().');

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
	 * @param  String $error_id
	 * @param  Array $errors
	 * @param  String $ignore_vals
	 * @return Void
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
			$replaced = '===a11yc_rplc==='.hash("sha256", $original).'===/a11yc_rplc===';

			$end_original = '[===end_a11yc_rplc==='.$error_id.'_'.$k.'===a11yc_rplc_back_class==='.$lv.'===end_a11yc_rplc===]';
			$end_replaced = '===end_a11yc_rplc==='.hash("sha256", $end_original).'===/end_a11yc_rplc===';

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
				$pos = static::$first_tag ? mb_strpos($html, static::$first_tag, 0, "UTF-8") : 0;
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

		static::$hl_html = $html;
	}

	/**
	 * revert html
	 *
	 * @param  String $html
	 * @return String
	 */
	public static function revert_html($html)
	{
		$retval = str_replace(
			array(
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
				'===end_a11yc_rplc===]'
			),
			array(
				// span
				'<span id="',
				'" title="',
				'" class="a11yc_validation_code_error a11yc_level_',

				// span to strong
				'" tabindex="0">ERROR!</span><strong class="a11yc_level_',
				'">',

				// strong to end
				'</strong><a href="#index_',
				'" class="a11yc_back_link a11yc_hasicon a11yc_level_',
				'" title="back to error"><span class="a11yc_icon_fa a11yc_icon_arrow_u" role="presentation" aria-hidden="true"></span><span class="a11yc_skip">back</span></a>',
			),
			$html);
		return $retval;
	}
}
