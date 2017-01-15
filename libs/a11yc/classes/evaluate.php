<?php
/**
 * A11yc\Evaluate
 *
 * @package    part of A11yc
 * @version    1.0
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
	 * @param   string     $url
	 * @return  string
	 */
	public static function fetch_results($url)
	{
		$sql = 'SELECT * FROM '.A11YC_TABLE_CHECKS.' WHERE `url` = '.Db::escape($url).';';
		$cs = array();
		foreach (Db::fetch_all($sql) as $v)
		{
			$cs[$v['code']]['memo'] = $v['memo'];
			$cs[$v['code']]['uid'] = $v['uid'];
			$cs[$v['code']]['passed'] = $v['passed'];
		}
		return $cs;
	}

	/**
	 * evaluate url
	 *
	 * @param   string     $url
	 * @return  array
	 */
	public static function evaluate_url($url)
	{
		$cs = static::fetch_results($url);
		return static::evaluate($cs);
	}

	/**
	 * pass condition from YAML
	 * which code made criterion passed
	 * ['1-1-1'] => array('1-1-1a', '1-1-1b'...)
	 *
	 * @return  array
	 */
	public static function pass_conditions()
	{
		static $pass_conds = array();
		if ($pass_conds) return $pass_conds;

		$yml = Yaml::fetch();
		foreach ($yml['checks'] as $v)
		{
			foreach ($v as $code => $vv)
			{
				foreach ($vv['pass'] as $criterion => $vvv)
				{
					$pass_conds[$criterion] = Arr::get($pass_conds, $criterion, array());
					$pass_conds[$criterion] = array_merge($pass_conds[$criterion], $vvv);
				}
			}
		}
		foreach ($pass_conds as $k => $v)
		{
			$pass_conds[$k] = array_unique($v);
		}
		return $pass_conds;
	}

	/**
	 * evaluate
	 *
	 * @param   string     $url
	 * @return  array
	 */
	public static function evaluate($cs)
	{
		$yml = Yaml::fetch();
		$memos = array();

		// prepare conditions and given value
		$checked = array();
		$passed = array();
		foreach ($yml['checks'] as $v)
		{
			foreach ($v as $code => $vv)
			{
				foreach ($vv['pass'] as $criterion => $vvv)
				{
					// passed
					$passed[$criterion] = Arr::get($passed, $criterion, array());
					if (isset($cs[$code]) && $cs[$code]['passed'])
					{
						$passed[$criterion] = array_merge($passed[$criterion], $vvv);
						$checked[] = $code;
					}

					// memos
					// do not add same memo
					if ( ! isset($memos[$criterion]))
					{
						$memos[$criterion] = '';
					}
					$memo = Arr::get($cs, $code.'.memo');
					if ($memo && mb_strpos($memos[$criterion], $memo) === false)
					{
						$memos[$criterion] = $memos[$criterion]."\n".$cs[$code]['memo'];
					}
				}
			}
		}
		$passed_flat = array();
		foreach ($passed as $k => $v)
		{
			$passed[$k] = array_unique($v);
			$passed_flat = array_merge($passed_flat, $v);
		}
		$passed_flat = array_unique($passed_flat);
		$checked = array_unique($checked);

		// pass condition
		$pass_conds = static::pass_conditions();

		// evaluate
		$results = array();
		$criterion_keys = array_keys($cs);
		foreach ($yml['checks'] as $k => $v)
		{
			$results[$k] = array();
			$err = array_diff($pass_conds[$k], $passed[$k]);
			$results[$k]['pass'] = $err ? false : TRUE;
			$results[$k]['memo'] = Arr::get($memos,$k);
		}

		// non exist
		$non_existing = array();
		foreach ($yml['checks'] as $criterion => $v)
		{
			foreach ($v as $code => $vv)
			{
				if ( ! isset($vv['non-exist'])) continue;
				$non_existing[$code] = $vv['non-exist'];
			}
		}
		foreach ($checked as $v)
		{
			if ( ! array_key_exists($v, $non_existing)) continue;
			foreach ($non_existing[$v] as $each)
			{
				if ( ! array_key_exists($each, $results)) continue;
				$results[$each]['non_exist'] = TRUE;
			}
		}

		return array($results, $checked, $passed_flat);
	}

	/**
	 * evaluate total
	 *
	 * @return  array
	 */
	public static function evaluate_total()
	{
		$ps = Db::fetch_all('SELECT * FROM '.A11YC_TABLE_PAGES.' WHERE `done` = 1 and `trash` = 0;');
		$css = array();

		// calculate percentage
		$passes = array();
		$total = array();
		foreach ($ps as $k => $p)
		{
			$cs = Db::fetch_all(
				'SELECT * FROM '.A11YC_TABLE_CHECKS.' WHERE `url` = ?;',
				array($p['url']));

			foreach ($cs as $v)
			{
				$total[$v['code']] = isset($total[$v['code']]) ? $total[$v['code']] + 1 : 1;
				if ($v['passed'])
				{
					$passes[$v['code']] = isset($passes[$v['code']]) ? $passes[$v['code']] + 1 : 1;
				}
			}
		}

		// use memo to show percentage
		foreach ($total as $k => $v)
		{
			$css[$k] = array();
			$percentage = round($passes[$k] / $v, 3) * 100;
			$css[$k]['memo'] = $percentage.'%';
			$css[$k]['passed'] = ($percentage == 100);
		}

		return $css;
	}

	/**
	 * fetch levels
	 *
	 * @return  array()
	 */
	public static function fetch_levels()
	{
		$yml = Yaml::fetch();
		// levels
		$levels = array();
		foreach ($yml['levels'] as $k => $v)
		{
			foreach ($yml['checks'] as $criterion => $vv)
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
	 * check result
	 *
	 * @param   array      $passed_flat
	 * @return  int
	 */
	public static function check_result($passed_flat)
	{
		$levels = static::fetch_levels();
		$dones = array();
		$ngs = array();
		foreach ($levels as $k => $v)
		{
			$ngs = array_merge($ngs, array_diff($v, $passed_flat)); // 達成できていない項目
			if (array_diff($v, $passed_flat)) continue;
			$dones[] = $k;
		}
		$ngs = array_unique($ngs);

		return count($dones);
	}

	/**
	 * result string
	 *
	 * @param   int      $level
	 * @param   int      $target_level
	 * @return  string
	 */
	public static function result_str($level, $target_level)
	{
		$level = intval($level);
		if ($target_level == 2 && $level == 1)
		{
			return sprintf(A11YC_LANG_CHECKLIST_CONFORMANCE_PARTIAL, 'AA');
		}
		elseif ($target_level == 3 && $level == 2)
		{
			return sprintf(A11YC_LANG_CHECKLIST_CONFORMANCE_PARTIAL, 'AAA');
		}
		else
		{
			$ls = array(
				sprintf(A11YC_LANG_CHECKLIST_CONFORMANCE_PARTIAL, 'A'),
				sprintf(A11YC_LANG_CHECKLIST_CONFORMANCE, 'A'),
				sprintf(A11YC_LANG_CHECKLIST_CONFORMANCE, 'AA'),
				sprintf(A11YC_LANG_CHECKLIST_CONFORMANCE, 'AAA'),
			);
			return $ls[$level];
		}
	}

	/**
	 * check site level
	 *
	 * @param   string     $target_level
	 * @return  string
	 */
	public static function check_site_level()
	{
		$min = Db::fetch('SELECT MIN(`level`) as min FROM '.A11YC_TABLE_PAGES.' WHERE `done` = 1;');
		return $min['min'];
	}

	/**
	 * pages of passed
	 *
	 * @return  array
	 */
	public static function passed_pages($target_level)
	{
		return Db::fetch_all('SELECT * FROM '.A11YC_TABLE_PAGES.' WHERE `level` >= '.$target_level.' and `done` = 1 and `trash` = 0;');
	}

	/**
	 * pages of unpassed
	 *
	 * @return  array
	 */
	public static function unpassed_pages($target_level)
	{
		return Db::fetch_all('SELECT * FROM '.A11YC_TABLE_PAGES.' WHERE `level` < '.$target_level.' and `done` = 1 and `trash` = 0;');
	}
}
