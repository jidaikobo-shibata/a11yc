<?php
/**
 * A11yc\DownloadReport
 *
 * @package    part of A11yc
 * @author     Jidaikobo Inc.
 * @license    The MIT License (MIT)
 * @copyright  Jidaikobo Inc.
 * @link       http://www.jidaikobo.com
 */
namespace A11yc\Controller;

use A11yc\Model;

trait DownloadReport
{
	/**
	 * download
	 *
	 * @return Void
	 */
	public static function report()
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
			$urls = array_column(Model\Page::fetchAll(), 'url');
			$zip = self::addResults($zip);
			$zip = self::addPageList($zip);
//			$zip = self::addCsv($zip, $urls);

			// pages report
			$zip = self::addPages($zip, $urls);

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
	private static function addPages($zip, $urls)
	{
		$n =1;
		$exist = false;
		foreach ($urls as $url)
		{
			if (Result::each($url, '', true) === false) continue;
			list($zip, $exist) = self::addEachPages($zip, $n, $exist);
			$n++;
		}

		// empty zip cannot destroy: warning
		// http://php.net/manual/ja/class.ziparchive.php
		if ( ! $exist)
		{
			$zip->addFromString('results/result'.$n.'.html', 'empty');
		}
		return $zip;
	}

	/**
	 * each pages
	 *
	 * @param Object $zip
	 * @param Integer $n
	 * @param Bool $exist
	 * @return Array
	 */
	private static function addEachPages($zip, $n, $exist)
	{
		$targets = array(
			'each'      => 'body',
			'icl'       => 'download_icl',
			'criterion' => 'download_criterion',
		);

		foreach ($targets as $file => $target)
		{
			$body = View::fetch($target);
			if ( ! $body) continue;
			$exist = true;
			$zip->addFromString($file.'s/'.$file.$n.'.html', self::replaceStrings($body));
		}

		return array($zip, $exist);
	}

	/**
	 * results
	 *
	 * @param Object $zip
	 * @return Object
	 */
	private static function addResults($zip)
	{
		$is_center = true;
		Result::all('', $is_center, true);
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
	private static function addCsv($zip, $urls)
	{
		// use DownloadCsv
		$csv = self::generateCsv($urls);
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
	private static function addPageList($zip)
	{
		Result::page();
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

		// add header and footer
		$str = View::fetchTpl('inc_report_header.php').$str.View::fetchTpl('inc_report_footer.php');

		return $str;
	}
}
