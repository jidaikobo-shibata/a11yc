<?php
namespace A11yc;

/* variables */

$icls = Model\Icl::fetch4ImplementChecklist();
$techs = Yaml::each('techs');

/* template */

$html = '';
$html.= '<h2>'.A11YC_LANG_CHECKLIST_IMPLEMENTSLIST_TITLE.'</h2>';
$html.= '<p>'.A11YC_LANG_ICL_REASON.'</p>';

foreach (Yaml::each('criterions') as $criterion => $criterion_vals):
	if ( ! isset($cs[$criterion])) continue;

	// implement exists?
	$implements = array();
	foreach ($icls[$criterion] as $icls_id => $situation):
		if ( ! isset($implements[$icls_id])) $implements[$icls_id] = array();
		foreach ($situation['implements'] as $sit):
			$implements[$icls_id] = array_merge($implements[$icls_id], $sit['techs']);
		endforeach;
	endforeach;
	if (empty($implements)) continue;

	// check exists?
	$is_checked = true;
	foreach ($implements as $each_techs):
		if (array_diff($cs[$criterion], $each_techs)):
			$is_checked = false;
			break;
		endif;
	endforeach;

	$non_implement_check = false;
	foreach ($cs[$criterion] as $each_id):
		foreach ($icls[$criterion] as $each_icl):
			foreach ($each_icl['implements'] as $each_implement):
				if ($each_implement['id'] == $each_id):
					$is_checked = true;
					$non_implement_check = $each_id;
					break;
				endif;
			endforeach;
		endforeach;
	endforeach;

	if ( ! $is_checked) continue;

	$html.= '<h3>'.Util::key2code($criterion).' '.$criterion_vals['name'].'</h3>';

	// main loop
	foreach ($icls[$criterion] as $sit):
		if (isset($sit['title'])):
			$html.= '<h4>'.$sit['title'].'</h4>';
		endif;

		foreach ($sit['implements'] as $implement):
			$ul = '';
			foreach ($implement['techs'] as $tech):
				if ( ! isset($techs[$tech])) continue;
				if ( ! in_array($tech, $cs[$criterion])) continue;

				$ul.= '<li>'.$techs[$tech]['title'].'</li>';
			endforeach;

			if (empty($ul) && $non_implement_check == $implement['id']):
				$ul.= '<li>'.A11YC_LANG_CHECKLIST_IMPLEMENT_CHECK.'</li>';
			endif;

			if (empty($ul)) continue;

			$html.= '<p>'.$implement['title'].'</p>';
			$html.= '<ul>'.$ul.'</ul>';
		endforeach;
	endforeach;

endforeach;
echo $html;
