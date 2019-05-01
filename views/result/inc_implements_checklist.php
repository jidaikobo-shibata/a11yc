<?php
namespace A11yc;

/* variables */

$icls = Model\Icl::fetchAll(true, true);
$icltree = Model\Icl::fetchTree();
// $iclchk = Model\Iclchk::fetch($url);
$techs = Yaml::each('techs');
$criterions = Yaml::each('criterions');
$iclopts = Values::iclOptions();

/* template */

$html = '';

foreach ($icltree as $criterion => $parents):
	$html.= '<div class="no_page_break"><h3>'.Util::key2code($criterions[$criterion]['code']).' '.$criterions[$criterion]['name'].'</h3>';

	$html.= '<table class="a11yc_table">';
	foreach ($parents as $pid => $ids):
		$html.= '<thead>';
		$html.= $pid != 'none' ? '<tr><th colspan="4">'.Arr::get($icls[$pid], 'title_short', Arr::get($icls[$pid], 'title', ''))."</th></tr>\n" : "\n";
		$html.= '<tr>';
		$html.= '<th>'.A11YC_LANG_ICL_IMPLEMENT.'</th>';
		$html.= '<th style="">'.A11YC_LANG_EXIST.'</th>';
		$html.= '<th style="">'.A11YC_LANG_PASS.'</th>';
		$html.= '<th style="">'.A11YC_LANG_ICL_TECHS.'</th>';
		$html.= '</tr>';
		$html.= '</thead>';

		foreach ($ids as $id):
			$exist = $iclopts[1];
			$confirm = '';
			if (isset($iclchks[$id])):
				$exist = $iclchks[$id] == 1 ? $iclopts[1] : A11YC_LANG_EXIST ;
				if ($iclchks[$id] != 1):
					$confirm = $iclchks[$id] == 2 ? $iclopts[2] : $iclopts[-1] ;
				endif;
			endif;

			$html.= '<tr><th  class="implement_title">'.$icls[$id]['title_short']."</th>\n";
			$html.= '<td class="a11yc_result_exist">'.$exist.'</td>';
			$html.= '<td class="a11yc_result_exist">'.$confirm.'</td>';
			$html.= '<td>';
			$lis = '';
			foreach (Arr::get($cs, $id, array()) as $tech):
				$lis.= '<li>'.$tech.'</li>';
			endforeach;
			$html.= empty($lis) ? '' : '<ul>'.$lis.'</ul>';
			$html.= '</td>';
			$html.= '<tr>';
		endforeach;

	endforeach;
	$html.= '</table></div><!-- /.no_page_break -->';
endforeach;

if ( ! empty($html)):
	$html = '<h2>'.A11YC_LANG_CHECKLIST_IMPLEMENTSLIST_TITLE.'</h2><p>'.A11YC_LANG_ICL_REASON_EXP.'</p>'.$html;
endif;

echo $html;
