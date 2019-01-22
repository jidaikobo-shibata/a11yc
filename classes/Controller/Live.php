<?php
/**
 * A11yc\Controller\Live
 *
 * @package    part of A11yc
 * @author     Jidaikobo Inc.
 * @license    The MIT License (MIT)
 * @copyright  Jidaikobo Inc.
 * @link       http://www.jidaikobo.com
 */
namespace A11yc\Controller;

use A11yc\Model;
use A11yc\Validate;

class Live
{
	/**
	 * action view
	 *
	 * @return Void
	 */
	public static function actionView()
	{
		$url = Util::enuniqueUri(Input::param('url', '', FILTER_VALIDATE_URL));
		static::view($url);
	}

	/**
	 * view
	 *
	 * @param String $url
	 * @return Void
	 */
	public static function view($url)
	{
		// keep head
		$html = Model\Html::fetch($url);
		if ($html === false) Util::error('failed to get HTML');
		$mod_head = self::modifyHead($html);

		// validate
		Validate::url($url);
		$hl_html = Validate\Get::highLightedHtml($url);

		// hl_head
		$hl_head = mb_substr($hl_html, 0, mb_strpos($hl_html, '&lt;/head&gt;') + 13);
		$hl_head = '<div class="a11yc_live_head">'.str_replace(array("\n\r", "\n"), '<br />', $hl_head).'</div>';

		// replace url and errors
		$hl_html = self::replaceUrls($url, $hl_html);
		$hl_html = self::replaceErrorStrs($hl_html);

		// replace head
		$result = $mod_head.mb_substr($hl_html, mb_strpos($hl_html, '</head>') + 7).$hl_head;

		// assign
		View::assign('body', $result, false);
	}

	/**
	 * modify head
	 *
	 * @param String|Bool $html
	 * @return String
	 */
	private static function modifyHead($html)
	{
		if ( ! is_string($html)) Util::error('invalid HTML was given');

		$head = mb_substr($html, 0, mb_strpos($html, '</head>') + 7);

		// css
		$head = str_replace(
			'</head>',
			'<link rel="stylesheet" type="text/css" media="all" href="'.A11YC_ASSETS_URL.'/css/a11yc_live.css" />'."\n".'</head>',
			$head
		);

		// jQuery
		if (strpos($head, 'jquery') === false)
		{
			$head = str_replace(
				'</head>',
				'<script type="text/javascript" src="'.A11YC_ASSETS_URL.'/js/jquery-1.11.1.min.js"></script>'."\n".'</head>',
				$head
			);
		}

		// js
		$head = str_replace(
			'</head>',
			'<script type="text/javascript" src="'.A11YC_ASSETS_URL.'/js/a11yc_live.js"></script>'."\n".'</head>',
			$head
		);

		return $head;
	}

	/**
	 * replace strs
	 *
	 * @param String $url
	 * @param String $html
	 * @return String
	 */
	public static function replaceUrls($url, $html)
	{

		$settings = Model\Setting::fetchAll();
		if ($settings['base_url'])
		{
			$root = $settings['base_url'];
		}
		else
		{
			$roots = explode('/', $url);
			$root = $roots[0].'//'.$roots[2];
		}
		$html = htmlspecialchars_decode($html, ENT_QUOTES);

		// check depth
		if ($url == $root)
		{
		}
		else
		{
// あとで！
		}

		// replace root relative
		$html = preg_replace(
			array(
				'/src *?= *?"\/(?!\/)/i',
				'/src *?= *?"(?!http|\/)/i',
				'/href *?= *?"\/(?!\/)/i',
				'/href *?= *?"(?!http|\/|#)/i'
			),
			array(
				'src="'.$root.'/',
				'src="'.$root.'/',
				'href="'.$root.'/',
				'href="'.$root.'/'
			),
			$html
		);

		return $html;
	}

	/**
	 * replace a11yc error strs
	 *
	 * @param String $html
	 * @return String
	 */
	private static function replaceErrorStrs($html)
	{
		// remove "back" link
		$html = preg_replace(
			'/\<a href="#index_.+?a11yc_back_link.+?\<\/a\>/i',
			'',
			$html
		);

		// replace strong to span
		$html = preg_replace(
			'/strong class="a11yc_level_(a+?)"/i',
			'span class="a11yc_live_error_wrapper a11yc_level_\1"',
			$html
		);
		$html = str_replace(
			'</strong><!-- a11yc_strong_end -->',
			'</span><!-- a11yc_strong_end -->',
			$html
		);

		// make live valid - style_for_structure
		$error_codes = array(
			'meanless_element',
			'style_for_structure',
			'invalid_tag',
			'suspicious_attributes',
			'titleless_frame',
//			'must_be_numeric_attr',
		);

		foreach ($error_codes as $error_code)
		{
			preg_match_all(
				'/\<span id="'.$error_code.'(.+?) class="([^"]+?)"\>(ERROR!|NOTICE)\<\/span\>\<span class="a11yc_live_error_wrapper a11yc_level_(.+?)"\>\<([^\>]+?)\>\<\/span\>\<!-- a11yc_strong_end --\>/',
				$html,
				$ms
			);

			preg_match_all(
				'/\<span id="'.$error_code.'([^\>]+?)\>(ERROR!|NOTICE)\<\/span\>\<span class="a11yc_live_error_wrapper a11yc_level_(.+?)"\>\<([^\>]+?)\>\<\/span\>\<!-- a11yc_strong_end --\>/',
				$html,
				$ms
			);

			if (isset($ms[0][0]))
			{
				foreach ($ms[0] as $k => $v)
				{
					$replace = '<span id="'.$error_code.''.$ms[1][$k].'>'.$ms[2][$k].'</span><span class="a11yc_live_error_wrapper a11yc_level_'.$ms[3][$k].'"></span><!-- a11yc_strong_end --><'.$ms[4][$k].'>';
					$html = str_replace($v, $replace, $html);
				}
			}
		}

		return $html;
	}
}
