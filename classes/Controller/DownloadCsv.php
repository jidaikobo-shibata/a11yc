<?php
/**
 * A11yc\Controller\DownloadCsv
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

trait DownloadCsv
{
	/**
	 * csv
	 *
	 * @param String|Array $url
	 * @return Void
	 */
	public static function csv($url)
	{
		$csv = static::generateCsv($url);

		// output
		ob_start();
		$fp = fopen('php://output', 'w');
		if ($fp === FALSE) throw new Exception('failed to export');

		foreach ($csv as $fields)
		{
			mb_convert_variables('SJIS', 'UTF-8', $fields);
			fputcsv($fp, $fields, "\t");
		}
		fclose($fp);

		$buffer = ob_get_contents();
		ob_end_clean();

		File::download('a11yc.csv', $buffer);
	}

	/**
	 * generateCsv
	 *
	 * @param String|Array $url
	 * @return Array
	 */
	public static function generateCsv($url)
	{
		// get Pages
		if (is_array($url))
		{
			$urls = $url;
		}
		else
		{
			$urls = array();
			$urls[] = $url;
		}

		$csv = array();
		$csv[] = array(
			'URL',
			'No.',
			A11YC_LANG_LEVEL,
			A11YC_LANG_IMPORTANCE,
			A11YC_LANG_CRITERION,
			A11YC_LANG_CTRL_CHECK,
			A11YC_LANG_CHECKLIST_SOURCE,
			A11YC_LANG_CHECKLIST_MEMO,
		);

		// check and generate csv
		foreach ($urls as $url)
		{
			$html = Model\Html::fetch($url);
			if ($html === false) continue;

			// validate
			Validate::url($url);

			// csv
			$n = 1;
			$csv = self::addErr2Csv($url, $csv, $n);
			$csv = self::addImgs2Csv($url, $csv, $n);
			$csv = self::addIssues2Csv($url, $csv, $n);
			$csv[] = array();
		}

		return $csv;
	}

	/**
	 * add errors to csv
	 *
	 * @param String  $url
	 * @param Array   $csv
	 * @param Integer $n
	 * @return Array
	 */
	private static function addErr2Csv($url, $csv, $n)
	{
		$yml = Yaml::fetch();

		foreach (Validate\Get::errorIds($url) as $err_code => $errs)
		{
			foreach ($errs as $err)
			{
				if ( ! isset($yml['errors'][$err_code])) continue;
				$current_err = $yml['errors'][$err_code];
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
	 * stack issue
	 *
	 * @param String $url
	 * @param Array $issues
	 * @return Array
	 */
	private static function stackIssue($url, $issues = array())
	{
		foreach (Model\Issue::fetchByUrl($url) as $val)
		{
			foreach ($val as $v)
			{
				$issues[] = $v;
			}
		}
		return $issues;
	}

	/**
	 * issue
	 *
	 * @param String  $url
	 * @param Array|Null   $csv
	 * @param Integer $n
	 * @return Array
	 */
	private static function addIssues2Csv($url, $csv, $n)
	{
		if (is_null($csv)) return array();
		$csv[] = array();

		$issues = self::stackIssue($url, self::stackIssue('commons'));

		foreach ($issues as $issue)
		{
			$err_type = $issue['n_or_e'] == 0 ? 'notice' : 'error';

			$csv[] = array(
				$url,
				$n,
				'-',
				$err_type,
				'-',
				$issue['error_message'],
				$issue['html'],
				'',
			);
			$n++;
		}
		return $csv;
	}

	/**
	 * add images to csv
	 *
	 * @param String  $url
	 * @param Array|Null   $csv
	 * @param Integer $n
	 * @return Array
	 */
	private static function addImgs2Csv($url, $csv, $n)
	{
		if (is_null($csv)) return array();

		// alt list
		foreach (\A11yc\Image::getImages($url) as $v)
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
				Arr::get($v['attrs'], 'src', ''),
				$alt,
			);
			$n++;
		}
		return $csv;
	}
}
