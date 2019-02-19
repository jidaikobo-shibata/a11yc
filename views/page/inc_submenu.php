<?php namespace A11yc; ?>

<ul class="a11yc_submenu ">
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

		$lis[] = "\t".'<li><a href="'.A11YC_PAGE_URL.'index'.$q.'"'.$class_str.'>'.constant('A11YC_LANG_CTRL_'.strtoupper($index_list)).'('.$count[$index_list].')</a></li>';
	endforeach;

	$lis[] = "\t".'<li><a href="'.A11YC_PAGE_URL.'add">'.A11YC_LANG_CTRL_ADDNEW.'</a></li>';
	echo join("\n", $lis);
?>
	<li><a href="<?php echo A11YC_DOWNLOAD_URL ?>csv"><?php echo A11YC_LANG_EXPORT_CSV ?></a></li>
</ul>
