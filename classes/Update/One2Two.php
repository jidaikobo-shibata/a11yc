<?php
/**
 * A11yc\Update\One2Two
 *
 * @package    part of A11yc
 * @author     Jidaikobo Inc.
 * @license    The MIT License (MIT)
 * @copyright  Jidaikobo Inc.
 * @link       http://www.jidaikobo.com
 */
namespace A11yc\Update;

use A11yc\Model;

class One2Two
{
	/**
	 * update
	 *
	 * @return Void
	 */
	public static function update()
	{
		if ( ! Db::isTableExist(A11YC_TABLE_SETUP_OLD)) return;
		if (Db::isTableExist(A11YC_TABLE_CACHES)) return;

		// rename tables
		$sql = 'ALTER TABLE %s RENAME TO %s';

		// pages and maintenance
		$pages_old_tbl = A11YC_TABLE_PAGES_OLD.'_old';
		$checks_old_tbl = A11YC_TABLE_CHECKS_OLD.'_old';
		$maintenance_old_tbl = A11YC_TABLE_MAINTENANCE_OLD.'_old';
		Db::execute(sprintf($sql, A11YC_TABLE_PAGES_OLD, $pages_old_tbl));
		Db::execute(sprintf($sql, A11YC_TABLE_CHECKS_OLD, $checks_old_tbl));
		Db::execute(sprintf($sql, A11YC_TABLE_MAINTENANCE_OLD, $maintenance_old_tbl));

		// init
		Db::initTable();

		// migrate settings
		self::settings();

		// migrate pages
		self::pages($pages_old_tbl, $checks_old_tbl);
	}

	/**
	 * settings
	 *
	 * @return Void
	 */
	private static function settings()
	{
		$sql = 'SELECT * FROM '.A11YC_TABLE_SETUP_OLD.';';
		$settings = Db::fetchAll($sql);
		if (empty($settings[0])) return;

		$sql = 'DELETE FROM '.A11YC_TABLE_SETTINGS.' WHERE 1 = 1;';
		Db::execute($sql);

		$sql = 'INSERT INTO '.A11YC_TABLE_SETTINGS;
		$sql.= ' (`target_level`, ';
		$sql.= '`standard`, ';
		$sql.= '`selected_method`, ';
		$sql.= '`declare_date`, ';
		$sql.= '`test_period`, ';
		$sql.= '`dependencies`, ';
		$sql.= '`contact`, ';
		$sql.= '`policy`, ';
		$sql.= '`report`, ';
		$sql.= '`additional_criterions`, ';
		$sql.= '`base_url`,';
		$sql.= '`basic_user`, ';
		$sql.= '`basic_pass`, ';
		$sql.= '`checklist_behaviour`,';
		$sql.= '`stop_guzzle`,';
		$sql.= '`version`)';
		$sql.= ' VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)';

		Db::execute($sql, array(
				Arr::get($settings[0], 'target_level'),
				Arr::get($settings[0], 'standard'),
				Arr::get($settings[0], 'selected_method'),
				Arr::get($settings[0], 'declare_date'),
				Arr::get($settings[0], 'test_period'),
				Arr::get($settings[0], 'dependencies'),
				Arr::get($settings[0], 'contact'),
				Arr::get($settings[0], 'policy'),
				Arr::get($settings[0], 'report'),
				Arr::get($settings[0], 'additional_criterions'),
				Arr::get($settings[0], 'base_url', ''),
				'',
				'',
				Arr::get($settings[0], 'checklist_behaviour'),
				Arr::get($settings[0], 'stop_guzzle'),
				0
			));
	}

	/**
	 * pages
	 *
	 * @param String $pages_old_tbl
	 * @param String $checks_old_tbl
	 * @return Void
	 */
	private static function pages($pages_old_tbl, $checks_old_tbl)
	{
		// migrate pages
		self::migratePages($pages_old_tbl);

		// yaml
		$yml = self::yml();

		// migrate results
		$sql   = 'SELECT * FROM '.A11YC_TABLE_PAGES.';';
		$pages = Db::fetchAll($sql);
		foreach ($pages as $page)
		{
			$url = $page['url'];

			// checks
			$sql     = 'SELECT * FROM '.$checks_old_tbl.' WHERE `url` = ?;';
			$chks_db = Db::fetchAll($sql, array($url));
			$chks = array();
			foreach ($chks_db as $chk)
			{
				$chks[$chk['code']]['memo']   = $chk['memo'];
				$chks[$chk['code']]['uid']    = $chk['uid'];
				$chks[$chk['code']]['passed'] = 1;
			}

			// this check list item is special
			if ( ! isset($chks['1-1-1m']) && ! isset($chks['1-1-1n']))
			{
				$chks['1-1-1m']['memo']   = '';
				$chks['1-1-1m']['uid']    = 0;
				$chks['1-1-1m']['passed'] = 1;
			}

			// ngs
			$sql = 'SELECT * FROM '.A11YC_TABLE_CHECKS_NGS_OLD.' WHERE `url` = ?;';
			$ngs_db = Db::fetchAll($sql, array($url));
			$ngs = array();
			foreach ($ngs_db as $ng)
			{
				if (Arr::get($ng, 'memo') == '') continue;
				if ( ! isset($ng['code'])) continue;
				$ngs[$ng['code']]['memo']   = $ng['memo'];
				$ngs[$ng['code']]['uid']    = $ng['uid'];
			}

			// result
			$results_vals = self::doEvaluate($chks, $ngs, $yml);
			$results = array();

			foreach ($results_vals as $criterion => $v)
			{
				$memo = '';
				$uid = '';
				foreach ($yml['conditions'] as $codes)
				{
					foreach ($codes as $code)
					{
						$memo.= Arr::get($chks, "{$code}.memo", '');
						$uid = Arr::get($chks, "{$code}.uid", '');
					}
				}

				$results[$criterion]['result'] = $v;
				$results[$criterion]['method'] = 0;
				$results[$criterion]['uid']    = $uid;
				$results[$criterion]['memo']   = $memo;
			}

			Model\Result::update($url, $results);
		}
	}

	/**
	 * migratePages
	 *
	 * @param String $pages_old_tbl
	 * @return Void
	 */
	private static function migratePages($pages_old_tbl)
	{
		$sql = 'SELECT * FROM '.$pages_old_tbl.';';
		$pages = Db::fetchAll($sql);

		$sql = 'DELETE FROM '.A11YC_TABLE_PAGES.' WHERE 1 = 1;';
		Db::execute($sql);

		foreach ($pages as $page)
		{
			$sql = 'INSERT INTO '.A11YC_TABLE_PAGES.' (';
			$sql.= '`url`, ';
			$sql.= '`alt_url`, ';
			$sql.= '`type`, ';
			$sql.= '`title`, ';
			$sql.= '`level`, ';
			$sql.= '`standard`, ';
			$sql.= '`selection_reason`, ';
			$sql.= '`done`, ';
			$sql.= '`trash`, ';
			$sql.= '`date`, ';
			$sql.= '`created_at`, ';
			$sql.= '`updated_at`, ';
			$sql.= '`version`';
			$sql.= ') VALUES ';
			$sql.= '(?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 0);';

			Db::execute($sql, array(
					is_null($page['url'])              ? '' : Arr::get($page, 'url', ''),
					is_null($page['alt_url'])          ? '' : Arr::get($page, 'alt_url', ''),
					1,
					is_null($page['page_title'])       ? '' : Arr::get($page, 'page_title', ''),
					is_null($page['level'])            ? '' : Arr::get($page, 'level', ''),
					is_null($page['standard'])         ? '' : Arr::get($page, 'standard', ''),
					is_null($page['selection_reason']) ? '' : Arr::get($page, 'selection_reason', ''),
					is_null($page['done'])             ? '' : Arr::get($page, 'done', ''),
					is_null($page['trash'])            ? '' : Arr::get($page, 'trash', ''),
					is_null($page['date'])             ? '' : Arr::get($page, 'date', ''),
					is_null($page['add_date'])         ? '' : Arr::get($page, 'add_date', ''),
					date('Y-m-d H:i:s')
				)
			);
		}
	}

	/**
	 * yml
	 *
	 * @return Array
	 * codes, passes, conditions and non_exists
	 * codes: array('1-1-1a' => '1-1-1', ... '2-1-1a' => '2-1-1', )
	 * passes: array('1-1-1a' => array('1-1-1a', '1-1-1b', ...))
	 * conditions: array('1-1-1' => array('1-1-1a', '1-1-1b',... '1-2-1a'...))
	 * non_exists: array('1-1-1a' => '1-1-1', ...)
	 */
	private static function yml()
	{
		include A11YC_LIB_PATH.'/spyc/Spyc.php';
		if ( ! class_exists('Spyc')) Util::error('Spyc is not found');
		$chekcs_text = file_get_contents(__DIR__.'/resources/old_checks.yml');
		$yml = \Spyc::YAMLLoadString($chekcs_text);

		$ret = array();
		$ret['codes'] = array();
		$ret['passes'] = array();
		$ret['conditions'] = array();
		foreach ($yml['checks'] as $v)
		{
			foreach ($v as $code => $vv)
			{
				// codes
				$ret['codes'][$code] = $vv['criterion'];

				// passes and conditions
				if (isset($vv['pass']))
				{
					foreach ($vv['pass'] as $criterion => $vvv)
					{
						// passes
						$ret['passes'][$code] = Arr::get($ret, "passes.{$code}", array());
						$ret['passes'][$code] = array_merge($ret['passes'][$code], $vvv);

						// conditions
						$ret['conditions'][$criterion] = Arr::get($ret, "conditions.{$criterion}", array());
						$ret['conditions'][$criterion] = array_merge($ret['conditions'][$criterion], $vvv);
					}
				}

				// non exist
				if ( ! isset($vv['non-exist'])) continue;
				$ret['non_exists'][$code] = $vv['non-exist'];
			}
		}
		$ret['conditions'] = array_map('array_unique', $ret['conditions']);
		return $ret;
	}

	/**
	 * passed
	 *
	 * @param Array $cs
	 * @param Array $yml
	 * @return Array array('1-1-1' => array('1-1-1a', '1-1-b' ....))
	 */
	private static function passed($cs, $yml)
	{
		$passed = array();
		foreach ($yml['passes'] as $code => $codes)
		{

			if ( ! isset($cs[$code]) || ! $cs[$code]['passed']) continue;

			foreach ($codes as $each_code)
			{
				$criterion = $yml['codes'][$each_code];

				$passed[$criterion] = Arr::get($passed, $criterion, array());
//				$passed[$criterion] = array_merge($passed[$criterion], $yml['passes'][$each_code]);
				$passed[$criterion] = array_merge($passed[$criterion], $yml['passes'][$code]);
			}
		}

		return array_map('array_unique', $passed);
	}

	/**
	 * do evaluate
	 * -1: Nonconformity
	 * 1: non exist and conformity
	 * 2: conformity
	 *
	 * @param Array $cs
	 * @param Array $ngs
	 * @param Array $yml
	 * @return Array $results = array('1-1-1' => 0, '1-1-2' => 1 or 2 ....)
	 */
	private static function doEvaluate($cs, $ngs, $yml)
	{
		// passed
		$passed = self::passed($cs, $yml);

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
		foreach ($yml['conditions'] as $criterion => $conditions)
		{
			// check NG
			if ( ! Arr::get($passed, $criterion) || array_key_exists($criterion, $ngs))
			{
				$results[$criterion] = -1;
				continue;
			}

			// check criterion
			$results[$criterion] = array_diff($conditions, $passed[$criterion]) ? -1 : 2;
		}

		// non exists
		foreach ($yml['non_exists'] as $code => $criterions)
		{
			if ( ! in_array($code, $checked)) continue;

			foreach ($criterions as $criterion)
			{
				if ($results[$criterion] == -1) continue;
				$results[$criterion] = 1;
			}
		}

		return $results;
	}

}
