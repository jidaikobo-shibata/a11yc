<?php
/**
 * A11yc\Message
 *
 * @package    part of A11yc
 * @author     Jidaikobo Inc.
 * @license    The MIT License (MIT)
 * @copyright  Jidaikobo Inc.
 * @link       http://www.jidaikobo.com
 */
namespace A11yc;

class Message
{
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
	public static function getText($url, $code_str, $place, $key, $docpath = '')
	{
		$yml = Yaml::fetch();

		$current_err = Validate::setCurrentErr($url, $code_str, $place);

		// set error to message
		if ($current_err)
		{
			$docpath = $docpath ?: A11YC_DOC_URL;


			// level - use lower level
			$lv = strtolower($yml['criterions'][$current_err['criterions'][0]]['level']['name']);

			// count errors
			if ( ! isset($current_err['notice'])) Validate::$err_cnts[$lv]++;

			// dt and dd
			$ret = self::dt($url, $code_str, $place, $key, $docpath, $current_err, $lv);
			$ret.= '<dd class="a11yc_validation_error_str a11yc_level_'.$lv.'" data-level="'.$lv.'" data-place="'.Util::s($place['id']).'">'.Util::s($place['str']).'</dd>';

			return $ret;
		}
		return FALSE;
	}

	/**
	 * dt
	 *
	 * @param String $url
	 * @param String $code_str
	 * @param Array $place
	 * @param String $key
	 * @param String $docpath
	 * @param Array $current_err
	 * @param String $lv
	 * @return Array
	 */
	private static function dt($url, $code_str, $place, $key, $docpath, $current_err, $lv)
	{
		$yml = Yaml::fetch();

		$anchor = $code_str.'_'.$key;

		// dt
		$ret = '<dt id="index_'.$anchor.'" tabindex="-1" class="a11yc_level_'.$lv.'">'.$current_err['message'];

		// dt - information
		foreach ($current_err['criterions'] as $each_criterion)
		{
			$criterion = $yml['criterions'][$each_criterion];

			$ret.= '<span class="a11yc_validation_reference_info"><strong>'.A11YC_LANG_LEVEL.strtoupper($lv).'</strong> <strong>'.Util::key2code($criterion['code']).'</strong> ';
			$ret.= '<a href="'.$docpath.$each_criterion.'" target="a11yc_doc">'.A11YC_LANG_CHECKLIST_SEE_DETAIL.'('.Util::key2code($criterion['code']).')</a> ';

			if ( ! defined('A11YC_IS_GUEST_VALIDATION'))
			{
				$ret.= '[<a href="'.A11YC_ISSUES_ADD_URL.$url.'&amp;criterion='.$each_criterion.'&amp;err_id='.$code_str.'&amp;src='.Util::s($place['str']).'" target="a11yc_issue">'.A11YC_LANG_ISSUES_ADD.'</a>] ';
			}

			$ret.= '</span>';
		}

		if ($place['id'])
		{
			$ret.= '<a href="#'.$anchor .'" class="a11yc_validation_error_link a11yc_level_'.$lv.' a11yc_hasicon"><span class="a11yc_icon_fa a11yc_icon_arrow_b" role="presentation" aria-hidden="true"></span>Code</a>';
		}
		$ret.= '</dt>';
		return $ret;
	}
}
