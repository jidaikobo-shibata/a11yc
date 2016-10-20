<?php
/**
 * A11yc validate for ajax
 *
 * @package    part of A11yc
 * @version    1.0
 * @author     Jidaikobo Inc.
 * @license    WTFPL2.0
 * @copyright  Jidaikobo Inc.
 * @link       http:/www.jidaikobo.com
 */

// kontiki and a11yc
require (__DIR__.'/config/config.php');
require (__DIR__.'/libs/kontiki/main.php');
require (A11YC_PATH.'/main.php');

// database
\A11yc\Db::forge(array(
	'dbtype' => 'sqlite',
	'path' => __DIR__.'/db/db.sqlite',
));
\A11yc\Db::init_table();

// view
\A11yc\View::forge(A11YC_PATH.'/views/');

// assign
//\A11yc\Controller_Disclosure::total();
$url = isset($_GET['url']) ? urldecode($_GET['url']) : '';
$link_check = isset($_GET['link_check']) ? intval($_GET['link_check']) : '';
if ( ! $url) die('invalid access');
$errs = \A11yc\Controller_Checklist::validate_page($url, $link_check);
\A11yc\View::assign('errs', $errs, false);

$raw = \A11yc\Util::s(\A11yc\Validate::get_hl_html());
$raw = str_replace(
	array(
		'[===a11yc_rplc===',
		'===a11yc_rplc===]',
		'[===end_a11yc_rplc===',
		'===end_a11yc_rplc===]'
	),
	array(
		'<span id="',
		'"></span><strong>',
		'</strong><a class="a11yc_back_link" href="#index_',
		'">back</a>',
	),
	$raw);

$lines = explode("\n", $raw);
$lines = array_map(function($v){return $v.'<br>';}, $lines);
$raw = join("\n", $lines);

\A11yc\View::assign('raw', $raw, false);

\A11yc\View::display(array('checklist/validate.php'));
