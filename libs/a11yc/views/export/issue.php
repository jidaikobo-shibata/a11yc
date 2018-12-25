<?php namespace A11yc; ?><!DOCTYPE html>
<html lang="<?php echo A11YC_LANG ?>">
<head>
	<meta charset="utf-8">
	<title><?php echo $title ?></title>

	<!-- robots -->
	<meta name="robots" content="noindex, nofollow">

	<!--css-->
	<link rel="stylesheet" type="text/css" media="all" href="<?php echo A11YC_ASSETS_URL ?>/css/a11yc_issue.css" />
	<link href="<?php echo A11YC_ASSETS_URL ?>/css/font-awesome/css/font-awesome.min.css" rel="stylesheet">
</head>
<body>
<article>
<?php
$header = '<header class="issue_header">'.$title;
$header.= ! empty($settings['test_period']) ? '<br>'.A11YC_LANG_TEST_PERIOD.' '.$settings['test_period'] : '' ;
$header.= '</header>';

$show_common_cover_page = false;
$show_each_cover_page = array();
?>

<?php
$page_num = 0;
?>
<?php
foreach ($issues as $type => $issue_parents):
$issue_num = 0;
// each cover page
if ($type != 'common'):
	$show_each_cover_page[] = $issue['url'];
	echo '<section class="cover_page">';
	echo '<h1 class="heading"><span class="issue_title">'.sprintf('%02d', ++$page_num).': '.$titles[$issue['url']].'</span><br><span class="issue_url">'.$issue['url'].'</span></h1>';
	echo '</section>';
		echo '<section class="cover_page screenshot">';
		if ( ! empty($images[$issue['url']])):
			echo '<p><img src="'.dirname(A11YC_URL).'/screenshots/pages/'.base64_encode($issue['url']).'/'.$images[$issue['url']].'" alt="'.A11YC_LANG_ISSUES_SCREENSHOT.'" /></p>';
		endif;
	echo '</section>';
elseif ($show_common_cover_page === false):
	// common
	$show_common_cover_page = true;
	echo '<div class="cover_page common">';
	echo $header;
	echo '<h1 class="heading">'.A11YC_LANG_ISSUES_IS_COMMON.'</h1>';
	echo '</div>';
endif;

foreach ($issue_parents as $issue): ?>

<section class="each_issue">
<h2 class="heading"><?php echo ( $page_num === 0 ? 'c' : sprintf('%02d', $page_num)) .'-'.++$issue_num.': '.$issue['title']  //issue title?  ?></h2>
<?php if ( ! empty($issue['image_path'])): ?>
	<section class="screenshot"><h3 class="heading"><?php echo A11YC_LANG_ISSUES_SCREENSHOT ?></h3>
	<?php
	echo '<img src="'.dirname(A11YC_URL).'/screenshots/issues/'.$issue['id'].'/'.$issue['image_path'].'" alt="">';
	?>
	</section>
<?php endif; ?>

<section class="issue">
<?php if($issue['html'] ): ?>
	<h3 class="heading"><?php echo A11YC_LANG_ISSUES_HTML ?></h3>
	<div>
		<pre><code><?php echo $issue['html'] ?></code></pre>
	</div>
<?php endif ; ?>
	<h3 class="heading"><?php echo A11YC_LANG_ISSUES_ERRMSG ?></h3>
	<p>
		<?php echo nl2br($issue['error_message']) ?>
	</p>
<?php if ($issue['tech_url']): ?>
	<h3 class="heading"><?php echo A11YC_LANG_ISSUES_TECH ?></h3>
	<div>
	<?php
	foreach (explode("\n", $issue['tech_url']) as $tech_url):
		echo '<a href="'.$tech_url.'">'.$tech_url.'</a><br>';
	endforeach;
	?>
	</div>
<?php endif; ?>
</section><!-- /.issue -->
</section><!-- /.each_issue -->
<?php endforeach; ?>
<?php endforeach; ?>
</article>
</body>
</html>
