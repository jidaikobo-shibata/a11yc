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
	protected static $showed = array();

	/**
	 * message
	 *
	 * @param String $url
	 * @param String $code_str
	 * @param Array $place
	 * @param String $key
	 * @param Integer $num_of_err
	 * @return String|Bool
	 */
	public static function getText($url, $code_str, $place, $key, $num_of_err)
	{
		$yml = Yaml::fetch();

		$current_err = Validate::setCurrentErr($url, $code_str);

		if ($current_err === false) return false;
		if ( ! isset(self::$showed[$url])) self::$showed[$url] = array();
		$anchor = $code_str.'_'.$key;
		$ret = array();

		// set error to message
		if ($current_err)
		{
			// level - use lower level
			$lv = strtolower($yml['criterions'][$current_err['criterions'][0]]['level']['name']);

			// count errors
			if ( ! isset($current_err['notice'])) Validate::$err_cnts[$lv]++;

			$ret['code_str'] = $code_str;
			if ( ! in_array($code_str, self::$showed[$url]))
			{
				$ret['dt'] = self::dt($url, $code_str, $place, $key, $current_err, $lv, $num_of_err);
				self::$showed[$url][] = $code_str;
			}

			if ($place['id'] || $place['str'])
			{
			$ret['li'] = '<li class="a11yc_validation_error_str a11yc_level_'.$lv.'" data-level="'.$lv.'" data-place="'.Util::s($place['id']).'">'.Util::s($place['str']);

				if ($place['id'])
				{
					$ret['li'].= '<a href="#'.$anchor .'" class="a11yc_validation_error_link a11yc_level_'.$lv.' a11yc_hasicon"><span class="a11yc_icon_fa a11yc_icon_arrow_b" role="presentation" aria-hidden="true"></span>Code</a>';
				}
			$ret['li'].= '</li>';
			}

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
	 * @param Array|Bool  $current_err
	 * @param String $lv
	 * @param Integer $num_of_err
	 * @return Array
	 */
	private static function dt($url, $code_str, $place, $key, $current_err, $lv, $num_of_err)
	{
		if ( ! is_array($current_err)) Util::error('invalid value was given');

		$yml = Yaml::fetch();

		$anchor = $code_str.'_'.$key;

		$internal_link = '';
		if (isset($current_err['internal_link']))
		{
			$internal_link = ' [<a target="_blank" href="'.A11YC_URL.constant($current_err['internal_link']).Util::urlenc($url).'">'.A11YC_LANG_POST_SHOW_LIST_IMAGES.'</a>]';
		}

		// dt
		$ret = '<dt id="index_'.$anchor.'" tabindex="-1" class="a11yc_level_'.$lv.'">'.$current_err['message'].' ('.$num_of_err.')'.$internal_link;

		// dt - information
		foreach ($current_err['criterions'] as $each_criterion)
		{
			$criterion = $yml['criterions'][$each_criterion];

			$ret.= '<span class="a11yc_validation_reference_info"><strong>'.A11YC_LANG_LEVEL.strtoupper($lv).'</strong> <strong>'.Util::key2code($criterion['code']).'</strong> ';
			$ret.= '<a href="'.A11YC_DOC_URL.$each_criterion.'" target="a11yc_doc">'.A11YC_LANG_CHECKLIST_SEE_DETAIL.'('.Util::key2code($criterion['code']).')</a> ';

			if ( ! defined('A11YC_IS_GUEST_VALIDATION'))
			{
				$ret.= '[<a href="'.A11YC_ISSUE_URL.'add&amp;url='.$url.'&amp;criterion='.$each_criterion.'&amp;err_id='.$code_str.'&amp;src='.rawurlencode(Util::s($place['str'])).'" target="a11yc_issue">'.A11YC_LANG_ISSUE_ADD.'</a>] ';
			}

			$ret.= '</span>';
		}

		// dt - information tech
		$refs = Values::getRefUrls();

		if (isset($current_err['techs']))
		{
			foreach ($current_err['techs'] as $each_tech)
			{
				$tech = $yml['techs'][$each_tech];
				$ret.= '<span class="a11yc_validation_reference_info">';
				$ret.= '<a href="'.$refs[0]['t'].$each_tech.'.html">'.$tech['title'].'</a>';
				$ret.= '</span>';
			}
		}
		$ret.= '</dt><dd><ul>';

		return $ret;
	}
}
