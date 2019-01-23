<?php
/**
 * A11yc\Controller\Tech
 *
 * @usage
 * This controller is for engineering.
 * Genarate Yaml
 * Do not forget other languages
 * @source https://waic.jp/docs/WCAG-TECHS/complete.html
 * @source https://www.w3.org/TR/WCAG20-TECHS/complete.html
 *
 * @package    part of A11yc
 * @author     Jidaikobo Inc.
 * @license    The MIT License (MIT)
 * @copyright  Jidaikobo Inc.
 * @link       http://www.jidaikobo.com
 */
namespace A11yc\Controller;

class Tech
{
	/**
	 * Import
	 *
	 * @return Void
	 */
	public static function actionImport()
	{
		die();

		// physical path of Techniques for WCAG 2.0 complete.html
		$path = 'https://waic.jp/docs/WCAG-TECHS/complete.html';
		if ( ! file_exists($path)) Util::error();
		$text = file_get_contents($path);

		// get procedures
		$techs = explode('<h3>', $text);
		$procedures = self::getProcedures($techs);

		// get title, type and criterions
		$text4tech = preg_replace('/\<p[^\>]*?\>.+?\<\/p\>/mis', '', $text);
		$techs = explode('<h3>', $text4tech);
		$results = self::getTitles($techs);

		// reverse value
		$results2 = self::getReversedValue($results);

		// output
		self::outputYaml($results);
		self::outputYaml($results2);
		self::outputYaml($procedures);
		die();
	}

	/**
	 * outputYaml
	 *
	 * @param Array $results
	 * @return Void
	 */
	private static function outputYaml($results)
	{
		if ( ! class_exists('Spyc'))
		{
			include A11YC_LIB_PATH.'/spyc/Spyc.php';
		}
		$yml = \Spyc::YAMLDump($results);
		echo '<textarea style="width:100%;height:200px;background-color:#fff;color:#111;font-size:90%;font-family:monospace;position:relative;z-index:9999">';
		echo $yml;
		echo '</textarea>';
	}

	/**
	 * get each
	 *
	 * @param String $tech
	 * @return Array|false
	 */
	private static function getEach($tech)
	{
		if (substr($tech, 0, 7) !== '<a name') return false;
		$each = explode('</h3>', $tech);
		if (count($each) !== 2) return false;
		return $each;
	}

	/**
	 * get tech code
	 *
	 * @param Array $each
	 * @return String|false
	 */
	private static function getTechCode($each)
	{
		if ( ! preg_match('/".+?"/', $each[0], $m)) return false;
		$code = str_replace('"', '', $m[0]);
		return $code;
	}

	/**
	 * get Procedures
	 *
	 * @param Array $techs
	 * @return Array
	 */
	private static function getProcedures($techs)
	{
		// remove introduction
		unset($techs[0]);

		$procedures = array();
		foreach ($techs as $tech)
		{
			$each = self::getEach($tech);
			if ($each === false) continue;

			// tech code
			$code = self::getTechCode($each);
			if ($code === false) continue;

			// code must not be contain "_".
			if (strpos($code, '_') !== false) continue;

			// needless type of criterion
			if (strpos($code, 'FLASH') !== false) continue;
			if (strpos($code, 'SL') !== false) continue;
			if (strpos($code, 'PDF') !== false) continue;

			// extract procedure
			$target = '<h5 class="small-head" id="'.$code.'-procedure">';
			$txt = substr($each[1], strpos($each[1], $target));

			// needless str
			$search = '</div></div><div class="technique"><hr class="divider" title="Beginning of new technique"/>';
			$txt = str_replace($search, '', $txt);

			// get procedure and result
			$txts = explode('<h5', $txt);

			// procedure not exist. C18, SCR36.
			if ( ! isset($txts[2])) continue;

			// extract text
			$procedure = mb_substr($txts[1], mb_strpos($txts[1], '</h5>') + 5);
			$result = mb_substr($txts[2], mb_strpos($txts[2], '</h5>') + 5);
			$result = mb_substr($result, 0, mb_strpos($result, '</ul>') + 5);

			$procedures[$code]['procedure'] = $procedure;
			$procedures[$code]['result'] = $result;
		}
		return $procedures;
	}

	/**
	 * get titles
	 *
	 * @param Array $techs
	 * @return Array
	 */
	private static function getTitles($techs)
	{
		$results = array();
		foreach ($techs as $tech)
		{
			$each = self::getEach($tech);
			if ($each === false) continue;

			// code
			$code = self::getTechCode($each);
			if ($code === false) continue;

			// needless type of criterion
			if (strpos($code, 'FLASH') !== false) continue;
			if (strpos($code, 'SL') !== false) continue;
			if (strpos($code, 'PDF') !== false) continue;

			// allowed types
			$types = Values::techsTypes();
			$type = preg_replace('/\d/', '', $code);
			if ( ! in_array($type, $types)) continue;

			// title
			$title = trim(strip_tags($each[0]));

			// applicability
			$app = mb_substr($each[1], 0, mb_strpos($each[1], '-description">'));
			preg_match_all('/\d\.\d\.\d{1,2}/', strip_tags($app), $ms);
			$apps = array_unique($ms[0]);
			sort($apps);
			$apps = array_map(function($s){return str_replace('.', '-', $s);}, $apps);

			// results
			$results[$code] = array(
				'title' => $title,
				'type' => $type,
				'apps' => $apps,
			);
		}
		return $results;
	}

	/**
	 * get reversed value
	 *
	 * @param Array $results
	 * @return Array
	 */
	private static function getReversedValue($results)
	{
		$results2 = array();
		foreach ($results as $k => $v)
		{
			foreach ($v['apps'] as $code)
			{
				if ($v['type'] == 'F')
				{
					$results2[$code]['f'][] = $k;
				}
				else
				{
					$results2[$code]['t'][] = $k;
				}
			}
		}
		ksort($results2);
		return $results2;
	}
}
