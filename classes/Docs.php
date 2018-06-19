<?php
/**
 * \JwpA11y\Docs
 *
 * @package    WordPress
 * @version    1.0
 * @author     Jidaikobo Inc.
 * @license    GPL
 * @copyright  Jidaikobo Inc.
 * @link       http://www.jidaikobo.com
 */
namespace JwpA11y;

class Docs extends \A11yc\Controller\Docs
{
	/**
	 * Show document or document Index
	 *
	 * @return  void
	 */
	public static function show()
	{
		$code = \A11yc\Input::get('code', '');
		$code ? parent::each($code) : parent::index();

		$html = '';
		$html.= '<div class="wrap">';
		$html.= '<div id="icon-themes" class="icon32"><br /></div>';
		$html.= '<h1>'.self::title().'</h1>';
		$html.= '<div class="postbox" style="margin-top: 15px;">';
		$html.= '<div class="inside">';
		$html.= \A11yc\View::fetchTpl('messages.php');
		$html.= '<div id="a11yc_docs">';

		$html.= \A11yc\View::fetch('body');

		$html.= '</div><!--/#a11yc_docks-->';
		$html.= '</div><!--/.inside-->';
		$html.= '</div><!--/.postbox-->';
		$html.= '</div><!--/.wrap-->';
		echo $html;
	}

	/**
	 * document page title
	 *
	 * @return String
	 */
	public static function title()
	{
		$yml = \A11yc\Yaml::fetch();
		$code = \A11yc\Input::get('code');
		$title = __('Documents', 'jwp_a11y');
		if ($code)
		{
			$type = strpos($code, 'doc') !== false ? 'tests' : 'criterions';
			$title.= ' '.$yml[$type][$code]['name'];
		}
		return $title;
	}
}
