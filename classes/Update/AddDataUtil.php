<?php
/**
 * A11yc\Update\AddDataUtil
 *
 * @package    part of A11yc
 * @author     Jidaikobo Inc.
 * @license    The MIT License (MIT)
 * @copyright  Jidaikobo Inc.
 * @link       http://www.jidaikobo.com
 */
namespace A11yc\Update;

trait AddDataUtil
{
	/**
	 * serialize2json
	 *
	 * @return Void
	 */
	private static function serialize2json()
	{
		$sql = 'ALTER TABLE '.A11YC_TABLE_SETTINGS.' ADD `is_array` BOOL NOT NULL DEFAULT 0;';
		Db::execute($sql);

		$serializeds = array(
			'additional_criterions',
			'non_exist_and_passed_criterions',
			'non_use_techs',
		);
		foreach ($serializeds as $serialized)
		{
			$sql = 'SELECT `version` FROM '.A11YC_TABLE_SETTINGS.' GROUP BY `version`;';

			foreach (Db::fetchAll($sql) as $v)
			{
				$version = $v['version'];

				$sql = 'SELECT * FROM '.A11YC_TABLE_SETTINGS.' WHERE `key` = ? AND `version` = ?;';
				$results = Db::fetchAll($sql, array($serialized, $version));
				$results['value'] = empty($results['value']) ? array() : $results['value'];
				unset($results['version']);
				$vals = array(
					'key'      => $serialized,
					'value'    => $results['value'],
					'version'  => $version,
				);
				self::insertSettings($vals);
			}
		}
	}

	/**
	 * insert
	 * old settings' insert method
	 *
	 * @param Array $vals
	 * @return Bool
	 */
	public static function insertSettings($vals)
	{
		$is_array = false;
		if (is_array($vals['value']))
		{
			$vals['value'] = json_encode($vals['value']);
			$is_array = true;
		}

		$sql = 'INSERT INTO '.A11YC_TABLE_SETTINGS.' (';
		$sql.= '`key`,';
		$sql.= '`value`,';
		$sql.= '`is_array`,';
		$sql.= '`version`';
		$sql.= ')';
		$sql.= ' VALUES (?, ?, ?, ?);';
		return Db::execute(
			$sql,
			array(
				Arr::get($vals, 'key', ''),
				Arr::get($vals, 'value', ''),
				$is_array,
				Arr::get($vals, 'version', 0), // do not intval()
			)
		);
	}

	/**
	 * initKeysByCriterions
	 *
	 * @return Array
	 */
	private static function initKeysByCriterions()
	{
		$vals = array();
		$criterions = Yaml::each('criterions');
		foreach (array_keys($criterions) as $criterion)
		{
			$vals[$criterion] = array();
		}
		return $vals;
	}

	/**
	 * versionArray
	 *
	 * @param Array $versions
	 * @return Array
	 */
	private static function versionArray($versions)
	{
		return empty($versions) ? array(0 => array('version' => 0)) : $versions ;
	}
}
