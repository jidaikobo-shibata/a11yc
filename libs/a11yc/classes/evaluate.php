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

class Evaluate
{
	/**
	 * fetch results from db
	 *
	 * @param  String $url
	 * @return String
	 */
	public static function fetch_results($url)
	{
		$sql = 'SELECT * FROM '.A11YC_TABLE_CHECKS.' WHERE `url` = ?;';
		$cs = array();
		foreach (Db::fetch_all($sql, array($url)) as $v)
		{
			$cs[$v['code']]['memo'] = $v['memo'];
			$cs[$v['code']]['uid'] = $v['uid'];
			$cs[$v['code']]['passed'] = $v['passed'];
		}
		return $cs;
	}

	/**
	 * fetch NG results from db
	 *
	 * @param  String $url
	 * @return String
	 */
	public static function fetch_ngs($url)
	{
		$sql = 'SELECT * FROM '.A11YC_TABLE_CHECKS_NGS.' WHERE `url` = ?;';
		$ngs = array();
		foreach (Db::fetch_all($sql, array($url)) as $v)
		{
			if (Arr::get($v, 'memo') == '') continue;
			$ngs[$v['criterion']]['memo'] = Arr::get($v, 'memo');
			$ngs[$v['criterion']]['uid'] = $v['uid'];
		}
		return $ngs;
	}

	/**
	 * evaluate url
	 *
	 * @param  String $url
	 * @return Array array('1-1-1' => 0, '1-1-2' => 1 or 2 ....)
	 */
	public static function evaluate_url($url)
	{
		$cs = static::fetch_results($url);
		$ngs = static::fetch_ngs($url);
		$rets = static::do_evaluate($cs, $ngs);

		// passed and non_exist
		$results = array();
		foreach ($rets as $criterion => $ret)
		{
			$results[$criterion]['passed'] = ($ret >= 1);
			$results[$criterion]['non_exist'] = ($ret >= 2);
		}

		// memo
		$yml = Yaml::fetch();
		foreach ($yml['codes'] as $code => $criterion)
		{
			$results[$criterion] = Arr::get($results, $criterion, array());
			$results[$criterion]['memo'] = Arr::get($results, "{$criterion}.memo", '');
			if ( ! isset($cs[$code])) continue;
			$results[$criterion]['memo'].= Arr::get($cs, "{$code}.memo");
		}

		foreach (array_keys($yml['criterions']) as $criterion)
		{
			if ( ! isset($ngs[$criterion]['memo'])) continue;
			$results[$criterion]['memo'].= $ngs[$criterion]['memo'];
		}

		return $results;
	}

	/**
	 * evaluate total
	 *
	 * @return Array
	 */
	public static function evaluate_total()
	{
		$sql = 'SELECT * FROM '.A11YC_TABLE_PAGES.' WHERE `done` = 1 and `trash` = 0';
		$sql.= Controller_Setup::version_sql().';';
		$ps = Db::fetch_all($sql);

		// calculate percentage
		$results = array();
		$passes = array();
		$non_exists = array();
		$total = array();
		foreach ($ps as $p)
		{
			if (empty($p['alt_url']))
			{
				$url = $p['url'];
			}
			else
			{
				$url = $p['alt_url'];
			}

			foreach (static::evaluate_url($url) as $criterion => $result)
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
			$results[$criterion]['memo'] = $percentage.'%';
			$results[$criterion]['passed'] = ($percentage == 100);
			$results[$criterion]['non_exist'] = $non_exists[$criterion];
		}

		return $results;
	}

	/**
	 * passed
	 *
	 * @param  Array $cs
	 * @return Array $passed array('1-1-1' => array('1-1-1a', '1-1-b' ....))
	 */
	private static function passed($cs)
	{
		$yml = Yaml::fetch();
		$passed = array();

		foreach ($yml['passes'] as $code => $codes)
		{
			if ( ! isset($cs[$code]) || ! $cs[$code]['passed']) continue;
			foreach ($codes as $each_code)
			{
				$criterion = $yml['codes'][$each_code];
				$passed[$criterion] = Arr::get($passed, $criterion, array());
				$passed[$criterion] = array_merge($passed[$criterion], $yml['passes'][$each_code]);
			}
		}
		return array_map('array_unique', $passed);
	}

	/**
	 * do evaluate
	 * 0: Nonconformity
	 * 1: conformity
	 * 2: non exist and conformity
	 *
	 * @param  Array $cs
	 * @param  Array $ngs
	 * @return Array $results = array('1-1-1' => 0, '1-1-2' => 1 or 2 ....)
	 */
	private static function do_evaluate($cs, $ngs)
	{
		$yml = Yaml::fetch();

		// passed
		$passed = static::passed($cs);

		// for non exists
		$checked = array();
		foreach ($cs as $code => $v)
		{
			if ($v['passed'])
			{
				$checked[] = $code;
			}
		}

		// results
		$results = array();
		foreach ($yml['conditions'] as $criterion => $codes)
		{
			// check NG
			if ( ! Arr::get($passed, $criterion) || array_key_exists($criterion, $ngs))
			{
				$results[$criterion] = 0;
				continue;
			}

			// check criterion
			$results[$criterion] = array_diff($codes, $passed[$criterion]) ? 0 : 1;
		}

		// non exists
		foreach ($yml['non_exists'] as $code => $criterions)
		{
			if ( ! in_array($code, $checked)) continue;
			foreach ($criterions as $criterion)
			{
				if ($results[$criterion] == 0) continue;
				$results[$criterion] = 2;
			}
		}

		return $results;
	}

	/**
	 * check level url
	 *
	 * @param  String $url
	 * @return Integer
	 */
	public static function check_level_url($url)
	{
		$cs = static::fetch_results($url);
		$ngs = static::fetch_ngs($url);
		$yml = Yaml::fetch();

		$passed = array();
		foreach (static::passed($cs) as $criterion => $codes)
		{
			if (array_key_exists($criterion, $ngs)) continue;
			$passed = array_merge($passed, $codes);
		}

		// NGコメントがあれば合格しない
		// non-pass when ng comment exists
		foreach (array_keys($ngs) as $code)
		{
			foreach ($passed as $k => $v)
			{
				if (in_array($v, array_keys($yml['checks'][$code])))
				{
					unset($passed[$k]);
				}
			}
		}

		return self::check_level($passed);
	}

	/**
	 * check level
	 *
	 * @param  Array $results
	 * @return Integer
	 */
	private static function check_level($results)
	{
		$levels = self::fetch_levels();
		$dones = array();
		$ngs = array();
		foreach ($levels as $k => $v)
		{
			$ngs = array_merge($ngs, array_diff($v, $results)); // 達成できていない項目
			if (array_diff($v, $results)) continue;
			$dones[] = $k;
		}
		$ngs = array_unique($ngs);
		return count($dones);
	}

	/**
	 * fetch levels
	 *
	 * @return Array
	 */
	private static function fetch_levels()
	{
		$yml = Yaml::fetch();
		// levels
		$levels = array();
		foreach ($yml['levels'] as $v)
		{
			foreach ($yml['checks'] as $vv)
			{
				foreach ($vv as $code => $vvv)
				{
					if ($vvv['level']['name'] != $v['name']) continue;
					$levels[$v['name']][] = $code;
				}
			}
		}
		$levels['AA'] = array_merge($levels['AA'], $levels['A']);
		$levels['AAA'] = array_merge($levels['AAA'], $levels['AA']);
		return $levels;
	}

	/**
	 * result string
	 *
	 * @param  Integer $level
	 * @param  Integer $target_level
	 * @param  Bool $is_str
	 * @return String
	 */
	public static function result_str($level, $target_level, $is_str = TRUE)
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
	 * @return String
	 */
	public static function check_site_level()
	{
		$sql = 'SELECT MIN(`level`) as min FROM '.A11YC_TABLE_PAGES.' WHERE `done` = 1';
		$sql.= Controller_Setup::version_sql();
		$sql.= ' AND `alt_url` = ""';
		$min = Db::fetch($sql);
		return is_null($min['min']) ? 0 : $min['min'];
	}

	/**
	 * check_alt_url_exception
	 *
	 * @return bool
	 */
	public static function check_alt_url_exception()
	{
		$sql = 'SELECT `level` FROM '.A11YC_TABLE_PAGES.' WHERE `done` = 1';
		$sql.= Controller_Setup::version_sql();
		$sql.= ' AND `alt_url` <> ""';
		return Db::fetch_all($sql) ? true : false;
	}

	/**
	 * pages of passed
	 *
	 * @param  String $target_level
	 * @return Array
	 */
	public static function passed_pages($target_level)
	{
		return Db::fetch_all('SELECT * FROM '.A11YC_TABLE_PAGES.' WHERE `level` >= '.$target_level.' and `done` = 1 and `trash` = 0;');
	}

	/**
	 * pages of unpassed
	 *
	 * @param  String $target_level
	 * @return Array
	 */
	public static function unpassed_pages($target_level)
	{
		return Db::fetch_all('SELECT * FROM '.A11YC_TABLE_PAGES.' WHERE `level` < '.$target_level.' and `done` = 1 and `trash` = 0;');
	}
}
