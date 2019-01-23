<?php
/**
 * A11yc\Model\Icl
 *
 * @package    part of A11yc
 * @author     Jidaikobo Inc.
 * @license    The MIT License (MIT)
 * @copyright  Jidaikobo Inc.
 * @link       http://www.jidaikobo.com
 */
namespace A11yc\Model;

class Icl
{
	protected static $vals = array(
		'icl'    => null,
		'iclsit' => null
	);
	public static $fields = array(
		'icl' => array(
			'title'      => '',
			'is_sit'     => true,
			'situation'  => '',
			'criterion'  => '',
			'identifier' => '',
			'inspection' => '',
			'techs'      => array(),
			'seq'        => 0,
			'trash'      => 0,
		),
		'iclsit' => array(
			'title'      => '',
			'is_sit'     => false,
			'criterion'  => '',
			'seq'        => 0,
			'trash'      => 0,
		)
	);

	/**
	 * fetch all
	 *
	 * @param String $type
	 * @param Bool $force
	 * @return Array
	 */
	public static function fetchAll($type = 'icl', $force = false)
	{
		if ( ! is_null(static::$vals[$type]) && ! $force) return static::$vals[$type];
		if ( ! in_array($type, array('icl', 'iclsit'))) return array();
		$ret = Data::fetch($type);
		if ( ! isset($ret['common'])) return array();

		$vals = array();
		foreach (Util::modCriterionBasedArr($ret['common']) as $criterion => $val)
		{
			if (empty($val))
			{
				$vals[$criterion] = array();
				continue;
			}
			foreach ($val as $v)
			{
				$v['level'] = Util::getLevelFromCriterion($criterion);
				$vals[$criterion][$v['id']] = $v;
			}
		}

		static::$vals[$type] = $vals;
		return static::$vals[$type];
	}

	/**
	 * fetch
	 *
	 * @param Integer $id
	 * @param Bool $force
	 * @return Array
	 */
	public static function fetch($id, $force = false)
	{
		$vals = array();
		$vals['icl'] = static::fetchAll('icl', $force);
		$vals['iclsit'] = static::fetchAll('iclsit', $force);

		foreach ($vals as $each)
		{
			foreach ($each as $val)
			{
				if (array_key_exists($id, $val))
				{
					return Arr::get($val, $id);
				}
			}
		}
		return array();
	}

	/**
	 * fetch4Checklist
	 *
	 * @param Bool $force
	 * @return Array
	 */
	public static function fetch4Checklist($force = false)
	{
		$icls = static::fetchAll('icl', $force);
		$iclsits = static::fetchAll('iclsit', $force);

		foreach ($icls as $criterion => $icl)
		{
			foreach ($icl as $id => $v)
			{
				if ( ! $v['situation']) continue;
				$iclsits[$criterion][$v['situation']]['implements'][] = $id;
			}
		}
		return $iclsits;
	}

	/**
	 * fetch4ImplementChecklist
	 *
	 * @param Bool $force
	 * @return Array
	 */
	public static function fetch4ImplementChecklist($force = false)
	{
		$icls = static::fetchAll('icl', $force);
		$iclsits = static::fetchAll('iclsit', $force);

		foreach ($icls as $criterion => $icl)
		{
			foreach ($icl as $id => $v)
			{
				if ($v['situation'])
				{
					$iclsits[$criterion][$v['situation']]['implements'][] = $v;
				}
				else
				{
					$iclsits[$criterion]['none']['implements'][] = $v;
				}
			}
		}
		return $iclsits;
	}

	/**
	 * insert
	 *
	 * @param Array $vals
	 * @param Bool $is_sit
	 * @return Integer|Bool
	 */
	public static function insert($vals, $is_sit = false)
	{
		$type = $is_sit ? 'iclsit' : 'icl';
		foreach (static::$fields[$type] as $key => $default)
		{
			$vals[$key] = Arr::get($vals, $key, $default);
		}
		return Data::insert($type, 'common', $vals);
	}

	/**
	 * update
	 *
	 * @param Integer $id
	 * @param Mixed $vals
	 * @return Bool
	 */
	public static function update($id, $vals)
	{
		$value = static::fetch($id, true);
		foreach ($vals as $k => $v)
		{
			$value[$k] = $v;
		}
		return Data::updateById($id, $value);
	}

	/**
	 * update partial
	 *
	 * @param Integer $id
	 * @param String $key
	 * @param Mixed $value
	 * @return Bool
	 */
	public static function updatePartial($id, $key, $value)
	{
		$vals = static::fetch($id, true);
		$vals[$key] = $value;
		return Data::updateById($id, $vals);
	}

	/**
	 * purge
	 *
	 * @param Integer $id
	 * @return Bool
	 */
	public static function purge($id)
	{
		return Data::deleteById($id);
	}
}
