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
define('KONTIKI_DEFAULT_LANG', 'ja');
require (__DIR__.'/libs/kontiki/main.php');

// a11yc
require (__DIR__.'/config/config.php');
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
$url = '';
$level = '';

// each page
if (isset($_GET['url']))
{
  $url = $_GET['url'];
	\A11yc\View::assign('report_title', A11YC_LANG_TEST_RESULT.': '.\A11yc\Util::fetch_page_title($url));
	\A11yc\Controller_Center::each($url);
  // level
  $level = \A11yc\Db::fetch('SELECT `level` FROM '.A11YC_TABLE_PAGES.' WHERE `url` = '.\A11yc\Db::escape($url).';');
  if ($level)
  {
    $level = $level['level'];
  }
}
// total
else
{
	\A11yc\View::assign('report_title', A11YC_LANG_TEST_RESULT);
	\A11yc\Controller_Center::index();
}

// re assign
$pages_url       = './pages.dist.php';
$title           = \A11yc\View::fetch('report_title');
$target_level    = \A11yc\View::fetch('target_level');
$selected_method = \A11yc\View::fetch('selected_method');
$result          = \A11yc\View::fetch('result');
$additional      = \A11yc\View::fetch('additional');
$done            = \A11yc\View::fetch('done');
$unpassed_pages  = \A11yc\View::fetch('unpassed_pages');
$passed_pages    = \A11yc\View::fetch('passed_pages');
$total           = \A11yc\View::fetch('total');

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

<table class="a11yc_table">
<tbody>
	<tr>
		<th><!-- target level --><?php echo A11YC_LANG_TARGET_LEVEL ?></th>
		<td><?php echo \A11YC\Util::num2str($target_level) ?></td>
	</tr>
<?php if ($url): ?>
	<tr>
		<th><!-- current level --><?php echo A11YC_LANG_CHECKLIST_ACHIEVEMENT ?></th>
		<td><?php echo \A11YC\Util::num2str($level) ?></td>
	</tr>
<?php else: ?>
	<tr>
		<th><!-- current level --><?php echo A11YC_LANG_CURRENT_LEVEL_WEBPAGES ?></th>
		<td>
<?php
$site_level = \A11YC\Evaluate::check_site_level();
echo \A11YC\Evaluate::result_str($site_level, $target_level);
?>
		</td>
	</tr>
	<tr>
		<th><!-- selected method --><?php echo A11YC_LANG_CANDIDATES0 ?></th>
<?php
$arr = array(
  A11YC_LANG_CANDIDATES1,
  A11YC_LANG_CANDIDATES2,
  A11YC_LANG_CANDIDATES3,
  A11YC_LANG_CANDIDATES4,
);
?>
		<td><?php echo $arr[$selected_method] ?></td>
	</tr>
	<tr>
		<th><!-- number of checked --><?php echo A11YC_LANG_NUM_OF_CHECKED ?></th>
		<td><?php echo $done['done'].' / '.$total['total'].' (<a href="'.$pages_url.'">'.A11YC_LANG_CHECKED_PAGES.'</a>)' ?></td>
	</tr>
	<tr>
		<th><!-- unpassed pages --><?php echo A11YC_LANG_UNPASSED_PAGES ?></th>
		<td>
			<?php
				if ($unpassed_pages):
			?>
			<ul>
				<?php
					foreach ($unpassed_pages as $v):
						$url = s($v['url']);
				?>
				<li>
					<a href="<?php echo $url ?>"<?php echo A11YC_TARGET ?>><?php echo $url ?></a>
					(<a href="<?php echo A11YC_CHECKLIST_URL.$url ?>"<?php echo A11YC_TARGET ?>>check</a>)
				</li>
				<?php endforeach; ?>
			</ul>
			<?php elseif (count($passed_pages) >= 1): ?>
			<?php echo A11YC_LANG_UNPASSED_PAGES_NO ?>
			<?php else: ?>
			<?php echo '-' ?>
<?php endif; ?>
		</td>
	</tr>
<?php endif; ?>
</tbody>
</table>

<!-- site results -->
<h2><?php echo A11YC_LANG_CHECKLIST_TITLE ?></h2>
<?php echo $result ?>

<?php if ($additional): ?>
<h2><?php echo A11YC_LANG_CHECKLIST_CONFORMANCE_ADDITIONAL ?></h2>
<?php echo $additional ?>
<?php endif; ?>

<?php if($url): ?>
<!--  -->
<p style="text-align: right;"><a href="<?php echo $pages_url ?>"><?php echo A11YC_LANG_CHECKED_PAGES ?></a></p>
<?php endif; ?>

</body>
</html>
