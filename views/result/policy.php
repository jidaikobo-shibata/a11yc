<?php
namespace A11yc;
// change version
if ($versions):
	$html = '';
	$html.= '<form action="'.$results_link.'" method="GET">';
	$html.= '<div><label for="a11yc_version">'.A11YC_LANG_RESULT_CHANGE_VERSION.'</label>';
	$html.= '<select name="a11yc_version" id="a11yc_version">';
	$html.= '<option value="">'.A11YC_LANG_RESULT_NEWEST_VERSION.'</option>';

	foreach ($versions as $version_name => $version):
		$selected = Input::get('a11yc_version', '') == $version_name ? ' selected="selected"' : '';
		$html.= '<option'.$selected.' value="'.$version_name.'">'.$version['name'].'</option>';
	endforeach;
	$html.= '</select>';
	$html.= '<input type="submit" value="'.A11YC_LANG_CTRL_SEND.'">';
	if (Input::get('a11yc_version', false)):
		$html.= '<a href="'.$results_link.'">'.A11YC_LANG_RESULT_NEWEST_VERSION.'</a>';
	endif;
	$html.= '</div></form>';
	echo $html;
endif;

// policy
echo $settings['policy'];

// implements
include('inc_criterions_checklist.php');

if ($settings['show_results']):
?>
<h2><?php echo A11YC_LANG_REPORT; ?></h2>
<p class="a11yc_link"><a href="<?php echo $report_link ?>"><?php echo A11YC_LANG_REPORT ?></a></p><?php
endif;
