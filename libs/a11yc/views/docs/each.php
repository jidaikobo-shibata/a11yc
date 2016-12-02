<h2><?php echo A11YC_LANG_UNDERSTANDING ?></h2>

<?php
$lines = isset($doc['tech']) ? explode("\n", stripslashes(\A11YC\Util::key2link($doc['tech']))) : false;

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
	<table id="a11yc_table_info" class="a11yc_table">
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
		<td><?php echo $doc['criterion']['name'].'<br><span class="a11yc_inlineblock">('.\A11yc\Util::key2code($doc['criterion']['code']).' '.$doc['criterion']['level']['name'].')</span>' ?></td>
		<td><?php echo $doc['criterion']['summary'] ?></td>
	</tr>
	</table>
<?php endif; ?>

		<!-- relation -->
<?php
	if (isset($doc['relations'])):
		$rels = \A11yc\Util::s($doc['relations']);
?>
		<h2><?php echo A11YC_LANG_RELATED ?></h2>
		<ul>
<?php
			foreach ($rels as $rel_criterion => $rel_codes):
				foreach ($rel_codes as $rel_code):
?>
				<li><a<?php echo A11YC_TARGET ?> href="<?php echo A11YC_DOC_URL.$rel_code ?>&amp;criterion=<?php echo \A11yc\Util::s($rel_criterion) ?>"><?php echo $yml['checks'][$rel_criterion][$rel_code]['name'] ?>&nbsp;(<?php echo \A11yc\Util::key2code($yml['checks'][$rel_criterion][$rel_code]['criterion']['code']).'&nbsp;'.$yml['checks'][$rel_criterion][$rel_code]['level']['name'] ?>)</a></li>
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
	<h2><?php echo A11YC_LANG_UNDERSTANDING ?>:&nbsp;<?php echo sprintf(A11YC_LANG_DOCS_UNDERSTANDING, \A11yc\Util::key2code($doc['criterion']['code'])) ?></h2>
	<p><a<?php echo A11YC_TARGET_OUT ?> href="<?php echo $yml['criterions'][$criterion]['url'] ?>"><?php echo $yml['criterions'][$criterion]['summary'] ?></a></p>
<?php endif; ?>

<?php if (isset($doc['url_as'])): ?>
	<!-- Accessibility Supported -->
	<h2><?php echo A11YC_LANG_AS ?></h2>
	<ul>
	<?php
		foreach ($doc['url_as'] as $v):
		$v = \A11yc\Util::s($v);
	?>
		<li><a<?php echo A11YC_TARGET_OUT ?> href="<?php echo $v['url'] ?>"><?php echo $v['name'] ?></a></li>
	<?php endforeach; ?>
	</ul>
<?php endif; ?>

<?php if (isset($doc['urls'])): ?>
	<!-- related -->
	<h2><?php echo A11YC_LANG_RELATED ?></h2>
	<ul>
	<?php
		foreach ($doc['urls'] as $v):
		$v = \A11yc\Util::s($v);
	?>
		<li><a<?php echo A11YC_TARGET_OUT ?> href="<?php echo $v['url'] ?>"><?php echo $v['name'] ?></a></li>
	<?php endforeach; ?>
	</ul>
<?php endif; ?>
