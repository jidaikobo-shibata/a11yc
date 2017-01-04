<?php
/**
 * A11yc report sample
 *
 * @package    part of A11yc
 * @version    1.0
 * @author     Jidaikobo Inc.
 * @license    The MIT License (MIT)
 * @copyright  Jidaikobo Inc.
 * @link       http://www.jidaikobo.com
 */

// kontiki and a11yc
require (__DIR__.'/libs/a11yc/main.php');

// database
\A11yc\Db::forge(array(
	'dbtype' => 'sqlite',
	'path' => __DIR__.'/db/db.sqlite',
));
\A11yc\Db::init_table();

// view
\A11yc\View::forge(A11YC_PATH.'/views/');

// assign
\A11yc\Controller_Disclosure::total();

?><!DOCTYPE html>
<html lang="<?php echo A11YC_LANG ?>">
<head>
	<meta charset="utf-8">
	<title><?php echo \A11yc\View::fetch('title') ?> - A11YC</title>

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

<h1><?php echo \A11yc\View::fetch('title') ?></h1>
<?php echo \A11yc\View::fetch('body') ?>

</body>
</html>
