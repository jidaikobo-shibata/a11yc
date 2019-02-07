<?php
/**
 * A11yc\Controller\DataExport
 *
 * @package    part of A11yc
 * @author     Jidaikobo Inc.
 * @license    The MIT License (MIT)
 * @copyright  Jidaikobo Inc.
 * @link       http://www.jidaikobo.com
 */
namespace A11yc\Controller;

use A11yc\Model;

trait DataExport
{
	/**
	 * export
	 *
	 * @return Void
	 */
	public static function export()
	{
		$vals = array();
		$versions = array(0);
		if (Input::get('site') == 1)
		{
			$vals['version'] = Model\Version::fetchAll();
			$versions = array_merge($versions, array_keys($vals['version']));
			$vals['base_url'] = Model\Data::baseUrl();
		}
		$vals['version_keys'] = $versions;

		foreach ($versions as $version)
		{
			$vals[$version] = array();
			if (Input::get('site') == 1)
			{
				$vals[$version]['setting']  = Model\Setting::fetchAll();
				$vals[$version]['icl']      = Model\Icl::fetchAll();
			}
			$vals[$version]['page']  = Model\Page::fetchAll();
			$vals[$version]['issue'] = Model\Issue::fetchAll();
			foreach ($vals[$version]['page'] as $page)
			{
				$vals[$version]['result'][$page['url']] = Model\Result::fetch($page['url']);
				$vals[$version]['check'][$page['url']]  = Model\Checklist::fetch($page['url']);
				$vals[$version]['html'][$page['url']]   = Model\Html::fetch($page['url']);
			}
		}
		File::download('a11yc.export.json', json_encode($vals));
	}

	/**
	 * Icl
	 *
	 * @return Void
	 */
	public static function exportIcl()
	{
		$vals = array();
		$vals[0]['icl'] = Model\Icl::fetchAll();
		File::download('a11yc.export.icl.json', json_encode($vals));
	}
}
