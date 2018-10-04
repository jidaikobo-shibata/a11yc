<?php
/**
 * A11yc\Controller\Pages\Bulk
 *
 * @package    part of A11yc
 * @author     Jidaikobo Inc.
 * @license    The MIT License (MIT)
 * @copyright  Jidaikobo Inc.
 * @link       http://www.jidaikobo.com
 */
namespace A11yc\Controller\Pages;

use A11yc\Model;

class Bulk
{
	/**
	 * update order
	 *
	 * @return Void
	 */
	public static function updateSeq()
	{
		foreach (Input::postArr('seq') as $url => $seq)
		{
			Model\Pages::updateField($url, 'seq', intval($seq));
		}
	}

	/**
	 * bulk delete
	 *
	 * @return Void
	 */
	public static function delete()
	{
		foreach (array_keys(Input::postArr('bulk')) as $url)
		{
			if (Model\Pages::delete($url))
			{
				Session::add(
					'messages',
					'messages',
					sprintf(A11YC_LANG_PAGES_DELETE_DONE, Util::s($url))
				);
			}
		}
	}

	/**
	 * bulk purge
	 *
	 * @return Void
	 */
	public static function purge()
	{
		foreach (array_keys(Input::postArr('bulk')) as $url)
		{
			if (Model\Pages::purge($url))
			{
				Session::add(
					'messages',
					'messages',
					sprintf(A11YC_LANG_PAGES_PURGE_DONE, Util::s($url))
				);
			}
		}
	}

	/**
	 * bulk undelete
	 *
	 * @return Void
	 */
	public static function undelete()
	{
		foreach (array_keys(Input::postArr('bulk')) as $url)
		{
			if (Model\Pages::undelete($url))
			{
				Session::add(
					'messages',
					'messages',
					sprintf(A11YC_LANG_PAGES_UNDELETE_DONE, Util::s($url))
				);
			}
		}
	}

	/**
	 * bulk result
	 *
	 * @return Void
	 */
	public static function result()
	{
		if (class_exists('ZipArchive'))
		{
			if (ini_get('zlib.output_compression'))
			{
				ini_set('zlib.output_compression', 'Off');
			}

			$zip  = new \ZipArchive();
			$file = 'a11yc.zip';
			$dir  = '/tmp/a11yc/';
			if ( ! file_exists($dir))
			{
				mkdir($dir);
			}

			$result = $zip->open($dir.$file, \ZIPARCHIVE::CREATE | \ZIPARCHIVE::OVERWRITE);
			if ($result !== true) Util::error('zip failed');

			set_time_limit(0);
			$n =1;
			$exist = false;
			foreach (array_keys(Input::postArr('bulk')) as $url)
			{
				\A11yc\Controller\Results::each($url);
				$each_result = View::fetch('body');
				if ( ! $each_result) continue;
				$exist = true;
				$zip->addFromString('result'.$n.'.html', $each_result);
				$n++;
			}

			// empty zip cannot destroy: warning
			// http://php.net/manual/ja/class.ziparchive.php
			if ( ! $exist)
			{
				$zip->addFromString('result'.$n.'.html', 'empty');
			}

			$zip->close();

			// output to stream
			mb_http_output("pass");
			header('Content-Type: application/zip; name="' . $file . '"');
			header('Content-Disposition: attachment; filename="' . $file . '"');
			header('Content-Length: '.filesize($dir.$file));
			readfile($dir.$file);

			// unlink temporary files
			@unlink($dir.$file);
			@unlink($dir);
			exit();
		}

		$str = '';
		foreach (array_keys(Input::postArr('bulk')) as $url)
		{
			Results::each($url);
			$str.= View::fetch('body');
			$str.= "\n\n/====A11YC_RESULTS_CSPLIT====/\n\n";
		}

		// export
		$filename = 'a11yc_results.txt';
		header("HTTP/1.1 200 OK");
		header('Content-Type: text/plain');
		header('Content-Length: '.mb_strlen($str));
		header('Content-Disposition: attachment; filename='.$filename);
		echo $str;
		exit();
	}
}
