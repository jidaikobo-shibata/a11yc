<form action="<?php echo \A11yc\Util::uri() ?>" method="POST">
<label for="source">source</label>
<textarea name="source" id="source" style="width: 100%; min-height: 10em;"><?php echo $target_html ?></textarea>
<input type="submit" value="<?php echo A11YC_LANG_CTRL_SEND ?>">
</form>
<?php
if (\A11yc\Input::post()):
echo $result;
endif;
?>