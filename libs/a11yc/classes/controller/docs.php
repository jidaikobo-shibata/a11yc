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
	 * action search
	 *
	 * @return  void
	 */
	public static function Action_Search()
	{

		View::assign('yml', Yaml::fetch(), FALSE);
		View::assign('test', Yaml::each('test'));
		View::assign('title', A11YC_LANG_DOCS_TITLE);
		View::assign('body', View::fetch_tpl('docs/index.php'), FALSE);
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
		// search
		$r = array();
		$word = '';
		if (isset($_GET['s']))
		{
			$yaml = Yaml::fetch();
			$test = Yaml::each('test');

			$word = mb_convert_kana(trim($_GET['s']), "as");

			$r['chks'] = array();
			$r['tests'] = array();
			foreach ($yaml['checks'] as $criterion => $v)
			{
				foreach ($v as $chk => $vv)
				{
					if (
						strpos($chk, $word) !== false ||
						strpos($vv['criterion']['code'], $word) !== false ||
						strpos($vv['criterion']['guideline']['principle']['name'], $word) !== false ||
						strpos($vv['criterion']['guideline']['principle']['summary'], $word) !== false ||
//						strpos($vv['criterion']['guideline']['url'], $word) !== false ||
						strpos($vv['criterion']['guideline']['summary'], $word) !== false ||
						strpos($vv['criterion']['code'], $word) !== false ||
//						strpos($vv['criterion']['url'], $word) !== false ||
						strpos($vv['criterion']['summary'], $word) !== false ||
						strpos(@$vv['tech'], $word) !== false ||
						strpos($vv['name'], $word) !== false
					)
					{
						$r['chks']['principles'][] = $vv['criterion']['guideline']['principle']['code'];
						$r['chks']['guidelines'][] = $vv['criterion']['guideline']['code'];
						$r['chks']['criterions'][] = $vv['criterion']['code'];
						$r['chks']['codes'][] = $chk;
					}
				}
			}

			foreach ($test['tests'] as $code => $v)
			{
				if (
					strpos($v['name'], $word) !== false ||
					strpos($v['tech'], $word) !== false
				)
				{
					$r['tests'][] = $code;
				}
			}
		}

		View::assign('word', $word);
		View::assign('results', $r);
		View::assign('yml', Yaml::fetch(), FALSE);
		View::assign('test', Yaml::each('test'));
		View::assign('title', A11YC_LANG_DOCS_TITLE);
		View::assign('search_form', View::fetch_tpl('docs/search.php'), FALSE);
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
