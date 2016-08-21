<?php if ($url == 'bulk'): ?>
	<form action="<?php echo A11YC_BULK_URL ?>" method="POST">
<?php else: ?>
	<form action="<?php echo A11YC_CHECKLIST_URL.$url ?>" method="POST">
<?php endif; ?>
<?php echo $form ?>
<input type="submit" value="submit" />
</form>
