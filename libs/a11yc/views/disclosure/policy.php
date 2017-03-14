<?php
namespace A11yc;
// change version
if ($versions):
	$html = '';
	$html.= '<form action="'.$disclosure_link.'" method="GET">';
	$html.= '<div><label for="a11yc_version">'.A11YC_LANG_DISCLOSURE_CHANGE_VERSION.'</label>';
	$html.= '<select name="a11yc_version" id="a11yc_version">';
	foreach ($versions as $version):
		$selected = Input::get('a11yc_version', '') == $version ? ' selected="selected"' : '';
		$html.= '<option'.$selected.' value="'.$version.'">'.date('Y-m-d', strtotime($version)).'</option>';
	endforeach;
	$html.= '</select>';
	$html.= '<input type="submit" value="'.A11YC_LANG_CTRL_SEND.'">';
	if (Input::get('a11yc_version', false)):
		$html.= '<a href="'.$disclosure_link.'">'.A11YC_LANG_DISCLOSURE_NEWEST_VERSION.'</a>';
	endif;
	$html.= '</div></form>';
	echo $html;
endif;

// policy
echo $policy;
?>

<h2><?php echo A11YC_LANG_REPORT; ?></h2>
<p class="a11yc_link"><a href="<?php echo $report_link ?>"><?php echo A11YC_LANG_REPORT ?></a></p>