<?php
/**
 * A11yc\Controller\Docs
 *
 * @package    part of A11yc
 * @author     Jidaikobo Inc.
 * @license    The MIT License (MIT)
 * @copyright  Jidaikobo Inc.
 * @link       http://www.jidaikobo.com
 */
namespace A11yc\Controller;

use A11yc\Model;

class Docs
{
	/**
	 * action index
	 *
	 * @return Void
	 */
	public static function actionIndex()
	{
		static::index();
	}

	/**
	 * action search
	 *
	 * @return Void
	 */
	public static function actionSearch()
	{
		View::assign('yml', Yaml::fetch(), FALSE);
		View::assign('tests', Yaml::each('tests'));
		View::assign('title', A11YC_LANG_DOCS_TITLE);
		View::assign('body', View::fetchTpl('docs/index.php'), FALSE);
	}

	/**
	 * action each
	 *
	 * @return Void
	 */
	public static function actionEach()
	{
		$criterion = Input::get('criterion', '');
		static::each($criterion);
	}

	/**
	 * word exists
	 *
	 * @param  String $target
	 * @param  String $word
	 * @return Bool
	 */
	private static function wordExists($target, $word)
	{
		$words = explode(' ', strtolower($word));
		$target = strtolower($target);

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
	 * @return Void
	 */
	public static function index()
	{
		// search
		$r = array();
		$word = '';
		if (Input::get('s'))
		{
			$yaml = Yaml::fetch();
			$tests = Yaml::each('tests');

			$word = mb_convert_kana(trim(Input::get('s')), "as");

			$r['criterions'] = array();
			$r['tests'] = array();
			foreach ($yaml['criterions'] as $k => $v)
			{
				if (
					self::wordExists($v['code'], $word) ||
					self::wordExists($v['doc'], $word) ||
					self::wordExists($v['guideline']['principle']['name'], $word) ||
					self::wordExists($v['guideline']['principle']['summary'], $word) ||
					self::wordExists($v['guideline']['summary'], $word) ||
					self::wordExists($v['code'], $word) ||
					self::wordExists($v['code'], str_replace('.', '-', $word)) ||
					self::wordExists($v['summary'], $word) ||
					self::wordExists(@$v['tech'], $word) ||
					self::wordExists($v['name'], $word)
				)
				{
					$r['criterions']['principles'][] = $v['guideline']['principle']['code'];
					$r['criterions']['guidelines'][] = $v['guideline']['code'];
					$r['criterions']['criterions'][] = $v['code'];
				}
			}

			foreach ($tests as $code => $v)
			{
				if (
					self::wordExists($v['name'], $word) ||
					self::wordExists($v['tech'], $word)
				)
				{
					$r['tests'][] = $code;
				}
			}
		}

		View::assign('word', $word);
		View::assign('results', $r);
		View::assign('yml', Yaml::fetch(), FALSE);
		View::assign('tests', Yaml::each('tests'));
		View::assign('title', A11YC_LANG_DOCS_TITLE);
		View::assign('search_form', View::fetchTpl('docs/search.php'), FALSE);
		View::assign('body', View::fetchTpl('docs/index.php'), FALSE);
	}

	/**
	 * Show each
	 *
	 * @param String $code
	 * @return Void
	 */
	public static function each($code)
	{
		$yml = Yaml::fetch();
		$tests = Yaml::each('tests');
		$doc = array();
		$is_test = false;

		if (isset($yml['criterions'][$code]))
		{
			$doc = $yml['criterions'][$code];
		}
		elseif(isset($tests[$code]))
		{
			$doc = $tests[$code];
			$is_test = true;
		}
		else
		{
			Util::error('invalid access.');
		}

		// reference urls
		$standards = Yaml::each('standards');
		$standard = Arr::get(Model\Settings::fetchAll(), 'standard', 0);
		$refs = Values::getRefUrls();
		View::assign('refs', $refs[$standard]);

		View::assign('criterion', $code);
		View::assign('yml', $yml, FALSE);
		View::assign('is_test', $is_test);
		View::assign('title', A11YC_LANG_DOCS_TITLE.': '.$doc['name']);
		View::assign('doc', $doc);
		View::assign('body', View::fetchTpl('docs/each.php'), FALSE);
	}
}
