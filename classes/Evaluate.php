<?php
/**
 * A11yc\Evaluate
 *
 * @package    part of A11yc
 * @author     Jidaikobo Inc.
 * @license    The MIT License (MIT)
 * @copyright  Jidaikobo Inc.
 * @link       http://www.jidaikobo.com
 */
namespace A11yc;

use A11yc\Model;

class Evaluate
{
	/**
	 * check level url
	 *
	 * @param String $url
	 * @return Integer [-1 Non-Interferences, 0 A-, 1 A, 2 AA, 3 AAA]
	 */
	public static function getLevelByUrl($url)
	{
		$results = Model\Result::fetchPassed($url);
		$levels = Util::criterionsOfLevels();

		// Non-Interferences
		if (array_diff(Yaml::nonInterferences(), $results)) return -1;

		// level check
		$current_level = 0;
		foreach ($levels as $level => $criterions)
		{
			if ( ! array_diff($criterions, $results))
			{
				$current_level = strlen($level);
			}
		}

		return $current_level;
	}

	/**
	 * result string
	 *
	 * @param Integer $level
	 * @param Integer $target_level
	 * @param Bool $is_str
	 * @return String
	 */
	public static function resultStr($level, $target_level, $is_str = TRUE)
	{
		$level = intval($level);
		if ($target_level == 2 && $level == 1)
		{
			return $is_str ?
				sprintf(A11YC_LANG_CHECKLIST_CONFORMANCE_PARTIAL, 'AA') :
				'AA-';
		}
		elseif ($target_level == 3 && $level == 2)
		{
			return $is_str ?
				sprintf(A11YC_LANG_CHECKLIST_CONFORMANCE_PARTIAL, 'AAA') :
				'AAA-';
		}
		elseif ($level == -1)
		{
			return $is_str ?
				A11YC_LANG_CHECKLIST_CONFORMANCE_FAILED :
				'A--';
		}
		else
		{
			$ls =  $is_str ?
					array(
						sprintf(A11YC_LANG_CHECKLIST_CONFORMANCE_PARTIAL, 'A'),
						sprintf(A11YC_LANG_CHECKLIST_CONFORMANCE, 'A'),
						sprintf(A11YC_LANG_CHECKLIST_CONFORMANCE, 'AA'),
						sprintf(A11YC_LANG_CHECKLIST_CONFORMANCE, 'AAA'),
					) :
					array(
						'A-',
						'A',
						'AA',
						'AAA',
					);
			return $ls[$level];
		}
	}

	/**
	 * check site level
	 *
	 * @return Integer
	 */
	public static function checkSiteLevel()
	{
		$levels = array();
		foreach (Model\Page::fetchAll(array('list' => 'done')) as $page)
		{
			if ( ! empty($page['alt_url'])) continue;
			$levels[] = intval(Arr::get($page, 'level', 0));
		}
		return empty($levels) ? 0 : min($levels);
	}

	/**
	 * check alt url exception
	 *
	 * @return bool
	 */
	public static function checkAltUrlException()
	{
		$alt_pages = array();
		foreach (Model\Page::fetchAll(array('list' => 'done')) as $page)
		{
			if (empty($page['alt_url'])) continue;
			$alt_pages[] = $page;
		}
		return empty($alt_pages) ? false : true;
	}

	/**
	 * evaluate url
	 *
	 * @param String $url
	 * @return Array array('1-1-1' => 0, '1-1-2' => 1 or 2 ....)
	 */
	public static function evaluateUrl($url)
	{
		$rets = Model\Result::fetch($url);

		// passed and non_exist
		$results = array();
		foreach ($rets as $criterion => $ret)
		{
			if ( ! isset($ret['result'])) continue;
			$results[$criterion]['passed'] = ($ret['result'] >= 1);
			$results[$criterion]['non_exist'] = ($ret['result'] == 1);
			$results[$criterion]['memo'] = $ret['memo'];
		}

		return $results;
	}

	/**
	 * evaluate total
	 *
	 * @return Array
	 */
	public static function evaluateTotal()
	{
		// fetch pages
		$args = array(
			'list' => 'done',
		);
		$pages = Model\Page::fetchAll($args);

		// calculate percentage
		$results = array();
		$passes = array();
		$non_exists = array();
		$total = array();

		foreach ($pages as $p)
		{
			$url = Arr::get($p, 'alt_url') ?: Arr::get($p, 'url');

			foreach (static::evaluateUrl($url) as $criterion => $result)
			{
				// initialize
				$total[$criterion] = Arr::get($total, $criterion, 0);
				$passes[$criterion] = Arr::get($passes, $criterion, 0);

				// count
				$total[$criterion]++;
				$passes[$criterion] += $result['passed'] >= 1 ? 1 : 0;

				// non_exists
				if (Arr::get($non_exists, $criterion) === false) continue;
				$non_exists[$criterion] = $result['non_exist'];
			}
		}

		// use memo to show percentage
		foreach ($total as $criterion => $num)
		{
			$percentage = round($passes[$criterion] / $num, 3) * 100;
//			$results[$criterion]['memo'] = $passes[$criterion].'/'.$num.' ('.$percentage.'%)';
			$results[$criterion]['memo'] = $passes[$criterion].'/'.$num;
			$results[$criterion]['passed'] = ($percentage == 100);
			$results[$criterion]['non_exist'] = $non_exists[$criterion];
		}

		return $results;
	}
}
