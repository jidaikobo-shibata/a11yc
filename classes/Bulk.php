<?php
/**
 * \JwpA11y\Bulk
 *
 * @package    WordPress
 * @version    1.0
 * @author     Jidaikobo Inc.
 * @license    GPL
 * @copyright  Jidaikobo Inc.
 * @link       http://www.jidaikobo.com
 */
namespace JwpA11y;

class Bulk
{
	/**
	 * update all
	 *
	 * @return  void
	 */
	public static function checklist()
	{
		$html = '';
		$html.= '<div class="wrap">';
		$html.= '<div id="icon-themes" class="icon32"><br /></div>';
		$html.= '<h1>'.__("Bulk", "jwp_a11y").'</h1>';
		$html.= '<div class="postbox" style="margin-top: 15px;">';
		$html.= '<div class="inside">';
		$html.= \A11yc\View::fetchTpl('messages.php');

		$html.= '<div class="a11yc">';
		$userinfo = wp_get_current_user();
		if ( ! get_users()) die('no users exists');
		$users = array();
		foreach (get_users() as $v)
		{
			$users[$v->data->ID] = esc_html($v->data->user_nicename);
		}
		$html.= '<form action="'.\A11yc\Util::uri().'" method="POST">';

		\A11yc\Controller\Bulk::form('bulk', $users, $userinfo->ID);
		$html.= \A11yc\View::fetch('form');
		$html.= '<div id="a11yc_submit">';
		$html.= '<input type="submit" value="'.A11YC_LANG_CTRL_SEND.'" class="button button-primary button-large" />';
		$html.= '</div><!--/#a11yc_submit-->';
		$html.= '</form>';
		$html.= '</div><!--/.a11yc-->';

		$html.= '</div><!--/.inside-->';
		$html.= '</div><!--/.postbox-->';
		$html.= '</div><!--/.wrap-->';

		echo $html;
	}
}
