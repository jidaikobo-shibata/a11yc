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
if (file_exists(__DIR__.'/constants.php'))
{
	require (__DIR__.'/constants.php');
}
elseif(file_exists(__DIR__.'/config/config.php'))
{
	require (__DIR__.'/config/config.php');
}
else
{
	die('set constants first. by constants.php or config/config.php');
}
require (__DIR__.'/libs/kontiki/main.php');
require (A11YC_PATH.'/main.php');

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

// assign
//\A11yc\Controller_Disclosure::total();
$url = isset($_GET['url']) ? \A11yc\Util::urldec($_GET['url']) : '';
$link_check = isset($_GET['link_check']) ? intval($_GET['link_check']) : '';
if ( ! $url) die('invalid access');
$errs = \A11yc\Controller_Checklist::validate_page($url, $link_check);
\A11yc\View::assign('errs', $errs, false);
\A11yc\View::assign('errs_cnts', array_merge(array('total' => count($errs)), \A11yc\Controller_Checklist::$err_cnts));

$raw = \A11yc\Util::s(\A11yc\Validate::get_hl_html());

$raw = str_replace(
	array(
		// span
		'[===a11yc_rplc===',
		'===a11yc_rplc_title===',
		'===a11yc_rplc_class===',

		// strong
		'===a11yc_rplc===][===a11yc_rplc_strong_class===',
		'===a11yc_rplc_strong===]',

		// strong to end
		'[===end_a11yc_rplc===',
		'===a11yc_rplc_back_class===',
		'===end_a11yc_rplc===]'
	),
	array(
		// span
		'<span id="',
		'" title="',
		'" class="a11yc_validation_code_error a11yc_level_',

		// span to strong
		'" tabindex="0">ERROR!</span><strong class="a11yc_level_',
		'">',

		// strong to end
		'</strong><a href="#index_',
		'" class="a11yc_back_link a11yc_hasicon a11yc_level_',
		'" title="back to error"><span class="a11yc_icon_fa a11yc_icon_arrow_u" role="presentation" aria-hidden="true"></span><span class="a11yc_skip">back</span></a>',
	),
	$raw);

$lines = explode("\n", $raw);
$lines = array_map(function($v){return $v.'<br>';}, $lines);
$raw = join("\n", $lines);

\A11yc\View::assign('raw', $raw, false);

\A11yc\View::display(array('checklist/validate.php'));
