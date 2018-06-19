<?php
/**
 * \JwpA11y\Center
 *
 * @package    WordPress
 * @version    1.0
 * @author     Jidaikobo Inc.
 * @license    GPL
 * @copyright  Jidaikobo Inc.
 * @link       http://www.jidaikobo.com
 */
namespace JwpA11y;

class Center extends \A11yc\Controller\Center
{
	/**
	 * Show A11y Center Index
	 *
	 * @return  void
	 */
	public static function index()
	{
		if (\A11yc\Input::get('a11yc_pages'))
		{
			\A11yc\Controller\Results::pages();
		}
		elseif ($url = \A11yc\Input::get('url'))
		{
			\A11yc\Controller\Results::each($url);
		}
		else
		{
			parent::index();
		}

		$html = '';
		$html.= '<div class="wrap">';
		$html.= '<div id="icon-themes" class="icon32"><br /></div>';
		$html.= '<h1>'.__('Accessibility Center', 'jwp_a11y').'</h1>';
		$html.= '<div class="postbox" style="margin-top: 15px;">';
		$html.= '<div class="inside">';
		$html.= \A11yc\View::fetchTpl('messages.php');
		$html.= \A11yc\View::fetch('body');
		$html.= '<h2>'.__("Shortcode", "jwp_a11y").'</h2>';
		$html.= '<p>'.__('To show accessibility information: <code>[jwp_a11y_results]</code>', "jwp_a11y").'</p>';

		$html.= '</div><!--/.inside-->';
		$html.= '</div><!--/.postbox-->';
		$html.= '</div><!--/.wrap-->';
		echo $html;
	}
}
