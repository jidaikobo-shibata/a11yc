<!-- list -->
<h2 id="a11yc_checklist_index_title"><?php echo A11YC_LANG_PAGES_INDEX ?></h2>
<ul class="a11yc_checklist_pages">
<?php
	$index_lists = array('all', 'yet', 'done', 'trash');
	foreach ($index_lists as $index_list):
		if ($index_list == 'all'):
			$class_str = ! $list ? ' class="current"' : '' ;
			$q = '';
		else:
			$class_str = $index_list == $list ? ' class="current"' : '' ;
			$q = '&amp;list='.$index_list;
		endif;
		switch ($index_list):
			case 'yet':
				$cnt = $yetcnt['num'];
				break;
			case 'done':
				$cnt = $donecnt['num'];
				break;
			case 'trash':
				$cnt = $trashcnt['num'];
				break;
			default:
				$cnt = $allcnt['num'];
				break;
		endswitch;

		echo "\t".'<li><a href="'.A11YC_PAGES_URL.$q.'"'.$class_str.'>'.constant('A11YC_LANG_PAGES_'.strtoupper($index_list)).'('.$cnt.')</a></li>';
	endforeach;
?>
</ul>
<!-- /.a11yc_checklist_pages -->
<?php

// show search and order form
echo $search_form;

if ($pages):
	// index information
	echo '<p id="a11yc_pagenate_info">'.$index_information.'</p>';
	// pagination
	$pagination = '';
	if ($prev || $next):
		$pagination.= '<ul class="a11yc_pagenation">';
	if ($prev):
		$pagination.= '<li class="a11yc_pagenation_prev"><a href="'.$prev.'" class="a11yc_hasicon"><span class="a11yc_icon_tr_l a11yc_icon_fa" role="presentation" aria-hidden="true"></span><span>'.A11YC_LANG_CTRL_PREV.'</span></a></li>';
	else:
		$pagination.= '<li class="a11yc_pagenation_prev" role="presentation" aria-hidden="true"><span class="a11yc_icon_tr_l a11yc_icon_fa" role="presentation" aria-hidden="true"></span><span>'.A11YC_LANG_CTRL_PREV.'</span></li>';
	endif;
	if ($next):
		$pagination.= '<li class="a11yc_pagenation_next"><a href="'.$next.'" class="a11yc_hasicon"><span>'.A11YC_LANG_CTRL_NEXT.'</span><span class="a11yc_icon_tr_r a11yc_icon_fa" role="presentation" aria-hidden="true"></span></a></li>';
	else:
		$pagination.= '<li class="a11yc_pagenation_next" role="presentation" aria-hidden="true"><span>'.A11YC_LANG_CTRL_NEXT.'</span><span class="a11yc_icon_tr_r a11yc_icon_fa" role="presentation" aria-hidden="true"></span></li>';
	endif;
		$pagination.= '</ul><!-- /.a11yc_pagenation -->';
	endif;
	echo $pagination;
?>
	<table class="a11yc_table">
	<thead>
	<th>URL</th>
	<th scope="col" class="a11yc_result"><?php echo A11YC_LANG_LEVEL ?></th>
	<th scope="col" class="a11yc_result"><?php echo A11YC_LANG_CHECKLIST_DONE ?></th>
	<?php if ($list != 'trash'): ?>
	<th scope="col" class="a11yc_result"><?php echo A11YC_LANG_PAGES_CHECK ?></th>
	<?php endif; ?>
	<th scope="col" class="a11yc_result"><?php echo A11YC_LANG_PAGES_CTRL ?></th>
	<th scope="col" class="a11yc_result"><?php echo A11YC_LANG_PAGES_ADD_DATE ?></th>
	<th scope="col" class="a11yc_result"><?php echo A11YC_LANG_TEST_DATE ?></th>
	</thead>
	<tbody>
	<?php
	$i = 0;
	$no_url = isset($_GET['no_url']) ? \A11yc\Util::urldec($_GET['no_url']) : '';
	foreach ($pages as $page):
	$url = \A11yc\Util::s($page['url']);
	$not_found_class = $no_url == $url ? ' not_found_url' : '';
	$page_title = \A11yc\Util::s($page['page_title']);
	$class_str = ++$i%2==0 ? ' class="even'.$not_found_class.'"' : ' class="odd'.$not_found_class.'"';
	?>
	<tr<?php echo $class_str ?>>
		<th scope="row">
			<?php echo $no_url == $url ? '<div><strong>'.A11YC_LANG_CHECKLIST_PAGE_NOT_FOUND_ERR.'</strong></div>' : '' ?>
			<?php echo $page_title.'<br /><a href="'.$url.'">'.$url ?></a>
		</th>

		<td class="a11yc_result"><?php echo \A11yc\Util::num2str($page['level']) ?></td>
		<?php
			$done = @$page['done'] == 1 ? A11YC_LANG_PAGES_DONE : '' ;
		?>
		<td class="a11yc_result"><?php echo $done ?></td>
		<?php if ($list != 'trash'): ?>
		<td class="a11yc_result"><a href="<?php echo A11YC_CHECKLIST_URL.\A11yc\Util::urlenc($url) ?>" class="a11yc_hasicon"><span class="a11yc_skip"><?php echo A11YC_LANG_PAGES_CHECK ?></span><span class="a11yc_icon_check a11yc_icon_fa" role="presentation" aria-hidden="true"></span></a></td>
		<?php endif; ?>
		<?php if ($list == 'trash'): ?>
			<td class="a11yc_result">
				<a href="<?php echo A11YC_PAGES_URL ?>&amp;undel=1&amp;url=<?php echo \A11yc\Util::urlenc($url).$current_qs ?>"><?php echo A11YC_LANG_PAGES_UNDELETE ?></a>
				<a href="<?php echo A11YC_PAGES_URL ?>&amp;purge=1&amp;url=<?php echo \A11yc\Util::urlenc($url).$current_qs ?>"><?php echo A11YC_LANG_PAGES_PURGE ?></a>
			</td>

		<?php else: ?>
			<td class="a11yc_result"><a href="<?php echo A11YC_PAGES_URL ?>&amp;del=1&amp;url=<?php echo \A11yc\Util::urlenc($url).$current_qs ?>" class="a11yc_hasicon"><span class="a11yc_skip"><?php echo A11YC_LANG_PAGES_DELETE ?></span><span class="a11yc_icon_delete a11yc_icon_fa" role="presentation" aria-hidden="true"></span></a></td>
		<?php endif; ?>
		<td class="a11yc_result"><?php echo $page['add_date'] ? date('Y-m-d', strtotime($page['add_date'])) : '-' ?></td>
		<td class="a11yc_result"><?php echo $page['date'] ?></td>
	</tr>
	<?php endforeach; ?>
	</tbody>
	</table>
<?php
	// pagination
	echo $pagination;
else:
?>
	<p><?php echo A11YC_LANG_PAGES_NOT_FOUND ?></p>
<?php endif; ?>

<!-- add pages form -->
<form action="" method="POST">
<h2><label for="a11yc_add_pages"><?php echo A11YC_LANG_PAGES_URLS ?></label></h2>
<p><?php echo A11YC_LANG_PAGES_URL_FOR_EACH_LINE ?></p>

<textarea id="a11yc_add_pages" name="pages" rows="7" style="width: 100%;"><?php
if ($crawled):
foreach ($crawled as $v):
echo $v."\n";
endforeach;
endif;
?></textarea>
<input type="submit" value="<?php echo A11YC_LANG_PAGES_URLS_ADD ?>" />
</form>

<!-- get site urls -->
<form action="" method="POST">
<h2><label for="a11yc_get_urls"><?php echo A11YC_LANG_PAGES_GET_URLS ?></label></h2>
<p><?php echo A11YC_LANG_PAGES_GET_URLS_EXP ?></p>

<input type="text" name="get_urls" id="a11yc_get_urls" size="60" value="<?php echo $get_urls ?>" />
<input type="submit" value="<?php echo A11YC_LANG_PAGES_GET_URLS_BTN ?>" />
</form>
