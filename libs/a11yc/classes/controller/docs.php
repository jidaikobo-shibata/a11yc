<?php
/**
 * A11yc\Docs
 *
 * @package    part of A11yc
 * @author     Jidaikobo Inc.
 * @license    The MIT License (MIT)
 * @copyright  Jidaikobo Inc.
 * @link       http://www.jidaikobo.com
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
		$criterion = Input::get('criterion', '');
		$code = Input::get('code', '');
		static::each($criterion, $code);
	}

	/**
	 * word exists
	 *
	 * @param  string $target
	 * @param  string $word
	 * @return  string
	 */
	private static function word_exists($target, $word)
	{
		$words = explode(' ', $word);

		$found = true;
		foreach ($words as $each_word)
		{
			if (strpos($target, $each_word) === false)
			{
				$found = false;
			}
		}
		return $found;
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
		if (Input::get('s'))
		{
			$yaml = Yaml::fetch();
			$test = Yaml::each('test');

			$word = mb_convert_kana(trim(Input::get('s')), "as");

			$r['chks'] = array();
			$r['tests'] = array();
			foreach ($yaml['checks'] as $criterion => $v)
			{
				foreach ($v as $chk => $vv)
				{
					if (
						static::word_exists($chk, $word) ||
						static::word_exists($vv['criterion']['code'], $word) ||
						static::word_exists($vv['criterion']['guideline']['principle']['name'], $word) ||
						static::word_exists($vv['criterion']['guideline']['principle']['summary'], $word) ||
						static::word_exists($vv['criterion']['guideline']['summary'], $word) ||
						static::word_exists($vv['criterion']['code'], $word) ||
						static::word_exists($vv['criterion']['code'], str_replace('.', '-', $word)) ||
						static::word_exists($vv['criterion']['summary'], $word) ||
						static::word_exists(@$vv['tech'], $word) ||
						static::word_exists($vv['name'], $word)
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
					static::word_exists($v['name'], $word) ||
					static::word_exists($v['tech'], $word)
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
			Util::error('invalid access.');
		}

		View::assign('yml', $yml, FALSE);
		View::assign('test', $test);
		View::assign('title', A11YC_LANG_DOCS_TITLE.': '.$doc['name']);
		View::assign('doc', $doc, FALSE);
		View::assign('body', View::fetch_tpl('docs/each.php'), FALSE);
	}
}
