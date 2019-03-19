<?php
namespace A11yc;
include('inc_submenu.php');

//A11YC_LANG_SITECHECK_RELATED_RESULTS

if ($pages):
	$html = '';
	$html.= '<p>'.count($pages).'/'.$total.'</p>';
	$html.= '<table class="a11yc_table">';

	$html.= '<thead>';
	$html.= '<tr>';
	$html.= '<th style="width: 80%">'.A11YC_LANG_CHECKLIST_TARGETPAGE.'</th>';
	$html.= '<th style="text-align: center;">'.A11YC_LANG_CTRL_CHECK.'</th>';
	$html.= '<th style="text-align: center;">'.A11YC_LANG_CTRL_ACT.'</th>';
	$html.= '</tr>';
	$html.= '</thead>';

	foreach ($pages as $page):
		$html.= '<tr>';
		$html.= '<th>';
		$html.= $page['title'].'<br><a href="'.$page['url'].'">'.$page['url'].'</a>';
		$html.= '</th>';

		$html.= '<td class="a11yc_result" style="text-align: center;">';
		$html.= '<a href="'.A11YC_CHECKLIST_URL.Util::urlenc($page['url']).'" class="a11yc_hasicon"><span class="a11yc_skip"><?php echo A11YC_LANG_CTRL_CHECK ?></span><span class="a11yc_icon_check a11yc_icon_fa" role="presentation" aria-hidden="true"></span></a>';
		$html.= '</td>';
		$html.='<td class="a11yc_result" style="text-align: center;"><a href="'.A11YC_PAGE_URL.'edit&amp;url='.Util::urlenc($page['url']).'" class="a11yc_hasicon">'.A11YC_LANG_CTRL_ACT.'</a></td>';
		$html.= '</tr>';
	endforeach;
	$html.= '</table>';
	echo $html;
elseif (Input::isPostExists()):
	echo '<p>'.A11YC_LANG_PAGE_NOT_FOUND.'</p>';
endif;
