<?php
/**
 * A11yc Online Validation
 *
 * @package    part of A11yc
 * @author     Jidaikobo Inc.
 * @license    The MIT License (MIT)
 * @copyright  Jidaikobo Inc.
 * @link       http://www.jidaikobo.com
 */

// a11yc
namespace A11yc;
require (__DIR__.'/libs/a11yc/main.php');

// url
$url = Util::uri();
$url = Util::remove_query_strings($url);

// language and validation access
$lang = Lang::get_lang();
if (
	empty($lang) ||
	substr($url, 0 - strlen('/post.php')) != '/post.php')
{
	Util::error('Not found.');
}
include A11YC_PATH.'/languages/'.$lang.'/post.php';

// document list
$docs = Input::get('docs');
if ($docs)
{
	Controller_Docs::index();
	define('A11YC_LANG_POST_TITLE', A11YC_LANG_DOCS_TITLE);
}

// document
$code = Input::get('code');
$criterion = Input::get('criterion');
if ($code && $criterion)
{
	Controller_Docs::each($criterion, $code);
	$doc = View::fetch('doc');
	define('A11YC_LANG_POST_TITLE', $doc['name']);
}

// validation
$target_html = '';
$raw = '';
$all_errs = array();
$errs_cnts = array();
$target_html = Input::post('source');
if (( ! $docs && ! $code && ! $criterion) && $target_html)
{
	$all_errs = array();
	Validate::set_html($target_html);
	// Crawl::set_target_path($url); // for same_urls_should_have_same_text

	$codes = Validate::$codes;
	unset($codes['link_check']);
	unset($codes['same_urls_should_have_same_text']);

	// validate
	foreach ($codes as $method => $class)
	{
		$class::$method();
	}

	if (Validate::get_error_ids())
	{
		foreach (Validate::get_error_ids() as $err_code => $errs)
		{
			foreach ($errs as $key => $err)
			{
				$all_errs[] = Controller_Checklist::message($err_code, $err, $key, $url.'?code=');
			}
		}
	}

	// results
	$errs_cnts = array_merge(array('total' => count($all_errs)), Controller_Checklist::$err_cnts);
	$raw = nl2br(Validate::revert_html(Util::s(Validate::get_hl_html())));
}

// basic assign
if ( ! $docs && ! $code && ! $criterion)
{
	// title
	define('A11YC_LANG_POST_TITLE', 'Online Validation');

	// assign
	View::assign('is_call_from_post', true);

	View::assign('errs', $all_errs, false);
	View::assign('errs_cnts', $errs_cnts);
	View::assign('raw', $raw, false);

	View::assign('result', View::fetch_tpl('checklist/validate.php'), false);

	View::assign('target_html', $target_html);
	View::assign('title', 'Online Validation');
	View::assign('body', View::fetch_tpl('post.php'), false);
}

// render
View::assign('mode', 'post');
View::display(array(
		'header.php',
		'messages.php',
		'body.php',
		'footer.php',
	));
