<?php
/**
 * A11yc\Controller\Image
 *
 * @package    part of A11yc
 * @author     Jidaikobo Inc.
 * @license    The MIT License (MIT)
 * @copyright  Jidaikobo Inc.
 * @link       http://www.jidaikobo.com
 */
namespace A11yc\Controller;

class Image
{
	/**
	 * action view
	 *
	 * @return Void
	 */
	public static function actionView()
	{
		$url = Util::enuniqueUri(Input::param('url', ''));
		static::view($url);
	}

	/**
	 * view
	 *
	 * @param String $url
	 * @return Void
	 */
	public static function view($url)
	{
		View::assign('images', \A11yc\Image::getImages($url));
		View::assign('title',  'Image List');
		View::assign('body',   View::fetchTpl('checklist/images.php'), false);
	}
}
