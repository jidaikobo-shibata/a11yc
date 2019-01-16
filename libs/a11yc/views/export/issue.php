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

$page_num = 0;

foreach ($issues as $url => $issue_parents):
$issue_num = 0;

// common cover page
if ($url == 'commons' && $show_common_cover_page === false):
	$show_common_cover_page = true;
	echo '<div class="cover_page common">';
	echo $header;
	echo '<h1 class="heading">'.A11YC_LANG_ISSUE_IS_COMMON.'</h1>';
	echo '</div>';
endif;

foreach ($issue_parents as $criterion => $each):
foreach ($each as $issue):

// each cover page
if ($url != 'commons' && ! in_array($url, $show_each_cover_page)):
	$show_each_cover_page[] = $url;
	echo '<section class="cover_page'.( ! empty($images[$url]) ? ' has_image' : '' ).'">';
	echo '<div class="heading">';
	echo '<h1 class="heading"><span class="issue_title">'.sprintf('%02d', ++$page_num).': '.$titles[$url].'</span>';
	if (strpos($url, 'example.com') === false):
		echo '<br><span class="issue_url">'.$url.'</span>';
	endif;
	echo '</h1>';
	if ( ! empty($images[$url])):
		echo '<p class="sceenshot"><img src="'.A11YC_UPLOAD_URL.'/'.Model\Data::groupId().'/pages/'.base64_encode($url).'/'.$images[$url].'" alt="'.A11YC_LANG_ISSUE_SCREENSHOT.'" /></p>';
	endif;
	echo '</div><!-- /.heading --></section>';
endif;

?>
<section class="each_issue">
<h2 class="heading"><?php echo ( $page_num === 0 ? 'c' : sprintf('%02d', $page_num)) .'-'.++$issue_num.': '.$issue['title']  //issue title?  ?></h2>
<?php if ( ! empty($issue['image_path'])): ?>
	<section class="screenshot"><h3 class="heading"><?php echo A11YC_LANG_ISSUE_SCREENSHOT ?></h3>
	<?php
//	echo '<img src="'.A11YC_UPLOAD_URL.'/'.Model\Data::groupId().'/issues/'.$issue['id'].'/'.$issue['image_path'].'" alt="">';
	echo '<img src="'.A11YC_UPLOAD_URL.'/'.Model\Data::groupId().'/issues/'.$issue['image_path'].'" alt="">';
	?>
	</section>
<?php endif; ?>

<section class="issue">
<?php if($issue['html'] ): ?>
<<<<<<< HEAD
	<h3 class="heading"><?php echo A11YC_LANG_ISSUE_HTML ?></h3>
	<div>
		<pre><code><?php echo $issue['html'] ?></code></pre>
	</div>
<?php endif ; ?>
	<h3 class="heading"><?php echo A11YC_LANG_ISSUE_ERRMSG ?></h3>
	<p>
		<?php echo nl2br($issue['error_message']) ?>
	</p>

<?php if (isset($issue['techs']) && ! empty($issue['techs'])): ?>
	<h3 class="heading"><?php echo A11YC_LANG_ISSUE_TECH ?></h3>
	<div>
	<ul>
	<?php
	$yml = Yaml::each('techs');
	foreach ($issue['techs'] as $tech):
		$tech_url = A11YC_REF_WCAG20_TECH_URL.$tech.'.html';
		echo '<li>'.$yml[$tech]['title'].'<br>'.'<a href="'.$tech_url.'">'.$tech_url.'</a></li>';
	endforeach;
	?>
	</ul>
=======
	<div class="issue_html">
		<h3 class="heading"><?php echo A11YC_LANG_ISSUES_HTML ?></h3>
		<pre><code><?php echo $issue['html'] ?></code></pre>
	</div>
<?php endif ; ?>
	<div class="issue_understanding">
		<h3 class="heading"><?php echo A11YC_LANG_UNDERSTANDING ?></h3>
		<p>
			<?php echo nl2br($issue['error_message']) ?>
		</p>
	</div>
<?php if ($issue['tech_url']): ?>
	<div class="issue_tech">
		<h3 class="heading"><?php echo A11YC_LANG_ISSUES_TECH ?></h3>
		<?php
		foreach (explode("\n", $issue['tech_url']) as $tech_url):
			echo '<a href="'.$tech_url.'">'.$tech_url.'</a><br>';
		endforeach;
		?>
>>>>>>> 6caf0c91dd51ba28d67e771453a9fedce468fe86
	</div>
<?php endif; ?>

<?php
$other_url = Arr::get($issue, 'other_urls', false);
if ($other_url):
$other_urls = explode("\n", $other_url);
?>
	<h3 class="heading"><?php echo A11YC_LANG_ISSUE_OTHER_URLS ?></h3>
	<div>
	<ul>
	<?php
	foreach ($other_urls as $each_url):
		echo '<li><a href="'.$each_url.'">'.$each_url.'</a></li>';
	endforeach;
	?>
	</ul>
	</div>
<?php endif; ?>

</section><!-- /.issue -->
</section><!-- /.each_issue -->
<?php endforeach; ?>
<?php endforeach; ?>
<?php endforeach; ?>
</article>
</body>
</html>
