<?php
/**
 * A11yc validate for ajax
 *
 * @package    part of A11yc
 * @author     Jidaikobo Inc.
 * @license    The MIT License (MIT)
 * @copyright  Jidaikobo Inc.
 * @link       http://www.jidaikobo.com
 */

// a11yc
if ( ! defined('A11YC_URL'))
{
	require (__DIR__.'/libs/a11yc/main.php');
}

// header
header("HTTP/1.1 200 OK");
header('Content-Type: text/html; charset=utf-8');

// database
\A11yc\Db::forge(array(
	'dbtype' => 'sqlite',
	'path' => __DIR__.'/db/db.sqlite',
));
\A11yc\Db::init_table();

// view
\A11yc\View::forge(A11YC_PATH.'/views/');

// action
$url = \A11yc\Util::urldec(\A11yc\Input::post('url', ''));
$link_check = intval(\A11yc\Input::post('link_check', ''));
if ( ! $url) \A11yc\Util::error('invalid access');

// assign
$errs = \A11yc\Controller_Checklist::validate_page($url, $link_check);
\A11yc\View::assign('errs', $errs, false);
\A11yc\View::assign('errs_cnts', array_merge(array('total' => count($errs)), \A11yc\Controller_Checklist::$err_cnts));

$raw = \A11yc\Util::s(\A11yc\Validate::get_hl_html());

$raw = \A11yc\Validate::revert_html($raw);

$lines = explode("\n", $raw);
$lines = array_map(function($v){return $v.'<br>';}, $lines);
$raw = join("\n", $lines);

\A11yc\View::assign('raw', $raw, false);

// dispatch
\A11yc\View::display(array('checklist/validate.php'));
