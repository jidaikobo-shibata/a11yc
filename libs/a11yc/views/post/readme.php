<?php namespace A11yc; ?>
<?php echo sprintf(A11YC_LANG_POST_HOWTO, Util::removeQueryStrings(Util::uri())) ?>

<h2><?php echo A11YC_LANG_POST_SERVICE_NAME_TITLE ?></h2>
<p><?php echo A11YC_LANG_POST_SERVICE_NAME_EXP ?></p>

<h2><?php echo A11YC_LANG_POST_CONDITION_TITLE ?></h2>
<ul>
	<li><?php echo A11YC_LANG_POST_CONDITION_EXP_FREE ?></li>
	<li><?php echo A11YC_LANG_POST_CONDITION_EXP_10MIN ?></li>
	<li><?php echo A11YC_LANG_POST_CONDITION_EXP_24H ?></li>
</ul>

<h2><?php echo A11YC_LANG_POST_COLLECTION_TITLE ?></h2>
<ul>
	<li><?php echo A11YC_LANG_POST_COLLECTION_GOOGLE ?></li>
	<li><?php echo A11YC_LANG_POST_COLLECTION_IP ?></li>
</ul>

<h2><?php echo A11YC_LANG_POST_VENDOR_TITLE ?></h2>
<ul id="a11yc_readme_vendor">
	<li><a href="http://www.jidaikobo.com"><img src="<?php echo A11YC_ASSETS_URL ?>/img/logo_author.png" class="a11yc_logo_author" alt="<?php echo A11YC_LANG_CENTER_LOGO ?>"><?php echo A11YC_LANG_POST_VENDOR_JIDAIKOBO ?></a></li>
	<li><a href="https://twitter.com/jidaikobo"><img src="<?php echo A11YC_ASSETS_URL ?>/img/Twitter_Logo_Blue.png" class="a11yc_logo_author" alt="Twitter Logo"><?php echo A11YC_LANG_POST_VENDOR_JIDAIKOBO_TWITTER ?></a></li>
</ul>

<h2 class="clear"><?php echo A11YC_LANG_POST_TECH_TITLE ?></h2>
<ul>
	<li><?php echo A11YC_LANG_POST_TECH_A11YC ?></li>
	<li><?php echo A11YC_LANG_POST_TECH_A11YC_ADD ?></li>
	<li><?php echo A11YC_LANG_POST_TECH_JWP_A11YC ?></li>
	<li><?php echo A11YC_LANG_POST_TECH_JWP_A11YC_ADD ?></li>
</ul>

<h2>TODO</h2>
<ul>
	<li><code>figure</code>などHTML5における<code>img</code>要素の<code>alt</code>省略条件の加味</li>
	<li><code>aria-*</code>にどこまで対応するか悩む</li>
	<li>SVGをどうするか考える</li>
	<li>画像一覧で外部サーバの非SSLの画像を表示すると、SSLでなくなるのをどうするか考える（Camo？）</li>
</ul>

<h2><?php echo A11YC_LANG_POST_FEEDBACK_TITLE ?></h2>
<p><?php echo A11YC_LANG_POST_FEEDBACK_EXP ?></p>

<h2>Change Log</h2>

<dl>

<dt>2.0.4 (<time>2018-7-28</time>)</dt>
	<dd>Refine regular expression of extraction html tag.</dd>
	<dd>Became able to check non UTF-8 page.</dd>
	<dd>Update Japanese title of Techniques for WCAG 2.0.</dd>

<dt>2.0.3 (<time>2018-7-26</time>)</dt>
	<dd>Add New check: fieldsetless and legendless.</dd>
	<dd>Add link to Techniques for WCAG 2.0 into error message.</dd>

<dt>2.0.2 (<time>2018-7-24</time>)</dt>
	<dd>Improve accuracy of getting attributes from html tag (thx <a href="https://twitter.com/momdo_">@momdo_</a>).</dd>

<dt>2.0.1 (<time>2018-7-23</time>)</dt>
	<dd>Omit checking way of close of empty element (thx <a href="https://twitter.com/momdo_">@momdo_</a>).</dd>

</dl>
