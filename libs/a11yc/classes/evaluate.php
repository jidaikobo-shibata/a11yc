<?php
/**
 * A11yc\Evaluate
 *
 * @package    part of A11yc
 * @version    1.0
 * @author     Jidaikobo Inc.
 * @license    WTFPL2.0
 * @copyright  Jidaikobo Inc.
 * @link       http:/www.jidaikobo.com
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
		}
		return $cs;
	}

	/**
	 * evaluate results
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
	 * evaluate results
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
		$pass_conds = array();
		$pass_codes = array();
		foreach ($yml['checks'] as $k => $v)
		{
			$memos[$k] = '';
			foreach ($v as $kk => $vv)
			{
				foreach ($vv['pass'] as $kkk => $vvv)
				{
					// pass_conds
					$pass_conds[$kkk] = isset($pass_conds[$kkk]) ? $pass_conds[$kkk] : array();
					$pass_conds[$kkk] = array_merge($pass_conds[$kkk], $vvv);

					// pass_codes
					$pass_codes[$kkk] = isset($pass_codes[$kkk]) ? $pass_codes[$kkk] : array();
					if (isset($cs[$kk]))
					{
						$pass_codes[$kkk] = array_merge($pass_codes[$kkk], $vvv);
						$checked[] = $kk;
					}
					if (
						isset($cs[$kk]['memo']) &&
						! empty($cs[$kk]['memo']) &&
						mb_strpos($memos[$k], $cs[$kk]['memo']) === false)
					{
						$memos[$k] = $memos[$k]."\n".$cs[$kk]['memo'];
					}
				}
			}
		}

		foreach ($pass_conds as $k => $v)
		{
			$pass_conds[$k] = array_unique($v);
		}
		$passed_flat = array();
		foreach ($pass_codes as $k => $v)
		{
			$pass_codes[$k] = array_unique($v);
			$passed_flat = array_merge($passed_flat, $v);
		}
		$passed_flat = array_unique($passed_flat);
		$checked = array_unique($checked);

		// evaluate
		$results = array();
		$criterion_keys = array_keys($cs);
		foreach ($yml['checks'] as $k => $v)
		{
			$results[$k] = array();
			$err = array_diff($pass_conds[$k], $pass_codes[$k]);
			$results[$k]['pass'] = $err ? false : TRUE;
			$results[$k]['memo'] = trim($memos[$k]);
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
	 * evaluate total
	 *
	 * @return  array
	 */
	public static function evaluate_total()
	{
		$ps = Db::fetch_all('SELECT * FROM '.A11YC_TABLE_PAGES.' WHERE `done` = 1;');
		$css = array();
		foreach ($ps as $k => $p)
		{
			$url = Db::escape($p['url']);
			$cs = Db::fetch_all('SELECT * FROM '.A11YC_TABLE_CHECKS.' WHERE `url` = '.$url.';');
			foreach ($cs as $v)
			{
				$css[$k][] = $v['code'];
			}
		}

		// intersects
		$intersects = array();
		foreach ($css as $v)
		{
			$exist = TRUE;
			foreach ($v as $vv)
			{
				foreach ($css as $vvv)
				{
					if ( ! in_array($vv, $vvv))
					{
						$exist = false;
						break;
					}
					if ($exist)
					{
						$intersects[] = $vv;
					}
				}
			}
		}
		return array_flip(array_unique($intersects));
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
