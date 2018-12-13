<?php namespace A11yc; ?><!DOCTYPE html>
<html lang="<?php echo A11YC_LANG ?>">
<head>
	<meta charset="utf-8">
	<title><?php echo $title ?></title>

	<!-- robots -->
	<meta name="robots" content="noindex, nofollow">

	<!--css-->
	<link rel="stylesheet" type="text/css" media="all" href="<?php echo A11YC_ASSETS_URL ?>/css/a11yc.css" />
	<link href="<?php echo A11YC_ASSETS_URL ?>/css/font-awesome/css/font-awesome.min.css" rel="stylesheet">
</head>
<body>
<?php

$header = '<div class="issue_header">'.$title.'<br>'.A11YC_LANG_TEST_PERIOD.' '.$settings['test_period'].'</div>';

// common cover page
if (isset($issues[0]) && ($issues[0]['is_common'] || empty($issues[0]['url']))):
	echo $header;
	echo '<div class="cover_page">';
	echo '<h1>'.A11YC_LANG_ISSUES_IS_COMMON.'</h1>';
	echo '</div>';
endif;
?>

<?php foreach ($issues as $k => $issue): ?>

<?php
// each cover page
if ($k == 0 && isset($issues[0]) && ! empty($issues[0]['url'])):
	echo $header;
	echo '<div class="cover_page">';
	echo '<h1>'.$issue['url'].'</h1>';
	echo '</div>';
endif;
?>

<div class="each_page">

<?php if ( ! empty($issue['image_path'])): ?>
	<h2><?php echo A11YC_LANG_ISSUES_SCREENSHOT ?></h2>
	<?php
	echo '<div><img src="'.dirname(A11YC_URL).'/screenshots/'.$issue['id'].'/'.$issue['image_path'].'" alt="" /></div>';

	?>
<?php endif; ?>

<table class="a11yc_table">

<tr>
	<th><?php echo A11YC_LANG_ISSUES_N_OR_E ?></th>
	<td>
	<?php
	if ($issue['n_or_e'] == 0):
		echo 'Notice';
	else:
		echo 'Error';
	endif;
	?>
	</td>
</tr>

<tr>
	<th><?php echo A11YC_LANG_ISSUES_HTML ?></th>
	<td><?php echo $issue['html'] ?></td>
</tr>

<tr>
	<th><?php echo A11YC_LANG_ISSUES_ERRMSG ?></th>
	<td><?php echo nl2br($issue['error_message']) ?></td>
</tr>

<tr>
	<th><?php echo A11YC_LANG_ISSUES_STATUS ?></th>
	<td>
	<select name="status" id="a11yc_status">
		<?php $selected = $issue['status'] == 0 ? ' selected="selected"': ''; ?>
		<option<?php echo $selected ?> value="0"><?php echo A11YC_LANG_ISSUES_STATUS_1 ?></option>
		<?php $selected = $issue['status'] == 1 ? ' selected="selected"': ''; ?>
		<option<?php echo $selected ?> value="1"><?php echo A11YC_LANG_ISSUES_STATUS_2 ?></option>
		<?php $selected = $issue['status'] == 2 ? ' selected="selected"': ''; ?>
		<option<?php echo $selected ?> value="2"><?php echo A11YC_LANG_ISSUES_STATUS_3 ?></option>
	</select>
	</td>
</tr>

<?php if ($issue['tech_url']): ?>
<tr>
	<th><?php echo A11YC_LANG_ISSUES_TECH ?></th>
	<td>
	<?php
	foreach (explode("\n", $issue['tech_url']) as $tech_url):
		echo '<a href="'.$tech_url.'">'.$tech_url.'</a>';
	endforeach;
	?>
	</td>
</tr>
<?php endif; ?>
</table>
</div>
<?php endforeach; ?>
</body>
</html>
