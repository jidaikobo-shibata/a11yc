<?php
namespace A11yc;
echo $processes[$p]['exp'];
?>

<form action="?c=process&amp;a=form&amp;p=<?php echo $p ?>&amp;m=<?php echo $m ?>" method="POST">

<?php
// loop start
foreach ($processes[$p]['processes'] as $pp => $v):

echo '<h2>'.$pp.': '.$v['title'].'</h2>';

echo '<ol>';
foreach ($v['procedure'] as $vv):
	if (is_array($vv)):
		echo '<ol style="list-style-type: lower-alpha">';
		foreach ($vv as $vvv):
			echo '<li>'.$vvv.'</li>';
		endforeach;
		echo '</ol>';
	else:
		echo '<li>'.$vv;
		continue;
	endif;
	echo '</li>';
endforeach;
echo '</ol>';
?>

<?php $idfor = $pp.'_result'; ?>
<h3><label for="<?php echo $idfor ?>">試験結果</label></h3>
<select id="<?php echo $idfor ?>" name="vals[<?php echo $pp ?>][result]">
<?php
$results = array(
	'yet' => '未テスト',
	'dna' => '適用なし',
	'false' => '不適合',
	'true' => '適合'
);
foreach ($results as $kk => $vv):
	$selected = $kk == Arr::get(Arr::get($current, $pp, array()), 'result', '') ? ' selected="selected"' : '';
	echo '<option value="'.$kk.'"'.$selected.'>'.$vv.'</option>';
endforeach;
?>
</select>

<?php if (isset($v['techs'])): ?>
<h3>関連する達成方法/不適合事例</h3>
<ul>
<?php
foreach ($v['techs'] as $tech):
	$checked = in_array($tech, Arr::get(Arr::get($current, $pp, array()), 'techs', array())) ? ' checked="checked"' : '';
	echo '<li><label><input type="checkbox" name="vals['.$pp.'][techs][]" value="'.$tech.'"'.$checked.'> '.$techs[$tech]['title'].'</label> (<a href="'.A11YC_REF_WCAG20_TECH_URL.$tech.'.html" target="_blank">'.$tech.'</a>)</li>';
endforeach;
?>
</ul>
<?php endif; ?>

<?php $idfor = $pp.'_memo'; ?>
<h3><label for="<?php echo $idfor ?>">メモ</label></h3>
<textarea id="<?php echo $idfor ?>" name="vals[<?php echo $pp ?>][memo]" cols="35" rows="7"><?php
echo Arr::get(Arr::get($current, $pp, array()), 'memo', '');
?></textarea>

<?php
endforeach;
?>
<div id="a11yc_submit">
<a href="?c=process&a=index">一覧に戻る</a>
<input type="submit" value="保存">
</div>
</form>
