<?php
/**
 * A11yc report sample
 *
 * @package    part of A11yc
 * @author     Jidaikobo Inc.
 * @license    The MIT License (MIT)
 * @copyright  Jidaikobo Inc.
 * @link       http://www.jidaikobo.com
 */
namespace A11yc;

// kontiki and a11yc
require (__DIR__.'/libs/a11yc/main.php');

// select version
$version = Input::get('a11yc_version', false);
$target = $version ? Controller_Disclosure::version2filename($version) : A11YC_DATA_FILE;

// database
Db::forge(array(
		'dbtype' => 'sqlite',
		'path'   => A11YC_DATA_PATH.$target,
	));

// versions
// Db::forge(
// 	'versions',
// 	array(
// 		'dbtype' => 'sqlite',
// 		'path' => A11YC_DATA_PATH.'/versions.sqlite',
// 	));

// init table
Db::init_table();

// view
View::forge(A11YC_PATH.'/views/');

// assign
Controller_Disclosure::index();

?><!DOCTYPE html>
<html lang="<?php echo A11YC_LANG ?>">
<head>
	<meta charset="utf-8">
	<title><?php echo View::fetch('title') ?> - A11YC</title>

	<!-- robots -->
	<meta name="robots" content="noindex, nofollow">

	<!-- viewport -->
	<meta name="viewport" content="width=device-width,initial-scale=1.0">

	<!--script-->
	<script type="text/javascript" src="<?php echo A11YC_ASSETS_URL ?>/js/jquery-1.11.1.min.js"></script>
	<script type="text/javascript" src="<?php echo A11YC_ASSETS_URL ?>/js/a11yc.js"></script>

	<!--css-->
	<link rel="stylesheet" type="text/css" media="all" href="<?php echo A11YC_ASSETS_URL ?>/css/a11yc.css" />
	<link href="<?php echo A11YC_ASSETS_URL ?>/css/font-awesome/css/font-awesome.min.css" rel="stylesheet">

</head>
<body>

<h1><?php echo View::fetch('title') ?></h1>
<?php echo View::fetch('body') ?>

</body>
</html>
