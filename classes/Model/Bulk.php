<?php
/**
 * A11yc\Model\Bulk
 *
 * @package    part of A11yc
 * @author     Jidaikobo Inc.
 * @license    The MIT License (MIT)
 * @copyright  Jidaikobo Inc.
 * @link       http://www.jidaikobo.com
 */
namespace A11yc\Model;

class Bulk
{
	/*
	fields:
	$fields = array(
		'checks' => array(
			'1-1-1' => array('H51'..),
		),
		'result' => array(
			'1-1-1' => array(
				'memo' => '',
				'uid' => 1,
				'result' => 1,
				'method' => 1,
			),
		)
	);
	*/

	/**
	 * fetch
	 *
	 * @return Array
	 */
	public static function fetchChecks()
	{
		return Setting::fetch('bulk_checks', array(), true);
	}

	/**
	 * fetch results
	 *
	 * @return Array
	 */
	public static function fetchResults()
	{
		return Setting::fetch('bulk_results', array(), true);
	}

	/**
	 * fetch iclchk
	 *
	 * @return Array
	 */
	public static function fetchIclchk()
	{
		return Setting::fetch('bulk_iclchks', array(), true);
	}
}
