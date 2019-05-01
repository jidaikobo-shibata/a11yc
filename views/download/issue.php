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
$index_titles = array();
/*
$header = '<header class="issue_header">'.$title;
$header.= ! empty($settings['test_period']) ? '<br>'.A11YC_LANG_TEST_PERIOD.' '.$settings['test_period'] : '' ;
$header.= '</header>';
*/
$show_common_cover_page = false;
$show_each_cover_page = array();

$index_titles[] = array();
$page_num = 0;
$index_titles[$page_num] = array(
	'title' => A11YC_LANG_ISSUE_IS_COMMON,
	'items' => array()
);

foreach ($issues as $url => $issue_parents):
$issue_num = 0;

// common cover page
if ($url == 'commons' && $show_common_cover_page === false):
	$show_common_cover_page = true;
	echo '<div class="cover_page common">';
//	echo $header;
	echo '<h1 class="heading">'.A11YC_LANG_ISSUE_IS_COMMON.'</h1>';
	echo '</div>';
endif;

// each cover page
if ($url != 'commons' && ! in_array($url, $show_each_cover_page)):
	$page_num = Arr::get($serial_nums, $url);
	$show_each_cover_page[] = $url;
	echo '<section class="cover_page'.( ! empty($images[$url]) ? ' has_image' : '' ).'">';
	echo '<div class="heading">';
	$this_title = $page_num.': '.$titles[$url];
	echo '<h1 class="heading"><span class="issue_title">'.$this_title.'</span>';
	if ( ! Arr::get($settings, 'hide_url_results')):
		echo '<br><span class="issue_url">'.$url.'</span>';
	endif;
	echo '</h1>';
	if ( ! empty($images[$url])):
		echo '<p class="sceenshot"><img src="'.A11YC_UPLOAD_URL.'/'.Model\Data::groupId().'/pages/'.base64_encode($url).'/'.$images[$url].'" alt="'.A11YC_LANG_ISSUE_SCREENSHOT.'" /></p>';
	endif;
	echo '</div><!-- /.heading --></section>';
	$index_titles[$page_num] = array(
		'title' => $this_title,
		'items' => array()
	);
endif;

// OMEDETO! There is no issue
if (empty($issue_parents)):
	echo '<section class="print_block">';
	echo  '<h3 class="heading">'.A11YC_LANG_REPORT_SHORT.'</h3>';
	echo '<p>'.A11YC_LANG_ISSUE_NOT_EXIST.'</p>';
	echo '</section>';
	continue;
endif;

foreach ($issue_parents as $criterion => $issue):

// common issues of each page
if ( ! isset($issue['title'])):
	echo '<section class="print_block">';
	echo  '<h3 class="heading">'.$page_num.'-0: '.A11YC_LANG_PAGES_EXIST_ISSUES.'</h3>';
	echo '<p>'.A11YC_LANG_PAGES_EXIST_ISSUES_EXP.'</p>';

	echo '<ul>';
	foreach (Arr::get($issues, 'commons') as $k => $v):
		if ( ! in_array($v['id'], Arr::get($issue, 'issue_ids', array()))) continue;
		$common_page_num = $k + 1;
		echo '<li>0-'.$common_page_num.': '.$v['title'].'</li>';
	endforeach;
	echo '</ul>';

	echo '</section>';
	continue;
endif;

?>
<section class="each_issue">
<div class="print_block">
<?php
$issue_title = $page_num.'-'.++$issue_num.': '.$issue['title'];
$index_titles[$page_num]['items'][] = $issue_title;
?>
<h2 class="heading" data-editurl="<?php echo A11YC_ISSUE_URL.'edit&amp;id='.intval($issue['id']) ?>"><?php echo $issue_title ?></h2>
<?php if ( ! empty($issue['image_path'])): ?>
	<section class="screenshot"><h3 class="heading"><?php echo A11YC_LANG_ISSUE_SCREENSHOT ?></h3>
	<?php
//	echo '<img src="'.A11YC_UPLOAD_URL.'/'.Model\Data::groupId().'/issues/'.$issue['id'].'/'.$issue['image_path'].'" alt="">';
	echo '<img src="'.A11YC_UPLOAD_URL.'/'.Model\Data::groupId().'/issues/'.$issue['image_path'].'" alt="">';
	?>
	</section>
<?php endif; ?>
</div><!-- ./print_block -->
<section class="issue">
<?php if ($issue['html'] ): ?>
<div class="print_block">
	<h3 class="heading"><?php echo A11YC_LANG_ISSUE_HTML ?></h3>
	<div>
		<pre><code><?php echo $issue['html'] ?></code></pre>
	</div>
</div><!-- ./print_block -->
<?php endif; ?>

<?php if (trim($issue['error_message']) !== '' ): ?>
<div class="print_block">
	<h3 class="heading"><?php echo A11YC_LANG_UNDERSTANDING ?></h3>
	<p>
		<?php echo nl2br($issue['error_message']) ?>
	</p>
</div><!-- ./print_block -->
<?php endif; ?>

<?php if ( ! empty($issue['criterions'])): ?>
<div class="print_block">
	<h3 class="heading"><?php echo A11YC_LANG_ISSUE_RELATED_CRITERIONS ?></h3>
	<ul>
	<?php
	foreach (Yaml::each('criterions') as $k => $v):
		if ( ! in_array($k, $issue['criterions'])) continue;
		echo '<li>'.Util::key2code($k).': '.$v['name'].'</li>';
	endforeach;
	?>
	<ul>
</div><!-- ./print_block -->
<?php endif; ?>

<?php if ( ! empty($issue['page_ids'])): ?>
<div class="print_block">
	<h3 class="heading"><?php echo A11YC_LANG_ISSUE_EXIST_PAGES ?></h3>
	<?php
	$pages = array();
	foreach (Model\Page::fetchAll() as $k => $v):
		if ( ! in_array($v['dbid'], $issue['page_ids'])) continue;
		$pages[] = '<li>'.$v['title'].'</li>';
	endforeach;
	if (count($pages) == count(Model\Page::fetchAll())):
		echo '<ul><li>'.A11YC_LANG_PAGE_ALL.'</li></ul>';
	else:
		echo '<ul>'.join($pages).'<ul>';
	endif;
	?>
</div><!-- ./print_block -->
<?php endif; ?>

<?php if (isset($issue['techs']) && ! empty($issue['techs'])): ?>
<div class="print_block">
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
	</div>
</div><!-- ./print_block -->
<?php endif; ?>

<?php
$other_url = Arr::get($issue, 'other_urls', false);
if ($other_url):
$other_urls = explode("\n", trim($other_url));
?>
<div class="print_block">
	<h3 class="heading"><?php echo A11YC_LANG_ISSUE_OTHER_URLS ?></h3>
	<div>
	<ul>
	<?php
	foreach ($other_urls as $each_url):
		if ( ! $each_url ) continue;
		echo '<li><a href="'.$each_url.'">'.$each_url.'</a></li>';
	endforeach;
	?>
	</ul>
	</div>
</div><!-- ./print_block -->
<?php endif; ?>

</section><!-- /.issue -->
</section><!-- /.each_issue -->

<?php endforeach; ?>
<?php endforeach; ?>

<?php
echo '<div class="noprint">';
echo '<h2>INDEX</h2>';
$is_common = true;
foreach ($index_titles as $v):
	echo '<h3>'.$v['title'].'</h3>';

	if ($is_common):
		$is_common = false;
		echo '<ul>';
		foreach ($v['items'] as $vv):
			echo '<li>'.$vv.'</li>';
		endforeach;
		echo '</ul>';
	endif;
endforeach;
echo '</div><!-- /.noprint -->';
?>
</article>
<script>
<!--
/*
	var flg = flg ? flg : false;
	var url = '';
	var link, target;
	if( location.href.indexOf('index.php') != -1 && ! flg )
	{
		var issue_title = document.querySelectorAll('[data-editurl]');
		for( var i =0; i < issue_title.length; i++ ){
			target = issue_title[i];
			url = target.dataset.editurl;
			link = document.createElement("a");
			link.href = url;
			link.appendChild(document.createTextNode(' edit'));
			target.appendChild(link);
		}
		flg = true;
	}
	*/
// -->
</script>
</body>
</html>
