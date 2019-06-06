<?php namespace A11yc; ?>

<p>ページを追加すると、以下のテストをすすめられます。</p>

<?php
$html = '';
$html.= '<table class="a11yc_table">';
$html.= '<thead><tr>';
$html.= '<th scope="row">テスト項目</th>';
$html.= '<th style="text-align: center;">共通</th>';

foreach ($pages as $page)
{
	$html.= '<th title="'.$page['title'].'" style="text-align: center;">'.$page['serial_num'].'</th>';
}
$html.= '</tr></thead>';

foreach ($processes as $pcode => $process)
{
	$html.= '<tr>';
	$html.= '<th>'.$pcode.' '.$process['title'].'</th>';

	$each_crr = Arr::get($current, 'common', array());
	$status = Arr::get(Arr::get($each_crr, $pcode, array()), 'status', '');
	$linktext = $status == 'common' ? '○' : '-';
	$statustext = $status == 'common' ? '共通部分済み' : '未チェック';
	$html.= '<td style="text-align: center;"><a href="?c=process&amp;a=form&amp;p='.$pcode.'&amp;m=common" title="'.$statustext.'">'.$linktext.'</a></td>';

	foreach ($pages as $page)
	{
		$each_crr = Arr::get($current, $page['url'], array());
		$status = Arr::get(Arr::get($each_crr, $pcode, array()), 'status', '');
		$linktext = $status == 'common' ? '△' : '-';
		$statustext = $status == 'common' ? '共通部分済み' : '未チェック';
		$linktext = $status == 'done' ? '○' : $linktext;
		$statustext = $status == 'done' ? '試験済み' : $statustext;
		$html.= '<td style="text-align: center;"><a href="?c=process&amp;a=form&amp;p='.$pcode.'&amp;m='.urlencode($page['url']).'" title="'.$statustext.'">'.$linktext.'</a></td>';
	}
	$html.= '</tr>';
}

echo $html;

?>
