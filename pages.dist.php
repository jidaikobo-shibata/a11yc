<?php
/**
 * A11yc report sample
 *
 * @package    part of A11yc
 * @version    1.0
 * @author     Jidaikobo Inc.
 * @license    WTFPL2.0
 * @copyright  Jidaikobo Inc.
 * @link       http:/www.jidaikobo.com
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

// pages
$pages = \A11yc\Db::fetch_all('SELECT * FROM '.A11YC_TABLE_PAGES.' WHERE `trash` = 0 ORDER BY `url` ASC;');

// title
$title = A11YC_LANG_CHECKED_PAGES;

?><!DOCTYPE html>
<html lang="<?php echo A11YC_LANG ?>">
<head>
	<meta charset="utf-8">
	<title><?php echo $title ?> - A11YC</title>

	<!-- robots -->
	<meta name="robots" content="noindex, nofollow">

	<!-- viewport -->
	<meta name="viewport" content="width=device-width,initial-scale=1.0">

	<!--script-->
	<script type="text/javascript" src="//code.jquery.com/jquery-1.11.1.min.js"></script>
	<script type="text/javascript" src="<?php echo A11YC_URL_DIR ?>/js/a11yc.js"></script>

	<!--css-->
	<link rel="stylesheet" type="text/css" media="all" href="<?php echo A11YC_URL_DIR ?>/css/a11yc.css" />
	<link href="<?php echo A11YC_URL_DIR ?>/css/font-awesome/css/font-awesome.min.css" rel="stylesheet">

</head>
<body>

<h1><?php echo $title ?></h1>

<table>
<thead>
<tr>
	<th><?php echo A11YC_LANG_CHECKLIST_TARGETPAGE ?></th>
	<th><?php echo A11YC_LANG_CURRENT_LEVEL ?></th>
	<th><?php echo A11YC_LANG_TEST_DATE ?></th>
</tr>
</thead>
<?php
foreach ($pages as $v):
$url = \A11yc\Util::s($v['url']);
?>
<tr>
	<td><?php echo '<a href=".$url.">'.$url.'</a>' ?></td>
	<td><?php echo \A11yc\Util::num2str($v['level']) ?></td>
	<td><?php echo \A11yc\Util::s($v['date']) ?></td>
</tr>
<?php endforeach; ?>
</table>

<p style="text-align: right;"><a href="./report.dist.php"><?php echo A11YC_LANG_REPORT ?></a></p>

</body>
</html>
