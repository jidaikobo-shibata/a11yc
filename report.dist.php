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

include (__DIR__.'/inc_report.php');

// re assign
$pages_url       = './pages.dist.php';
$title           = \A11yc\View::fetch('report_title');
$target_level    = \A11yc\View::fetch('target_level');
$selected_method = \A11yc\View::fetch('selected_method');
$result          = \A11yc\View::fetch('result');
$done            = \A11yc\View::fetch('done');
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
</table>

<!-- site results -->
<h2><?php echo A11YC_LANG_CHECKLIST_TITLE ?></h2>
<?php echo $result ?>

</body>
</html>
