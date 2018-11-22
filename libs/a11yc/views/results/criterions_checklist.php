<?php namespace A11yc; ?>
<!-- site results -->
<?php if (isset($result) && $result): ?>
<h2><?php echo A11YC_LANG_CHECKLIST_CRITERIONSLIST_TITLE ?></h2>
<?php
echo $result;
endif;
if (isset($additional) && $additional): ?>
<h2><?php echo A11YC_LANG_CHECKLIST_CONFORMANCE_ADDITIONAL ?></h2>
<?php
echo $additional;
endif;
