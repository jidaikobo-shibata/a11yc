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
	<li>画像一覧で外部サーバの非SSLの画像を表示すると、SSLでなくなるのをどうするか考える（Camo？）</li>
</ul>

<h2><?php echo A11YC_LANG_POST_FEEDBACK_TITLE ?></h2>
<p><?php echo A11YC_LANG_POST_FEEDBACK_EXP ?></p>

<h2>Change Log</h2>

<dl>

<dt>4.0.3 (<time>2019-10-9</time>)</dt>
	<dd>Better Image List. Show text that exist with <code>&lt;img&gt;</code> in same <code>&lt;a&gt;</code> element.</dd>

<dt>4.0.2 (<time>2019-10-1</time>)</dt>
	<dd>Fix Check Bug of <code>alt</code> of <code>area</code>. thx <a href="https://twitter.com/yocco405">@yocco405</a></dd>

<dt>4.0.1 (<time>2019-7-4</time>)</dt>
	<dd>Add mention of F16 to <code>marquee</code>. thx <a href="https://twitter.com/yocco405">@yocco405</a></dd>

<dt>2.1.1 (<time>2018-10-16</time>)</dt>
	<dd>Change service name.</dd>
	<dd>Many Refactoring.</dd>

<dt>2.1.0 (<time>2018-10-02</time>)</dt>
	<dd>start to support <code>aria-label</code> and <code>aria-labelledby</code></dd>

<dt>2.0.9 (<time>2018-08-23</time>)</dt>
	<dd>fix DOCTYPE recognition</dd>
	<dd>better charset recognition</dd>
	<dd>add images list mode by get request</dd>

<dt>2.0.8 (<time>2018-08-17</time>)</dt>
	<dd>at html5, ignore check of existence of summary attribute</dd>

<dt>2.0.7 (<time>2018-08-09</time>)</dt>
	<dd>no space between attribute.</dd>
	<dd>better comment out logic.</dd>
	<dd>ignore CDATA section.</dd>

<dt>2.0.6 (<time>2018-08-05</time>)</dt>
	<dd>better <code>&lt;DOCTYPE&gt;</code> recognition.</dd>

<dt>2.0.5 (<time>2018-08-02</time>)</dt>
	<dd>at readme: content of <time> was invalid string (Y-n-j -> Y-m-d).</dd>
	<dd>at documentation: fix unexpectedlly escaped HTML and markup HTML by <code>&lt;code&gt;</code>.</dd>
	<dd>at labelless check: if action attribute was not exists, use <code>&lt;form&gt;</code> to indicate place.</dd>

<dt>2.0.4 (<time>2018-07-28</time>)</dt>
	<dd>Refine regular expression of extraction html tag.</dd>
	<dd>Became able to check non UTF-8 page.</dd>
	<dd>Update Japanese title of Techniques for WCAG 2.0.</dd>
	<dd>Better lang check (<a href="https://github.com/jidaikobo-shibata/a11yc/issues/2">#2</a>).</dd>

<dt>2.0.3 (<time>2018-07-26</time>)</dt>
	<dd>Add New check: fieldsetless and legendless.</dd>
	<dd>Add link to Techniques for WCAG 2.0 into error message.</dd>

<dt>2.0.2 (<time>2018-07-24</time>)</dt>
	<dd>Improve accuracy of getting attributes from html tag.</dd>

<dt>2.0.1 (<time>2018-07-23</time>)</dt>
	<dd>Omit checking way of close of empty element.</dd>

</dl>

<h2>Special Thanks</h2>

<ul>
	<li><a href="https://twitter.com/momdo_" title="without him, A11yc would be held more Bugs and problems.">@momdo_</a></li>
</ul>
