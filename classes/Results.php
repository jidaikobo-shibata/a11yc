<?php
/**
 * \JwpA11y\Results
 *
 * @package    WordPress
 * @version    1.0
 * @author     Jidaikobo Inc.
 * @license    GPL
 * @copyright  Jidaikobo Inc.
 * @link       http://www.jidaikobo.com
 */
namespace JwpA11y;

class Results extends \A11yc\Controller\Results
{

	/**
	 * shortcode for disclosure page
	 *
	 * @param   array  $attrs
	 * @param   string $content
	 * @return  string
	 */
	public static function disclosure($attrs, $content = null)
	{
		$setup = \A11yc\Controller\Results::index();
		return \A11yc\View::fetch('body');
	}
}
