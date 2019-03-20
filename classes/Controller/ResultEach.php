<?php
/**
 * A11yc\Controller\ResultEach
 *
 * @package    part of A11yc
 * @author     Jidaikobo Inc.
 * @license    The MIT License (MIT)
 * @copyright  Jidaikobo Inc.
 * @link       http://www.jidaikobo.com
 */
namespace A11yc\Controller;

use A11yc\Model;

trait ResultEach
{
	/**
	 * Show checklist Results
	 *
	 * @param Srting $url
	 * @param Srting $base_url
	 * @param Bool $is_assign
	 * @param Bool $is_center
	 * @return Bool
	 */
	public static function each($url, $base_url = '', $is_assign = false, $is_center = false)
	{
		$page = Model\Page::fetch($url);

		if ( ! $page || ! $page['done'] || $page['trash'])
		{
			if ($is_assign) return false;
			header("HTTP/1.0 404 Not Found");
			echo '404 Not Found';
			exit();
		}

		$settings = Model\Setting::fetchAll();
		static::assignLinks($base_url);
		self::assignLevels($settings['target_level'], $page['level']);

		// alt checklist link
		if ( ! empty($page['alt_url']))
		{
			$chk = Util::remove_query_strings(Util::uri(), array('url', 'a11yc_page'));
			$chk = Util::add_query_strings(
				$chk,
				array(array('url', Util::urlenc($page['alt_url'])))
			);
			View::assign(
				'alt_results',
				' ('.sprintf(A11YC_LANG_ALT_URL_LEVEL, $chk).': '.Evaluate::result_str(Evaluate::getLevelByUrl($page['alt_url']), $settings['target_level']).')'
			);
		}
		else
		{
			View::assign('alt_results', '');
		}

		View::assign('page',        $page);
		View::assign('settings',    $settings);
		View::assign('is_center',   $is_center);
		View::assign('is_assign',   $is_assign);
		View::assign('is_total',    false);
		View::assign('is_download', false);
		View::assign('title',       A11YC_LANG_TEST_RESULT.': '.Arr::get($page, 'serial_num').': '.Model\Html::pageTitle($url));

		// assign results
		self::assignResults($settings['target_level'], $url);

		// assign checklist
		View::assign('cs', Model\Checklist::fetch($url));
		View::assign('iclchks', Model\Iclchk::fetch($url));

		// set body
		View::assign('body', View::fetchTpl('result/each.php'), false);

		// set body for download
		View::assign('download_icl', View::fetchTpl('result/each_implements.php'), false);
		View::assign('download_criterion', View::fetchTpl('result/each_criterions.php'), false);

		return true;
	}
}
