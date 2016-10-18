<?php
/**
 * A11yc validate for ajax
 *
 * @package    part of A11yc
 * @version    1.0
 * @author     Jidaikobo Inc.
 * @license    WTFPL2.0
 * @copyright  Jidaikobo Inc.
 * @link       http:/www.jidaikobo.com
 */

// kontiki and a11yc
require (__DIR__.'/config/config.php');
require (__DIR__.'/libs/kontiki/main.php');
require (A11YC_PATH.'/main.php');

// database
\A11yc\Db::forge(array(
	'dbtype' => 'sqlite',
	'path' => __DIR__.'/db/db.sqlite',
));
\A11yc\Db::init_table();

// view
\A11yc\View::forge(A11YC_PATH.'/views/');

// assign
//\A11yc\Controller_Disclosure::total();
$url = isset($_GET['url']) ? urldecode($_GET['url']) : '';
$link_check = isset($_GET['link_check']) ? intval($_GET['link_check']) : '';
if ( ! $url) die('invalid access');
$errs = \A11yc\Controller_Checklist::validate_page($url, $link_check);
\A11yc\View::assign('errs', $errs, false);

// assign html source code
$raw = '';
if ($errs)
{
	$html = \A11yc\Util::fetch_html($url);
	$html = \A11yc\Util::s($html);
	$yml = \A11yc\Yaml::fetch();

	$replaces = array();
	$ignores = array_merge(\A11yc\Validate::$ignores, \A11yc\Validate::$ignores_comment_out);
	foreach ($ignores as $k => $ignore)
	{
		preg_match_all(\A11yc\Util::s($ignore), $html, $ms);
		if ($ms)
		{
			foreach ($ms[0] as $kk => $vv)
			{
				$original = $vv;
				$replaced = hash("sha256", $vv);
				$replaces[$k][$kk] = array(
					'original' => $original,
					'replaced' => $replaced,
				);
				$html = str_replace($original, $replaced, $html);
			}
		}
	}

	foreach (\A11yc\Validate::get_error_ids() as $eid => $v)
	{
		foreach($v as $id => $vv)
		{
			$html = str_replace(
				$vv['id'],
				'<strong id="a11yc_validate_'.$id.'" title="'.$yml['errors'][$eid]['message'].'('.str_replace( '-', '.', $yml['errors'][$eid]['criterion'] ).' '.$yml['criterions'][$yml['errors'][$eid]['criterion']]['level']['name'].')" tabindex="0">'.$vv['id'].'</strong>',
				$html);
		}
	}


/*
	foreach (\A11yc\Validate::get_errors() as $id => $v)
	{
		$html = str_replace(
			$v,
			'<strong id="a11yc_validate_'.$id.'">'.$v.'</strong>',
			$html);
	}
*/
	foreach ($replaces as $v)
	{
		foreach ($v as $vv)
		{
			$html = str_replace(
				$vv['replaced'],
				'<span style="color:#900">'.$vv['original'].'</span>',
				$html);
		}
	}

	$lines = explode("\n", $html);
	$lines = array_map(function($v){return $v.'<br>';}, $lines);
	$raw = join("\n", $lines);
}
\A11yc\View::assign('raw', $raw, false);

\A11yc\View::display(array('checklist/validate.php'));
