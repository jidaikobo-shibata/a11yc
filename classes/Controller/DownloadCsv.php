<?php
/**
 * A11yc\Controller\DownloadCsv
 *
 * @package    part of A11yc
 * @author     Jidaikobo Inc.
 * @license    The MIT License (MIT)
 * @copyright  Jidaikobo Inc.
 * @link       http://www.jidaikobo.com
 */
namespace A11yc\Controller;

use A11yc\Model;
use A11yc\Validate;

trait DownloadCsv
{
	/**
	 * csv
	 *
	 * @return Void
	 */
	public static function csv()
	{
		$csv = static::generateCsv();

		// output
		ob_start();
		$fp = fopen('php://output', 'w');
		if ($fp === FALSE) throw new Exception('failed to export');

		foreach ($csv as $fields)
		{
			mb_convert_variables('SJIS', 'UTF-8', $fields);
			fputcsv($fp, $fields, "\t");
		}
		fclose($fp);

		$buffer = ob_get_contents();
		ob_end_clean();

		File::download('a11yc.csv', $buffer);
	}

	/**
	 * generateCsv
	 *
	 * @return Array
	 */
	public static function generateCsv()
	{
		$pages = Model\Page::fetchAll();
		$csv   = array();
		$csv[] = self::addTitleLine($pages);
		$csv   = self::addCriterionLines($csv, $pages);
		$csv[] = array();
		$csv   = self::addIclLines($csv, $pages);
		return $csv;
	}

	/**
	 * addTitleLine
	 *
	 * @param Array $pages
	 * @return Array
	 */
	private static function addTitleLine($pages)
	{
		$titles = array('');
		foreach ($pages as $k => $v)
		{
			$pages[$k]['results'] = Model\Result::fetch($v['url']);
			$pages[$k]['cs']      = Model\Checklist::fetch($v['url']);
			$pages[$k]['iclchks'] = Model\Iclchk::fetch($v['url']);
			$titles[] = (sprintf("%02d", $v['seq'])).': '.$v['title'];
		}
		return $titles;
	}

	/**
	 * addCriterionLines
	 *
	 * @param Array $csv
	 * @param Array $pages
	 * @return Array
	 */
	private static function addCriterionLines($csv, $pages)
	{
		$resultspts = Values::resultsOptions();
		$criterions = Yaml::each('criterions');
		foreach ($criterions as $criterion => $v)
		{
			$line = array(
				Util::key2code($criterion).' '.$v['name']
			);
			foreach ($pages as $vv)
			{
				$result = isset($vv['results'][$criterion]['result']) ? $vv['results'][$criterion]['result'] : 0;
				$line[] = $resultspts[$result];
			}
			$csv[] = $line;
		}
		return $csv;
	}

	/**
	 * addIclLines
	 *
	 * @param Array $csv
	 * @param Array $pages
	 * @return Array
	 */
	private static function addIclLines($csv, $pages)
	{
		$resultspts = Values::resultsOptions();
		$criterions = Yaml::each('criterions');
		$icls = Model\Icl::fetchAll();
		$icltree = Model\Icl::fetchTree();
		foreach ($icltree as $criterion => $parents)
		{
			$csv[] = array(Util::key2code($criterions[$criterion]['code']).' '.$criterions[$criterion]['name']);
			foreach ($parents as $pid => $ids)
			{
				if ($pid != 'none')
				{
					$csv[] =  array(Arr::get($icls[$pid], 'title_short', Arr::get($icls[$pid], 'title', '')));
				}
				foreach ($ids as $id){
					$line = array($icls[$id]['title_short']);
					foreach ($pages as $vv)
					{
						$result = isset($vv['iclchks'][$id]) ? $vv['iclchks'][$id] : 0;
						$line[] = $resultspts[$result];
					}
					$csv[] = $line;
				}
			}
		}
		return $csv;
	}
}
