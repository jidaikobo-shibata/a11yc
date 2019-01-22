<?php
namespace A11yc;

$html = '';

if ( ! $is_view):
	$html.= $submenu;
	$html.= '<form action="'.A11YC_ICL_URL.'index" method="POST">';
endif;

foreach (Yaml::each('criterions') as $criterion => $criterion_vals):

	// implement exists?
	$implements = array();
	if (isset($icls[$criterion])):
		foreach ($icls[$criterion] as $icls_id => $situation):
			if ( ! isset($implements[$icls_id])) $implements[$icls_id] = array();
			foreach ($situation['implements'] as $sit):
				$implements[$icls_id] = array_merge($implements[$icls_id], $sit['techs']);
			endforeach;
		endforeach;
	endif;
	if (empty($implements)) continue;

	$html.= '<h2>'.Util::key2code($criterion).' '.$criterion_vals['name'].' ['.$criterion_vals['level']['name'].']</h2>';

	// main loop
	foreach ($icls[$criterion] as $sit):
		if (isset($sit['title'])):
			$html.= '<h3>'.$sit['title'].'</h3>';
		endif;

		$html.= '<table class="a11yc_table">';
		$html.= '<thead>';
		$html.= '<tr>';
		if ( ! $is_view):
			$html.= '<th scope="col">'.A11YC_LANG_CTRL_CHECK.'</th>';
		endif;
		$html.= '<th scope="col" style="width:30%;">'.A11YC_LANG_ICL_IMPLEMENT.'</th>';
		$html.= '<th scope="col" style="width:12%;">'.A11YC_LANG_ICL_ID.'</th>';
		$html.= '<th scope="col">'.A11YC_LANG_ICL_RELATED.'</th>';
		$html.= '<th scope="col" style="width:30%;">'.A11YC_LANG_ICL_VALIDATE.'</th>';
		if ( ! $is_view):
			$html.= '<th scope="col">'.A11YC_LANG_CTRL_ACT.'</th>';
		endif;
		$html.= '</tr>';
		$html.= '</thead>';

		foreach ($sit['implements'] as $implement):

			$id = $implement['id'];
			$checked = ! $is_view && in_array($id, $checks) ? ' checked="checked"' : '';

			// title
			$html.= '<tr><td>';
			if ( ! $is_view):
				$html.= '<input type="checkbox" name="icls[]" id="icls_'.$id.'" data-level="'.$implement['level'].'" value="'.$id.'"'.$checked.'></td>';
				$html.= '<td><label for="icls_'.$id.'">'.$implement['title'].'</label>';
			else:
				$html.= $implement['title'];
			endif;
			$html.= '</td><td>';

			// identifier
			$html.= $implement['identifier'];
			$html.= '</td><td>';

			// techs
			$ul = '';
			foreach ($implement['techs'] as $tech):
				if ( ! isset($techs[$tech])) continue;

				$ul.= '<li>'.$techs[$tech]['title'].'</li>';
			endforeach;
			$html.= ! empty($ul) ? '<ul>'.$ul.'</ul>' : '';
			$html.= '</td><td>';

			// inspection
			$html.= $implement['inspection'];
			$html.= '</td>';

			// ctrl
			if ( ! $is_view):
				$html.= '<td class="a11yc_result" style="white-space: nowrap;">';
				if (Arr::get($implement, 'trash', 0) != 0):
					$html.= '<a href="'.A11YC_ICL_URL.'undelete&amp;id='.intval($implement['id']).'">'.A11YC_LANG_CTRL_UNDELETE.'</a> - ';
					$html.= '<a href="'.A11YC_ICL_URL.'purge&amp;id='.intval($implement['id']).'" data-a11yc-confirm="'.sprintf(A11YC_LANG_CTRL_CONFIRM, A11YC_LANG_CTRL_PURGE).'">'.A11YC_LANG_CTRL_PURGE.'</a>';
				else:
					$html.= '<a href="'.A11YC_ICL_URL.'edit&amp;id='.intval($implement['id']).'">'.A11YC_LANG_CTRL_LABEL_EDIT.'</a>'.' - ';
					$html.= '<a href="'.A11YC_ICL_URL.'delete&amp;id='.intval($implement['id']).'">'.A11YC_LANG_CTRL_DELETE.'</a>';
				endif;
				$html.= '</td>';
			endif;

			$html.= '</tr>';
		endforeach;

		$html.= '</table>';
	endforeach;

endforeach;

if ( ! $is_view):
	$html.= '<div id="a11yc_submit">';
	$html.= '<input type="submit" value="'.A11YC_LANG_CTRL_SEND.'">';
	$html.= '</div>';
	$html.= '</form>';
endif;

echo $html;
