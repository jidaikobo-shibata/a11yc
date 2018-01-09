<?php
/**
 * A11yc live validate
 *
 * @package    part of A11yc
 * @author     Jidaikobo Inc.
 * @license    The MIT License (MIT)
 * @copyright  Jidaikobo Inc.
 * @link       http://www.jidaikobo.com
 */
namespace A11yc;

// a11yc
if ( ! defined('A11YC_URL'))
{
	require (__DIR__.'/libs/a11yc/main.php');
}

// export is also used by post.php
Controller_Post::set_consts();
Controller_Post::load_language();

// session
Session::forge();
if ( ! Controller_Post::auth()) Util::error('invalid access');

// action
$url = Util::urldec(
	Input::post(
		'url',
		Input::get('url', '', FILTER_VALIDATE_URL),
		FILTER_VALIDATE_URL
	)
);

// render
$url = Input::get('url');
Controller_Post::Validation_Core(Util::urldec($url));
$html = View::fetch_tpl('post/render.php');

// replace raw head
$head = Validate::revert_html(Util::s(Validate::get_hl_html()));
$head = Controller_Post::replace_error_strs($head);
$head = mb_substr($head, 0, mb_strpos($head, '&lt;/head&gt;') + 13);
$head ='<div class="a11yc_live_head">'.str_replace(array("\n\r", "\n"), '<br />', $head).'</div>';
$html = mb_substr($html, mb_strpos($html, '</head>') + 7);
$raw_head = Controller_Post::replace_strs($url, htmlspecialchars_decode(View::fetch('target_html_head'), ENT_QUOTES));
$html = $raw_head.$html;

// add altered head
$html = str_replace(
	'</body>',
	$head.'</body>',
	$html
);

View::assign('body', $html, false);
View::display(array(
		'body.php',
	));
