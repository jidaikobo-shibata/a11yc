<?php
/**
 * A11yc\Controller\ResultAll
 *
 * @package    part of A11yc
 * @author     Jidaikobo Inc.
 * @license    The MIT License (MIT)
 * @copyright  Jidaikobo Inc.
 * @link       http://www.jidaikobo.com
 */
namespace A11yc\Controller;

use A11yc\Model;

trait ResultAll
{
	/**
	 * Show report
	 *
	 * @param String $base_url
	 * @param Bool $is_center
	 * @param Bool $is_download
	 * @return Void
	 */
	public static function all($base_url = '', $is_center = false, $is_download = false)
	{
		$settings = Model\Setting::fetchAll();
		$target_level = $settings['target_level'];
		static::assignLinks($base_url);
		self::assignLevels($target_level);

		View::assign('settings',     $settings);
		View::assign('target_level', $target_level);
		View::assign('done',         Model\Page::count('done'));
		View::assign('total',        Model\Page::count('all'));
		View::assign('is_center',    $is_center);
		View::assign('is_download',  $is_download);
		View::assign('is_assign',    false);
		View::assign('is_total',     true);
		View::assign('title',        A11YC_LANG_TEST_RESULT);

		// passed and unpassed pages
		View::assign('unpassed_pages', Model\Result::unpassedPages($target_level));
		View::assign('passed_pages',   Model\Result::passedPages($target_level));

		// assign result
		self::assignResults($target_level);

		// set body
		View::assign('body', View::fetchTpl('result/each.php'), false);
		View::assign('body_result', View::fetchTpl('result/each_criterions.php'), false);
	}
}
