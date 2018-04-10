<?php
/**
 * A11yc\Controller\Export
 *
 * @package    part of A11yc
 * @author     Jidaikobo Inc.
 * @license    The MIT License (MIT)
 * @copyright  Jidaikobo Inc.
 * @link       http://www.jidaikobo.com
 */
namespace A11yc\Controller;

use A11yc\Model;

class Export
{
	/**
	 * action csv
	 *
	 * @return Void
	 */
	public static function actionCsv()
	{
		$url = Util::enuniqueUri(Input::param('url', '', FILTER_VALIDATE_URL));
		static::csv($url);
	}

	/**
	 * csv
	 *
	 * @param  String|Array $url
	 * @return Void
	 */
	public static function csv($url)
	{
		// get Pages
		if ( ! is_array($url))
		{
			$urls[] = $url;
		}
		else
		{
			$urls = $url;
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

		// check and generate csv
		foreach ($urls as $url)
		{
			$html = Model\Html::getHtml($url);
			if ( ! $html) continue;

			// validate
			Validate::url($url);

			// csv
			$n = 1;
			$csv = self::addErr2Csv($url, $csv, $n);
			$csv = self::addImgs2Csv($url, $csv, $n);
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
	}

	/**
	 * add errors to csv
	 *
	 * @param  String  $url
	 * @param  Array   $csv
	 * @param  Integer $n
	 * @return Array
	 */
	private static function addErr2Csv($url, $csv, $n)
	{
		$yml = Yaml::fetch();
		foreach (Validate::getErrorIds($url) as $err_code => $errs)
		{
			foreach ($errs as $err)
			{
				// Yaml not exist
				$current_err = array();
				if ( ! isset($yml['errors'][$err_code]))
				{
					$issue = Model\Issues::fetch4Validation($url, $err['str']);
					if ( ! $issue) return;
					$current_err['message'] = $issue['error_message'];
					$current_err['criterion'] = $issue['criterion'];
					$current_err['code'] = '';
					if ($issue['n_or_e'] == 0)
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
				$criterion = $current_err['criterions'][0];

				$csv[] = array(
					$url,
					$n,
					$yml['criterions'][$criterion]['level']['name'],
					$err_type,
					Util::key2code($criterion),
					$current_err['message'],
					$err['id'],
					$err['str'] == $err['id'] ? '' : $err['str'],
				);
				$n++;
			}
		}
		return $csv;
	}


	/**
	 * add images to csv
	 *
	 * @param  String  $url
	 * @param  Array   $csv
	 * @param  Integer $n
	 * @return Array
	 */
	private static function addImgs2Csv($url, $csv, $n)
	{
		// alt list
		foreach (\A11yc\Images::getImages($url) as $v)
		{
			// message
			$alt = Arr::get($v['attrs'], 'alt');
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
		return $csv;
	}

}
