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
	public static $is_partial = false;
	protected static $do_link_check = false;
	protected static $do_css_check = false;
	protected static $error_ids = array();
	protected static $html = '';
	protected static $hl_html = ''; // HighLighted
	protected static $csses = array();

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
					$all_errs[$err_type][] = Message::getText($url, $code, $err, $key);
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
			$ignores = Element::$$ignore_vals;

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
}
