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
class Docs
{
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
		View::assign('title', A11YC_LANG_DOCS_TITLE);
		View::assign('doc', $doc);
		View::assign('body', View::fetch_tpl('docs/each.php'), FALSE);
	}
}
