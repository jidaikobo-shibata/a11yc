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

// common cover page
if (isset($issues[0]) && ($issues[0]['is_common'] || empty($issues[0]['url']))):
//	echo '<section class="each_issue">';
	echo '<div class="cover_page common">';
	echo $header;
	echo '<h1 class="heading">'.A11YC_LANG_ISSUES_IS_COMMON.'</h1>';
	echo '</div>';
endif;
?>

<?php foreach ($issues as $k => $issue): ?>

<?php
// each cover page
if ($k == 0 && isset($issues[0]) && ! empty($issues[0]['url'])):
	echo '<section class="cover_page">';
	echo $header;
	echo '<h1 class="heading">'.$issue['url'].'</h1>';
	echo '</section>';
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
	<h3 class="heading"><?php echo A11YC_LANG_ISSUES_HTML ?></h3>
	<div>
		<pre><code><?php echo $issue['html'] ?></code></pre>
	</div>

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
</article>
</body>
</html>
