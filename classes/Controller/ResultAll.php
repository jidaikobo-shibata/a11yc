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
	 * @param Bool $is_center
	 * @return Void
	 */
	public static function all($is_center = false)
	{
		$settings = Model\Setting::fetchAll();
		$target_level = intval(Arr::get($settings, 'target_level'));

		static::assignLinks();
		self::assignLevels($target_level);

		View::assign('settings',          $settings);
		View::assign('target_level',      $target_level);
		View::assign('selection_reasons', Values::selectionReasons());
		View::assign('selected_methods',  Values::selectedMethods());
		View::assign('selected_method',   intval(Arr::get($settings, 'selected_method')));
		View::assign('done',              Model\Page::count('done'));
		View::assign('total',             Model\Page::count('all'));
		View::assign('standards',         Yaml::each('standards'));
		View::assign('is_center',         $is_center);
		View::assign('title',             A11YC_LANG_TEST_RESULT);

		// passed and unpassed pages
		View::assign('unpassed_pages', Model\Result::unpassedPages($target_level));
		View::assign('passed_pages',   Model\Result::passedPages($target_level));

		// assign result
		self::assignResults($target_level);

		// set body
		View::assign('body', View::fetchTpl('result/index.php'), false);
	}
}
