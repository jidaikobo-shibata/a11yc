<?php
/**
 * \JwpA11y\Settings
 *
 * @package    WordPress
 * @version    1.0
 * @author     Jidaikobo Inc.
 * @license    GPL
 * @copyright  Jidaikobo Inc.
 * @link       http://www.jidaikobo.com
 */
namespace JwpA11y;

class Settings extends \A11yc\Controller\Settings
{
	/**
	 * Set up basic information
	 *
	 * @return  void
	 */
	public static function front()
	{
		$action = \A11yc\Input::get('a');
		if (in_array($action, array('ua', 'versions')))
		{
			parent::$action();
		}
		else
		{
			parent::form();
		}

		if (\A11yc\Input::isPostExists())
		{
			$nonce = \A11yc\Input::post('jwp_a11y_nonce', false);
			if (
				! $nonce ||
				(
					! wp_verify_nonce($nonce, 'jwp_a11y_settings_action') &&
					! wp_verify_nonce($nonce, 'jwp_a11y_protect_action')
				)
			)
			{
				print 'nonce check failed.';
				exit;
			}
		}

		$html = '';
		$html.= '<div class="wrap">';
		$html.= '<div id="icon-themes" class="icon32"><br /></div>';
		$html.= '<h1>'.__("jwp-a11y Settings", "jwp_a11y").'</h1>';
		$html.= '<div class="postbox" style="margin-top: 15px;">';
		$html.= '<div class="inside">';
		$html.= \A11yc\View::fetchTpl('messages.php');
		$html.= \A11yc\View::fetch('submenu');

		$html.= '<form action="'.\A11yc\Util::uri().'" method="POST" class="a11yc">';
		if ($action == 'versions')
		{
			$html.= \A11yc\View::fetch('protect_form');
		}
		$html.= \A11yc\View::fetch('form');
		$html.= '<div id="a11yc_submit">';

		$html.= wp_nonce_field('jwp_a11y_settings_action', 'jwp_a11y_nonce', true, false);

		$html.= '<input type="submit" value="'.A11YC_LANG_CTRL_SEND.'" class="button button-primary button-large" />';
		$html.= '</div>';
		$html.= '</form>';

		$html.= '</div><!--/.inside-->';
		$html.= '</div><!--/.postbox-->';
		$html.= '</div><!--/.wrap-->';
		echo $html;
	}
}
