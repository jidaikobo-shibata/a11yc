<?php
/**
 * A11yc\Report
 *
 * @package    part of A11yc
 * @author     Jidaikobo Inc.
 * @license    The MIT License (MIT)
 * @copyright  Jidaikobo Inc.
 * @link       http://www.jidaikobo.com
 */
namespace A11yc;

use A11yc\Controller;
use A11yc\Model;

class Report
{
	/**
	 * download
	 *
	 * @param Bool $is_full
	 * @return Void
	 */
	public static function download($is_full)
	{
		if (class_exists('ZipArchive'))
		{
			if (ini_get('zlib.output_compression'))
			{
				ini_set('zlib.output_compression', 'Off');
			}

			$zip  = new \ZipArchive();
			$file = 'a11yc.zip';
			$dir  = sys_get_temp_dir();

			$result = $zip->open($dir.$file, \ZIPARCHIVE::CREATE | \ZIPARCHIVE::OVERWRITE);
			if ($result !== true) Util::error('zip failed');

			// create archive
			if ($is_full)
			{
				$urls = array_column(Model\Page::fetchAll(), 'url');
				$zip = self::results($zip);
				$zip = self::pageList($zip);
				$zip = self::csv($zip, $urls);
			}
			else
			{
				$urls = array_keys(Input::postArr('bulk'));
			}

			// pages report
			$zip = self::pages($zip, $urls);

			// close
			$zip->close();

			// output to stream
			mb_http_output("pass");
			header('Content-Type: application/zip; name="'.$file.'"');
			header('Content-Disposition: attachment; filename="'.$file.'"');
			header('Content-Length: '.filesize($dir.$file));
			readfile($dir.$file);

			// unlink temporary files
			if (file_exists($dir.$file))
			{
				unlink($dir.$file);
			}
			exit();
		}

		Util::error('zip failed: class not exist.');
	}

	/**
	 * pages
	 *
	 * @param Object $zip
	 * @param Array $urls
	 * @return Object
	 */
	private static function pages($zip, $urls)
	{
		$n =1;
		$exist = false;
		foreach ($urls as $url)
		{
			if (Controller\Result::each($url, true) === false) continue;

			$body = View::fetch('body');
			if ( ! $body) continue;
			$exist = true;
			$body = self::replaceStrings($body);
			$zip->addFromString('result'.$n.'.html', $body);
			$n++;
		}

		// empty zip cannot destroy: warning
		// http://php.net/manual/ja/class.ziparchive.php
		if ( ! $exist)
		{
			$zip->addFromString('result'.$n.'.html', 'empty');
		}
		return $zip;
	}

	/**
	 * results
	 *
	 * @param Object $zip
	 * @return Object
	 */
	private static function results($zip)
	{
		$is_center = true;
		Controller\Result::all($is_center);
		$body = View::fetch('body');
		$body = self::replaceStrings($body);
		$zip->addFromString('index.html', $body);

		return $zip;
	}

	/**
	 * csv
	 *
	 * @param Object $zip
	 * @param Array $urls
	 * @return Object
	 */
	private static function csv($zip, $urls)
	{
		$csv = Controller\Export::generateCsv($urls);
		$filename = 'a11yc.csv';
		$filepath = sys_get_temp_dir().$filename;
		$fp = fopen($filepath, 'w');
		if ($fp === FALSE) throw new Exception('failed to export');

		foreach ($csv as $fields)
		{
			mb_convert_variables('SJIS', 'UTF-8', $fields);
			fputcsv($fp, $fields, "\t");
		}
		fclose($fp);

		$zip->addFromString($filename, file_get_contents($filepath));
		unlink($filepath);

		return $zip;
	}

	/**
	 * page list
	 *
	 * @param Object $zip
	 * @return Object
	 */
	private static function pageList($zip)
	{
		Controller\Result::pages();
		$body = View::fetch('body');
		$body = self::replaceStrings($body);
		$zip->addFromString('pages.html', $body);
		return $zip;
	}

	/**
	 * replace strings
	 *
	 * @param String $str
	 * @return String
	 */
	private static function replaceStrings($str)
	{
		// non download string
		$start = preg_quote(A11YC_NON_DOWNLOAD_START, '/');
		$end = preg_quote(A11YC_NON_DOWNLOAD_END, '/');
		$search = '/'.$start.'.+?'.$end.'/ism';
		$str = preg_replace($search, '', $str);

		// replace headings
		$search  = array('<h2', '/h2', '<h3', '/h3', '<h4', '/h4');
		$replace = array('<h1', '/h1', '<h2', '/h2', '<h3', '/h3');
		$str = str_replace($search, $replace, $str);

		// replace url

		return $str;
	}
}
