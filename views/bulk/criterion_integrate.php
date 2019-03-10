<?php
namespace A11yc;

include(dirname(__DIR__).'/checklist/inc_submenu.php');

echo '<form action="'.A11YC_BULK_URL.'criterion&amp;focus='.$focus.'&amp;criterion='.$criterion.'&amp;integrate=1" method="POST">';
$vvv = $yml['criterions'][$criterion];
$iclchks = array();
$results = array();
$issues  = array();
$cs      = array();
$url     = '';
$page_id = '0';

include(dirname(__DIR__).'/checklist/inc_criterion_form.php');

echo '<div id="a11yc_submit">';
echo '<input type="hidden" value="1" />';
echo '<input type="submit" value="'.A11YC_LANG_CTRL_SEND.'" />';
echo '</div>';

echo '</form>';
