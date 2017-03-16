<?php
namespace A11yc;

// call from post
$is_call_from_post = isset($is_call_from_post);

if ($images):
?>
<ul class="a11yc_cmt">
	<li><?php echo A11YC_LANG_CHECKLIST_IMPORTANT_EMP ?></li>
	<li><?php echo A11YC_LANG_CHECKLIST_IMPORTANT_EMP2 ?></li>
	<li><?php echo sprintf(A11YC_LANG_CHECKLIST_IMPORTANT_EMP3, '?a=doc&code=1-1-1b&criterion=1-1-1') ?></li>
</ul>

<table class="a11yc_image_list" summary="Image and alt">
<thead>
<tr>
	<th>importance</th>
	<th>element</th>
	<th>image</th>
	<th>alt</th>
	<th>title</th>
	<th>role</th>
	<th>aria-*</th>
</tr>
</thead>
<?php

foreach ($images as $v):

$classes = array();

// important
$important = $v['href'] ? A11YC_LANG_IMPORTANT : '';
$classes[] = $important ? 'important' : '';

// alt
$alt = Arr::get($v, 'attrs.alt', null);

$need_check = '';
if ($alt === NULL):
	$alt = '<em>'.A11YC_LANG_CHECKLIST_ALT_NULL.'</em>';
	$classes[] = 'error';
elseif (empty($alt)):
	$alt = '<em>'.A11YC_LANG_CHECKLIST_ALT_EMPTY.'</em>';
	$classes[] = $important ? 'error' : '';
	$need_check = $important ? A11YC_LANG_NEED_CHECK : '';
elseif ($alt == '===a11yc_alt_of_blank_chars==='):
	$alt = '<em>'.A11YC_LANG_CHECKLIST_ALT_BLANK.'</em>';
	$classes[] = $important ? 'error' : '';
	$need_check = $important ? A11YC_LANG_NEED_CHECK : '';
endif;

// row class
$class = '';
if ($classes):
	$class = ' class="'.join(' ', $classes).'"';
endif;

?>
<tr<?php echo $class; ?>>
	<td class="a11yc_image_importance"><?php
		echo $important ? '<strong>'.$important.'</strong>' : '';
		echo $need_check ? '<strong>'.$need_check.'</strong>' : '';
?></td>
	<td class="a11yc_image_element"><?php echo $v['element']; ?></td>
	<td class="a11yc_image_img">
		<div>
	<?php if ($v['attrs']['src']): ?>
			<img src="<?php echo $v['attrs']['src'] ?>" alt="" role="presentation">
	<?php endif; ?>
		</div>
	</td>
	<td class="a11yc_image_alt"><?php echo $alt; ?></td>
	<td class="a11yc_image_title"><?php echo Arr::get('attrs', 'title', '') ?></td>
	<td class="a11yc_image_role"><?php echo Arr::get('attrs', 'role', '') ?></td>
	<td class="a11yc_image_aria"><?php
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
