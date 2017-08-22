<?php namespace A11yc; ?>
<?php $level = isset($is_call_form_index) ? 'h5' : 'h2' ; ?>
<<?php echo $level ?>><?php echo A11YC_LANG_UNDERSTANDING ?></<?php echo $level ?>>

<?php
$lines = isset($doc['tech']) ? explode("\n", stripslashes(Util::key2link($doc['tech']))) : false;

if ($lines): ?>
	<ul>
	<?php foreach ($lines as $line): ?>
		<li><?php echo $line ?></li>
	<?php endforeach; ?>
	</ul>
<?php else: ?>
	<p><?php echo A11YC_LANG_NO_DOC ?></p>
<?php endif; ?>
<?php if (isset($doc['criterion'])): ?>
	<table class="a11yc_table_info a11yc_table">
	<tr>
		<th scope="row"><?php echo A11YC_LANG_PRINCIPLE ?></th>
		<td><?php echo $doc['criterion']['guideline']['principle']['name'] ?></td>
		<td><?php echo $doc['criterion']['guideline']['principle']['summary'] ?></td>
	</tr>

	<tr>
		<th scope="row"><?php echo A11YC_LANG_GUIDELINE ?></th>
		<td><?php echo $doc['criterion']['guideline']['name'] ?></td>
		<td><?php echo $doc['criterion']['guideline']['summary'] ?></td>
	</tr>

	<tr>
		<th scope="row"><?php echo A11YC_LANG_CRITERION ?></th>
		<td><?php echo $doc['criterion']['name'].'<br><span class="a11yc_inlineblock">('.Util::key2code($doc['criterion']['code']).' '.$doc['criterion']['level']['name'].')</span>' ?></td>
		<td><?php echo $doc['criterion']['summary'] ?></td>
	</tr>
	</table>
<?php endif; ?>

		<!-- relation -->
<?php
	if (isset($doc['relations'])):
		$rels = Util::s($doc['relations']);
?>
		<<?php echo $level ?>><?php echo A11YC_LANG_RELATED ?></<?php echo $level ?>>
		<ul>
<?php
			foreach ($rels as $rel_criterion => $rel_codes):
				foreach ($rel_codes as $rel_code):
?>
				<li><a<?php echo A11YC_TARGET ?> href="<?php echo A11YC_DOC_URL.$rel_code ?>&amp;criterion=<?php echo Util::s($rel_criterion) ?>"><?php echo $yml['checks'][$rel_criterion][$rel_code]['name'] ?>&nbsp;(<?php echo Util::key2code($yml['checks'][$rel_criterion][$rel_code]['criterion']['code']).'&nbsp;'.$yml['checks'][$rel_criterion][$rel_code]['level']['name'] ?>)</a></li>
			<?php
				endforeach;
			endforeach;
			?>
	</ul>
<?php endif; ?>

<?php
	$criterion = isset($doc['criterion']) ? $doc['criterion']['code'] : '';
	if (isset($yml['criterions'][$criterion]['url'])):
?>
		<!-- understanding -->
	<<?php echo $level ?>><?php echo A11YC_LANG_UNDERSTANDING ?>:&nbsp;<?php echo sprintf(A11YC_LANG_DOCS_UNDERSTANDING, Util::key2code($doc['criterion']['code'])) ?></<?php echo $level ?>>
	<p><a<?php echo A11YC_TARGET_OUT ?> href="<?php echo $yml['criterions'][$criterion]['url'] ?>"><?php echo $yml['criterions'][$criterion]['summary'] ?></a></p>
<?php endif; ?>

<!-- Accessibility Supported and other related things -->
<?php
foreach (array('url_as', 'urls') as $key):
if (isset($doc[$key])):
?>
	<<?php echo $level ?>><?php echo $key == 'url_as' ? A11YC_LANG_AS : A11YC_LANG_RELATED ?></<?php echo $level ?>>
	<ul>
	<?php
		foreach ($doc[$key] as $v):
		$v = Util::s($v);
	?>
		<li><a<?php echo A11YC_TARGET_OUT ?> href="<?php echo $v['url'] ?>"><?php echo $v['name'] ?></a></li>
	<?php endforeach; ?>
	</ul>
<?php
endif;
endforeach;
?>
