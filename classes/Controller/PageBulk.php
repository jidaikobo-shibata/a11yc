<?php
/**
 * A11yc\Controller\PageBulk
 *
 * @package    part of A11yc
 * @author     Jidaikobo Inc.
 * @license    The MIT License (MIT)
 * @copyright  Jidaikobo Inc.
 * @link       http://www.jidaikobo.com
 */
namespace A11yc\Controller;

use A11yc\Model;

trait PageBulk
{
	/**
	 * update id
	 *
	 * @return Void
	 */
	public static function updateSerialNum()
	{
		foreach (Input::postArr('serial_nums') as $url => $serial_num)
		{
			Model\Page::updatePartial($url, 'serial_num', $serial_num);
		}
	}

	/**
	 * update order
	 *
	 * @return Void
	 */
	public static function updateSeq()
	{
		foreach (Input::postArr('seq') as $url => $seq)
		{
			Model\Page::updatePartial($url, 'seq', intval($seq));
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
			if (Model\Page::delete($url))
			{
				Session::add(
					'messages',
					'messages',
					sprintf(A11YC_LANG_CTRL_DELETE_DONE, Util::s($url))
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
			if (Model\Page::purge($url))
			{
				Session::add(
					'messages',
					'messages',
					sprintf(A11YC_LANG_CTRL_PURGE_DONE, Util::s($url))
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
			if (Model\Page::undelete($url))
			{
				Session::add(
					'messages',
					'messages',
					sprintf(A11YC_LANG_CTRL_UNDELETE_DONE, Util::s($url))
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
			$is_full = false;
			Report::download($is_full);
		}

		$str = '';
		foreach (array_keys(Input::postArr('bulk')) as $url)
		{
			Result::each($url);
			$str.= View::fetch('body');
			$str.= "\n\n/====A11YC_RESULT_CSPLIT====/\n\n";
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
