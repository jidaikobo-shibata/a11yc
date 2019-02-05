<?php
/**
 * A11yc\Update\AddData
 *
 * @package    part of A11yc
 * @author     Jidaikobo Inc.
 * @license    The MIT License (MIT)
 * @copyright  Jidaikobo Inc.
 * @link       http://www.jidaikobo.com
 */
namespace A11yc\Update;

use A11yc\Model;

class AddData
{
	use AddDataUtil;

	/**
	 * update
	 *
	 * @return Void
	 */
	public static function update()
	{
		if ( ! Db::isTableExist(A11YC_TABLE_SETTINGS)) return;

		self::serialize2json(); // trait AddDataUtil
		self::updateSettings();
		self::updateVersion();
		self::updatePages();

		foreach (Model\Page::fetchAll(array(), true) as $page)
		{
			$url = $page['url'];
			self::updateChecklist($url);
			self::updateResults($url);
		}

		self::updateBulk();
		self::updateMaintenance();
		self::updateUa();
		self::updateIssues();
		self::updateCaches();

		// drop tables
		$drops = array(
			A11YC_TABLE_RESULTS,
			A11YC_TABLE_BRESULTS,
			A11YC_TABLE_BCHECKS,
			A11YC_TABLE_MAINTENANCE,
			A11YC_TABLE_VERSIONS,
			A11YC_TABLE_UAS,
			A11YC_TABLE_SETTINGS,
			A11YC_TABLE_PAGES,
			A11YC_TABLE_CACHES,
			A11YC_TABLE_CHECKS,
			A11YC_TABLE_ISSUES,
			A11YC_TABLE_ISSUESBBS,
		);

		foreach ($drops as $drop)
		{
			$sql = 'DROP TABLE '.$drop.';';
			Db::execute($sql);
		}
	}

	/**
	 * updateSettings
	 *
	 * @return Void
	 */
	private static function updateSettings()
	{
		$sql = 'SELECT `version` FROM '.A11YC_TABLE_SETTINGS.' GROUP BY `version`;';
		$versions = self::versionArray(Db::fetchAll($sql));

		foreach ($versions as $v)
		{
			$version = $v['version'];
			$sql = 'SELECT * FROM '.A11YC_TABLE_SETTINGS.' WHERE `version` = ?;';

			$vals = array();
			foreach (Db::fetchAll($sql, array($version)) as $v)
			{
				$vals[$v['key']] = $v['is_array'] ? json_decode($v['value'], true) : $v['value'];
			}

			// update urls
			$url = Arr::get($vals, 'base_url', 'https://example.com');
			Model\Data::insert('sites', 'global', array(1 => $url));
			unset($vals['base_url']);

			// update settings
			Model\Data::insert('setting', 'common', $vals, $version);
		}
	}

	/**
	 * update pages
	 *
	 * @return Void
	 */
	private static function updatePages()
	{
		$sql = 'SELECT `version` FROM '.A11YC_TABLE_PAGES.' GROUP BY `version`;';
		$versions = self::versionArray(Db::fetchAll($sql));

		foreach ($versions as $v)
		{
			$version = $v['version'];
			$sql = 'SELECT * FROM '.A11YC_TABLE_PAGES.' WHERE `version` = ?;';

			foreach (Db::fetchAll($sql, array($version)) as $vals)
			{
				// update settings
				$url = $vals['url'];
				unset($vals['url']);
				$vals['image_path'] = isset($vals['image_path']) ? $vals['image_path'] : '';
				$vals['type'] = $vals['type'] == 2 ? 'pdf' : 'html';
				Model\Data::insert('page', $url, $vals, $version);
			}
		}
	}

	/**
	 * updateVersion
	 *
	 * @return Array
	 */
	private static function updateVersion()
	{
		$sql = 'SELECT * FROM '.A11YC_TABLE_VERSIONS.';';
		$vals = array();
		foreach (Db::fetchAll($sql) as $v)
		{
			$vals[Model\Data::baseUrl(true)][$v['version']] = array(
				'name'    => $v['name'],
				'trash'   => $v['trash'],
			);
		}
		if (empty($vals)) return;

		Model\Data::insert('versions', 'common', $vals);
	}

	/**
	 * updateChecklist
	 *
	 * @param String $url
	 * @return Void
	 */
	private static function updateChecklist($url)
	{
		$techs = Yaml::each('techs');
		$sql = 'SELECT `version` FROM '.A11YC_TABLE_CHECKS.' WHERE `url` = ? GROUP BY `version`;';
		$versions = self::versionArray(Db::fetchAll($sql, array($url)));

		foreach ($versions as $v)
		{
			$version = $v['version'];

			$sql = 'SELECT * FROM '.A11YC_TABLE_CHECKS.' WHERE `url` = ? AND `version` = ?;';
			$chks = array();
			foreach (Db::fetchAll($sql, array($url, $version)) as $v)
			{
				$chks[] = $v['code'];
			}

			$value = self::initKeysByCriterions();
			foreach ($chks as $chk)
			{
				foreach ($techs[$chk]['apps'] as $criterion)
				{
					$value[$criterion][] = $chk;
				}
			}

			Model\Data::insert('check_failure', $url, Model\Checklist::filterFailure($value), $version);
			Model\Data::insert('check', $url, $value, $version);
		}
	}

	/**
	 * updateResults
	 *
	 * @param String $url
	 * @return Array
	 */
	private static function updateResults($url)
	{
		$sql = 'SELECT `version` FROM '.A11YC_TABLE_RESULTS.' WHERE `url` = ? GROUP BY `version`;';
		$versions = self::versionArray(Db::fetchAll($sql, array($url)));

		foreach ($versions as $v)
		{
			$version = $v['version'];

			$sql = 'SELECT * FROM '.A11YC_TABLE_RESULTS.' WHERE `url` = ? AND `version` = ?;';
			$results = Db::fetchAll($sql, array($url, $version));
			$value = self::initKeysByCriterions();
			foreach ($results as $v)
			{
				unset($v['version']);
				unset($v['url']);
				$criterion = $v['criterion'];
				unset($v['criterion']);
				$value[$criterion] = $v;
			}

			Model\Data::insert('result', $url, $value, $version);
		}
	}

	/**
	 * updateBulk
	 *
	 * @return Array
	 */
	private static function updateBulk()
	{
		$sql = 'SELECT * FROM '.A11YC_TABLE_BCHECKS.';';
		$value = array();
		foreach (Db::fetchAll($sql) as $v)
		{
			$value[$v['code']]['is_checked'] = $v['is_checked'];
		}
		Model\Setting::insert(array(
				'bulk_checks' => $value,
			));

		$sql = 'SELECT * FROM '.A11YC_TABLE_BRESULTS.';';
		$value = array();
		foreach (Db::fetchAll($sql) as $v)
		{
			$value[$v['criterion']]['memo'] = Arr::get($v, 'memo');
			$value[$v['criterion']]['uid'] = $v['uid'];
			$value[$v['criterion']]['result'] = Arr::get($v, 'result');
			$value[$v['criterion']]['method'] = Arr::get($v, 'method');
		}
		Model\Setting::insert(array(
				'bulk_results' => $value,
			));
	}

	/**
	 * updateMaintenance
	 *
	 * @return Array
	 */
	private static function updateMaintenance()
	{
		$sql = 'SELECT `last_checked` FROM '.A11YC_TABLE_MAINTENANCE.';';
		$ret = Db::fetch($sql);
		Model\Setting::insert(array(
				'last_checked' => $ret['last_checked'],
			));
	}

	/**
	 * update UA
	 *
	 * @return Array
	 */
	private static function updateUa()
	{
		$sql = 'SELECT * FROM '.A11YC_TABLE_UAS.';';
		Model\Setting::insert(array(
				'user_agents' => Db::fetchAll($sql),
			));
	}

	/**
	 * updateIssues
	 *
	 * @return Void
	 */
	private static function updateIssues()
	{
		$sql = 'SELECT `version` FROM '.A11YC_TABLE_ISSUES.' GROUP BY `version`;';
		$versions = self::versionArray(Db::fetchAll($sql));

		foreach ($versions as $v)
		{
			$version = $v['version'];
			$sql = 'SELECT * FROM '.A11YC_TABLE_ISSUES.' WHERE `version` = ?;';

			foreach (Db::fetchAll($sql, array($version)) as $vals)
			{
				$vals['bbs'] = self::issueBbs($vals);
				unset($vals['id']);

				$vals['title'] = Arr::get($vals, 'title', '');
				$vals['seq'] = 0;
				$vals['url'] = $vals['is_common'] == 1 || empty($vals['url']) ? 'common' : $vals['url'] ;
				$vals['is_common'] = $vals['url'] == 'common' ? 1 : $vals['is_common'];
				$vals['techs'] = array();
				$vals['other_urls'] = '';

				$vals = self::techVals($vals);

				$url = $vals['url'];
				if (empty($url)) continue;
				unset($vals['url']);

				Model\Data::insert('issue', $url, $vals, $version);
			}
		}
	}

	/**
	 * issueBbs
	 *
	 * @param Array $vals
	 * @return Array
	 */
	private static function issueBbs($vals)
	{
		$bbses = array();
		$sql = 'SELECT * FROM '.A11YC_TABLE_ISSUESBBS.' WHERE `issue_id` = ?;';
		foreach (Db::fetchAll($sql, array($vals['id'])) as $bbs)
		{
			unset($bbs['id']);
			unset($bbs['issue_id']);
			$bbses[] = $bbs;
		}
		return $bbses;
	}

	/**
	 * techVals
	 *
	 * @param Array $vals
	 * @return Array
	 */
	private static function techVals($vals)
	{
		if (isset($vals['tech_url']) && ! empty($vals['tech_url']))
		{
			$techs = array();
			$other_urls = array();
			foreach (explode("\n", $vals['tech_url']) as $tech)
			{
				if (strpos($tech, 'WCAG-TECHS') === false)
				{
					$other_urls[] = $tech;
					continue;
				}
				$tech = substr($tech, strrpos($tech, '/') + 1);
				$techs[] = trim(str_replace('.html', '', $tech));
			}
			$vals['techs'] = $techs;
			$vals['other_urls'] = join("\n", $other_urls);
		}
		unset($vals['tech_url']);

		return $vals;
	}

	/**
	 * updateCaches
	 *
	 * @return Void
	 */
	private static function updateCaches()
	{
		$sql = 'SELECT * FROM '.A11YC_TABLE_CACHES.';';
		foreach (Db::fetchAll($sql) as $vals)
		{
			$url = $vals['url'];
			$ua = $vals['ua'];
			unset($vals['url']);
			unset($vals['type']);
			unset($vals['ua']);

			$vals[$ua] = $vals['data'];
			unset($vals['data']);

			Model\Data::insert('html', $url, $vals);
		}
	}
}
