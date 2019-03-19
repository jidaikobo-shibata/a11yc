<?php namespace A11yc; ?>

<tr>
	<th><label for="a11yc_pages"><?php echo A11YC_LANG_CHECKLIST_TARGETPAGE ?></label></th>
	<td>
		<ul>
		<?php
		foreach (Model\Page::fetchAll() as $page):
			$unpassed_criterions = array();
			foreach (Model\Result::fetch($page['url']) as $criterion_tmp => $v):
				if (Arr::get($v, 'result') != -1) continue;
				$unpassed_criterions[] = $criterion_tmp;
			endforeach;
			$criterions = json_encode($unpassed_criterions);
			$checked = in_array($page['dbid'], Arr::get($item, 'page_ids', array())) ? ' checked="checked"' : '';

			echo '<li data-unpassed_criterions=\''.$criterions.'\'><label><input'.$checked.' type="checkbox" name="page_ids[]" value="'.$page['dbid'].'">'.$page['title'].'<br>(<a href="'.$page['url'].'">'.$page['url'].'</a>)</label></li>'."\n";
		endforeach;
		?>
		</ul>
	</td>
</tr>
