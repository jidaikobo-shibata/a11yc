<?php
/**
 * A11yc
 *
 * @package    part of A11yc
 * @version    1.0
 * @author     Jidaikobo Inc.
 * @license    WTFPL2.0
 * @copyright  Jidaikobo Inc.
 * @link       http:/www.jidaikobo.com
 *
 * ussage
 * see sample: A11YC_PATH./report.dist.php
 */

// kontiki
define('KONTIKI_CONFIG_PATH', __DIR__.'/config/kontiki.php');
require (__DIR__.'/libs/kontiki/main.php');

// a11yc
require (__DIR__.'/config/config.php');
require (A11YC_PATH.'/main.php');

// database
\A11yc\Db::forge();
\A11yc\Db::init_table();

// assign
if (isset($_GET['url']))
{
  $url = $_GET['url'];
	\A11yc\View::assign('report_title', A11YC_LANG_TEST_RESULT.': '.\A11yc\Util::fetch_page_title($url));
	\A11yc\Center::each($url);
}
else
{
	\A11yc\View::assign('report_title', A11YC_LANG_TEST_RESULT);
	\A11yc\Center::index();
}
