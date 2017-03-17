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
	<th><?php echo A11YC_LANG_IMAGE ?></th>
	<th><?php echo A11YC_LANG_IMPORTANCE ?></th>
	<th><?php echo A11YC_LANG_ELEMENT ?></th>
	<th>Alt</th>
	<th><?php echo A11YC_LANG_ATTRS ?></th>
</tr>
</thead>
<?php

foreach ($images as $v):

$classes = array();

// important
$important = $v['href'] ? A11YC_LANG_IMPORTANT : '';
$classes[] = $important ? 'a11yc_important' : '';

// alt
$alt = Arr::get($v, 'attrs.alt', null);

$need_check = '';
if ($alt === NULL):
	$alt = '<em>'.A11YC_LANG_CHECKLIST_ALT_NULL.'</em>';
	$classes[] = 'a11yc_error';
	$need_check = A11YC_LANG_NEED_CHECK;
elseif (empty($alt)):
	$alt = '<em>'.A11YC_LANG_CHECKLIST_ALT_EMPTY.'</em>';
	$classes[] = $important ? 'a11yc_error' : '';
	$need_check = $important ? A11YC_LANG_NEED_CHECK : '';
elseif ($alt == '===a11yc_alt_of_blank_chars==='):
	$alt = '<em>'.A11YC_LANG_CHECKLIST_ALT_BLANK.'</em>';
	$classes[] = $important ? 'a11yc_error' : '';
	$need_check = $important ? A11YC_LANG_NEED_CHECK : '';
endif;

// row class
$class = '';
if ($classes):
	$class = ' class="'.join(' ', $classes).'"';
endif;

// attribute error
$attr_err = '';
foreach ($v['attrs'] as $kk => $vv):
	if ( ! in_array($kk, array('width', 'height'))) continue;
	if (is_numeric($vv)) continue;
	$attr_err.= '<em>'.sprintf(A11YC_LANG_CHECKLIST_MUST_BE_NUMERIC, $kk).'</em>';
endforeach;

?>
<tr<?php echo $class; ?>>
	<th class="a11yc_image_img">
		<div>
	<?php if (isset($v['attrs']['src']) && $v['attrs']['src']): ?>
			<img src="<?php echo $v['attrs']['src'] ?>" alt="" role="presentation">
			<?php echo '<span class="a11yc_image_src">'.basename($v['attrs']['src']).'</span>' ?>
	<?php elseif (in_array($v['element'], array('img', 'input'))):
		echo '<em>'.A11YC_LANG_CHECKLIST_SRC_NONE.'</em>';
	endif; ?>
		</div>
	</th>
	<td class="a11yc_image_importance"><?php
		echo $important ? '<strong>'.$important.'</strong>' : '';
		echo $need_check ? '<strong>'.$need_check.'</strong>' : '';
?></td>
	<td class="a11yc_image_element"><?php echo $v['element']; ?></td>
	<td class="a11yc_image_alt"><?php echo $alt; ?></td>
	<td class="a11yc_image_attrs">
<?php
	$attrs = array();
	$max = 20;

	// parent attrs
	foreach (array('tabindex', 'aria-hidden') as $vv):
		if (isset($v[$vv]) && $v[$vv]):
			$key = mb_strlen($vv) <= $max ? $vv : '<span title="'.$vv.'">'.Util::truncate($vv, 10).'</span>' ;
			$val = mb_strlen($v[$vv]) <= $max ? $v[$vv] : '<span title="'.$v[$vv].'">'.Util::truncate($v[$vv], 10).'</span>' ;

			$attrs[] = '<li>'.$key.' = "'.$val.'"<span class="a11yc_image_parent">parent</span></li>';
		endif;
	endforeach;

	// self attrs
	foreach ($v['attrs'] as $kk => $vv):
		if (in_array($kk, array('suspicious_end_quote', 'newline', 'alt', 'src'))) continue;
		$key = mb_strlen($kk) <= $max ? $kk : '<span title="'.$kk.'">'.Util::truncate($kk, 10).'</span>' ;
		$val = mb_strlen($vv) <= $max ? $vv : '<span title="'.$vv.'">'.Util::truncate($vv, 10).'</span>' ;

		$attrs[] = '<li>'.$key.' = "'.$val.'"</li>';
	endforeach;
	if ($attrs):
		echo '<ul>'.join($attrs).'</ul>';
	endif;
	echo $attr_err;
?>
</td>
</tr>
<?php endforeach; ?>
</table>
<?php else:
	echo '<p id="a11yc_validation_not_found_error"><span class="a11yc_icon_fa" role="presentation" aria-hidden="true"></span>'.A11YC_LANG_CHECKLIST_NOT_FOUND_ERR.'</p>';
endif; ?>
