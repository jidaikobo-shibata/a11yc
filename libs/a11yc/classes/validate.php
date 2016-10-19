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
	protected static $base_path;
	protected static $target_path;
	protected static $error_ids = array();
	protected static $errors = array();
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
	 * set_base_path
	 *
	 * param string $target_path
	 * @return  void
	 */
	public static function set_base_path($target_path)
	{
		$setup = Controller_Setup::fetch_setup();
		static::$base_path = rtrim($setup['base_path'], '/');
		static::$target_path = rtrim($target_path, '/');
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
		$maybe_base_pathes = explode("/", static::$target_path);
		static::$base_path = static::$base_path ?: join("/", array_slice($maybe_base_pathes, 0, 3));
		if (empty($str)) return static::$target_path;

		// care with start with '//'
		if (substr($str, 0, 2) == '//')
		{
			$str = $str;
		}
		else
		{
			// root relative path.
			if ($str[0] == '/' && $str[1] != '/')
			{
				$str = $str[0] == '/' ? static::$base_path.$str : $str;
			}
			elseif(substr($str, 0, 2) == './')
			{
				$str = static::$target_path.'/'.substr($str, 2);
			}
			elseif(substr($str, 0, 3) == '../')
			{
				$str = dirname(dirname(static::$target_path)).'/'.substr($str, 3);
			}

			// scheme
			$scheme = substr($str, 0, strpos($str, ':'));
			if (in_array($scheme, array('http', 'https', 'file', 'mailto', 'gopher', 'news', 'nntp', 'telnet', 'wais', 'prospero', 'javascript')))
			{
				$str = $str;
			}
			// maybe link to file
			else if ($str[0] != '#')
			{
				$ds = $str[0] != '/' ? '/' : '';
				$str = static::$base_path.$ds.$str;
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

		$str = preg_replace("/ +/", " ", $str); // remove plural spaces
		$str = str_replace('"', "'", $str); // integration quote
		$str = str_replace("= '", "='", $str); // integration delimiter
		$str = str_replace('<', " <", $str); // divide tags
		$attrs = array();

		foreach (explode(' ', $str) as $k => $v)
		{
			if (empty($v)) continue;
			if ($v[0] == '<') continue;
			if (strpos($v, "='") === false) continue;
			list($key, $val) = explode("='", $v);
			$val = rtrim($val, ">");
			// suspicious
			if (array_key_exists($key, $attrs))
			{
				$key = $key.'_'.$k;
				$attrs['suspicious'] = TRUE;
			}
			$attrs[$key] = trim($val, "'");
		}
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
				if (preg_match_all("/\<a([^\>]+)\>/i", $str, $ms))
				{
					$retvals[$type] = $ms;
				}
				break;
			case 'anchors_and_values':
				if (preg_match_all("/\<a([^\>]+)\>(.*?)\<\/a\>/si", $str, $ms))
				{
					$retvals[$type] = $ms;
				}
				break;
			case 'imgs':
				if (preg_match_all("/\<img([^\>]+)\>/i", $str, $ms))
				{
					$retvals[$type] = $ms;
				}
				break;
			default:
				if (preg_match_all("/(?:\<[a-zA-Z1-6]+? +?([^\>]*?)[\/]*\>|\<[a-zA-Z1-6]+?[\/]*\>)/i", $str, $ms))
				{
					$retvals[$type] = $ms;
				}
				break;
		}
		return $retvals[$type];
	}

	/**
	 * add error to html
	 *
	 * @param   strings  $error_id
	 * @param   array    $errors
	 * @return  void
	 */
	public static function add_error_to_html($error_id, $errors, $ignore_vals = '')
	{
		$html = static::$hl_html;

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

		// replace errors
		$results = array();
		$replaces = array();
		foreach ($errors as $k => $error)
		{
			$offset = 0;

			// hash strgings to avoid wrong replace
			$original = '[===a11yc_rplc==='.$error_id.'_'.$k.'===a11yc_rplc===]';
			$replaced = '===a11yc_rplc==='.hash("sha256", $original).'===a11yc_rplc===';
			$replaces[$k] = array(
				'original' => $original,
				'replaced' => $replaced,
			);
			$err_span_length = strlen($replaced);

			// first search
			$pos = mb_strpos($html, $error, $offset);

			// is already replaced?
			if (in_array($pos, $results))
			{
				//  search next
				$offset = max($results) + 1;
				$pos = mb_strpos($html, $error, $offset);
			}

			// add error
			$html = mb_substr($html, 0, $pos).$replaced.mb_substr($html, $pos);
			$results[] = $pos + $err_span_length;
		}

		// hash to error
		foreach ($replaces as $v)
		{
			$html = str_replace($v['replaced'], $v['original'], $html);
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
