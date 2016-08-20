<!-- target level -->
<h2><?php echo A11YC_LANG_TARGET_LEVEL ?></h2>
<p><?php echo \A11YC\Util::num2str($target_level) ?></p>

<!-- current level -->
<h2><?php echo A11YC_LANG_CURRENT_LEVEL_WEBPAGES ?></h2>
<?php
$site_level = \A11YC\Evaluate::check_site_level();
echo \A11YC\Evaluate::result_str($site_level, $target_level);
?>

<!-- selected method -->
<h2><?php echo A11YC_LANG_CANDIDATES0 ?></h2>
<?php
$arr = array(
  A11YC_LANG_CANDIDATES1,
  A11YC_LANG_CANDIDATES2,
  A11YC_LANG_CANDIDATES3,
  A11YC_LANG_CANDIDATES4,
);
?>
<p><?php echo $arr[$selected_method] ?></p>

<!-- number of checked -->
<h2><?php echo A11YC_LANG_NUM_OF_CHECKED ?></h2>
<p><?php echo $done['done'].' / '.$total['total'] ?></p>

<!-- unpassed pages -->
<h2><?php echo A11YC_LANG_UNPASSED_PAGES ?></h2>
<?php
$unpassed_pages = \A11yc\Evaluate::unpassed_pages($target_level);
if ($unpassed_pages):
?>
  <ul>
<?php
foreach ($unpassed_pages as $v):
$url = s($v['url']);
?>
  <li>
  <a href="<?php echo $url ?>"<?php echo A11YC_TARGET ?>><?php echo $url ?></a>
     (<a href="<?php echo A11YC_CHECKLIST_URL.$url ?>"<?php echo A11YC_TARGET ?>>check</a>)
  </li>
<?php endforeach; ?>
  <ul>
<?php else: ?>
  <p><?php echo A11YC_LANG_UNPASSED_PAGES_NO ?></p>
<?php endif; ?>

<!-- site results -->
<h2><?php echo A11YC_LANG_CHECKLIST_TITLE ?></h2>
<?php
list($results, $checked, $passed_flat) = \A11yc\Evaluate::evaluate(\A11yc\Evaluate::evaluate_total());
echo \A11yc\Checklist::part_result($results, $target_level);
?>
