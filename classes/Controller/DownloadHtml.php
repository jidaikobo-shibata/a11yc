<?php
/**
 * A11yc\Controller\DownloadHtml
 *
 * @package    part of A11yc
 * @author     Jidaikobo Inc.
 * @license    The MIT License (MIT)
 * @copyright  Jidaikobo Inc.
 * @link       http://www.jidaikobo.com
 */
namespace A11yc\Controller;

use A11yc\Model;

trait DownloadHtml
{
	/**
	 * download
	 *
	 * @return Void
	 */
	public static function html()
	{
		if (class_exists('ZipArchive'))
		{
			if (ini_get('zlib.output_compression'))
			{
				ini_set('zlib.output_compression', 'Off');
			}

			$zip  = new \ZipArchive();
			$file = 'a11yc_html.zip';
			$dir  = sys_get_temp_dir();

			$result = $zip->open($dir.$file, \ZIPARCHIVE::CREATE | \ZIPARCHIVE::OVERWRITE);
			if ($result !== true) Util::error('zip failed');

			// create archive
			$zip = self::addHtml($zip);

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
	 * csv
	 *
	 * @param Object $zip
	 * @return Object
	 */
	private static function addHtml($zip)
	{
		foreach (Model\Page::fetchAll() as $v)
		{
			$zip->addFromString($v['serial_num'].'.html', Model\Html::fetch($v['url']));
		}

		return $zip;
	}
}
