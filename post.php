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

// language and validation access
$lang = Lang::get_lang();
if (
	empty($lang) ||
	substr($_SERVER['REQUEST_URI'], 0 - strlen('/post.php')) != '/post.php')
{
	Util::error('Not found.');
}
include A11YC_PATH.'/languages/'.$lang.'/post.php';

// values
$target_html = '';
$raw = '';
$all_errs = array();
$errs_cnts = array();

// validate
$target_html = Input::post('source');
if ($target_html)
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
		foreach (Validate::get_error_ids() as $code => $errs)
		{
			foreach ($errs as $key => $err)
			{
				$all_errs[] = Controller_Checklist::message($code, $err, $key);
			}
		}
	}

	// results
	$errs_cnts = array_merge(array('total' => count($all_errs)), Controller_Checklist::$err_cnts);
	$raw = nl2br(Validate::revert_html(Util::s(Validate::get_hl_html())));
}

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
View::assign('mode', 'post');

// render
View::display(array(
		'header.php',
		'messages.php',
		'body.php',
		'footer.php',
	));
