<?php
/**
 * \JwpA11y\Issues
 *
 * @package    WordPress
 * @version    1.0
 * @author     Jidaikobo Inc.
 * @license    GPL
 * @copyright  Jidaikobo Inc.
 * @link       http://www.jidaikobo.com
 */
namespace JwpA11y;

class Issues extends \A11yc\Controller\Issues
{
	/**
	 * routing
	 *
	 * @return void
	 */
	public static function routing()
	{
		// users
		$userinfo = wp_get_current_user();

		// users
		$users = array();
		foreach (get_users() as $v)
		{
			$users[$v->data->ID] = esc_html($v->data->user_nicename);
		}
		$is_admin = current_user_can('administrator');
		$is_edit = false;

		// nonce
		if ($_POST)
		{
			if (
				! isset($_POST['jwp_a11y_nonce']) ||
				! wp_verify_nonce($_POST['jwp_a11y_nonce'], 'jwp_a11y_issues_action')
			)
			{
				print 'nonce check failed.';
				exit;
			}
		}

		switch (\A11yc\Input::get('a', ''))
		{
			case 'add':
				static::edit($is_add = true, $users, $userinfo->ID);
				$is_edit = true;
				break;
			case 'edit':
				static::edit($is_add = false, $users, $userinfo->ID);
				$is_edit = true;
				break;
			case 'view':
				static::view($users, $userinfo->ID, $is_admin);
				$is_edit = true;
				break;
			default:
				static::index();
				break;
		}

		$html = '';
		$html.= '<div class="wrap">';
		$html.= '<div id="icon-themes" class="icon32"><br /></div>';
		$html.= '<h1>'.\A11yc\View::fetch('title').'</h1>';
		$html.= '<div class="postbox" style="margin-top: 15px;">';
		$html.= '<div class="inside">';
		$html.= \A11yc\View::fetchTpl('messages.php');
		$html.= '<div id="a11yc_docs">';

		if ($is_edit)
		{
			$html.= '<form action="'.\A11yc\Util::uri().'" method="POST" class="a11yc">';

			$html.= \A11yc\View::fetch('form');
			$html.= '<div id="a11yc_submit">';

			$html.= wp_nonce_field('jwp_a11y_issues_action', 'jwp_a11y_nonce', true, false);

			$html.= '<input type="submit" value="'.A11YC_LANG_CTRL_SEND.'" class="button button-primary button-large" />';
			$html.= '</div>';
			$html.= '</form>';
		}
		else
		{
			$html.= \A11yc\View::fetch('body');
		}

		$html.= '</div><!--/#a11yc_docks-->';
		$html.= '</div><!--/.inside-->';
		$html.= '</div><!--/.postbox-->';
		$html.= '</div><!--/.wrap-->';
		echo $html;
	}
}
