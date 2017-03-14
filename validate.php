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
namespace A11yc;

// a11yc
if ( ! defined('A11YC_URL'))
{
	require (__DIR__.'/libs/a11yc/main.php');
}

// header
header("HTTP/1.1 200 OK");
header('Content-Type: text/html; charset=utf-8');

// database
Db::forge(array(
	'dbtype' => 'sqlite',
	'path' => __DIR__.'/db/db.sqlite',
));
Db::init_table();

// view
View::forge(A11YC_PATH.'/views/');

// action
$url = Util::urldec(Input::post('url', ''));
$link_check = intval(Input::post('link_check', ''));
if ( ! $url) Util::error('invalid access');

// assign
$errs = Controller_Checklist::validate_page($url, $link_check);
View::assign('errs', $errs, false);
View::assign('errs_cnts', array_merge(array('total' => count($errs)), Controller_Checklist::$err_cnts));

$raw = Util::s(Validate::get_hl_html());

$raw = Validate::revert_html($raw);

$lines = explode("\n", $raw);
$lines = array_map(function($v){return $v.'<br>';}, $lines);
$raw = join("\n", $lines);

View::assign('raw', $raw, false);

// dispatch
View::display(array('checklist/validate.php'));
