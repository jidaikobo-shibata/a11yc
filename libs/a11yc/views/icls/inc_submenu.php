<?php namespace A11yc; ?>
<ul>
	<li><a href="?c=icls&amp;a=index">実装チェックリスト</a></li>
<?php if (empty(Model\Settings::fetch('is_waic_imported'))): ?>
	<li><a href="?c=icls&amp;a=import">WAICの実装チェックリストを取り込む</a></li>
<?php endif; ?>
</ul>
