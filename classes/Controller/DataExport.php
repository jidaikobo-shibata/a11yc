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

		if (Input::get('site') == 1)
		{
			$vals['base_url'] = Model\Data::baseUrl();
		}
		$vals['setting'] = Model\Setting::fetchAll();
		$vals['page'] = Model\Page::fetchAll();
		$vals['issue']  = Model\Issue::fetchAll();
		$vals['iclsit'] = Model\Icl::fetchAll('iclsit');
		$vals['icl'] = Model\Icl::fetchAll();
		foreach ($vals['page'] as $page)
		{
			$vals['result'][$page['url']] = Model\Result::fetch($page['url']);
			$vals['check'][$page['url']]  = Model\Checklist::fetch($page['url']);
			$vals['html'][$page['url']] = Model\Html::fetch($page['url'], '', true, true);
		}

		File::download('a11yc.export.json', json_encode($vals));
	}
}
