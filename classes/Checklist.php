<?php
/**
 * \JwpA11y\Checklist
 *
 * @package    WordPress
 * @version    1.0
 * @author     Jidaikobo Inc.
 * @license    GPL
 * @copyright  Jidaikobo Inc.
 * @link       http://www.jidaikobo.com
 */
namespace JwpA11y;

class Checklist extends \A11yc\Controller\Checklist
{
	/**
	 * Check Target Page.
	 *
	 * @return Void
	 */
	public static function checklist()
	{
		$url = \A11yc\Util::enuniqueUri(\A11yc\Input::param('url', ''));

		// page existence
		if ( ! \A11yc\Crawl::isPageExist($url))
		{
			\A11yc\Session::add('messages', 'errors', A11YC_LANG_CHECKLIST_PAGE_NOT_FOUND_ERR);
		}

		// nonce
		if (\A11yc\Input::isPostExists())
		{
			$nonce = \A11yc\Input::post('jwp_a11y_nonce', false);
			if ( ! $nonce || ! wp_verify_nonce($nonce, 'jwp_a11y_checklist_action'))
			{
				print 'nonce check failed.';
				exit;
			}
		}

		// prepare html
		$html = '';
		$html.= '<div class="wrap">';
		$html.= '<div id="icon-themes" class="icon32"><br /></div>';
		$html.= '<h1>'.self::pageTitleByAction().'</h1>';
		$html.= '<div class="postbox" style="margin-top: 15px;">';
		$html.= '<div class="inside a11yc">';

		$close = '';
		$close.= '</div><!--/.inside-->';
		$close.= '</div><!--/.postbox-->';
		$close.= '</div><!--/.wrap-->';

		// action
		switch (\A11yc\Input::get('a'))
		{
			case 'each':
				\A11yc\Controller\Results::each($url);
				$html.= \A11yc\View::fetch('body');
				echo $html.$close;
				break;

			case 'images':
				\A11yc\Controller\Images::view($url);
				$html.= \A11yc\View::fetch('body');
				echo $html.$close;
				break;

			case 'csv':
				ob_end_clean();
				\A11yc\Controller\Export::csv($url);
				break;

			case 'view':
				ob_end_clean();
				\A11yc\Controller\Live::view($url);
				echo \A11yc\View::fetch('body');
				break;

			default:
				self::checklistBody($url, $html, $close);
				break;
		}
	}

	/**
	 * page title
	 *
	 * @return String
	 */
	public static function pageTitleByAction()
	{
		// action
		switch (\A11yc\Input::get('a'))
		{
			case 'each':
				return A11YC_LANG_CHECKLIST_TITLE;
			case 'images':
				return A11YC_LANG_IMAGES_TITLE;
			case 'csv':
				return A11YC_LANG_EXPORT_ERRORS_CSV;
			case 'view':
				return A11YC_LANG_PAGES_LIVE;
			default:
				return A11YC_LANG_CHECKLIST_TITLE;
		}
	}

	/**
	 * checklist
	 *
	 * @param String $url
	 * @param String $html
	 * @param String $close
	 * @return Void
	 */
	private static function checklistBody($url, $html, $close)
	{
		// vals
		$userinfo = wp_get_current_user();

		// users
		if ( ! get_users()) die('no users exists');
		$users = array();
		foreach (get_users() as $v)
		{
			$users[$v->data->ID] = esc_html($v->data->user_nicename);
		}
		parent::form($url, $users, $userinfo->ID);

		$page = \A11yc\View::fetch('page');

		// form
		$html.= \A11yc\View::fetchTpl('messages.php');
		$html.= '<form action="'.A11YC_CHECKLIST_URL.\A11yc\Util::urlenc($url).'" method="POST">';
		$html.= \A11yc\View::fetch('form');
		$html.= '<div id="a11yc_submit">';
		// is done
		if ($url != 'bulk')
		{
			$alt_url = isset($page['alt_url']) ? $page['alt_url'] : '';
			$html.= '<label for="a11yc_alt_url">'.A11YC_LANG_CHECKLIST_ALT_URL.'</label> <input type="text" name="alt_url" id="a11yc_alt_url" value="'.\A11yc\Util::s(\A11yc\Util::urldec($alt_url)).'" /> ';

			$html.= '<label for="a11yc_do_css_check"><input type="checkbox" name="do_css_check" id="a11yc_do_css_check" value="1" />'.A11YC_LANG_CHECKLIST_DO_CSS_CHECK.'</label>';


			$html.= '<label for="a11yc_do_link_check"><input type="checkbox" name="do_link_check" id="a11yc_do_link_check" value="1" />'.A11YC_LANG_CHECKLIST_DO_LINK_CHECK.'</label>';

			$checked = @$page['done'] ? ' checked="checked"' : '';
			$html.= '<label for="a11yc_done"><input type="checkbox" name="done" id="a11yc_done" value="1"'.$checked.'/>'.A11YC_LANG_CHECKLIST_DONE.'</label>';
		}

		$html.= wp_nonce_field('jwp_a11y_checklist_action', 'jwp_a11y_nonce', true, false);

		$html.= '<input type="submit" value="'.A11YC_LANG_CTRL_SEND.'" class="button button-primary button-large" />';
		$html.= '</div>';

		$html.= '</form>';
		$html.= $close;

		echo $html;
	}
}
