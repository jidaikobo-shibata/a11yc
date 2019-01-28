<?php namespace A11yc; ?>
<h2><?php echo A11YC_LANG_UNDERSTANDING ?></h2>

<?php
$lines = explode("\n", Util::docHtmlWhitelist(stripslashes(Util::key2link($doc['doc'], $a11yc_doc_url))));

if ( ! empty($lines)):
	foreach ($lines as $line): ?>
		<p><?php echo $line ?></p>
	<?php endforeach; ?>

<?php else: ?>
	<p><?php echo A11YC_LANG_NO_DOC ?></p>
<?php endif;

$html = '';

if (! $is_test):
?>

	<table class="a11yc_table_info a11yc_table">
	<tr>
		<th scope="row"><?php echo A11YC_LANG_PRINCIPLE ?></th>
		<td><?php echo $doc['guideline']['principle']['name'] ?></td>
		<td><?php echo $doc['guideline']['principle']['summary'] ?></td>
	</tr>

	<tr>
		<th scope="row"><?php echo A11YC_LANG_GUIDELINE ?></th>
		<td><?php echo $doc['guideline']['name'] ?></td>
		<td><?php echo $doc['guideline']['summary'] ?></td>
	</tr>

	<tr>
		<th scope="row"><?php echo A11YC_LANG_CRITERION ?></th>
		<td><?php echo $doc['name'].'<br><span class="a11yc_inlineblock">('.Util::key2code($doc['code']).' '.$doc['level']['name'].')</span>' ?></td>
		<td><?php echo $doc['summary'] ?></td>
	</tr>
	</table>

<!-- Techniques for WCAG 2.0 -->
<?php
$html.= '<h2>'.A11YC_LANG_ISSUE_TECH.'</h2>';

$html.= '<ul>';
foreach (array('t', 'f') as $tf):
	if ( ! isset($yml['techs_codes'][$criterion][$tf])) continue;
	foreach ($yml['techs_codes'][$criterion][$tf] as $tcode):
		$html.= '<li><a href="'.$refs['t'].$tcode.'.html">'.$yml['techs'][$tcode]['title'].'</a></li>';
	endforeach;
endforeach;
$html.= '</ul>';

elseif (isset($doc['urls'])):
// test

$html.= '<h2>'.A11YC_LANG_RELATED.'</h2>';
$html.= '<ul>';
foreach ($doc['urls'] as $u):
	$html.= '<li><a href="'.Util::s($u['url']).'">'.Util::s($u['name']).'</a></li>';
endforeach;
$html.= '</ul>';
endif;

echo $html;
