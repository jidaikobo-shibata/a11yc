<?php
/**
 * A11yc\Controller\Implement
 *
 * @package    part of A11yc
 * @author     Jidaikobo Inc.
 * @license    The MIT License (MIT)
 * @copyright  Jidaikobo Inc.
 * @link       http://www.jidaikobo.com
 */
namespace A11yc\Controller;

use A11yc\Model;

class Implement
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
	 * Show Techs Index
	 *
	 * @return Void
	 */
	public static function index()
	{
		// if ( ! View::fetch('a11yc_doc_url'))
		// {
		// 	View::assign('a11yc_doc_url', A11YC_DOC_URL);
		// }
		// View::assign('word', $word);
		// View::assign('results', $r);
		// View::assign('yml', Yaml::fetch(), FALSE);
		// View::assign('tests', Yaml::each('tests'));
		// View::assign('title', A11YC_LANG_DOCS_TITLE);
		// View::assign('search_form', View::fetchTpl('docs/search.php'), FALSE);
		// View::assign('body', View::fetchTpl('docs/index.php'), FALSE);
	}
}
