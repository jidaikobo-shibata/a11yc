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
$header = '<header class="issue_header">'.$title.'<br>'.A11YC_LANG_TEST_PERIOD.' '.$settings['test_period'].'</header>';

$show_common_cover_page = false;
$show_each_cover_page = array();
?>



<?php foreach ($issues as $type => $issue_parents): ?>
<?php foreach ($issue_parents as $issue): ?>

<?php
// each cover page
if ( ! empty($issue['url']) && ! in_array($issue['url'], $show_each_cover_page)):
	$show_each_cover_page[] = $issue['url'];
	echo '<section class="cover_page">';
	echo $header;
	echo '<h1 class="heading"><span class="issue_title">'.$pages[$issue['url']].'</span><br><span class="issue_url">'.$issue['url'].'</span></h1>';
	echo '</section>';
elseif ($show_common_cover_page === false):
	// common
	$show_common_cover_page = true;
	echo '<div class="cover_page common">';
	echo $header;
	echo '<h1 class="heading">'.A11YC_LANG_ISSUES_IS_COMMON.'</h1>';
	echo '</div>';
endif;
?>

<section class="each_page">
<?php if ( ! empty($issue['image_path'])): ?>
	<h2 class="heading"><?php echo A11YC_LANG_ISSUES_SCREENSHOT ?></h2>
	<?php
	echo '<div><img src="'.dirname(A11YC_URL).'/screenshots/'.$issue['id'].'/'.$issue['image_path'].'" alt="" class="screenshot"></div>';

	?>
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
		echo '<a href="'.$tech_url.'">'.$tech_url.'</a>';
	endforeach;
	?>
	</div>
<?php endif; ?>
</section><!-- /.issue -->
</section><!-- /.each_page -->
<?php endforeach; ?>
<?php endforeach; ?>
</article>
</body>
</html>
