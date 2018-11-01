<?php namespace A11yc; ?>

<ul class="a11yc_checklist_pages">
<?php
	$lis = array();
	$index_lists = array('all', 'yet', 'done', 'trash');
	foreach ($index_lists as $index_list):
		if ($index_list == 'all'):
			$class_str = ! $list ? ' class="current"' : '' ;
			$q = '';
		else:
			$class_str = $index_list == $list ? ' class="current"' : '' ;
			$q = '&amp;list='.$index_list;
		endif;

		$lis[] = "\t".'<li><a href="'.A11YC_PAGES_URL.$q.'"'.$class_str.'>'.constant('A11YC_LANG_PAGES_'.strtoupper($index_list)).'('.$count[$index_list].')</a></li>';
	endforeach;

	$lis[] = "\t".'<li><a href="'.A11YC_PAGES_ADD_URL.'">'.A11YC_LANG_CTRL_ADDNEW.'</a></li>';
	echo join("\n", $lis);
?>
</ul>
