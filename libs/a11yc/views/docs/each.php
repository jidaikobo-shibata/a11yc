<?php
$lines = isset($doc['tech']) ? explode("\n", stripslashes($doc['tech'])) : false;
if ($lines): ?>
	<ul>
	<?php foreach ($lines as $line): ?>
		<li><?php echo $line ?></li>
	<?php endforeach; ?>
	</ul>
<?php else: ?>
	<p><?php echo A11YC_LANG_NO_DOC ?></p>
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
				<li><a<?php echo A11YC_TARGET ?> href="<?php echo A11YC_DOC_URL.$rel_code ?>&amp;criterion=<?php echo \A11yc\Util::s($rel_criterion) ?>"><?php echo $yml['checks'][$rel_criterion][$rel_code]['name'] ?></a></li>
			<?php
				endforeach;
			endforeach;
			?>
	</ul>
<?php endif; ?>

<?php
	$criterion = $doc['criterion']['code'];
	if (isset($yml['criterions'][$criterion]['url'])):
?>
		<!-- understanding -->
	<h2><?php echo A11YC_LANG_UNDERSTANDING ?></h2>
	<p><a<?php echo A11YC_TARGET_OUT ?> href="<?php echo $yml['criterions'][$criterion]['url'] ?>"><?php echo $yml['criterions'][$criterion]['name'] ?></a></p>
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
