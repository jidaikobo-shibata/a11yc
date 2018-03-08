<?php namespace A11yc; ?>
<?php $level = isset($is_call_form_index) ? 'h5' : 'h2' ; ?>
<<?php echo $level ?>><?php echo A11YC_LANG_UNDERSTANDING ?></<?php echo $level ?>>

<?php
$lines = isset($doc['doc']) ? explode("\n", Util::docHtmlWhitelist(stripslashes(Util::key2link($doc['doc'])))) : false;

if ($lines): ?>
	<?php foreach ($lines as $line): ?>
		<p><?php echo $line ?></p>
	<?php endforeach; ?>
<?php else: ?>
	<p><?php echo A11YC_LANG_NO_DOC ?></p>
<?php endif; ?>

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
$html = '';
$html.= '<h2>'.A11YC_LANG_ISSUES_TECH.'</h2>';

$html.= '<ul>';
foreach (array('t', 'f') as $tf):
	if ( ! isset($yml['techs_codes'][$criterion][$tf])) continue;
	foreach ($yml['techs_codes'][$criterion][$tf] as $tcode):
		$html.= '<li><a href="'.$refs['t'].$tcode.'.html">'.$yml['techs'][$tcode]['title'].'</a></li>';
	endforeach;
endforeach;
$html.= '</ul>';
echo $html;
