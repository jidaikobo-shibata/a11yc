<?php
/**
 * A11yc\Docs
 *
 * @package    part of A11yc
 * @version    1.0
 * @author     Jidaikobo Inc.
 * @license    WTFPL2.0
 * @copyright  Jidaikobo Inc.
 * @link       http:/www.jidaikobo.com
 */
namespace A11yc;
class Controller_Docs
{
	/**
	 * action index
	 *
	 * @return  void
	 */
	public static function Action_Index()
	{
		static::index();
	}

	/**
	 * action each
	 *
	 * @return  void
	 */
	public static function Action_Each()
	{
		$criterion = isset($_GET['criterion']) ? $_GET['criterion'] : '';
		$code = isset($_GET['code']) ? $_GET['code'] : '';
		static::each($criterion, $code);
	}

	/**
	 * Show Techs Index
	 *
	 * @return  string
	 */
	public static function index()
	{
		View::assign('yml', Yaml::fetch(), FALSE);
		View::assign('test', Yaml::each('test'));
		View::assign('title', A11YC_LANG_DOCS_TITLE);
		View::assign('body', View::fetch_tpl('docs/index.php'), FALSE);
	}

	/**
	 * Show each
	 *
	 * @return  string
	 */
	public static function each($criterion, $code)
	{
		$yml = Yaml::fetch();
		$test = Yaml::each('test');

		if (isset($yml['checks'][$criterion][$code]))
		{
			$doc = $yml['checks'][$criterion][$code];
		}
		elseif(isset($test['tests'][$code]))
		{
			$doc = $test['tests'][$code];
		}
		else
		{
			die('invalid access.');
		}

		View::assign('yml', $yml, FALSE);
		View::assign('test', $test);
		View::assign('title', A11YC_LANG_DOCS_TITLE.': '.$doc['name']);
		View::assign('doc', $doc, FALSE);
		View::assign('body', View::fetch_tpl('docs/each.php'), FALSE);
	}
}
