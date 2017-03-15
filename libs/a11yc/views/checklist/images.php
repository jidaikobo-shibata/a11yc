<?php
namespace A11yc;

// call from post
$is_call_from_post = isset($is_call_from_post);

if ($images):
?>
<table border="1">
<thead>
<tr>
	<th>element</th>
	<th>image</th>
	<th>alt</th>
	<th>title</th>
	<th>role</th>
	<th>aria-*</th>
</tr>
</thead>
<?php foreach ($images as $v): ?>
<tr>
	<td>
	<?php echo $v['element'] ?>
	</td>
	<td style="background-color: #efefef;">
	<?php if ($v['uri']): ?>
	<img src="<?php echo $v['uri'] ?>" style="min-width:150px;">
	<?php endif; ?>
	</td>
	<td><?php
	if ($v['alt'] === NULL):
		echo '属性値が存在しません';
	elseif (empty($v['alt'])):
		echo '属性値が空です';
	elseif ($v['alt'] == '===a11yc_alt_of_blank_chars==='):
		echo '属性値が空白文字です';
	else:
		echo $v['alt'];
	endif;
	?></td>
	<td><?php echo $v['title'] ?></td>
	<td><?php echo $v['role'] ?></td>
	<td><?php
	foreach ($v['aria'] as $kk => $vv):
		echo $kk.' - '.$vv;
	endforeach;
	?></td>
</tr>
<?php endforeach; ?>
</table>
<?php else:
	echo '<p id="a11yc_validation_not_found_error"><span class="a11yc_icon_fa" role="presentation" aria-hidden="true"></span>'.A11YC_LANG_CHECKLIST_NOT_FOUND_ERR.'</p>';
endif; ?>
