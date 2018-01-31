<?php
/**
 * A11yc export csv file
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
$is_all = intval(Input::post('is_all', Input::get('is_all', false)));
$source = Input::post('source');

// error before DB access
if ( ! $url && ! $source && ! $is_all) Util::error('invalid access');

// database
Db::forge(array(
	'dbtype' => 'sqlite',
	'path' => __DIR__.'/db/db.sqlite',
));
Db::init_table();

// prepare messages
$yml = Yaml::fetch();

// get Pages
$pages = array();

// "is_all" is under construction
if ($is_all)
{
	$sql = 'SELECT * FROM '.A11YC_TABLE_PAGES.' WHERE ';
	//$sql.= '`done` = 1 and (`trash` = 0 OR `trash` is null)';
	$sql.= '(`trash` = 0 OR `trash` is null)';
	$sql.= Controller_Setup::curent_version_sql();
	$pages = Db::fetch_all($sql);
}
elseif ($url)
{
	$pages[0]['url'] = $url;
}
elseif ($source)
{
	$pages[0]['url'] = 'http://www.example.com/';
}
else
{
	Util::error('invalid access');
}

$csv = array();
$csv[] = array(
	'URL',
	'No.',
	A11YC_LANG_LEVEL,
	A11YC_LANG_IMPORTANCE,
	A11YC_LANG_CRITERION,
	A11YC_LANG_PAGES_CHECK,
	A11YC_LANG_CHECKLIST_SOURCE,
	A11YC_LANG_CHECKLIST_MEMO,
);

$codes = Validate::$codes;
unset($codes['link_check']);

// check and generate csv
foreach ($pages as $page)
{
	$url = $page['url'];
	if ($url != 'http://www.example.com/')
	{
		Guzzle::forge($url);
		Guzzle::instance($url)->set_config(
			'User-Agent',
			Util::s(Input::user_agent()).' Service/a11yc (+http://www.jidaikobo.com)'
		);

		// failed
		if (
			Guzzle::instance($url)->status_code == 401 ||
			Guzzle::instance($url)->errors
		)
		{
			continue;
		}

		// get HTML
		$target_html = Guzzle::instance($url)->is_html ? Guzzle::instance($url)->body : false;
	}
	else
	{
		$target_html = $source;
	}

	if ( ! $target_html) continue;

	// validate
	Validate::set_html($target_html);
	Crawl::set_target_path($url);
	foreach ($codes as $method => $class)
	{
		$class::$method();
	}

	// csv
	$n = 1;
	foreach (Validate::get_error_ids() as $err_code => $errs)
	{
		foreach ($errs as $key => $err)
		{
			// Yaml not exist
			$current_err = array();
			if ( ! isset($yml['errors'][$err_code]))
			{
				$current_err['message'] = Validate_Human::$humans[$err_code]['message'];
				$current_err['criterion'] = Validate_Human::$humans[$err_code]['criterion'];
				$current_err['code'] = Validate_Human::$humans[$err_code]['code'];
				if (Validate_Human::$humans[$err_code]['e_or_n'] == 'notice')
				{
					$current_err['notice'] = true;
				}
			}
			else
			{
				$current_err = $yml['errors'][$err_code];
			}

			$err_type = isset($current_err['notice']) ? 'notice' : 'error';

			// alt mention is not need. alt will be revealed
			if ($err_code == 'notice_img_exists') continue;

			// level
			$criterion = $current_err['criterion'];

			$csv[] = array(
				$url,
				$n,
				$yml['criterions'][$criterion]['level']['name'],
				$err_type,
				Util::key2code($current_err['criterion']),
				$current_err['message'],
				$err['id'],
				$err['str'] == $err['id'] ? '' : $err['str'],
			);
			$n++;
		}
	}

	// alt list
	foreach (Validate_Alt::get_images() as $v)
	{
		// message
		$alt = $v['attrs']['alt'];
		$alt_alert = '';

		// important
		if ($v['is_important'])
		{
			$alt_alert = A11YC_LANG_IMPORTANT;
		}

		// error
		if ($alt === NULL)
		{
			$alt_alert.= A11YC_LANG_NEED_CHECK.': '.A11YC_LANG_CHECKLIST_ALT_NULL;
		}
		elseif (empty($alt))
		{
			$alt_alert.= A11YC_LANG_NEED_CHECK.': '.A11YC_LANG_CHECKLIST_ALT_EMPTY;
		}
		elseif ($alt == '===a11yc_alt_of_blank_chars===')
		{
			$alt_alert.= A11YC_LANG_NEED_CHECK.': '.A11YC_LANG_CHECKLIST_ALT_BLANK;
		}

		$csv[] = array(
			$url,
			$n,
			'A',
			'notice',
			'1.1.1',
			$alt_alert,
			$v['attrs']['src'],
			$alt,
		);
		$n++;
	}

	$csv[] = array();
}

// export
$filename = 'a11yc.csv';
$filepath = '/tmp/'.$filename;
$fp = fopen($filepath, 'w');
if ($fp === FALSE) throw new Exception('failed to export');

foreach ($csv as $fields)
{
	mb_convert_variables('SJIS', 'UTF-8', $fields);
	fputcsv($fp, $fields, "\t");
}
fclose($fp);

header("HTTP/1.1 200 OK");
header('Content-Type: application/octet-stream');
header('Content-Length: '.filesize($filepath));
header('Content-Transfer-Encoding: binary');
header('Content-Disposition: attachment; filename='.$filename);
readfile($filepath);
exit();