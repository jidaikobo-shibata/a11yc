<?php
/**
 * A11yc\Message\Plugin
 *
 * @package    part of A11yc
 * @author     Jidaikobo Inc.
 * @license    The MIT License (MIT)
 * @copyright  Jidaikobo Inc.
 * @link       http://www.jidaikobo.com
 */
namespace A11yc\Message;

class Plugin
{
	/**
	 * error
	 *
	 * @param Array $errors
	 * @param Bool $show_link_to_issue
	 * @return String
	 */
	public static function error($errors, $show_link_to_issue = false)
	{
		$html = '';
		$html.= '<section>';
		$html.= '<a href="#end_line_of_a11y_checklist" class="a11yc_skip">'.A11YC_LANG_PLUGIN_SKIP.'</a>';
		$html.= '<h1>'.A11YC_LANG_PLUGIN_TITLE.'</h1>';
		$html.= '<p>'.A11YC_LANG_PLUGIN_ERROR.'</p>';

		// count errors
		$yml = \A11yc\Yaml::fetch();
		$errs_cnts = array('a' => 0, 'aa' => 0, 'aaa' => 0);
		foreach ($errors as $message)
		{
			$code = $message['code_str'];
			if ( ! isset($yml['errors'][$code])) continue;
			$lv = strtolower($yml['criterions'][$yml['errors'][$code]['criterions'][0]]['level']['name']);
			$errs_cnts[$lv]++;
		}

		$errs_cnts = array_merge(array('total' => count($errors)), $errs_cnts);
		foreach ($errs_cnts as $lv => $errs_cnt)
		{
			$html.= '<span class="a11yc_errs_lv">'.strtoupper($lv).'</span> <span class="a11yc_errs_cnt">'.intval($errs_cnt).'</span> ';
		}

		$html.= '<dl id="a11yc_validation_errors" class="a11yc_hide_if_fixedheader">';
		$html = self::removeViewSrc($html, $errors, $show_link_to_issue);
		$html.= '</ul></dd>';
		$html.= '</dl>';
		$html.= '</section><a id="end_line_of_a11y_checklist" class="a11yc_skip" tabindex="-1">'.A11YC_LANG_PLUGIN_SKIP_TARGET.'</a>';
		return $html;
	}

	/**
	 * no error
	 *
	 * @param Array $no_errors
	 * @return String
	 */
	public static function noError($no_errors = array())
	{
		$html = '';
		// no error
		$html.= '<p>'.A11YC_LANG_CHECKLIST_NOT_FOUND_ERR.'</p>';
		if (isset($no_errors['no_dead_link']))
		{
			$html.= '<p>'.A11YC_LANG_PLUGIN_NODEADLINK.'</p>';
		}
		return $html;
	}

	/**
	 * notice
	 *
	 * @param Array $notices
	 * @param Bool $show_link_to_issue
	 * @return String
	 */
	public static function notice($notices, $show_link_to_issue = false)
	{
		$html = '';
		$html.= '<h2>'.A11YC_LANG_PLUGIN_NOTICE.'</h2>'."\n";
		$html.= '<dl id="a11yc_validation_notices" class="a11yc_hide_if_fixedheader">';
		$html = self::removeViewSrc($html, $notices, $show_link_to_issue);
		$html.= '</ul></dd>';
		$html.= '</dl>';
		return $html;
	}

	/**
	 * Remove "view source"
	 *
	 * @param string $html
	 * @param array $messages
	 * @param Bool $show_link_to_issue
	 * @return string
	 */
	private static function removeViewSrc($html, $messages, $show_link_to_issue)
	{
		foreach($messages as $k => $message)
		{
			if (isset($message['dt']))
			{
				$dt = Arr::get($message, 'dt');
				if ($show_link_to_issue === false)
				{
					$dt = preg_replace('/\[.+?\]/i', '', $dt);
				}
				$html.= $dt;
			}

			$html.= preg_replace('/\<a href="#.+?\<\/a\>/i', '', $message['li']);

			$next = $k + 1;
			if (isset($messages[$next]['dt']))
			{
				$html.= '</ul></dd>';
			}
		}

		return $html;
	}
}
