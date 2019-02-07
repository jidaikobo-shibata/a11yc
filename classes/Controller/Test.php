<?php
/**
 * A11yc\Controller\Test
 *
 * @usage
 * This controller is for engineering.
 * Do not forget other languages
 * @source https://waic.jp/docs/jis2010/test-guidelines/201211/icl-index.html
 *
 * @package    part of A11yc
 * @author     Jidaikobo Inc.
 * @license    The MIT License (MIT)
 * @copyright  Jidaikobo Inc.
 * @link       http://www.jidaikobo.com
 */
namespace A11yc\Controller;

class Test
{
	/**
	 * Import
	 *
	 * @return Void
	 */
	public static function actionImport()
	{
		die();

		$path = 'https://waic.jp/docs/jis2010/test-guidelines/201211/';
		$text = file_get_contents($path.'icl-index.html');
		preg_match_all('/\<a href="icl-.+?\<\/a\>/', $text, $ms);

		$rets = array();
		foreach ($ms[0] as $v)
		{
			$url = substr($v, 9, 16);
			$criterion = substr($url, 6, 5);
			$text = file_get_contents($path.$url);
			$rets[$criterion] = self::decompose($text);
		}

		echo '<textarea style="width:100%;height:200px;background-color:#fff;color:#111;font-size:90%;font-family:monospace;position:relative;z-index:9999">';
		var_export($rets);
		echo '</textarea>';
		die();
	}

	/**
	 * decompose
	 *
	 * @param String $text
	 * @return Array
	 */
	private static function decompose($text)
	{
		$rets = array();

		// icl table
		preg_match('/\<table class="icl"\>.+?\<\/table\>/ms', $text, $ms);

		$rows = explode('</tr>', $ms[0]);
		unset($rows[0]); // <table>
		array_pop($rows); // '</table>'

		foreach ($rows as $row)
		{
			// situations
			if (strpos($row, 'th colspan="6"') !== false)
			{
				$rets[] = self::situations($row);
			}
			// implements
			else
			{
				$tmp = self::implement($row);
				if (empty($tmp)) continue;
				$rets[] = $tmp;
			}
		}

		return $rets;
	}

	/**
	 * situations
	 *
	 * @param String $row
	 * @return Array
	 */
	private static function situations($row)
	{
		$ret = strip_tags($row);
		$ret = str_replace(array("\n", '&nbsp;', ' ', '　'), '',  $ret);
		$ret = str_replace('：', ': ',  $ret);
		$ret = trim($ret);
		return trim($ret);
	}

	/**
	 * implement
	 *
	 * @param String $row
	 * @return Array
	 */
	private static function implement($row)
	{
		$ret = array();
		$cols = explode("</td>", $row);
		foreach ($cols as $k => $v)
		{
			$v = str_replace('<tr>', '', $v);
			$v = preg_replace('/\<th[^\>]*?\>.+?\<\/th\>/', '', $v);
			$v = str_replace('<td>', '', $v);
			$v = str_replace('&nbsp;', '', $v);
			$cols[$k] = trim($v);
		}

		if( ! isset($cols[6])) return;

		$implement = preg_replace('/\<!--.+?--\>/ism', '', $cols[6]);
		$arr = explode("<br>", $implement);
		$implements = array();
		foreach ($arr as $v)
		{
			$v = trim($v);
			if (empty($v)) continue;
			if ( ! ctype_alnum($v)) continue;
			$implements[] = trim($v);
		}

		// 実装方法	適合	適用	試験方法	注記	状況-番号-項目	関連する実装テクニック	検証方法
		$ret['title'] = $cols[0];
		$ret['identifier'] = $cols[5];
		$ret['techs'] = $implements;
		$ret['inspection'] = $cols[7];
		return $ret;
	}
}
