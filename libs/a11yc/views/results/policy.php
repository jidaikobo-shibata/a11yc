<?php
namespace A11yc;
// change version
if ($versions):
	$html = '';
	$html.= '<form action="'.$results_link.'" method="GET">';
	$html.= '<div><label for="a11yc_version">'.A11YC_LANG_RESULTS_CHANGE_VERSION.'</label>';
	$html.= '<select name="a11yc_version" id="a11yc_version">';
	$html.= '<option value="">'.A11YC_LANG_RESULTS_NEWEST_VERSION.'</option>';
	foreach ($versions as $version):
		$selected = Input::get('a11yc_version', '') == $version ? ' selected="selected"' : '';
		$html.= '<option'.$selected.' value="'.$version['version'].'">'.$version['name'].'</option>';
	endforeach;
	$html.= '</select>';
	$html.= '<input type="submit" value="'.A11YC_LANG_CTRL_SEND.'">';
	if (Input::get('a11yc_version', false)):
		$html.= '<a href="'.$results_link.'">'.A11YC_LANG_RESULTS_NEWEST_VERSION.'</a>';
	endif;
	$html.= '</div></form>';
	echo $html;
endif;

// policy
echo $policy;

// implements
include('criterions_checklist.php');

if ($settings['show_results']):
?>
<h2><?php echo A11YC_LANG_REPORT; ?></h2>
<p class="a11yc_link"><a href="<?php echo $report_link ?>"><?php echo A11YC_LANG_REPORT ?></a></p><?php
endif;
